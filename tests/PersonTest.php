<?php

use Medoo\Medoo;
use Xarenisoft\ORM\Engine;
use PHPUnit\Framework\TestCase;


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
        

    }

    public function testinsert(){
        $p= new Person();

        $p->age=12;
        $p->name="dan";
        
        //in this case 
        

        $this->engine->emitter->addListener('inserted',function(){
            echo "inserted";
        });
        $this->engine->emitter->addListener('updated',function(){
            echo "updated";
        });
        $this->engine->store($p);
    }

    public function querytest(){
        //this must return 
        $person=$this->engine->find(Person::class,['id'=>1]);

    }
}