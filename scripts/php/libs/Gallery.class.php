<?php

/**
 *
 *  Require base tag lib class.
 *
 */
require_once("BaseTagLib.class.php");

require_once("scripts/php/classes/ResourceBundle.class.php");
require_once("scripts/php/classes/dataaccess/Select.class.php");
require_once("scripts/php/classes/RoleHelper.class.php");

/**
 * 
 *  Class Gallery. 
 *      
 *  @author     Marek SMM
 *  @timestamp  2017-11-26
 * 
 */
class Gallery extends BaseTagLib {

    public function __construct() {
        global $webObject;

        parent::setTagLibXml("xml/Gallery.xml");
		parent::loadResourceBundle('gallery');
		
		self::transformFileSystem();
    }
	
	protected function canUserDir($objectId, $rightType) {
		return RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(FileAdmin::$DirectoryRightDesc, $objectId, $rightType));
	}
	
	protected function canUserFile($objectId, $rightType) {
		return RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(FileAdmin::$FileRightDesc, $objectId, $rightType));
	}
	
	//C-tag
	public function imageList($dirId) {
		$return = '';

		$files = parent::dao('File')->getFromDirectory($dirId);
	}

?>