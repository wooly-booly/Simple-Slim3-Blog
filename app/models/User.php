<?php namespace models;

class User extends Model
{
    protected $table    = "users";
    protected $fillable = [
        'email', 'username', 'first_name', 'last_name', 'password',
        'recover_hash', 'remember_identifier', 'remember_token'
    ];

    public function getFullName()
    {
        if (!$this->first_name || !$this->last_name) {
            return null;
        }

        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullNameOrUsername()
    {
        return $this->getFullName() ?: $this->username;
    }

    public function updateRememberCredentials($id, $token)
    {
        $this->update([
            'remember_identifier' => $id,
            'remember_token'      => $token,
        ]);
    }

    public function removeRememberCredentials()
    {
        $this->updateRememberCredentials(null, null);
    }

    protected function _setValidationRules($v, $type = '')
    {
        switch ($type) {

            case 'add':
                $v->rule('required', ['username', 'email', 'password', 'confirm_password']);
                $v->rule('email', 'email');
                $v->rule('lengthMin', ['username', 'first_name', 'last_name'], 2);
                $v->rule('lengthMax', ['username', 'first_name', 'last_name'], 50);
                $v->rule('lengthMin', ['password'], 6);
                $v->rule('lengthMax', ['password'], 30);
                $v->rule('equals', ['confirm_password'], 'password');
                $v->rule('unique', ['username', 'email'], $this->table);
                break;

            case 'edit':
                $v->rule('required', ['email']);
                $v->rule('email', 'email');
                $v->rule('lengthMin', ['first_name', 'last_name'], 2);
                $v->rule('lengthMax', ['first_name', 'last_name'], 50);
                break;

            case 'reset-password':
                $v->rule('required', ['password', 'confirm_password']);
                $v->rule('lengthMin', ['password'], 6);
                $v->rule('lengthMax', ['password'], 30);
                $v->rule('equals', ['confirm_password'], 'password');
                break;

        }

        //     'auth' => [
        //         'identifier' => "required", // username or email
        //         'password'   => "required",
        //     ],

        //     'password-reset' => [
        //         'email' => "required|valid_email",
        //     ],
    }
}
