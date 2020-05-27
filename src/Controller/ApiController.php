<?php

namespace App\Controller;

use App\Business\FaceitService;
use App\Entity\User;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @param SerializerInterface $serializer
     * @param FaceitService $faceit
     *
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function searchInRecentMatches(
        Request $request,
        SerializerInterface $serializer,
        FaceitService $faceit
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        if ($user === null) {
            return $this->render('index/index.html.twig', [
                'user' => []
            ]);
        }

        $searchedName = $request->get('nickname');
        if (empty($searchedName)) {
            throw new BadRequestHttpException('searchedName cannot be empty');
        }
        if ($searchedName === $user->getUsername()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Seriously?'
            ]);
        }

        $searchedUser = $faceit->getPlayerByNickname($searchedName);

        if ($searchedUser === null) {
            $message = 'User \'' . $searchedName . '\' was not found';
            return new JsonResponse([
                'success' => false,
                'message' => $message
            ]);
        }

        $map = $faceit->getPlayerMatchMap($user);
        $playedWith = $faceit->hasPlayedWith($map, $searchedUser);
        $matchesData = $playedWith ? $faceit->getMatchesData($map, $user, $searchedUser) : [];

        return new Response($serializer->serialize([
            'success' => true,
            'searchedUser' => $searchedUser,
            'playedWith' => $playedWith,
            'matches' => $matchesData
        ], 'json'));
    }
}