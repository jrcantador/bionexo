<?php

namespace App\Repositories;

use App\Models\Information;
use App\Repositories\BaseRepository;

class InformationRepository extends BaseRepository
{
    public function __construct(Information $model)
    {
        $this->model = $model;
    }   
}