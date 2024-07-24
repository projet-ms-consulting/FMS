<?php
namespace App\Service;

use App\Entity\Mission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppFiles extends AbstractController
{
    public function recursiveRemoveDirectory($dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->recursiveRemoveDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    /**
     * @param mixed $invoice
     * @param Mission $mission
     * @param EntityManagerInterface $entityManager
     * @return void
     */
    public function deleteInvoiceSuppliers(mixed $invoice, Mission $mission, EntityManagerInterface $entityManager): void
    {
        foreach ($invoice->getInvoiceSuppliers()->getValues() as $invoiceSupplier) {
            $file = $this->getParameter('kernel.project_dir') . '/facture/mission/' . $mission->getId() . '/supplier/' . $invoiceSupplier->getFile();
            if (file_exists($file)) {
                unlink($file);
            }
            $entityManager->remove($invoiceSupplier);
        }

        // Delete invoice
        $file = $this->getParameter('kernel.project_dir') . '/facture/mission/' . $mission->getId() . '/' . $invoice->getFile();
        if (file_exists($file)) {
            unlink($file);
        }
        $entityManager->remove($invoice);
    }
}