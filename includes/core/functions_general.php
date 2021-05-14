<?php

	// INIT
	
	function class_autoload($api = false) {
		if ($api) {
			spl_autoload_register(function($class_name) {
				if (preg_match('~Dompdf|PhantomJs|Svg~iu', $class_name)) return false;
				require('./../includes/core/class_'.strtolower($class_name).'.php');
			});
		} else {
			spl_autoload_register(function($class_name) {
				if (preg_match('~Dompdf|PhantomJs|Svg~iu', $class_name)) return false;
				require('./includes/core/class_'.strtolower($class_name).'.php');
			});
		}
	}
	
	function controllers_common() {
		$includes_dir = opendir('./includes/controllers_common');
		while (($inc_file = readdir($includes_dir)) != false)
			if (strstr($inc_file,'.php')) require('./includes/controllers_common/'.$inc_file);
	}
	
	function controllers_call() {
		$includes_dir = opendir('./includes/controllers_call');
		while (($inc_file = readdir($includes_dir)) != false)
			if (strstr($inc_file,'.php')) require('./includes/controllers_call/'.$inc_file);
	}

	// COMMON
	
	function ts_timezone($ts, $tz) {
		return $ts + $tz * 60;
	}

	function date_str($ts, $mode) {
		// simple
		if (!$ts) return $ts = $mode != 'view' ? '' : 'не указана';
		if ($mode != 'view') return date('d.m.Y', $ts);
		// view
		$d = date('j n Y', $ts);
		$d = explode(' ', $d);
		return $d[0].' '.month_title($d[1], 'gen').' '.$d[2].' года';
	}
	
	function generate_rand_str($length, $type = 'hexadecimal') {
		// vars
		$str = '';
		if ($type == 'decimal') $chars = '0123456789';
		else if ($type == 'password') $chars = ['0123456789', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'];
		else $chars = 'abcdef0123456789';
		// generate
		for ($i = 0; $i < $length; $i++) {
			$microtime = round(microtime(true));
			if ($type != 'password') {
				srand($microtime + $i);
				$size = strlen($chars);
				$str .= $chars[rand(0, $size-1)];
			} else {
				$l = rand(-3, -1);
				$sub = substr($str, $l);
				if (!preg_match('~[0-9]~', $sub)) $chars_a = $chars[0];
				else if (!preg_match('~[A-Z]~', $sub)) $chars_a = $chars[1];
				else $chars_a = $chars[2];
				srand($microtime + $i);
				$size = strlen($chars_a);
				$str .= $chars_a[rand(0, $size-1)];
			}
		}
		// output
		return $str;
	}
	
	function month_title($id, $case) {
		$res = ['', ''];
		if ($id == 1) $res = ['январь', 'января'];
		if ($id == 2) $res = ['февраль', 'февраля'];
		if ($id == 3) $res = ['март', 'марта'];
		if ($id == 4) $res = ['апрель', 'апреля'];
		if ($id == 5) $res = ['май', 'мая'];
		if ($id == 6) $res = ['июнь', 'июня'];
		if ($id == 7) $res = ['июль', 'июля'];
		if ($id == 8) $res = ['август', 'августа'];
		if ($id == 9) $res = ['сентябрь', 'сентября'];
		if ($id == 10) $res = ['октябрь', 'октября'];
		if ($id == 11) $res = ['ноябрь', 'ноября'];
		if ($id == 12) $res = ['декабрь', 'декабря'];
		return $case == 'gen' ? $res[1] : $res[0];
	}

	function name_case($count, $names) {
		// calculate
		$count = abs($count);
		$a1 = $count % 10;
		$a2 = $count % 100;
		// output
		if ($a1 == 1 && ($a2 <= 10 || $a2 > 20)) return $names[0];
		if ($a1 >= 2 && $a1 <= 4 && ($a2 <= 10 || $a2 > 20)) return $names[1];
		return $names[2];
	}

	function error_404() {
		header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found', true, 404);
		HTML::display('./partials/service/404.html');
		exit();
	}
	
	function access_error($mode) {
		if ($mode == 1) echo '';
		else header('Location: /');
		exit();
	}
	
	function error_response($code, $msg, $data = []) {
		$result['error_code'] = $code;
		$result['error_msg'] = $msg;
		if ($data) $result['error_data'] = $data;
		return $result;
	}
	
	function response($response) {
		$response = !isset($response['error_code']) ? ['success'=>'true', 'response'=>$response] : ['success'=>'false', 'error'=>$response];
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
		exit();
	}

	function call($method_allow, $method_use, $data, $callback) {
		if ($method_allow != $method_use) response(error_response(1003, 'Application authorization failed: HTTP method is not supported.'));
		$data ? response($callback($data)) : response($callback());
	}
	
	function phone_formatting($phone) {
		if (preg_match('~^[78][\d]{10}$~', $phone)) $phone = preg_replace('~^([78])([\d]{3})([\d]{3})([\d]{2})([\d]{2})$~', '$1 ($2) $3-$4-$5', $phone);
		return $phone;
	}

	function paginator($total, $offset, $limit, $method = '', $params = []) {
		// params
		$a = [];
		foreach($params as $p) $a[] = is_numeric($p) ? $p : "'".$p."'";
		$params = $a ? ', '.implode(', ', $a) : '';
		// prev
		$prev_offset = $offset - $limit > 0 ? $offset - $limit : 0;
		$prev_class = $offset == 0 ? 'disabled' : '';
		$prev_onclick = !$prev_class ? ' onclick="'.$method.'('.$prev_offset.$params.');"' : '';
		// next
		$next_offset = $offset + $limit;
		$next_class = $offset + $limit >= $total ? 'disabled' : '';
		$next_onclick = !$next_class ? ' onclick="'.$method.'('.$next_offset.$params.');"' : '';
		// result
		$result = '<div class="paginator">';
		$result .= '<i'.$prev_onclick.' class="icon icon_arrow prev '.$prev_class.'"></i>';
		$result .= '<i'.$next_onclick.' class="icon icon_arrow next '.$next_class.'"></i>';
		$result .= '</div>';
		// output
		return $result;
	}

	function check_digit($id) {
		// vars
		$number_even = 0;
		$number_odd = 0;
		// sum (even, odd)
		for ($i = 0; $i < strlen($id); $i++) {
			$number = (int)substr($id, $i, 1);
			if ($i % 2 == 0) $number_even += $number;
			else $number_odd += $number;
		}
		// sum
		$sum = $number_even + ($number_odd * 3);
		// output
		return ($sum % 10) == 0 ? 0 : (10 - ($sum % 10));
	}

	function create_qr($id, $mode = 'product') {
		// leading zeros
		$barcode_id = str_pad($id, 12, '0', STR_PAD_LEFT);
		// check digit
		$barcode_id .= check_digit($barcode_id);
		// barcode path
		$barcode_path = '/storage/'.$mode.'_'.$id.'.png';
		// create
		if (Session::$mode != 2 && !file_exists('.'.$barcode_path)) {
			$url = SITE_SCHEME.'://'.SITE_DOMAIN.'/'.$mode.'s/'.$id;
			$barcode = new \Com\Tecnick\Barcode\Barcode();
			$bobj = $barcode->getBarcodeObj('QRCODE,H', $url, 528, 528)->setBackgroundColor('#ffffff');
			file_put_contents('.'.$barcode_path, $bobj->getPngData());
		}
		// output
		return ['path'=>$barcode_path, 'id'=>$barcode_id];
	}

	function create_ean13($id, $mode = 'product') {
		// leading zeros
		$barcode_id = str_pad($id, 12, '0', STR_PAD_LEFT);
		// check digit
		$barcode_id .= check_digit($barcode_id);
		// barcode path
		$barcode_path = '/storage/'.$mode.'_ean13_'.$barcode_id.'.png';
		// create
		if (Session::$mode != 2 && !file_exists('.'.$barcode_path)) {
			$barcode = new \Com\Tecnick\Barcode\Barcode();
			$bobj = $barcode->getBarcodeObj('EAN13', $barcode_id, 528, 83)->setBackgroundColor('#ffffff');
			file_put_contents('.'.$barcode_path, $bobj->getPngData());
		}
		// output
		return ['path'=>$barcode_path, 'id'=>$barcode_id];
	}

	function create_datamatrix($id, $mode = 'product') {
		// leading zeros
		$barcode_id = str_pad($id, 12, '0', STR_PAD_LEFT);
		// check digit
		$barcode_id .= check_digit($barcode_id);
		// barcode path
		$barcode_path = '/storage/'.$mode.'_datamatrix_'.$id.'.png';	
		// create
		if (Session::$mode != 2 && !file_exists('.'.$barcode_path)) {
			$url = SITE_SCHEME.'://'.SITE_DOMAIN.'/'.$mode.'s/'.$id.'/';
			$barcode = new \Com\Tecnick\Barcode\Barcode();
			$bobj = $barcode->getBarcodeObj('DATAMATRIX', $url, 528, 528)->setBackgroundColor('#ffffff');
			file_put_contents('.'.$barcode_path, $bobj->getPngData());
		}
		// output
		return ['path'=>$barcode_path, 'id'=>$barcode_id];
	}

	function create_pdf417($id, $mode = 'product') {
		// leading zeros
		$barcode_id = str_pad($id, 12, '0', STR_PAD_LEFT);
		// check digit
		$barcode_id .= check_digit($barcode_id);
		// barcode path
		$barcode_path = '/storage/'.$mode.'_pdf417_'.$id.'.png';
		// create	
		if (Session::$mode != 2 && !file_exists('.'.$barcode_path)) {
			$url = SITE_SCHEME.'://'.SITE_DOMAIN.'/'.$mode.'s/'.$id.'/';
			$barcode = new \Com\Tecnick\Barcode\Barcode();
			$bobj = $barcode->getBarcodeObj('PDF417', $url, 528, 229)->setBackgroundColor('#ffffff');
			file_put_contents('.'.$barcode_path, $bobj->getPngData());
		}
		// output
		return ['path'=>$barcode_path, 'id'=>$barcode_id];
	}