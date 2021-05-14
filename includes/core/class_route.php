<?php

class Route {

    // VARS

    public static $path = '';
    public static $query = [];

    // GENERAL

    public static function init() {
        // vars
        $url = $_SERVER['REQUEST_URI'];
        if (substr($url, 0, 1) == '/') $url = substr($url, 1);
        $url = explode('?', $url);
        // path
        self::$path = isset($url[0]) && $url[0] ? flt_input($url[0]) : 'home';
        // queries
        isset($url[1]) ? parse_str($url[1], $queries) : $queries = [];
        foreach ($queries as $key => $value) self::$query[flt_input($key)] = flt_input($value);
        // seo
        return self::route_common();
    }

    private static function route_common() {
        // vars
        $company = Company::company_info(['id' => Session::$company_id]);
        // account
        if (Session::$access) {
            self::route_home($company);
            HTML::assign('section_content', './partials/home.html');
            if (self::$path == 'logout') Session::logout();
            else if (self::$path == 'picks') return controller_picks(self::$query);
            else if (self::$path == 'scans') return controller_scans(self::$query);
            else if (strpos(self::$path, 'products') !== false) return self::products();
            else return self::owners();
        }
        // intro
        else {
            if (self::$path == 'login') {
                HTML::assign('section_content', './partials/section/intro/login.html');
            } else if (in_array(self::$path, ['home', 'marker', 'scaner', 'help'])) {
                controller_intro(self::$path);
            } else {
                HTML::assign('section_content', './partials/section/intro/login.html');
            }
        }
        return '';
    }

    public static function route_call($dpt, $sub, $act, $data) {
        // routes
        if ($dpt == 'common') $result = controller_common($sub, $act, $data);
        else if ($dpt == 'owner') $result = controller_owner($sub, $act, $data);
        else if ($dpt == 'pick') $result = controller_pick($sub, $act, $data);
        else if ($dpt == 'product') $result = controller_product($sub, $act, $data);
        else if ($dpt == 'scan') $result = controller_scan($sub, $act, $data);
        else $result = [];
        // output
        echo json_encode($result, true);
        exit();
    }

    // ROUTES

    private static function products() {
        // details
        preg_match('~^products/([\d]+)$~i', self::$path, $m);
        $id = isset($m[1]) ? $m[1] : 0;
        self::$path = 'product';
        return controller_product(self::$query, $id);
    }


    private static function owners() {
        error_404();
        return '';
    }

    // SERVICE

    private static function route_home($company) {
        if (self::$path != 'home') return false;
        if (Session::$access == 4) self::$path = 'picks';
    }

}
