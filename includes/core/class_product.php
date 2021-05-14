<?php

class Product {

    // GENERAL

    public static function product_info($product_id) {
        // info
        $q = DB::query("SELECT product_id, company_id, title, hidden FROM products WHERE product_id='".$product_id."' LIMIT 1;") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            return [
                'id' => $row['product_id'],
                'company_id' => $row['company_id'],
                'title' => Session::$mode != 2 ? flt_output($row['title']) : $row['title'],
                'hidden' => $row['hidden']
            ];
        } else {
            return [
                'id' => 0,
                'company_id' => 0,
                'title' => '',
                'hidden' => 0
            ];
        }
    }

    public static function product_info_full($product_id, $mode = 'default') {
        // info
        $q = DB::query("SELECT product_id, company_id, user_id, customer_id, consignee_id, contract_id, group_id, label_id, status, product_status, code, title, destination, quantity, hidden, produced, marked, shipped, created FROM products WHERE product_id='".$product_id."' LIMIT 1;") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            // vars
            $qr = '';
            $ean13 = '';
            $datamatrix = '';
            $pdf417 = '';
            $barcode_id = '';
            // info
            $manufacturer = Company::company_info(['id' => $row['company_id']]);
            $label = Template::template_info($row['label_id']);
            $code = Session::$mode != 2 ? flt_output($row['code']) : $row['code'];
            if (!$code && Session::$mode == 2) $code = 'не указан';
            // barcode
            if ($label['type_id'] == 5) {
                $barcode = create_ean13($row['product_id'], 'product');
                $ean13 = $barcode['path'];
                $barcode_id = $barcode['id'];
            } else if ($label['type_id'] == 6) {
                $barcode = create_datamatrix($row['product_id'], 'product');
                $datamatrix = $barcode['path'];
                $barcode_id = $barcode['id'];
            } else if ($label['type_id'] == 7) {
                $barcode = create_pdf417($row['product_id'], 'product');
                $pdf417 = $barcode['path'];
                $barcode_id = $barcode['id'];
            } else {
                $barcode = create_qr($row['product_id'], 'product');
                $qr = $barcode['path'];
                $barcode_id = $barcode['id'];
            }
            // output
            return [
                'id' => $row['product_id'],
                'user_id' => $row['user_id'],
                'group_id' => $row['group_id'],
                'label_id' => $row['label_id'],
                'label_title' => !$row['label_id'] ? 'Стандартная' : $label['title'],
                'code' => $code,
                'title' => Session::$mode != 2 && $mode != 'copy' ? flt_output($row['title']) : $row['title'],
                'destination' => Session::$mode != 2 && $mode != 'copy' ? flt_output($row['destination']) : $row['destination'],
                'quantity' => $row['quantity'],
                'barcode_id' => $barcode_id,
                'qr' => SITE_SCHEME.'://'.SITE_DOMAIN.$qr,
                'ean13' => SITE_SCHEME.'://'.SITE_DOMAIN.$ean13,
                'datamatrix' => SITE_SCHEME.'://'.SITE_DOMAIN.$datamatrix,
                'pdf417' => SITE_SCHEME.'://'.SITE_DOMAIN.$pdf417,
                'url' => SITE_SCHEME.'://'.SITE_DOMAIN.'/products/'.$row['product_id'],
                'manufacturer' => $manufacturer,
                'customer' => Company::company_info(['id' => $row['customer_id']]),
                'consignee' => Company::company_info(['id' => $row['consignee_id']]),
                'contract' => Contract::contract_info($row['contract_id']),
                'options' => Product_Option::options_list_full($row['product_id'], $row['code']),
                'files' => Product_File::files_list($row['product_id']),
                'draft' => 0,
                'status' => $row['status'],
                'product_status' => $row['product_status'],
                'produced' => date_str($row['produced'], $mode),
                'produced_ts' => $row['produced'],
                'marked' => $row['marked'] ? date('d.m.Y H:i', ts_timezone($row['marked'], Session::$tz)) : '',
                'marked_ts' => $row['marked'],
                'shipped' => date_str($row['shipped'], $mode),
                'shipped_ts' => $row['shipped'],
                'created' => $row['created'] ? date('d.m.Y H:i', ts_timezone($row['created'], Session::$tz)) : '',
                'created_ts' => $row['created'],
                'company_title' => $manufacturer['title'],
                'company_address' => $manufacturer['address']
            ];
        } else {
            return [
                'id' => 0,
                'group_id' => 0,
                'label_id' => 0,
                'label_title' => 'Стандартная',
                'code' => '',
                'title' => '',
                'destination' => '',
                'quantity' => 1,
                'barcode_id' => '0000000000000',
                'qr' => '',
                'ean13' => '',
                'datamatrix' => '',
                'pdf417' => '',
                'url' => '',
                'manufacturer' => ['id' => 0, 'title' => ''],
                'customer' => ['id' => 0, 'title' => ''],
                'consignee' => ['id' => 0, 'title' => ''],
                'contract' => ['id' => 0, 'title' => ''],
                'options' => [],
                'files' => [],
                'draft' => 0,
                'status' => 0,
                'product_status' => 0,
                'produced' => '',
                'produced_ts' => '',
                'marked' => '',
                'marked_ts' => '',
                'shipped' => '',
                'shipped_ts' => '',
                'created' => '',
                'created_ts' => '',
                'company_title' => '',
                'company_address' => ''
            ];
        }
    }


    public static function products_list($data = []) {
        // vars
        $info = [];
        $mode = isset($data['mode']) ? $data['mode'] : 'default';
        $show = isset($data['show']) ? $data['show'] : 'all';
        $query = isset($data['query']) && trim($data['query']) ? trim($data['query']) : '';
        $offset = isset($data['offset']) && is_numeric($data['offset']) ? $data['offset'] : 0;
        $group_id = isset($data['group_id']) && is_numeric($data['group_id']) ? $data['group_id'] : -1;
        $group_file_id = isset($data['group_file_id']) && is_numeric($data['group_file_id']) ? $data['group_file_id'] : 0;
        $group_status = isset($data['group_status']) && in_array($data['group_status'], ['active', 'archive']) ? $data['group_status'] : 'active';
        // limit
        if ($mode == 'search') $limit = 10;
        else if ($mode == 'group_files') $limit = 10;
        else $limit = isset($data['limit']) && is_numeric($data['limit']) ? $data['limit'] : 10;
        // info
        $company = Company::company_info(['id' => Session::$company_id]);
        $user = User::user_info(Session::$user_id);
        $print_id = $company['print_id'];
        // where
        $where = [];
        if (Session::$access != 1) $where[] = "company_id='".Session::$company_id."'";
        if ($group_status == 'active') $where[] = "product_status='0'";
        if ($group_status == 'archive') $where[] = "product_status='1'";
        if ($group_id != -1) $where[] = "group_id='".$group_id."'";
        if ($query) {
            if ($group_id >= 0 && $mode == 'group_files') {
                $product_ids = [];
                $q = DB::query("SELECT product_id, option_title, option_value FROM product_options WHERE group_id='".$group_id."' AND option_value LIKE '%".$query."%';") or die (DB::error());
                while ($row = DB::fetch_row($q)) $product_ids[] = "'".$row['product_id']."'";
                if ($product_ids) $where[] = "(title LIKE '%".$query."%' OR product_id IN (".implode(",", $product_ids)."))";
                else $where[] = "title LIKE '%".$query."%'";
            } else {
                $where[] = "title LIKE '%".$query."%'";
            }
        }
        $where[] = "hidden='0'";
        $where = implode(' AND ', $where);
        // sort
        $sort = $user['sort_products'] == 'asc' ? "ASC" : "DESC";
        // info
        $q = DB::query("SELECT product_id, user_id, customer_id, consignee_id, contract_id, group_id, label_id, status, product_status, code, title, destination, quantity, produced, marked, shipped, created FROM products WHERE ".$where." ORDER BY product_id ".$sort." LIMIT ".$offset.", ".$limit.";") or die (DB::error());
        while ($row = DB::fetch_row($q)) {
            // vars
            $label = Template::template_info($row['label_id']);
            // info
            $info[] = [
                'id' => $row['product_id'],
                'user_id' => $row['user_id'],
                'group_id' => $row['group_id'],
                'group_status' => $group_status,
                'print_id' => $print_id,
                'label_title' => !$row['label_id'] ? 'Стандартная' : $label['title'],
                'status' => $row['status'],
                'product_status' => $row['product_status'],
                'code' => Session::$mode != 2 ? flt_output($row['code']) : $row['code'],
                'title' => Session::$mode != 2 ? flt_output($row['title']) : $row['title'],
                'destination' => Session::$mode != 2 ? flt_output($row['destination']) : $row['destination'],
                'customer' => Company::company_info(['id' => $row['customer_id']]),
                'consignee' => Company::company_info(['id' => $row['consignee_id']]),
                'contract' => Contract::contract_info($row['contract_id']),
                'options' => Product_Option::options_list($row['product_id']),
                'custom' => [],
                'quantity' => $row['quantity'],
                'produced' => $row['produced'] ? date('d.m.Y', ts_timezone($row['produced'], Session::$tz)) : '',
                'marked' => $row['marked'] ? date('d.m.Y', ts_timezone($row['marked'], Session::$tz)) : '',
                'shipped' => $row['shipped'] ? date('d.m.Y', ts_timezone($row['shipped'], Session::$tz)) : '',
                'created' => $row['created'] ? date('d.m.Y H:i', ts_timezone($row['created'], Session::$tz)) : '',
                'group_file_attached' => ($mode == 'group_files' && $group_file_id) ? Product_Group_File::group_file_attached($row['product_id'], $group_file_id) : 0
            ];
        }
        // paginator
        $q = DB::query("SELECT count(*) FROM products WHERE ".$where.";") or die (DB::error());
        $count = ($row = DB::fetch_row($q)) ? $row['count(*)'] : 0;
        $callback = $mode == 'group_files' ? 'product.group_files_product_paginator' : $mode == 'search' ? 'product.search_paginator' : 'product.product_paginator';
        $paginator = paginator($count, $offset, $limit, $callback, ['group_id' => $group_id, 'query' => $query, 'mode' => 'page', 'group_status' => $group_status]);
        // label
        $label = name_case($count, ['изделие', 'изделия', 'изделий']);
        // output
        return ['info' => $info, 'paginator' => $paginator, 'count' => $count, 'label' => $label, 'where' => $where];
    }

    // SERVICE

    public static function product_back_url($group_id, $group_status) {
        // vars
        $url = '/products';
        // queries
        $q = [];
        if ($group_status == 1) $q[] = 'sub=archive';
        if ($group_id) $q[] = 'group_id='.$group_id;
        if ($q) $url .= '?'.implode('&', $q);
        // output
        return $url;
    }

}
