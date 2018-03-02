<?php 

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "LocalizationBundle.class.php");

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
			parent::loadLocalizationBundle('inquiry');
		}
		
		private function resetInquiry($inquiryId) {
			parent::db()->execute('update `inquiry_answer` set `count` = 0 where `inquiry_id` = '.$inquiryId.';');
			parent::db()->execute('delete from `inquiry_vote` where `inquiry_id` = '.$inquiryId.';');
		}
		
		/* ================== ADMIN ======================================================= */
		
		public function getInquiries($useFrames = false) {
			if($_POST['inquiry-delete'] == parent::rb('button.delete')) {
				$id = $_POST['inquiry-id'];
				parent::db()->execute('delete from `inquiry_answer` where `inquiry_id` = '.$id.';');
				parent::db()->execute('delete from `inquiry_vote` where `inquiry_id` = '.$id.';');
				parent::db()->execute('delete from `inquiry` where `id` = '.$id.';');
				$return .= parent::getSuccess(parent::rb('message.deleted'));
			}
			
			$data = parent::db()->fetchAll('select `id`, `question`, `enabled`, `allow_multiple`, (select count(`id`) from `inquiry_answer` where `inquiry_id` = `i`.`id`) as `answer_count`, (select count(`id`) from `inquiry_vote` where `inquiry_id` = `i`.`id`) as `vote_count` from `inquiry` as `i` order by `id`');
			if(count($data) > 0) {
				$return .= parent::view('inquiry-list', $data);
			} else {
				$return .= parent::getWarning(parent::rb('data.noinquiries'));
			}
			
			$return .= ''
			.'<hr />'
			.'<div class="gray-box">'
				.'<form name="inquiry-create" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
					.'<input type="submit" name="inquiry-create" value="'.parent::rb('button.create').'" />'
				.'</form>'
			.'</div>';
			
			if($useFrames != 'false') {
				return parent::getFrame(parent::rb('title.inquiries'), $return, true);
			} else {
				return $return;
			}
		}
		
		public function editInquiry($useFrames = false) {
			$return = '';
			
			if($_POST['inquiry-save'] == parent::rb('button.save')) {
				$data['id'] = $_POST['inquiry-id'];
				$data['question'] = $_POST['inquiry-question'];
				$data['enabled'] = $_POST['inquiry-enabled'] == 'on' ? 1 : 0;
				$data['allow-multiple'] = $_POST['inquiry-allowmultiple'] == 'on' ? 1 : 0;
				
				if($data['id'] == '') {
					parent::db()->execute('insert into `inquiry`(`question`, `enabled`, `allow_multiple`) values("'.$data['question'].'", '.$data['enabled'].', '.$data['allow-multiple'].');');
					$return .= parent::getSuccess(parent::rb('message.inserted'));
				} else {
					parent::db()->execute('update `inquiry` set `question` = "'.$data['question'].'", `enabled` = '.$data['enabled'].', `allow_multiple` = '.$data['allow-multiple'].' where `id`= '.$data['id'].';');
					$return .= parent::getSuccess(parent::rb('message.updated'));
				}
			} else if($_POST['inquiry-reset'] == parent::rb('button.reset')) {
				self::resetInquiry($_POST['inquiry-id']);
				$return .= parent::getSuccess(parent::rb('message.reseted'));
			}
			
			if($_POST['inquiry-create'] == parent::rb('button.create') || $_POST['inquiry-edit'] == parent::rb('button.edit')) {
				$id = $_POST['inquiry-id'];
				$data = array('enabled' => 1);
				$answers = array();
				if($id != '') {
					$data = parent::db()->fetchSingle('select `id`, `question`, `enabled`, `allow_multiple` from `inquiry` where `id` = '.$id.';');
				}
			
				if($useFrames != 'false') {
					return parent::getFrame(parent::rb('title.edit'), $return.parent::view('inquiry-edit', $data), true);
				} else {
					return $return;
				}
			}
		}
		
		public function getAnswers($useFrames = false) {
			$return = '';
			
			if($_POST['inquiry-answer-delete'] == parent::rb('button.answerdelete')) {
				$id = $_POST['inquiry-answer-id'];
				parent::db()->execute('delete from `inquiry_vote` where `answer_id` = '.$id.';');
				parent::db()->execute('delete from `inquiry_answer` where `id` = '.$id.';');
			}
			
			if($_POST['inquiry-answers'] == parent::rb('button.answers')) {
				$id = $_POST['inquiry-id'];
				$data = parent::db()->fetchAll('select `id`, `answer`, `count`, `inquiry_id` from `inquiry_answer` where `inquiry_id` = '.$id.' order by `answer`;');
				
				if(count($data) > 0) {
					$return .= parent::view('inquiry-answer-list', $data);
				} else {
					$return .= parent::getWarning(parent::rb('data.noanswers'));
				}
			
			$return .= ''
				.'<hr />'
				.'<div class="gray-box">'
					.'<form name="inquiry-answer-create" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
						.'<input type="hidden" name="inquiry-id" value="'.$id.'" />'
						.'<input type="hidden" name="inquiry-answers" value="'.parent::rb('button.answers').'" />'
						
						.'<input type="submit" name="inquiry-answer-create" value="'.parent::rb('button.answercreate').'" />'
					.'</form>'
				.'</div>';
			}
			
			if($useFrames != 'false') {
				return parent::getFrame(parent::rb('title.answers'), $return, true);
			} else {
				return $return;
			}
		}
		
		public function editAnswer($useFrames = false) {
			$return = '';
			
			if($_POST['inquiry-answer-save'] == parent::rb('button.answersave')) {
				$data['id'] = $_POST['inquiry-answer-id'];
				$data['inquiry-id'] = $_POST['inquiry-id'];
				$data['answer'] = $_POST['inquiry-answer'];
				
				if($data['id'] == '') {
					parent::db()->execute('insert into `inquiry_answer`(`inquiry_id`, `answer`) values('.$data['inquiry-id'].', "'.$data['answer'].'");');
					$return .= parent::getSuccess(parent::rb('message.answerinserted'));
				} else {
					parent::db()->execute('update `inquiry_answer` set `answer` = "'.$data['answer'].'" where `id`= '.$data['id'].';');
					$return .= parent::getSuccess(parent::rb('message.answerupdated'));
				}
			}
			
			if($_POST['inquiry-answer-create'] == parent::rb('button.answercreate') || $_POST['inquiry-answer-edit'] == parent::rb('button.answeredit')) {
				$id = $_POST['inquiry-answer-id'];
				$data = array('inquiry_id' => $_POST['inquiry-id']);
				if($id != '') {
					$data = parent::db()->fetchSingle('select `id`, `inquiry_id`, `answer` from `inquiry_answer` where `id` = '.$id.';');
				}
				
				$return .= ''
				.'<form name="inquiry-answer-edit" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
					.'<div class="gray-box">'
						.'<label class="w100" for="inquiry-answer">'.parent::rb('label.answer').'</label>'
						.'<input type="text" name="inquiry-answer" id="inquiry-answer" value="'.$data['answer'].'" class="w300" />'
					.'</div>'
					.'<div class="gray-box">'
						.'<input type="hidden" name="inquiry-answers" value="'.parent::rb('button.answers').'" />'
						.'<input type="hidden" name="inquiry-id" value="'.$data['inquiry_id'].'" />'
					
						.'<input type="hidden" name="inquiry-answer-id" value="'.$data['id'].'" />'
						.'<input type="submit" name="inquiry-answer-save" value="'.parent::rb('button.answersave').'" />'
					.'</div>'
				.'</form>';
			
				if($useFrames != 'false') {
					return parent::getFrame($data['id'] == '' ? parent::rb('title.addanswer') : parent::rb('title.editanswer'), $return, true);
				} else {
					return $return;
				}
			}
		}
		
		public function setCurrentId($label = false, $label2 = false, $useFrames = false) {
			$return = '';
			
			if($_POST['inquiry-set-current'] == parent::rb('button.setcurrent')) {
				self::setCurrentInquiryId($_POST['inquiry-current-id']);
				self::setCurrentInquiryId2($_POST['inquiry-current-id2']);
			}
			
			$data = parent::db()->fetchAll('select `id`, `question` from `inquiry` where `enabled` = 1 order by `id`;');
			
			if($label == '') {
				$label = parent::rb('label.inquiry');
			}
			if($label2 == '') {
				$label2 = parent::rb('label.inquiry2');
			}
			
			$return .= ''
			.'<form name="inquiry-set-current" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
				.'<div class="gray-box">'
					.'<label class="w100" for="inquiry-current-id">'.$label.'</label>'
					.'<select id="inquiry-current-id" name="inquiry-current-id">';
					
			foreach($data as $i => $item) {
				$return .= '<option value="'.$item['id'].'"'.(self::getCurrentInquiryId() == $item['id'] ? ' selected="selected"' : '').'>'.$item['question'].'</option>';
			}
			
			$return .= ''			
					.'</select> '
				.'</div>'
				.'<div class="gray-box">'
					.'<label class="w100" for="inquiry-current-id2">'.$label2.'</label>'
					.'<select id="inquiry-current-id2" name="inquiry-current-id2">';
					
			foreach($data as $i => $item) {
				$return .= '<option value="'.$item['id'].'"'.(self::getCurrentInquiryId2() == $item['id'] ? ' selected="selected"' : '').'>'.$item['question'].'</option>';
			}
			
			$return .= ''			
					.'</select> '
				.'</div>'
				.'<div class="gray-box">'
					.'<input type="submit" name="inquiry-set-current" value="'.parent::rb('button.setcurrent').'" />'
				.'</div>'
			.'</form>';
			
			if($useFrames != 'false') {
				return parent::getFrame(parent::rb('title.setcurrentid'), $return, true);
			} else {
				return $return;
			}
		}
		
		/* ================== WEB ========================================================= */
		
		public function renderView($inquiryId) {
			$return = '';
			
			$voted = false;
			$data = parent::db()->fetchSingle('select `question`, `allow_multiple` from `inquiry` where `id` = '.$inquiryId.' and `enabled` = 1;');
			if($_POST['inquiry-vote'] == parent::rb('button.vote') && $_POST['inquiry-id'] = $inquiryId) {
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
						.'<form name="inquiry-vote" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
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
								.'<input type="submit" name="inquiry-vote" value="'.parent::rb('button.vote').'" />'
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
		private $currentId2 = -1;
		
		public function setCurrentInquiryId($value) {
			parent::setSystemProperty('inquiry_currentid', $value);
			$this->currentId = $value;
		
			return $value;
		}
		
		public function getCurrentInquiryId() {
			if($this->currentId == -1) {
				$this->currentId = parent::getSystemProperty('inquiry_currentid');
			}
			return $this->currentId;
			
		}
		
		public function setCurrentInquiryId2($value) {
			parent::setSystemProperty('inquiry_currentid2', $value);
			$this->currentId2 = $value;
		
			return $value;
		}
		
		public function getCurrentInquiryId2() {
			if($this->currentId2 == -1) {
				$this->currentId2 = parent::getSystemProperty('inquiry_currentid2');
			}
			return $this->currentId2;
			
		}
	}

?>