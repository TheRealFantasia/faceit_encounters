<?php

namespace App\Controller;

use App\Provider\FaceitUser;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FaceitController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/faceit", name="connect_faceit_start")
     * @param ClientRegistry $clientRegistry
     *
     * @return RedirectResponse
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('faceit')
            ->redirect(['openid', 'profile'], ['redirect_popup' => true]);
    }

    /**
     * @Route("/connect/faceit/check", name="connect_faceit_check")
     */
    public function connectCheckAction() {}
}
