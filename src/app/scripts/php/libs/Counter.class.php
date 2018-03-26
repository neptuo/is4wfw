<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FullTagParser.class.php");  

    /**
     * 
     *  Class Counter
     *  User access counter.      
     *      
     *  @author     Marek SMM
     *  @timestamp  2009-07-02
     * 
     */  
    class Counter extends BaseTagLib {                      

        public function __construct() {
            parent::setTagLibXml("Counter.xml");
        }

        /**
         *
         *  Counts user access.
         *  C tag.
         *  
         *  @param    id    counter id
         *  @param    every time for repeated counting                    
         *  @return   none
         *
         */                        
        public function countAccess($id = false, $every = false) {
            global $dbObject;
            $userIp = $_SERVER['REMOTE_ADDR'];

            if ($id == false) {
                $id = 1;
            }

            if ($every == false) {
                $every = "day";
            }

            $lastTime = time();
            switch ($every) {
                case "every": $lastTime = time(); break;
                case "minute": $lastTime = time() - (60); break;
                case "hour": $lastTime = time() - (60 * 60); break;
                case "day": $lastTime = mktime(0, 0, 0, date("n"), date("j"), date("Y")); break;
                case "week": $lastTime = time() - (60 * 60 * 24 * 7); break;
            }

            $counterItem = $dbObject->fetchAll("SELECT `timestamp`, `count` FROM `counter` WHERE `ip` = \"".$userIp."\" AND `counter_id` = ".$id.";");
            if (count($counterItem) > 0) {
                if ($counterItem[0]['timestamp'] > $lastTime) {
                    $dbObject->execute("UPDATE `counter` SET `timestamp` = ".time()." WHERE `ip` = \"".$userIp."\" AND `counter_id` = ".$id.";");
                } else {
                    $dbObject->execute("UPDATE `counter` SET `timestamp` = ".time().", `count` = ".($counterItem[0]['count'] + 1)." WHERE `ip` = \"".$userIp."\" AND `counter_id` = ".$id.";");
                }
            } else {
                $dbObject->execute("INSERT INTO `counter`(`ip`, `timestamp`, `count`, `counter_id`) VALUES (\"".$userIp."\", ".time().", 1, ".$id.");");
            }
        }

        /**
         *
         *  Prints values from table counter to template and return it.
         *  
         *  @param    template  		template for displaying (deprecated)
         *  @param    templateId  	template for displaying (using dynamic templates from cms)
         *  @param    id          	counter id
         *  @return   values from 	table counter in template          
         *
         */                   
        public function showTable($template = false, $templateId = false, $id = false, $leadingZeros = false) {
            global $dbObject;
            global $loginObject;
            $userIp = $_SERVER['REMOTE_ADDR'];
            $return = "";
            
            if ($id == false) {
                $id = 1;
            }
            
            $templateContent = '';
            if ($templateId != false) {
            // ziskani templatu ...
                $rights = $dbObject->fetchAll('SELECT `value` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = '.$templateId.' AND `template_right`.`type` = '.WEB_R_READ.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().'));');
                if (count($rights) > 0 && $templateId > 0) {
                    $template = $dbObject->fetchAll('SELECT `content` FROM `template` WHERE `id` = '.$templateId.';');
                    $templateContent = $template[0]['content'];
                } else {
                    $message = "Permission Denied when reading template[templateId = ".$templateId."]!";
                    trigger_error($message, E_USER_WARNING);
                    return;
                }
            } elseif($template != false) {
                if (is_file($template) && is_readable($template)) {
                    $templateContent = file_get_contents($template);
                } else {
                    $message = "Template file doesn't exist or is un-readable!";
                    trigger_error($message, E_USER_WARNING);
                    return;
                }
            } else {
                $message = "Template or TemplateId must be set!";
                trigger_error($message, E_USER_WARNING);
                return;
            }
                
            $cols = $dbObject->fetchAll("SELECT `ip`, `timestamp`, `count` FROM `counter` WHERE `counter_id` = ".$id.";");
            $_SESSION['counter'] = array();
            $_SESSION['counter']['all'] = 0;
            $_SESSION['counter']['visitors'] = 0;
            $_SESSION['counter']['visitors-week'] = 0;
            $_SESSION['counter']['visitors-today'] = 0;
            $_SESSION['counter']['visitors-hour'] = 0;
            $_SESSION['counter']['visitors-online'] = 0;
            $_SESSION['counter']['user'] = 0;
            $_SESSION['counter']['user-week'] = 0;
            $_SESSION['counter']['user-today'] = 0;
            $_SESSION['counter']['user-hour'] = 0;        
            foreach($cols as $col) {
                $_SESSION['counter']['all'] += $col['count'];
                $_SESSION['counter']['visitors'] ++;
                if ($col['ip'] == $userIp) {
                    $_SESSION['counter']['user'] += $col['count'];	
                }
                if ($col['timestamp'] > (time() - 60 * 4)) {
                    $_SESSION['counter']['visitors-online'] ++;
                }
                if ($col['timestamp'] > (time() - 60 * 60)) {
                    $_SESSION['counter']['visitors-hour'] ++;
                    if ($col['ip'] == $userIp) {
                        $_SESSION['counter']['user-hour'] ++;
                    }
                }
                if (date("Y.m.d") == date("Y.m.d", $col['timestamp'])) {
                    $_SESSION['counter']['visitors-today'] ++;
                    if ($col['ip'] == $userIp) {
                        $_SESSION['counter']['user-today'] ++;
                    }
                }
                if ($col['timestamp'] > (time() - 60 * 60 * 24 * 7)) {
                    $_SESSION['counter']['visitors-week'] ++;
                    if ($col['ip'] == $userIp) {
                        $_SESSION['counter']['user-week'] ++;
                    }
                }
            }
            
            if ($leadingZeros != false && $leadingZeros > 0 && $leadingZeros <= 10) {
                foreach ($_SESSION['counter'] as $key => $value) {
                    for ($j = 0; $j < $leadingZeros; $j ++) {
                        if (strlen($_SESSION['counter'][$key]) <= $j) {
                            $_SESSION['counter'][$key] = '0'.$_SESSION['counter'][$key];
                        }
                    }
                }
            }
            
            $Parser = new FullTagParser();
            $Parser->setContent($templateContent);
            $Parser->startParsing();
            $return .= $Parser->getResult();
            
            return $return;
        }

        public function redirectWhenTimeLimitExceeded($pageId, $counterId = false, $every = false) {
            global $webObject;
            global $dbObject;
            $userIp = $_SERVER['REMOTE_ADDR'];
            
            if ($counterId == false) {
                $counterId = 1;
            }
            
            if ($every == false) {
                $every = "day";
            }
            
            $counterItem = $dbObject->fetchAll("SELECT `timestamp` FROM `counter` WHERE `ip` = \"".$userIp."\" AND `counter_id` = ".$counterId.";");
            if (count($counterItem) > 0) {
                $lastTime = time();
                switch ($every) {
                    case "every": $lastTime = time(); break;
                    case "minute": $lastTime = time() - (60); break;
                    case "hour": $lastTime = time() - (60 * 60); break;
                    case "day": $lastTime = time() - (60 * 60 * 24); break;
                    case "week": $lastTime = time() - (60 * 60 * 24 * 7); break;
                }
                
                if ($counterItem[0]['timestamp'] < $lastTime) {
                    $webObject->redirectTo($pageId);
                }
            }
        }

        public function redirectWhenTimeLimitNotExceeded($pageId, $counterId = false, $every = false) {
            global $webObject;
            global $dbObject;
            $userIp = $_SERVER['REMOTE_ADDR'];
            
            if ($counterId == false) {
                $counterId = 1;
            }

            if ($every == false) {
                $every = "day";
            }
            
            $counterItem = $dbObject->fetchAll("SELECT `timestamp` FROM `counter` WHERE `ip` = \"".$userIp."\" AND `counter_id` = ".$counterId.";");
            if (count($counterItem) > 0) {
                $lastTime = time();
                switch($every) {
                    case "every": $lastTime = time(); break;
                    case "minute": $lastTime = time() - (60); break;
                    case "hour": $lastTime = time() - (60 * 60); break;
                    case "day": $lastTime = time() - (60 * 60 * 24); break;
                    case "week": $lastTime = time() - (60 * 60 * 24 * 7); break;
                }
                
                if ($counterItem[0]['timestamp'] > $lastTime) {
                    $webObject->redirectTo($pageId);
                }
            }
        }

        public function showAll() {
            return $_SESSION['counter']['all'];
        }

        public function showVisitors() {
            return $_SESSION['counter']['visitors'];
        }

        public function showVisitorsWeek() {
            return $_SESSION['counter']['visitors-week'];
        }

        public function showVisitorsToday() {
            return $_SESSION['counter']['visitors-today'];
        }

        public function showVisitorsHour() {
            return $_SESSION['counter']['visitors-hour'];
        }

        public function showVisitorsOnline() {
            return $_SESSION['counter']['visitors-online'];
        }

        public function showUser() {
            return $_SESSION['counter']['user'];
        }

        public function showUserWeek() {
            return $_SESSION['counter']['user-week'];
        }

        public function showUserToday() {
            return $_SESSION['counter']['user-today'];
        }

        public function showUserHour() {
            return $_SESSION['counter']['user-hour'];
        }
    }
  
?>