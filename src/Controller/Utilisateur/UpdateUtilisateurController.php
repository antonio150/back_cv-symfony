<?php 

namespace App\Controller\Utilisateur;

use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UpdateUtilisateurController extends AbstractController
{
    public function __invoke(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        TokenStorageInterface $tokenStorage
    )
    {
        // Utilisateur connecté
        $user = $tokenStorage->getToken()?->getUser();
        if (!$user || !is_object($user)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Vous devez être connecté'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Utilisateur
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($id);
        if (!$utilisateur) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Utilisateur introuvable'
            ], Response::HTTP_NOT_FOUND);
        }

       
        // Décodage JSON
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Format JSON invalide'
            ], Response::HTTP_BAD_REQUEST);
        }

        $email = trim($data['email'] ?? '');
        $roles = trim($data['roles'] ?? '');
        $password = trim($data['password'] ?? '');
        $nom = trim($data['nom'] ?? '');
        $prenom = trim($data['prenom'] ?? '');
        $dateNaissance = trim($data['dateNaissance'] ?? '');
        $sexe = trim($data['sexe'] ?? '');
        if ($email === '') {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Le champ email est requis'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($roles === []) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Le role email est requis'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($password === '') {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Le champ mot de passe est requis'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($nom === '') {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Le champ nom est requis'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($prenom === '') {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Le champ prenom est requis'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Mise à jour
        $utilisateur->setEmail($email);
        $utilisateur->setPassword($password);
        $utilisateur->setNom($nom);

        // Validation
        $errors = $validator->validate($utilisateur);
        
        // Sauvegarde
        try {
            $entityManager->flush();

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Utilisateur modifié avec succès',
                'data' => [
                    'id' => $utilisateur->getId(),
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}