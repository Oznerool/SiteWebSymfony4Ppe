<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneReservation;
use App\Form\CommandeType;
use App\Service\HTML2PDF;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use function mysql_xdevapi\getSession;

/**
 * @Route("/commande")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/ere", name="commande_index", methods={"GET"})
     */
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="commande_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('commande_index');
        }

        return $this->render('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/{id}", name="commande_show", methods={"GET"})
     */
    public function showadmin(Commande $commande): Response
    {
        return $this->render('commande/showadmin.html.twig', [
            'commande' => $commande,
        ]);
    }


    /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add($id, SessionInterface $session)
    {

        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute("commandeclient_show");

    }

    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     */
    public function remove($id, SessionInterface $session)
    {

        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);


        return $this->redirectToRoute("commandeclient_show");

    }

    /**
     * @Route("/panier/moinsun/{id}", name="cart_moinsun")
     */
    public function moinsun($id, SessionInterface $session)
    {

        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]--;
        }

        $session->set('panier', $panier);


        return $this->redirectToRoute("commandeclient_show");

    }


    /**
     * @Route("/panier", name="commandeclient_show")
     */

    public function panier(SessionInterface $session, ProduitRepository $produitRepository)
    {

        $panier = $session->get('panier', []);


        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $produitRepository->find($id),
                'quantity' => $quantity
            ];
            dump($panierWithData);
        }

        $total = 0;

        foreach ($panierWithData as $item) {
            $totalItem = $item['product']->getPrixht() * $item['quantity'];
            dump($item);
            $total += $totalItem;
        }

        return $this->render('commande/show.html.twig', [
            'item' => $panierWithData,
            'total' => $total
        ]);
    }

    /**
     * @Route("/panier/payer", name="payer")
     */
    public function payer(){
        return $this->render('paypal/payment.php');
    }

    /**
     * @Route("/panier/facture", name="commandeclient_facture")
     * @return Response
     */

    public function facture(SessionInterface $session, ProduitRepository $produitRepository)
    {

        $panier1 = $session->get('panier', []);


        $panierWithData1 = [];
        foreach ($panier1 as $id => $quantity) {
            $panierWithData1[] = [
                'product' => $produitRepository->find($id),
                'quantity' => $quantity
            ];
            dump($panierWithData1);
        }

        $total = 0;

        foreach ($panierWithData1 as $item) {
            $totalItem = $item['product']->getPrixht() * $item['quantity'];
            dump($item);
            $total += $totalItem;
        }

        $template = $this->renderView('views/pdf.html.twig', [
            'item' => $panierWithData1
        ]);

        $html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));
        $html2pdf->writeHTML($template);
        return $html2pdf->output("FactureZooHome.pdf");

    }


    /**
     * @Route("/{id}/edit", name="commande_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Commande $commande): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('commande_index');
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="commande_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Commande $commande): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('commande_index');
    }
}
