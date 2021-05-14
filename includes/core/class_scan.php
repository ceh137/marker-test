<?php

class Scan {

    // GENERAL

    public static function scans_list($data) {
        // vars
        $info = [];
        $query = isset($data['query']) ? trim($data['query']) : '';
        $offset = isset($data['offset']) && is_numeric($data['offset']) ? $data['offset'] : 0;
        $limit = 20;
        // where
        $where = [];
        $where[] = "company_id='".Session::$company_id."'";
        if ($query) {
            $search = self::scans_search($query);
            $where_search = [];
            if ($search['product_ids']) $where_search[] = "product_id IN (".implode(",", $search['product_ids']).")";
            if ($search['user_ids']) $where_search[] = "user_id IN (".implode(",", $search['user_ids']).")";
            $where[] = $where_search ? implode(" OR ", $where_search) : "id='0'";
        }
        $where = implode(" AND ", $where);
        // query
        $q = DB::query("SELECT id, user_id, product_id, created FROM scans WHERE ".$where." ORDER BY id DESC LIMIT ".$offset.", ".$limit.";") or die (DB::error());
        while ($row = DB::fetch_row($q)) {
            $product = Product::product_info_full($row['product_id']);
            $user = User::user_info($row['user_id']);
            $info[] = [
                'id' => $row['id'],
                'product_id' => $row['product_id'],
                'product_title' => $product['title'],
                'user_name' => $user['full_name'],
                'manufacturer' => $product['manufacturer']['title'],
                'created' => date('d.m.y в H:i', ts_timezone($row['created'], Session::$tz))
            ];
        }
        // paginator
        $q = DB::query("SELECT count(*) FROM scans WHERE ".$where.";") or die (DB::error());
        $count = ($row = DB::fetch_row($q)) ? $row['count(*)'] : 0;
        $paginator = paginator($count, $offset, $limit, 'scan.paginator');
        // output
        return ['info' => $info, 'paginator' => $paginator];
    }

    public static function scans_fetch($data = []) {
        $scans = self::scans_list($data);
        HTML::assign('scans', $scans['info']);
        return ['info' => HTML::fetch('./partials/section/scans/scans_table.html'), 'paginator' => $scans['paginator']];
    }

    private static function scans_search($query) {
        // vars
        $company_ids = [];
        $product_ids = [];
        $user_ids = [];
        // companies
        $q = DB::query("SELECT company_id FROM companies WHERE title_full LIKE '%".$query."%';") or die (DB::error());
        while ($row = DB::fetch_row($q)) $company_ids[] = $row['company_id'];
        // products (where)
        $where = [];
        $where[] = "title LIKE '%".$query."%'";
        if ($company_ids) $where[] = "company_id IN (".implode(",", $company_ids).")";
        $where = implode(" OR ", $where);
        // products
        $q = DB::query("SELECT product_id FROM products WHERE ".$where.";") or die (DB::error());
        while ($row = DB::fetch_row($q)) $product_ids[] = $row['product_id'];
        // users (where)
        $where = [];
        $queries = explode(" ", $query);
        if (count($queries) == 1) $where[] = "(first_name LIKE '%".$query."%' OR last_name LIKE '%".$query."%')";
        else {
            $where_tmp = [];
            foreach ($queries as $q) $where_tmp[] = "(first_name LIKE '%".$q."%' OR last_name LIKE '%".$q."%')";
            $where[] = implode(" AND ", $where_tmp);
        }
        $where = implode(" OR ", $where);
        // users
        $q = DB::query("SELECT user_id FROM users WHERE ".$where.";") or die (DB::error());
        while ($row = DB::fetch_row($q)) $user_ids[] = $row['user_id'];
        // output
        return ['product_ids' => $product_ids, 'user_ids' => $user_ids];
    }
}
