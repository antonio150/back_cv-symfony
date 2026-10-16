<?php 

namespace App\Controller\Utilisateur;

use App\Entity\Utilisateur;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Date;

class CreateUtilisateurController extends AbstractController
{
    public function __invoke(Request $request,
    UserPasswordHasherInterface $passwordHasher,
    EntityManagerInterface $em
): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');
        $nom = trim($data['nom'] ?? '');
        $prenom = trim($data['prenom'] ?? '');
        $dateNaissance = trim($data['dateNaissance'] ?? '');
        $sexe = trim($data['sexe'] ?? true);

        $user = new Utilisateur();

        $password = $passwordHasher->hashPassword($user, $password);

        if($dateNaissance == "")
            {
                $dateNaissance = new DateTime();
            }

        else{
          $dateNaissance = new DateTime($dateNaissance);  
        }
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setDateNaissance($dateNaissance);
        $user->setSexe($sexe);

        $em->persist($user);
        $em->flush();

        return new JsonResponse(
            [
                "statut" => 200,
                "message" => "Utilisateur crée avec succés",
                "data" => $user->getId()
            ]
        , Response::HTTP_CREATED);

    }
}