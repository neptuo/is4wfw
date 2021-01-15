<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/EmailException.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/Validator.class.php");

	/**
	 * 
	 *  Class Email. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-01-15
	 * 
	 */
	class Email extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("Email.xml");
		}
		
		public function send($template, $from, $to, $replyTo, $cc, $bcc, $subject, $isHtml = true) {
            $content = $template();
            
            $headers = array();
            $headers[] = 'MIME-Version: 1.0';

            if ($isHtml) {
                $headers[] = 'Content-type: text/html; charset=utf8';
            }

            if (Validator::isEmail($from)) {
                $headers[] = 'From: ' . $from;
            }

            if (Validator::isEmail($replyTo)) {
                $headers[] = 'Reply-To: ' . $replyTo;
            }
            
            if (Validator::isEmail($cc)) {
                $headers[] = 'Cc: ' . $cc;
            }

            if (Validator::isEmail($bcc)) {
                $headers[] = 'Bcc: ' . $bcc;
            }

            $tos = explode(",", $to);
            $to = [];
            foreach ($tos as $item) {
                if (Validator::isEmail($item)) {
                    $to[] = $item;
                }
            }

            $to = implode(",", $to);

            $result = mail($to, $subject, $content, $headers);
            if (!$result) {
                $errorMessage = error_get_last()['message'];
                throw new EmailException($to, $subject, $errorMessage);
            }
		}
	}

?>