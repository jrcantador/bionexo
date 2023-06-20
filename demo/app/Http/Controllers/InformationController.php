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

    public function saveInfosTable(Request $request)
    {
        try {            
            $response = $this->informationService->saveInfosTable($request->all());            
            return response($response, 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response(["message" => $e->getMessage()], 500);
        }
    }

    public function setForm(){
        try {            
            $response = $this->informationService->setForm();            
            return response($response, 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response(["message" => $e->getMessage()], 500);
        }
    }

    public function downlaod(){
        try {            
            $response = $this->informationService->downlaod();            
            return response($response, 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response(["message" => $e->getMessage()], 500);
        }
    }
    public function upload(){
        try {            
            $response = $this->informationService->upload();            
            return response($response, 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response(["message" => $e->getMessage()], 500);
        }
    }
    public function readPdf(){
        try {            
            $response = $this->informationService->readPdf();            
            return response($response, 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response(["message" => $e->getMessage()], 500);
        }
    }
}
