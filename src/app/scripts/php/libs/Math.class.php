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

		public function number(PropertyReference $out, $set = "", $add = "", $multiply = "", $divide = "") {
			$value = $out->get();

			if ($set != "") {
				$value = $set;
			} else if (empty($value)) {
				$value = 0;
			}

			if ($add != "") {
				$value += $add;
			}

			if ($multiply != "") {
				$value *= $multiply;
			}

			if ($divide != "") {
				$value /= $divide;
			}

			if (is_nan($value) || is_infinite($value)) {
				$value = null;
			}

			$out->set($value);
		}
	}

?>