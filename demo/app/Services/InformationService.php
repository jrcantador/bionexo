<?php

namespace App\Services;

use App\Repositories\InformationRepository;

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

    public function create($data)
    {
        return $this->informationRepository->create($data);
    }

    public function delete(int $id)
    {
        $this->informationRepository->create($id);
    }
}
