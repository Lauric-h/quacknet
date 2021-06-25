<?php

namespace App\Controller;

use App\Entity\Ducks;
use App\Entity\Quack;
use App\Repository\QuackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ApiController
 * @package App\Controller
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/quack", name="api")
     */
    public function index(Request $request, QuackRepository $quackRepository): Response
    {
        $quacks = $quackRepository->findNotDeleted();
        $data = [];
        foreach ($quacks as $quack) {
            $data[] = [
                'id' => $quack->getId(),
                'username' => $quack->getDuck()->getUsername(),
                'content' => $quack->getContent(),
                'created_at' => $quack->getCreatedAt(),
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/quack/{id}", name="api_show", methods={"GET"})
     */
    public function show(Quack $quack): JsonResponse
    {
        if ($quack->getDeleted() === 1) {
            return new JsonResponse('Quack does not exist', Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $quack->getId(),
            'username' => $quack->getDuck()->getUsername(),
            'content' => $quack->getContent(),
            'created_at' => $quack->getCreatedAt(),
        ];
        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/quack/{id}", name="delete_api", methods={"DELETE"})
     */
    public function delete(Request $request, Quack $quack): JsonResponse
    {
        if (!$this->isLoggedIn($request)) {
           return new JsonResponse('Please login', Response::HTTP_FORBIDDEN);
        }

        if ($quack->getDeleted() === 1) {
            return new JsonResponse('Quack does not exist', Response::HTTP_NOT_FOUND);
        }

        $quack->setDeleted(1);
        $this->getDoctrine()
            ->getManager()
            ->flush();

        return new JsonResponse('Quack successfully deleted', Response::HTTP_OK);
    }

    /**
     * @Route("/search", methods={"GET"})
     */
    public function search(Request $request): JsonResponse
    {
        $key = $request->query->get("q");
        return $this->json($request->query->get("q"));
    }

    /**
     * @Route("/register", name="api_register", methods={"POST"})
     */
    public function register(Request $request, SerializerInterface $serializer, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        if ($this->isLoggedIn($request)) {
            return new JsonResponse("You are already logged in and registered", Response::HTTP_BAD_REQUEST);
        }

        $data = $request->getContent();
        $duck = $serializer->deserialize($data, Ducks::class, 'json');

        if (!$duck->getPassword()) {
            return new JsonResponse('Need a password to register', Response::HTTP_FORBIDDEN);
        }

        $duck->setPassword(
            $passwordEncoder->encodePassword(
                $duck,
                $duck->getPassword()
            )
        );

        $em = $this->getDoctrine()->getManager();
        $em->persist($duck);
        $em->flush();

        return new JsonResponse("Duck successfully registered", Response::HTTP_OK);
    }

    /**
     * @Route("/duck/{id}", name="api_update", methods={"PUT"})
     */
    public function update(Request $request, SerializerInterface $serializer, UserPasswordEncoderInterface $passwordEncoder): Response {
        if (!$this->isLoggedIn($request)) {
           return new JsonResponse('Please login', Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $this->updateData($data, $user, $passwordEncoder);

        return new JsonResponse('Successfully modified', Response::HTTP_OK);
    }

    /**
     * @Route("/whoami", name="api_login", methods={"GET", "POST"})
     */
    public function login(Request $request): Response {
        $user = $this->getUser();

        if($request->isMethod("GET") && $user) {
            $data = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ];

            return $this->json($data);
        }

        if ($request->isMethod("POST")) {
            return $this->json([
                'username' => $user->getUsername(),
                'message' => 'Successfully logged in',
            ]);
        }

        return new JsonResponse("Please login", Response::HTTP_FORBIDDEN);
    }

    /**
     * @Route("/logout")
     */
    public function logout(Request $request): Response
    {
        if (!$this->isLoggedIn($request)) {
            return new JsonResponse('You are not logged in', Response::HTTP_FORBIDDEN);
        }
        return $this->redirectToRoute('app_logout');
    }

    /**
     * Helper function
     * @param Request $request
     * @return bool
     */
    public function isLoggedIn(Request $request): bool
    {
        if ($request->getUser()) {
            return true;
        }
        return false;
    }

    /**
     * Helper function
     * Updates duck and persist it
     * @param array $data
     * @param UserInterface $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function updateData(array $data, UserInterface $user, UserPasswordEncoderInterface $passwordEncoder) {
        empty($data['firstname']) ? true : $user->setFirstname($data['firstname']);
        empty($data['lastname']) ? true : $user->setLastname($data['lastname']);
        empty($data['username']) ? true : $user->setUsername($data['username']);
        empty($data['email']) ? true : $user->setEmail($data['email']);
        empty($data['password']) ? true : $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $data['password']
            )
        );

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
    }
}
