<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use App\Repository\MissionRepository;
use PhpZip\ZipFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ZipGeneratorController extends AbstractController
{
    #[Route('/zip/generator', name: 'app_zip_generator')]
    public function index(InvoiceRepository $invoiceRepository, MissionRepository $missionRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $outputFilename = 'factures.zip';
        // create new archive
        $zipFile = new ZipFile();
        try{
            $invoiceDir = $this->getParameter('kernel.project_dir') . '/invoice/mission/';
            $files = array_diff(scandir($invoiceDir), array('.', '..'));

            foreach ($files as $file) {
                // Change the name of the file before adding it to the zip
                $newName = 'new_name_' . $file;
                $zipFile->addFile($invoiceDir . $file, 'invoice/' . $newName);
            }

            // Create a CSV file for each mission
            $missions = $missionRepository->findAll();
            foreach ($missions as $mission) {
                $csvFile = fopen('mission_' . $mission->getId() . '.csv', 'w');
                fputcsv($csvFile, ['Id', 'Nom', 'Description', 'Prix', 'Client', 'Manageur', 'Fini', 'Créer le']);
                fputcsv($csvFile, [$mission->getId(), $mission->getName(), $mission->getDescription(), $mission->getPrice(), $mission->getClient()->getName(), $mission->getManager()->getName(), $mission->isFinished(), $mission->getCreatedAt()->format('Y-m-d H:i:s')]);
                fclose($csvFile);
                $zipFile->addFile('mission_' . $mission->getId() . '.csv', 'missions/mission_' . $mission->getId() . '.csv');
            }

            $zipFile->saveAsFile($outputFilename)->close();
        }
        catch(\PhpZip\Exception\ZipException $e){
            // handle exception
        }
        finally{
            $zipFile->close();
        }
        return $this->file($outputFilename);
    }
}
