<?php

class User {

    public static function user_info($user_id) {
        // info
        $q = DB::query("SELECT user_id, access, company_id, company_title, first_name, last_name, middle_name, occupation, note, email, phone, password, password_salt, sort_products, sort_passports, sort_users, sort_scans, sort_picks, sort_breachs, filter_scans_company, filter_picks_company, scans_from_date, scans_to_date, picks_from_date, picks_to_date, breachs_from_date, breachs_to_date, blocked, count_notifications FROM users WHERE user_id='".$user_id."' LIMIT 1;") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            $company = Company::company_info(['id' => $row['company_id']]);
            $group = User_Group::user_group_info(['user_id' => $row['user_id']]);
            return [
                'user_id' => $row['user_id'],
                'access' => $row['access'],
                'type' => $company['type'],
                'company' => $company,
                'company_id' => $row['company_id'],
                'company_title' => Session::$mode != 2 ? flt_output($row['company_title']) : $row['company_title'],
                'first_name' => Session::$mode != 2 ? flt_output($row['first_name']) : $row['first_name'],
                'last_name' => Session::$mode != 2 ? flt_output($row['last_name']) : $row['last_name'],
                'middle_name' => Session::$mode != 2 ? flt_output($row['middle_name']) : $row['middle_name'],
                'full_name' => Session::$mode != 2 ? flt_output(trim($row['last_name'].' '.$row['first_name'])) : trim($row['last_name'].' '.$row['first_name']),
                'occupation' => $row['occupation'],
                'note' => $row['note'],
                'email' => $row['email'],
                'phone' => $row['phone'] ? phone_formatting($row['phone']) : '',
                'phone_raw' => $row['phone'],
                'password' => Session::$mode != 2 ? $row['password'] : '',
                'password_salt' => Session::$mode != 2 ? $row['password_salt'] : '',
                'group_id' => $group['group_id'],
                'group_title' => $group['group_title'],
                'show_home' => $company['show_home'],
                'sort_products' => $row['sort_products'] ? 'asc' : 'dsc',
                'sort_passports' => $row['sort_passports'] ? 'asc' : 'dsc',
                'sort_users' => $row['sort_users'] ? 'asc' : 'dsc',
                'sort_scans' => $row['sort_scans'],
                'sort_picks' => $row['sort_picks'],
                'sort_breachs' => $row['sort_breachs'],
                'filter_scans_company' => $row['filter_scans_company'],
                'filter_picks_company' => $row['filter_picks_company'],
                'scans_from_date' => $row['scans_from_date'],
                'scans_to_date' => $row['scans_to_date'],
                'picks_from_date' => $row['picks_from_date'],
                'picks_to_date' => $row['picks_to_date'],
                'breachs_from_date' => $row['breachs_from_date'],
                'breachs_to_date' => $row['breachs_to_date'],
                'blocked' => Session::$mode != 2 ? $row['blocked'] : '',
                'count_notifications' => $row['count_notifications']
            ];
        } else {
            return [
                'user_id' => 0,
                'access' => 0,
                'type' => 0,
                'company' => ['show_home' => 0, 'show_tasks' => 0],
                'company_id' => 0,
                'company_title' => '',
                'first_name' => '',
                'last_name' => '',
                'middle_name' => '',
                'full_name' => '',
                'occupation' => '',
                'note' => '',
                'email' => '',
                'phone' => '',
                'phone_raw' => '',
                'password' => '',
                'password_salt' => '',
                'group_id' => 0,
                'group_title' => '',
                'sort_products' => 'dsc',
                'sort_passports' => 'dsc',
                'sort_users' => 'dsc',
                'sort_scans' => 0,
                'sort_picks' => 0,
                'sort_breachs' => 0,
                'filter_scans_company' => 0,
                'filter_picks_company' => 0,
                'scans_from_date' => 0,
                'scans_to_date' => 0,
                'picks_from_date' => 0,
                'picks_to_date' => 0,
                'breachs_from_date' => 0,
                'breachs_to_date' => 0,
                'blocked' => 0,
                'count_notifications' => 0
            ];
        }
    }

}
