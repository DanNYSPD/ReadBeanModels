<?php
use PHPUnit\Framework\TestCase;

final class NormalClasesTest extends TestCase {

    public function setup(){
        RedBeanEngine::setup(
            'pgsql:host=localhost;dbname=people',
            'postgres',
            '123456'
        );
    }
    public function test(){
        $p= new Passion();

        
    }

}