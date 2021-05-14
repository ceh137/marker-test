<?php

function controller_common($sub, $act, $data) {

    if ($sub == 'intro') {
        if ($act == 'change_page') return Intro::change_page($data);
        if ($act == 'video_window') return Intro::video_window($data);
    }

}
