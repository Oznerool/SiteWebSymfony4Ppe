<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\LigneReservation;
use App\Entity\Produit;
use App\Entity\Image;
use App\Form\LigneReservationType;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="produit_vente", methods={"GET"})
     */
    public function vente(ProduitRepository $produitRepository): Response
    {
        $reposi = $this->getDoctrine()->getRepository(Categorie::class);
        $categorie = $reposi->findAll();
        $repo = $this->getDoctrine()->getRepository(Produit::class);
        $manthe = $repo->findAll();
        return $this->render('produit/produit.html.twig', [
            'manthe' => $manthe,
            'categorie' => $categorie,
        ]);
    }

    /**
     * @Route("/admin", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="produit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/{id}", name="produit_showadmin", methods={"GET"})
     */
    public function showadmin(Produit $produit): Response
    {
        return $this->render('produit/showadmin.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}", name="produit_show", methods={"GET","POST"})
     *
     */
    public function show(Produit $id,Request $request): Response
    {
        $repo = $this->getDoctrine()->getRepository(Image::class);
        $repos = $this->getDoctrine()->getRepository(Produit::class);
        $image = $repo->findBy(['idProduit' => $id->getId()]) ;
        $plus = $repos->findBy(['idCategorie' => $id->getidCategorie()]);

        return $this->render('produit/show.html.twig', [
            'produit' => $id,
            'image' => $image,
            'plus' => $plus,

        ]);
    }



    /**
     * @Route("/{id}", name="produit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index');
    }

}
