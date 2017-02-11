<?php
/**
 * Created by PhpStorm.
 * User: vanyaonn
 * Date: 15-10-29
 * Time: 12:20 AM
 */

namespace PROJECT\Helpers;

class UserHelper
{
    public static function generateRandPassword($length = 8)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$&*_-";
        $password = substr(str_shuffle($chars), 0, $length);
        return $password;
    }

    /**
     * Generate password hash
     * @param  string $password passwrod
     * @return string           encrypted password
     */
    public static function encryptPassword($password)
    {
        return hash("sha256", $password);
    }

    /**
     * Verify passwd
     * @param  string $password       password
     * @param  string $hashedPassword hashedPassword
     * @return boolean
     */
    public static function verifyPasswd($password, $hashedPassword)
    {
        return hash("sha256", $password) == $hashedPassword;
    }
}
