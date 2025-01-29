<?php
namespace Xarenisoft\ReadBean\Models;



use RuntimeException;
use RedBeanPHP\Facade;
use RedBeanPHP\OODBBean;
use Xarenisoft\ReadBean\Models\IModel;
/**
 * This class extends R in order to allow different kind of models to use RedBean.
 */
class RedBeanEngine extends Facade{
    /**
     * Besides normal store, this method allows to receive normal objects and parse it to bean.
     * otherwise an Catchable fata error will be thrwon:Object of class  XXX could not be converted to string.
     * 
     * 
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
        }else if(!$model instanceof OODBBean){
            //if it's not a Model instance either, we are gonna try to create the bean.
            $modelObj = new Model;
            //in this case, the received object will be tranform into a model Object
            $modelObj->loadFromObject($model);
            
            
            #echo \get_class($model);
            $model=self::createBean($modelObj);
           
        }else{ 
            /*
             foreach($model->getProperties() as $prop =>$value){
                if(is_array($value) &&isList($prop) ){
                    self::createBean();
                } 
             }*/

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
     * Recieves an string class to instanciate
     *
     * @param string $model
     * @return void
     */
    private static function initializeModel(string $model){
        $modelObj= new $model();
        return $modelObj;
    }
    private static function autoCompleteModel(IModel $modelObj){
        if(!$modelObj->hasTableName()){
            #echo "class:".(get_class($modelObj));
            #echo "class:".self::get_class_name(get_class($modelObj));
            $modelObj->setTableName(self::get_class_name(get_class($modelObj)));
           # echo self::decamelize($modelObj->getTableName());
            $modelObj->setTableName(self::decamelize($modelObj->getTableName()));
            #echo "\n".$modelObj->getTableName();
        }
        return $modelObj;
    }
    /**   
    * Crea un bean desde el nombre de clase de un model, o desde un objeto model,
    * Esto es pensado para ser la parte final , cuando ya este listo tu objecto para guardar, actualizar. y retornar el bean correspondiente
    *
    * @param string|Model $model   
    * @param boolean $ignoreNull
    * @param boolean $ignoreEmtpyString
    * @param boolean $includeDefaultPrimarykey If you already have a no bean model with primary key you can included it in the bean, so it can be updated and avoid to create a new one.
    * @return void
    */
    public static function createBean($model,bool $ignoreNull=true,bool $ignoreEmtpyString=true,$includeDefaultPrimarykey=false){
        $modelObj=null;
        if(\is_string($model)){
            $modelObj= self::initializeModel($model);
            
        }else{
            $modelObj= $model;
        }

        self::autoCompleteModel($modelObj);
        $bean=self::dispense($modelObj->getTableName());
        return self::transfer($modelObj,$bean,$ignoreNull,$ignoreEmtpyString,$includeDefaultPrimarykey);
    }
    /**
     * Pass attributes from IModelObject to a OODBBean
     *
     * @param IModel $model
     * @param OODBBean $bean
     * @param boolean $ignoreNull
     * @param boolean $ignoreEmtpyString
     * @return OODBBean
     */
    public static function transfer(IModel $model,OODBBean $bean,$ignoreNull=true,$ignoreEmtpyString=true,$includeDefaultPrimarykey=false){
        foreach ($model->getFillable() as $key => $fieldName) {
            
            if(\is_int($key)){
                //if key is numeric and  because redbean only allow snake case, we need to convert 'camelCase' and 'PascalCase' to snake_case
                $snakeCase=self::decamelize($fieldName);
                if($ignoreNull&&is_null($model->{$fieldName})){
                    continue;
                }
                if($ignoreEmtpyString&& ''===$model->{$fieldName}){
                    continue;
                }
                $bean->{$snakeCase}=$model->{$fieldName};
            }else{
                if($ignoreNull&&is_null($model->{$key})){
                    continue;
                }
                if($ignoreEmtpyString && ''===$model->{$key}){
                    continue;
                }
                $bean->{$fieldName}=$model->{$key};                
            }   
        }
        //besides fillable ,the model can have ownXList so
        foreach ($model as $property => $value) {
            if($model->isFillable($property)){
                continue;
            }
            if($model->isInOwnList($property)||self::isList($property)){#for performance  isInOwnList must be evaluated first.
               // $bean->{$property}=$model->{$property};
                //now we must verify that all the elements are Beans or "Model"
                if(\is_array($model->{$property})||\is_object($model->{$property})){
                    if($model->isInOwnList($property)){
                        #if it's in the _ownList, it means it has a explicit name
                        $propertyBeanName=$model->getTranslatedOwnListName($property);
                    }else{
                        $propertyBeanName=$property;
                    }
                    $bean->{$propertyBeanName}=[];//we ensure the correct array initialization
                    # if it's an object this means that it shuold be model as an array with one element.
                    if(\is_object($model->{$property})){

                         
                        if($model->{$property} instanceof IModel){
                            //here we call recursibly
                           
                            $bean->{$propertyBeanName}[]=self::createBean($model->{$property},$ignoreNull,$ignoreEmtpyString,$includeDefaultPrimarykey);
                           
                        }else{
                            $bean->{$propertyBeanName}[]=$model->{$property};
                        }
                    }
                    else if(is_array($model->{$property})){
                        //this can be heavy in performance terms but for this version we won't worry about that.
                        foreach ($model->{$property} as $element) {
                        
                            
                            #the firts idea was to deal only with classes of type IModel, but normal clases now will be supported, with
                            #the assumption that includes _type property and for simple cases all properties will be inserted
                            if($element instanceof IModel){
                                //here we call recursibly
                                
                                $bean->{$propertyBeanName}[]=self::createBean($element,$ignoreNull,$ignoreEmtpyString,$includeDefaultPrimarykey);
                            }else{
                                $bean->{$propertyBeanName}[]=$element;
                            }
                        }
                    }
                }else if(null!==$bean->{$property}){
                    throw new RuntimeException("property :$property is not an array or null");
                }else if(\is_scalar($model->{$property})){
                    throw new RuntimeException("property :$property is scalar, scalars cannot be tranformed into List");
                }else if(\is_object($model->{$property})){
                   #  throw new RuntimeException("property :$property is defined as Object but target is on ownList , if you want to include it , set it explicitly as list with *, ");

                }
            }
        }
        if($includeDefaultPrimarykey && property_exists($model,'id')){
            $bean->id=$model->id;
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
    /**
     * If a class is received, this will return objects of the same  class instead of an array of beans 
     *
     * @param [type] $type
     * @param [type] $sql
     * @param array $bindings
     * @param [type] $snippet
     * @return void
     */
    public static function find($type, $sql = NULL, $bindings = array(), $snippet = NULL ){
        if(class_exists($type)){
           $model= self::initializeModel($type);
           $model= self::autoCompleteModel($model);

           return self::processFind(Facade::find($model->getTableName(),$sql,$bindings,$snippet),$type);
        }
        return Facade::find($type,$sql,$bindings,$snippet);
    }
    private static function processFind($findResult,string $modelClassName){
        if(empty($findResult)){
            return $findResult;
        }
        $newModelResult=[];
        foreach ($findResult as $primaryIDValue      => $beanObject) {
            $newModelResult[$primaryIDValue]=self::fromBeanToModel($beanObject,$modelClassName);
        }
        return $newModelResult;
    }
    /**
     * Oposite  function to createBean.
     *
     * @param [type] $bean
     * @param Model $model
     * @return void
     */
    private static function fromBeanToModel($bean,string $model){
        if($bean==null) return null;
        $model= self::initializeModel($model);
        foreach ($model->getFillable() as $property=>$fieldName) {
           $model->{$property} =$bean->{$fieldName};
        }
        return $model;
    }

    public static function classNameToTableName($object):string{
       return self::decamelize(self::get_class_name(get_class($object)));
    }
}