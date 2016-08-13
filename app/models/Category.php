<?php namespace models;

use \Illuminate\Database\Capsule\Manager as DB;

class Category extends Model
{
    protected $table           = 'categories';
    protected $tableClouser    = 'categories_closure';
    protected $fillable        = ['title', 'desc', 'position', 'parent_id'];
    protected $categoryClosure = null;
    public $timestamps         = false;

    public function __construct()
    {
        $this->categoryClosure = new CategoryClosure;

        parent::__construct();
    }

    public function getCategories()
    {
        $categories = DB::table($this->tableClouser . ' as cp')
            ->select(
                'cp.descendant AS id',
                'c1.parent_id',
                'c1.position',
                DB::raw('GROUP_CONCAT(c2.title ORDER BY cp.depth DESC SEPARATOR "&nbsp;&nbsp;&gt;&nbsp;&nbsp;") AS name')
            )
            ->leftJoin($this->table . ' as c1', 'cp.descendant', '=', 'c1.id')
            ->leftJoin($this->table . ' as c2', 'cp.ancestor', '=', 'c2.id')
            ->groupBy('cp.descendant')
            ->orderBy('name', 'asc')
            ->get();
        
        return $categories;
    }

    public function addCategory($data)
    {
        $data['parent_id'] = ($data['parent_id'] == 0) ? null : $data['parent_id'];
        $this->fill($data)->save();

        $ancestor = ($data['parent_id'] == null) ? $this->id : $data['parent_id'];
        $this->categoryClosure->insertNode($ancestor, $this->id);
    }

    public function edit($data)
    {
        $data['parent_id'] = ($data['parent_id'] == 0) ? null : $data['parent_id'];

        if ($this->parent_id != $data['parent_id']) {
            $this->categoryClosure->moveNodeTo($this->parent_id, $this->id, $data['parent_id']);
        }
        
        $this->fill($data)->save();
    }

    protected function _setValidationRules($v, $type = '')
    {
        $v->rule('required', ['title', 'position', 'parent_id']);
        $v->rule('integer', ['position', 'parent_id']);
        $v->rule('max', ['position'], 100);
    }
}
