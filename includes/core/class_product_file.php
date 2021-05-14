<?php

class Product_File {

    public static function files_list($product_id, $mode = 'default') {
        // vars
        $info = [];
        // info
        $q = DB::query("SELECT file_id, type, path, title, size, created FROM product_files WHERE product_id='".$product_id."' AND hidden<>'1';") or die (DB::error());
        while ($row = DB::fetch_row($q)) {
            $info[] = [
                'id' => $row['file_id'],
                'type' => $row['type'],
                'path' => $mode != 'copy' ? SITE_SCHEME.'://'.SITE_DOMAIN.$row['path'] : $row['path'],
                'title' => $mode != 'copy' ? flt_output($row['title']) : $row['title'],
                'size' => (string)round($row['size'] / 1048576, 2),
                'size_raw' => $row['size'],
                'created' => date('d.m.Y H:i', ts_timezone($row['created'], Session::$tz))
            ];
        }
        // output
        return $info;
    }

}
