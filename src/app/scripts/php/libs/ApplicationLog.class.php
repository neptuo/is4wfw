<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/BaseGrid.class.php");

	/**
	 * 
	 *  Class ApplicationLog.	     
	 *      
	 *  @author     Marek SMM
	 *  @timestamp  2010-08-17
	 * 
	 */
	class ApplicationLog extends BaseTagLib {	
		public function __construct() {
			parent::setTagLibXml("ApplicationLog.xml");
		}

		/**
		 *
		 *	Shows list of application logs
		 *	C tag.
		 *
		 */
		public function listLogs($useFrames = false, $showMsg = false) {
			$return = '';
			
			$logFiles = self::fileList(LOGS_PATH, array('log'), false);
			$data = array();
			
			$projects = parent::db()->fetchAll('select `id`, `name` from `web_project` order by `id`;');
			
			foreach($logFiles as $file) {
				$item = self::parseLogName($file);
				if (is_numeric($item[0])) {
					$id = $item[0];
					foreach ($projects as $prj) {
						if ($item[0] == $prj['id']) {
							$item[0] = $prj['name'];
							break;
						}
					}
					if ($item[0] == $id) {
						$item[0] = 'Unknown project [id='.$id.']';
					}
				} else if (empty($item[0])) {
					$item[0] = 'Global log file';
				}
				
				$item[1] = date('d.m.Y', $item[1]);
				$item[2] = ''
				.'<form name="show-log" action="' . $_SERVER['REQUEST_URI'] . '" method="post">'
					.'<input type="hidden" name="log-name" value="'.$file.'" />'
					.'<input type="hidden" name="show-log" value="Show log" />'
					.'<input type="image" src="~/images/page_edi.png" name="show-log" value="Show log" />'
				.'</form>';

				$data[] = $item;
			}
			
			if ($data != array()) {
				$grid = new BaseGrid();
				$grid->setHeader(array(0 => 'Project name:', 1 => 'Date:', 2 => ''));
				$grid->addRows($data);
				$return .= $grid->render();
			}

			if ($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame('Application Logs', $return, "", true);
			}
		}

		/**
		 * 
		 * 	Shows detail of log posted by listLogs
		 *  C tag.
		 * 
		 */
		public function showLog($useFrames = false, $showMsg = false) {
			$return = '';
			
			if ($_POST['show-log'] == 'Show log') {
				$fileName = $_POST['log-name'];
				if (file_exists(LOGS_PATH . $fileName)) {
					$content = file_get_contents(LOGS_PATH . $fileName);
					$content = htmlspecialchars($content);
					$content = str_replace(PHP_EOL, "<br />", $content);
					$content = preg_replace("([0-9][0-9]:[0-9][0-9]:[0-9][0-9])", "<br /><strong>$0</strong><br />", $content);
					if (parent::startsWith($content, "<br />")) {
						$content = substr($content, 6);
					}

					$return .= ''
					.'<div id="editors" class="gray-box">'
						.'<code>' . $content . '</code>'
						//class="edit-area html" 
					.'</div>';
				} else {
					parent::getError('Log file doesn\'t exist!');
				}
			}
			
			if ($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame('Application Log', $return, "", true);
			}
		}
		
		public function fileList($folder, $fileTypes, $dirsOK) {
			if ($dir = @opendir($folder)){
				$found = array();
				while (false !== ($item = readdir($dir))) {
					$fileInfo = pathinfo($item);
					if ($dirsOK) {
						if ((array_key_exists('extension', $fileInfo) && in_array($fileInfo['extension'],$fileTypes)) || !array_key_exists('extension', $fileInfo)){
							$found[] = $item;
						}
					} else {
						if ( (array_key_exists('extension', $fileInfo) && in_array($fileInfo['extension'],$fileTypes))){
							$found[] = $item;
						}
					}
				}

				closedir($dir);
				natcasesort($found);
				return $found;
			} else {
				return array();
			}
		}

		public function parseLogName($name) {
			$projectId = 0;
			$date = '5';
			
			$name = split('.log', $name);
			$name = $name[0];

			$parts = split('-', $name);
			if ($parts[0] != '') {
				$projectId = $parts[0];
				unset($parts[0]);
				$dateString = implode("-", $parts);
				$date = strtotime($dateString);
			} else {
				$dateString = substr($name, 2, strlen($name));
				$date = strtotime($dateString);
			}
			
			return array($projectId, $date);
		}

	}

?>
