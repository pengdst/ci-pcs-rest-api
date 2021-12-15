<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ItemTransaksiModel extends CyModel {

    protected $tableName = "item_transaksi";

    public function mapObjToModel($obj)
    {
        if ($obj == null) return $obj;

        $model = new ItemTransaksiModel();
        $model->id = (int) $obj->id;
        $model->transaksi_id = (int) $obj->transaksi_id;
        $model->produk_id = (int) $obj->produk_id;
        $model->qty = (int) $obj->qty;
        $model->harga_saat_transaksi = (double) $obj->harga_saat_transaksi;
        $model->sub_total = (double) $obj->sub_total;

        return parent::mapObjToModel($model);
    }
    
}
