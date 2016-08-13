<?php namespace models;

use \Illuminate\Database\Capsule\Manager as DB;

class CategoryClosure extends Model
{
    protected $table              = 'categories_closure';
    protected $ancestorColumn     = 'ancestor';
    protected $descendantColumn   = 'descendant';
    protected $depthColumn        = 'depth';

    public function insertNode($ancestorId, $descendantId)
    {
        if (!is_numeric($ancestorId) || !is_numeric($descendantId)) {
            throw new \InvalidArgumentException('`ancestorId` and `descendantId` arguments must be of type int.');
        }

        $table      = $this->table;
        $ancestor   = $this->ancestorColumn;
        $descendant = $this->descendantColumn;
        $depth      = $this->depthColumn;
        $query      = "INSERT INTO {$table} ({$ancestor}, {$descendant}, {$depth})
            SELECT tbl.{$ancestor}, {$descendantId}, tbl.{$depth}+1
            FROM {$table} AS tbl
            WHERE tbl.{$descendant} = {$ancestorId}
            UNION ALL
            SELECT {$descendantId}, {$descendantId}, 0
        ";
            
        DB::statement($query);
    }

    public function moveNodeTo($currentAncestorId, $currentDescendantId, $newAncestorId = null)
    {
        if (!is_null($newAncestorId) && !is_numeric($newAncestorId)) {
            throw new \InvalidArgumentException('`ancestor` argument must be of type int.');
        }

        $table      = $this->table;
        $ancestor   = $this->ancestorColumn;
        $descendant = $this->descendantColumn;
        $depth      = $this->depthColumn;

        // Prevent constraint collision
        if (!is_null($newAncestorId) && $currentAncestorId === $newAncestorId) {
            return;
        }

        $this->unbindRelationships($currentDescendantId);

        // Since we have already unbound the node relationships,
        // given null ancestor id, we have nothing else to do,
        // because now the node is already root.
        if (is_null($newAncestorId)) {
            return;
        }

        $query = "INSERT INTO {$table} ({$ancestor}, {$descendant}, {$depth})
            SELECT supertbl.{$ancestor}, subtbl.{$descendant}, supertbl.{$depth}+subtbl.{$depth}+1
            FROM {$table} as supertbl
            CROSS JOIN {$table} as subtbl
            WHERE supertbl.{$descendant} = {$newAncestorId}
            AND subtbl.{$ancestor} = {$currentDescendantId}
        ";

        DB::statement($query);
    }

    protected function unbindRelationships($descendantId)
    {
        $table            = $this->table;
        $ancestorColumn   = $this->ancestorColumn;
        $descendantColumn = $this->descendantColumn;
        $descendant       = $descendantId;

        $query = "DELETE FROM {$table}
            WHERE {$descendantColumn} IN (
              SELECT d FROM (
                SELECT {$descendantColumn} as d FROM {$table}
                WHERE {$ancestorColumn} = {$descendant}
              ) as dct
            )
            AND {$ancestorColumn} IN (
              SELECT a FROM (
                SELECT {$ancestorColumn} AS a FROM {$table}
                WHERE {$descendantColumn} = {$descendant}
                AND {$ancestorColumn} <> {$descendant}
              ) as ct
            )
        ";

        DB::statement($query);
    }

    // if not use InnoDB with cascade delete, do this
    public function deletePaths($descendantId)
    {
        if (!is_numeric($descendantId)) {
            throw new \InvalidArgumentException('`descendantId` arguments must be of type int.');
        }

        $table            = $this->table;
        ;
        $ancestorColumn   = $this->ancestorColumn;
        $descendantColumn = $this->descendantColumn;
        $descendant       = $descendantId;

        $query = "DELETE FROM {$table}
            WHERE {$descendantColumn} IN (
                SELECT {$descendantColumn} FROM (
                    SELECT {$descendantColumn} FROM {$table}
                    WHERE {$ancestorColumn} = {$descendant}
                ) AS tmptable
            )
        ";
        
        DB::statement($query);
    }
}
