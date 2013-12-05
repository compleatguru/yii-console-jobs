<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserPasswordService
 *
 * @author David
 */
class UserEncryptionService extends CComponent {

    public function encode($str) {
        return md5($str);
    }

    /**
     *
     * @param User $user
     * @param integer $stampTime
     * @return string
     */
    public function generateSignature1($user, $stampTime) {
        $params = array(
            'email' => $user->email,
            'contact_number' => $user->contact_number,
            'd' => $stampTime
        );
        $url = http_build_query($params);

        return $this->encrypt($url);
    }

    /**
     *
     * @param User $user
     * @param integer $stampTime
     * @return string
     */
    public function generateSignature2($user, $stampTime) {
        $params = array(
        'id' => $user->creative_user_id,
        'email' => $user->email,
        'contact_number' => $user->contact_number,
        'd' => $stampTime
        );
        $url = http_build_query($params);

        return $this->encrypt($url);
    }

}
