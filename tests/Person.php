<?php

use Passion;
use Xarenisoft\ORM\Model;

class Person extends Model{
    public $id;
    public $name;
    public $age;

    public $isParent=true;
    public $table='person';
    protected $fillable=[
        'name',
        'age',
        'isParent'=>'is_parent'
    ];

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