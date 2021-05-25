<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Utils;

class Validator
{
    public function validateUsername($username)
    {
        if (empty($username)) {
            throw new \Exception('The username can not be empty.');
        }

        if (1 !== preg_match('/^[a-z0-9]+$/', $username)) {
            throw new \Exception('The username must contain only lowercase alpha numeric characters.');
        }

        return $username;
    }

    public function validatePassword($plainPassword)
    {
        if (empty($plainPassword)) {
            throw new \Exception('The password can not be empty.');
        }

        if (mb_strlen(trim($plainPassword)) < 6) {
            throw new \Exception('The password must be at least 6 characters long.');
        }

        return $plainPassword;
    }

    public function isValidatePassword($plainPassword)
    {
        if (empty($plainPassword)) {
            return false;
        }

        if (mb_strlen(trim($plainPassword)) < 6) {
            return true;
        }
    }

    public function validateEmail($email)
    {
        if (empty($email)) {
            throw new \Exception('The email can not be empty.');
        }

        if (false === mb_strpos($email, '@')) {
            throw new \Exception('The email should look like a real email.');
        }

        return $email;
    }

    public function validateFullname($fullname)
    {
        if (empty($fullname)) {
            throw new \Exception('The full name can not be empty.');
        }

        return $fullname;
    }

    public function validateType($type)
    {
        if (empty($type)) {
            throw new \Exception('Scholar type cannot be empty.');
        }
        $e = ($type === 'msc' || $type === 'phd');
        if (!$e) {
            throw new \Exception('Value must be msc or phd.');
        }
        return $type;
    }

    public function validateNumber($number)
    {
        if (empty($number)) {
            throw new \Exception('The number to generate can not be empty.');
        }

        if (1 !== preg_match('/^[0-9]+$/', $number)) {
            throw new \Exception('Must be a number.');
        }

        return $number;
    }
}
