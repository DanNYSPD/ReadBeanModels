<?php

use Medoo\Medoo;
//use RedBeanPHP\R;
use Xarenisoft\ORM\Engine;
use RedBeanPHP\Facade as R;
//use \RedBeanPHP\R as R;
use PHPUnit\Framework\TestCase;
use Xarenisoft\ORM\RedBeanEngine;
use RedBeanPHP\Util\DispenseHelper;

class PersonTest extends TestCase {
    /**
     * Undocumented variable
     *
     * @var Engine
     */
    public $engine;
    public function setup():void{
        $medoo= new Medoo([
            	// required
                'database_type' => 'pgsql',
                'database_name' => 'people',
                'server' => 'localhost',
                'username' => 'postgres',
                'password' => '123456',
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //lanzar excepciones
        ]);

        $this->engine= new Engine($medoo);
        
        RedBeanEngine::setup(
            'pgsql:host=localhost;dbname=people',
            'postgres',
            '123456'
        );
        
    }

    public function testinsert(){
        $personNative= new Person();

        $personNative->age=12;
        $personNative->name="dan";
        
        $c=RedBeanEngine::dispense("child");
        $c->name="jan";
        $c->bird= new DateTime(); 
        $c2=RedBeanEngine::dispense("child");
        $c2->name="xan";
        $c2->bird= new DateTime(); 
        echo json_encode($personNative,JSON_PRETTY_PRINT);
        $personNative->bird= new DateTime();
        //debe inicial en own y terminar en List
        $personNative->ownChildList=[
           $c,$c2
        ];
        RedBeanEngine::store($personNative);
        DispenseHelper::setEnforceNamingPolicy(false);
        
    }

    public function querytest(){
        //this must return
        $person=$this->engine->find(Person::class, ['id'=>1]);
        
    }
    
}