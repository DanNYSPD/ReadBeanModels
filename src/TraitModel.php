<?php
namespace Xarenisoft\ReadBean\Models;

trait TraitModel {
    protected $primaryKey="id";
    protected $fillable=[]; #atrtibutos de la tabla , opcionalmente pueden ser clase=>'nombre_de_campo'

    protected $hidden=[];

    protected $_ownList=[];

    protected $isSerialId=true;
    protected $table;#nombre de tabla

    protected $relations=[];

    public function getFillable(){
        return $this->fillable;
    }
    public function getTableFields(){
        return array_values($this->fillable);
    }
    public function getTableName(){
        return $this->table;
    }
    public function setTableName(string $tableName){
        return $this->table=$tableName;
    }
    public function getMappedTableValues():array{
        $table=[];
        print_r($this->fillable);
        foreach ($this->fillable as $propertyeClass => $fieldName) {
            if(is_int($propertyeClass)){
                $table[$fieldName]=$this->{$fieldName};
            }else{
                    $table[$fieldName]=$this->{$propertyeClass};
            }   
        }
        print_r($table);
        return $table;
    }
    public function isSerialId(){
        return $this->isSerialId;
    }
    public function setPrimaryId($value){
        $this->{$this->primaryKey}=$value;
    }
    public function getPrimaryId(){
        return $this->{$this->primaryKey};
    }
    public function getPrimaryKeyName(){
        return $this->primaryKey;
    }
    public function hasList():bool{
        
        foreach ($this as $property => $value) {
            //we check if there is a property with this pattern
            if(1===\preg_match('/^own.*List$/',$property)){
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
    public function hasTableName(){
        return !empty($this->table);
    }
    /**
     * Loads this class base on an object, in this case all the public properties are "tranfered" to this object
     *
     * @param Object $object
     * @return void
     */
    public function loadFromObject($object){
        foreach ($object as $property => $value) {
            $this->{$property}=$value;
        }
    }
    public function getOwnList(){
        return $this->_ownList;
    }
    public function isInOwnList(string $propertyName){
        return isset($this->_ownList[$propertyName]);
    }
    public function getTranslatedOwnListName($propertyName){
        return $this->_ownList[$propertyName];
    }
}