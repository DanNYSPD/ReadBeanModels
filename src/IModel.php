<?php
namespace Xarenisoft\ReadBean\Models;

interface IModel {
     
    public function getFillable();

    public function getTableFields();
    public function getTableName();
    public function setTableName(string $tableName);
    public function getMappedTableValues():array;

    public function isSerialId();
    public function setPrimaryId($value);
    public function getPrimaryId();
    public function getPrimaryKeyName();
    public function hasList():bool;
    public function getList();
    public function hasTableName();
    public function loadFromObject($object);
    public function isFillable(string $propertyName):bool;
}