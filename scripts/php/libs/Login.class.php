<?php

  /**
   *
   *  Require base tag lib class.   
   *
   */
  require_once("BaseTagLib.class.php");
  
  /**
   *
   *  Simple login class.
   *  Default object.
   *
   *  @objectname loginObject     
   *      
   *  @author     Marek SMM
   *  @timestamp  2009-06-03
   *
   *
   */              
  class Login extends BaseTagLib {
    
    /**
     *
     *  User login.
     *
     */                   
    private $UserLogin;
    
    /**
     *
     *	User id.
     *
     */		 		 		     
    private $UserId = 0;
    
    /**
     *
     *  User name.
     *
     */                   
    private $UserName = "";
    
    /**
     *
     *  User surname.
     *
     */                   
    private $UserSurname = "";
    
    /**
     *
     *  User group name.
     *
     */                   
    private $UserGroup = "";
    
    /**
     *
     *  Groups where user is in.
     *
     */                   
    private $Groups = array(array('gid' => 3, 'name' => "web"));
    
    /**
     *
     *	Used user group for log in.
     *
     */		 		 		     
    private $UsedGroup = array();
    
    /**
     *
     *  Min value from user groups.
     *
     */                   
    private $GroupValue = 254;
    
    /**
     *
     *  Session id.
     *
     */                   
    private $SessionId = 0;
    
    /**
     *
     *  User login time.
     *
     */                   
    private $Logtime = "";
    
    /**
     *
     *  Id in user_log table.     
     *
     */              
    private $LogId = 0;
    
    /**
     *
     *  Flag is logged.     
     *
     */              
    private $IsLogged = false;
  
    /**
     *
     *  Initialize object.
     *
     */                   
    public function __construct() {
      self::setTagLibXml("xml/Login.xml");
      
      //self::initLogin();
    }
    
    /**
     *
     *  Init login.
     *  C tag.
     *
     */                        
    public function initLogin($group) {
      if(!self::isLogged()) {
        global $dbObject;
        $this->SessionId = $_SESSION[$group.'_session_id'];
        if(is_numeric($this->SessionId)) {
          $return = $dbObject->fetchAll("SELECT `user`.`uid`, `user`.`login`, `user`.`name`, `user`.`surname`, `user_log`.`timestamp` FROM `user` LEFT JOIN `user_log` ON `user`.`uid` = `user_log`.`user_id` WHERE `user_log`.`session_id` = ".$this->SessionId.";");
          if(count($return) == 1) {
            $groups = $dbObject->fetchAll("SELECT `group`.`gid`, `group`.`name`, `group`.`value` FROM `user_in_group` LEFT JOIN `group` ON `user_in_group`.`gid` = `group`.`gid` WHERE `uid` = ".$return[0]['uid']." ORDER BY `group`.`value`;");
            $groupMinValue = $dbObject->fetchAll("SELECT MIN(`group`.`value`) AS `value` FROM `group` LEFT JOIN `user_in_group` ON `group`.`gid` = `user_in_group`.`gid` WHERE `uid` = ".$return[0]['uid'].";");
            $this->UsedGroup['name'] = $group;
            //$this->Groups = array();
						foreach($groups as $group) {
              $this->Groups[] = $group;
            }
            $this->GroupValue = $groupMinValue[0]['value'];
            $this->UserLogin = $return[0]['login'];
            $this->UserName = $return[0]['name'];
            $this->UserSurname = $return[0]['surname'];
            $this->UserId = $return[0]['uid'];
            $this->Logtime = $return[0]['timestamp'];
            $this->IsLogged = true;
          }
        }
      }
    }
    
    /**
     *
     * Shows login form.
     * C tag.
     * 
     * @param group   group where to look users for
     * @param pageId  page id to redirect after login process
     * @return  html login form     
     *
     */
    public function showLoginForm($group, $pageId) {
      if(!self::isLogged()) {
        if($_POST['login'] == "Log in") {
          global $dbObject;
          global $webObject;
          $username = $_POST['username'];
          $password = sha1($username.$_POST['password']);
          
          $return = $dbObject->fetchAll("SELECT `gid` FROM `group` WHERE `name` = \"".$group."\";");
          $group_id = $return[0]['gid'];
          
          $return = $dbObject->fetchAll("SELECT `user`.`uid`, `user`.`name`, `user`.`surname` FROM `user` LEFT JOIN `user_in_group` ON `user`.`uid` = `user_in_group`.`uid` WHERE `user`.`login` = \"".$username."\" AND `user`.`password` = \"".$password."\" AND `user_in_group`.`gid` = ".$group_id." AND `enable` = 1;");
          if(count($return) == 1) {
            $uid = $return[0]['uid'];
            $this->UserLogin = $username;
            $this->UserName = $return[0]['name'];
            $this->UserSurame = $return[0]['surname'];
            
            $sessionId = rand(100000, 2000000);
            $timestamp = time();
            $dbObject->execute("INSERT INTO `user_log`(`user_id`, `session_id`, `timestamp`, `login_timestamp`, `used_group`) VALUES (".$uid.", ".$sessionId.", ".$timestamp.", ".$timestamp.", \"".$group."\");");
            $return = $dbObject->fetchAll("SELECT `id`, `session_id` FROM `user_log` WHERE `user_id` = ".$uid." AND `session_id` = ".$sessionId.";");
            if(count($return) == 1) {
              $this->LogId = $return[0]['id'];
              $this->SessionId = $return[0]['session_id'];
              $_SESSION[$group.'_session_id'] = $return[0]['session_id'];
              $this->LoggedIn = true;
              
              $link = $webObject->composeUrl($pageId);
              $link = $link;
              header("Location: ".$link);
              $a = $webObject->makeAnchor($pageId, "Redirect");
              echo $a;
              exit;
            } else {
              $message = "Login process failed! Please, try it again.";
            }
          } else {
            $message = "Bad user name or password!";
          }
        }
      
        $return = '<div class="login-form">'.
                    '<form name="login" method="post" action="">'.
                      ((strlen($message) > 0) ? '<p class="login-message">'.$message.'</p>' : '').
                      '<p class="login-head">Login</p>'.
                      '<p class="login-user">'.
                        '<label for="username">Username:</label> '.
                        '<input id="username" type="text" name="username" value="'.$username.'" />'.
                      '</p>'.
                      '<p class="login-passwd">'.
                        '<label for="password">Password:</label> '.
                        '<input id="password" type="password" name="password" value="" />'.
                      '</p>'.
                      '<p class="login-submit">'.
                        '<input type="submit" name="login" value="Log in" />'.
                      '</p>'.
                    '</form>'.
                  '</div>';
        
        return $return;
      } else {
        global $webObject;
              
        $link = $webObject->composeUrl($pageId);
        $link = $link;
        header("Location: ".$link);
              
        $a = $webObject->makeAnchor($pageId, "Redirect");
        echo $a;
        exit;
      }
    }
    
    /**
     *
     *  Shows logout form.
     *  C tag.  
     *  
     *  @param    group     user group
     *  @param    pageId    page id to redirect after logout process
     *  @return   html logout form                  
     *          
     */              
    public function showLogoutForm($group, $pageId) {
      if(self::isLogged()) {
        if($_POST['logout'] == "Log out" || ($this->Logtime + 60 * 15) < time()) {
          // process logout
          global $dbObject;
          global $webObject;
          
          $dbObject->execute("UPDATE `user_log` SET `logout_timestamp` = ".time()." WHERE `session_id` = ".$this->SessionId.";");
          $this->LoggedIn = false;
          $_SESSION[$group.'_session_id'] = '';
          unset($_SESSION[$group.'_session_id']);
          //session_destroy();
          
              
          $link = $webObject->composeUrl($pageId);
          $link = $link;
          header("Location: ".$link);
          
          $a = $webObject->makeAnchor($pageId, "Redirect");
          echo $a;
          exit;
        } else {
          global $dbObject;
	        $this->Logtime = time();
	        $dbObject->execute("UPDATE `user_log` SET `timestamp` = ".$this->Logtime." WHERE `session_id` = ".$this->SessionId.";");
	      }
      
        $return = '<div class="logout-form">'.
                    '<form name="logout" method="post" action="">'.
                      '<p class="logout-submit">'.
                        '<input type="submit" name="logout" value="Log out" />'.
                      '</p>'.
                    '</form>'.
                  '</div>';
                  
        return $return;
      } else {
        global $webObject;
              
        $link = $webObject->composeUrl($pageId);
        $link = $link;
        header("Location: ".$link);
          
        $a = $webObject->makeAnchor($pageId, "Redirect");
        echo $a;
        exit;
      }
    }
    
    /**
     *
     *  Show informations about user.
     *  C tag.     
     *
     */                   
    public function loggedUserInfo() {
      if(self::isLogged()) {
        return '<div class="user-info"><div class="user-name">'.$this->UserName.' '.$this->UserSurname.'</div><div class="user-group">'.$this->UsedGroup['name'].'</div><div class="user-login">'.$this->UserLogin.'@'.$_SERVER['HTTP_HOST'].'</div></div>';
      } else {
        return '<div class="user-info">Not logged!</div>';
      }
    }
    
    /**
     *
     *  Redirect to pageId when user is logged.
     *  C tag.     
     *  
     *  @param  pageId  page id to redirect 
     *  @return none              
     *
     */
    public function redirectWhenLogged($pageId) {
      if(self::isLogged()) {
        global $webObject;
              
        $link = $webObject->composeUrl($pageId);
        $link = $link;
        header("Location: ".$link);
        exit;
      }
    }
    
    /**
     *
     *  Redirect to pageId when user is not logged.
     *  C tag.
     *  
     *  @param  pageId  page id to redirect          
     *  @return none
     *     
     */
    public function redirectWhenNotLogged($pageId) {
      if(!self::isLogged()) {
        global $webObject;
              
        $link = $webObject->composeUrl($pageId);
        $link = $link;
        header("Location: ".$link);
        exit;
      }
    }
    
    /**
     *
     *  Returns true, if user is logged.
     *  
     *  @return is logged flag               
     *
     */              
    public function isLogged() {
      return $this->IsLogged;
    }
    
    /**
     *
     *  Retruns group min value.
     *  
     *  @return group min value
     *
     */                        
    public function getGroupValue() {
      return $this->GroupValue;
    }
    
    /**
     *
     *  Returns user groups as array.
     *  
     *  @return user where user is in
     *
     */                        
    public function getGroups() {
      return $this->Groups;
    }
    
    /**
     *
     *  Returns user groups names as string.
     *  
     *  @return user where user is in
     *
     */                        
    public function getGroupsNamesAsString() {
    	$return = '';
    	$first = true;
    	foreach($this->Groups as $gp) {
    		if($first) {
					$return .= '"'.$gp['name'].'"';
					$first = false;
				} else {
					$return .= ', "'.$gp['name'].'"';
				}
			}
      return $return;
    }
    
    /**
     *
     *  Returns user groups ids as string.
     *  
     *  @return user where user is in
     *
     */                        
    public function getGroupsIdsAsString() {
    	$return = '';
    	$first = true;
    	foreach($this->Groups as $gp) {
    		if($first) {
					$return .= $gp['gid'];
					$first = false;
				} else {
					$return .= ', '.$gp['gid'];
				}
			}
      return $return;
    }
    
    /**
     *
     *  Returns user login.
     *  
     *  @return user login          
     *
     */                   
    public function getUserLogin() {
      return $this->UserLogin;
    }
    
    /**
     *
     *	Returns user id.
     *	
     *	@return	user id
     *
     */		 		 		     
    public function getUserId() {
			return $this->UserId;
		}
		
		/**
		 *
		 *	Returns session id/
		 *	
		 *	@return	session id
		 *	
		 */
		public function getSessionId() {
			return $this->SessionId;
		}
  }

?>
