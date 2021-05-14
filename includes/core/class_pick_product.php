<?php

class Pick_Product {

    // GENERAL

    public static function pick_products_list($pick_id) {
        // vars
        $items = [];
        $manufacturers = [];
        // query
        $q = DB::query("SELECT pick_id, product_id, quantity, type, created FROM pick_products WHERE pick_id='".$pick_id."';") or die (DB::error());
        while ($row = DB::fetch_row($q)) {
            $product = Product::product_info($row['product_id']);
            $manufacturer = Company::company_info(['id' => $product['company_id']]);
            if (!in_array($manufacturer['title'], $manufacturers)) $manufacturers[] = $manufacturer['title'];
            $items[] = [
                'id' => $row['product_id'],
                'title' => $product['title'],
                'quantity' => $row['quantity'],
                'type' => self::pick_types($row['type']),
                'manufacturer' => $manufacturer['title'],
                'manufacturer_id' => $manufacturer['id'],
                'created' => date('d.m.y в H:i', ts_timezone($row['created'], Session::$tz))
            ];
        }
        // output
        return ['items' => $items, 'manufacturers' => implode(', ', $manufacturers)];
    }

    private static function pick_types($id) {
        if ($id == 1) return 'шт.';
        if ($id == 2) return 'п.м.';
        if ($id == 3) return 'кг';
        if ($id == 4) return 'м3';
        return '';
    }

}
