<?php

class Product_Group_File {

    // ACTIONS

    public static function group_file_attached($product_id, $group_file_id) {
        $q = DB::query("SELECT product_id FROM product_files WHERE product_id='".$product_id."' AND group_file_id='".$group_file_id."' AND hidden='0' LIMIT 1;") or die (DB::error());
        return ($row = DB::fetch_row($q)) ? 1 : 0;
    }

}
