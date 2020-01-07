<?php
namespace Xarenisoft\ORM;

use Xarenisoft\ORM\Model;

class Child extends Model{
    protected $fillable=['name','bird'];
    public $name;
    public $bird;

}