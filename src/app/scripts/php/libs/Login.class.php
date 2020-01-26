<?php

    require_once("BaseTagLib.class.php");

    /**
     *
     *  Simple login class.
     *  Default object.
     *
     *  @objectname loginObject     
     *      
     *  @author     Marek SMM
     *  @timestamp  2010-08-02
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
         * 	User id.
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
         * 	Used user group for log in.
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
            self::setTagLibXml("Login.xml");

            //self::initLogin();
        }

        /**
         *
         *  Init login.
         *  C tag.
         *
         */
        public function initLogin($group) {
            if (!self::isLogged()) {
                global $dbObject;
                $this->SessionId = $_SESSION[$group . '_session_id'];
                if (is_numeric($this->SessionId)) {
                    $return = $dbObject->fetchAll("SELECT `user`.`uid`, `user`.`group_id`, `user`.`login`, `user`.`name`, `user`.`surname`, `user_log`.`timestamp` FROM `user` LEFT JOIN `user_log` ON `user`.`uid` = `user_log`.`user_id` WHERE `user_log`.`session_id` = " . $this->SessionId . ";");
                    if (count($return) == 1) {
                        $groups = $dbObject->fetchAll("SELECT `group`.`gid`, `group`.`name`, `group`.`value` FROM `user_in_group` LEFT JOIN `group` ON `user_in_group`.`gid` = `group`.`gid` WHERE `uid` = " . $return[0]['uid'] . " ORDER BY `group`.`value`;");
                        $groupMinValue = $dbObject->fetchAll("SELECT MIN(`group`.`value`) AS `value` FROM `group` LEFT JOIN `user_in_group` ON `group`.`gid` = `user_in_group`.`gid` WHERE `uid` = " . $return[0]['uid'] . ";");
                        $this->UsedGroup['name'] = $group;
                        //$this->Groups = array();
                        foreach ($groups as $group) {
                            $this->Groups[] = $group;
                        }
                        $this->GroupValue = $groupMinValue[0]['value'];
                        $this->UserLogin = $return[0]['login'];
                        $this->UserName = $return[0]['name'];
                        $this->UserSurname = $return[0]['surname'];
                        $this->UserId = $return[0]['uid'];
                        $this->Logtime = $return[0]['timestamp'];
                        $this->IsLogged = true;
                        $this->UserGroup = $return[0]['group_id'];
                    }
                }
            }
        }

        private function inGroup($groupName) {
            $groups = self::getGroups();
            foreach ($groups as $assignedGroup) {
                if ($groupName == $assignedGroup["name"]) {
                    return true;
                }
            }

            return false;
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
        public function showLoginForm($group, $pageId, $autoLoginUser = false, $autoLoginPasswd = false) {
            if (!self::isLogged()) {
                if ($_POST['login'] != "Log in" && !array_key_exists('auto-login-ignore', $_REQUEST) && $autoLoginUser != false && $autoLoginPasswd != false) {
                    $_POST['username'] = $autoLoginUser;
                    $_POST['password'] = $autoLoginPasswd;
                    $_POST['login'] = "Log in";
                }

                $message = "";
                if ($_POST['login'] == "Log in") {
                    $message = self::loginPrivate($group, $_POST['username'], $_POST['password']);
                    if ($message === true) {
                        parent::web()->redirectTo($pageId);
                    }
                }

                $return = ''
                .'<div class="login-form">'
                    . '<form name="login" method="post" action="' . $_SERVER['REQUEST_URI'] . (array_key_exists('auto-login-ignore', $_REQUEST) ? '?auto-login-ignore' : '') . '">'
                        . ((strlen($message) > 0) ? '<p class="login-message">' . $message . '</p>' : '')
                        . '<p class="login-head">Login</p>' 
                        . '<p class="login-user">' 
                            . '<label for="username">Username:</label> ' 
                            . '<input id="username" type="text" name="username" value="' . $_POST['username'] . '" />' 
                        . '</p>' 
                        . '<p class="login-passwd">' 
                            . '<label for="password">Password:</label> ' 
                            . '<input id="password" type="password" name="password" value="" />' 
                        . '</p>' 
                        . '<p class="login-submit">' 
                            . '<input type="submit" name="login" value="Log in" />' 
                        . '</p>' 
                    . '</form>' 
                . '</div>';

                return $return;
            } else {
                parent::web()->redirectTo($pageId);
            }
        }

        public function loginLookless($template, $group, $username, $password) {
            $message = self::loginPrivate($group, $username, $password);
            if ($message === true) {
                parent::parseContent($template);
            }
        }

        public function loginPrivate($group, $username, $password) {
            $db = parent::dataAccess();

            $password = sha1($username . $password);

            $return = $db->fetchAll(parent::sql()->select("group", array("gid"), array("name" => $group)));
            $group_id = $return[0]['gid'];
            $return = $db->fetchAll('SELECT `user`.`uid`, `user`.`name`, `user`.`surname` FROM `user` LEFT JOIN `user_in_group` ON `user`.`uid` = `user_in_group`.`uid` WHERE `user`.`login` = "' . $db->escape($username) . '" AND `user`.`password` = "' . $db->escape($password) . '" AND `user_in_group`.`gid` = ' . $group_id . ' AND `enable` = 1;');
            if (count($return) == 1) {
                $uid = $return[0]['uid'];
                $this->UserLogin = $username;
                $this->UserName = $return[0]['name'];
                $this->UserSurame = $return[0]['surname'];

                $sessionId = rand(100000, 2000000);
                $timestamp = time();
                $userLogSql = parent::sql()->insert("user_log", array("user_id" => $uid, "session_id" => $sessionId, "timestamp" => $timestamp, "login_timestamp" => $timestamp, "used_group" => $group));
                $db->execute($userLogSql);
                $return = $db->fetchAll(parent::sql()->select("user_log", array("id", "session_id"), array("user_id" => $uid, "session_id" => $sessionId)));
                if (count($return) == 1) {
                    $this->LogId = $return[0]['id'];
                    $this->SessionId = $return[0]['session_id'];
                    $_SESSION[$group . '_session_id'] = $return[0]['session_id'];
                    $this->LoggedIn = true;

                    return true;
                } else {
                    return "Login process failed! Please, try it again.";
                }
            } else {
                return "Bad user name or password!";
            }

            return false;
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
            if (!self::refreshPrivate($group)) {
                parent::web()->redirectTo($pageId);
            }

            return ''
            . '<div class="logout-form">'
                . '<form name="logout" method="post" action="' . $_SERVER['REQUEST_URI'] . (array_key_exists('auto-login-ignore', $_REQUEST) ? '?auto-login-ignore' : '') . '">'
                    . '<p class="logout-submit">'
                        . '<input type="submit" name="logout" value="Log out" />'
                    . '</p>'
                . '</form>'
            . '</div>';
        }

        public function logout($template, $group) {
            $sql = parent::sql()->update("user_log", array("logout_timestamp" => time()), array("session_id" => $this->SessionId));
            parent::db()->execute($sql);
            $this->LoggedIn = false;

            $sessionId = $group . '_session_id';
            $_SESSION[$sessionId] = '';
            unset($_SESSION[$sessionId]);

            parent::parseContent($template);
        }

        public function refresh($template, $group) {
            if (!self::refreshPrivate($group)) {
                parent::parseContent($template);
            }
        }
        
        public function refreshPrivate($group) {
            if (self::isLogged()) {
                require_once('System.class.php');
                $name = 'Login.session';
                $sessionTime = parent::system()->getPropertyValue($name);

                if ($sessionTime < 0) {
                    $sessionTime = 15;
                }

                if ($_POST['logout'] == "Log out" || ($this->Logtime + 60 * $sessionTime ) < time()) {
                    self::logout("", $group);
                    return false;
                    parent::web()->redirectTo($pageId);
                } else {
                    $this->Logtime = time();
                    $sql = parent::sql()->update("user_log", array("timestamp" => $this->Logtime), array("session_id" => $this->SessionId));
                    parent::db()->execute($sql);
                    return true;
                }
            } else {
                return false;
            }
        }

        public function authorized($template, $all = "", $any = "", $none = "") {
            if ($all == "" && $any == "" && $none == "") {
                return parent::parseContent($template);
            }
            
            if ($all != "") {
                $all = explode(",", $all);
                foreach ($all as $group) {
                    if (!self::inGroup($group)) {
                        return "";
                    }
                }

                return parent::parseContent($template);
            }

            if ($any != "") {
                $any = explode(",", $any);
                foreach ($any as $group) {
                    if (self::inGroup($group)) {
                        return parent::parseContent($template);
                    }
                }

                return "";
            }

            if ($none != "") {
                $none = explode(",", $none);
                foreach ($none as $group) {
                    if (self::inGroup($group)) {
                        return "";
                    }
                }
                
                return parent::parseContent($template);
            }
        }

        /**
         *
         *  Show informations about user.
         *  C tag.     
         *
         */
        public function loggedUserInfo($field = '') {
            if (self::isLogged()) {
                $login = $this->UserLogin . '@' . $_SERVER['HTTP_HOST'];

                if ($field == "name") {
                    return $this->UserName;
                } else if ($field == "surname") {
                    return $this->UserSurname;
                } else if ($field == "group") {
                    return $this->UsedGroup['name'];
                } else if ($field == "login") {
                    return $login;
                } else if ($field == "username") {
                    return $this->UserLogin;
                }
                return '<div class="user-info"><div class="user-name">' . $this->UserName . ' ' . $this->UserSurname . '</div><div class="user-group">' . $this->UsedGroup['name'] . '</div><div class="user-login">' . $login . '</div></div>';
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
            if (self::isLogged()) {
                parent::web()->redirectTo($pageId);
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
            if (!self::isLogged()) {
                parent::web()->redirectTo($pageId);
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
            foreach ($this->Groups as $gp) {
                if ($first) {
                    $return .= '"' . $gp['name'] . '"';
                    $first = false;
                } else {
                    $return .= ', "' . $gp['name'] . '"';
                }
            }
            return $return;
        }
        
        public function getGroupsIds() {
            $return = array();
            $i = 0;
            foreach ($this->Groups as $gp) {
                $return[$i] = $gp['gid'];
                $i++;
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
            foreach ($this->Groups as $gp) {
                if ($first) {
                    $return .= $gp['gid'];
                    $first = false;
                } else {
                    $return .= ', ' . $gp['gid'];
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
         * 	Returns user id.
         * 	
         * 	@return	user id
         *
         */
        public function getUserId() {
            return $this->UserId;
        }

        public function getMainGroupId() {
            return $this->UserGroup;
        }

        /**
         *
         * 	Not implemented yet!
         * 	
         * 	@return	user id
         *
         */
        public function setUserId($uid) {
            return $uid;
        }

        /**
         *
         * 	Returns session id/
         *
         * 	@return	session id
         *
         */
        public function getSessionId() {
            return $this->SessionId;
        }
    }

?>
