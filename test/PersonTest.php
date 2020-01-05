<?php

use Xarenisoft\ORM\Engine;

class PersonTest{
    /**
     * Undocumented variable
     *
     * @var Engine
     */
    public $engine;
    public function setup(){
        $this->engine= new Engine();
        $this->engine->store($p);
    }

    public function insertTest(){
        $p= new Person();

        $p->age=12;
        $p->name="dan";
        $engine= new Engine();
        //in this case 
        $engine->store($p);
    }

    public function query(){
        //this must return 
        $person=$this->engine->find(Person::class,['id'=>1]);

    }
}