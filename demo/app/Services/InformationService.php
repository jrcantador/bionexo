<?php

namespace App\Services;

use App\Repositories\InformationRepository;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\LocalFileDetector;
use League\CommonMark\Delimiter\Delimiter;
use Smalot\PdfParser\Parser;
use Smalot\PdfParser\Config;

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
            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/Teste TKS.txt", "wb");
            fwrite($fp, $fileText);
            fclose($fp);
            return "Concluido";
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
            $fileInput->sendKeys($_SERVER['DOCUMENT_ROOT'] . "/Teste TKS.txt");
            $driver->findElement(WebDriverBy::id('itsafile'))->click();
            $driver->findElement(WebDriverBy::id('itsafile'))->click()->submit();

            $result = $driver->findElement(WebDriverBy::tagName("body"))->getDomProperty("innerHTML");
            return $result;

            return "Concluido";
        } finally {
            $driver->quit();
        }
    }

    public function readPdf()
    {
        $parser = new Parser();
        $pdf = $parser->parseFile('storage/Leitura PDF.PDF');
        $pages = $pdf->getPages();
        $headRegex =  '/^\d{1,2} - /';
        $infos = [];

        $test = $pages[0]->getDataTm();

        foreach ($pages as $index => $page) {
            array_push($infos, array_map(function ($row) use ($index, $headRegex) {
                return [
                    "page" => $index,
                    "x" => floatval($row[0][4]),
                    "y" => floatval($row[0][5]),
                    "value" => $row[1],
                    "id" => preg_match($headRegex,  $row[1]) ? "head" : "value"
                ];
            }, $page->getDataTm()));
        }

        $heads = [];
        foreach ($infos as $key => $infosPages) {
            $transform = array_filter($infosPages, function ($info) {
                return $info['id'] == "head";
            });
            $heads = array_merge($heads, $transform);
        }

        $values = [];
        foreach ($infos as $key => $infosPages) {
            $transform = array_filter($infosPages, function ($info) {
                return  $info['id'] == "value";
            });
            $values = array_merge($values, $transform);
        }

        $data = [];
        foreach ($heads as $key => $head) {
            $value = $this->closerElement($head, $values);
            $data[] = [
                "page" => $value["page"],
                "head" => $head["value"],
                "value" => $value["value"]
            ];
        }


        $csvFile = 'storage/arquivo.csv';
        $csvHandler = fopen($csvFile, 'w');

        $finalHead = array_unique(array_map(function ($head) {
            return $head["value"];
        }, $heads));

        fputcsv($csvHandler, $finalHead);


        $finalInfos = [];
        for ($i = 0; $i < count($pages); $i++) {
            $row = [];
            foreach ($finalHead as $head) {               
                foreach ($data as $value) {
                    if ($value["page"] == $i && $head == $value["head"]) {
                        array_push($row, $value["value"]);
                        break;
                    }                   
                }
            }
            $finalInfos[] = $row;
        };
        foreach ($finalInfos as $infos) {
            fputcsv($csvHandler, $infos);
        }


        fclose($csvHandler);

        return $data;
    }

    private function closerElement($head, $values)
    {
        $closer = [];
        foreach ($values as $value) {
            if ($head["page"] == $value["page"]) {
                if (
                    $value["x"] < $head["x"]
                ) {
                    continue;
                }

                if (count($closer) == 0) {
                    $closer = $value;
                }


                if (
                    $this->getDistanceBetweenPointsNew($head["x"], $head["y"], $closer["x"], $closer["y"]) >
                    $this->getDistanceBetweenPointsNew($value["x"], $value["y"], $closer["x"], $closer["y"])
                ) {
                    $closer = $value;
                }
            }
        }
        return $closer;
    }

    function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $theta = $longitude1 - $longitude2;
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
        return (round($distance, 2));
    }
}
