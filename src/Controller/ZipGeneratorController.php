<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Entity\SupplierMission;
use App\Repository\InvoiceRepository;
use App\Repository\MissionRepository;
use App\Repository\SupplierMissionRepository;
use PhpZip\ZipFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ZipGeneratorController extends AbstractController
{
    #[Route('/zip/generator/{id}', name: 'app_zip_generator')]
    public function index(InvoiceRepository $invoiceRepository, MissionRepository $missionRepository, SupplierMissionRepository $supplierRepository, $id, Mission $mission): void
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $mission = $missionRepository->find($id);
        $supplier = $supplierRepository->find($id);
        $invoiceMissions = $mission->getInvoices()->getValues();
        $outputFilename = 'factures.zip';
        // create new archive
        $zipFile = new ZipFile();
        try{
            // Dossier mission
            $invoiceDirMission = $this->getParameter('kernel.project_dir') . '/invoice/' . $id . '/mission/';
            $filesMission = array_diff(scandir($invoiceDirMission), array('.', '..'));

            foreach ($filesMission as $file) {
                $invoice = $invoiceRepository->findOneBy(['file' => $file]);
                $newName = $invoice->getBillNum() . '_' . $invoice->getRealFilename();
                $zipFile->addFile($invoiceDirMission . $file, 'mission/' . $newName);
            }

            $data = [
                ['BillNum', 'RealFilename', 'File', 'Deadline', 'IsPaid'],
            ];
            foreach ($invoiceMissions as $invoice) {
                if ($invoice->isPaid() === true) {
                    $paid = 'Yes';
                } else {
                    $paid = 'No';
                }
                $data[] = [
                    $invoice->getBillNum(),
                    $invoice->getRealFilename(),
                    $invoice->getFile(),
                    $invoice->getDeadline()->format('Y-m-d'),
                    $paid,
                ];

            }

            $csvFilename = 'factures.csv';

            $csvFile = fopen($csvFilename, 'w');

            if ($csvFile === false) {
                die('Error opening the file ' . $csvFilename);
            }

            fputs($csvFile, (chr(0xEF) . chr(0xBB) . chr(0xBF))); // support unicode

            foreach ($data as $row) {
                fputcsv($csvFile, $row, ';');
            }

            fclose($csvFile);
            // Add the CSV file to the zip
            $zipFile->addFile($csvFilename, $csvFilename);

            // Load the CSV data into a Spreadsheet object
            $spreadsheet = IOFactory::load($csvFilename);

            // Get the active sheet
            $sheet = $spreadsheet->getActiveSheet();

            // Iterate over all columns
            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            // Save as an Excel file
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('factures.xlsx');

            $zipFile->addFile('factures.xlsx', 'factures.xlsx');

            $zipFile->saveAsFile($outputFilename)->close();

        }
        catch(\PhpZip\Exception\ZipException $e){
            // handle exception
            error_log($e->getMessage());
        }
        finally{
            $zipFile->close();
        }
        // Check if the file exists and is readable
        if (file_exists($outputFilename) && is_readable($outputFilename)) {
            // Send the file to the client
            $response = $this->file($outputFilename);

            // Ignore user aborts and allow the script to run forever
            ignore_user_abort(true);
            set_time_limit(0);

            // Send the response to the client
            $response->send();

            // Check if the connection is still active
            if (connection_status() == CONNECTION_NORMAL) {
                // Delete the files
                unlink($csvFilename);
                unlink($outputFilename);
                unlink('factures.xlsx');
            }
            dd('ok');
        }
    }
}
