<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {
    /* @var User $user */

    private $user;

    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.
     */
    public function authenticate() {
        $user = new User();
        $user->email = $this->username;
        $user->password = $this->password;
        // @var $result User
        $result = $user->verifyUserLogin();
        if (!$result) {
//            $this->errorCode = self::ERROR_USERNAME_INVALID;
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            $this->errorCode = self::ERROR_NONE;
            $this->user = $result;
            $this->initUser();
        }
        return !$this->errorCode;
    }

    public function getId() {
        return $this->user->creative_user_id;
    }

    /**
     *
     * @param User $user
     */
    private function initUser() {
        if (!empty($this->user)) {
            $user = $this->user;

            $this->setPersistentStates(array(
                'name' => $user->first_name . ' ' . $user->last_name,
                'id' => $user->creative_user_id,
            ));

            $user->last_login_date = time();
            $user->save(false);

            return true;
        } else
            throw new CException('No User logged');
    }

}
