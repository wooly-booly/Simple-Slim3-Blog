<?php namespace src;

use Valitron\Validator;
use \Illuminate\Database\Capsule\Manager as DB;

class Validation extends Validator
{
    public function __construct($data, $fields = array(), $lang = null, $langDir = null)
    {
        parent::__construct($data, $fields, $lang, $langDir);
    }

    protected function validateUnique($field, $value, $params)
    {
        $row = DB::table($params[0])->where($field, $value)->first();

        if ($row) {
            return false;
        }

        return true;
    }
}
