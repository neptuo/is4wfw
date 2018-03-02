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
			parent :: setTagLibXml("xml/ApplicationLog.xml");
		}

		/**
		 *
		 *	Shows list of application logs
		*	C tag.
		*
		*/
		public function listLogs($useFrames = false, $showMsg = false) {
			$return = '';
			
			//$return .= parent::getWarning('Coming soon ...');
			
			$logFiles = self::fileList('logs', array('log'), false);
			$data = array();
			
			$projects = parent::db()->fetchAll('select `id`, `name` from `web_project` order by `id`;');
			
			foreach($logFiles as $file) {
				$arr = self::parseLogName($file);
				if($arr[0] != 0) {
					$id = $arr[0];
					foreach($projects as $prj) {
						if($arr[0] == $prj['id']) {
							$arr[0] = $prj['name'];
							break;
						}
					}
					if($arr[0] == $id) {
						$arr[0] = 'Unknown project [id='.$id.']';
					}
				} else {
					$arr[0] = 'Global log file';
				}
				
				$arr[1] = date('d.m.Y', $arr[1]);
				$arr[2] = ''
				.'<form name="show-log" action="" method="post">'
					.'<input type="hidden" name="log-name" value="'.$file.'" />'
					.'<input type="submit" name="show-log" value="Show log" />'
				.'</form>';
				$data[] = $arr;
			}
			
			if($data != array()) {
				$grid = new BaseGrid();
				$grid->setHeader(array(0 => 'Project name:', 1 => 'Date:', 2 => ''));
				$grid->addRows($data);
				$return .= $grid->render();
			}


			if ($useFrames == "false") {
				return $return;
			} else {
				return parent :: getFrame('Application Logs', $return, "", true);
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
			
			if($_POST['show-log'] == 'Show log') {
				$fileName = $_POST['log-name'];
				if(file_exists('logs/'.$fileName)) {
					$content = file_get_contents('logs/'.$fileName);
					$return .= ''
					.'<div id="editors" class="gray-box">'
						.'<textarea id="robots" name="robots" rows="20">'.$content.'</textarea>'
						//class="edit-area html" 
					.'</div>';
				} else {
					parent::getError('Log file doesn\'t exist!');
				}
			}
			
			if ($useFrames == "false") {
				return $return;
			} else {
				return parent :: getFrame('Application Log', $return, "", true);
			}
		}
		
		public function fileList($folder, $fileTypes, $dirsOK) {
			if($dir = @opendir($folder)){
				$found = array();
				while(false !== ($item = readdir($dir))) {
					$fileInfo = pathinfo($item);
					if($dirsOK) {
						if((array_key_exists('extension', $fileInfo) && in_array($fileInfo['extension'],$fileTypes)) || !array_key_exists('extension', $fileInfo)){
							$found[] = $item;
						}
					} else {
						if((array_key_exists('extension', $fileInfo) && in_array($fileInfo['extension'],$fileTypes))){
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

		public function parseLogName ($name) {
			$parts = split('-', $name);
			$projectId = 0;
			$date = '5';
			
			$name = split('.log', $name);
			$name = $name[0];
			
			if($parts[0] != '') {
				$projectId = $parts[0];
				$dateString = substr($name, 3, strlen($name));
				$date = strtotime($dateString);
			} else {
				$dateString = substr($name, 2, strlen($name));
				$date = strtotime($dateString);
			}
			
			return array($projectId, $date);
		}

	}

?>
