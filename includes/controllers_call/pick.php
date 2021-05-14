<?php

function controller_pick($sub, $act, $data) {

    if ($sub == 'common') {
        if (!Session::$access) Session::access_error();
        if ($act == 'paginator') return Pick::picks_fetch($data);
    }

}
