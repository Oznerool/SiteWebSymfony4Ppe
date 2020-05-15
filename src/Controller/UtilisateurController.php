<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/utilisateur")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/", name="utilisateur_index", methods={"GET"})
     */
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

    /**
     * @Route("/login", name="utilisateur_login")
     */
    public function  login(){
        return $this->render('utilisateur/login.html.twig');
    }

    /**
     * @Route("/deconnexion", name="utilisateur_logout")
     */
    public function logout(){

    }

    /**
     * @Route("/profile", name="utilisateur_profile")
     */
public function profile(){
    return $this->render('utilisateur/infocompte.html.twig');
}

    /**
     * @Route("/new", name="utilisateur_new", methods={"GET","POST"})
     */
    public function new(Request $request,UserPasswordEncoderInterface $encoder,\Swift_Mailer $mailer): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);




        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $hash = $encoder->encodePassword($utilisateur,$utilisateur->getPassword());
            $utilisateur->setMotDePasse($hash);

            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $message = (new \Swift_Message('Mon premier email via Symfony')) // Le sujet
            ->setFrom('noreply@ZooHome.fr') // L'email d'envoi
            ->setTo($utilisateur->getCourriel()) // L'email destinataire
            // Le contenu de l'email, qu'on va générer à partir d'un twig
            ->setBody(
            // Utilisation du renderView au lieu du render
            // Permettant de renvoyer uniquement le html et non un objet Response
                $this->renderView(
                    'utilisateur/contact-email.html.twig',
                    ['contact' => $utilisateur]
                ),
                'text/html'
            )
            ;

            // On utilise le mailer pour envoyer notre \Swift_Message
            $mailer->send($message);

            return $this->redirectToRoute('utilisateur_login');
        }

        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="utilisateur_show", methods={"GET"})
     */
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="utilisateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Utilisateur $utilisateur): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('utilisateur_index');
        }

        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="utilisateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Utilisateur $utilisateur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('utilisateur_index');
    }

}
