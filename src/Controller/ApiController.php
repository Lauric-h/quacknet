<?php

namespace App\Controller;

use App\Entity\Ducks;
use App\Entity\Quack;
use App\Repository\QuackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
    public function index(QuackRepository $quackRepository): Response
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
    public function show(Quack $quack) {
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
    public function delete(Quack $quack): JsonResponse
    {
        if ($quack->getDeleted() === 1) {
            return new JsonResponse('Quack does not exist', Response::HTTP_NOT_FOUND);
        }

        $quack->setDeleted(1);
        $this->getDoctrine()
            ->getManager()
            ->flush();

        return new JsonResponse('Quack successfully deleted', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/search")
     */
    public function search() {
        // TODO
    }

    /**
     * @Route("/register", name="api_register", methods={"POST"})
     */
    public function register(Request $request, SerializerInterface $serializer, UserPasswordEncoderInterface $passwordEncoder) {
        $data = $request->getContent();
        $duck = $serializer->deserialize($data, Ducks::class, 'json');

        $duck->setPassword(
            $passwordEncoder->encodePassword(
                $duck,
                $duck->getPassword()
            )
        );

        // vérif data

        $em = $this->getDoctrine()->getManager();
        $em->persist($duck);
        $em->flush();

        return new JsonResponse("Duck successfully registered", Response::HTTP_OK);
    }

    /**
     * @Route("/duck/{id}", name="api_update", methods={"PUT"})
     */
    public function update(Request $request, SerializerInterface $serializer) {
        $data = $request->getContent();

    }

    /**
     * @Route("/whoami", name="api_login", methods={"GET", "POST"})
     */
    public function login(Request $request) {
        $data = $request->getContent();
        // if method = get && user existe => json(info de l'user)
        // if method = post => login
        return $this->json($request);
    }
}
