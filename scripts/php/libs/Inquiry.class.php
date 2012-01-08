<?php

/**
 *
 *  Require base tag lib class.
 *
 */
require_once("BaseTagLib.class.php");

require_once("scripts/php/classes/ResourceBundle.class.php");

/**
 * 
 *  Class Inquery.
 *      
 *  @author     Marek SMM
 *  @timestamp  2012-01-06
 * 
 */
class Inquiry extends BaseTagLib {

    private $BundleName = 'inquiry';
    private $BundleLang = 'cs';

    public function __construct() {
        global $webObject;

        parent::setTagLibXml("xml/Inquiry.xml");

        if ($webObject->LanguageName != '') {
            $rb = new ResourceBundle();
            if ($rb->testBundleExists($this->BundleName, $webObject->LanguageName)) {
                $this->BundleLang = $webObject->LanguageName;
            }
        }
    }
	
	/* ================== ADMIN ======================================================= */
	
	public function getInquiries($useFrames = false) {
		$rb = new ResourceBundle();
		$rb->loadBundle($this->BundleName, $this->BundleLang);
		$return = '';
		
		if($_POST['inquiry-delete'] == $rb->get('button.delete')) {
			$id = $_POST['inquiry-id'];
			parent::db()->execute('delete from `inquiry_answer` where `inquiry_id` = '.$id.';');
			parent::db()->execute('delete from `inquiry_vote` where `inquiry_id` = '.$id.';');
			parent::db()->execute('delete from `inquiry` where `id` = '.$id.';');
			$return .= parent::getSuccess($rb->get('message.deleted'));
		}
		
		$data = parent::db()->fetchAll('select `id`, `question`, `enabled`, `allow_multiple`, (select count(`id`) from `inquiry_answer` where `inquiry_id` = `i`.`id`) as `answer_count`, (select count(`id`) from `inquiry_vote` where `inquiry_id` = `i`.`id`) as `vote_count` from `inquiry` as `i` order by `id`');
		if(count($data) > 0) {
			$return .= ''
			.'<table class="standart">'
				.'<tr>'
					.'<th>'.$rb->get('label.id').'</th>'
					.'<th>'.$rb->get('label.question').'</th>'
					.'<th>'.$rb->get('label.enabled').'</th>'
					.'<th>'.$rb->get('label.allowmultiple').'</th>'
					.'<th>'.$rb->get('label.answercount').'</th>'
					.'<th>'.$rb->get('label.votecount').'</th>'
					.'<th></th>'
				.'<tr>';
			
			foreach ($data as $i => $item) {
				$return .= ''
				.'<tr class="' . ((($i % 2) == 0) ? 'idle' : 'even') . '">'
					.'<td>'.$item['id'].'</td>'
					.'<td>'.$item['question'].'</td>'
					.'<td>'.($item['enabled'] == 1 ? $rb->get('o.yes') : $rb->get('o.no')).'</td>'
					.'<td>'.($item['allow_multiple'] == 1 ? $rb->get('o.yes') : $rb->get('o.no')).'</td>'
					.'<td>'.$item['answer_count'].'</td>'
					.'<td>'.$item['vote_count'].'</td>'
					.'<td>'
						.'<form name="inquiry-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
							.'<input type="hidden" name="inquiry-id" value="'.$item['id'].'" />'
							.'<input type="hidden" name="inquiry-edit" value="'.$rb->get('button.edit').'" />'
							.'<input type="image" src="~/images/page_edi.png" name="inquiry-edit" value="'.$rb->get('button.edit').'" title="'.$rb->get('button.edithint').'" />'
						.'</form> '
						.'<form name="inquiry-delete" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
							.'<input type="hidden" name="inquiry-id" value="'.$item['id'].'" />'
							.'<input type="hidden" name="inquiry-delete" value="'.$rb->get('button.delete').'" />'
							.'<input class="confirm" type="image" src="~/images/page_del.png" name="inquiry-delete" value="'.$rb->get('button.delete').'" title="'.$rb->get('button.deletehint').'" />'
						.'</form> - '
						.'<form name="inquiry-answer-add" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
							.'<input type="hidden" name="inquiry-id" value="'.$item['id'].'" />'
							.'<input type="hidden" name="inquiry-answer-create" value="'.$rb->get('button.answercreate').'" />'
							.'<input type="hidden" name="inquiry-answers" value="'.$rb->get('button.answers').'" />'
							.'<input type="image" src="~/images/page_add.png" name="inquiry-answer-create" value="'.$rb->get('button.answercreate').'" title="'.$rb->get('button.answercreatehint').'" />'
						.'</form> '
						.'<form name="inquiry-answers" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
							.'<input type="hidden" name="inquiry-id" value="'.$item['id'].'" />'
							.'<input type="hidden" name="inquiry-answers" value="'.$rb->get('button.answers').'" />'
							.'<input type="image" src="~/images/page_edi.png" name="inquiry-answers" value="'.$rb->get('button.answers').'" title="'.$rb->get('button.answershint').'" />'
						.'</form> '
					.'</td>'
				.'</tr>';
			}
			
			$return .= ''			
			.'</table>';
			
		} else {
			$return .= parent::getWarning($rb->get('data.noinquiries'));
		}
		
		$return .= ''
		.'<hr />'
		.'<form name="inquiry-create" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
			.'<input type="submit" name="inquiry-create" value="'.$rb->get('button.create').'" />'
		.'</form>';
		
		if($useFrames != 'false') {
			return parent::getFrame($rb->get('title.inquiries'), $return, true);
		} else {
			return $return;
		}
	}
	
	public function editInquiry($useFrames = false) {
		$rb = new ResourceBundle();
		$rb->loadBundle($this->BundleName, $this->BundleLang);
		$return = '';
		
		if($_POST['inquiry-save'] == $rb->get('button.save')) {
			$data['id'] = $_POST['inquiry-id'];
			$data['question'] = $_POST['inquiry-question'];
			$data['enabled'] = $_POST['inquiry-enabled'] == 'on' ? 1 : 0;
			$data['allow-multiple'] = $_POST['inquiry-allowmultiple'] == 'on' ? 1 : 0;
			
			if($data['id'] == '') {
				parent::db()->execute('insert into `inquiry`(`question`, `enabled`, `allow_multiple`) values("'.$data['question'].'", '.$data['enabled'].', '.$data['allow-multiple'].');');
				$return .= parent::getSuccess($rb->get('message.inserted'));
			} else {
				parent::db()->execute('update `inquiry` set `question` = "'.$data['question'].'", `enabled` = '.$data['enabled'].', `allow_multiple` = '.$data['allow-multiple'].' where `id`= '.$data['id'].';');
				$return .= parent::getSuccess($rb->get('message.updated'));
			}
		}
		
		if($_POST['inquiry-create'] == $rb->get('button.create') || $_POST['inquiry-edit'] == $rb->get('button.edit')) {
			$id = $_POST['inquiry-id'];
			$data = array('enabled' => 1);
			$answers = array();
			if($id != '') {
				$data = parent::db()->fetchSingle('select `id`, `question`, `enabled`, `allow_multiple` from `inquiry` where `id` = '.$id.';');
			}
			
			$return .= ''
			.'<form name="inquiry-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
				.'<div class="gray-box">'
					.'<label class="w160" for="inquiry-question">'.$rb->get('label.question').'</label>'
					.'<input type="text" name="inquiry-question" id="inquiry-question" value="'.$data['question'].'" class="w300" />'
				.'</div>'
				.'<div class="gray-box">'
					.'<label class="w160" for="inquiry-enabled">'.$rb->get('label.enabled').'</label>'
					.'<input type="checkbox" name="inquiry-enabled" id="inquiry-enabled" '.($data['enabled'] == 1 ? 'checked="checked"' : '').'/>'
				.'</div>'
				.'<div class="gray-box">'
					.'<label class="w160" for="inquiry-allowmultiple">'.$rb->get('label.allowmultiple').'</label>'
					.'<input type="checkbox" name="inquiry-allowmultiple" id="inquiry-allowmultiple" '.($data['allow_multiple'] == 1 ? 'checked="checked"' : '').'/>'
				.'</div>'
				.'<div class="gray-box">'
					.'<input type="hidden" name="inquiry-id" value="'.$data['id'].'" />'
					.'<input type="submit" name="inquiry-save" value="'.$rb->get('button.save').'" />'
				.'</div>'
			.'</form>';
		
			if($useFrames != 'false') {
				return parent::getFrame($rb->get('title.edit'), $return, true);
			} else {
				return $return;
			}
		}
	}
	
	public function getAnswers($useFrames = false) {
		$rb = new ResourceBundle();
		$rb->loadBundle($this->BundleName, $this->BundleLang);
		$return = '';
		
		if($_POST['inquiry-answer-delete'] == $rb->get('button.answerdelete')) {
			$id = $_POST['inquiry-answer-id'];
			parent::db()->execute('delete from `inquiry_vote` where `answer_id` = '.$id.';');
			parent::db()->execute('delete from `inquiry_answer` where `id` = '.$id.';');
		}
		
		if($_POST['inquiry-answers'] == $rb->get('button.answers')) {
			$id = $_POST['inquiry-id'];
			$data = parent::db()->fetchAll('select `id`, `answer`, `count` from `inquiry_answer` where `inquiry_id` = '.$id.';');
			
			if(count($data) > 0) {
			$return .= ''
			.'<table class="standart">'
				.'<tr>'
					.'<th>'.$rb->get('label.id').'</th>'
					.'<th>'.$rb->get('label.answer').'</th>'
					.'<th>'.$rb->get('label.votecount').'</th>'
					.'<th></th>'
				.'<tr>';
			
				foreach ($data as $i => $item) {
					$return .= ''
					.'<tr class="' . ((($i % 2) == 0) ? 'idle' : 'even') . '">'
						.'<td>'.$item['id'].'</td>'
						.'<td>'.$item['answer'].'</td>'
						.'<td>'.$item['count'].'</td>'
						.'<td>'
							.'<form name="inquiry-answer-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
								.'<input type="hidden" name="inquiry-id" value="'.$id.'" />'
								.'<input type="hidden" name="inquiry-answers" value="'.$rb->get('button.answers').'" />'
								
								.'<input type="hidden" name="inquiry-answer-id" value="'.$item['id'].'" />'
								.'<input type="hidden" name="inquiry-answer-edit" value="'.$rb->get('button.answeredit').'" />'
								.'<input type="image" src="~/images/page_edi.png" name="inquiry-answer-edit" value="'.$rb->get('button.answeredit').'" title="'.$rb->get('button.answeredithint').'" />'
							.'</form> '
							.'<form name="inquiry-answer-delete" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
								.'<input type="hidden" name="inquiry-id" value="'.$id.'" />'
								.'<input type="hidden" name="inquiry-answers" value="'.$rb->get('button.answers').'" />'
								
								.'<input type="hidden" name="inquiry-answer-id" value="'.$item['id'].'" />'
								.'<input type="hidden" name="inquiry-answer-delete" value="'.$rb->get('button.answerdelete').'" />'
								.'<input class="confirm" type="image" src="~/images/page_del.png" name="inquiry-answer-delete" value="'.$rb->get('button.answerdelete').'" title="'.$rb->get('button.answerdeletehint').'" />'
							.'</form>'
						.'</td>'
					.'</tr>';
				}
				
				$return .= ''			
				.'</table>';
				
			} else {
				$return .= parent::getWarning($rb->get('data.noanswers'));
			}
		
		$return .= ''
			.'<hr />'
			.'<form name="inquiry-answer-create" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
				.'<input type="hidden" name="inquiry-id" value="'.$id.'" />'
				.'<input type="hidden" name="inquiry-answers" value="'.$rb->get('button.answers').'" />'
				
				.'<input type="submit" name="inquiry-answer-create" value="'.$rb->get('button.answercreate').'" />'
			.'</form>';
		}
		
		if($useFrames != 'false') {
			return parent::getFrame($rb->get('title.answers'), $return, true);
		} else {
			return $return;
		}
	}
	
	public function editAnswer($useFrames = false) {
		$rb = new ResourceBundle();
		$rb->loadBundle($this->BundleName, $this->BundleLang);
		$return = '';
		
		if($_POST['inquiry-answer-save'] == $rb->get('button.answersave')) {
			$data['id'] = $_POST['inquiry-answer-id'];
			$data['inquiry-id'] = $_POST['inquiry-id'];
			$data['answer'] = $_POST['inquiry-answer'];
			
			if($data['id'] == '') {
				parent::db()->execute('insert into `inquiry_answer`(`inquiry_id`, `answer`) values('.$data['inquiry-id'].', "'.$data['answer'].'");');
				$return .= parent::getSuccess($rb->get('message.answerinserted'));
			} else {
				parent::db()->execute('update `inquiry_answer` set `answer` = "'.$data['answer'].'" where `id`= '.$data['id'].';');
				$return .= parent::getSuccess($rb->get('message.answerupdated'));
			}
		}
		
		if($_POST['inquiry-answer-create'] == $rb->get('button.answercreate') || $_POST['inquiry-answer-edit'] == $rb->get('button.answeredit')) {
			$id = $_POST['inquiry-answer-id'];
			$data = array('inquiry_id' => $_POST['inquiry-id']);
			if($id != '') {
				$data = parent::db()->fetchSingle('select `id`, `inquiry_id`, `answer` from `inquiry_answer` where `id` = '.$id.';');
			}
			
			$return .= ''
			.'<form name="inquiry-answer-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
				.'<div class="gray-box">'
					.'<label class="w100" for="inquiry-answer">'.$rb->get('label.answer').'</label>'
					.'<input type="text" name="inquiry-answer" id="inquiry-answer" value="'.$data['answer'].'" class="w300" />'
				.'</div>'
				.'<div class="gray-box">'
					.'<input type="hidden" name="inquiry-answers" value="'.$rb->get('button.answers').'" />'
					.'<input type="hidden" name="inquiry-id" value="'.$data['inquiry_id'].'" />'
				
					.'<input type="hidden" name="inquiry-answer-id" value="'.$data['id'].'" />'
					.'<input type="submit" name="inquiry-answer-save" value="'.$rb->get('button.answersave').'" />'
				.'</div>'
			.'</form>';
		
			if($useFrames != 'false') {
				return parent::getFrame($data['id'] == '' ? $rb->get('title.addanswer') : $rb->get('title.editanswer'), $return, true);
			} else {
				return $return;
			}
		}
	}
	
	public function setCurrentId($useFrames = false) {
		$rb = new ResourceBundle();
		$rb->loadBundle($this->BundleName, $this->BundleLang);
		$return = '';
		
		if($_POST['inquiry-set-current'] == $rb->get('button.setcurrent')) {
			self::setCurrentInquiryId($_POST['inquiry-current-id']);
		}
		
		$data = parent::db()->fetchAll('select `id`, `question` from `inquiry` where `enabled` = 1 order by `id`;');
		
		$return .= ''
		.'<form name="inquiry-set-current" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
			.'<div class="gray-box">'
				.'<label class="w100" for="inquiry-current-id">'.$rb->get('label.inquiry').'</label>'
				.'<select id="inquiry-current-id" name="inquiry-current-id">';
				
		foreach($data as $i => $item) {
			$return .= '<option value="'.$item['id'].'"'.(self::getCurrentInquiryId() == $item['id'] ? ' selected="selected"' : '').'>'.$item['question'].'</option>';
		}
		
		$return .= ''			
				.'</select> '
				.'<input type="submit" name="inquiry-set-current" value="'.$rb->get('button.setcurrent').'" />'
			.'</div>'
		.'</form>';
		
		if($useFrames != 'false') {
			return parent::getFrame($rb->get('title.setcurrentid'), $return, true);
		} else {
			return $return;
		}
	}
	
	/* ================== WEB ========================================================= */
	
	public function renderView($inquiryId) {
		$rb = new ResourceBundle();
		$rb->loadBundle($this->BundleName, $this->BundleLang);
		$return = '';
		
		$voted = false;
		$data = parent::db()->fetchSingle('select `question`, `allow_multiple` from `inquiry` where `id` = '.$inquiryId.' and `enabled` = 1;');
		if($_POST['inquiry-vote'] == $rb->get('button.vote') && $_POST['inquiry-id'] = $inquiryId) {
			$vote = parent::db()->fetchSingle('select `id` from `inquiry_vote` where `ip_address` = "'.$_SERVER['REMOTE_ADDR'].'" and `inquiry_id` = '.$inquiryId.';');
			if($vote == array() || $data['allow_multiple']) {
				$answerId = $_POST['inquiry-answer-id'];
				$answer = parent::db()->fetchSingle('select `count` from `inquiry_answer` where `id` = '.$answerId.' and `inquiry_id` = '.$inquiryId.';');
				if($answer != array()) {
					parent::db()->execute('insert into `inquiry_vote`(`inquiry_id`, `answer_id`, `timestamp`, `ip_address`) values ('.$inquiryId.', '.$answerId.', '.time().', "'.$_SERVER['REMOTE_ADDR'].'");');
					parent::db()->execute('update `inquiry_answer` set `count` = '.($answer['count'] + 1).' where `id` = '.$answerId.';');
					$voted = true;
				}
			}
		}
		
		if($data != array()) {
			$answers = parent::db()->fetchAll('select `id`, `answer`, `count` from `inquiry_answer` where `inquiry_id` = '.$inquiryId.' order by `id`;');
			$votes = parent::db()->fetchSingle('select count(`id`) as `id` from `inquiry_vote` where `inquiry_id` = '.$inquiryId.';');
			$data['vote_count'] = $votes['id'];
			
			if(!$voted) {
				$vote = parent::db()->fetchSingle('select `id` from `inquiry_vote` where `ip_address` = "'.$_SERVER['REMOTE_ADDR'].'" and `inquiry_id` = '.$inquiryId.';');
				$voted = $vote != array();
			}
			if($voted && !$data['allow_multiple']) {
				$return .= ''
				.'<div class="inquiry inquiry-voted">'
					.'<div class="inquiry-question">'
						.$data['question']
					.'</div>';
				
				foreach($answers as $i => $item) {
					$return .= ''
					.'<div class="inquiry-answer inquiry-answer-'.($i + 1).'">'
						.$item['answer']
						.'<div class="inquiry-count">'
							.$item['count'].' / '.self::getPercentage($item['count'], $data['vote_count'], true)
						.'</div>'
						.'<div class="inquiry-percentage">'
							.'<div class="inquiry-slider" style="width: '.self::getPercentage($item['count'], $data['vote_count']).';"></div>'
						.'</div>'
					.'</div>';
				}
							
				$return .= ''
				.'</div>';
			} else {
				$return .= ''
				.'<div class="inquiry">'
					.'<div class="inquiry-question">'
						.$data['question']
					.'</div>'
					.'<form name="inquiry-vote" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
						.'<div class="inquiry-answers">'
							.'<input type="hidden" name="inquiry-id" vale="'.$inquiryId.'" />';
					
				foreach($answers as $i => $item) {
					$return .= ''
					.'<div class="inquiry-answer inquiry-answer-'.($i + 1).'">'
						.'<input type="radio" name="inquiry-answer-id" id="inquiry-answer-'.$item['id'].'" value="'.$item['id'].'" />'
						.'<label for="inquiry-answer-'.$item['id'].'">'.$item['answer'].'</label>'
						.($data['allow_multiple'] ? ''
							.'<div class="inquiry-count">'
								.$item['count'].' / '.self::getPercentage($item['count'], $data['vote_count'], true)
							.'</div>'
							.'<div class="inquiry-percentage">'
								.'<div class="inquiry-slider" style="width: '.self::getPercentage($item['count'], $data['vote_count']).';"></div>'
							.'</div>'
						: '')
					.'</div>';
				}
							
				$return .= ''
						.'</div>'
						.'<div class="inquiry-button">'
							.'<input type="submit" name="inquiry-vote" value="'.$rb->get('button.vote').'" />'
						.'</div>'
					.'</form>'
				.'</div>';
			}
		}
		
		return $return;
	}
	
	private function getPercentage($value, $max, $replace) {
		$result = (round($value / $max * 100, 1)).'%';
		if($replace) {
			return str_replace('.', ',', $result);
		} else {
			return $result;
		}
	}

    /* ================== PROPERTIES ================================================== */

	private $currentId = -1;
	
	public function setCurrentInquiryId($value) {
		if(parent::db()->fetchSingle('select `value` from `system_property` where `key` = "inquiry_currentid";') == array()) {
			parent::db()->execute('insert into `system_property`(`value`, `key`) values("'.$value.'", "inquiry_currentid");');
		} else {
			parent::db()->execute('update `system_property` set `value` = "'.$value.'" where `key` = "inquiry_currentid";');
		}
		$this->currentId = $value;
	
		return $value;
	}
	
	public function getCurrentInquiryId() {
		if($this->currentId == -1) {
			$val = parent::db()->fetchSingle('select `value` from `system_property` where `key` = "inquiry_currentid";');
			$this->currentId = $val['value'];
		}
		return $this->currentId;
		
	}
}

?>