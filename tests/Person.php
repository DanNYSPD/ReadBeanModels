<?php

use Passion;
use Xarenisoft\ORM\Model;

class Person extends Model{
    public $id;
    public $name;
    public $age;

    public $isParent=true;
    public $wasSold=false;
    public $refence_id;
    public $table='person';

    protected $fillable=[
        'bird',
        'name',
        'age',
        'isParent'=>'is_parent',
        'wasSold',
        'refence_id'
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