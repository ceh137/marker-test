<?php

class Template {

    // GENERAL

    public static function template_info($label_id) {
        $q = DB::query("SELECT label_id, type_id, company_title, title, size_width, size_height, size_code, size_row FROM labels WHERE label_id='".$label_id."' AND company_id='".Session::$company_id."' AND hidden<>'1';") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            $type = self::label_type($row['type_id']);
            return [
                'id' => $row['label_id'],
                'type_id' => $type['id'],
                'type_title' => $type['title'],
                'company_title' => unflt_output($row['company_title']),
                'product_title' => 'Наименование изделия',
                'title' => flt_output($row['title']),
                'size_width' => $row['size_width'],
                'size_height' => $row['size_height'],
                'size_code' => $row['size_code'],
                'size_row' => $row['size_row'],
                'preview_width' => $row['size_width'] * 10,
                'preview_height' => $row['size_height'] * 10,
                'preview_top' => -$row['size_height'] * 0.5 * 0.5 * 10,
                'preview_left' => -$row['size_width'] * 0.5 * 0.5 * 10,
                'preview_code' => $row['size_code'] * 10,
                'preview_row' => $row['size_row'] * 10,
                'barcode_id' => '0000000000000',
                'qr' => '/images/qr.png',
                'ean13' => '/images/ean13.png',
                'datamatrix' => '/images/datamatrix.png',
                'pdf417' => '/images/pdf417.png',
                'options' => Template_Option::template_options_list($row['label_id'], 'label')
            ];
        } else {
            $type = self::label_type(0);
            $company = Company::company_info(['id' => Session::$company_id]);
            return [
                'id' => 0,
                'type_id' => $type['id'],
                'type_title' => $type['title'],
                'company_title' => $company['title'].' тел. '.$company['phone'],
                'product_title' => 'Наименование изделия',
                'title' => 'Новый шаблон',
                'size_width' => $type['w'],
                'size_height' => $type['h'],
                'size_code' => 22,
                'size_row' => 6.4,
                'preview_width' => $type['w'] * 10,
                'preview_height' => $type['h'] * 10,
                'preview_top' => -$type['h'] * 0.5 * 0.5 * 10,
                'preview_left' => -$type['w'] * 0.5 * 0.5 * 10,
                'preview_code' => 22 * 10,
                'preview_row' => 6.4 * 10,
                'barcode_id' => '0000000000000',
                'qr' => '/images/qr.png',
                'ean13' => '/images/ean13.png',
                'datamatrix' => '/images/datamatrix.png',
                'pdf417' => '/images/pdf417.png',
                'options' => [
                    ['id' => 0, 'show_label' => 1, 'show_list' => 1, 'title' => 'Заводской номер', 'value' => 'Значение'],
                    ['id' => 0, 'show_label' => 1, 'show_list' => 1, 'title' => 'Дата изготовления', 'value' => 'Значение'],
                    ['id' => 0, 'show_label' => 1, 'show_list' => 1, 'title' => 'Дата отгрузки', 'value' => 'Значение'],
                    ['id' => 0, 'show_label' => 1, 'show_list' => 1, 'title' => '', 'value' => 'Значение']
                ]
            ];
        }
    }

    // SERVICE

    public static function label_type($id = -1) {
        $result = [];
        if ($id == 0 || $id == -1) $result[] = ['id' => 0, 'title' => 'Универсальная этикетка', 'w' => 80, 'h' => 38];
        if ($id == 2 || $id == -1) $result[] = ['id' => 2, 'title' => 'Трубная продукция (2 покрытия)', 'w' => 80, 'h' => 38];
        if ($id == 3 || $id == -1) $result[] = ['id' => 3, 'title' => 'Трубная продукция (1 покрытие)', 'w' => 80, 'h' => 38];
        if ($id == 4 || $id == -1) $result[] = ['id' => 4, 'title' => 'QR-код', 'w' => 80, 'h' => 38];
        if ($id == 5 || $id == -1) $result[] = ['id' => 5, 'title' => 'Штрих-код', 'w' => 80, 'h' => 38];
        if ($id == 6 || $id == -1) $result[] = ['id' => 6, 'title' => 'Data Matrix', 'w' => 80, 'h' => 38];
        if ($id == 7 || $id == -1) $result[] = ['id' => 7, 'title' => 'PDF417', 'w' => 80, 'h' => 38];
        if ($id == -1) return $result;
        else return isset($result[0]) ? $result[0] : ['id' => 0, 'title' => '', 'w' => 80, 'h' => 38];
    }

    public static function label_info_handling($product) {
        // label
        $label_id = $product['label_id'];
        if (!$label_id && $product['group_id']) {
            $group = Product_Group::product_group_info($product['group_id']);
            $label_id = $group['label_id'];
        }
        // info
        $label = self::template_info($label_id);
        $product['options'] = Product_Option::options_list($product['id']);
        // vars
        $result['product_title'] = mb_strlen($product['title'], 'UTF-8') > 90 ? mb_substr($product['title'], 0, 88, 'UTF-8').'...' : $product['title'];
        $result['company_title'] = $label['company_title'];
        $result['preview_width'] = $label['preview_width'];
        $result['preview_height'] = $label['preview_height'];
        $result['preview_code'] = $label['preview_code'];
        $result['preview_row'] = $label['preview_row'];
        $result['barcode_id'] = $product['barcode_id'];
        $result['qr'] = $product['qr'];
        $result['ean13'] = $product['ean13'];
        $result['datamatrix'] = $product['datamatrix'];
        $result['pdf417'] = $product['pdf417'];
        $result['template'] = './partials/print/label_'.$label['type_id'].'.html';
        // vars (options)
        $options = [];
        foreach ($label['options'] as $a) {
            $found = false;
            foreach ($product['options'] as $b) {
                if (preg_match('~^'.$a['title'].'$~iu', $b['title']) && $a['show_label']) {
                    $options[] = ['id' => 0, 'show_label' => 1, 'title' => $a['title'], 'value' => $b['value']];
                    $found = true;
                }
            }
            if (!$found) {
                if (preg_match('~^'.$a['title'].'$~iu', 'заводской номер')) {
                    $options[] = ['id' => 0, 'show_label' => 1, 'title' => 'Заводской номер', 'value' => $product['code'] ? $product['code'] : '-'];
                    $found = true;
                }
                if (preg_match('~^'.$a['title'].'$~iu', 'дата изготовления')) {
                    $options[] = ['id' => 0, 'show_label' => 1, 'title' => 'Дата изготовления', 'value' => $product['produced'] ? $product['produced'] : '-'];
                    $found = true;
                }
                if (preg_match('~^'.$a['title'].'$~iu', 'дата отгрузки')) {
                    $options[] = ['id' => 0, 'show_label' => 1, 'title' => 'Дата отгрузки', 'value' => $product['shipped'] ? $product['shipped'] : '-'];
                    $found = true;
                }
            }
            if (!$found) $options[] = ['id' => 0, 'show_label' => 1, 'title' => $a['title'], 'value' => '-'];
        }
        if (!$options) $options = [
            ['id' => 0, 'title' => 'Заводской номер', 'value' => $product['code'] ? $product['code'] : '-'],
            ['id' => 0, 'title' => 'Дата изготовления', 'value' => $product['produced'] ? $product['produced'] : '-'],
            ['id' => 0, 'title' => 'Дата отгрузки', 'value' => $product['shipped'] ? $product['shipped'] : '-']
        ];
        $result['options'] = $options;
        // output
        return $result;
    }

}
