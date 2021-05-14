<?php

	// COMMON
	
	function flt_input($var) {
		return str_replace(
			[ '\\', "\0", "'", '"', "\x1a", "\x00" ],
			[ '\\\\', '\\0', "\\'", '\\"', '\\Z', '\\Z' ],
			$var);
	}
	
	function flt_output($str) {
		return htmlentities($str, ENT_QUOTES, 'UTF-8');
	}
	
	function unflt_output($str) {
		$str = html_entity_decode($str);
		$str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
		return preg_replace( '/"([^"]*)"/', "«$1»", $str );
	}
	
	function p_hash($password, $salt) {
		return md5(md5($salt).md5($password));
	}
