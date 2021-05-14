<?php

class Product_Group {

    public static function product_group_info($group_id) {
        // info
        $q = DB::query("SELECT group_id, local_id, customer_id, contract_id, label_id, status, title, quantity, created FROM product_groups WHERE group_id='".$group_id."' AND company_id='".Session::$company_id."' LIMIT 1;") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            $created = $row['created'] ? date('d.m.Y', ts_timezone($row['created'], Session::$tz)) : '';
            $label = Template::template_info($row['label_id']);
            return [
                'id' => $row['group_id'],
                'local_id' => $row['local_id'],
                'label_id' => $row['label_id'],
                'label_title' => !$row['label_id'] ? 'Стандартная' : $label['title'],
                'status' => $row['status'],
                'title' => $row['title'] ? $row['title'] : 'Партия #'.$row['local_id'].' от '.$created,
                'customer' => Company::company_info(['id' => $row['customer_id']]),
                'contract' => Contract::contract_info($row['contract_id']),
                'quantity' => $row['quantity'],
                'qr' => create_qr($row['group_id'], 'group'),
                'ean13' => create_ean13($row['group_id'], 'group'),
                'datamatrix' => create_datamatrix($row['group_id'], 'group'),
                'pdf417' => create_pdf417($row['group_id'], 'group'),
                'created' => $row['created'] ? date('d.m.Y', ts_timezone($row['created'], Session::$tz)) : ''
            ];
        } else {
            return [
                'id' => 0,
                'local_id' => 0,
                'label_id' => 0,
                'label_title' => 'Стандартная',
                'status' => 0,
                'title' => '',
                'customer' => ['id' => 0, 'title' => ''],
                'contract' => ['id' => 0, 'title' => '', 'annex_number' => 0],
                'quantity' => 0,
                'qr' => '',
                'ean13' => '',
                'datamatrix' => '',
                'pdf417' => '',
                'created' => ''
            ];
        }
    }

    public static function product_group_ids($data) {
        // vars
        $ids = [];
        $group_id = isset($data['group_id']) ? $data['group_id'] : 0;
        $where = isset($data['where']) ? $data['where'] : "company_id='".Session::$company_id."' AND group_id='".$group_id."' AND hidden='0'";
        $user = User::user_info(Session::$user_id);
        $sort = $user['sort_products'] == 'asc' ? "ASC" : "DESC";
        // query
        $q = DB::query("SELECT product_id FROM products WHERE ".$where." ORDER BY product_id ".$sort.";") or die (DB::error());
        while ($row = DB::fetch_row($q)) $ids[] = $row['product_id'];
        // output
        return $ids;
    }

}
