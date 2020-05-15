<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    /**
 * @Route("/sitemap.xml", name="sitemapproduit", defaults={"_format"="xml"})
 */
    public function produit(Request $request)
    {
        $hostname = $request->getSchemeAndHttpHost();

        $urls = [];

        //ajout des page a exporter
        $urls[] = ['loc' => $this->generateUrl('produit_vente')];


        //ajout url dynamique
        foreach ($this->getDoctrine()->getRepository(Produit::class)->findAll() as $produit) {

            $urls[] = [
                'loc' => $this->generateUrl('produit_show', [
                    'id' => $produit->getId()
                ]),
                'nom' => $produit->getLibelle(),
                'prix' => $produit->getPrixht(),
                'libelle' => $produit->getDescription(),
                'stockrestant' => $produit->getStock()
            ];
        }

        $response = new Response(
            $this->renderView('sitemap/index.html.twig', [
                'urls' => $urls,
                'hostname' => $hostname
            ]),
            200
        );

        $response->headers->set('Content-Type','text/xml');

        return $response;
    }

    /**
     * @Route("/sitemapu.xml", name="sitemaputilisateur", defaults={"_format"="xml"})
     */
    public function utilisateur(Request $request)
    {
        $hostnamei = $request->getSchemeAndHttpHost();

        $urlsi = [];

        //ajout des page a exporter
        $urlsi[] = ['loc' => $this->generateUrl('utilisateur_show')];


        //ajout url dynamique
        foreach ($this->getDoctrine()->getRepository(Utilisateur::class)->findAll() as $utilisateur) {

            $urlsi[] = [
                'loc' => $this->generateUrl('utilisateur_show', [
                    'id' => $utilisateur->getId()
                ]),
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'courriel' => $utilisateur->getCourriel(),
                'telephone' => $utilisateur->getTelephone()
            ];
        }

        $response = new Response(
            $this->renderView('sitemap/utilisateur.html.twig', [
                'urls' => $urlsi,
                'hostname' => $hostnamei
            ]),
            200
        );

        $response->headers->set('Content-Type','text/xml');

        return $response;
    }
}
