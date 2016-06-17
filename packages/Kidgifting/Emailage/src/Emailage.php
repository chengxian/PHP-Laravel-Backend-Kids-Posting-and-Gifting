<?php
/**
 * @author: chengxian
 * Date: 3/7/16
 * @copyright Cheng Xian Lim
 */


namespace Kidgifting\Emailage;


/**
 * Class Emailage
 * @package Kidgifting\Emailage
 *
 * $table->boolean('emailage_validated')->default(false);
 * $table->boolean('emailage_score')->default(-1);
 * $table->boolean('emailage_band')->default(-1);
 */

trait Emailage
{
    public static function bootEmailage()
    {
        $validateClosure = function($item) {

            return true;
        };
        /*
         * Laravel does not provide a "loading" event
         * See notes under decrypt below
        $dencryptClosure = function($item) {
            $item->setVaultClient(self::getVaultClient());
            $item->decrypt();
            return true;
        };
        */
        // Encrypt before writing to disk
        static::saving($validateClosure);
        static::creating($validateClosure);
        static::updating($validateClosure);
    }

    protected function validateEmail($email, $ip, EmailageWrapper $client)
    {
        $results = $client->validate($email, $ip);

        $this->emailage_validated = true;
        $this->emailage_score = $results['emailage_score'];
        $this->emailage_band = $results['emailage_band'];
    }
}