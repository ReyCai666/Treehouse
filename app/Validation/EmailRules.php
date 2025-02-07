<?php

namespace App\Validation;

class EmailRules
{
    public function check_uqconnect_domain(string $str, string &$error = null): bool
    {
        $UQpattern = '/@uq/';
        if (preg_match($UQpattern, $str)) {
            return true;
        }
    
        $error = 'The email must have the UQ domain.';
        return false;
    }
}
