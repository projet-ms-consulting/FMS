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
        $invoiceDir = realpath($this->getParameter('kernel.project_dir') . '/invoice/');
        $invoice = $invoiceRepository->findAll();
        // create new archive
        $zipFile = new ZipFile();
        try{
            $zipFile
                ->addDir($invoiceDir, 'invoice') // add files from the directory
                ->saveAsFile($outputFilename) // save the archive to a file
                ->close(); // close archive
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
