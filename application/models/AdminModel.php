<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminModel extends CyModel {

    protected $tableName = "admin";

    public function mapObjToModel($obj)
    {
        if ($obj == null) return $obj;

        $model = new AdminModel();
        $model->id = (int) $obj->id;
        $model->email = $obj->email;
        $model->nama = $obj->nama;

        return parent::mapObjToModel($model);
    }
    
}