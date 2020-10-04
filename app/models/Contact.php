<?php

namespace App\Models;

/**
 * Contact - модель пользователя
 */
class Contact extends Model
{
    public function photoUrl()
    {
        return $this->photo ? 'public/uploads/'.$this->photo : false;
    }

    public function customSort(string $sort)
    {
        switch ($sort) {
          case 'date_asc':
            return $this->addOrder(['id' => 'ASC']);
            break;

          case 'date_desc':
            return $this->addOrder(['id' => 'DESC']);
            break;
          
          default:
            return $this;
            break;
        }
    }
}