<?php namespace models;

class Post extends Model
{
    protected $table    = "posts";
    protected $fillable = ['title', 'desc', 'content', 'img', 'category_id', 'state'];
    public $timestamps  = false;

    protected function _setValidationRules($v, $type = '')
    {
        $v->rule('required', ['title', 'desc', 'content', 'category_id']);
        $v->rule('integer', ['category_id']);
    }
}
