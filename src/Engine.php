<?php 
namespace Xarenisoft\ORM;

use Medoo\Medoo;
use Xarenisoft\ORM\Model;
/**
 *  @author Daniel Hernandez <daniel.hernandez.job@gmail.com>
 *  The main goal and philosophy of this class is to be less intrusive than other libraries, 
 *  so we don't have to change the current clases that we have, 
 *  to be more flexible with the  class  property names ,table names  and table columns name(there is an intern array mapper),
 *  use not static method calls.
 *  
 * To have a decopled class manager for CRUD operations instead of one single class.
 *  And finally recollect the best useful feactures of the current libraries in this one.
 * 
 */
class Engine{
    /**
     * 
     *
     * @var Medoo
     */
    public $pdo;
    /**
     * Inicializa la clase que debe ser de tipo model
     *
     * @param string $className
     * @return Model
     */
    public function provide(string $className){
        $obj= new $className;
        return new $obj;
    }
    /**
     * Create or update.
     * Este metodo es sumamente importante, lo mas facil es ver si existe, si no existe hacer un update.!!
     *
     * @param Model $model
     * @return void
     */
    public function store(Model $model){
       #$res= $this->pdo->query("SELECT id from {$model->table} where {$model->primaryKey}={$model->{$model->primaryKey}}");
       $this->find($model);
       if($res!=null){
        $this->pdo->insert(
            $model->getTableName(),
            $this->getMappedTableValues()
        );
         if($isSerialId){
             $model->setPrimaryId($this->pdo->id());
         } 
       }else{
           $this->pdo->update(
            $model->getTableName(),
            $this->getMappedTableValues(),
             [
                $model->primaryKey=> $model->{$model->primaryKey}
             ]
           );
       }
    }

    public function findOne($classNameOrModel,array $where){
        $this->find($classNameOrModel,$where);
        //here i will do more
    }
    public function find($classNameOrModel,array $where){
        if (\is_string($classNameOrModel)) {
            $model=$this->provide($className);
        }else{
            $model=$classNameOrModel;
        }
        $result=$this->pdo->query(
            $model->getTableName(),
            $model->getTableFields(),
            $where
        );
        //I will need to map, in the very firts phase , I only will map the properties but it's necessary to map relations too.


        //here I notice if has relation ships(note, a different approach is to send a join , and do all the process here)

        //here will query that relationships.

    }

    public static function isManageble($v){
        if(\is_object($v) && $v instanceof Model){
            return true;
        }
        if(\is_array($v)){ //simple arrays are manageble
            return true;
        }
        //objects that not extend from model aren't manageble,
        return false;
    }
}