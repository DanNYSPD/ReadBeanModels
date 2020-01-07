<?php
namespace Xarenisoft\ORM;

use RedBeanPHP\Facade;
/**
 * This class extends R in order to allow different kind of models to use RedBean.
 */
class RedBeanEngine extends Facade{
    /**
     * Besides normal store, this method allows to receive normal objects and parse it to bean
     *
     * @param [type] $model
     * @param boolean $unfreezeIfNeeded
     * @return void
     */
    public static function store($model,$unfreezeIfNeeded = FALSE){
        if($model instanceof Model){
            $model=self::createBean($model);
            //Facade::store($bean,$unfreezeIfNeeded);
            //return;
        }
        Facade::store($model,$unfreezeIfNeeded);
    }
    private static function isList(string $property){
        if(1===\preg_match('/^own.*List$/',$property)){
            return true;
        }
        return false;
    }
    
    /**
     * Crea un bean desde el nombre de clase de un model, o desde un objeto model,
     * Esto es pensado para ser la parte final , cuando ya este listo tu objecto para guardar, actualizar. y retornar el bean correspondiente
     *
     * @param string|Model $model
     * @return void
     */
    public static function createBean($model){
        $modelObj=null;
        if(\is_string($model)){
            $model= new $model();
        }else{
            $modelObj= $model;
        }
        $bean=self::dispense($model->getTableName());
        foreach ($model->getFillable() as $key => $fieldName) {
            if(\is_int($key)){
                $bean->{$fieldName}=$model->{$fieldName};
            }else{
                $bean->{$fieldName}=$model->{$key};
            }   
        }
        //besides fillable ,the model can have ownXList so
        foreach ($model as $property => $value) {
            if(self::isList($property)){
                $bean->{$property}=$model->{$property};
            }
        }
        return $bean;
    }
}