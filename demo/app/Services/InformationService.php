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

        dd($infos);

        $head01 = [
            "1 - Registro ANS" => [],
            "3 - Nome da Operadora" => [],
            "4 - CNPJ da Operadora" => [],
            "5 - Data de Emissão" => [],
            "6 - Código na Operadora" => [],
            "7 - Nome do Contratado" => [],
            "8 - Código CNES" => [],
            "9 - Número do Lote" => [],
            "10 - Nº do Protocolo (Processo)" => [],
            "11- Data do Protocolo" => [],
            "12 - Código da Glosa do Protocolo" => []
        ];

        $head02 = [];

        $head03 = [];

        // Primeira Página
        foreach ($infos as $key => $info) {
            $padrao = "/1 - Registro ANS/";
            if (preg_match($padrao, $info)) {
                $head01["1 - Registro ANS"][] = $infos[$key + 1];
                continue;
            }

            $padrao = "/3 - Nome da Operadora/";
            if (preg_match($padrao, $info)) {
                $head01["3 - Nome da Operadora"][] =
                    $row[] = $infos[$key + 1];
                continue;
            }

            $padrao = "/4 - CNPJ da Operadora/";
            if (preg_match($padrao, $info)) {
                $head01["4 - CNPJ da Operadora"][] = $infos[$key + 1];
                continue;
            }

            $padrao = "/TOTAL GERAL/";
            if (preg_match($padrao, $info)) {
                $head01["5 - Data de Emissão"][] = $infos[$key + 1];
                continue;
            }

            $padrao = "/6 - Código na Operadora/";
            if (preg_match($padrao, $info)) {
                $head01["6 - Código na Operadora"][] = $infos[$key - 1];
                continue;
            }

            $padrao = "/7 - Nome do Contratado/";
            if (preg_match($padrao, $info)) {
                $head01["7 - Nome do Contratado"][] = $infos[$key - 1];
                continue;
            }

            $padrao = "/8 - Código CNES/";
            if (preg_match($padrao, $info)) {
                $heads["9 - Número do Lote"][] = $infos[$key + 1];
                continue;
            }
            $padrao = "/9 - Número do Lote/";
            if (preg_match($padrao, $info)) {
                $head01["10 - Nº do Protocolo (Processo)"][] = $infos[$key - 1];
                continue;
            }

            $padrao = "/10 - Nº do Protocolo/";
            if (preg_match($padrao, $info)) {
                $heads["11- Data do Protocolo"][] = $infos[$key + 1];
                continue;
            }

            $padrao = "/12 - Código da Glosa do Protocolo/";
            if (preg_match($padrao, $info)) {
                $head01["8 - Código CNES"][] = $infos[$key + 1];
                $head01["12 - Código da Glosa do Protocolo"][] = $infos[$key + 2];
                continue;
            }
        }

        // Primeira Página
        foreach ($infos as $key => $info) {
            $padrao = "/13 - Número da Guia no Prestador/";
            if (preg_match($padrao, $info)) {
                $head02["13 - Número da Guia no Prestador"][] = $infos[$key - 1];
                continue;
            }
            $padrao = "/14 - Número da Guia Atribuido pela Operadora/";
            if (preg_match($padrao, $info)) {
                $head02["14 - Número da Guia Atribuido pela Operadora"][] = $infos[$key - 1];
                continue;
            }
            $padrao = "/15 - Senha/";
            if (preg_match($padrao, $info)) {
                $head02["14 - Número da Guia Atribuido pela Operadora"][] = $infos[$key - 1];
                continue;
            }

            $padrao = "/16 - Nome do Beneficiário/";
            if (preg_match($padrao, $info)) {
                $head02["16 - Nome do Beneficiário"][] = $infos[$key - 1];
                continue;
            }
            $padrao = "/17 - Número da Carteira/";
            if (preg_match($padrao, $info)) {
                $head02["16 - Nome do Beneficiário"][] = $infos[$key - 1];
                continue;
            }
            $padrao = "/18 - Data Início do Faturamento/";
            if (preg_match($padrao, $info)) {
                $head02["18 - Data Início do Faturamento"][] = $infos[$key - 1];
                continue;
            }
            $padrao = "/19 - Hora Início do Faturamento/";
            if (preg_match($padrao, $info)) {
                $head02["19 - Hora Início do Faturamento"][] = $infos[$key - 1];
                continue;
            }
            $padrao = "/20 - Data Fim do Faturamento/";
            if (preg_match($padrao, $info)) {
                $head02["20 - Data Fim do Faturamento"][] = $infos[$key - 1];
                continue;
            }
            $padrao = "/21 - Hora Fim do Faturamento/";
            if (preg_match($padrao, $info)) {
                $head02["21 - Hora Fim do Faturamento"][] = $infos[$key - 1];
                continue;
            }
            $padrao = "/22 - Código da Glosa da Guiao/";
            if (preg_match($padrao, $info)) {
                $head02["22 - Código da Glosa da Guia"][] = $infos[$key - 1];
                continue;
            }
            
        }

           

        // Dados da Guia
        $tableInfos = [];
        $initiaded = false;
        $countIndex = 0;
        foreach ($infos as $key => $info) {
            if (
                !preg_match("/22 - Código da Glosa da Guia/", $info) &&
                !$initiaded
            ) {
                continue;
            }

            if (preg_match("/22 - Código da Glosa da Guia/", $info)) {
                $countIndex++;
                $initiaded = true;
                $tableInfos[] = $infos[$key + 1];
            } elseif ($initiaded && !preg_match("/23 - Data de/", $info)) {
                $tableInfos[] = $infos[$key + 1];
            } elseif ($initiaded && preg_match("/23 - Data de/", $info)) {
                $countInfos = (count($tableInfos) - 1) / 10;
                $values = array_chunk($tableInfos, $countInfos);
                foreach ($values as $index => $value) {
                    foreach ($value as $infoField) {
                        if ($index == 0) {
                            $head03["23 - Data de realização"][] = $infoField;
                        } elseif ($index == 1) {
                            $head03["24 - Tabela /Item assistencial"][] = $infoField;
                        } elseif ($index == 2) {
                            $head03["25 - Código Procedimento"][] = $infoField;
                        } elseif ($index == 3) {
                            $head03["26 - Descrição"][] = $infoField;
                        } elseif ($index == 4) {
                            $head03["27 - Grau de Participação - Descrição"][] = $infoField;
                        } elseif ($index == 5) {
                            $head03["28 - Valor informado"][] = $infoField;
                        } elseif ($index == 6) {
                            $head03["29 -Quantidade executada"][] = $infoField;
                        } elseif ($index == 7) {
                            $head03["30 - Valor processado"][] = $infoField;
                        } elseif ($index == 8) {
                            $head03["31 - Valor liberado"][] = $infoField;
                        } elseif ($index == 9) {
                            $head03["32 - Valor glosao"][] = $infoField;
                        }
                    }
                }
                $initiaded = false;
                $tableInfos = [];                
            }
        }

        $file = fopen("storage/file.csv", "w");
        fputcsv($file, array_keys($head01), ",");

        $countRows = 0;
        foreach ( $head01  as $key => $head) {
            $countRows += count($head);
        }
        $countRows = $countRows / count($head01);


        $rows = [];
        for ($i = 0; $i  < $countRows; $i++) {
            $row = [];
            foreach ($head01 as $key => $head) {
                $row[] = (count($head) - 1) >= $i ? $head[$i] : "";
            }
            $rows[] = $row;
        }

        foreach ($rows as $row) {
            fputcsv($file, $row, ",");
        }

        

        fputcsv($file, [], ",");

        fputcsv($file, array_keys($head03), ",");
        $countRows = 0;
        foreach ( $head03  as $key => $head) {
            $countRows += count($head);
        }
        $countRows = $countRows / count($head01);


        $rows = [];
        for ($i = 0; $i  < $countRows; $i++) {
            $row = [];
            foreach ($head03 as $key => $head) {
                $row[] = (count($head) - 1) >= $i ? $head[$i] : "";
            }
            $rows[] = $row;
        }

        foreach ($rows as $row) {
            fputcsv($file, $row, ",");
        }

        fclose($file);
       

        return "Concluído";
    }
}
