<?php namespace models;

use \Slim\Container;
use \Illuminate\Database\Eloquent\Model as EloquentModel;
use \src\Validation;
use \src\App;

class Model extends EloquentModel
{
    protected $errors    = [];
    protected $container = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $app = App::object();
        $this->container = $app->getContainer();
    }

    public function errors()
    {
        $errors = [];

        if (!empty($this->errors)) {
            foreach ($this->errors as $field => $err) {
                $errors[$field] = $err[0];
            }
        }

        return $errors;
    }

    public function isValid($data, $type = '')
    {
        $settings = $this->container->get('settings');
        $lang     = $settings['main']['language'];
        $langDir  = $settings['language_dir'] . $lang . '/validation/';

        $v = new Validation($data, [], $lang, $langDir);

        $this->_setValidationRules($v, $type);

        if ($v->validate()) {
            return true;
        } else {
            return  $this->errors = $v->errors();
        }
    }

    // Rules for Validation should bе set in SubClasses
    protected function _setValidationRules($v, $type)
    {
        // By default, no validation rules
        return;
    }
}
