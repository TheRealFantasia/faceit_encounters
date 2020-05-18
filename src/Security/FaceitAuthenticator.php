<?php

namespace App\Security;

use App\Entity\User;
use App\Provider\FaceitUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Security\Exception\IdentityProviderAuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Exception\InvalidStateAuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Exception\NoAuthCodeAuthenticationException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FaceitAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $repository;
    private $router;

    /**
     * FaceitAuthenticator constructor.
     *
     * @param ClientRegistry $clientRegistry
     * @param UserRepository $repository
     * @param RouterInterface $router
     */
    public function __construct(ClientRegistry $clientRegistry, UserRepository $repository, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->repository = $repository;
        $this->router = $router;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'connect_faceit_check';
    }

    /**
     * @param Request $request
     *
     * @return AccessToken
     * @throws IdentityProviderAuthenticationException
     * @throws InvalidStateAuthenticationException
     * @throws NoAuthCodeAuthenticationException
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getFaceitClient());
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface|null
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var FaceitUser $resourceOwner */
        $resourceOwner = $this->getFaceitClient()->fetchUserFromToken($credentials);

        /** @var User $user */
        $user = $this->repository->findOneBy(['username' => $resourceOwner->getNickname()]);

        if ($user === null) {
            $user = (new User())->setUsername($resourceOwner->getNickname());
        }

        $user = $user->fromFaceitUser($resourceOwner);
        $this->repository->save($user);

        return $user;
    }

    /**
     * @return OAuth2ClientInterface
     */
    private function getFaceitClient()
    {
        return $this->clientRegistry->getClient('faceit');
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     *
     * @return RedirectResponse|Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetUrl = $this->router->generate('vue');

        return new RedirectResponse($targetUrl);
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     *
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
