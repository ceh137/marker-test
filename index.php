<?php

// INIT

require('./cfg/general.inc.php');
require('./includes/core/functions_general.php');
require('./includes/core/functions_security.php');
require('./vendor/autoload.php');

class_autoload();
controllers_common();
DB::connect();

// VARS

Session::init();
$g['user_id'] = Session::$user_id;
$g['company_id'] = Session::$company_id;
$g['access'] = Session::$access;
$g['menu'] = Session::$menu;
$g['tz'] = Session::$tz;
$g['sid'] = Session::$sid;
$g['url'] = flt_input($_SERVER['REQUEST_URI']);
HTML::assign('search', '');

Route::init();
$g['path'] = Route::$path;
$g['query'] = Route::$query;
HTML::assign('title', DEFAULT_TITLE);

$owner = User::user_info(Session::$user_id);

// OUTPUT
HTML::assign('owner', $owner);
HTML::assign('global', $g);
HTML::display('./partials/index.html');
