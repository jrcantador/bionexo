<?php

namespace App\Services;

use App\Repositories\InformationRepository;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use  Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Remote\LocalFileDetector;

use DOMDocument;
use DOMXPath;

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

            // Go to URL

            \Log::error('echo "Teste 2"');
            $driver->get('https://testpages.herokuapp.com/styled/basic-html-form-test.html');

            \Log::error('echo "Teste "');

            $name = $driver->findElement(WebDriverBy::name('username'))->click()->clear()->sendKeys('Junior'); // fill the search box                        
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

            //getting the input element
            $fileInput = $driver->findElement(WebDriverBy::name('filename'));

            //set the fileDetector
            $fileInput->setFileDetector(new LocalFileDetector());

            $filePath = 'storage/doc.txt';
            $fileInput->sendKeys($filePath)->submit();


            $result = $driver->findElement(WebDriverBy::tagName("body"))->getText();



            \Log::error("Aoba boba");
            return $result;
        } finally {
            $driver->quit();
        }
    }

    public function create()
    {
        $contents = file_get_contents("https://testpages.herokuapp.com/styled/tag/table.html");
        $document = new DOMDocument();
        $document->loadHTML($contents);
        $xpath = new DOMXPath($document);
        $table = $xpath->query("//*[@id='mytable']")->item(0);

        // for printing the whole html table just type: print $xml->saveXML($table); 

        $rows = $table->getElementsByTagName("tr");
        $data = [];

        foreach ($rows as $row) {
            $cells = $row->getElementsByTagName('td');
            $info = [];
            foreach ($cells as $key => $cell) {
                $info[$key ? "amount" : "name"] = $cell->nodeValue; // print cells' content as 124578
            }
            if (count($info) > 0) {
                $data[] = $this->informationRepository->create($info);
            }
        }
        return $data;
    }

    public function delete(int $id)
    {
        $this->informationRepository->create($id);
    }
}
