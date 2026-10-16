<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Google\Client as GoogleClient;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): void
    {
        // Géré automatiquement par Symfony + LexikJWT
    }

    #[Route('/api/auth/google', name: 'api_google_auth', methods: ['POST'])]
    public function googleAuth(Request $request, EntityManagerInterface $em, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $idToken = $request->toArray()['token'] ?? null;

        if (!$idToken) {
            return $this->json(['status' => false, 'message' => 'Token manquant'], 400);
        }

        // Vérification token Google
        $client = new GoogleClient(['client_id' => $_ENV['GOOGLE_CLIENT_ID']]);
        $payload = $client->verifyIdToken($idToken);

        if (!$payload) {
            return $this->json(['status' => false, 'message' => 'Token Google invalide'], 401);
        }

        $email = $payload['email'];
        $name = $payload['name'] ?? null;

        $user = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new Utilisateur();
            $user->setEmail($email);
            $user->setNom($name);
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);
            $em->flush();
        }

        $token = $jwtManager->create($user);

        return $this->json([
            'status' => true,
            'message' => 'Connexion Google réussie',
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ]
        ]);
    }
}
