<?php

class Contract {

    public static function contract_info($contract_id) {
        // info
        $q = DB::query("SELECT contract_id, company_id, contract_title, contract_date, annex_number, annex_date FROM contracts WHERE contract_id='".$contract_id."' LIMIT 1;") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            return [
                'id' => $row['contract_id'],
                'company_id' => $row['company_id'],
                'title' => $row['contract_title'],
                'date' => $row['contract_date'] ? date('d.m.Y', $row['contract_date']) : '',
                'annex_number' => $row['annex_number'] ? $row['annex_number'] : '',
                'annex_date' => $row['annex_date'] ? date('d.m.Y', $row['annex_date']) : ''
            ];
        } else {
            return [
                'id' => 0,
                'company_id' => 0,
                'title' => '',
                'date' => '',
                'annex_number' => '',
                'annex_date' => ''
            ];
        }
    }

}
