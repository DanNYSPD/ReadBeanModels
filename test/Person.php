<?php

use Passion;
use Xarenisoft\ORM\Model;

class Person extends Model{
    public $name;
    public $age;

    public $isParent=true;

    protected $relations=[
        'passionsList'=>'.*' #one to many
    ];
    /**
     * PassionList must be an array of Models
     *
     * @var Passion[]
     */
    public $passionsList;
}