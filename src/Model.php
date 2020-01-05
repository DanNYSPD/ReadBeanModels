<?php 
namespace Xarenisoft\ORM;
/**
 * Intension de esta clase es unicamente incrementar la velocidad de consulta e insercion.
 * 
 * 
 */
class Model {
    protected $primaryKey="id";
    public $fillable=[]; #atrtibutos de la tabla , opcionalmente pueden ser clase=>'nombre_de_campo'

    protected $hidden=[];

    protected $isSerialId=true;
    protected $table;#nombre de tabla

    protected $relations=[];

    public function getFillable(){
        return $this->fillable;
    }
    public function getTableFields(){
        return array_keys($this->fillable);
    }
    public function getTableName(){
        return $this->table;
    }
    public function getMappedTableValues():array{
        $table=[];
        foreach ($this->fillable as $propertyeClass => $fieldName) {
            $table[$fieldName]=$this->{$propertyeClass};
        }
        return $table;
    }
    public function isSerialId(){
        return $this->isSerialId;
    }
    public function setPrimaryId($value){
        $this->{$this->primaryKey}=$value;
    }
    public function hasList():bool{
        foreach ($this as $property => $value) {
            if(is_object($this->{$property})){
                return true;
            }
        }
    }
    /**
     * By the default properties which name end with List ,pattern: <.*List> are tables . 
     * 
     *
     * @return void
     */
    public function getList(){
        foreach ($this as $property => $value) {
            if(is_object($this->{$property})){
                return true;
            }
        }
    }
}