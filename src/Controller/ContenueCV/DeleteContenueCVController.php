<?php

namespace App\Controller\ContenueCV;

use App\Entity\ContenueCV;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteContenueCVController extends AbstractController
{
    public function __invoke(EntityManagerInterface $em, int $id): Response
    {
        $cv = $em->getRepository(ContenueCV::class)->find($id);

        if (!$cv instanceof ContenueCV) {
            return new JsonResponse(['error' => 'ContenueCV not found'], Response::HTTP_NOT_FOUND);
        }

        // Supprimer les entités enfants liées avant de supprimer le ContenueCV

        if($cv->getPhoto()){
            $em->remove($cv->getPhoto());
        }
        
        // Supprimer BiographieSuite liées à la Biographie
        if ($cv->getBiographie()) {
            foreach ($cv->getBiographie()->getBiographieSuites() as $biographieSuite) {
                $em->remove($biographieSuite);
            }
        }

        // Supprimer ExperienceContenu liés à Experience
        if ($cv->getExperience()) {
            foreach ($cv->getExperience()->getExperienceContenu() as $experienceContenu) {
                $em->remove($experienceContenu);
            }
        }

        // Supprimer FormationContenu liés à Formation
        if ($cv->getFormation()) {
            foreach ($cv->getFormation()->getFormationContenu() as $formationContenu) {
                $em->remove($formationContenu);
            }
        }

        // Supprimer CompetenceContenu liés à Competence
        if ($cv->getCompetence()) {
            foreach ($cv->getCompetence()->getCompetenceContenus() as $competenceContenu) {
                $em->remove($competenceContenu);
            }
        }

        // Supprimer LangueContenue liés à Langue
        if ($cv->getLangue()) {
            foreach ($cv->getLangue()->getLangueContenue() as $langueContenue) {
                $em->remove($langueContenue);
            }
        }

        // Supprimer AutreActiviteContenue liés à AutreActivite
        if ($cv->getAutreActivite()) {
            foreach ($cv->getAutreActivite()->getAutreActiviteContenues() as $autreActiviteContenue) {
                $em->remove($autreActiviteContenue);
            }
        }

        // Supprimer le ContenueCV et ses entités liées (OneToOne avec cascade)
        $em->remove($cv);
        $em->flush();

        return new JsonResponse(['message' => 'ContenueCV deleted successfully'], Response::HTTP_OK);
    }
}
