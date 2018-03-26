<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  //require_once("BaseTagLib.class.php");
  
  /**
   * 
   *  Class Hotproject.
   * 	hp dynamics
   *      
   *  @author     Marek SMM
   *  @timestamp  2009-06-18
   * 
   */  
  class Hotproject extends BaseTagLib {
  
    public function __construct() {
      parent::setTagLibXml("Hotproject.xml");
    }
    
    /**
     *
     *	Print projections management.
     *	C tag.
     *	
     *	@param	useFrames			use frames in output
     *
     */		 		 		 		     
    public function showProjectionEdit($useFrames = false) {
			global $dbObject;
			$return = '';
			
			if($_POST['hp-projection-delete'] == "Delete projection") {
				$dbObject->execute('DELETE FROM `w_projection` WHERE `id` = '.$_POST['hp-projection-id'].';');
			}
			
			$return .= ''
			.'<div class="hp-projection-edit">'
				.'<table>'
					.'<tr>'
						.'<th class="hp-projection-id">Id:</th>'
						.'<th class="hp-projection-name">Name:</th>'
						.'<th class="hp-projection-subname">Subname:</th>'
						.'<th class="hp-projection-value">Value:</th>'
						.'<th class="hp-projection-visible">Visible:</th>'
						.'<th class="hp-projection-edit">Edit:</th>'
					.'</tr>';
			$i = 0;
			$projections = $dbObject->fetchAll('SELECT `id`, `name`, `subname`, `value`, `visible` FROM `W_projection` ORDER BY `position`;');
			foreach($projections as $pr) {
				$return .= ''
				.'<tr class="'.((($i % 2) == 1) ? 'even' : 'idle').'">'
					.'<td class="hp-projection-id">'.$pr['id'].'</td>'
					.'<td class="hp-projection-name">'.$pr['name'].'</td>'
					.'<td class="hp-projection-subname">'.$pr['subname'].'</td>'
					.'<td class="hp-projection-value">'.$pr['value'].'</td>'
					.'<td class="hp-projection-viible">'.(($pr['id'] == 1) ? 'on' : 'off').'</td>'
					.'<td class="hp-projection-edit">'
						.'<form name="hp-projection-edit" method="post" action=""> '
							.'<input type="hidden" name="hp-projection-id" value="'.$pr['id'].'" />'
							.'<input type="hidden" name="hp-projection-edit" value="Edit projection" />'
							.'<input type="image" src="~/images/page_edi.png" name="hp-projection-edit" value="Edit projection" title="Edit projection" />'
						.'</form>'
						.'<form name="hp-projection-delete" method="post" action=""> '
							.'<input type="hidden" name="hp-projection-id" value="'.$pr['id'].'" />'
							.'<input type="hidden" name="hp-projection-delete" value="Delete projection" />'
							.'<input class="confirm" type="image" src="~/images/page_del.png" name="hp-projection-delete" value="Delete projection" title="Delete projection, id('.$pr['id'].')" />'
						.'</form>'
					.'</td>'
				.'</tr>';
				$i ++;
			}
			$return .= ''
				.'</table>'
			.'</div>'
			.'<hr />'
			.'<form name="hp-projection-add-new" method="post" action="">'
				.'<input type="submit" name="hp-projection-new" value="Add projection" />'
			.'</form>';
			
			if($useFrames != "false") {
				return parent::getFrame('Projections List', $return, '', true);
			} else {
				return $return;
			}
		}
		
		/**
		 *
		 *	Generate form for updating projection.
		 *	C tag.
		 *	
		 *	@param		useFrames				use frames in output
		 *	@param		showErrors			show errors in output		 		 		 
		 *
		 */		 		 		 		
		public function showProjectionForm($useFrames = false, $showErrors = false) {
			global $dbObject;
			$return = '';
			
			if($_POST['hp-projection-form-submit'] == "Save") {
				$projection['id'] = $_POST['hp-projection-form-id'];
				$projection['name'] = $_POST['hp-projection-form-name'];
				$projection['subname'] = $_POST['hp-projection-form-subname'];
				$projection['value'] = $_POST['hp-projection-form-value'];
				$projection['visible'] = (($_POST['hp-projection-form-visible'] == 'on') ? 1 : 0);
				
				$prj = $dbObject->fetchAll('SELECT `id` FROM `w_projection` WHERE `id` = '.$projection['id'].';');
				if(count($prj) != 0) {
					// update
					$dbObject->execute('UPDATE `w_projection` SET `name` = "'.$projection['name'].'", `subname` = "'.$projection['subname'].'", `value` = '.$projection['value'].', `visible` = '.$projection['visible'].' WHERE `id` = '.$projection['id'].';');
					$return .= '<h4 class="success">Projection updated!</h4>';
					$_POST['hp-projection-edit'] = "Edit projection";
					$_POST['hp-projection-id'] = $projection['id'];
				} else {
					// insert
					$nextId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `w_projection`;');
					$nextId = $nextId[0]['id'] + 1;
					$dbObject->execute('INSERT INTO `w_projection`(`id`, `name`, `subname`, `value`, `visible`, `position`) VALUES ('.$nextId.', "'.$projection['name'].'", "'.$projection['subname'].'", '.$projection['value'].', '.$projection['visible'].', '.$nextId.');');
					$return .= '<h4 class="success">Projection saved!</h4>';
					$_POST['hp-projection-edit'] = "Edit projection";
					$_POST['hp-projection-id'] = $nextId;
				}
			}
			
			if($_POST['hp-projection-edit'] == "Edit projection" || $_POST['hp-projection-new'] == "Add projection") {
				$projectionId = $_POST['hp-projection-id'];
				
				$projection = $dbObject->fetchAll('SELECT `name`, `subname`, `value`, `visible` FROM `W_projection` WHERE `id` = '.$projectionId.';');
				if($_POST['hp-projection-new'] == "Add projection" || count($projection) != 0) {
					$return .= ''
					.'<div class="hp-projection-form">'
						.'<form name="hp-projection-edit-detail" method="post" action="">'
							.'<div class="hp-projection-form-name">'
								.'<label for="hp-projection-form-name">Name:</label> '
								.'<input type="text" name="hp-projection-form-name" id="hp-projection-form-name" value="'.$projection[0]['name'].'" />'
							.'</div>'
							.'<div class="hp-projection-form-subname">'
								.'<label for="hp-projection-form-subname">Subname:</label> '
								.'<input type="text" name="hp-projection-form-subname" id="hp-projection-form-subname" value="'.$projection[0]['subname'].'" />'
							.'</div>'
							.'<div class="hp-projection-form-value">'
								.'<label for="hp-projection-form-value">Value:</label> '
								.'<select name="hp-projection-form-value" id="hp-projection-form-value">'
									.'<option value="0"'.(($projection[0]['value'] == 0) ? 'selected="selected"' : '').'>0</option>'
									.'<option value="1"'.(($projection[0]['value'] == 1) ? 'selected="selected"' : '').'>1</option>'
									.'<option value="2"'.(($projection[0]['value'] == 2) ? 'selected="selected"' : '').'>2</option>'
									.'<option value="3"'.(($projection[0]['value'] == 3) ? 'selected="selected"' : '').'>3</option>'
									.'<option value="4"'.(($projection[0]['value'] == 4) ? 'selected="selected"' : '').'>4</option>'
									.'<option value="5"'.(($projection[0]['value'] == 5) ? 'selected="selected"' : '').'>5</option>'
								.'</select>'
							.'</div>'
							.'<div class="hp-projection-form-visible">'
								.'<label for="hp-projection-form-visible">Visible:</label> '
								.'<input type="checkbox" name="hp-projection-form-visible" id="hp-projection-form-visible"'.(($projection[0]['visible'] == 1) ? 'checked="checked"' : '').' />'
							.'</div>'
							.'<div class="hp-projection-form-submit">'
								.'<input type="hidden" name="hp-projection-form-id" value="'.$projectionId.'" />'
								.'<input type="submit" name="hp-projection-form-submit" value="Save" />'
							.'</div>'
						.'</form>'
					.'</div>'
					.'<div class="clear"></div>';
				} else {
					if($showErrors != 'false') {
						$return .= '<h4 class="error">No projection selected!</h4>';
					}
				}
			} else {
				if($showErrors != 'false') {
					$return .= '<h4 class="error">No projection selected!</h4>';
				}
			}
			
			if($useFrames != "false" && strlen($return) != 0) {
				return parent::getFrame('Projection edit', $return, '');
			} else {
				return $return;
			}
		}
		
		/**
		 *
		 *	Show all visible projections.
		 *	C tag.	
		 *	
		 *	@param		templateId			template id		 		 	 
		 *
		 */		 		 		 		
		public function showProjections($templateId) {
			global $dbObject;
			global $loginObject;
			$return = '';
		
			$template = $dbObject->fetchAll('SELECT `content` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = '.$templateId.' AND `template_right`.`type` = '.WEB_R_READ.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'))');
			if(count($template) > 0) {
				$templateContent = $template[0]['content'];
				$projections = $dbObject->fetchAll('SELECT `name`, `subname`, `value` FROM `w_projection` WHERE `visible` = 1 ORDER BY `position`;');
				require_once("scripts/php/classes/FullTagParser.class.php");
				foreach($projections as $prj) {
      	  $_SESSION['current-projection']['name'] = $prj['name'];
        	$_SESSION['current-projection']['subname'] = $prj['subname'];
	        $_SESSION['current-projection']['value'] = $prj['value'];
  	      
					$Parser = new FullTagParser();
				  $Parser->setContent($templateContent);
			  	$Parser->startParsing();
	 				$return .= $Parser->getResult();
				}
			} else {
				trigger_error("Template id is not valid!!", E_USER_WARNING);
				return;
			}
		
			return $return;
		}
		
		/**
		 *
		 *	Retuens part of projection specified in type.
		 *	
		 *	@param		type					part type ( name | subnam | value )		 		 
		 *
		 */		 		 		 		
		public function showProjectionDetail($type) {
			$return = '';
			switch($type) {
				case 'name' : $return = $_SESSION['current-projection']['name']; break;
				case 'subname' : $return = $_SESSION['current-projection']['subname']; break;
				case 'value' : $return = $_SESSION['current-projection']['value']; break;
				default: trigger_error('Type is not valid value! [passed value is "'.$type.'"]', E_USER_WARNING);
			}
			
			return $return;
		}
		
		/**
     *
     *	Print projections management.
     *	C tag.
     *	
     *	@param	useFrames			use frames in output
     *
     */	
		public function showReferencenEdit($useFrames = false) {
			global $dbObject;
			$return = '';
			
			if($_POST['hp-reference-delete'] == "Delete reference") {
				$dbObject->execute('DELETE FROM `w_reference` WHERE `id` = '.$_POST['hp-reference-id'].';');
			}
			
			$references = $dbObject->fetchAll('SELECT `id`, `name`, `subname`, `type`, `visible` FROM `w_reference` ORDER BY `position`;');
			if(count($references) > 0) {
				$typeNames = array(0 => "Významní investoři", 1 => "Významné akce");
				$i = 0;
				$return .= ''
				.'<div class="references-edit">'
					.'<table>'
						.'<tr>'
							.'<th class="hp-reference-id">Id:</th>'
							.'<th class="hp-reference-name">Name:</th>'
							.'<th class="hp-reference-subname">Subname:</th>'
							.'<th class="hp-reference-type">Type:</th>'
							.'<th class="hp-reference-visible">Visible:</th>'
							.'<th class="hp-reference-submit">Edit:</th>'
						.'</tr>';
				foreach($references as $ref) {
					$return .= ''
					.'<tr class="'.((($i % 2) == 1) ? 'even' : 'idle').'">'
						.'<td clas="hp-reference-id">'.$ref['id'].'</td>'
						.'<td clas="hp-reference-name">'.$ref['name'].'</td>'
						.'<td clas="hp-reference-subname">'.$ref['subname'].'</td>'
						.'<td clas="hp-reference-type">'.$typeNames[$ref['type']].'</td>'
						.'<td clas="hp-reference-visible">'.(($ref['visible']) ? 'on' : 'off').'</td>'
						.'<td clas="hp-reference-submit">'
							.'<form name="reference-edit-1" method="post" action="">'
								.'<input type="hidden" name="hp-reference-id" value="'.$ref['id'].'" />'
								.'<input type="hidden" name="hp-reference-edit" value="Edit reference" />'
								.'<input type="image" src="~/images/page_edi.png" name="hp-reference-edit" value="Edit reference" title="Edit reference" />'
							.'</form> '
							.'<form name="reference-edit-2" method="post" action="">'
								.'<input type="hidden" name="hp-reference-id" value="'.$ref['id'].'" />'
								.'<input type="hidden" name="hp-reference-delete" value="Delete reference" />'
								.'<input class="confirm" type="image" src="~/images/page_del.png" name="hp-reference-delete" value="Delete reference" title="Delete reference, id('.$ref['id'].')" />'
							.'</form>'
						.'</td>'
					.'</tr>';
					$i ++;
				}
				$return .= ''
					.'</table>'
					.'<hr />'
					.'<form name="hp-reference-add-new" method="post" action="">'
						.'<input type="submit" name="hp-reference-new" value="Add reference" />'
					.'</form>'
				.'</div>';
			} else {
				$return .= '<h4 class="error">No references to show</h4>';
			}
		
			if($useFrames != "false") {
				return parent::getFrame('References List', $return, '', true);
			} else {
				return $return;
			}
		}
		
		/**
		 *
		 *	Generate form for updating reference.
		 *	C tag.
		 *	
		 *	@param		useFrames				use frames in output
		 *	@param		showErrors			show errors in output		 		 		 
		 *
		 */		 		 		 		
		public function showReferenceForm($useFrames = false, $showErrors = false) {
			global $dbObject;
			$return = '';
			
			if($_POST['hp-reference-form-submit'] == "Save") {
				$reference['id'] = $_POST['hp-reference-form-id'];
				$reference['name'] = $_POST['hp-reference-form-name'];
				$reference['subname'] = $_POST['hp-reference-form-subname'];
				$reference['type'] = $_POST['hp-reference-form-type'];
				$reference['visible'] = (($_POST['hp-reference-form-visible'] == 'on') ? 1 : 0);
				
				$prj = $dbObject->fetchAll('SELECT `id` FROM `w_reference` WHERE `id` = '.$reference['id'].';');
				if(count($prj) != 0) {
					// update
					$dbObject->execute('UPDATE `w_reference` SET `name` = "'.$reference['name'].'", `subname` = "'.$reference['subname'].'", `type` = '.$reference['type'].', `visible` = '.$reference['visible'].' WHERE `id` = '.$reference['id'].';');
					$return .= '<h4 class="success">Reference updated!</h4>';
					$_POST['hp-reference-edit'] = "Edit reference";
					$_POST['hp-reference-id'] = $reference['id'];
				} else {
					// insert
					$nextId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `w_reference`;');
					$nextId = $nextId[0]['id'] + 1;
					$dbObject->execute('INSERT INTO `w_reference`(`id`, `name`, `subname`, `type`, `visible`, `position`) VALUES ('.$nextId.', "'.$reference['name'].'", "'.$reference['subname'].'", '.$reference['type'].', '.$reference['visible'].', '.$nextId.');');
					$return .= '<h4 class="success">Reference saved!</h4>';
					$_POST['hp-reference-edit'] = "Edit reference";
					$_POST['hp-reference-id'] = $nextId;
				}
			}
			
			if($_POST['hp-reference-edit'] == "Edit reference" || $_POST['hp-reference-new'] == "Add reference") {
				$referenceId = $_POST['hp-reference-id'];
				
				$reference = $dbObject->fetchAll('SELECT `name`, `subname`, `type`, `visible` FROM `W_reference` WHERE `id` = '.$referenceId.';');
				if($_POST['hp-reference-new'] == "Add reference" || count($reference) != 0) {
					$return .= ''
					.'<div class="hp-reference-form">'
						.'<form name="hp-reference-edit-detail" method="post" action="">'
							.'<div class="hp-reference-form-name">'
								.'<label for="hp-reference-form-name">Name:</label> '
								.'<input type="text" name="hp-reference-form-name" id="hp-reference-form-name" value="'.$reference[0]['name'].'" />'
							.'</div>'
							.'<div class="hp-reference-form-subname">'
								.'<label for="hp-reference-form-subname">Subname:</label> '
								.'<input type="text" name="hp-reference-form-subname" id="hp-reference-form-subname" value="'.$reference[0]['subname'].'" />'
							.'</div>'
							.'<div class="hp-reference-form-type">'
								.'<label for="hp-reference-form-type">Type:</label> '
								.'<select name="hp-reference-form-type" id="hp-reference-form-type">'
									.'<option value="0"'.(($reference[0]['type'] == 0) ? 'selected="selected"' : '').'>Významní investoři</option>'
									.'<option value="1"'.(($reference[0]['type'] == 1) ? 'selected="selected"' : '').'>Významné akce</option>'
								.'</select>'
							.'</div>'
							.'<div class="hp-reference-form-visible">'
								.'<label for="hp-reference-form-visible">Visible:</label> '
								.'<input type="checkbox" name="hp-reference-form-visible" id="hp-reference-form-visible"'.(($reference[0]['visible'] == 1) ? 'checked="checked"' : '').' />'
							.'</div>'
							.'<div class="hp-reference-form-submit">'
								.'<input type="hidden" name="hp-reference-form-id" value="'.$referenceId.'" />'
								.'<input type="submit" name="hp-reference-form-submit" value="Save" />'
							.'</div>'
						.'</form>'
					.'</div>'
					.'<div class="clear"></div>';
				} else {
					if($showErrors != 'false') {
						$return .= '<h4 class="error">No reference selected!</h4>';
					}
				}
			} else {
				if($showErrors != 'false') {
					$return .= '<h4 class="error">No references selected!</h4>';
				}
			}
			
			if($useFrames != "false" && strlen($return) != 0) {
				return parent::getFrame('Projection edit', $return, '');
			} else {
				return $return;
			}
		}
		
		/**
		 *
		 *	Show all visible references.
		 *	C tag.	
		 *	
		 *	@param		templateId			template id		 		 	 
		 *
		 */		 		 		 		
		public function showreferences($templateId) {
			global $dbObject;
			global $loginObject;
			$return = '';
		
			$template = $dbObject->fetchAll('SELECT `content` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = '.$templateId.' AND `template_right`.`type` = '.WEB_R_READ.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'))');
			if(count($template) > 0) {
				$templateContent = $template[0]['content'];
				$references = $dbObject->fetchAll('SELECT `name`, `subname`, `type` FROM `w_reference` WHERE `visible` = 1 ORDER BY `position`;');
				require_once("scripts/php/classes/FullTagParser.class.php");
				foreach($references as $ref) {
      	  $_SESSION['current-reference']['name'] = $ref['name'];
        	$_SESSION['current-reference']['subname'] = $ref['subname'];
	        $_SESSION['current-reference']['type'] = $ref['type'];
	        $_SESSION['current-reference']['type-name'] = $ref['type'];
  	      
					$Parser = new FullTagParser();
				  $Parser->setContent($templateContent);
			  	$Parser->startParsing();
	 				$return .= $Parser->getResult();
				}
			} else {
				trigger_error("Template id is not valid!!", E_USER_WARNING);
				return;
			}
		
			return $return;
		}
		
		/**
		 *
		 *	Retuens part of reference specified in type.
		 *	
		 *	@param		type					part type ( name | subnam | type | type-name )		 		 
		 *
		 */		 		 		 		
		public function showReferenceDetail($type) {
			$return = '';
			$typeNames = array(0 => "Významní investoři", 1 => "Významné akce");
			switch($type) {
				case 'name' : $return = $_SESSION['current-reference']['name']; break;
				case 'subname' : $return = $_SESSION['current-reference']['subname']; break;
				case 'type' : $return = $_SESSION['current-reference']['type']; break;
				case 'type-name' : $return = $typeNames[$_SESSION['current-reference']['type']]; break;
				default: trigger_error('Type is not valid value! [passed value is "'.$type.'"]', E_USER_WARNING);
			}
			
			return $return;
		}

	}

?>
