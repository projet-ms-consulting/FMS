<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use PhpZip\ZipFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ZipGeneratorController extends AbstractController
{
    #[Route('/zip/generator', name: 'app_zip_generator')]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $outputFilename = 'factures.zip';
        // create new archive
        $zipFile = new ZipFile();
        try{
            $invoiceDir = $this->getParameter('kernel.project_dir') . '/invoice/mission/';
            $zipFile->addDirRecursive($invoiceDir, 'invoice');
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
