<?php

namespace App\Business;

use App\Entity\CachedMatch;
use App\Entity\CachedName;
use App\Entity\User;
use App\Repository\CachedMatchRepository;
use App\Repository\CachedNameRepository;
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
     * @var CachedMatchRepository
     */
    private $matchRepository;
    /**
     * @var CachedNameRepository
     */
    private $nameRepository;

    public function __construct(string $apiKey, CachedMatchRepository $matchRepository, CachedNameRepository $nameRepository)
    {
        $this->apiKey = $apiKey;
        $this->matchRepository = $matchRepository;
        $this->nameRepository = $nameRepository;
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
    public function fetchPlayer(string $name): array
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
    public function fetchMatches(string $player_id): array
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
     * @param array $matches
     * @param string $ownId
     *
     * @return array
     */
    public function getPlayerMatchMap(array $matches, string $ownId): array
    {
        $players = [];
        foreach ($matches as $match) {
            $matchPlayers = $match['playing_players'];

            foreach ($matchPlayers as $player) {
                if ($player !== $ownId) {
                    $players[$player][] = $match['match_id'];
                }
            }
        }

        return $players;
    }

    /**
     * @param string $matchId
     * @param string $own
     * @param CachedName $other
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function fetchMatchData(string $matchId, string $own, CachedName $other): ?array
    {
        /** @var CachedMatch $match */
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

            $match = (new CachedMatch())
                ->setGuid($matchId)
                ->setTeam1($json['teams']['faction1']['name'])
                ->setTeam2($json['teams']['faction2']['name'])
                ->setWinner($json['results']['winner'] === 'faction1' ? 'team1' : 'team2')
                ->setUrl(str_replace('{lang}', 'en', $json['faceit_url']));

            foreach ($json['teams']['faction1']['roster'] as $player) {
                $cachedName = $this->nameRepository->findOneBy(['faceitId' => $player['player_id']]);

                if ($cachedName === null) {
                    $cachedName = (new CachedName())
                        ->setName($player['nickname'])
                        ->setFaceitId($player['player_id']);
                    $this->nameRepository->save($cachedName);
                }

                $match->addPlayer($cachedName);
            }

            $this->matchRepository->save($match);
        }

        return $match->toArray($own, $other);
    }
}
