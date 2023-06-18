<?php

namespace App\Http\Controllers;

use App\Services\InformationService;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    private $informationService;

    public function __construct(
        InformationService $informationService
    ) {
        $this->informationService = $informationService;
    }

    public function save(Request $request)
    {
        try {            
            $response = $this->informationService->create($request->all());            
            return response($response, 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response(["message" => $e->getMessage()], 500);
        }
    }

    public function setDocument(){
        try {            
            $response = $this->informationService->setDocument();            
            return response($response, 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response(["message" => $e->getMessage()], 500);
        }
    }
}
