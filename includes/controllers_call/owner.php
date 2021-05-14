<?php

function controller_owner($sub, $act, $data) {

    if ($sub == 'common') {
        if (Session::$access) Session::access_error();
        if ($act == 'login') return Session::login($data);
    }

}
