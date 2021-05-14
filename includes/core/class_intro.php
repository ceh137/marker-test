<?php

class Intro {

    public static function change_page($data) {
        // vars
        $section = isset($data['section']) ? $data['section'] : 'home';
        $url = in_array($section, [
            'home', 'marker', 'scaner', 'help', 'docs', 'docs/auth/login', 'docs/auth/logout', 'docs/auth/owner',
            'docs/companies/info', 'docs/companies/list', 'docs/groups/create', 'docs/groups/edit', 'docs/groups/delete',
            'docs/groups/info', 'docs/groups/list', 'docs/products/create', 'docs/products/edit', 'docs/products/delete',
            'docs/products/list', 'docs/products/info']) ? '/'.$section : '/';
        // output
        HTML::assign('async', true);
        HTML::assign('section', $section);
        return ['html' => HTML::fetch('./partials/intro/'.$section.'.html'), 'title' => self::page_title($section), 'url' => $url];
    }

    public static function video_window($data) {
        $id = isset($data['id']) && in_array($data['id'], [1, 2]) ? $data['id'] : 1;
        return ['html' => HTML::fetch('./partials/intro/modal_video_'.$id.'.html')];
    }

    public static function page_title($section) {
        // main
        if ($section == 'marker') return 'Маркер - Маркировать продукцию просто';
        if ($section == 'scaner') return 'Сканер - Сканировать продукцию стало быстрее';
        if ($section == 'help') return 'Помощь - Остались вопросы?';
        if ($section == 'docs') return 'Документация';
        // docs (auth)
        if ($section == 'docs/auth/login') return 'Документация - Метод API - login';
        if ($section == 'docs/auth/logout') return 'Документация - Метод API - logout';
        if ($section == 'docs/auth/owner') return 'Документация - Метод API - owner';
        // docs (companies)
        if ($section == 'docs/companies/info') return 'Документация - Метод API - companies.info';
        if ($section == 'docs/companies/list') return 'Документация - Метод API - companies.list';
        // docs (groups)
        if ($section == 'docs/groups/create') return 'Документация - Метод API - groups.create';
        if ($section == 'docs/groups/edit') return 'Документация - Метод API - groups.edit';
        if ($section == 'docs/groups/delete') return 'Документация - Метод API - groups.delete';
        if ($section == 'docs/groups/info') return 'Документация - Метод API - groups.info';
        if ($section == 'docs/groups/list') return 'Документация - Метод API - groups.list';
        // docs (products)
        if ($section == 'docs/products/create') return 'Документация - Метод API - products.create';
        if ($section == 'docs/products/edit') return 'Документация - Метод API - products.edit';
        if ($section == 'docs/products/delete') return 'Документация - Метод API - products.delete';
        if ($section == 'docs/products/info') return 'Документация - Метод API - products.info';
        if ($section == 'docs/products/list') return 'Документация - Метод API - products.list';
        // default
        return 'МАСК - Платформа промышленной маркировки №1';
    }

}
