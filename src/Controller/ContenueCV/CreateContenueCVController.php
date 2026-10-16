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
use Symfony\Component\HttpFoundation\Response;
use UploadImageBundle\Service\FileUploader;

class CreateContenueCVController extends AbstractController
{
    public function __invoke(
        Request $request,
        EntityManagerInterface $em,
        FileUploader $fileUploader
    ): Response {
        $data = $request->request->all();

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


        if (empty($data)) {
            return new JsonResponse(['error' => 'No FormData provided'], Response::HTTP_BAD_REQUEST);
        }

        $cv = new ContenueCV();

        // Utilisateur (by id)
        if (!empty($data['utilisateur_id'])) {
            $user = $em->getRepository(Utilisateur::class)->find($data['utilisateur_id']);
            if ($user instanceof Utilisateur) {
                $cv->setUtilisateur($user);
            }
        }

        // Apropos
        if (!empty($data['Apropos']) && is_array($data['Apropos'])) {
            $a = new Apropos();
            $a->setTitre($data['Apropos']['titre'] ?? null);
            $a->setDescription($data['Apropos']['description'] ?? null);
            $cv->setApropos($a);
            $em->persist($a);
        }

        // AutreActivite + AutreActiviteContenue
        if (!empty($data['AutreActivite']) && is_array($data['AutreActivite'])) {
            $aa = new AutreActivite();
            if (!empty($data['AutreActivite']['contenus']) && is_array($data['AutreActivite']['contenus'])) {
                foreach ($data['AutreActivite']['contenus'] as $item) {
                    $cont = new AutreActiviteContenue();
                    $cont->setChamp($item['champ'] ?? null);
                    $cont->setContenue($item['contenue'] ?? null);
                    $cont->setAutreActivite($aa);
                    $aa->addAutreActiviteContenue($cont);
                    $em->persist($cont);
                }
            }
            $cv->setAutreActivite($aa);
            $em->persist($aa);
        }

        // Biographie + BiographieSuite
        if (!empty($data['Biographie']) && is_array($data['Biographie'])) {
            $b = new Biographie();
            $b->setNom($data['Biographie']['nom'] ?? null);
            $b->setPrenom($data['Biographie']['prenom'] ?? null);
            $b->setAdresse($data['Biographie']['adresse'] ?? null);
            $b->setPhone($data['Biographie']['phone'] ?? null);
            $b->setEmail($data['Biographie']['email'] ?? null);

            if (!empty($data['BiographieSuite']['contenus']) && is_array($data['BiographieSuite']['contenus'])) {
                foreach ($data['BiographieSuite']['contenus'] as $suite) {
                    $s = new BiographieSuite();
                    $s->setTitre($suite['titre'] ?? null);
                    $s->setContenue($suite['contenue'] ?? null);
                    $s->setBiographie($b);
                    $b->addBiographieSuite($s);
                    $em->persist($s);
                }
            }

            $cv->setBiographie($b);
            $em->persist($b);
        }

        // Competence + CompetenceContenu
        if (!empty($data['Competence']) && is_array($data['Competence'])) {
            $c = new Competence();
            if (!empty($data['Competence']['contenus']) && is_array($data['Competence']['contenus'])) {
                foreach ($data['Competence']['contenus'] as $item) {
                    $cc = new CompetenceContenu();
                    $cc->setChamp($item['champ'] ?? null);
                    $cc->setContenue($item['contenue'] ?? null);
                    $cc->setCompetences($c);
                    $c->addCompetenceContenu($cc);
                    $em->persist($cc);
                }
            }
            $cv->setCompetence($c);
            $em->persist($c);
        }

        // Experience
        if (!empty($data['Experience']) && is_array($data['Experience'])) {
            $exp = new Experience();
            $cv->setExperience($exp);
            $em->persist($exp);
            if (!empty($data['Experience']['contenus']) && is_array($data['Experience']['contenus'])) {
                foreach ($data['Experience']['contenus'] as $item) {
                    $e = new ExperienceContenu();
                    if (!empty($item['anneeDebut'])) {
                        $e->setAnneeDebut(new \DateTime($item['anneeDebut']));
                    }
                    if (!empty($item['anneeFin'])) {
                        $e->setAnneeFin(new \DateTime($item['anneeFin']));
                    }
                    $e->setEntreprise($item['entreprise'] ?? null);
                    $e->setDuree(isset($item['duree']) ? (int)$item['duree'] : null);
                    $e->setTitre($item['titre'] ?? null);
                    $e->setDescription($item['description'] ?? null);
                    $e->setPosteActuel(
                        filter_var($item['posteActuel'] ?? false, FILTER_VALIDATE_BOOLEAN)
                    );
                    $e->setTypeTravail($item['typeTravail'] ?? null);
                    $e->setExperience($exp);
                    $em->persist($e);
                }
            }
        }

        // Formation
        if (!empty($data['Formation']) && is_array($data['Formation'])) {
            $fo = new Formation();
            $cv->setFormation($fo);
            $em->persist($fo);
            if (!empty($data['Formation']['contenus']) && is_array($data['Formation']['contenus'])) {
                foreach ($data['Formation']['contenus'] as $formationData) {
                    $f = new FormationContenu();
                    $f->setEcole($formationData['ecole'] ?? null);
                    $f->setLieu($formationData['lieu'] ?? null);

                    if (!empty($formationData['anneeDebut'])) {
                        $f->setAnneeDebut(new \DateTime($formationData['anneeDebut']));
                    }

                    if (!empty($formationData['anneeFin'])) {
                        $f->setAnneeFin(new \DateTime($formationData['anneeFin']));
                    }

                    $f->setDiplome($formationData['diplome'] ?? null);

                    $f->setFormation($fo); // ⚠️ relation OneToMany
                    $em->persist($f);
                }
            }
        }


        // Langue (and optional contenues)
        if (!empty($data['Langue']) && is_array($data['Langue'])) {
            $l = new Langue();
            $cv->setLangue($l);
            $em->persist($l);
            if (!empty($data['Langue']['contenus']) && is_array($data['Langue']['contenus'])) {
                foreach ($data['Langue']['contenus'] as $item) {
                    $lc = new LangueContenue();
                    $lc->setLanguage($item['langue'] ?? null);
                    $lc->setNiveau($item['niveau'] ?? null);
                    $lc->setLangue($l);
                    $em->persist($lc);
                }
            }
        }

        // Photo
        $file = $request->files->get('file');
        if ($file !== null) {
            $result = $fileUploader->upload($file);
            if ($result !== null) {
                $p = new Photo();
                $p->setPathAbsolute($result['absolute_path'] ?? null);
                $p->setPathRelative($result['public_path'] ?? null);
                $cv->setPhoto($p);
                $em->persist($p);
            }
        }

        // Persist and flush
        $em->persist($cv);
        $em->flush();

        return new JsonResponse(['id' => $cv->getId(), 'data' => $data], Response::HTTP_CREATED);
    }
}
