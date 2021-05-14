<?php

class Company {

    public static function company_info($data) {
        // vars
        $id = isset($data['id']) && is_numeric($data['id']) ? $data['id'] : 0;
        $title = isset($data['title']) && trim($data['title']) ? trim($data['title']) : '';
        // where
        $where = "";
        if (isset($data['id'])) $where = "company_id='".$id."'";
        if (isset($data['title'])) $where = "title_full LIKE '".$title."'";
        // info
        $q = DB::query("SELECT company_id, type, title_full, address, address_lat, address_lng, inn, kpp, ogrn, bank_title, bank_code, bank_cs, bank_rs, phone, email, balance, print_id, pick_email, pick_server, show_home, show_passports, show_products, show_tasks, size_used, size_total, types_codes, types_products, created FROM companies WHERE ".$where." LIMIT 1;") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            return [
                'id' => $row['company_id'],
                'type' => $row['type'],
                'title' => Session::$mode != 2 ? flt_output($row['title_full']) : $row['title_full'],
                'address' => Session::$mode != 2 ? flt_output($row['address']) : $row['address'],
                'address_lat' => (string)$row['address_lat'], 'address_lng' => (string)$row['address_lng'],
                'inn' => $row['inn'] ? $row['inn'] : '',
                'kpp' => $row['kpp'] ? $row['kpp'] : '',
                'ogrn' => $row['ogrn'] ? $row['ogrn'] : '',
                'bank_title' => $row['bank_title'],
                'bank_code' => $row['bank_code'],
                'bank_cs' => $row['bank_cs'],
                'bank_rs' => $row['bank_rs'],
                'phone' => $row['phone'] ? phone_formatting($row['phone']) : '',
                'email' => $row['email'],
                'balance' => number_format($row['balance'] * 0.01, 0, ',', ' '),
                'print_id' => $row['print_id'], 'pick_email' => $row['pick_email'],
                'pick_server' => $row['pick_server'],
                'show_home' => $row['show_home'],
                'show_passports' => $row['show_passports'],
                'show_products' => $row['show_products'],
                'show_tasks' => $row['show_tasks'],
                'size_free' => round(($row['size_total'] - $row['size_used']) / 1073741824, 2),
                'size_used' => round($row['size_used'] / 1073741824, 2),
                'size_total' => round($row['size_total'] / 1073741824, 2),
                'types_codes' => $row['types_codes'] ? json_decode($row['types_codes'], true) : ['ean13' => 1, 'qr' => 1, 'datamatrix' => 1, 'pdf417' => 1],
                'types_products' => $row['types_products'] ? json_decode($row['types_products'], true) : ['single' => 1, 'primary' => 1, 'groups' => 1, 'transport' => 1],
                'created' => date('d.m.Y', ts_timezone($row['created'], Session::$tz))
            ];
        } else {
            return [
                'id' => 0,
                'code' => '',
                'type' => 0,
                'title' => '',
                'address' => '',
                'address_lat' => '0.00000000',
                'address_lng' => '0.00000000',
                'inn' => '',
                'kpp' => '',
                'ogrn' => '',
                'bank_title' => '',
                'bank_code' => '',
                'bank_cs' => '',
                'bank_rs' => '',
                'phone' => '',
                'email' => '',
                'balance' => 0,
                'print_id' => 0,
                'pick_email' => '',
                'pick_server' => '',
                'show_home' => 0,
                'show_passports' => 0,
                'show_products' => 0,
                'show_tasks' => 0,
                'size_free' => 0,
                'size_used' => 0,
                'size_total' => 0,
                'types_codes' => ['ean13' => 1, 'qr' => 1, 'datamatrix' => 1, 'pdf417' => 1],
                'types_products' => ['single' => 1, 'primary' => 1, 'groups' => 1, 'transport' => 1],
                'created' => ''
            ];
        }
    }

}