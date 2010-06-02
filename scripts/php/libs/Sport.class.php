<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  
  /**
   * 
   *  Class Sport.
   * 	all about sport	     
   *      
   *  @author     Marek SMM
   *  @timestamp  2009-10-10
   * 
   */  
  class Sport extends BaseTagLib {
  
  	private $BundleName = 'sport';
  	
  	private $BundleLang = 'cs';
  	
  	private $SubQueriesSQL = '';
  	
  	private $ConditionsSQL = '';
  
    public function __construct() {
    	global $webObject;
    
      parent::setTagLibXml("xml/Sport.xml");
      
      if($webObject->LanguageName != '') {
				$rb = new ResourceBundle();
				if($rb->testBundleExists($this->BundleName, $webObject->LanguageName)) {
					$this->BundleLang = $webObject->LanguageName;
				}
			}
      
      require_once("scripts/php/classes/CustomTagParser.class.php");
      require_once("scripts/php/classes/ResourceBundle.class.php");
    }
    
    /**
     *
     *	Setups request variables to session.
     *	     
     */		 		 		     
    public function setFromRequest() {
			if($_REQUEST['season-id'] != '') {
				$_SESSION['sport']['season-id'] = $_REQUEST['season-id'];
			}
			if($_REQUEST['team-id'] != '') {
				$_SESSION['sport']['team-id'] = $_REQUEST['team-id'];
			}
			if($_REQUEST['player-id'] != '') {
				$_SESSION['sport']['player-id'] = $_REQUEST['player-id'];
			}
			if($_REQUEST['match-id'] != '') {
				$_SESSION['sport']['match-id'] = $_REQUEST['match-id'];
			}
			
			echo $_SESSION['player-id'];
		}
    
    /**
     *
     *	Show select season form.
     *		      
     *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
     *	     
     */		 		 		     
    public function selectSeason($useFrames = false, $showMsg = false) {
			global $dbObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if($_POST['select-season-submit'] == $rb->get('season.select')) {
				$seasonId = $_POST['select-season'];
				if($seasonId == 0) {
					$_SESSION['sport']['season-id'] = '';
				} else {
					$_SESSION['sport']['season-id'] = $seasonId;
				} 
			}
			
			$return .= ''
			.'<div class="select-season">'
				.'<form name="select-season" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<label for="select-season">'.$rb->get('season.selectlab').':</label> '
					.'<select name="select-season" id="select-season">'
						.'<option value="0">'.$rb->get('season.all').'</option>'
						.self::getSeasonsOptions(0, 0, $_SESSION['sport']['season-id'])
					.'</select> '
					.'<input type="submit" name="select-season-submit" value="'.$rb->get('season.select').'" />'
				.'</form>'
			.'</div>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('seasons.title'), $return, "", true);
			}
		}
    
    /**
     *
     *	Show select table form.
     *		      
     *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
     *	     
     */		 		 		     
    public function selectTable($useFrames = false, $showMsg = false) {
			global $dbObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if($_POST['select-table-submit'] == $rb->get('tables.select')) {
				$tableId = $_POST['select-table'];
				if($tableId == 0) {
					$_SESSION['sport']['table-id'] = '';
				} else {
					$_SESSION['sport']['table-id'] = $tableId;
				} 
			}
			
			$return .= ''
			.'<div class="select-table">'
				.'<form name="select-table" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<label for="select-table">'.$rb->get('tables.selectlab').':</label> '
					.'<select name="select-table" id="select-table">'
						.'<option value="0">'.$rb->get('tables.all').'</option>'
						.self::getTablesOptions($_SESSION['sport']['table-id'])
					.'</select> '
					.'<input type="submit" name="select-table-submit" value="'.$rb->get('tables.select').'" />'
				.'</form>'
			.'</div>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('tables.title'), $return, "", true);
			}
		}
    
    /**
     *
     *	Show select team form.
     *		      
     *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
     *	     
     */		 		 		     
    public function selectTeam($useFrames = false, $showMsg = false) {
			global $dbObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if($_POST['select-team-submit'] == $rb->get('team.select')) {
				$teamId = $_POST['select-team'];
				if($teamId == 0) {
					$_SESSION['sport']['team-id'] = '';
				} else {
					$_SESSION['sport']['team-id'] = $teamId;
				} 
			}
			
			$return .= ''
			.'<div class="select-team">'
				.'<form name="select-team" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<label for="select-team">'.$rb->get('team.selectlab').':</label> '
					.'<select name="select-team" id="select-team">'
						.'<option value="0">'.$rb->get('team.all').'</option>'
						.self::getTeamsOptions($_SESSION['sport']['team-id'])
					.'</select> '
					.'<input type="submit" name="select-team-submit" value="'.$rb->get('team.select').'" />'
				.'</form>'
			.'</div>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('teams.title'), $return, "", true);
			}
		}
    
    /**
     *
     *	List of seasons for editing.
     *	C tag.
     *	
     *	@param		pageId					next page id
     *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
     *
     */		 		 		 		     
    public function showEditSeasons($pageId = false, $useFrames = false, $showMsg = false) {
    	global $dbObject;
    	global $webObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$retrun = '';
			
			if($pageId != false) {
				$url = $webObject->composeUrl($pageId);
			} else {
				$url = '';
			}
			
			if($_POST['season-delete'] == $rb->get('seasons.delete')) {
				$seasonId = $_POST['season-id'];
				$dbObject->execute('DELETE FROM `w_sport_season` WHERE `id` = '.$seasonId.';');
			}
			
			$seasons = $dbObject->fetchAll('SELECT `id`, `start_year`, `end_year` FROM `w_sport_season` ORDER BY `id`;');
			if(count($seasons) > 0) {
				$return .= ''
				.'<table class="seasons-list">'
					.'<tr>'
						.'<th class="seasons-list-id">'.$rb->get('seasons.id').':</th>'
						.'<th class="seasons-list-start">'.$rb->get('seasons.startyear').':</th>'
						.'<th class="seasons-list-end">'.$rb->get('seasons.endyear').':</th>'
						.'<th class="seasons-list-edit">'.$rb->get('seasons.edit').':</th>'
					.'</tr>';
				for($i = 0; $i < count($seasons); $i ++) {
					$return .= ''
					.'<tr class="'.((($i % 2) == 0 ) ? 'idle' : 'even').'">'
						.'<td class="seasons-list-id">'.$seasons[$i]['id'].'</td>'
						.'<td class="seasons-list-start">'.$seasons[$i]['start_year'].'</td>'
						.'<td class="seasons-list-end">'.$seasons[$i]['end_year'].'</td>'
						.'<td class="seasons-list-edit">'
							.'<form name="seasons-edit" method="post" action="'.$actionUrl.'">'
								.'<input type="hidden" name="season-id" value="'.$seasons[$i]['id'].'" />'
								.'<input type="hidden" name="season-edit" value="'.$rb->get('seasons.edit').'" />'
								.'<input type="image" src="~/images/page_edi.png" name="season-edit" value="'.$rb->get('seasons.edit').'" title="'.$rb->get('seasons.editcap').'" />'
							.'</form> '
							.'<form name="seasons-delete" method="post">'
								.'<input type="hidden" name="season-id" value="'.$seasons[$i]['id'].'" />'
								.'<input type="hidden" name="season-delete" value="'.$rb->get('seasons.delete').'" />'
								.'<input class="confirm" type="image" src="~/images/page_del.png" name="season-delete" value="'.$rb->get('seasons.delete').'" title="'.$rb->get('seasons.deletecap').', id ('.$seasons[$i]['id'].')" />'
							.'</form>'
						.'</td>'
					.'</tr>';
				}
			
				$return .=''
				.'</table>';
			} else {
				$return .= '<h4 class="warning">'.$rb->get('seasons.warning.nodata').'</h4>';
			}
			$return .= ''
			.'<hr />'
			.'<form name="season-new" method="post" action="'.$actionUrl.'">'
				.'<input type="submit" name="season-new" value="'.$rb->get('seasons.new').'" title="'.$rb->get('seasons.newcap').'" />'
			.'</form>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('seasons.title'), $return, "", true);
			}
		}
		
		/**
		 *
		 *	Edit season.
		 *	C tag.
		 *	
     *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
		 *
		 */		 		 		 		 		
		public function showEditSeasonForm($useFrames = false, $showMsg = false) {
			global $dbObject;
			$return = '';
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			
			if($_POST['season-save'] == $rb->get('seasons.form.save')) {
				$seasonId = $_POST['season-id'];
				$seasonStart = $_POST['season-edit-start'];
				$seasonEnd = $_POST['season-edit-end'];
				$season = $dbObject->fetchAll('SELECT `id`, `start_year`, `end_year` FROM `w_sport_season` WHERE `id` = '.$seasonId.';');
				if(count($season) > 0) {
					$dbObject->execute('UPDATE `w_sport_season` SET `start_year` = '.$seasonStart.', `end_year` = '.$seasonEnd.' WHERE `id` = '.$seasonId.';');
				} else {
					$dbObject->execute('INSERT INTO `w_sport_season`(`start_year`, `end_year`) VALUES ('.$seasonStart.', '.$seasonEnd.');');
				}
			}
			
			if($_POST['season-new'] == $rb->get('seasons.new') || $_POST['season-edit'] == $rb->get('seasons.edit')) {
				if($_POST['season-edit'] == $rb->get('seasons.edit')) {
					$seasonId = $_POST['season-id'];
					$season = $dbObject->fetchAll('SELECT `id`, `start_year`, `end_year` FROM `w_sport_season` WHERE `id` = '.$seasonId.';');
					$season = $season[0];
				} else {
					$season = array();
				}
				
				$return .= ''
				.'<div class="season-edit-form">'
					.'<form name="season-edit-form" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
						.'<div class="season-edit-start">'
							.'<label for="season-edit-start">'.$rb->get('seasons.form.startyear').':</label> '
							.'<input type="text" name="season-edit-start" id="season-edit-start" value="'.$season['start_year'].'" />'
						.'</div>'
						.'<div class="season-edit-end">'
							.'<label for="season-edit-end">'.$rb->get('seasons.form.endyear').':</label> '
							.'<input type="text" name="season-edit-end" id="season-edit-end" value="'.$season['end_year'].'" />'
						.'</div>'
						.'<div class="season-edit-submit">'
							.'<input type="hidden" name="season-id" value="'.$season['id'].'" />'
							.'<input type="submit" name="season-save" value="'.$rb->get('seasons.form.save').'" />'
						.'</div>'
					.'</form>'
				.'</div>'
				.'<div class="clear"></div>';
				
				if($useFrames == "false") {
					return $return;
				} else {
					return parent::getFrame($rb->get('seasons.form.title'), $return, "", true);
				}
			}
		}
    
    /**
     *
     *	List of teams for editing.
     *	C tag.
     *	
     *	@param		pageId					next page id
     *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
     *
     */		 		 		 		     
    public function showEditTeams($pageId = false, $useFrames = false, $showMsg = false) {
    	global $dbObject;
    	global $webObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$retrun = '';
			
			if($pageId != false) {
				$url = $webObject->composeUrl($pageId);
			} else {
				$url = '';
			}
			
			if($_POST['team-add-season-submit'] == $rb->get('teams.addseasonsubmit')) {
				$addteam = $_POST['teams-add-season-team'];
				$addseason = $_POST['teams-add-season-season'];
				
				$retsql = $dbObject->fetchAll('SELECT `id` FROM `w_sport_team` WHERE `id` = '.$addteam.' AND `season` = '.$addseason.';');
				if(count($retsql) == 0) {
					$reteam = $dbObject->fetchAll('SELECT `name`, `logo` FROM `w_sport_team` WHERE `id` = '.$addteam.' ORDER BY `season` ASC;');
					$dbObject->execute('INSERT INTO `w_sport_team`(`id`, `name`, `logo`, `season`) VALUES('.$addteam.', "'.$reteam[0]['name'].'", "'.$reteam[0]['logo'].'", '.$addseason.');');
					$dbObject->execute('INSERT INTO `w_sport_table`(`team`, `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season`) VALUES('.$addteam.', 0, 0, 0, 0, 0, 0, 0, '.$addseason.');');
				} else {
					if($showMsg != 'false') {
						$return .= '<h4 class="error">'.$rb->get('teams.addseasonerr').'</h4>';
					}
				}
			}
			
			if($_POST['team-delete'] == $rb->get('teams.delete')) {
				$teamId = $_POST['team-id'];
				$seasonId = $_POST['season-id'];
				$dbObject->execute('DELETE FROM `w_sport_team` WHERE `id` = '.$teamId.' AND `season` = '.$seasonId.';');
				$dbObject->execute('DELETE FROM `w_sport_table` WHERE `team` = '.$teamId.' AND `season` = '.$seasonId.';');
			}
			
			if($_SESSION['sport']['season-id'] != '') {
				if($_SESSION['sport']['team-id'] != '') {
					$teams = $dbObject->fetchAll('SELECT `w_sport_team`.`id`, `w_sport_team`.`name`, `w_sport_team`.`season`, `w_sport_team`.`logo`, `w_sport_season`.`start_year`, `w_sport_season`.`end_year` FROM `w_sport_team` LEFT JOIN `w_sport_season` ON `w_sport_team`.`season` = `w_sport_season`.`id` WHERE `w_sport_team`.`season` = '.$_SESSION['sport']['season-id'].' AND `w_sport_team`.`id` = '.$_SESSION['sport']['team-id'].' ORDER BY `w_sport_season`.`start_year` DESC;');	
				} else {
					$teams = $dbObject->fetchAll('SELECT `w_sport_team`.`id`, `w_sport_team`.`name`, `w_sport_team`.`season`, `w_sport_team`.`logo`, `w_sport_season`.`start_year`, `w_sport_season`.`end_year` FROM `w_sport_team` LEFT JOIN `w_sport_season` ON `w_sport_team`.`season` = `w_sport_season`.`id` WHERE `w_sport_team`.`season` = '.$_SESSION['sport']['season-id'].' ORDER BY `w_sport_season`.`start_year` DESC;');
				}
			} else {
				if($_SESSION['sport']['team-id'] != '') {
					$teams = $dbObject->fetchAll('SELECT `w_sport_team`.`id`, `w_sport_team`.`name`, `w_sport_team`.`season`, `w_sport_team`.`logo`, `w_sport_season`.`start_year`, `w_sport_season`.`end_year` FROM `w_sport_team` LEFT JOIN `w_sport_season` ON `w_sport_team`.`season` = `w_sport_season`.`id` WHERE `w_sport_team`.`id` = '.$_SESSION['sport']['team-id'].' ORDER BY `w_sport_season`.`start_year` DESC;');
				} else {
					$teams = $dbObject->fetchAll('SELECT `w_sport_team`.`id`, `w_sport_team`.`name`, `w_sport_team`.`season`, `w_sport_team`.`logo`, `w_sport_season`.`start_year`, `w_sport_season`.`end_year` FROM `w_sport_team` LEFT JOIN `w_sport_season` ON `w_sport_team`.`season` = `w_sport_season`.`id` ORDER BY `w_sport_season`.`start_year` DESC;');
				}
			}
			if(count($teams) > 0) {
				$return .= ''
				.'<table class="teams-list">'
					.'<tr>'
						.'<th class="teams-list-id">'.$rb->get('teams.id').':</th>'
						.'<th class="teams-list-name">'.$rb->get('teams.name').':</th>'
						.'<th class="teams-list-logo">'.$rb->get('teams.logo').':</th>'
						.'<th class="teams-list-season">'.$rb->get('teams.season').':</th>'
						.'<th class="teams-list-edit">'.$rb->get('teams.edit').':</th>'
					.'</tr>';
				for($i = 0; $i < count($teams); $i ++) {
					$teams[$i]['logo'] = str_replace('~', '&#126', $teams[$i]['logo']);
					$return .= ''
					.'<tr class="'.((($i % 2) == 0 ) ? 'idle' : 'even').'">'
						.'<td class="teams-list-id">'.$teams[$i]['id'].'</td>'
						.'<td class="teams-list-name">'.$teams[$i]['name'].'</td>'
						.'<td class="teams-list-logo">'.$teams[$i]['logo'].'</td>'
						.'<td class="teams-list-season">'.$teams[$i]['start_year'].' - '.$teams[$i]['end_year'].'</td>'
						.'<td class="teams-list-edit">'
							.'<form name="teams-edit" method="post" action="'.$actionUrl.'">'
								.'<input type="hidden" name="team-id" value="'.$teams[$i]['id'].'" />'
								.'<input type="hidden" name="season-id" value="'.$teams[$i]['season'].'" />'
								.'<input type="hidden" name="team-edit" value="'.$rb->get('teams.edit').'" />'
								.'<input type="image" src="~/images/page_edi.png" name="team-edit" value="'.$rb->get('teams.edit').'" title="'.$rb->get('teams.editcap').'" />'
							.'</form> '
							.'<form name="teams-delete" method="post">'
								.'<input type="hidden" name="team-id" value="'.$teams[$i]['id'].'" />'
								.'<input type="hidden" name="season-id" value="'.$teams[$i]['season'].'" />'
								.'<input type="hidden" name="team-delete" value="'.$rb->get('teams.delete').'" />'
								.'<input class="confirm" type="image" src="~/images/page_del.png" name="team-delete" value="'.$rb->get('teams.delete').'" title="'.$rb->get('teams.deletecap').', id ('.$teams[$i]['id'].'), season ('.$teams[$i]['start_year'].' - '.$teams[$i]['end_year'].')" />'
							.'</form>'
						.'</td>'
					.'</tr>';
				}
			
				$return .=''
				.'</table>';
			} else {
				$return .= '<h4 class="warning">'.$rb->get('teams.warning.nodata').'</h4>';
			}
			$return .= ''
			.'<hr />'
			.'<form name="team-new" method="post" action="'.$actionUrl.'">'
				.'<input type="submit" name="team-new" value="'.$rb->get('teams.new').'" title="'.$rb->get('teams.newcap').'" /> '
				.'<span class="team-add-season">'
					.'<select name="teams-add-season-team">'
						.self::getTeamsOptions()
					.'</select> '
					.'<select name="teams-add-season-season">'
						.self::getSeasonsOptions()
					.'</select> '
					.'<input type="submit" name="team-add-season-submit" value="'.$rb->get('teams.addseasonsubmit').'" title="'.$rb->get('teams.addseasonsubmitcap').'" />'
				.'</span>'
			.'</form>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('seasons.title'), $return, "", true);
			}
		}
		
		/**
		 *
		 *	Edit team.
		 *	C tag.
		 *	
     *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
		 *
		 */		 		 		 		 		
		public function showEditTeamForm($useFrames = false, $showMsg = false) {
			global $dbObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if($_POST['team-save'] == $rb->get('teams.form.save')) {
				$seasonId = $_POST['season-id'];
				$teamId = $_POST['team-id'];
				$name = $_POST['team-edit-name'];
				$logo = $_POST['team-edit-logo'];
				$season = $_POST['team-edit-season'];
				$seasql = $dbObject->fetchAll('SELECT `id` FROM `w_sport_team` WHERE `id` = '.$teamId.' AND `season` = '.$seasonId.';');
				if(count($seasql) > 0) {
					$dbObject->execute('UPDATE `w_sport_team` SET `name` = '.$name.', `logo` = '.$logo.', `season` = '.$season.' WHERE `id` = '.$teamId.' AND `season` = '.$seasonId.';');
				} else {
					$dbObject->execute('INSERT INTO `w_sport_team`(`name`, `logo`, `season`) VALUES ("'.$name.'", "'.$logo.'", '.$season.');');
					$tea = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `w_sport_team`;');
					$dbObject->execute('INSERT INTO `w_sport_table`(`team`, `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season`) VALUES('.$tea[0]['id'].', 0, 0, 0, 0, 0, 0, 0, '.$season.');');
				}
			}
			
			if($_POST['team-new'] == $rb->get('teams.new') || $_POST['team-edit'] == $rb->get('teams.edit')) {
				if($_POST['team-edit'] == $rb->get('teams.edit')) {
					$seasonId = $_POST['season-id'];
					$teamId = $_POST['team-id'];
					$team = $dbObject->fetchAll('SELECT `id`, `name`, `logo`, `season` FROM `w_sport_team` WHERE `season` = '.$seasonId.' AND `id` = '.$teamId.';');
					$team = $team[0];
					$team['logo'] = str_replace('~', '&#126', $team['logo']);
				} else {
					$team = array();
				}
				
				$seasons = self::getSeasonsOptions($teamId, $seasonId);
				
				$return .= ''
				.'<div class="team-edit-form">'
					.'<form name="team-edit-form" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
						.'<div class="team-edit-name">'
							.'<label for="team-edit-name">'.$rb->get('teams.name').'</label> '
							.'<input type="text" name="team-edit-name" id="team-edit-name" value="'.$team['name'].'" />'
						.'</div>'
						.'<div class="team-edit-logo">'
							.'<label for="team-edit-logo">'.$rb->get('teams.logo').'</label> '
							.'<input type="text" name="team-edit-logo" id="team-edit-logo" value="'.$team['logo'].'" />'
						.'</div>'
						.'<div class="team-edit-season">'
							.'<label for="team-edit-season">'.$rb->get('teams.season').'</label> '
							.'<select name="team-edit-season" id="team-edit-season">'
								.$seasons
							.'</select>'
						.'</div>'
						.'<div class="team-edit-submit">'
							.'<input type="hidden" name="season-id" value="'.$team['season'].'" />'
							.'<input type="hidden" name="team-id" value="'.$team['id'].'" />'
							.'<input type="submit" name="team-save" value="'.$rb->get('teams.form.save').'" />'
						.'</div>'
					.'</form>'
				.'</div>'
				.'<div class="clear"></div>';
				
				if($useFrames == "false") {
					return $return;
				} else {
					return parent::getFrame($rb->get('teams.form.title'), $return, "", true);
				}
			}
		}
    
    /**
     *
     *	List of players for editing.
     *	C tag.
     *	
     *	@param		pageId					next page id
     *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
     *
     */		 		 		 		     
    public function showEditPlayers($pageId = false, $useFrames = false, $showMsg = false) {
    	global $dbObject;
    	global $webObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$retrun = '';
			$actionUrl = $_SERVER['REDIRECT_URL'];
			
			if($pageId != false) {
				$url = $webObject->composeUrl($pageId);
			} else {
				$url = '';
			}
			
			if($_POST['player-delete'] == $rb->get('players.delete')) {
				$seasonId = $_POST['season-id'];
				$playerId = $_POST['player-id'];
				$teamId = $_POST['team-id'];
				
				$dbObject->execute('DELETE FROM `w_sport_player` WHERE `id` = '.$playerId.' AND `team` = '.$teamId.' AND `season` = '.$seasonId.';');
				$return .= '<h4 class="success">'.$rb->get('players.success.delete').'</h4>';
			} elseif($_POST['player-deletewhole'] == $rb->get('players.deletewhole')) {
				$playerId = $_POST['player-id'];
				
				$dbObject->execute('DELETE FROM `w_sport_player` WHERE `id` = '.$playerId.';');
				$return .= '<h4 class="success">'.$rb->get('players.success.deletewhole').'</h4>';
			}
			
			if($_SESSION['sport']['season-id'] != '') {
				if($_SESSION['sport']['team-id'] != '') {
					$players = $dbObject->fetchAll('SELECT DISTINCT `w_sport_player`.`id`, `w_sport_player`.`name`, `w_sport_player`.`surname` FROM `w_sport_player` WHERE `w_sport_player`.`season` = '.$_SESSION['sport']['season-id'].' AND `w_sport_player`.`team` = '.$_SESSION['sport']['team-id'].' ORDER BY `w_sport_player`.`id` ASC;');
				} else {
					$players = $dbObject->fetchAll('SELECT DISTINCT `w_sport_player`.`id`, `w_sport_player`.`name`, `w_sport_player`.`surname` FROM `w_sport_player` WHERE `w_sport_player`.`season` = '.$_SESSION['sport']['season-id'].' ORDER BY `w_sport_player`.`id` ASC;');
				}
			} else {
				if($_SESSION['sport']['team-id'] != '') {
					$players = $dbObject->fetchAll('SELECT DISTINCT `w_sport_player`.`id`, `w_sport_player`.`name`, `w_sport_player`.`surname` FROM `w_sport_player` WHERE `w_sport_player`.`team` = '.$_SESSION['sport']['team-id'].' ORDER BY `w_sport_player`.`id` ASC;');
				} else {
					$players = $dbObject->fetchAll('SELECT DISTINCT `w_sport_player`.`id`, `w_sport_player`.`name`, `w_sport_player`.`surname` FROM `w_sport_player` ORDER BY `w_sport_player`.`id` ASC;');
				}
				//$players = $dbObject->fetchAll('SELECT DISTINCT `w_sport_player`.`id`, `w_sport_player`.`name`, `w_sport_player`.`surname` FROM `w_sport_player` ORDER BY `w_sport_player`.`id` ASC;');
			}
			if(count($players) > 0) {
				$return .= ''
				.'<table class="players-list">'
					.'<tr>'
						.'<th class="players-list-id">'.$rb->get('players.id').'</th>'
						.'<th class="players-list-name">'.$rb->get('players.name').'</th>'
						.'<th class="players-list-surname">'.$rb->get('players.surname').'</th>'
						.'<th class="players-list-teasea">'.$rb->get('players.season').' / '.$rb->get('players.team').'</th>'
					.'</tr>';
				
				$i = 1;
				foreach($players as $pl) {
					if($_SESSION['sport']['season-id'] != '') {
						$seasons = $dbObject->fetchAll('SELECT DISTINCT `w_sport_team`.`id` AS `tid`, `w_sport_team`.`name`, `w_sport_season`.`id` AS `sid`, `w_sport_season`.`start_year`, `w_sport_season`.`end_year` FROM `w_sport_player` LEFT JOIN `w_sport_season` ON `w_sport_player`.`season` = `w_sport_season`.`id` LEFT JOIN `w_sport_team` ON `w_sport_player`.`team` = `w_sport_team`.`id` WHERE `w_sport_player`.`id` = '.$pl['id'].' AND `w_sport_player`.`season` = '.$_SESSION['sport']['season-id'].' ORDER BY `w_sport_season`.`start_year` DESC;');
					} else {
						$seasons = $dbObject->fetchAll('SELECT DISTINCT `w_sport_team`.`id` AS `tid`, `w_sport_team`.`name`, `w_sport_season`.`id` AS `sid`, `w_sport_season`.`start_year`, `w_sport_season`.`end_year` FROM `w_sport_player` LEFT JOIN `w_sport_season` ON `w_sport_player`.`season` = `w_sport_season`.`id` LEFT JOIN `w_sport_team` ON `w_sport_player`.`team` = `w_sport_team`.`id` WHERE `w_sport_player`.`id` = '.$pl['id'].' ORDER BY `w_sport_season`.`start_year` DESC;');
					}
					
					$teaseastr = '';
					foreach($seasons as $sea) {
						if(strlen($teaseastr) != 0) {
							$teaseastr .= ', ';
						}
						$teaseastr .= '('.$sea['start_year'].' - '.$sea['end_year'].' / '.$sea['name'].' - '
						.'<form name="player-edit" method="post" action="'.$actionUrl.'">'
							.'<input type="hidden" name="player-id" value="'.$pl['id'].'" />'
							.'<input type="hidden" name="season-id" value="'.$sea['sid'].'" />'
							.'<input type="hidden" name="team-id" value="'.$sea['tid'].'" />'
							.'<input type="hidden" name="player-edit" value="'.$rb->get('players.edit').'" />'
							.'<input type="image" src="~/images/page_edi.png" name="player-edit" value="'.$rb->get('players.edit').'" title="'.$rb->get('players.editcap').'" />'
						.'</form> '
						.'<form name="player-delete" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
							.'<input type="hidden" name="player-id" value="'.$pl['id'].'" />'
							.'<input type="hidden" name="season-id" value="'.$sea['sid'].'" />'
							.'<input type="hidden" name="team-id" value="'.$sea['tid'].'" />'
							.'<input type="hidden" name="player-delete" value="'.$rb->get('players.delete').'" />'
							.'<input class="confirm" type="image" src="~/images/page_del.png" name="player-delete" value="'.$rb->get('players.delete').'" title="'.$rb->get('players.deletecap').', id ('.$pl['id'].'), season ('.$sea['sid'].'), team ('.$sea['tid'].')" />'
						.'</form> )';
					}
			
					$teaseastr .= ''
					.' <form name="player-add" method="post" action="'.$actionUrl.'">'
						.'<input type="hidden" name="player-id" value="'.$pl['id'].'" />'
						.'<input type="hidden" name="player-add" value="'.$rb->get('players.add').'" />'
						.'<input type="image" src="~/images/page_add.png" name="player-add" value="'.$rb->get('players.add').'" title="'.$rb->get('players.addcap').'" />'
					.'</form> '
					.'<form name="player-deletewhole" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
						.'<input type="hidden" name="player-id" value="'.$pl['id'].'" />'
						.'<input type="hidden" name="player-deletewhole" value="'.$rb->get('players.deletewhole').'" />'
						.'<input class="confirm" type="image" src="~/images/page_del.png" name="player-deletewhole" value="'.$rb->get('players.deletewhole').'" title="'.$rb->get('players.deletewholecap').', id ('.$pl['id'].')" />'
					.'</form> ';
				
					$return .= ''
					.'<tr class="'.((($i % 2) == 0) ? 'idle' : 'even').'">'
						.'<td class="players-list-id">'.$pl['id'].'</td>'
						.'<td class="players-list-name">'.$pl['name'].'</td>'
						.'<td class="players-list-surname">'.$pl['surname'].'</td>'
						.'<td class="players-list-teasea">'.$teaseastr.'</td>'
					.'</tr>';
					$i ++;
				}
			
				$return .= ''
				.'</table>';
			} else {
				$return .= '<h4 class="warning">'.$rb->get('players.warning.nodata').'</h4>';
			}
			$return .= ''
			.'<hr />'
			.'<form name="player-deletewhole" method="post" action="'.$actionUrl.'">'
				.'<input type="submit" name="player-new" value="'.$rb->get('players.new').'" title="'.$rb->get('players.newcap').'" />'
			.'</form>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('players.title'), $return, "", true);
			}
		}
		
		/**
		 *
		 *	Edit player.
		 *	C tag.
		 *	
     *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
		 *
		 */		 		 		 		 		
		public function showEditPlayerForm($useFrames = false, $showMsg = false) {
			global $dbObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$ok = true;
			$player = array();
			$return = '';
			
			if($_POST['player-save'] == $rb->get('player.form.save')) {
				$seasonId = $_POST['season-id'];
				$playerId = $_POST['player-id'];
				$teamId = $_POST['team-id'];
				
				$player['name'] = $_POST['player-edit-name'];
				$player['surname'] = $_POST['player-edit-surname'];
				$player['birthyear'] = $_POST['player-edit-birthyear'];
				$player['number'] = $_POST['player-edit-number'];
				$player['position'] = $_POST['player-edit-position'];
				$player['photo'] = $_POST['player-edit-photo'];
				$player['season'] = $_POST['player-edit-season'];
				$player['team'] = $_POST['player-edit-team'];
				
				if(strlen(trim($player['name'])) == 0) {
					$return .= '<h4 class="error">'.$rb->get('player.form.error.name').'</h4>';
					$ok = false;
				}
				if(strlen(trim($player['surname'])) == 0) {
					$return .= '<h4 class="error">'.$rb->get('player.form.error.surname').'</h4>';
					$ok = false;
				}
				
				if($ok) {
					$pl = $dbObject->fetchAll('SELECT `id` FROM `w_sport_player` WHERE `id` = '.$playerId.' AND `season` = '.$seasonId.' AND `team` = '.$teamId.';');
					if(count($pl) == 1) {
						$dbObject->execute('UPDATE `w_sport_player` SET `name` = "'.$player['name'].'", `surname` = "'.$player['surname'].'", `birthyear` = '.$player['birthyear'].', `number` = '.$player['number'].', `photo` = "'.$player['photo'].'", `season` = '.$player['season'].', `team` = '.$player['team'].' WHERE `id` = '.$playerId.' AND `season` = '.$seasonId.' AND `team` = '.$teamId.';');
					} else {
						if($playerId == '') {
							$dbObject->execute('INSERT INTO `w_sport_player`(`name`, `surname`, `birthyear`, `number`, `position`, `photo`, `season`, `team`) VALUES ("'.$player['name'].'", "'.$player['surname'].'", '.$player['birthyear'].', '.$player['number'].', '.$player['position'].', "'.$player['photo'].'", '.$player['season'].', '.$player['team'].');');
						} else {
							$dbObject->execute('INSERT INTO `w_sport_player`(`id`, `name`, `surname`, `birthyear`, `number`, `position`, `photo`, `season`, `team`) VALUES ('.$playerId.', "'.$player['name'].'", "'.$player['surname'].'", '.$player['birthyear'].', '.$player['number'].', '.$player['position'].', "'.$player['photo'].'", '.$player['season'].', '.$player['team'].');');
						}
					}
				}
			}
			
			if($ok == false || $_POST['player-edit'] == $rb->get('players.edit') || $_POST['player-new'] == $rb->get('players.new') || $_POST['player-add'] ==  $rb->get('players.add')) {
				//$return .= '<h4 class="warning">Coming soon ...</h4>';
				
				if($_POST['player-edit'] == $rb->get('players.edit')) {
					$seasonId = $_POST['season-id'];
					$playerId = $_POST['player-id'];
					$teamId = $_POST['team-id'];
					$player = $dbObject->fetchAll('SELECT `name`, `surname`, `birthyear`, `number`, `position`, `photo`, `season`, `team` FROM `w_sport_player` WHERE `id` = '.$playerId.' AND `team` = '.$teamId.' AND `season` = '.$seasonId.';');
					$player = $player[0];
					$player['photo'] = str_replace('~', '&#126', $player['photo']);
				} elseif ($_POST['player-add'] ==  $rb->get('players.add')) {
					$playerId = $_POST['player-id'];
					$player = $dbObject->fetchAll('SELECT `name`, `surname`, `birthyear`, `number`, `position`, `photo` FROM `w_sport_player` WHERE `id` = '.$playerId.' ORDER BY `season` DESC;');
					$player = $player[0];
				}
				
				$return .= ''
				.'<div class="player-edit-form">'
					.'<form name="player-edit-form" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
						.'<div class="player-edit-name">'
							.'<label for="player-edit-name">'.$rb->get('players.form.name').'</label>'
							.'<input type="text" name="player-edit-name" id="player-edit-name" value="'.$player['name'].'" />'
						.'</div>'
						.'<div class="player-edit-surname">'
							.'<label for="player-edit-surname">'.$rb->get('players.form.surname').'</label>'
							.'<input type="text" name="player-edit-surname" id="player-edit-surname" value="'.$player['surname'].'" />'
						.'</div>'
						.'<div class="player-edit-birthyear">'
							.'<label for="player-edit-birthyear">'.$rb->get('players.form.birthyear').'</label>'
							.'<input type="text" name="player-edit-birthyear" id="player-edit-birthyear" value="'.$player['birthyear'].'" />'
						.'</div>'
						.'<div class="player-edit-number">'
							.'<label for="player-edit-number">'.$rb->get('players.form.number').'</label>'
							.'<input type="text" name="player-edit-number" id="player-edit-number" value="'.$player['number'].'" />'
						.'</div>'
						.'<div class="player-edit-position">'
							.'<label for="player-edit-position">'.$rb->get('players.form.position').'</label>'
							.'<input type="text" name="player-edit-position" id="player-edit-position" value="'.$player['position'].'" />'
						.'</div>'
						.'<div class="player-edit-photo">'
							.'<label for="player-edit-photo">'.$rb->get('players.form.photo').'</label>'
							.'<input type="text" name="player-edit-photo" id="player-edit-photo" value="'.$player['photo'].'" />'
						.'</div>'
						.'<div class="player-edit-season">'
							.'<label for="player-edit-season">'.$rb->get('players.form.season').'</label>'
							.'<select name="player-edit-season" id="player-edit-season">'
								.self::getSeasonsOptions(0, 0, $seasonId)
							.'</select>'
						.'</div>'
						.'<div class="player-edit-team">'
							.'<label for="player-edit-team">'.$rb->get('players.form.team').'</label>'
							.'<select name="player-edit-team" id="player-edit-team">'
								.self::getTeamsOptions($teamId)
							.'</select>'
						.'</div>'
						.'<div class="player-edt-submit">'
							.'<input type="hidden" name="player-id" value="'.$playerId.'" />'
							.'<input type="hidden" name="season-id" value="'.$seasonId.'" />'
							.'<input type="hidden" name="team-id" value="'.$teamId.'" />'
							.'<input type="submit" name="player-save" value="'.$rb->get('player.form.save').'" />'
						.'</div>'
					.'</form>'
				.'</div>'
				.'<div class="clear"></div>';
				
				if($useFrames == "false") {
					return $return;
				} else {
					return parent::getFrame($rb->get('players.form.title'), $return, "", true);
				}
			}
		}
		
		/**
     *
     *	List of matches for editing.
     *	C tag.
     *	
     *	@param		pageId					next page id
     *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
     *
     */	
		public function showEditMatches($pageId = false, $useFrames = false, $showMsg = false) {
    	global $dbObject;
    	global $webObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$retrun = '';
			
			if($pageId != false) {
				$url = $webObject->composeUrl($pageId);
			} else {
				$url = '';
			}
			
			if($_POST['match-delete'] == $rb->get('matches.delete')) {
				$match['id'] = $_POST['match-id'];
				$match['season'] = $_SESSION['sport']['season-id'];
				$tmpma = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `comment`, `round`, `in_table`, `season` FROM `w_sport_match` WHERE `id` = '.$match['id'].' AND `season` = '.$match['season'].';');
				if(count($tmpma) != 0) {
					if($tmpma[0]['in_table'] == 1) {
						$team1 = $dbObject->fetchAll('SELECT `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points` FROM `w_sport_table` WHERE `team` = '.$tmpma[0]['h_team'].' AND `season` = '.$match['season'].';');
						$team2 = $dbObject->fetchAll('SELECT `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points` FROM `w_sport_table` WHERE `team` = '.$tmpma[0]['a_team'].' AND `season` = '.$match['season'].';');
						if(count($team1) > 0 && count($team2) > 0) {
							$team1 = $team1[0];
							$team2 = $team2[0];
							$team1['matches'] --;
							$team2['matches'] --;
							if($tmpma[0]['h_score'] > $tmpma[0]['a_score']) {
								$team1['wins'] --;
								$team2['loses'] --;
								$team1['points'] -= 3;
							} elseif($tmpma[0]['a_score'] > $tmpma[0]['h_score']) {
								$team2['wins'] --;
								$team1['loses'] --;
								$team2['points'] -= 3;
							} elseif($tmpma[0]['h_score'] == $tmpma[0]['a_score'] && $tmpma[0]['h_extratime'] == 1) {
								$team1['draws'] --;
								$team2['draws'] --;
								$team1['points'] -= 2;
								$team2['points'] --;
							} elseif($tmpma[0]['h_score'] == $tmpma[0]['a_score'] && $tmpma[0]['a_extratime'] == 1) {
								$team1['draws'] --;
								$team2['draws'] --;
								$team2['points'] -= 2;
								$team1['points'] --;
							} else {
								$team1['draws'] --;
								$team2['draws'] --;
								$team1['points'] --;
								$team2['points'] --;
							}
							$team1['s_score'] -= $tmpma[0]['h_score'];
							$team1['r_score'] -= $tmpma[0]['a_score'];
							$team2['s_score'] -= $tmpma[0]['a_score'];
							$team2['r_score'] -= $tmpma[0]['h_score'];
					
							$dbObject->execute('UPDATE `w_sport_table` SET `matches` = '.$team1['matches'].', `wins` = '.$team1['wins'].', `draws` = '.$team1['draws'].', `loses` = '.$team1['loses'].', `s_score` = '.$team1['s_score'].', `r_score` = '.$team1['r_score'].', `points` = '.$team1['points'].' WHERE `team` = '.$tmpma[0]['h_team'].' AND `season` = '.$match['season'].';');
							$dbObject->execute('UPDATE `w_sport_table` SET `matches` = '.$team2['matches'].', `wins` = '.$team2['wins'].', `draws` = '.$team2['draws'].', `loses` = '.$team2['loses'].', `s_score` = '.$team2['s_score'].', `r_score` = '.$team2['r_score'].', `points` = '.$team2['points'].' WHERE `team` = '.$tmpma[0]['a_team'].' AND `season` = '.$match['season'].';');
						} else {
							$return .= '<h4 class="error">'.$rb->get('match.warning.teamsnotintable').'</h4>';
						}
					}
					$dbObject->execute('DELETE FROM `w_sport_match` WHERE `id` = '.$match['id'].' AND `season` = '.$match['season'].';');
					$dbObject->execute('DELETE FROM `w_sport_stats` WHERE `mid` = '.$match['id'].' AND `season` = '.$match['season'].';');
					$return .= '<h4 class="success">'.$rb->get('matches.success.deleted').'</h4>';
				} else {
					$return .= '<h4 class="error">'.$rb->get('matches.error.deletingerror').'</h4>';
				}
			}
			
			if($_POST['match-stats-delete'] == $rb->get('matches.stats.delete')) {
				$matchId = $_POST['match-id'];
				$seasonId = $_SESSION['sport']['season-id'];
				$dbObject->execute('DELETE FROM `w_sport_stats` WHERE `mid` = '.$matchId.' AND `season` = '.$seasonId.';');
			}
			
			if($_SESSION['sport']['season-id'] != '') {
				if($_SESSION['sport']['team-id'] != '') {
					$matches = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `round`, `season` FROM `w_sport_match` WHERE `season` = '.$_SESSION['sport']['season-id'].' AND (`w_sport_match`.`h_team` = '.$_SESSION['sport']['team-id'].' OR `w_sport_match`.`a_team` = '.$_SESSION['sport']['team-id'].') ORDER BY `round`, `id` DESC;');
				} else {
					$matches = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `round`, `season` FROM `w_sport_match` WHERE `season` = '.$_SESSION['sport']['season-id'].' ORDER BY `round`, `id` DESC;');
				}
				
				if(count($matches) > 0) {
					$return .= ''
					.'<table class="matches-table">'
						.'<tr>'
							.'<th class="matches-table-id">'.$rb->get('matches.id').':</th>'
							.'<th class="matches-table-round">'.$rb->get('matches.round').':</th>'
							.'<th class="matches-table-home">'.$rb->get('matches.hteam').':</th>'
							.'<th class="matches-table-away">'.$rb->get('matches.ateam').':</th>'
							.'<th class="matches-table-score">'.$rb->get('matches.score').':</th>'
							.'<th class="matches-table-shoots">'.$rb->get('matches.shoots').':</th>'
							.'<th class="matches-table-pentalty">'.$rb->get('matches.penalty').':</th>'
							.'<th class="matches-table-extratime">'.$rb->get('matches.extratime').':</th>'
							.'<th class="matches-table-edit">'.$rb->get('matches.edit').':</th>'
						.'</tr>';
					
					$i = 1;
					foreach($matches as $match) {
						$home = $dbObject->fetchAll('SELECT `name` FROM `w_sport_team` WHERE `id` = '.$match['h_team'].';');
						$away = $dbObject->fetchAll('SELECT `name` FROM `w_sport_team` WHERE `id` = '.$match['a_team'].';');
						$stats = $dbObject->fetchAll('SELECT `pid` FROM `w_sport_stats` WHERE `mid` = '.$match['id'].' AND `season` = '.$match['season'].';');
						$extime = ($match['h_extratime'] == 1) ? $rb->get('matches.form.homeexwin') : (($match['a_extratime'] == 1) ? $rb->get('matches.form.awayexwin') : '');
						$return .= ''
						.'<tr class="'.((($i % 2) == 1) ? 'idle' : 'even').'">'
							.'<td class="matches-table-id">'.$match['id'].'</td>'
							.'<td class="matches-table-round">'.$match['round'].'</td>'
							.'<td class="matches-table-home">'.$home[0]['name'].'</td>'
							.'<td class="matches-table-away">'.$away[0]['name'].'</td>'
							.'<td class="matches-table-score">'.$match['h_score'].' : '.$match['a_score'].'</td>'
							.'<td class="matches-table-shoots">'.$match['h_shoots'].' : '.$match['a_shoots'].'</td>'
							.'<td class="matches-table-pentalty">'.$match['h_penalty'].' : '.$match['a_penalty'].'</td>'
							.'<td class="matches-table-extratime">'.$extime.'</td>'
							.'<td class="matches-table-edit">'
								.'<form name="matches-edit" method="post" action="'.$actionUrl.'">'
									.'<input type="hidden" name="match-id" value="'.$match['id'].'" />'
									.'<input type="hidden" name="match-edit" value="'.$rb->get('matches.edit').'" />'
									.'<input type="image" src="~/images/page_edi.png" name="match-edit" value="'.$rb->get('matches.edit').'" title="'.$rb->get('matches.editcap').'" />'
								.'</form> -'
								.((count($stats) == 0) ? ''
								.' <form name="matches-stats-add" method="post" action="'.$actionUrl.'">'
									.'<input type="hidden" name="match-id" value="'.$match['id'].'" />'
									.'<input type="hidden" name="match-stats-add" value="'.$rb->get('matches.statsadd').'" />'
									.'<input type="image" src="~/images/page_add.png" name="match-stats-add" value="'.$rb->get('matches.statsadd').'" title="'.$rb->get('matches.statsaddcap').'" />'
								.'</form> -'
								: ''
								.' <form name="matches-stats-edit" method="post" action="'.$actionUrl.'">'
									.'<input type="hidden" name="match-id" value="'.$match['id'].'" />'
									.'<input type="hidden" name="match-stats-edit" value="'.$rb->get('matches.statsedit').'" />'
									.'<input type="image" src="~/images/page_edi.png" name="match-stats-edit" value="'.$rb->get('matches.statsedit').'" title="'.$rb->get('matches.statseditcap').'" />'
								.'</form> '
								.' <form name="matches-stats-delete" method="post" action="'.$actionUrl.'">'
									.'<input type="hidden" name="match-id" value="'.$match['id'].'" />'
									.'<input type="hidden" name="match-stats-delete" value="'.$rb->get('matches.stats.delete').'" />'
									.'<input class="confirm" type="image" src="~/images/page_del.png" name="match-stats-delete" value="'.$rb->get('matches.stats.delete').'" title="'.$rb->get('matches.stats.deletecap').', id('.$match['id'].')" />'
								.'</form> -'
								)
								.' <form name="matches-delete" method="post" action="'.$actionUrl.'">'
									.'<input type="hidden" name="match-id" value="'.$match['id'].'" />'
									.'<input type="hidden" name="match-delete" value="'.$rb->get('matches.delete').'" />'
									.'<input class="confirm" type="image" src="~/images/page_del.png" name="match-delete" value="'.$rb->get('matches.delete').'" title="'.$rb->get('matches.deletecap').', id('.$match['id'].')" />'
								.'</form>'
							.'</td>'
						.'</tr>';
						$i ++;
					}
				} else {
					$return .= '<h4 class="warning">'.$rb->get('matches.warning.nodata').'</h4>';
				}
			} else {
				$return .= '<h4 class="error">'.$rb->get('season.error.notset').'</h4>';
			}
			
			$return .= ''
			.'</table>'
			.'<hr />'
			.'<form name="match-new" method="post" action="'.$actionUrl.'">'
				.'<input type="submit" name="match-new" value="'.$rb->get('matches.new').'" title="'.$rb->get('matches.newcap').'" />'
			.'</form>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('matches.title'), $return, "", true);
			}
		}
		
		/**
		 *
		 *	Edit match.
		 *	C tag.
		 *	
     *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
		 *
		 */		 		 		 		 		
		public function showEditMatchForm($useFrames = false, $showMsg = false) {
			global $dbObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$match = array();
			$return = '';
			
			if($_POST['match-edit-save'] == $rb->get('matches.save')) {
				$match = array();
				$match['id'] = $_POST['match-id'];
				$match['h_team'] = $_POST['match-edit-hteam'];
				$match['h_score'] = $_POST['match-edit-hscore'];
				$match['h_shoots'] = $_POST['match-edit-hshoots'];
				$match['h_penalty'] = $_POST['match-edit-hpenalty'];
				$match['h_extratime'] = ($_POST['match-edit-hextratime'] == 'on') ? 1 : 0;
				$match['a_team'] = $_POST['match-edit-ateam'];
				$match['a_score'] = $_POST['match-edit-ascore'];
				$match['a_shoots'] = $_POST['match-edit-ashoots'];
				$match['a_penalty'] = $_POST['match-edit-apenalty'];
				$match['a_extratime'] = ($_POST['match-edit-aextratime'] == 'on') ? 1 : 0;
				$match['in_table'] = $_POST['match-edit-in-table'];
				$match['comment'] = $_POST['match-edit-comment'];
				$match['round'] = $_POST['match-edit-round'];
				$match['season'] = $_SESSION['sport']['season-id'];
				
				$ok = true;
				if($match['h_team'] == $match['a_team']) {
					$ok = false;
					$return .= '<h4 class="error">'.$rb->get('match.error.sameteams').'</h4>';
				}
				if(!is_numeric($match['h_score']) || !is_numeric($match['a_score']) || !is_numeric($match['h_shoots']) || !is_numeric($match['a_shoots']) || !is_numeric($match['h_penalty']) || !is_numeric($match['a_penalty']) || !is_numeric($match['round'])) {
					$ok = false;
					$return .= '<h4 class="error">'.$rb->get('match.error.isnotnumber').'</h4>';
					if($match['h_score'] < 1 || $match['a_score'] < 1 || $match['h_shoots'] < 1 || $match['a_shoots'] < 1 || $match['h_penalty'] < 1 || $match['a_penalty'] < 1 || $match['round'] < 1) {
						$return .= '<h4 class="error">'.$rb->get('match.error.islessthanone').'</h4>';
					}
				}
				if($match['h_extratime'] == 1 && $match['a_extratime'] == 1) {
					$ok = false;
					$return .= '<h4 class="error">'.$rb->get('match.error.bothexwin').'</h4>';
				}
				if(($match['h_extratime'] == 1 || $match['a_extratime'] == 1) && $match['h_score'] != $match['a_score']) {
					$ok = false;
					$return .= '<h4 class="error">'.$rb->get('match.error.exwinsamescore').'</h4>';
				}
				
				if($ok) {
					$tmpma = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `comment`, `round`, `in_table`, `season` FROM `w_sport_match` WHERE `id` = '.$match['id'].' AND `season` = '.$match['season'].';');
					if(count($tmpma) != 0) {
						if($tmpma[0]['in_table'] != 0) {
							$team1 = $dbObject->fetchAll('SELECT `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `table_id` FROM `w_sport_table` WHERE `team` = '.$tmpma[0]['h_team'].' AND `season` = '.$match['season'].' AND `table_id` = '.$tmpma[0]['in_table'] .';');
							$team2 = $dbObject->fetchAll('SELECT `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `table_id` FROM `w_sport_table` WHERE `team` = '.$tmpma[0]['a_team'].' AND `season` = '.$match['season'].' AND `table_id` = '.$tmpma[0]['in_table'] .';');
							if(count($team1) > 0 && count($team2) > 0) {
								$team1 = $team1[0];
								$team2 = $team2[0];
								$team1['matches'] --;
								$team2['matches'] --;
								if($tmpma[0]['h_score'] > $tmpma[0]['a_score']) {
									$team1['wins'] --;
									$team2['loses'] --;
									$team1['points'] -= 3;
								} elseif($tmpma[0]['a_score'] > $tmpma[0]['h_score']) {
									$team2['wins'] --;
									$team1['loses'] --;
									$team2['points'] -= 3;
								} elseif($tmpma[0]['h_score'] == $tmpma[0]['a_score'] && $tmpma[0]['h_extratime'] == 1) {
									$team1['draws'] --;
									$team2['draws'] --;
									$team1['points'] -= 2;
									$team2['points'] --;
								} elseif($tmpma[0]['h_score'] == $tmpma[0]['a_score'] && $tmpma[0]['a_extratime'] == 1) {
									$team1['draws'] --;
									$team2['draws'] --;
									$team2['points'] -= 2;
									$team1['points'] --;
								} else {
									$team1['draws'] --;
									$team2['draws'] --;
									$team1['points'] --;
									$team2['points'] --;
								}
								$team1['s_score'] -= $tmpma[0]['h_score'];
								$team1['r_score'] -= $tmpma[0]['a_score'];
								$team2['s_score'] -= $tmpma[0]['a_score'];
								$team2['r_score'] -= $tmpma[0]['h_score'];
						
								$dbObject->execute('UPDATE `w_sport_table` SET `matches` = '.$team1['matches'].', `wins` = '.$team1['wins'].', `draws` = '.$team1['draws'].', `loses` = '.$team1['loses'].', `s_score` = '.$team1['s_score'].', `r_score` = '.$team1['r_score'].', `points` = '.$team1['points'].' WHERE `team` = '.$tmpma[0]['h_team'].' AND `season` = '.$match['season'].' AND `table_id` = '.$team1['table_id'].';');
								$dbObject->execute('UPDATE `w_sport_table` SET `matches` = '.$team2['matches'].', `wins` = '.$team2['wins'].', `draws` = '.$team2['draws'].', `loses` = '.$team2['loses'].', `s_score` = '.$team2['s_score'].', `r_score` = '.$team2['r_score'].', `points` = '.$team2['points'].' WHERE `team` = '.$tmpma[0]['a_team'].' AND `season` = '.$match['season'].' AND `table_id` = '.$team2['table_id'].';');
							} else {
								$return .= '<h4 class="error">'.$rb->get('match.error.teamsnotintable').'</h4>';
							}
						}
						$dbObject->execute('UPDATE `w_sport_match` SET `h_team` = '.$match['h_team'].', `a_team` = '.$match['a_team'].', `h_score` = '.$match['h_score'].', `a_score` = '.$match['a_score'].', `h_shoots` = '.$match['h_shoots'].', `a_shoots` = '.$match['a_shoots'].', `h_penalty` = '.$match['h_penalty'].', `a_penalty` = '.$match['a_penalty'].', `h_extratime` = '.$match['h_extratime'].', `a_extratime` = '.$match['a_extratime'].', `comment` = "'.$match['comment'].'", `round` = '.$match['round'].', `in_table` = '.$match['in_table'].', `season` = '.$match['season'].' WHERE `id` = '.$match['id'].';');
					} else {
						$dbObject->execute('INSERT INTO `w_sport_match`(`h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `comment`, `round`, `in_table`, `season`) VALUES ('.$match['h_team'].', '.$match['a_team'].', '.$match['h_score'].', '.$match['a_score'].', '.$match['h_shoots'].', '.$match['a_shoots'].', '.$match['h_penalty'].', '.$match['a_penalty'].', '.$match['h_extratime'].', '.$match['a_extratime'].', "'.$match['comment'].'", '.$match['round'].', '.$match['in_table'].', '.$match['season'].');');
					}
					if($match['in_table'] != 0) {
						$team1 = $dbObject->fetchAll('SELECT `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points` FROM `w_sport_table` WHERE `team` = '.$match['h_team'].' AND `season` = '.$match['season'].' AND `table_id` = '.$match['in_table'].';');
						$team2 = $dbObject->fetchAll('SELECT `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points` FROM `w_sport_table` WHERE `team` = '.$match['a_team'].' AND `season` = '.$match['season'].' AND `table_id` = '.$match['in_table'].';');
						if(count($team1) <= 0 || count($team2) <= 0) {
							$dbObject->execute('INSERT INTO `w_sport_table`(`team`, `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season`, `table_id`) VALUES ('.$match['h_team'].', 0, 0, 0, 0, 0, 0, 0, '.$match['season'].', '.$match['in_table'].');');
							$dbObject->execute('INSERT INTO `w_sport_table`(`team`, `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season`, `table_id`) VALUES ('.$match['a_team'].', 0, 0, 0, 0, 0, 0, 0, '.$match['season'].', '.$match['in_table'].');');
							$team1 = $dbObject->fetchAll('SELECT `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points` FROM `w_sport_table` WHERE `team` = '.$match['h_team'].' AND `season` = '.$match['season'].' AND `table_id` = '.$match['in_table'].';');
							$team2 = $dbObject->fetchAll('SELECT `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points` FROM `w_sport_table` WHERE `team` = '.$match['a_team'].' AND `season` = '.$match['season'].' AND `table_id` = '.$match['in_table'].';');
							//$return .= '<h4 class="errpr">'.$rb->get('match.warning.teamsnotintable').'</h4>';
						}
						$team1 = $team1[0];
						$team2 = $team2[0];
						$team1['matches'] ++;
						$team2['matches'] ++;
						if($match['h_score'] > $match['a_score']) {
							$team1['wins'] ++;
							$team2['loses'] ++;
							$team1['points'] += 3;
						} elseif($match['a_score'] > $match['h_score']) {
							$team2['wins'] ++;
							$team1['loses'] ++;
							$team2['points'] += 3;
						} elseif($match['h_score'] == $match['a_score'] && $match['h_extratime'] == 1) {
							$team1['draws'] ++;
							$team2['draws'] ++;
							$team1['points'] += 2;
							$team2['points'] ++;
						} elseif($match['h_score'] == $match['a_score'] && $match['a_extratime'] == 1) {
							$team1['draws'] ++;
							$team2['draws'] ++;
							$team2['points'] += 2;
							$team1['points'] ++;
						} else {
							$team1['draws'] ++;
							$team2['draws'] ++;
							$team1['points'] ++;
							$team2['points'] ++;
						}
						$team1['s_score'] += $match['h_score'];
						$team1['r_score'] += $match['a_score'];
						$team2['s_score'] += $match['a_score'];
						$team2['r_score'] += $match['h_score'];
					
						$dbObject->execute('UPDATE `w_sport_table` SET `matches` = '.$team1['matches'].', `wins` = '.$team1['wins'].', `draws` = '.$team1['draws'].', `loses` = '.$team1['loses'].', `s_score` = '.$team1['s_score'].', `r_score` = '.$team1['r_score'].', `points` = '.$team1['points'].' WHERE `team` = '.$match['h_team'].' AND `season` = '.$match['season'].' AND `table_id` = '.$match['in_table'].';');
						$dbObject->execute('UPDATE `w_sport_table` SET `matches` = '.$team2['matches'].', `wins` = '.$team2['wins'].', `draws` = '.$team2['draws'].', `loses` = '.$team2['loses'].', `s_score` = '.$team2['s_score'].', `r_score` = '.$team2['r_score'].', `points` = '.$team2['points'].' WHERE `team` = '.$match['a_team'].' AND `season` = '.$match['season'].' AND `table_id` = '.$match['in_table'].';');
						$dbObject->execute('UPDATE `w_sport_stats` SET `table_id` = '.$match['in_table'].' WHERE `mid` = '.$match['id'].'');
					}
				} else {
					$_POST['match-new'] = $rb->get('matches.new');
				}
			}
			
			if($_POST['match-edit'] == $rb->get('matches.edit') || $_POST['match-new'] == $rb->get('matches.new')) {
				if($_POST['match-edit'] == $rb->get('matches.edit')) {
					$matchId = $_POST['match-id'];
					$match = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `comment`, `round`, `in_table`, `season` FROM `w_sport_match` WHERE `id` = '.$matchId.';');
					$match = $match[0];
				} else {
					//$match['in_table'] = 1;
				}
			
				$return .= ''
				.'<div class="match-edit-form">'
					.'<form name="match-edit-form" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
						.'<table class="match-edit-table">'
							.'<tr>'
								.'<th class="match-table-team"> </th>'
								.'<th class="match-table-name">'.$rb->get('matches.name').'</th>'
								.'<th class="match-table-score">'.$rb->get('matches.score').'</th>'
								.'<th class="match-table-shoots">'.$rb->get('matches.shoots').'</th>'
								.'<th class="match-table-penalty">'.$rb->get('matches.penalty').'</th>'
								.'<th class="match-table-extratime">'.$rb->get('matches.extratime').'</th>'
							.'</tr>'
							.'<tr>'
								.'<td class="match-table-team">'
									.'<label for="match-edit-hteam">'.$rb->get('matches.hteam').'</label>'
								.'</td>'
								.'<td class="match-table-name">'
									.'<select name="match-edit-hteam" id="match-edit-hteam">'
										.self::getTeamsOptions($match['h_team'])
									.'</select>'
								.'</td>'
								.'<td class="match-table-score">'
									.'<input type="text" name="match-edit-hscore" id="match-edit-hscore" value="'.$match['h_score'].'" />'
								.'</td>'
								.'<td class="match-table-shoots">'
									.'<input type="text" name="match-edit-hshoots" id="match-edit-hshoots" value="'.$match['h_shoots'].'" />'
								.'</td>'
								.'<td class="match-table-penalty">'
									.'<input type="text" name="match-edit-hpenalty" id="match-edit-hpenalty" value="'.$match['h_penalty'].'" />'
								.'</td>'
								.'<td class="match-table-extratime">'
									.'<input type="checkbox" name="match-edit-hextratime" id="match-edit-hextratime"'.(($match['h_extratime'] == 1) ? 'checked="checked"' : '').' />'
								.'</td>'
							.'</tr>'
							.'<tr>'
								.'<td class="match-table-team">'
									.'<label for="match-edit-ateam">'.$rb->get('matches.ateam').'</label>'
								.'</td>'
								.'<td class="match-table-name">'
									.'<select name="match-edit-ateam" id="match-edit-ateam">'
										.self::getTeamsOptions($match['a_team'])
									.'</select>'
								.'</td>'
								.'<td class="match-table-score">'
									.'<input type="text" name="match-edit-ascore" id="match-edit-ascore" value="'.$match['a_score'].'" />'
								.'</td>'
								.'<td class="match-table-shoots">'
									.'<input type="text" name="match-edit-ashoots" id="match-edit-ashoots" value="'.$match['a_shoots'].'" />'
								.'</td>'
								.'<td class="match-table-penalty">'
									.'<input type="text" name="match-edit-apenalty" id="match-edit-apenalty" value="'.$match['a_penalty'].'" />'
								.'</td>'
								.'<td class="match-table-extratime">'
									.'<input type="checkbox" name="match-edit-aextratime" id="match-edit-aextratime"'.(($match['a_extratime'] == 1) ? 'checked="checked"' : '').' />'
								.'</td>'
							.'</tr>'
						.'</table>'
						.'<div class="match-edit-comment">'
							.'<textarea name="match-edit-comment" id="match-edit-comment" rows="4">'
								.$match['comment']
							.'</textarea>'
						.'</div>'
						.'<div class="match-edit-round">'
							.'<label for="match-edit-round">'.$rb->get('match.round').':</label> '
							.'<input type="text" name="match-edit-round" id="match-edit-round" value="'.$match['round'].'" />'
							.'<label for="match-edit-in-table">'.$rb->get('match.intable').':</label> '
							.'<select name="match-edit-in-table" id="match-edit-in-table">'
										.'<option value="0">'.$rb->get('matches.nottotable').'</option>'
								.self::getTablesOptions($match['in_table'])
							.'</select>'
							/*.'<input type="checkbox" name="match-edit-in-table" id="match-edit-in-table"'.(($match['in_table'] == 0) ? '' : ' checked="checked"').'" />'*/
						.'</div>'
						.'<div class="match-edit-submit">'
							.'<input type="hidden" name="match-id" value="'.$match['id'].'" />'
							.'<input type="submit" name="match-edit-save" value="'.$rb->get('matches.save').'" />'
						.'</div>'
					.'</form>'
				.'</div>'
				.'<div class="clear"></div>';
				
				if($useFrames == "false") {
					return $return;
				} else {
					return parent::getFrame($rb->get('matches.form.title'), $return, "", true);
				}
			}
		}
		
		/**
		 *
		 *	Edit match.
		 *	C tag.
		 *	
     *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
		 *
		 */		 		 		 		 		
		public function showEditStatsForm($useFrames = false, $showMsg = false) {
			global $dbObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$ok = true;
			$return = '';
			
			if($_POST['match-stats-save'] == $rb->get('matches.stats.save')) {
				//echo '<pre>';
				//print_r($_POST);
				//echo '</pre>';
				
				$matchId = $_POST['match-id'];
				$playerId1 = $_POST['match-stats-player-id1'];
				$inmatch1 = $_POST['match-stats-inmatch1'];
				$goals1 = $_POST['match-stats-goals1'];
				$assists1 = $_POST['match-stats-assists1'];
				$shoots1 = $_POST['match-stats-shoots1'];
				$penalty1 = $_POST['match-stats-penalty1'];
				
				$playerId2 = $_POST['match-stats-player-id2'];
				$inmatch2 = $_POST['match-stats-inmatch2'];
				$goals2 = $_POST['match-stats-goals2'];
				$assists2 = $_POST['match-stats-assists2'];
				$shoots2 = $_POST['match-stats-shoots2'];
				$penalty2 = $_POST['match-stats-penalty2'];
				
				$match = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `comment`, `round`, `season` FROM `w_sport_match` WHERE `id` = '.$matchId.';');
				if(count($match) > 0) {
					$match = $match[0];
					// Kontrola integrity dat: ---
					/*$goa1 = 0;
					foreach($goals1 as $gl) {
						$goa1 += $gl;
					}
					$goa2 = 0;
					foreach($goals2 as $gl) {
						$goa2 += $gl;
					}
					if($goa1 != $match['h_team']) {
						$ok = false;
						$return .= '<h4 class="error">'.$rb->get('matches.stats.error.wronggoalscounth').'</h4>';
					}
					if($goa2 != $match['a_team']) {
						$ok = false;
						$return .= '<h4 class="error">'.$rb->get('matches.stats.error.wronggoalscounta').'</h4>';
					}
					$pen1 = 0;
					foreach($penalty1 as $pe) {
						$pen1 += $pe;
					}
					$pen2 = 0;
					foreach($penalty2 as $pe) {
						$pen2 += $pe;
					}
					if($pen1 != $match['h_penalty']) {
						$ok = false;
						$return .= '<h4 class="error">'.$rb->get('matches.stats.error.wrongpenaltycounth').'</h4>';
					}
					if($goa2 != $match['a_penalty']) {
						$ok = false;
						$return .= '<h4 class="error">'.$rb->get('matches.stats.error.wrongpenaltycounta').'</h4>';
					}*/
					// Konec ---
					
					if($ok) {
						$dbObject->execute('DELETE FROM `w_sport_stats` WHERE `mid` = '.$match['id'].' AND `season` = '.$_SESSION['sport']['season-id'].';');
						foreach($playerId1 as $key => $pl) {
							if($pl != '' && $inmatch1[$key] == 'on') {
								$goals = (($goals1[$key] != '') ? $goals1[$key] : 0);
								$assists = (($assists1[$key] != '') ? $assists1[$key] : 0);
								$shoots = (($shoots1[$key] != '') ? $shoots1[$key] : 0);
								$penalty = (($penalty1[$key] != '') ? $penalty1[$key] : 0);
								
								$stats = $dbObject->fetchAll('SELECT `pid` FROM `w_sport_stats` WHERE `pid` = '.$pl.' AND `mid` = '.$match['id'].' AND `season` = '.$_SESSION['sport']['season-id'].';');
								if(count($stats) == 0) {
									$dbObject->execute('INSERT INTO `w_sport_stats`(`pid`, `mid`, `season`, `goals`, `assists`, `shoots`, `penalty`) VALUES ('.$pl.', '.$match['id'].', '.$_SESSION['sport']['season-id'].', '.$goals.', '.$assists.', '.$shoots.', '.$penalty.');');
									$return .= '<h4 class="success">Saved!</h4>';
								} else {
								
								}
							}
						}
						foreach($playerId2 as $key => $pl) {
							if($pl != '' && $inmatch2[$key] == 'on') {
								$goals = (($goals2[$key] != '') ? $goals2[$key] : 0);
								$assists = (($assists2[$key] != '') ? $assists2[$key] : 0);
								$shoots = (($shoots2[$key] != '') ? $shoots2[$key] : 0);
								$penalty = (($penalty2[$key] != '') ? $penalty2[$key] : 0);
								
								$stats = $dbObject->fetchAll('SELECT `pid` FROM `w_sport_stats` WHERE `pid` = '.$pl.' AND `mid` = '.$match['id'].' AND `season` = '.$_SESSION['sport']['season-id'].';');
								if(count($stats) == 0) {
									$dbObject->execute('INSERT INTO `w_sport_stats`(`pid`, `mid`, `season`, `goals`, `assists`, `shoots`, `penalty`) VALUES ('.$pl.', '.$match['id'].', '.$_SESSION['sport']['season-id'].', '.$goals.', '.$assists.', '.$shoots.', '.$penalty.');');
									$return .= '<h4 class="success">Saved!</h4>';
								} else {
								
								}
							}
						}
					} else {
						$_POST['match-stats-add'] = $rb->get('matches.statsadd');
					}
				}
			}
			
			if($_POST['match-stats-edit'] == $rb->get('matches.statsedit') || $_POST['match-stats-add'] == $rb->get('matches.statsadd')) {
				//$return .= '<h4 class="warning">Coming soon ...</h4>';
				
				$matchId = $_POST['match-id'];
				$seasonId = $_SESSION['sport']['season-id'];
				$match = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `comment`, `round`, `season` FROM `w_sport_match` WHERE `id` = '.$matchId.';');
				$match = $match[0];
				$home = $dbObject->fetchAll('SELECT `name` FROM `w_sport_team` WHERE `id` = '.$match['h_team'].';');
				$away = $dbObject->fetchAll('SELECT `name` FROM `w_sport_team` WHERE `id` = '.$match['a_team'].';');
				
				$players1 = $dbObject->fetchAll('SELECT `id`, `name`, `surname` FROM `w_sport_player` WHERE `team` = '.$match['h_team'].' AND `season` = '.$seasonId.';');
				$players2 = $dbObject->fetchAll('SELECT `id`, `name`, `surname` FROM `w_sport_player` WHERE `team` = '.$match['a_team'].' AND `season` = '.$seasonId.';');
				
				$playersStr1 = $playersStr2 = ''
				.'<table class="match-stats-table">'
					.'<tr>'
						.'<th class="match-stats-name">'.$rb->get('matches.stats.name').':</th>'
						.'<th class="match-stats-inmatch">'.$rb->get('matches.stats.inmatch').':</th>'
						.'<th class="match-stats-goals">'.$rb->get('matches.stats.goals').':</th>'
						.'<th class="match-stats-assists">'.$rb->get('matches.stats.assists').':</th>'
						.'<th class="match-stats-shoots">'.$rb->get('matches.stats.shoots').':</th>'
						.'<th class="match-stats-penalty">'.$rb->get('matches.stats.penalty').':</th>'
					.'</tr>';

				$i = 1;
				foreach($players1 as $pl) {
					$tmpstats = $dbObject->fetchAll('SELECT `goals`, `assists`, `penalty`, `shoots` FROM `w_sport_stats` WHERE `pid` = '.$pl['id'].' AND `mid` = '.$match['id'].' AND `season` = '.$seasonId.';');
					$stats = $tmpstats[0];
					$playersStr1 .= ''
					.'<tr class="'.((($i % 2) == 1) ? 'idle' : 'even').'">'
						.'<td class="match-stats-name">'.$pl['name'].' '.$pl['surname'].'</td>'
						.'<td class="match-stats-inmatch">'
							.'<input type="hidden" name="match-stats-player-id1['.$pl['id'].']" value="'.$pl['id'].'" />'
							.'<input type="checkbox" name="match-stats-inmatch1['.$pl['id'].']"'.((count($tmpstats) == 1) ? ' checked="checked"' : '').' />'
						.'</td>'
						.'<td class="match-stats-goals">'
							.'<input type="text" name="match-stats-goals1['.$pl['id'].']" value="'.$stats['goals'].'" />'
						.'</td>'
						.'<td class="match-stats-assists">'
							.'<input type="text" name="match-stats-assists1['.$pl['id'].']" value="'.$stats['assists'].'" />'
						.'</td>'
						.'<td class="match-stats-shoots">'
							.'<input type="text" name="match-stats-shoots1['.$pl['id'].']" value="'.$stats['shoots'].'" />'
						.'</td>'
						.'<td class="match-stats-penalty">'
							.'<input type="text" name="match-stats-penalty1['.$pl['id'].']" value="'.$stats['penalty'].'" />'
						.'</td>'
					.'</tr>';
					$i ++;
				}

				$i = 1;
				foreach($players2 as $pl) {
					$tmpstats = $dbObject->fetchAll('SELECT `goals`, `assists`, `penalty`, `shoots` FROM `w_sport_stats` WHERE `pid` = '.$pl['id'].' AND `mid` = '.$match['id'].' AND `season` = '.$seasonId.';');
					$stats = $tmpstats[0];
					$playersStr2 .= ''
					.'<tr class="'.((($i % 2) == 1) ? 'idle' : 'even').'">'
						.'<td class="match-stats-name">'.$pl['name'].' '.$pl['surname'].'</td>'
						.'<td class="match-stats-inmatch">'
							.'<input type="hidden" name="match-stats-player-id2['.$pl['id'].']" value="'.$pl['id'].'" />'
							.'<input type="checkbox" name="match-stats-inmatch2['.$pl['id'].']"'.((count($tmpstats) == 1) ? ' checked="checked"' : '').' />'
						.'</td>'
						.'<td class="match-stats-goals">'
							.'<input type="text" name="match-stats-goals2['.$pl['id'].']" value="'.$stats['goals'].'" />'
						.'</td>'
						.'<td class="match-stats-assists">'
							.'<input type="text" name="match-stats-assists2['.$pl['id'].']" value="'.$stats['assists'].'" />'
						.'</td>'
						.'<td class="match-stats-shoots">'
							.'<input type="text" name="match-stats-shoots2['.$pl['id'].']" value="'.$stats['shoots'].'" />'
						.'</td>'
						.'<td class="match-stats-penalty">'
							.'<input type="text" name="match-stats-penalty2['.$pl['id'].']" value="'.$stats['penalty'].'" />'
						.'</td>'
					.'</tr>';
					$i ++;
				}
				
				$playersStr1 .= ''
				.'</table>';
				$playersStr2 .= ''
				.'</table>';
				
				$return .= ''
				.'<div class="match-edit-form">'
					.'<form name="match-edit-form" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
						.'<table class="match-edit-table">'
							.'<tr>'
								.'<th class="match-table-team"> </th>'
								.'<th class="match-table-name">'.$rb->get('matches.name').'</th>'
								.'<th class="match-table-score">'.$rb->get('matches.score').'</th>'
								.'<th class="match-table-shoots">'.$rb->get('matches.shoots').'</th>'
								.'<th class="match-table-penalty">'.$rb->get('matches.penalty').'</th>'
								.'<th class="match-table-extratime">'.$rb->get('matches.extratime').'</th>'
							.'</tr>'
							.'<tr>'
								.'<td class="match-table-team">'
									.'<label for="match-edit-hteam">'.$rb->get('matches.hteam').'</label>'
								.'</td>'
								.'<td class="match-table-name">'
									.$home[0]['name']
								.'</td>'
								.'<td class="match-table-score">'
									.$match['h_score']
								.'</td>'
								.'<td class="match-table-shoots">'
									.$match['h_shoots']
								.'</td>'
								.'<td class="match-table-penalty">'
									.$match['h_penalty']
								.'</td>'
								.'<td class="match-table-extratime">'
									.(($match['h_extratime'] == 1) ? $rb->get('matches.form.homeexwin') : '')
								.'</td>'
							.'</tr>'
							.'<tr>'
								.'<td class="match-table-team">'
									.$rb->get('matches.ateam')
								.'</td>'
								.'<td class="match-table-name">'
									.$away[0]['name']
								.'</td>'
								.'<td class="match-table-score">'
									.$match['a_score']
								.'</td>'
								.'<td class="match-table-shoots">'
									.$match['a_shoots']
								.'</td>'
								.'<td class="match-table-penalty">'
									.$match['a_penalty']
								.'</td>'
								.'<td class="match-table-extratime">'
									.(($match['a_extratime'] == 1) ? $rb->get('matches.form.awayexwin') : '')
								.'</td>'
							.'</tr>'
						.'</table>'
						.$playersStr1
						.$playersStr2
						.'<div class="match-edit-submit">'
							.'<input type="hidden" name="match-id" value="'.$match['id'].'" />'
							.'<input type="submit" name="match-stats-save" value="'.$rb->get('matches.stats.save').'" />'
						.'</div>'
					.'</form>'
				.'</div>'
				.'<div class="clear"></div>';
			
				if($useFrames == "false") {
					return $return;
				} else {
					return parent::getFrame($rb->get('matches.stats.title').', '.$rb->get('match.round').': '.$match['round'].', '.$home[0]['name'].' : '.$away[0]['name'], $return, "", true);
				}
			}
		}
		
		/**
		 *
		 *	Shows seasons.
		 *	C tag.
		 *	
		 *	@param		templateId			template id
		 *	@param		sorting					ASC or DESC
		 *
		 */		 		 		 		 		
		public function showSeasons($templateId, $sorting) {
			global $dbObject;
			global $loginObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if(strtolower($sorting) == 'asc') {
				$sorting = 'ASC';
			} else {
				$sorting = 'DESC';
			}
			
			$seasons = $dbObject->fetchAll('SELECT `id` FROM `w_sport_season` ORDER BY `start_year` '.$sorting.';');
			if(count($seasons) > 0) {
				$rights = $dbObject->fetchAll('SELECT `value` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = '.$templateId.' AND `template_right`.`type` = '.WEB_R_READ.' AND `group`.`value` >= '.$loginObject->getGroupValue().';');
				if(count($rights) > 0 && $templateId > 0) {
					$template = $dbObject->fetchAll('SELECT `content` FROM `template` WHERE `id` = '.$templateId.';');
					$templateContent = $template[0]['content'];
				} else {
					$message = "Permission Denied when reading template[templateId = ".$templateId."]!";
    			trigger_error($message, E_USER_WARNING);
    		 	return;
				}
						
				$i = 1;
				$_SESSION['sport']['table']['season-id'] = $_SESSION['sport']['season-id'];
				$_SESSION['sport']['table']['i'] = $_SESSION['sport']['i'];
				foreach($seasons as $season) {
					$_SESSION['sport']['season-id'] = $season['id'];
					$_SESSION['sport']['i'] = $i;
					$Parser = new CustomTagParser();
				  $Parser->setContent($templateContent);
				  $Parser->startParsing();
  				$return .= $Parser->getResult();
					$i ++;
 				}
				$_SESSION['sport']['season-id'] = $_SESSION['sport']['table']['season-id'];
				$_SESSION['sport']['i'] = $_SESSION['sport']['table']['i'];
			} else {
				$return .= '<h4 class="warning>'.$rb->get('seasons.warning.nodata').'</h4>';
			}
			
			return $return;	
		}
		
		/**
		 *
		 *	Shows season.
		 *	C tag.
		 *	
		 *	@param		field						field name to show
		 *	@param		seasonId				season id		 
		 *	@param		errMsg					error msg		 
		 *
		 */		 		 		 		 		
		public function showSeason($field, $seasonId = false, $errMsg = false) {
			global $dbObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if($seasonId == false) {
				$seasonId = $_SESSION['sport']['season-id'];
			}
			
			if($seasonId != '') {
				if($_SESSION['sport']['season'][0]['id'] != $seasonId) {
					$season = $dbObject->fetchAll('SELECT `start_year`, `end_year` FROM `w_sport_season` WHERE `id` = '.$seasonId.';');
					$_SESSION['sport']['season'] = $season;
				} else {
					$season = $_SESSION['sport']['season'];
				}
				if(count($season) > 0) {
					$season = $season[0];
					switch(strtolower($field)) {
						case 'row': $return .= ((($_SESSION['sport']['i'] % 2) == 1) ? 'idle' : 'even'); break;
						case 'i': $return .= $_SESSION['sport']['i']; break;
						case 'id': $return .= $season['id']; break;
						case 'start_year': $return .= $season['start_year']; break;
						case 'end_year': $return .= $season['end_year']; break;
						default: $return .= '<h4 class="error">'.$rb->get('season.error.incorrectfield').'</h4>';;
					}
				} else {
					if($errMsg != false) {
						$return .=  $errMsg;
					} else {
						$return .= '<h4 class="error">'.$rb->get('season.error.seasondoesntexist').'</h4>';
					}
				}
			} else {
				if($errMsg != false) {
					$return .=  $errMsg;
				} else {
					$return .= '<h4 class="error">'.$rb->get('season.error.seasonnotset').'</h4>';
				}
			}
			
			return $return;
		}
		
		/**
		 *
		 *	Shows table for selected season.
		 *	C tag.
		 *	
		 *	@param		templateId			template id
		 *	@param		seasonId				season to show		 
		 *	@param		useFrames				use frames in output
     *	@param		showMsg					show messages in output
		 *
		 */		 		 		 		 		 		
		public function showTable($templateId = false, $seasonId = false, $tableId, $useFrames = false, $showMsg = false) {
			global $dbObject;
			global $loginObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if(($seasonId != '' || $_SESSION['sport']['season-id'] != '') && ($tableId != '' || $_SESSION['sport']['table-id'] != '')) {
				if($seasonId == '') {
					$seasonId = $_SESSION['sport']['season-id'];
				}
				if($tableId == '') {
					$tableId = $_SESSION['sport']['table-id'];
				}
				
				$table = $dbObject->fetchAll('SELECT `w_sport_team`.`id`, `w_sport_team`.`name`, `w_sport_table`.`matches`, `w_sport_table`.`wins`, `w_sport_table`.`draws`, `w_sport_table`.`loses`, `w_sport_table`.`s_score`, `w_sport_table`.`r_score`, `w_sport_table`.`points` FROM `w_sport_table` LEFT JOIN `w_sport_team` ON `w_sport_table`.`team` = `w_sport_team`.`id` WHERE `w_sport_table`.`season` = '.$seasonId.' AND `w_sport_team`.`season` = '.$seasonId.' AND `w_sport_table`.`table_id` = '.$tableId.' ORDER BY `points` DESC, (`w_sport_table`.`s_score` - `w_sport_table`.`r_score`) DESC, `w_sport_table`.`s_score` DESC, `w_sport_table`.`wins` DESC;');
				
				if(count($table) > 0) {
					if($templateId == false) {
						$return .= ''
						.'<table class="table">'
							.'<tr class="table-head">'
								.'<th class="table-position">'.$rb->get('table.position').'</th>'
								.'<th class="table-name">'.$rb->get('table.name').'</th>'
								.'<th class="table-matches">'.$rb->get('table.matches').'</th>'
								.'<th class="table-wins">'.$rb->get('table.wins').'</th>'
								.'<th class="table-draws">'.$rb->get('table.draws').'</th>'
								.'<th class="table-loses">'.$rb->get('table.loses').'</th>'
								.'<th class="table-s_score">'.$rb->get('table.s_score').'</th>'
								.'<th class="table-r_score">'.$rb->get('table.r_score').'</th>'
								.'<th class="table-points">'.$rb->get('table.points').'</th>'
							.'</tr>';
					
						$i = 1;
						foreach($table as $team) {
							$return .= ''
							.'<tr class="'.((($i % 2) == 1) ? 'idle' : 'even').'">'
								.'<td class="table-position">'.$i.'</td>'
								.'<td class="table-name">'.$team['name'].'</td>'
								.'<td class="table-matches">'.$team['matches'].'</td>'
								.'<td class="table-wins">'.$team['wins'].'</td>'
								.'<td class="table-draws">'.$team['draws'].'</td>'
								.'<td class="table-loses">'.$team['loses'].'</td>'
								.'<td class="table-s_score">'.$team['s_score'].'</td>'
								.'<td class="table-r_score">'.$team['r_score'].'</td>'
								.'<td class="table-points">'.$team['points'].'</td>'
							.'</tr>';
							$i ++;
						}
						
						$return .= ''
						.'</table>';
					} else {
						$rights = $dbObject->fetchAll('SELECT `value` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = '.$templateId.' AND `template_right`.`type` = '.WEB_R_READ.' AND `group`.`value` >= '.$loginObject->getGroupValue().';');
						if(count($rights) > 0 && $templateId > 0) {
							$template = $dbObject->fetchAll('SELECT `content` FROM `template` WHERE `id` = '.$templateId.';');
							$templateContent = $template[0]['content'];
						} else {
							$message = "Permission Denied when reading template[templateId = ".$templateId."]!";
    	  			trigger_error($message, E_USER_WARNING);
    			  	return;
						}
						
						$i = 1;
						$_SESSION['sport']['table']['season-id'] = $_SESSION['sport']['season-id'];
						$_SESSION['sport']['table']['team-id'] = $_SESSION['sport']['team-id'];
						$_SESSION['sport']['table']['i'] = $_SESSION['sport']['i'];
						foreach($table as $team) {
							$_SESSION['sport']['season-id'] = $seasonId;
							$_SESSION['sport']['team-id'] = $team['id'];
							$_SESSION['sport']['i'] = $i;
							$Parser = new CustomTagParser();
						  $Parser->setContent($templateContent);
						  $Parser->startParsing();
		  				$return .= $Parser->getResult();
							$i ++;
	  				}
						$_SESSION['sport']['season-id'] = $_SESSION['sport']['table']['season-id'];
						$_SESSION['sport']['team-id'] = $_SESSION['sport']['table']['team-id'];
						$_SESSION['sport']['i'] = $_SESSION['sport']['table']['i'];
					}
				} else {
					$return .= '<h4 class="warning">'.$rb->get('table.nodata').'</h4>';
				}
			} else {
				$return .= '<h4 class="error">'.$rb->get('season.error.notset').'</h4>';
			}
		
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('table.title'), $return, "", true);
			}
		}
		
		/**
		 *
		 *	Shows team.
		 *	C tag.
		 *	
		 *	@param		field						field name to show
		 *	@param		teamId					team id
		 *	@param		seasonId				season id
		 *	@param		match						for displaying in match, indicates "home" or "away"		 
		 *	@param		errMsg					error message
		 *
		 */		 		 		 		 		
		public function showTeam($field, $teamId = false, $seasonId = false, $match = false, $errMsg = false) {
			global $dbObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if($teamId == false) {
				if(strtolower($match) == 'home') {
					$teamId = $_SESSION['sport']['team-id-home'];
				} elseif(strtolower($match) == 'away') {
					$teamId = $_SESSION['sport']['team-id-away'];
				} else {
					$teamId = $_SESSION['sport']['team-id'];
				}
			}
			if($seasonId == false) {
				$seasonId = $_SESSION['sport']['season-id'];
			}
			
			if($teamId != '' && $seasonId != '') {
				if($_SESSION['sport']['team'][0]['id'] != $teamId) {
					$team = $dbObject->fetchAll('SELECT `w_sport_team`.`id`, `w_sport_team`.`name`, `w_sport_team`.`logo`, `w_sport_table`.`matches`, `w_sport_table`.`wins`, `w_sport_table`.`draws`, `w_sport_table`.`loses`, `w_sport_table`.`s_score`, `w_sport_table`.`r_score`, `w_sport_table`.`points` FROM `w_sport_table` LEFT JOIN `w_sport_team` ON `w_sport_table`.`team` = `w_sport_team`.`id` WHERE `w_sport_table`.`season` = '.$seasonId.' AND `w_sport_team`.`season` = '.$seasonId.' AND `w_sport_team`.`id` = '.$teamId.';');
					$_SESSION['sport']['team'] = $team;
				} else {
					$team = $_SESSION['sport']['team'];
				}
				if(count($team) > 0) {
					$team = $team[0];
					switch(strtolower($field)) {
						case 'row': $return .= ((($_SESSION['sport']['i'] % 2) == 1) ? 'idle' : 'even'); break;
						case 'i': $return .= $_SESSION['sport']['i']; break;
						case 'id': $return .= $team['id']; break;
						case 'name': $return .= $team['name']; break;
						case 'logo': $return .= $team['logo']; break;
						case 'matches': $return .= $team['matches']; break;
						case 'wins': $return .= $team['wins']; break;
						case 'draws': $return .= $team['draws']; break;
						case 'loses': $return .= $team['loses']; break;
						case 's_score': $return .= $team['s_score']; break;
						case 'r_score': $return .= $team['r_score']; break;
						case 'd_score': $return .= ($team['s_score'] - $team['r_score']); break;
						case 'points': $return .= $team['points']; break;
						default: $return .= '<h4 class="error">'.$rb->get('table.error.incorrectfield').'</h4>';;
					}
				} else {
					if($errMsg != false) {
						$return .=  $errMsg;
					} else {
						$return .= '<h4 class="error">'.$rb->get('table.error.teamdoesntexist').'</h4>';
					}
				}
			} else {
				if($errMsg != false) {
					$return .=  $errMsg;
				} else {
					$return .= '<h4 class="error">'.$rb->get('table.error.seasonorteamnotset').'</h4>';
				}
			}
			
			return $return;
		}
		
		/**
		 *
		 *	Shows matches.
		 *	C tag.
		 *	
		 *	@param		templateId			template id
		 *	@param		sorting					ASC or DESC		 
		 *	@param		round						matches from passed round
		 *	@param		teamId					team id
		 *	@param		seasonId				season id
		 *
		 */		 		 		 		 		
		public function showMatches($templateId, $sorting, $round = false, $teamId = false, $seasonId = false) {
			global $dbObject;
			global $loginObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if($round == false) {
				$round = $_SESSION['sport']['round'];
			}
			if($teamId == false) {
				$teamId = $_SESSION['sport']['team-id'];
			}
			if($seasonId == false) {
				$seasonId = $_SESSION['sport']['season-id'];
			}
			if(strtolower($sorting) == 'asc') {
				$sorting = 'ASC';
			} else {
				$sorting = 'DESC';
			}
			
			$data = array();
			if($teamId != '' && $seasonId != '' && $round != '') {
				$data = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `season` FROM `w_sport_match` WHERE (`h_team` = '.$teamId.' OR `a_team` = '.$teamId.') AND `season` = '.$seasonId.' AND `round` = '.$round.' ORDER BY `round` '.$sorting.', `id` '.$sorting.';');
			} elseif($teamId != '' && $seasonId != '' && $round == '') {
				$data = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `season` FROM `w_sport_match` WHERE (`h_team` = '.$teamId.' OR `a_team` = '.$teamId.') AND `season` = '.$seasonId.' ORDER BY `round` '.$sorting.', `id` '.$sorting.';');
			} elseif($teamId != '' && $seasonId == '' && $round != '') {
				$data = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `season` FROM `w_sport_match` WHERE (`h_team` = '.$teamId.' OR `a_team` = '.$teamId.') AND `round` = '.$round.' ORDER BY `round` '.$sorting.', `id` '.$sorting.';');
			} elseif($teamId != '' && $seasonId == '' && $round == '') {
				$data = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `season` FROM `w_sport_match` WHERE (`h_team` = '.$teamId.' OR `a_team` = '.$teamId.') ORDER BY `round` '.$sorting.', `id` '.$sorting.';');
			} elseif($teamId == '' && $seasonId != '' && $round != '') {
				$data = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `season` FROM `w_sport_match` WHERE `season` = '.$seasonId.' AND `round` = '.$round.' ORDER BY `round` '.$sorting.', `id` '.$sorting.';');
			} elseif($teamId == '' && $seasonId != '' && $round == '') {
				$data = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `season` FROM `w_sport_match` WHERE `season` = '.$seasonId.' ORDER BY `round` '.$sorting.', `id` '.$sorting.';');
			} elseif($teamId == '' && $seasonId == '' && $round != '') {
				$data = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `season` FROM `w_sport_match` WHERE `round` = '.$round.' ORDER BY `round` '.$sorting.', `id` '.$sorting.';');
			} elseif($teamId == '' && $seasonId == '' && $round == '') {
				$data = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `season` FROM `w_sport_match` ORDER BY `round` '.$sorting.', `id` '.$sorting.';');
			}
			
			if(count($data) > 0) {
				$rights = $dbObject->fetchAll('SELECT `value` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = '.$templateId.' AND `template_right`.`type` = '.WEB_R_READ.' AND `group`.`value` >= '.$loginObject->getGroupValue().';');
				if(count($rights) > 0 && $templateId > 0) {
					$template = $dbObject->fetchAll('SELECT `content` FROM `template` WHERE `id` = '.$templateId.';');
					$templateContent = $template[0]['content'];
				} else {
					$message = "Permission Denied when reading template[templateId = ".$templateId."]!";
 	  			trigger_error($message, E_USER_WARNING);
 			  	return;
				}
				
				$i = 1;
				$_SESSION['sport']['matches']['match-id'] = $_SESSION['sport']['match-id'];
				$_SESSION['sport']['matches']['team-id-home'] = $_SESSION['sport']['team-id-home'];
				$_SESSION['sport']['matches']['team-id-away'] = $_SESSION['sport']['team-id-away'];
				$_SESSION['sport']['matches']['season-id'] = $_SESSION['sport']['season-id'];
				$_SESSION['sport']['matches']['i'] = $_SESSION['sport']['i'];
				foreach($data as $match) {
					$_SESSION['sport']['match-id'] = $match['id'];
					$_SESSION['sport']['team-id-home'] = $match['h_team'];
					$_SESSION['sport']['team-id-away'] = $match['a_team'];
					$_SESSION['sport']['season-id'] = $match['season'];
					$_SESSION['sport']['i'] = $i;
					$Parser = new CustomTagParser();
				  $Parser->setContent($templateContent);
				  $Parser->startParsing();
  				$return .= $Parser->getResult();
					$i ++;
 				}
				$_SESSION['sport']['match-id'] = $_SESSION['sport']['matches']['match-id'];
				$_SESSION['sport']['team-id-home'] = $_SESSION['sport']['matches']['team-id-home'];
				$_SESSION['sport']['team-id-away'] = $_SESSION['sport']['matches']['team-id-away'];
				$_SESSION['sport']['season-id'] = $_SESSION['sport']['matches']['season-id'];
				$_SESSION['sport']['i'] = $_SESSION['sport']['matches']['i'];
			} else {
				$return .= '<h4 class="warning">'.$rb->get('matches.warning.nodata').'</h4>';
			}
			
			return $return;	
		}
		
		/**
		 *
		 *	Shows match.
		 *	C tag.
		 *	
		 *	@param		field						field name to show
		 *	@param		matchId					match id
		 *	@param		scope					possible value "session" for field "round"
		 *	@param		errMsg					error message
		 *
		 */		 		 		 		 		
		public function showMatch($field, $matchId = false, $scope = false, $errMsg = false) {
			global $dbObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if($matchId == false) {
				$matchId = $_SESSION['sport']['match-id'];
			}
			
			if($matchId != '') {
				if($_SESSION['sport']['match'][0]['id'] != $matchId) {
					$match = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `comment`, `round`, `in_table` FROM `w_sport_match` WHERE `id` = '.$matchId.';');
					$_SESSION['sport']['match'] = $match;
				} else {
					$match = $_SESSION['sport']['match'];
				}
				if(count($match) > 0) {
					$match = $match[0];
					switch(strtolower($field)) {
						case 'row': $return .= ((($_SESSION['sport']['i'] % 2) == 1) ? 'idle' : 'even'); break;
						case 'i': $return .= $_SESSION['sport']['i']; break;
						case 'id': $return .= $match['id']; break;
						case 'h_score': $return .= $match['h_score']; break;
						case 'a_score': $return .= $match['a_score']; break;
						case 'h_shoots': $return .= $match['h_shoots']; break;
						case 'a_shoots': $return .= $match['a_shoots']; break;
						case 'h_penalty': $return .= $match['h_penalty']; break;
						case 'a_penalty': $return .= $match['a_penalty']; break;
						case 'h_extratime': $return .= $match['h_extratime']; break;
						case 'a_extratime': $return .= $match['a_extratime']; break;
						case 'h_extratime_text': $return .= (($match['h_extratime'] == 1) ? $rb->get('matches.form.homeexwin') : ''); break;
						case 'a_extratime_text': $return .= (($match['a_extratime']) ? $rb->get('matches.form.awayexwin') : ''); break;
						case 'comment': $return .= $match['comment']; break;
						case 'round': $return .= $match['round']; break;
						default: $return .= '<h4 class="error">'.$rb->get('match.error.incorrectfield').'</h4>';;
					}
				} else {
					if($errMsg != false) {
						$return .=  $errMsg;
					} else {
						$return .= '<h4 class="error">'.$rb->get('match.error.matchdoesntexist').'</h4>';
					}
				}
			} elseif(strtolower($scope) == 'session' && strtolower($field) == 'round') {
				$return .= $_SESSION['sport']['round'];
			} else {
				if($errMsg != false) {
					$return .=  $errMsg;
				} else {
					$return .= '<h4 class="error">'.$rb->get('match.error.matchidnotset').'</h4>';
				}
			}
			
			return $return;
		}
		
		/**
		 *
		 *	Shows rounds.
		 *	C tag.
		 *	
		 *	@param		templateId			template id
		 *	@param		sorting					ASC or DESC
		 *	@param		seasonId				season id
		 *
		 */		 		 		 		 		
		public function showRounds($templateId, $sorting, $seasonId = false) {
			global $dbObject;
			global $loginObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if($seasonId == false) {
				$seasonId = $_SESSION['sport']['season-id'];
			}
			if(strtolower($sorting) == 'asc') {
				$sorting = 'ASC';
			} else {
				$sorting = 'DESC';
			}
			if($seasonId != '') {
				$rounds = $dbObject->fetchAll('SELECT DISTINCT `round` FROM `w_sport_match` WHERE `season` = '.$seasonId.' ORDER BY `round` '.$sorting.';');
				if(count($rounds) > 0) {
					$rights = $dbObject->fetchAll('SELECT `value` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = '.$templateId.' AND `template_right`.`type` = '.WEB_R_READ.' AND `group`.`value` >= '.$loginObject->getGroupValue().';');
					if(count($rights) > 0 && $templateId > 0) {
						$template = $dbObject->fetchAll('SELECT `content` FROM `template` WHERE `id` = '.$templateId.';');
						$templateContent = $template[0]['content'];
					} else {
						$message = "Permission Denied when reading template[templateId = ".$templateId."]!";
 	  				trigger_error($message, E_USER_WARNING);
 				  	return;
					}
				
					$i = 1;
					$_SESSION['sport']['rounds']['round'] = $_SESSION['sport']['round'];
					$_SESSION['sport']['rounds']['i'] = $_SESSION['sport']['i'];
					foreach($rounds as $round) {
						$_SESSION['sport']['round'] = $round['round'];
						$_SESSION['sport']['i'] = $i;
						$Parser = new CustomTagParser();
				  	$Parser->setContent($templateContent);
					  $Parser->startParsing();
  					$return .= $Parser->getResult();
						$i ++;
 					}
					$_SESSION['sport']['round'] = $_SESSION['sport']['rounds']['round'];
					$_SESSION['sport']['i'] = $_SESSION['sport']['rounds']['i'];
				} else {
					$return .= '<h4 class="warning">'.$rb->get('rounds.warning.nodata').'</h4>';
				}
			} else {
				$return .= '<h4 class="warning">'.$rb->get('rounds.error.seasonidnotset').'</h4>';
			}
		
			return $return;	
		}
		
		/**
		 *
		 *	Shows players.
		 *	C tag.
		 *	
		 *	@param		templateId			template id
		 *	@param		sorting					ASC or DESC
		 *	@param		sortBy					filed name to sort by
		 *	@param		seasonId				season id
		 *	@param		teamId					team id
		 *	@param		fromMatchId
		 *	@param		only
		 *	@param		scope
		 *	@param		showGolmans
		 *	@param		limit		 
		 *
		 */		 		 		 		 		
		public function showPlayers($templateId, $sorting, $sortBy, $tableId = false, $teamId = false, $seasonId = false, $fromMatchId = false, $only = false, $scope = false, $showGolmans = false, $limit = false) {
			global $dbObject;
			global $loginObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			$players = self::getPlayersFrom('id', $sorting, $sortBy, $tableId, $teamId, $seasonId, $fromMatchId, $only, $scope, $showGolmans, $limit, $playerId);
			//echo $_SESSION['sport']['match-id'];
			//unset($_SESSION['sport']['match-id']);
				
			if(count($players) > 0) {
				$rights = $dbObject->fetchAll('SELECT `value` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = '.$templateId.' AND `template_right`.`type` = '.WEB_R_READ.' AND `group`.`value` >= '.$loginObject->getGroupValue().';');
				if(count($rights) > 0 && $templateId > 0) {
					$template = $dbObject->fetchAll('SELECT `content` FROM `template` WHERE `id` = '.$templateId.';');
					$templateContent = $template[0]['content'];
				} else {
					$message = "Permission Denied when reading template[templateId = ".$templateId."]!";
	 				trigger_error($message, E_USER_WARNING);
			  	return;
				}
			
				$i = 1;
				$_SESSION['sport']['players']['round'] = $_SESSION['sport']['round'];
				$_SESSION['sport']['players']['team-id'] = $_SESSION['sport']['team-id'];
				$_SESSION['sport']['players']['season-id'] = $_SESSION['sport']['season-id'];
				$_SESSION['sport']['players']['i'] = $_SESSION['sport']['i'];
				foreach($players as $player) {
					$_SESSION['sport']['player-id'] = $player['id'];
					$_SESSION['sport']['team-id'] = $player['team-id'];
					$_SESSION['sport']['season-id'] = $seasonId;
					$_SESSION['sport']['i'] = $i;
					$Parser = new CustomTagParser();
			  	$Parser->setContent($templateContent);
				  $Parser->startParsing();
 					$return .= $Parser->getResult();
					$i ++;
				}
				$_SESSION['sport']['round'] = $_SESSION['sport']['players']['round'];
				$_SESSION['sport']['team-id'] = $_SESSION['sport']['players']['team-id'];
				$_SESSION['sport']['season-id'] = $_SESSION['sport']['players']['season-id'];
				$_SESSION['sport']['i'] = $_SESSION['sport']['players']['i'];
			} else {
				$return .= '<h4 class="warning">'.$rb->get('players.warning.nodata').'</h4>';
			}
			
			return $return;	
		}
		
		/**
		 *
		 *	Shows player.
		 *	C tag.
		 *	
		 *	@param		field						field name to show
		 *	@param		playerId				player id
		 *	@param		teamId					team id
		 *	@param		seasonId				season id		 
		 *	@param		errMsg					error message		 
		 *
		 */		 		 		 		 		
		public function showPlayer($field, $playerId = false, $tableId = false, $teamId = false, $seasonId = false, $errMsg = false) {
			global $dbObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if($playerId == false) {
				$playerId = $_SESSION['sport']['player-id'];
			}
			if($teamId == false) {
				$teamId = $_SESSION['sport']['team-id'];
			}
			if($seasonId == false) {
				$seasonId = $_SESSION['sport']['season-id'];
			}
			
			$player = array();
			$player = self::getPlayersFrom('most', $sorting, $sortBy, $tableId, $teamId, $seasonId, $fromMatchId, $only, $scope, $showGolmans, 1, $playerId);
			
			if(count($player) > 0) {
				$player = $player[0];
				switch(strtolower($field)) {
					case 'row': $return .= ((($_SESSION['sport']['i'] % 2) == 1) ? 'idle' : 'even'); break;
					case 'i': $return .= $_SESSION['sport']['i']; break;
					case 'id': $return .= $player['id']; break;
					case 'name': $return .= $player['name']; break;
					case 'surname': $return .= $player['surname']; break;
					case 'birthyear': $return .= $player['birthyear']; break;
					case 'number': $return .= $player['number']; break;
					case 'position': $return .= $player['position']; break;
					case 'photo': $return .= $player['photo']; break;
					case 'total_matches': $return .= $player['total_matches']; break;
					case 'total_points': $return .= $player['total_points']; break;
					case 'total_goals': $return .= $player['total_goals']; break;
					case 'total_assists': $return .= $player['total_assists']; break;
					case 'total_shoots': $return .= $player['total_shoots']; break;
					case 'total_penalty': $return .= $player['total_penalty']; break;
					case 'total_percentage': $return .= $player['total_percentage']; break;
					case 'total_average': $return .= $player['total_average']; break;
					case 'season_matches': $return .= $player['season_matches']; break;
					case 'season_points': $return .= $player['season_points']; break;
					case 'season_goals': $return .= $player['season_goals']; break;
					case 'season_assists': $return .= $player['season_assists']; break;
					case 'season_shoots': $return .= $player['season_shoots']; break;
					case 'season_penalty': $return .= $player['season_penalty']; break;
					case 'season_percentage': $return .= $player['season_percentage']; break;
					case 'season_average': $return .= $player['season_average']; break;
					case 'match_matches': $return .= $player['match_matches']; break;
					case 'match_points': $return .= $player['match_points']; break;
					case 'match_goals': $return .= $player['match_goals']; break;
					case 'match_assists': $return .= $player['match_assists']; break;
					case 'match_shoots': $return .= $player['match_shoots']; break;
					case 'match_penalty': $return .= $player['match_penalty']; break;
					case 'match_percentage': $return .= $player['match_percentage']; break;
					case 'match_average': $return .= $player['match_average']; break;
					default: $return .= '<h4 class="error">'.$rb->get('player.error.incorrectfield').'</h4>';
				}
			} else {
				if($errMsg != false) {
					$return .=  $errMsg;
				} else {
					$return .= '<h4 class="error">'.$rb->get('player.error.teamdoesntexist').'</h4>';
				}
			}
			
			return $return;
		}
		
		// ------------------------------------------------------------------------------------------------------------------- \\
		
		public function getSeasonsOptions($teamId, $seasonId, $seaselId) {
			global $dbObject;
			$return = '';
			
			$seasql = $dbObject->fetchAll('SELECT `id`, `start_year`, `end_year` FROM `w_sport_season` ORDER BY `start_year` DESC;');
			foreach($seasql as $sea) {
				$tea = $dbObject->fetchAll('SELECT `id` FROM `w_sport_team` WHERE `id` = '.$teamId.' AND `season` = '.$sea['id'].';');
				if(count($tea) == 0 || $sea['id'] == $seasonId) {
					$return .= '<option value="'.$sea['id'].'"'.(($sea['id'] == $seaselId) ? 'selected="selectd"' : '').'>'.$sea['start_year'].' - '.$sea['end_year'].'</option>';
				}
			}
			
			return $return;
		}
		
		public function getTablesOptions($tabselId) {
			global $dbObject;
			$return = '';
			
			$tabsql = $dbObject->fetchAll('SELECT `id`, `name` FROM `w_sport_tables` ORDER BY `name` DESC;');
			foreach($tabsql as $tab) {
				$return .= '<option value="'.$tab['id'].'"'.(($tab['id'] == $tabselId) ? 'selected="selectd"' : '').'>'.$tab['name'].'</option>';
			}
			
			return $return;
		}
		
		public function getTeamsOptions($teaselId) {
			global $dbObject;
			$return = '';
			
			$teams = $dbObject->fetchAll('SELECT DISTINCT `id`, `name` FROM `w_sport_team` ORDER BY `name`;');
			foreach($teams as $team) {
				$return .= '<option value="'.$team['id'].'"'.(($team['id'] == $teaselId) ? 'selected="selectd"' : '').'>'.$team['name'].'</option>';
			}
			
			return $return;
		}
		
		// ------------------------------------------------------------------------------------------------------------------- \\
		
		public function getPlayersFrom($type, $sorting, $sortBy, $tableId = false, $teamId = false, $seasonId = false, $fromMatchId = false, $only = false, $scope = false, $showGolmans = false, $limit = false, $playerId = false) {
			global $dbObject;
			
			$cols = '';
			$matchsql = '';
			$onlysql = '';
			$positionsql = '';
			$limisql = '';
			$joinstatssql = '';
			$subqueriessql = '';
			$conditionssql = '';
			
			//echo $_SESSION['sport']['match-id'];
			//unset($_SESSION['sport']['match-id']);
			if($seasonId == false) {
				$seasonId = $_SESSION['sport']['season-id'];
			}
			if($tableId == false) {
				$tableId = $_SESSION['sport']['table-id'];
			}
			if($teamId == false) {
				$teamId = $_SESSION['sport']['team-id'];
			}
			if($fromMatchId == false) {
				$fromMatchId = $_SESSION['sport']['match-id'];
			}
			
				
			if(strtolower($sorting) == 'asc') {
				$sorting = 'ASC';
			} else {
				$sorting = 'DESC';
			}
			
			if(strtolower($sortBy) == 'id') {
				$sortBy = 'id';
			} elseif(strtolower($sortBy) == 'name') {
				$sortBy = 'name';
			} elseif(strtolower($sortBy) == 'surname') {
				$sortBy = 'surname';
			} elseif(strtolower($sortBy) == 'number') {
				$sortBy = 'number';
			} elseif(strtolower($sortBy) == 'total_matches') {
				$sortBy = 'total_matches';
			} elseif(strtolower($sortBy) == 'total_goals') {
				$sortBy = 'total_goals';
			} elseif(strtolower($sortBy) == 'total_assists') {
				$sortBy = 'total_assists';
			} elseif(strtolower($sortBy) == 'total_shoots') {
				$sortBy = 'total_shoots';
			} elseif(strtolower($sortBy) == 'total_penalty') {
				$sortBy = 'total_penalty';
			} elseif(strtolower($sortBy) == 'total_percentage') {
				$sortBy = 'total_percentage';
			} elseif(strtolower($sortBy) == 'total_average') {
				$sortBy = 'total_average';
			} elseif(strtolower($sortBy) == 'season_matches') {
				$sortBy = 'season_matches';
			} elseif(strtolower($sortBy) == 'season_goals') {
				$sortBy = 'season_goals';
			} elseif(strtolower($sortBy) == 'season_assists') {
				$sortBy = 'season_assists';
			} elseif(strtolower($sortBy) == 'season_shoots') {
				$sortBy = 'season_shoots';
			} elseif(strtolower($sortBy) == 'season_penalty') {
				$sortBy = 'season_penalty';
			} elseif(strtolower($sortBy) == 'season_percentage') {
				$sortBy = 'season_percentage';
			} elseif(strtolower($sortBy) == 'season_average') {
				$sortBy = 'season_average';
			} elseif(strtolower($sortBy) == 'match_matches') {
				$sortBy = 'match_matches';
			} elseif(strtolower($sortBy) == 'match_goals') {
				$sortBy = 'match_goals';
			} elseif(strtolower($sortBy) == 'match_assists') {
				$sortBy = 'match_assists';
			} elseif(strtolower($sortBy) == 'match_shoots') {
				$sortBy = 'match_shoots';
			} elseif(strtolower($sortBy) == 'match_penalty') {
				$sortBy = 'match_penalty';
			} elseif(strtolower($sortBy) == 'match_percentage') {
				$sortBy = 'match_percentage';
			} elseif(strtolower($sortBy) == 'match_average') {
				$sortBy = 'match_average';
			} else {
				$sortBy = 'surname';
			}
			
			//unset($_SESSION['sport']['players']['from']);
			/*echo $_SESSION['sport']['players']['from']['type'].' == '.$type.'<br />'.
			$_SESSION['sport']['players']['from']['sorting'].' == '.$sorting.'<br />'.
			$_SESSION['sport']['players']['from']['sortBy'].' == '.$sortBy.'<br />'.
			$_SESSION['sport']['players']['from']['teamId'].' == '.$teamId.'<br />'.
			$_SESSION['sport']['players']['from']['seasonId'].' == '.$seasonId.'<br />'.
			$_SESSION['sport']['players']['from']['fromMatchId'].' == '.$fromMatchId.'<br />'.
			$_SESSION['sport']['players']['from']['only'].' == '.$only.'<br />'.
			$_SESSION['sport']['players']['from']['scope'].' == '.$scope.'<br />'.
			$_SESSION['sport']['players']['from']['showGolmans'].' == '.$showGolmans.'<br />'.
			$_SESSION['sport']['players']['from']['playerId'].' == '.$playerId.'<br />'.
			$_SESSION['sport']['players']['from']['players'].'<br />';*/
			
			if($type == $_SESSION['sport']['players']['from']['type'] && $sorting == $_SESSION['sport']['players']['from']['sorting'] && $sortBy == $_SESSION['sport']['players']['from']['sortBy'] && $teamId == $_SESSION['sport']['players']['from']['teamId'] && $seasonId == $_SESSION['sport']['players']['from']['seasonId'] && $fromMatchId == $_SESSION['sport']['players']['from']['fromMatchId'] && $only == $_SESSION['sport']['players']['from']['only'] && $scope == $_SESSION['sport']['players']['from']['scope'] && $showGolmans == $_SESSION['sport']['players']['from']['showGolmans'] && $limit == $_SESSION['sport']['players']['from']['limit'] && $playerId == $_SESSION['sport']['players']['from']['playerId']) {
				$players = $_SESSION['sport']['players']['from']['players'];
			} else {
			
				if($fromMatchId != false) {
					$matchsql = '`w_sport_stats`.`mid` = '.$fromMatchId.'';
					$joinstatssql = 'LEFT JOIN `w_sport_stats` ON `player`.`id` = `w_sport_stats`.`pid`';
				}
				if(strtolower($only) == 'match') {
					if(strtolower($scope) == 'total') {
						$onlysql = '`total_matches` > 0';
					} elseif(strtolower($scope) == 'season') {
						$onlysql = '`season_matches` > 0';
					} elseif(strtolower($scope) == 'match') {
						$onlysql = '`match_matches` > 0';
					}
				} elseif(strtolower($only) == 'goal') {
					if(strtolower($scope) == 'total') {
						$onlysql = '`total_goals` > 0';
					} elseif(strtolower($scope) == 'season') {
						$onlysql = '`season_goals` > 0';
					} elseif(strtolower($scope) == 'match') {
						$onlysql = '`match_goals` > 0';
					}
				} elseif(strtolower($only) == 'assist') {
					if(strtolower($scope) == 'total') {
						$onlysql = '`total_assists` > 0';
					} elseif(strtolower($scope) == 'season') {
						$onlysql = '`season_assists` > 0';
					} elseif(strtolower($scope) == 'match') {
						$onlysql = '`match_assists` > 0';
					}
				} elseif(strtolower($only) == 'shoot') {
					if(strtolower($scope) == 'total') {
						$onlysql = '`total_shoots` > 0';
					} elseif(strtolower($scope) == 'season') {
						$onlysql = '`season_shoots` > 0';
					} elseif(strtolower($scope) == 'match') {
						$onlysql = '`match_shoots` > 0';
					}
				} elseif(strtolower($only) == 'penalty') {
					if(strtolower($scope) == 'total') {
						$onlysql = '`total_penalty` > 0';
					} elseif(strtolower($scope) == 'season') {
						$onlysql = '`season_penalty` > 0';
					} elseif(strtolower($scope) == 'match') {
						$onlysql = '`match_penalty` > 0';
					}
				}
				if($showGolmans == 'true') {
					$positionsql = '`player`.`position` = 1';
				} elseif($showGolmans == 'false') {
					$positionsql = '(`player`.`position` = 2 OR `player`.`position` = 3)';
				}
				
				$subqueriessql .= '(SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_matches`, (SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_goals`, (SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_assists`, (SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_penalty`, (SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_shoots`, (SELECT (SUM(`shoots`) / (SUM(`shoots`) + SUM(`goals`)) * 100) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_percentage`,(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_average`, (SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`) AS `total_points`';
				if($teamId != '') {
					if(strlen($conditionssql) != 0) {
						$conditionssql .= ' AND `player`.`team` = '.$teamId;
					} else {
						$conditionssql .= ' `player`.`team` = '.$teamId;
					}
				}
				if($seasonId != '') {
					if(strlen($conditionssql) != 0) {
						$conditionssql .= ' AND `player`.`season` = '.$seasonId;
					} else {
						$conditionssql .= ' `player`.`season` = '.$seasonId;
					}
					$subqueriessql .= ', (SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_matches`, (SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_goals`, (SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_assists`, (SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_penalty`, (SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_shoots`, (SELECT (SUM(`shoots`) / (SUM(`shoots`) + SUM(`goals`)) * 100) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_percentage`,(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_average`, (SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = '.$seasonId.' AND `pid` = `player`.`id`) AS `season_points`';
				}
				if($tableId != '') {
					if(strlen($conditionssql) != 0) {
						$conditionssql .= ' AND `w_sport_stats`.`table_id` = '.$tableId;
					} else {
						$conditionssql .= ' `w_sport_stats`.`table_id` = '.$tableId;
					}
				}
				if(strlen($matchsql) != 0) {
					if(strlen($conditionssql) != 0) {
						$conditionssql .= ' AND '.$matchsql;
					} else {
						$conditionssql .= ' '.$matchsql;
					}
					$subqueriessql .= ', (SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_matches`, (SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_goals`, (SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_assists`, (SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_penalty`, (SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_shoots`, (SELECT (SUM(`shoots`) / (SUM(`shoots`) + SUM(`goals`)) * 100) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_percentage`,(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_average`, (SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = '.$fromMatchId.' AND `pid` = `player`.`id`) AS `match_points`';
				}
				if(strlen($onlysql) != 0) {
					if(strlen($conditionssql) != 0) {
						$conditionssql .= ' AND '.$onlysql;
					} else {
						$conditionssql .= ' '.$onlysql;
					}
				}
				if(strlen($positionsql) != 0) {
					if(strlen($conditionssql) != 0) {
						$conditionssql .= ' AND '.$positionsql;
					} else {
						$conditionssql .= ' '.$positionsql;
					}
				}
				if($playerId != false) {
					if(strlen($conditionssql) != 0) {
						$conditionssql .= ' AND `player`.`id` = '.$playerId;
					} else {
						$conditionssql .= ' `player`.`id` = '.$playerId;
					}
				}
				if($limit != false) {
					$limitsql = 'LIMIT '.$limit;
				}
				
				if($type == 'most') {
					$cols = '`player`.`id`, `player`.`name`, `player`.`surname`, `player`.`birthyear`, `player`.`number`, `player`.`position`, `player`.`photo`';
				} else {
					$cols = '`player`.`id`, `w_sport_team`.`id` AS `team-id`';
				}
			
				$players = $dbObject->fetchAll('SELECT DISTINCT '.$cols.', '.$subqueriessql.' FROM `w_sport_player` AS `player` LEFT JOIN `w_sport_team` ON `player`.`team` = `w_sport_team`.`id`'.((strlen($joinstatssql) != 0) ? ' '.$joinstatssql : '').''.((strlen($conditionssql) != 0) ? ' WHERE '.$conditionssql : '').' ORDER BY `'.$sortBy.'` '.$sorting.((strlen($limitsql) != 0) ? ' '.$limitsql : '').';', true, true);
				$_SESSION['sport']['players']['from']['type'] = $type;
				$_SESSION['sport']['players']['from']['sorting'] = $sorting;
				$_SESSION['sport']['players']['from']['sortBy'] = $sortBy;
				$_SESSION['sport']['players']['from']['teamId'] = $teamId;
				$_SESSION['sport']['players']['from']['seasonId'] = $seasonId;
				$_SESSION['sport']['players']['from']['fromMatchId'] = $fromMatchId;
				$_SESSION['sport']['players']['from']['only'] = $only;
				$_SESSION['sport']['players']['from']['scope'] = $scope;
				$_SESSION['sport']['players']['from']['showGolmans'] = $showGolmans;
				$_SESSION['sport']['players']['from']['limit'] = $limit;
				$_SESSION['sport']['players']['from']['playerId'] = $playerId;
				$_SESSION['sport']['players']['from']['players'] = $players;
			}
			
			return $players;
		}
  }

?>