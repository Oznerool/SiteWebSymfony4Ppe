<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiInscriptionController extends AbstractController
{
    /**
     * @Route("/api/inscription", name="api_inscription", methods={"GET"})
     */
    public function index(UtilisateurRepository $UtilisateurRepository, SerializerInterface $serializer)
    {
        return $response = $this->json($UtilisateurRepository->findAll(), 200, [], ['groups' => 'post:read']);
    }

    /**
     * @Route("/api/inscription", name="api_inscription_post", methods={"POST"})
     */
    public function post(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonRecu = $request->getContent();
        try{
            $utilisateur = $serializer->deserialize($jsonRecu, Utilisateur::class, 'json');

            $errors = $validator->validate($utilisateur);

            if(count($errors) > 0){
                return $this->json($errors, 400);
            }

            $em->persist($utilisateur);
            $em->flush();

            return $this->json($utilisateur, 201, [],['groups' => 'post:read']);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
        dd($utilisateur);
    }
}
