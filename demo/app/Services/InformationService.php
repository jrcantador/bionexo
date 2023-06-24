<?php

namespace App\Services;

use App\Repositories\InformationRepository;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\LocalFileDetector;
use Smalot\PdfParser\Parser;

class InformationService
{
    private $informationRepository;

    public function __construct(
        InformationRepository $informationRepository
    ) {
        $this->informationRepository = $informationRepository;
    }

    public function findById($id)
    {
        return $this->informationRepository->findById($id);
    }


    public function update($id, $data)
    {
        return $this->informationRepository->update($id, $data);
    }

    public function setForm()
    {
        $serverUrl = 'http://selenium-hub:4444/';
        $driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());
        try {
            $driver->get('https://testpages.herokuapp.com/styled/basic-html-form-test.html');
            $driver->findElement(WebDriverBy::name('username'))->click()->clear()->sendKeys('Junior'); // fill the search box                        
            $driver->findElement(WebDriverBy::name('password'))->click()->clear()->sendKeys('123456'); // fill the search box
            $driver->findElement(WebDriverBy::name('comments'))->click()->clear()->sendKeys('Teste teste teste');


            $checkboxes = $driver->findElements(WebDriverBy::name('checkboxes[]'));
            $checkboxes = $driver->findElements(WebDriverBy::name('checkboxes[]'));
            $checkboxes[0]->click();
            $checkboxes[1]->click();
            $checkboxes[2]->click();

            $radio = $driver->findElements(WebDriverBy::name('radioval'));
            $radio[0]->click();

            $select = $driver->findElement(WebDriverBy::name('multipleselect[]'));
            $options = $select->findElements(WebDriverBy::tagName('option'));
            $options[0]->click();
            $options[1]->click();
            $options[3]->click();

            $dropdown = $driver->findElement(WebDriverBy::name('dropdown'));
            $dpOptions = $dropdown->findElement(WebDriverBy::tagName('option'));
            $dpOptions->click();

            $fileInput = $driver->findElement(WebDriverBy::name('filename'));
            $fileInput->setFileDetector(new LocalFileDetector());
            $filePath = 'storage/doc.txt';
            $fileInput->sendKeys($filePath)->submit();


            $result = $driver->findElement(WebDriverBy::tagName("body"))->getDomProperty("innerHTML");
            return $result;
        } finally {
            $driver->quit();
        }
    }

    public function saveInfosTable()
    {
        $serverUrl = 'http://selenium-hub:4444/';
        $driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());
        try {
            $driver->get('https://testpages.herokuapp.com/styled/tag/table.html');
            $trs = $driver->findElement(WebDriverBy::id('mytable'))->findElements(WebDriverBy::tagName("tr"));
            foreach ($trs as $key => $tr) {
                $tds = $tr->findElements(WebDriverBy::tagName("td"));
                $info = [];
                foreach ($tds as $key => $td) {
                    $info[$key ? "amount" : "name"] = $td->getText();
                }
                if (count($info) > 0) {
                    $data[] = $this->informationRepository->create($info);
                }
            }
            return $data;
        } finally {
            $driver->quit();
        }
    }

    public function download()
    {
        $serverUrl = 'http://selenium-hub:4444/';
        $driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());
        try {
            $driver->get('https://testpages.herokuapp.com/files/textfile.txt');
            $fileText = $driver->findElement(WebDriverBy::tagName('pre'))->getText();
            $fp = fopen("storage/Teste TKS.txt", "wb");
            fwrite($fp, $fileText);
            fclose($fp);
            return "Arquivo criado em " . url("storage/Teste TKS.txt");
        } finally {
            $driver->quit();
        }
    }

    public function upload()
    {
        $serverUrl = 'http://selenium-hub:4444/';
        $driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());
        try {
            $driver->get('https://testpages.herokuapp.com/styled/file-upload-test.html');
            $fileInput = $driver->findElement(WebDriverBy::id('fileinput'));
            $fileInput->setFileDetector(new LocalFileDetector());
            $fileInput->sendKeys("storage/Teste TKS.txt");
            $driver->findElement(WebDriverBy::id('itsafile'))->click();
            $driver->findElement(WebDriverBy::id('itsafile'))->click()->submit();

            $result = $driver->findElement(WebDriverBy::tagName("body"))->getDomProperty("innerHTML");
            return $result;
        } finally {
            $driver->quit();
        }
    }

    public function readPdf()
    {
        $parser = new Parser();
        $pdf = $parser->parseFile('storage/Leitura PDF.PDF');
        $pages = $pdf->getPages();
        $infos = [];

        foreach ($pages as $page) {
            $infos = array_merge($infos, array_map(function ($row) {
                return  $row[1];
            }, $page->getDataTm()));
        }
        $head = [];
        $rows = [];
        $row = [];
        foreach ($infos as $key => $info) {
           
            $padrao = "/1 - Registro ANS/";
            if (preg_match($padrao, $info)) {
                $head[] = "1 - Registro ANS";
                $row[] = $infos[$key + 1];                
                continue;
            }

            $padrao = "/3 - Nome da Operadora/";
            if (preg_match($padrao, $info)) {
                $head[] = "3 - Nome da Operadora";
                $row[] = $infos[$key + 1];
                continue;
            }

            $padrao = "/4 - CNPJ da Operadora/";
            if (preg_match($padrao, $info)) {
                $head[] = "4 - CNPJ da Operadora";
                $row[] = $infos[$key + 1];
                continue;
            }

            $padrao = "/TOTAL GERAL/";
            if (preg_match($padrao, $info)) {
                $head[] = "5 - Data de Emissão";
                $row[] = $infos[$key + 1];
                continue;
            }

            $padrao = "/6 - Código na Operadora/";
            if (preg_match($padrao, $info)) {
                $head[] = "6 - Código na Operadora";
                $row[] = $infos[$key - 1];
                continue;
            }

            $padrao = "/7 - Nome do Contratado/";
            if (preg_match($padrao, $info)) {
                $head[] = "7 - Nome do Contratado";
                $row[] = $infos[$key - 1];
                continue;
            }

            $padrao = "/8 - Código CNES/";
            if (preg_match($padrao, $info)) {
                $head[] = "9 - Número do Lote";
                $row[] = $infos[$key + 1];
                continue;
            }
            $padrao = "/9 - Número do Lote/";
            if (preg_match($padrao, $info)) {
                $head[] = "10 - Nº do Protocolo (Processo)";
                $row[] = $infos[$key + 1];
                continue;
            }

            $padrao = "/10 - Nº do Protocolo/";
            if (preg_match($padrao, $info)) {
                $head[] = "11- Data do Protocolo";
                $row[] = $infos[$key + 1];
                continue;
            }

            $padrao = "/12 - Código da Glosa do Protocolo/";
            if (preg_match($padrao, $info)) {                
                $head[] = "8 - Código CNES";
                $head[] = "12 - Código da Glosa do Protocolo";
                $row[] = $infos[$key + 1];
                $row[] = $infos[$key + 2];            
                continue;
            }           
        }


        $file = fopen("storage/file.csv", "w");
        fputcsv($file, $head, ",");
        fputcsv($file, $row, ",");
        fclose($file);

        return "Concluído";
    }
}
