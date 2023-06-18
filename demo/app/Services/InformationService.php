<?php

namespace App\Services;

use App\Repositories\InformationRepository;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\Firefox\FirefoxOptions;


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

    public function setDocument()
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

    public function create()
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

    public function downlaod()
    {
        $serverUrl = 'http://selenium-hub:4444/';
        $driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());
        try {
            \Log::error($_SERVER['DOCUMENT_ROOT']);
            $driver->get('https://testpages.herokuapp.com/files/textfile.txt');
            $fileText = $driver->findElement(WebDriverBy::tagName('pre'))->getText();
            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/Teste TKS.txt","wb");
            fwrite($fp,$fileText);
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
}
