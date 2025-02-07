<?php

namespace App\Models;

use CodeIgniter\Model;

class Course_model extends Model
{
    protected $table = 'course_review';
    protected $allowedFields = ['course_code', 'course_name'];

    public function insert_course($data) {
        $db = \Config\Database::connect();
        $builder = $db->table('course_review');
        return $builder->insert($data);
    }
}