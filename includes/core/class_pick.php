<?php

class Pick {

    public static function picks_list($data) {
        // vars
        $info = [];
        $offset = isset($data['offset']) && is_numeric($data['offset']) ? $data['offset'] : 0;
        $limit = 20;
        // where
        $where = [];
        $where[] = "company_id='".Session::$company_id."'";
        $where = implode(' AND ', $where);
        // info
        $q = DB::query("SELECT pick_id, user_id, status, count_products, created FROM picks WHERE ".$where." ORDER BY pick_id DESC LIMIT ".$offset.", ".$limit.";") or die (DB::error());
        while ($row = DB::fetch_row($q)) {
            $products = Pick_Product::pick_products_list($row['pick_id']);
            $info[] = [
                'id' => $row['pick_id'],
                'user_id' => $row['user_id'],
                'status' => $row['status'],
                'products' => $products['items'],
                'count_products' => $row['count_products'],
                'manufacturers' => $products['manufacturers'],
                'created' => date('d.m.y в H:i', ts_timezone($row['created'], Session::$tz))
            ];
        }
        // output
        return ['info' => $info, 'paginator' => ''];
    }

}
