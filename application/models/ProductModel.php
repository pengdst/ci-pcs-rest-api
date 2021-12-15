<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductModel extends CyModel {

    protected $tableName = "product";

    public function mapObjToModel($obj)
    {
        if ($obj == null) return $obj;

        $model = new ProductModel();
        $model->id = (int) $obj->id;
        $model->admin_id = (int) $obj->admin_id;
        $model->nama = $obj->nama;
        $model->harga = (double) $obj->harga;
        $model->stock = (int) $obj->stock;

        return parent::mapObjToModel($model);
    }
    
}
