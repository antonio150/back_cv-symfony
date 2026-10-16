<?php

namespace App\Controller\ContenueCV;

use App\Entity\ContenueCV;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ListContenueCVController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): Response
    {
        $repository = $em->getRepository(ContenueCV::class);
        
        // Optional: filter by utilisateur_id
        $utilisateurId = $request->query->get('utilisateur_id');
        
        if ($utilisateurId) {
            $cvs = $repository->findBy(
                ['utilisateur' => $utilisateurId],
                ['id' => 'DESC'], // ou ['createdAt' => 'DESC'] si tu as ce champ
                1
            );
        } else {
            $cvs = $repository->findAll();
        }

        $data = $serializer->serialize($cvs, 'json', ['groups' => ['contenuecv:read']]);

        return new JsonResponse(json_decode($data, true), Response::HTTP_OK);
    }
}
