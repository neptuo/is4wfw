<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/PropertyReference.class.php");

	/**
	 * 
	 *  Class Math. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2022-01-25
	 * 
	 */
	class Math extends BaseTagLib {

		public function number(PropertyReference $out, $set = "", $add = "", $subtract = "", $multiply = "", $divide = "") {
			$value = $out->get();

			if ($set != "") {
				$value = $set;
			} else if (empty($value)) {
				$value = 0;
			}

			if ($add != "") {
				$value += $add;
			}

			if ($subtract != "") {
				$value -= $subtract;
			}

			if ($multiply != "") {
				$value *= $multiply;
			}

			if ($divide != "" && $divide != 0) {
				$value /= $divide;
			}

			if (is_nan($value) || is_infinite($value)) {
				$value = null;
			}

			$out->set($value);
		}

		public function round(PropertyReference $out, $decimals = 0) {
			$value = $out->get();
			$value = round($value, $decimals, PHP_ROUND_HALF_UP);
			$out->set($value);
		}
		
		public function random(PropertyReference $out, $min, $max) {
			$value = rand($min, $max);
			$out->set($value);
		}
	}

?>