<?php

namespace App\Controller;

use App\Entity\Quack;
use App\Form\QuackType;
use App\Form\SearchType;
use App\Repository\QuackRepository;
use Doctrine\ORM\Query\AST\Functions\CurrentTimestampFunction;
use http\Env;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

/**
 * @Route("/quack")
 */
class QuackController extends AbstractController
{
    /**
     * @Route("/", name="quack_index", methods={"GET", "POST"})
     */
    public function index(Request $request, QuackRepository $quackRepository): Response
    {
        if ($request->isMethod('POST')) {
            $searchKey = $request->request->get('search')['search'];
            $results = $quackRepository->findByAuthor($searchKey);
            $quacks = $results;
         } else {
            $quacks = $quackRepository->findNotDeleted();
        }

        return $this->render('quack/index.html.twig', [
            'quacks' => $quacks,
        ]);
    }

    /**
     * @Route("/new", name="quack_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $quack = new Quack($this->getUser());

        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quack);
            $entityManager->flush();

            return $this->redirectToRoute('quack_index');
        }

        return $this->render('quack/new.html.twig', [
            'quack' => $quack,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quack_show", methods={"GET"})
     */
    public function show(Quack $quack): Response
    {
        if ($quack->getDeleted() === 1) {
            return $this->render('quack/deleted.html.twig', []);
        }

        return $this->render('quack/show.html.twig', [
            'quack' => $quack,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="quack_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Quack $quack): Response
    {
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quack_index');
        }

        return $this->render('quack/edit.html.twig', [
            'quack' => $quack,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quack_delete", methods={"POST"})
     */
    public function delete(Request $request, Quack $quack): Response
    {
        $this->denyAccessUnlessGranted('delete', $quack);

        if ($this->isCsrfTokenValid('delete'.$quack->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $quack->setDeleted(1);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quack_index');
    }

    /**
     * @Route("/comment/{id}", name="quack_comment", methods={"GET", "POST"})
     */
    public function newComment(Request $request, Quack $parent): Response {

        $quack = new Quack($this->getUser());
        $quack->setParent($parent);

        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quack);
            $entityManager->flush();
            return $this->redirectToRoute('quack_index');
        }

        return $this->render('quack/newComment.html.twig', [
            'quack' => $quack,
            'parent' => $parent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Search method to create the search bar
     * @return Response render form
     */
    public function search(): Response
    {
        $form = $this->createForm(SearchType::class);
        return $this->render('quack/search.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/like/{id}", name="quack_like")
     */
    public function likeQuack(Request $request, Quack $quack, ParameterBagInterface $parameterBag) {
        // protect direct access
        if ($request->headers->get('referer') ===
            $parameterBag->get('app_server') &&
            $request->getUser())
        {
            $quack->setPositive($quack->getPositive() + 1);
            $this->getDoctrine()
                ->getManager()
                ->flush();
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/dislike/{id}", name="quack_dislike")
     */
    public function dislikeQuack(Request $request, Quack $quack, ParameterBagInterface $parameterBag) {
        // protect direct access
        if ($request->headers->get('referer') ===
            $parameterBag->get('app_server') &&
            $request->getUser())
        {
            $quack->setNegative($quack->getNegative() + 1);
            $this->getDoctrine()
                ->getManager()
                ->flush();
        }
        return $this->redirectToRoute('index');
    }


}
