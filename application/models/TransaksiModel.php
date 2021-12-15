<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransaksiModel extends CyModel {

    protected $tableName = "transaksi";

    public function mapObjToModel($obj)
    {
        if ($obj == null) return $obj;

        $model = new TransaksiModel();
        $model->id = (int) $obj->id;
        $model->admin_id = (int) $obj->admin_id;
        $model->tanggal = $obj->tanggal;
        $model->date_in_milis = strtotime($obj->tanggal);
        $model->total = (int) $obj->total;

        return parent::mapObjToModel($model);
    }
    
}
