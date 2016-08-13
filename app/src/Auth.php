<?php namespace src;

use \models\User;

class Auth
{
    protected $hash           = null;
    protected $authSessionKey = '';

    public function __construct($hash, $authSessionKey)
    {
        $this->hash           = $hash;
        $this->authSessionKey = $authSessionKey;
    }

    public function user()
    {
        return User::find($_SESSION[$this->authSessionKey]);
    }

    public function check()
    {
        return isset($_SESSION[$this->authSessionKey]);
    }

    public function attempt($identifier, $password)
    {
        $user = User::where(function ($query) use ($identifier) {
            return $query->where('email', $identifier)->orWhere('username', $identifier);
        })->first();

        if ($user && $this->hash->passwordCheck($password, $user->password)) {
            $_SESSION[$this->authSessionKey] = $user->id;

            return true;
        }

        return false;
    }

    public function logout()
    {
        unset($_SESSION[$this->authSessionKey]);
    }
}
