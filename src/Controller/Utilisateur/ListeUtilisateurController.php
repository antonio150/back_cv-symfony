<?php 

namespace App\Controller\Utilisateur;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class ListeUtilisateurController extends AbstractController
{
    public function __invoke(EntityManagerInterface $entityManager,
    SerializerInterface $serializer
    ):JsonResponse
    {
        $users = $entityManager->getRepository(Utilisateur::class)->findAll();
        $json = $serializer->serialize($users, 'json', [
            'groups' => ['utilisateur:read'],
        ]);

        return new JsonResponse([
            'status' => 200,
            'message' => 'Liste des utilisateurs',
            'data' => json_decode($json, true),
        ]);
    }
}