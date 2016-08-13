<?php namespace models;

class Setting extends Model
{
    protected $table    = "settings";
    protected $fillable = ['title', 'template', 'language', 'post_per_page'];

    protected function _setValidationRules($v, $type = '')
    {
        $v->rule('required', ['title', 'template', 'language', 'post_per_page']);
        $v->rule('integer', ['post_per_page']);
        $v->rule('max', ['post_per_page'], 50);
    }
}
