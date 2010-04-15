<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  
  /**
   * 
   *  Class Guestbook.
   *      
   *  @author     Marek SMM
   *  @timestamp  2009-04-24
   * 
   */  
  class Guestbook extends BaseTagLib {                      
  
    public function __construct() {
      parent::setTagLibXml("xml/Guestbook.xml");
    }
    
    /**
     *
     *  Generates input form for adding entry.
     *  C tag.     
     *  
     *  @param    guestbookId   guestbook id
     *  @param    parentId      parent entry id
     *  @param    pageId        id of page to redirect after submit form      
     *  @param    smilies       if true, it shows smilies                   
     *  @return   input form
     *
     */                             
    public function showForm($guestbookId = false, $parentId = false, $pageId = false, $smilies = false) {
      global $webObject;
      global $dbObject;
      $return = "";
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
      
      if($_POST['guestbook-send'] == "Send") {
      	if ($_POST['guestbook-name'] != "" && $_POST['guestbook-content'] != "") {
        	self::processInput();
        } else {
					$return .= ''
					.'<div class="error">You have to fill name & content!</div>';
				}
        
        $return .= ''
        .'<div class="guestbook-message">'
          .'Added!'
        .'</div>';
        
        if($pageId !== false) {
          $link = $webObject->composeUrl($pageId);
          
          header("Location: ".$link);
          echo '<a href="'.$link.'">Redirect</a>';
          exit;
        }
      }
      
      $return .= ''
      .'<div class="guestbook-input">'
        .'<form name="guestbook-input" method="post" action="">'
          .'<p class="guestbook-name">'
            .'<label for="guestbook-name">Name:</label>'
            .'<input type="text" name="guestbook-name" id="guestbook-name" value="" />'
          .'</p>'
          .'<p class="guestbook-content">'
            .'<label for="guestbook-name">Content:</label>'
            .'<textarea name="guestbook-content" id="guestbook-content" rows="5" cols="20"></textarea>'
          .'</p>'
          .'<p class="guestbook-send">'
            .'<input type="hidden" name="guestbook-gbid" value="'.$guestbookId.'" />'
            .'<input type="hidden" name="guestbook-parent" value="'.$parentId.'" />'
            .'<input type="submit" name="guestbook-send" value="Send" />'
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
      $name = $_POST['guestbook-name'];
      $content = $_POST['guestbook-content'];
      $guestbookId = $_POST['guestbook-gbid'];
      $parentId = $_POST['guestbook-parent'];
      
      $dbObject->execute("INSERT INTO `guestbook`(`parent_id`, `name`, `content`, `timestamp`, `guestbook_id`) VALUES (".$parentId.", \"".$name."\", \"".$content."\", ".time().", ".$guestbookId.");");
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
      $return = "";
      if($guestbookId == false) {
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
        return parent::getFrame("Guestbook Management - ".$guestbookId, $return, "");
      } else {
        return $return;
      }
    }
    
    private function printRows($guestbookId, $editable, $answer, $answerPageId, $parentId) {
      global $webObject;
      global $dbObject;
      $rows = $dbObject->fetchAll("SELECT `id`, `name`, `content`, `timestamp` FROM `guestbook` WHERE `guestbook_id` = ".$guestbookId." AND `parent_id` = ".$parentId." ORDER BY `timestamp` DESC;");
      
      if($answerPageId == false) {
        $answerHref = $_SERVER['REDIRECT_URL'];
      } else {
        $answerHref = $webObject->composeUrl($answerPageId);
      }
      
      foreach($rows as $row) {
        $return .= ''
        .'<div class="guestbook-line">'
          .'<div class="guestbook-head">'
            .(($editable == "true") ? ''
            .'<div class="guestbook-editable">'
              .'<form name="guestbook-editable" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
                .'<input type="hidden" name="guestbook-editable-gbid" value="'.$guestbookId.'" />'
                .'<input type="hidden" name="guestbook-editable-id" value="'.$row['id'].'" />'
                .'<input type="hidden" name="guestbook-editable-delete" value="Delete Entry" />'
                .'<input title="Delete Entry" class="confirm" type="image" src="'.WEB_ROOT.'images/page_del.png" name="guestbook-editable-delete" value="Delete Entry" />'
              .'</form>'
            .'</div>' 
            : '')
            .(($answer == "true") ? ''
            .'<div class="guestbook-answer">'
              .'<form name="guestbook-answer" method="post" action="'.$answerHref.'">'
                .'<input type="hidden" name="guestbook-parent-id" value="'.$row['id'].'" />'
                .'<input type="hidden" name="guestbook-editable-add-answer" value="Add Answer" />'
                .'<input title="Add Answer" type="image" src="'.WEB_ROOT.'images/page_add.png" name="guestbook-editable-add-answer" value="Add Answer" />'
              .'</form>'
            .'</div>'  
            : '')
            .'<div class="guestbook-timestamp">'
              .'<span class="guestbook-time">'.date("H:i:s", $row['timestamp']).'</span> '
              .'<span class="guestbook-date">'.date("d:m:Y", $row['timestamp']).'</span>'
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
  }
  
?>