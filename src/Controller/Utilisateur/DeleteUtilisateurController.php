<?php 
namespace App\Controller\Utilisateur;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DeleteUtilisateurController extends AbstractController
{
    public function __invoke(
        int $id,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage
    ):JsonResponse
    {
        $user = $tokenStorage->getToken()?->getUser();
        $userId = $entityManager->getRepository(Utilisateur::class)->find($id);

        try {
            $entityManager->remove($userId);
            $entityManager->flush();

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Commentaire supprimé avec succès'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}