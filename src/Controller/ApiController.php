<?php

namespace App\Controller;

use App\Business\FaceitService;
use App\Entity\CachedName;
use App\Entity\User;
use App\Repository\CachedNameRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiController extends AbstractController
{
    //TODO: schedule task -> iterate cached ids, if not found/name != cached name -> remove from db

    /**
     * @Route(
     *     "/api/search",
     *     options = { "expose" = true }
     * )
     * @param Request $request
     * @param CachedNameRepository $repository
     * @param FaceitService $faceit
     *
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function searchInRecentMatches(
        Request $request,
        CachedNameRepository $repository,
        FaceitService $faceit
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $otherName = $request->get('other');
        if (empty($otherName)) {
            throw new BadRequestHttpException('other cannot be empty');
        }
        if ($otherName === $user->getUsername()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Seriously?'
            ]);
        }

        $userId = $user->getGuid();
        $other = $repository->findOneBy(['name' => $otherName]); /** @var CachedName $other */

        if ($other === null) {
            $other = $faceit->fetchPlayer($otherName);

            if (!isset($other['player_id'])) {
                $message = 'User \'' . $otherName . '\' was not found';
                return new JsonResponse([
                    'success' => false,
                    'message' => $message
                ]);
            }

            $cached = (new CachedName())
                ->setName($otherName)
                ->setFaceitId($other['player_id'])
                ->setPicture($other['avatar']);
            $repository->save($cached);

            $other = $cached; /** @var CachedName $other */
        }

        $matches = $faceit->fetchMatches($userId);
        $map = $faceit->getPlayerMatchMap($matches, $userId);

        $playedWith = false;
        foreach ($map as $player => $matches) {
            if ($player === $other->getFaceitId()) {
                $playedWith = true;
                break;
            }
        }

        $matchesData = [];
        if ($playedWith) {
            foreach ($map[$other->getFaceitId()] as $matchId) {
                $match = $faceit->fetchMatchData($matchId, $userId, $other);
                $matchesData[] = $match;
            }
        }
        $matchesData = array_filter($matchesData);

        return new JsonResponse([
            'success' => true,
            'otherPicture' => $other->getPicture(),
            'playedWith' => $playedWith,
            'matches' => $matchesData
        ]);
    }
}