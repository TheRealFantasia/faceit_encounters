<?php

namespace App\Business;

use App\Entity\SearchedMatch;
use App\Entity\User;
use App\Repository\SearchedMatchRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class FaceitService
{
    private const PLAYER_API_ENDPOINT = 'https://open.faceit.com/data/v4/players';
    private const MATCHES_API_ENDPOINT = 'https://open.faceit.com/data/v4/players/%s/history';
    private const MATCH_API_ENDPOINT = 'https://open.faceit.com/data/v4/matches/%s';

    /**
     * @var string
     */
    private $apiKey;
    /**
     * @var SearchedMatchRepository
     */
    private $matchRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        string $apiKey,
        SearchedMatchRepository $matchRepository,
        UserRepository $userRepository
    ) {
        $this->apiKey = $apiKey;
        $this->matchRepository = $matchRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $nickname
     *
     * @return User
     * @throws ClientExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getPlayerByNickname(string $nickname): ?User
    {
        $player = $this->userRepository->findOneBy(['username' => $nickname]);

        if ($player === null || $player->getId() === null) {
            $newPlayer = $this->fetchPlayer($nickname);

            if (!isset($newPlayer['player_id'])) {
                return null;
            }

            $newUser = (new User())
                ->setUsername($nickname)
                ->setGuid($newPlayer['player_id'])
                ->setPicture($newPlayer['avatar']);
            $this->userRepository->save($newUser);

            return $newUser;
        }
        return $player;
    }

    /**
     * @param User $user
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getPlayerMatchMap(User $user): array
    {
        $matches = $this->fetchMatches($user->getGuid());

        $players = [];
        foreach ($matches as $match) {
            $matchPlayers = $match['playing_players'];

            foreach ($matchPlayers as $player) {
                if ($player !== $user->getGuid()) {
                    $players[$player][] = $match['match_id'];
                }
            }
        }

        return $players;
    }

    /**
     * @param array $mappedMatches
     * @param User $user
     *
     * @return bool
     */
    public function hasPlayedWith(array $mappedMatches, User $user)
    {
        foreach ($mappedMatches as $player => $matches) {
            if ($player === $user->getGuid()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $mappedMatches
     * @param User $user
     * @param User $searchedUser
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getMatchesData(array $mappedMatches, User $user, User $searchedUser)
    {
        $matchesData = [];
        foreach ($mappedMatches[$searchedUser->getGuid()] as $matchId) {
            $match = $this->fetchMatchData($matchId, $user, $searchedUser);
            $matchesData[] = $match;
        }
        return array_filter($matchesData);
    }

    /**
     * @param string $name
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function fetchPlayer(string $name): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', self::PLAYER_API_ENDPOINT, [
            'auth_bearer' => $this->apiKey,
            'query' => [
                'nickname' => $name
            ]
        ]);

        $json = json_decode($response->getContent(false), true);

        if ($json === null || isset($json['errors'])) {
            return [];
        }

        return $json;
    }

    /**
     * @param string $player_id
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function fetchMatches(string $player_id): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', sprintf(self::MATCHES_API_ENDPOINT, $player_id), [
            'auth_bearer' => $this->apiKey,
            'query' => [
                'game' => 'csgo',
                'offset' => 0,
                'limit' => 20
            ]
        ]);

        $json = json_decode($response->getContent(false), true);

        return $json['items'];
    }


    /**
     * @param string $matchId
     * @param User $user
     * @param User $searchedUser
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function fetchMatchData(string $matchId, User $user, User $searchedUser): ?array
    {
        /** @var SearchedMatch $match */
        $match = $this->matchRepository->findOneBy(['guid' => $matchId]);

        if ($match === null) {
            $client = HttpClient::create();
            $response = $client->request('GET', sprintf(self::MATCH_API_ENDPOINT, $matchId), [
                'auth_bearer' => $this->apiKey
            ]);

            $json = json_decode($response->getContent(false), true);

            if (isset($json['errors'])) {
                return null;
            }

            $match = (new SearchedMatch())
                ->setGuid($matchId)
                ->setTeam1($json['teams']['faction1']['name'])
                ->setTeam2($json['teams']['faction2']['name'])
                ->setWinner($json['results']['winner'] === 'faction1' ? 'team1' : 'team2')
                ->setUrl(str_replace('{lang}', 'en', $json['faceit_url']));

            foreach ($json['teams']['faction1']['roster'] as $player) {
                $matchPlayer = $this->userRepository->findOneBy(['guid' => $player['player_id']]);

                if ($matchPlayer === null) {
                    $matchPlayer = (new User())
                        ->setUsername($player['nickname'])
                        ->setGuid($player['player_id'])
                        ->setPicture($player['avatar']);
                    $this->userRepository->save($matchPlayer);
                }

                $match->addPlayer($matchPlayer);
            }

            $this->matchRepository->save($match);
        }

        return $match->toArray($user, $searchedUser);
    }
}
