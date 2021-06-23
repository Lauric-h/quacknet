<?php

namespace App\Controller;

use App\Entity\Ducks;
use App\Repository\DucksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DuckController extends AbstractController
{
    /**
     * @Route("/duck", name="duck")
     */
    public function index(DucksRepository $repository): Response
    {
        return $this->render('ducks/index.html.twig', [
            'ducks' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/duck/{id}", name="show_duck", methods={"GET"})
     */
    public function show(Ducks $ducks): Response
    {
        return $this->render('ducks/show.html.twig', [
            'ducks' => $ducks
        ]);
    }
}
