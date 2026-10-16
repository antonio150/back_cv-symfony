<?php

namespace App\Controller\ContenueCV;

use App\Entity\Apropos;
use App\Entity\AutreActivite;
use App\Entity\AutreActiviteContenue;
use App\Entity\Biographie;
use App\Entity\BiographieSuite;
use App\Entity\Competence;
use App\Entity\CompetenceContenu;
use App\Entity\ContenueCV;
use App\Entity\Experience;
use App\Entity\ExperienceContenu;
use App\Entity\Formation;
use App\Entity\FormationContenu;
use App\Entity\Langue;
use App\Entity\LangueContenue;
use App\Entity\Photo;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\HttpFoundation\Response;
use UploadImageBundle\Service\FileUploader;

class UpdateContenueCVController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $em, FileUploader $fileUploader, int $id): Response
    {
        $cv = $em->getRepository(ContenueCV::class)->find($id);

        if (!$cv instanceof ContenueCV) {
            return new JsonResponse(['error' => 'ContenueCV not found'], Response::HTTP_NOT_FOUND);
        }


        $data = $request->request->all();


        if (empty($data)) {
            return new JsonResponse([
                'error' => 'No FormData provided',
                "data" => $data
            ], Response::HTTP_BAD_REQUEST);
        }

        // 🔥 Normalisation FormData: Formation[contenus] → $data['Formation']['contenus']
        foreach ($data as $key => $value) {
            if (preg_match('/^(\w+)\[(\w+)\]$/', $key, $m)) {
                $parent = $m[1];
                $child  = $m[2];
                $data[$parent][$child] = json_decode($value, true);
                unset($data[$key]);
            } else {
                // Décodage JSON simple (Biographie, Apropos, etc.)
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $data[$key] = $decoded;
                    }
                }
            }
        }

        

        // Utilisateur (by id)
        if (!empty($data['utilisateur_id'])) {
            $user = $em->getRepository(Utilisateur::class)->find($data['utilisateur_id']);
            if ($user instanceof Utilisateur) {
                $cv->setUtilisateur($user);
            }
        }

        // Apropos
        if (!empty($data['Apropos']) && is_array($data['Apropos'])) {
            $a = $cv->getApropos() ?? new Apropos();
            $a->setTitre($data['Apropos']['titre'] ?? $a->getTitre());
            $a->setDescription($data['Apropos']['description'] ?? $a->getDescription());
            $cv->setApropos($a);
            $em->persist($a);
        }

        // AutreActivite + AutreActiviteContenue
        if (!empty($data['AutreActivite']) && is_array($data['AutreActivite'])) {
            $aa = $cv->getAutreActivite() ?? new AutreActivite();
            $cv->setAutreActivite($aa);

            // Tout supprimer
            foreach ($aa->getAutreActiviteContenues() as $item) {
                $em->remove($item);
            }
            $aa->getAutreActiviteContenues()->clear();

            // Recréer si présence de contenus
            if (!empty($data['AutreActivite']['contenus'])) {
                foreach ($data['AutreActivite']['contenus'] as $dataItem) {
                    $cont = new AutreActiviteContenue();
                    $cont->setChamp($dataItem['champ'] ?? null);
                    $cont->setContenue($dataItem['contenue'] ?? null);
                    $cont->setAutreActivite($aa);
                    $aa->addAutreActiviteContenue($cont);
                    $em->persist($cont);
                }
            }

            $em->persist($aa);
        }

        // Biographie (supprimer toutes les suites existantes + recréer à partir de la requête)
        if (!empty($data['Biographie']) && is_array($data['Biographie'])) {
            $bio = $cv->getBiographie() ?? new Biographie();
            $cv->setBiographie($bio);
            $em->persist($bio);

            // 1. Mettre à jour les champs simples de Biographie
            $bio->setNom($data['Biographie']['nom']     ?? $bio->getNom()     ?? null);
            $bio->setPrenom($data['Biographie']['prenom']   ?? $bio->getPrenom()   ?? null);
            $bio->setAdresse($data['Biographie']['adresse']  ?? $bio->getAdresse()  ?? null);
            $bio->setPhone($data['Biographie']['phone']    ?? $bio->getPhone()    ?? null);
            $bio->setEmail($data['Biographie']['email']    ?? $bio->getEmail()    ?? null);

            // 2. Supprimer toutes les BiographieSuite existantes
            foreach ($bio->getBiographieSuites() as $oldSuite) {
                $em->remove($oldSuite);
            }
            // Optionnel mais recommandé : vider la collection côté entité
            $bio->getBiographieSuites()->clear();

            // 3. Recréer les suites à partir de ce qui arrive dans la requête
            if (!empty($data['BiographieSuite']['contenus']) && is_array($data['BiographieSuite']['contenus'])) {
                foreach ($data['BiographieSuite']['contenus'] as $suiteData) {
                    $suite = new BiographieSuite();
                    $suite->setBiographie($bio);

                    $suite->setTitre($suiteData['titre']    ?? null);
                    $suite->setContenue($suiteData['contenue'] ?? null);

                    $bio->addBiographieSuite($suite);
                    $em->persist($suite);
                }
            }

            // Persist du parent (souvent redondant mais sans danger)
            $em->persist($bio);
        }

        // Competence
        if (!empty($data['Competence']) && is_array($data['Competence'])) {
            $c = $cv->getCompetence() ?? new Competence();
            $cv->setCompetence($c);
            $em->persist($c);

            $existing = $c->getCompetenceContenus(); // éléments actuels
            $idsFromRequest = [];

            if (!empty($data['Competence']['contenus']) && is_array($data['Competence']['contenus'])) {
                foreach ($data['Competence']['contenus'] as $item) {

                    if (!empty($item['id'])) {
                        // 🔁 UPDATE
                        $cc = $em->getRepository(CompetenceContenu::class)->find($item['id']);
                        if (!$cc) continue;
                    } else {
                        // ➕ CREATE
                        $cc = new CompetenceContenu();
                        $cc->setCompetences($c); // relation ManyToOne
                    }

                    $idsFromRequest[] = $cc->getId();

                    $cc->setChamp($item['champ'] ?? null);
                    $cc->setContenue($item['contenue'] ?? null);

                    $c->addCompetenceContenu($cc);
                    $em->persist($cc);
                }
            }

            // 🗑️ DELETE
            foreach ($existing as $old) {
                if ($old->getId() && !in_array($old->getId(), $idsFromRequest)) {
                    $em->remove($old);
                }
            }
        }


        // Experience
        // Experience (supprimer tout + recréer à partir de la requête)
        if (!empty($data['Experience']) && is_array($data['Experience'])) {
            $experience = $cv->getExperience() ?? new Experience();
            $cv->setExperience($experience);
            $em->persist($experience);

            // 1. Supprimer tous les anciens contenus
            foreach ($experience->getExperienceContenu() as $oldItem) {
                $em->remove($oldItem);
            }
            // On peut aussi vider la collection côté objet (optionnel mais plus propre)
            $experience->getExperienceContenu()->clear();

            // 2. Recréer les nouveaux à partir de ce qui arrive dans la requête
            if (!empty($data['Experience']['contenus']) && is_array($data['Experience']['contenus'])) {
                foreach ($data['Experience']['contenus'] as $item) {
                    $contenu = new ExperienceContenu();
                    $contenu->setExperience($experience);

                    if (!empty($item['anneeDebut'])) {
                        try {
                            $contenu->setAnneeDebut(new \DateTime($item['anneeDebut']));
                        } catch (\Exception $e) {
                            // optionnel : logger ou ignorer silencieusement les dates invalides
                        }
                    }

                    if (!empty($item['anneeFin'])) {
                        try {
                            $contenu->setAnneeFin(new \DateTime($item['anneeFin']));
                        } catch (\Exception $e) {
                            // idem
                        }
                    }
                    $contenu->setPosteActuel(
                        filter_var($item['posteActuel'] ?? false, FILTER_VALIDATE_BOOLEAN)
                    );
                    $contenu->setEntreprise($item['entreprise'] ?? null);
                    $contenu->setDuree(isset($item['duree']) ? (int)$item['duree'] : null);
                    $contenu->setTitre($item['titre'] ?? null);
                    $contenu->setDescription($item['description'] ?? null);
                    $contenu->setTypeTravail($item['typeTravail'] ?? null);

                    $experience->addExperienceContenu($contenu);
                    $em->persist($contenu);
                }
            }

            $em->persist($experience); // pas toujours nécessaire, mais inoffensif
        }

        // Formation (delete all + recreate)
        if (!empty($data['Formation']) && is_array($data['Formation'])) {
            $f = $cv->getFormation() ?? new Formation();
            $cv->setFormation($f);
            $em->persist($f);

            // 🗑️ Supprimer tous les anciens
            foreach ($f->getFormationContenu() as $old) {
                $em->remove($old);
            }

            // ➕ Recréer
            if (!empty($data['Formation']['contenus'])) {
                foreach ($data['Formation']['contenus'] as $formationData) {
                    $fo = new FormationContenu();
                    $fo->setFormation($f);

                    $fo->setEcole($formationData['ecole'] ?? null);
                    $fo->setLieu($formationData['lieu'] ?? null);

                    if (!empty($formationData['anneeDebut'])) {
                        $fo->setAnneeDebut(new \DateTime($formationData['anneeDebut']));
                    }

                    if (!empty($formationData['anneeFin'])) {
                        $fo->setAnneeFin(new \DateTime($formationData['anneeFin']));
                    }

                    $fo->setDiplome($formationData['diplome'] ?? null);

                    $em->persist($fo);
                }
            }
        }

        // Langue (supprimer tout + recréer à partir de la requête)
        if (!empty($data['Langue']) && is_array($data['Langue'])) {
            $langue = $cv->getLangue() ?? new Langue();
            $cv->setLangue($langue);
            $em->persist($langue);

            // 1. 🗑️ Supprimer TOUS les anciens contenus
            foreach ($langue->getLangueContenue() as $oldItem) {
                $em->remove($oldItem);
            }
            // Optionnel mais propre : vider la collection côté objet
            $langue->getLangueContenue()->clear();

            // 2. ➕ Recréer les NOUVEAUX à partir de la requête
            if (!empty($data['Langue']['contenus']) && is_array($data['Langue']['contenus'])) {
                foreach ($data['Langue']['contenus'] as $item) {
                    $contenu = new LangueContenue();
                    $contenu->setLangue($langue);

                    $contenu->setLanguage($item['langue'] ?? null);
                    $contenu->setNiveau($item['niveau'] ?? null);

                    $langue->addLangueContenue($contenu);
                    $em->persist($contenu);
                }
            }

            $em->persist($langue); // Sécurité supplémentaire (inoffensif)
        }

       
        // Photo
        $file = $request->files->get('file') ?? null;
        if ($file !== null) {
            $result = $fileUploader->upload($file);
         
            if ($result !== null) {
                $p = $cv->getPhoto() ?? new Photo();
                $p->setPathAbsolute($result['absolute_path'] ?? null);
                $p->setPathRelative($result['public_path'] ?? null);
                $cv->setPhoto($p);
                $em->persist($p);
            }
        }
   
        // Persist and flush
        $em->persist($cv);
        $em->flush();

        return new JsonResponse([
            'id' => $cv->getId(),
        ], Response::HTTP_OK);
    }
}
