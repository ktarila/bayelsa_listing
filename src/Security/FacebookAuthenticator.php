<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Uid\Ulid;

class FacebookAuthenticator extends OAuth2Authenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $clientRegistry;
    private $em;
    private $router;
    private $passwordHasher;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router, UserPasswordHasherInterface $passwordHasher)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        $this->passwordHasher = $passwordHasher;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return 'connect_facebook_check' === $request->attributes->get('_route');
    }

    public function getCredentials(Request $request)
    {
        // this method is only called if supports() returns true

        return $this->fetchAccessToken($this->getFacebookClient());
    }

    public function authenticate(Request $request): PassportInterface
    {
        $credentials = $this->getCredentials($request);
        /** @var AzureUser $facebookUser */
        $facebookUserData = $this->getFacebookClient()
            ->fetchUserFromToken($credentials)
        ;

        dump($facebookUserData);
        /*
  #data: array:8 [▼
    "id" => "10225460359926622"
    "name" => "Patrick Kenekayoro"
    "first_name" => "Patrick"
    "last_name" => "Kenekayoro"
    "email" => "patrick.kenekayoro@outlook.com"
    "picture" => array:1 [▼
      "data" => array:2 [▼
        "url" => "https://platform-lookaside.fbsbx.com/platform/profilepic/?asid=10225460359926622&height=200&width=200&ext=1626509768&hash=AeTw6ptVwj1jPu5Vunk"
        "is_silhouette" => false
      ]
    ]
    "picture_url" => "https://platform-lookaside.fbsbx.com/platform/profilepic/?asid=10225460359926622&height=200&width=200&ext=1626509768&hash=AeTw6ptVwj1jPu5Vunk"
    "is_silhouette" => false
  ]
*/

        $facebookUser = $facebookUserData->toArray();
        dump($facebookUser);
        $user = $this->em->getRepository(User::class)
            ->findFacebookUser($facebookUser['email'], $facebookUser['id'])
        ;

        if (!$user) {
            // random password
            $ulid = new Ulid();
            $plainPassword = $ulid->toBase58();
            $user = new User();
            $user->setEmail($facebookUser['email'])
                ->setFacebookId($facebookUser['id'])
                ->setFacebookProfileUrl($facebookUser['picture']['data']['url'])
                ->setFullname($facebookUser['name'])
            ;
            $encodedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($encodedPassword);

            $this->em->persist($user);
            $this->em->flush();
        }

        return new SelfValidatingPassport(
            new UserBadge($facebookUser['email']),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        $url = $this->getLoginUrl($request);

        return new RedirectResponse($url);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/microsoft', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    /**
     * @return FacebookClient
     */
    private function getFacebookClient()
    {
        return $this->clientRegistry
            ->getClient('facebook')
        ;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate(self::LOGIN_ROUTE);
    }
}
