<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
  /**
   * Model class for repo.
   *
   * @var string
   */
  protected $model;


  public function findById($id)
  {
    return $this->make()->find($id);
  }

  protected function make(): Model
  {
      return new $this->model;
  }

  public function update($id, $data)
  {
    $plan = $this->findById($id);
    if (!$plan) {
      return null;
    }

    $plan->update($data);
    return $plan;
  }

  public function create($data)
  {
    return $this->make()->create($data);
  }

  public function delete(int $id)
  {
    $information = $this->findById($id);
    if (!$information) {
      return null;
    }

    $information->delete();
    return $information;
  }
}
