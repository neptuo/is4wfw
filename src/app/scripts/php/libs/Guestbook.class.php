<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");

    /**
     * 
     *  Class Guestbook.
     *      
     *  @author     Marek SMM
     *  @timestamp  2012-11-06
     * 
     */  
    class Guestbook extends BaseTagLib {

        private $GuestbookId = 0;

        public function __construct() {
            self::setTagLibXml("Guestbook.xml");
            self::setLocalizationBundle("guestbook");
        }
        
        /**
         *
         *  Generates input form for adding entry.
         *  C tag.     
         *  
         *  @param    guestbookId   guestbook id
         *  @param    parentId      parent entry id
         *  @param    pageId        id of page to redirect after submit form                   
         *  @return   input form
         *
         */                             
        public function showForm($guestbookId = false, $parentId = false, $pageId = false) {
            global $webObject;
            global $dbObject;
            
            $rb = self::rb();
            
            $return = "";
            $message = "";
            $ok = true;
            if($guestbookId == false) {
                $guestbookId = 1;
            }

            if($parentId == false) {
                if(array_key_exists('guestbook-parent-id', $_POST)) {
                    $parentId = $_POST['guestbook-parent-id'];
                } else {
                    $parentId = 0;
                }
            }
            
            if($_POST['guestbook-send'] == $rb->get('gb.label.send')) {
                if ($_POST['guestbook-name'] != "" && $_POST['guestbook-content'] != "" && sha1($_POST['guestbook-formula']) == $_POST['guestbook-formularesult']) {
                    self::processInput();
                
                    $message .= ''
                    .'<div class="guestbook-message">'
                    .$rb->get('gb.success.addded')
                    .'</div>';
                } else {
                    $message .= ''
                    .'<div class="guestbook-error">'.$rb->get('gb.error.fillfields').'</div>';
                    $ok = false;
                }
                
                if($pageId != "") {
                    $webObject->redirectTo($pageId);
                }
            }
            
            $name = $ok ? "" : $_POST['guestbook-name'];
            $content = $ok ? "" : $_POST['guestbook-content'];
            
            $a = rand(1, 9);
            $b = rand(1, 9);
            $formula = $rb->get('gb.formula.'.$a).' plus '.$rb->get('gb.formula.'.$b);
            $r = $a + $b;
            $hash = sha1($r);
            
            $return .= ''
            .'<script type="text/javascript" src="~/js/web/guestbook.js"></script>'
            .'<div class="guestbook-input">'
                .$message
                .(($parentId == 0) ? "" : '<p id="guestbook-answer-notify" class="guestbook-answer-notify">'.$rb->get('gb.message.answernotify').' <a href="'.$rb->get('gb.label.cancel').'" onclick="cancelAnswer(); return false;">'.$rb->get('gb.label.cancel').'</a></p>')
                .'<form name="guestbook-input" method="post" action="">'
                    .'<p class="guestbook-name">'
                    .'<label for="guestbook-name">'.$rb->get('gb.label.name').':</label> '
                    .'<input type="text" name="guestbook-name" id="guestbook-name" value="'.$name.'" />'
                    .'</p>'
                    .'<p class="guestbook-content">'
                    .'<label for="guestbook-content">'.$rb->get('gb.label.content').':</label> '
                    .'<textarea name="guestbook-content" id="guestbook-content" rows="5" cols="20">'.$content.'</textarea>'
                    .'</p>'
                    .'<p class="guestbook-formula">'
                    .'<label for="guestbook-formula">'.$rb->get('gb.label.formula').':</label> '
                    .'<span class="guestbook-formulaset">'.$formula.'</spa> '
                    .'<input type="text" name="guestbook-formula" id="guestbook-formula" value="" />'
                    .'</p>'
                    .'<p class="guestbook-send">'
                    .'<input type="hidden" name="guestbook-gbid" value="'.$guestbookId.'" />'
                    .'<input type="hidden" name="guestbook-parent" id="guestbook-parent" value="'.$parentId.'" />'
                    .'<input type="hidden" name="guestbook-formularesult" value="'.$hash.'" />'
                    .'<input type="submit" name="guestbook-send" value="'.$rb->get('gb.label.send').'" />'
                    .'</p>'
                .'</form>'
            .'</div>';
            
            return $return;
        }
        
        /**
         *
         *  Work up function for input form
         *       
         *  @return none
         *
         */                        
        private function processInput() {
            global $dbObject;
            $name = strip_tags($_POST['guestbook-name']);
            $content = strip_tags($_POST['guestbook-content']);
            $guestbookId = $_POST['guestbook-gbid'];
            $parentId = $_POST['guestbook-parent'];
            
            $dbObject->execute("INSERT INTO `guestbook`(`parent_id`, `name`, `content`, `timestamp`, `guestbook_id`) VALUES (".$parentId.", \"".$name."\", \"".$content."\", ".time().", ".$guestbookId.");");
        }
        
        /**
         *
         *	Shows list of all guestbooks
            *
            *	@param		useFrame				if true, it formats output to frames     
            *
            */		 		 		 		     
        public function showListOfGuestbooks($useFrame = false) {
            global $dbObject;
            $return = '';
            
            if($_POST['gb-delete'] == 'Delete guestbook') {
                $id = $_POST['gb-delete-id'];
                $dbObject->execute('delete from `guestbook` where `guestbook_id` = '.$id.';');
                $return .= '<h4 class="success">Messages from guestbook.id='.$id.' has been deleted!</h4>';
            }
            
            $ids = self::selectDistinctGuestbookIds();
            
            if(count($ids) > 0) {
                $return .= ''
                .'<table class="guestbook-list">'
                    .'<tr>'
                    .'<th class="gb-id">Id</th>'
                    .'<th class="gb-action">Action</th>'
                    .'</tr>';
                $i = 1;
                foreach($ids as $id) {
                    $return .= ''
                    .'<tr class="gb-line '.((($i % 2) == 0) ? 'even' : 'idle').'">'
                    .'<td class="gb-id">'.$id.'</td>'
                    .'<td class="gb-action">'
                        .'<form name="gb-action" method="post" action="">'
                        .'<a href="?gb-id='.$id.'">Open detail</a> '
                        .'<input type="hidden" name="gb-delete-id" value="'.$id.'" />'
                        .'<input class="confirm" type="image" src="~/images/page_del.png" name="gb-delete" value="Delete guestbook" title="Delete guestbook, id('.$id.')" />'
                        .'</form>'
                    .'</td>'
                    .'</tr>';
                    $i ++;
                }
                
                $return .= ''
                .'</table>';
            } else {
                $return .= '<h4 class="warning">No guestbooks.</h4>';
            }
            
            if($useFrame == "false") {
                return $return;
            } else {
                if($return != '') {
                    return parent::getFrame('List of guestbooks', $return, "", true);
                }
            }
        }
        
        /**
         *
         *	Select distinct guesbook_id.
            *		      
            */		     
        private function selectDistinctGuestbookIds() {
            global $dbObject;
            $ret = array();
            $ret = $dbObject->fetchAll('select distinct `guestbook_id` from `guestbook` order by `guestbook_id`');
            $ids = array();
            foreach($ret as $id) {
                $ids[] = $id['guestbook_id'];
            }
            return $ids;
        }
        
        /**
         *
         *	Setups id of selected guestbook from list.
            *
            */		 		 		     
        public function setIdFromList() {
            if($_GET['gb-id'] != '') {
                $this->GuestbookId = $_GET['gb-id'];
            }
        }
        
        /**
         *
         *  Shows entries in guestbook.
         *  C tag.
         *  
         *  @param    guestbookId   guestbook id
         *  @param    editable      if true, it shows form form editing
         *  @param    answer        if true, user is allowed for adding answers to entries
         *  @param    answerPageId  page id for input form when answering
         *  @param    useFrame      use frame in output     
         *  @return   list of entries                              
         *
         */                        
        public function showGuestbook($guestbookId = false, $editable = false, $answer = false, $answerPageId = false, $useFrame = false) {
            global $webObject;
            global $dbObject;
            
            $rb = self::rb();
            
            $return = "";
            if($guestbookId == 'false') {
                $guestbookId = 1;
            }
            
            if($_POST['guestbook-editable-delete'] == "Delete Entry") {
                self::processDeleteEntry();
            }
            
            if($answerPageId == false) {
                $answerHref = "";
            } else {
                $answerHref = $webObject->composeUrl($answerPageId);
            }
            
            $return .= '<div class="guestbook-show">';
            $return .= self::printRows($guestbookId, $editable, $answer, $answerPageId, 0);
            $return .= '</div>';
            
            if($useFrame == "true") {
                return parent::getFrame("Guestbook Management - GuestbookId = ".$guestbookId, $return, "");
            } else {
                return $return;
            }
        }
        
        private function printRows($guestbookId, $editable, $answer, $answerPageId, $parentId) {
            global $webObject;
            global $dbObject;
            
            $rb = self::rb();
            
            $rows = $dbObject->fetchAll("SELECT `id`, `name`, `content`, `timestamp` FROM `guestbook` WHERE `guestbook_id` = ".$guestbookId." AND `parent_id` = ".$parentId." ORDER BY `timestamp` DESC;");
            
            if($answerPageId == false) {
                $answerHref = $_SERVER['REQUEST_URI'];
            } else {
                $answerHref = $webObject->composeUrl($answerPageId);
            }
            
            if(count($rows) == 0 && $parentId == 0) {
                $return .= '<h4 class="warning">'.$rb->get('gb.message.empty').'</h4>';
            }
            
            $i = 1;
            foreach($rows as $row) {
                $return .= ''
                .'<div class="guestbook-line number-'.$i.' '.((($i % 2) == 0) ? 'even' : 'idle').'">'
                    .'<div class="guestbook-head">'
                    .(($editable == "true") ? ''
                    .'<div class="guestbook-editable">'
                        .'<form name="guestbook-editable" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
                        .'<input type="hidden" name="guestbook-editable-gbid" value="'.$guestbookId.'" />'
                        .'<input type="hidden" name="guestbook-editable-id" value="'.$row['id'].'" />'
                        .'<input type="hidden" name="guestbook-editable-delete" value="Delete Entry" />'
                        .'<input title="Delete Entry" class="confirm" type="image" src="~/images/page_del.png" name="guestbook-editable-delete" value="Delete Entry" />'
                        .'</form>'
                    .'</div>' 
                    : '')
                    .(($answer == "true") ? ''
                    .'<div class="guestbook-answer">'
                        .'<form name="guestbook-answer" method="post" action="'.$answerHref.'">'
                        .'<input type="hidden" name="guestbook-parent-id" value="'.$row['id'].'" />'
                        .'<input type="hidden" name="guestbook-editable-add-answer" value="Add Answer" />'
                        .'<input title="'.$rb->get('gb.label.addanswer').'" type="image" src="~/images/page_add.png" name="guestbook-editable-add-answer" value="'.$rb->get('gb.label.addanswer').'" />'
                        .'</form>'
                    .'</div>'  
                    : '')
                    .'<div class="guestbook-timestamp">'
                        .'<span class="guestbook-time">'.date("H:i:s", $row['timestamp']).'</span> '
                        .'<span class="guestbook-date">'.date("d.m.Y", $row['timestamp']).'</span>'
                    .'</div>'
                    .'<div class="guestbook-name">'.$row['name'].'</div>'
                    .'<div class="guestboo-clear clear"></div>'
                    .'</div>'
                    .'<div class="guestbook-content">'.$row['content'].'</div>'
                    .'<div class="guestbook-clear clear"></div>'
                    //.(($answer == "true") ? ''
                    .'<div class="guestbook-answers">'
                    .self::printRows($guestbookId, $editable, $answer, $answerPageId, $row['id'])
                    .'</div>'
                    //: '')
                .'</div>';
                $i ++;
            }
            
            return $return;
        }
        
        /**
         *
         *  Work up function showGuestbook edit.
         *  
         *  @return   none          
         *
         */                   
        private function processDeleteEntry() {
            global $dbObject;
            
            $entryId = $_POST['guestbook-editable-id'];
            $guestbookId = $_POST['guestbook-editable-gbid'];
            $dbObject->execute("DELETE FROM `guestbook` WHERE `parent_id` = ".$entryId." AND `guestbook_id` = ".$guestbookId.";");
            $dbObject->execute("DELETE FROM `guestbook` WHERE `id` = ".$entryId." AND `guestbook_id` = ".$guestbookId.";");
        }
        
        /* ================== PROPERTIES ================================================== */
        
        public function setGuestbookId($id) {
            $this->GuestbookId = $id;
            return $GuestbookId;
        }
        
        public function getGuestbookId() {
            return $this->GuestbookId;
        }
    }
  
?>