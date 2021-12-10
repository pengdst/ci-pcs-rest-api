<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransaksiModel extends CyModel {

    protected $tableName = "transaksi";
    
    public function all()
    {
        $results = parent::all();
        foreach($results as $key => $transaction){
            // $transactions[$key]['answers'] = $answers_model->get_answers_by_transaction_id($transaction['transaction_id']);
            $transaction->date_in_milis = strtotime($transaction->tanggal);
            $results[$key] = $transaction;
        }
        return $results;
    }
    
}
