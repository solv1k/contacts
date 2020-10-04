<?php

namespace App\Models;

use App\Models\Contact;

/**
 * User - модель пользователя
 */
class User extends Model
{
    public function contacts()
    {
        return Contact::where([
          'user_id' => $this->id
        ]);
    }
}