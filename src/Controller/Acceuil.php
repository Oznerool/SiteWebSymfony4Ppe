<?php


namespace App\Controller;

use App\Entity\Adresse;
use App\Form\AdresseType;
use App\Repository\AdresseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class Acceuil extends AbstractController
{
    /**
     * @Route("/", name="acceuil_index")
     */
    public function index(): Response
    {
        return $this->render('index.html.twig',[]);
    }

}