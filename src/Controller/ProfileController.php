<?php

namespace App\Controller;

use App\Entity\Ducks;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileController
 * @package App\Controller
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="show_profile")
     * @return Response
     */
    public function show(): Response {
        return $this->render('profile/show.html.twig', [
            'duck' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/edit", name="edit_profile", methods={"GET","POST"})
     */
    public function edit(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('new_password')->getData()
                )
            );

            $this->getDoctrine()->getManager()
                                ->flush();

            return $this->redirectToRoute('show_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'duck' => $this->getUser(),
            'form' => $form->createView(),
        ]);
    }
}
