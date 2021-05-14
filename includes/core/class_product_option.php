<?php

class Product_Option {

    public static function options_list_full($product_id, $code) {
        $res = [];
        if ($code && Session::$mode != 2) $res[] = ['id' => 0, 'title' => 'Заводской номер', 'value' => $code, 'units_title' => '', 'created' => ''];
        return array_merge($res, self::options_list($product_id));
    }

    public static function options_list($product_id, $mode = 'default') {
        // vars
        $info = [];
        // info
        $q = DB::query("SELECT option_id, group_id, option_title, option_value, option_units_title, option_units_id, created FROM product_options WHERE product_id='".$product_id."' AND hidden<>'1';") or die (DB::error());
        while ($row = DB::fetch_row($q)) {
            $info[] = [
                'id' => $row['option_id'],
                'group_id' => $row['group_id'],
                'title' => $mode != 'copy' && Session::$mode != 2 ? flt_output($row['option_title']) : $row['option_title'],
                'value' => $mode != 'copy' && Session::$mode != 2 ? flt_output($row['option_value']) : $row['option_value'],
                'units_title' => $mode != 'copy' && Session::$mode != 2 ? flt_output($row['option_units_title']) : $row['option_units_title'],
                'units_id' => $row['option_units_id'],
                'created' => date('d.m.Y H:i', ts_timezone($row['created'], Session::$tz))
            ];
        }
        // output
        return $info;
    }

}
