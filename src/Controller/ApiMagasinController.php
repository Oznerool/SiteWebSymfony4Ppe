<?php

namespace App\Controller;

use App\Entity\Magasin;
use App\Repository\MagasinRepository;
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

class ApiMagasinController extends AbstractController
{
    /**
     * @Route("/api/magasin", name="api_magasin", methods={"GET"})
     */
    public function index(MagasinRepository $MagasinRepository, SerializerInterface $serializer)
    {
        return $response = $this->json($MagasinRepository->findAll(), 200, [], ['groups' => 'post:read']);
    }

    /**
     * @Route("/api/magasin", name="api_magasin_post", methods={"POST"})
     */
    public function post(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonRecu = $request->getContent();
try{
        $magasin = $serializer->deserialize($jsonRecu, Magasin::class, 'json');

        $errors = $validator->validate($magasin);

        if(count($errors) > 0){
            return $this->json($errors, 400);
        }

        $em->persist($magasin);
        $em->flush();

        return $this->json($magasin, 201, [],['groups' => 'post:read']);
        } catch (NotEncodableValueException $e) {
    return $this->json([
        'status' => 400,
        'message' => $e->getMessage()
    ], 400);
}
        dd($magasin);
    }
}
