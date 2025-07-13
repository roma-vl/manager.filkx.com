<?php

declare(strict_types=1);

namespace App\Security\OAuth;

use App\Security\UserIdentity;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class FacebookAuthenticator extends OAuth2Authenticator
{
    /**
     * @param UserProviderInterface<UserIdentity> $userProvider
     */
    public function __construct(
        private ClientRegistry $clientRegistry,
        private UrlGeneratorInterface $urlGenerator,
        private UserProviderInterface $userProvider,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'oauth.facebook_check';
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $client = $this->clientRegistry->getClient('facebook_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client): UserIdentity {
                /** @var FacebookUser $facebookUser */
                $facebookUser = $client->fetchUserFromToken($accessToken);
                $email = $facebookUser->getEmail();

                if ($email === null) {
                    throw new AuthenticationException('Email is required');
                }

                /** @var UserIdentity $user */
                $user = $this->userProvider->loadUserByIdentifier($email);

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}
