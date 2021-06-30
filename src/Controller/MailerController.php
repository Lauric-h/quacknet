<?php

namespace App\Controller;

use App\Entity\Ducks;
use GuzzleHttp\Exception\TransferException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    /**
     * @Route("/email/{id}", name="mailer")
     */
    public function sendEmail(MailerInterface $mailer, Ducks $duck) {
        $email = (new Email())
            ->to($duck->getEmail())
            ->subject('Vous avez un pigeon voyageur')
            ->html('<h1>Votre inscription à Quacknet</h1>
                          <p>Vous êtes bien inscrit à QuackNet</p>
                          <p>Venez vite poster vos plus beaux quacks</p>');

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
           return $this->render('mailer/notsent.html.twig', ['error'  => $e]);
        }
        return $this->redirectToRoute('index');
    }
}
