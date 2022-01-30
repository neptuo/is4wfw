<?php

	require_once("BaseTagLib.class.php");
	require_once("PhpRuntime.class.php");

	/**
	 * 
	 *  Class Variable. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2018-01-24
	 * 
	 */
	class Security extends BaseTagLib {

		public function requirePerm($name) {
			if ($name) {
				$login = $this->login();
				$perm = $login->getGroupPerm($name, $login->getMainGroupId(), false, 'false');
				$hasPerm = $perm['value'] == 'true';
			} else {
				$hasPerm = true;
			}

			return [PhpRuntime::$DecoratorExecuteName => $hasPerm];
		}
		
		public function requireGroup($name) {
			$hasGroup = false;
			$login = $this->login();
			foreach ($login->getGroups() as $group) {
				if ($group['name'] == $name) {
					$hasGroup = true;
					break;
				}
			}
			
			return [PhpRuntime::$DecoratorExecuteName => $hasGroup];
		}
	}

?>