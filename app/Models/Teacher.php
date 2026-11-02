<?php

namespace App\Models;

use Database\Factories\TeacherFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nip', 'name', 'email', 'phone', 'gender', 'address'])]
class Teacher extends Model
{
    /** @use HasFactory<TeacherFactory> */
    use HasFactory;
}
