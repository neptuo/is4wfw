<?php

namespace php\libs;

require_once("BaseTagLib.class.php");

use BaseTagLib;
use Exception;
use DateTime as GlobalDateTime;


/**
 * 
 *  Class DateTimeHelper. 
 *      
 *  @author     maraf
 *  @timestamp  2021-12-21
 * 
 */
class DateTime extends BaseTagLib {

	private $formats = [];
	private $values = [];
	private $diff;

	private function getGlobalDateTime($value) {
		if (!($value instanceof GlobalDateTime)) {
			$timestamp = $value;
			$value = new GlobalDateTime();
			$value->setTimestamp($timestamp);
		}

		return $value;
	}

	public function declare($name, $value, $setYear = "", $setMonth = "", $setDay = "", $setHour = "", $setMinute = "", $setSecond = "", $setWeekDay = "", $addYear = "", $addMonth = "", $addDay = "", $addHour = "", $addMinute = "", $addSecond = "") {
		$this->values[$name] = $this->getGlobalDateTime($value);

		$this->set(
			$name, 
			$setYear, 
			$setMonth, 
			$setDay, 
			$setHour, 
			$setMinute, 
			$setSecond,
			$setWeekDay
		);

		$this->add(
			$name, 
			$addYear, 
			$addMonth, 
			$addDay, 
			$addHour, 
			$addMinute, 
			$addSecond
		);
	}

	public function set($name, $year = "", $month = "", $day = "", $hour = "", $minute = "", $second = "", $weekDay = "") {
		$value = $this->get($name);

		if (!empty($year) || !empty($month) || !empty($day)) {
			if ($year == "") {
				$year = $value->format("Y");
			}

			if ($month == "") {
				$month = $value->format("m");
			}

			if ($day == "") {
				$day = $value->format("d");
			}

			$value->setDate($year, $month, $day);
		}

		if (!empty($hour) || !empty($minute) || !empty($second)) {
			if ($hour == "") {
				$hour = $value->format("H");
			}

			if ($minute == "") {
				$minute = $value->format("i");
			}

			if ($second == "") {
				$second = $value->format("s");
			}

			$value->setTime($hour, $minute, $second);
		}

		if (!empty($weekDay)) {
			$value->modify("$weekDay this week");
		}
	}

	private function ensureModifySign($value) {
		if ($value[0] != "-") {
			$value = "+" . $value;
		}

		return $value;
	}

	public function add($name, $year = "", $month = "", $day = "", $hour = "", $minute = "", $second = "") {
		$value = $this->get($name);

		if ($year != "") {
			$year = $this->ensureModifySign($year);
			$value->modify("$year year");
		}

		if ($month != "") {
			$month = $this->ensureModifySign($month);
			$value->modify("$month month");
		}

		if ($day != "") {
			$day = $this->ensureModifySign($day);
			$value->modify("$day day");
		}

		if ($hour != "") {
			$hour = $this->ensureModifySign($hour);
			$value->modify("$hour hour");
		}

		if ($minute != "") {
			$minute = $this->ensureModifySign($minute);
			$value->modify("$minute minute");
		}

		if ($second != "") {
			$second = $this->ensureModifySign($second);
			$value->modify("$second second");
		}
	}

	public function format($name = "", $value = "") {
		$this->formats[$name] = $value;
	}

	private function get($name) {
		if (array_key_exists($name, $this->values)) {
			return $this->values[$name];
		}

		throw new Exception("Missing date time with name '$name'.");
	}

	public function diff($start, $end) {
		$end = $this->getGlobalDateTime($end);
		$start = $this->getGlobalDateTime($start);

		$this->diff = $start->diff($end);
	}

	public function getProperty($name) {
		$parts = explode("-", $name, 2);
		$name = $parts[0];

		switch ($name) {
			case "now":
				if (count($parts) == 1) {
					return time();
				}

				$value = new GlobalDateTime();
				break;
			case "today":
				$value = new GlobalDateTime();
				$value->setTime(0, 0, 0);
				break;
			case "diff":
				if (count($parts) == 2) {
					return $this->getDiffPart($parts[1]);
				} else {
					return $this->diff;
				}
				break;
			default:
				if (!array_key_exists($name, $this->values)) {
					return;
				}

				$value = $this->get($name);
				break;
		}

		if (count($parts) == 2) {
			$format = $parts[1];

			switch($format) {
				case "year":
				case "Y":
					return $value->format("Y");

				case "month":
				case "m":
					return $value->format("m");

				case "day":
				case "d":
					return $value->format("d");

				case "hour":
				case "H":
					return $value->format("H");

				case "minute":
				case "i":
					return $value->format("i");

				case "second":
				case "s":
					return $value->format("s");

				case "format":
					if (array_key_exists("", $this->formats)) {
						return $value->format($this->formats[""]);
					}

					return "";
				
				default:
					if (array_key_exists($format, $this->formats)) {
						return $value->format($this->formats[$format]);
					}

					return "";
			}
		}

		return $value->getTimestamp();
	}

	private function getDiffPart($format) {
		if ($this->diff == null) {
			return "";
		}

		$diff = $this->diff;
		switch ($format) {
			case "positive":
				return $diff->invert === 0;
			case "y":
				return $diff->y;
			case "m":
				return $diff->m;
			case "d":
				return $diff->d;
			case "h":
				return $diff->h;
			case "i":
				return $diff->i;
			case "s":
				return $diff->s;
			case "total-m":
				return $diff->y * 12 + $diff->m;
			case "total-d":
				return $diff->days;
			case "total-h":
				return $diff->days * 24 + $diff->h;
			case "total-i":
				return ($diff->days * 24 + $diff->h) * 60 + $diff->i;
			case "total-s":
				return (($diff->days * 24 + $diff->h) * 60 + $diff->i) * 60 + $diff->s;
			
			default:
				return "";
		}
	}
}

?>