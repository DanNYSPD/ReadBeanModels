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

        if(!$modelObj->hasTableName()){
            #echo "class:".(get_class($modelObj));
            #echo "class:".self::get_class_name(get_class($modelObj));
            $modelObj->setTableName(self::get_class_name(get_class($modelObj)));
           # echo self::decamelize($modelObj->getTableName());
            $modelObj->setTableName(self::decamelize($modelObj->getTableName()));
            #echo "\n".$modelObj->getTableName();
        }
        $bean=self::dispense($modelObj->getTableName());
        foreach ($model->getFillable() as $key => $fieldName) {
            if(\is_int($key)){
                //if key is numeric and  because redbean only allow snake case, we need to convert 'camelCase' and 'PascalCase' to snake_case
                $snakeCase=self::decamelize($fieldName);
                $bean->{$snakeCase}=$model->{$fieldName};
            }else{
                $bean->{$fieldName}=$model->{$key};
            }   
        }
        //besides fillable ,the model can have ownXList so
        foreach ($model as $property => $value) {
            if(self::isList($property)){
               // $bean->{$property}=$model->{$property};
                //now we must verify that all the elements are Beans or "Model"
                if(\is_array($model->{$property})){
                    $bean->{$property}=[];//we ensure the correct array initialization
                    //this can be heavy in performance terms but for this version we won't worry about that.
                    foreach ($model->{$property} as $element) {
                        if($element instanceof Model){
                            //here we call recursibly
                            
                            $bean->{$property}[]=self::createBean($element);
                        }else{
                            $bean->{$property}[]=$element;
                        }
                    }
                }else if(null!==$bean->{$property}){
                    throw new RuntimeException("property :$property is not an array or null");
                }
            }
        }
        return $bean;
    }
    private static function  decamelize($string) {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }
    private static function get_class_name($classname)
    {
        if ($pos = strrpos($classname, '\\')) 
        if($pos!=-1){
            return substr($classname, $pos + 1);
        }

        
        return $classname;
    }
}