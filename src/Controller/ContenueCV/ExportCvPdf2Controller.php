<?php

namespace App\Controller\ContenueCV;

use App\Entity\ContenueCV;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Routing\Attribute\Route;

class ExportCvPdf2Controller extends AbstractController
{
    #[Route('/api/contenue/export-pdf2/{id}', name: 'contenue_cv_export_pdf2', methods: ['GET'])]
    public function exportPdf2(
        int $id,
        EntityManagerInterface $em
    ): Response {
        $cv = $em->getRepository(ContenueCV::class)->find($id);

        if (!$cv) {
            return new Response('CV non trouvé', Response::HTTP_NOT_FOUND);
        }

        // Optionnel : Vérifier que le CV appartient bien à l'utilisateur connecté
        // $this->denyAccessUnlessGranted('view', $cv);   ← si tu as un Voter

        // Options Dompdf (très important pour les images + français)
        $options = new Options();
        $options->set('defaultFont', 'DejaVuSans');           // support unicode + accents
        $options->set('isRemoteEnabled', true);               // autorise les images distantes (http://)
        $options->set('isHtml5ParserEnabled', true);
        $options->set('tempDir', sys_get_temp_dir());

        $dompdf = new Dompdf($options);

        // Rendu du template Twig (on réutilise presque le même HTML que ton front)
        $html = $this->renderView('contenue_cv/cv_pdf_2.html.twig', [
            'cv' => $cv,
            'api_url' => rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/'), // ou ton paramètre API_URL
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Nom du fichier : prénom-nom-cv.pdf (attention aux caractères spéciaux)
        $nom = $cv->getBiographie()?->getNom() ?? 'cv';
        $prenom = $cv->getBiographie()?->getPrenom() ?? '';
        $filename = trim(str_replace(' ', '-', $prenom . '-' . $nom . '-cv')) . '.pdf';

        // Stream vers le navigateur (téléchargement direct)
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}