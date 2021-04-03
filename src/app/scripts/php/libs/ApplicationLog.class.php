<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/BaseGrid.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/Formatter.class.php");

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
		public function listLogs($filter = []) {
			$return = '';

			if (array_key_exists("download-log", $_POST)) {
				$logFile = $_POST["log-name"];
				$filePath = LOGS_PATH . $logFile;
				$fileSize = filesize($filePath);

				header('Content-Type: ' . "text/plain");
				header('Accept-Ranges: bytes');
				header('Content-Length: ' . $fileSize);
				header('Content-Disposition: attachment; filename=' . $logFile);
				header('Content-Transfer-Encoding: binary');
				$file = @fopen($filePath, 'rb');
				if ($file) {
					fpassthru($file);
					parent::close();
				}
			}
			
			$logFiles = $this->fileList(LOGS_PATH, array('log'), false);

			$data = array();
			
			$projects = parent::db()->fetchAll('select `id`, `name` from `web_project` order by `id`;');
			
			foreach($logFiles as $file) {
				$item = $this->parseLogName($file);
				$id = "";
				if (is_numeric($item[0])) {
					$id = $item[0];
					foreach ($projects as $prj) {
						if ($item[0] == $prj['id']) {
							$item[0] = $prj['name'] . ' (' . $id . ')';
							break;
						}
					}

					if ($item[0] == $id) {
						$item[0] = 'Unknown project (id=' . $id . ')';
					}
				} else if (empty($item[0])) {
					$item[0] = 'Global';
				}
				
				// Filter max age.
				if (array_key_exists("age", $filter) && $filter["age"] != "") {
					$maxAge = time() - ($filter["age"] * 24 * 60 * 60);
					$age = $item[1];
					if ($age < $maxAge) {
						continue;
					}
				}

				// Filter project
				if (array_key_exists("project", $filter) && $filter["project"] != "") {
					$project = $filter["project"];
					if (is_numeric($project)) {
						if ($id != $project) {
							continue;
						}
					} else {
						if (stripos($item[0], $project) === false) {
							continue;
						}
					}
				}

				$item[1] = date('d.m.Y', $item[1]);

				$fileSize = filesize(LOGS_PATH . $file);
				$item[2] = Formatter::toByteString($fileSize);
				if ($fileSize > 1000 * 1000) {
					$item[2] = "<span class='red'>" . $item[2] . "</span>";
				}

				$fullUrl = UrlUtils::removeParameter(UrlUtils::addParameter($_SERVER['REQUEST_URI'], "fileName", $file), "tail");
				$tailUrl = UrlUtils::addParameter($fullUrl, "tail", 100);

				$item[3] = ''
				. '<a href="' . $tailUrl . '" class="image-button button-edit" title="Show log tail (100 lines)">'
					. '<img src="~/images/page_edi.png" />'
				. '</a> '
				. '<a href="' . $fullUrl . '" class="image-button button-edit" title="Show log">'
					. '<img src="~/images/page_edi.png" />'
				. '</a> '
				.'<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" target="_blank">'
					.'<input type="hidden" name="log-name" value="'.$file.'" />'
					.'<input type="hidden" name="download-log" value="download-log" />'
					.'<input type="image" src="~/images/icons/arrow_down.png" title="Download log ' . $item[0] . ' ' . $item[1] . '" />'
				.'</form>';

				$data[] = $item;
			}
			
			if (!empty($data)) {
				$grid = new BaseGrid();
				$grid->addClass("clickable");
				$grid->setHeader(array(0 => 'Project name:', 1 => 'Date:', 2 => "Size:", 3 => ''));
				$grid->addRows($data);
				$return .= $grid->render();
			} else {
				$return .= $this->getWarning("No log files to show.");
			}

			return $return;
		}

		/**
		 * 
		 * 	Shows detail of log posted by listLogs
		 *  C tag.
		 * 
		 */
		public function showLog($fileName, $tailLines = 0) {
			$return = '';
			
			$fileName = basename($fileName);
			if (file_exists(LOGS_PATH . $fileName)) {
				if ($tailLines == 0) {
					$content = file_get_contents(LOGS_PATH . $fileName);
				} else {
					$content = "..." . PHP_EOL . PHP_EOL . FileUtils::tail(LOGS_PATH . $fileName, $tailLines);
				}
				$content = htmlspecialchars($content);
				$content = str_replace(PHP_EOL, "<br />", $content);
				$content = preg_replace("([0-9][0-9]:[0-9][0-9]:[0-9][0-9])", "<br /><strong>$0</strong><br />", $content);
				if (StringUtils::startsWith($content, "<br />")) {
					$content = substr($content, 6);
				}

				$return .= $content;
			}
			
			return $return;
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
			$projectId = '';
			$date = '5';
			
			$name = explode('.log', $name);
			$name = $name[0];

			$parts = explode('-', $name);
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
