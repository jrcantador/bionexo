<?php

namespace App\Services;

use App\Repositories\InformationRepository;
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
            if(count($info) > 0){
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
