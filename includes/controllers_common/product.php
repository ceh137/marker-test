<?php

function controller_product($data, $id) {
    // validate
    if (!in_array(Session::$access, [3, 4])) access_error(Session::$mode);
    // info
    $product = Product::product_info_full($id, 'view');
    $group = Product_Group::product_group_info($product['group_id']);
    $back_url = Product::product_back_url($group['id'], $group['status']);
    $label = Template::label_info_handling($product);
    $label['preview_top'] = -$label['preview_height'] * 0.3 * 0.5;
    $label['preview_left'] = -$label['preview_width'] * 0.3 * 0.5;
    // output
    HTML::assign('product', $product);
    HTML::assign('group', $group);
    HTML::assign('label', $label);
    HTML::assign('label_preview', HTML::fetch($label['template']));
    HTML::assign('back_url', $back_url);
    return HTML::main_content('./partials/section/products/product.html', Session::$mode);
}
