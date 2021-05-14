<?php

class Template_Option {

    private static $option = [
        'id' => 0,
        'show_label' => 1,
        'show_list' => 1,
        'title' => '',
        'value' => ''
    ];

    // GENERAL

    public static function template_options_list($label_id, $mode) {
        // vars
        $info = [];
        // before
        if ($mode == 'export') $info[] = [
            'id' => 0,
            'show_label' => '',
            'show_list' => '',
            'title' => 'Наименование',
            'value' => ''
        ];
        // info
        $q = DB::query("SELECT id, show_label, show_list, title, title_print FROM label_options WHERE label_id='".$label_id."';") or die (DB::error());
        while ($row = DB::fetch_row($q)) {
            $info[] = [
                'id' => $row['id'],
                'show_label' => $row['show_label'],
                'show_list' => $row['show_list'],
                'title' => $row['title'],
                'value' => 'Значение'
            ];
        }
        // after
        if ($mode == 'label') {
            $count = 4 - count($info);
            if ($count > 0) for ($i = 0; $i < $count; $i++) $info[] = self::$option;
        }
        // output
        return $info;
    }

}
