<?php

namespace App\Security;

use App\Entity\DiscordUser;
use App\Entity\Ducks; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;


class DiscordAuthenticator extends SocialAuthenticator
{
    private ClientRegistry $clientRegistry;
    private RouterInterface $router;
    private EntityManagerInterface $em;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
   {
       $this->clientRegistry = $clientRegistry;
       $this->router = $router;
       $this->em = $em;
   }

   public function supports(Request $request): bool
   {
       return $request->attributes->get('_route') === 'connect_discord_check';
   }

   public function getCredentials(Request $request)
   {
       return $this->fetchAccessToken($this->getDiscordClient());
   }

   public function getUser($credentials, UserProviderInterface $userProvider)
   {
       $discordUser = $this->getDiscordClient()
           ->fetchUserFromToken($credentials);

       $email = $discordUser->getEmail();

       // 1) have they logged in with Discord before?
       $existingUser = $this->em->getRepository(Ducks::class)
           ->findOneBy(['discord_id' =>
               $discordUser->getId()]);
       if ($existingUser) {
           return $existingUser;
       }

       // 2) Is there a matching email
       $user = $this->em->getRepository(Ducks::class)
           ->findOneBy(['email' => $email]);
       if($user != null) {
           return $user;
       }

       // 3) Register user with discord info
       $user = new Ducks();
       $userArrayInfo = $discordUser->toArray();

       $user->setDiscordId($discordUser->getId());
       $user->setUsername($userArrayInfo["username"]);
       $user->setEmail($email);

       $this->em->persist($user);
       $this->em->flush();

       return $user;
   }

    private function getDiscordClient(): \KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface
    {
        return $this->clientRegistry
            ->getClient('discord');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): RedirectResponse
    {
        $targetUrl = $this->router->generate('quack_index');
        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }


    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            '/connect/discord',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }


}
