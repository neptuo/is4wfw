<?php

    // use PHPMailer\PHPMailer;

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/EmailException.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/FileUploadModel.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/Validator.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/mailer/Exception.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/mailer/OAuth.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/mailer/POP3.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/mailer/SMTP.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/mailer/PHPMailer.php");

	/**
	 * 
	 *  Class Email. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-01-15
	 * 
	 */
	class Email extends BaseTagLib {

        private $attachments;

		public function send($template, $from, $to, $replyTo, $cc, $bcc, $subject, $isHtml = true) {
            $oldAttachments = $this->attachments;
            $this->attachments = [];

            $content = $template();

            $attachments = $this->attachments;
            $this->attachments = $oldAttachments;

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->CharSet = PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
            
            $headers = array();
            $headers[] = 'MIME-Version: 1.0';

            if ($isHtml) {
                $headers[] = 'Content-type: text/html; charset=utf8';
            }

            if (Validator::isEmail($from)) {
                $headers[] = 'From: ' . $from;
                $mail->setFrom($from);
            }

            if (Validator::isEmail($replyTo)) {
                $headers[] = 'Reply-To: ' . $replyTo;
                $mail->addReplyTo($replyTo);
            }
            
            if (Validator::isEmail($cc)) {
                $headers[] = 'Cc: ' . $cc;
                $mail->addCC($cc);
            }

            if (Validator::isEmail($bcc)) {
                $headers[] = 'Bcc: ' . $bcc;
                $mail->addBCC($bcc);
            }

            $tos = explode(",", $to);
            $to = [];
            foreach ($tos as $item) {
                if (Validator::isEmail($item)) {
                    $to[] = $item;
                    $mail->addAddress($item);
                }
            }

            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment["path"], $attachment["name"]);
            }

            try {
                $mail->isHTML($isHtml);
                $mail->Subject = $subject;
                $mail->Body = $content;
                $mail->send();
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                throw new EmailException($to, $subject, $e->getMessage());
            }
		}

        public function attachment($upload = null, $fileId = null, $name) {
            if ($upload instanceof FileUploadModel) {
                $this->attachments[] = ["path" => $upload->TempName, "name" => empty($name) ? $upload->Name : $name];

            } else if (is_array($upload)) {
                foreach ($upload as $item) {
                    if ($item instanceof FileUploadModel) {
                        $this->attachments[] = ["path" => $item->TempName, "name" => empty($name) ? $item->Name : $name];
                    }
                }
            } else if (!empty($fileId)) {
                $file = parent::dao("File")->get($fileId);
                if (!empty($file)) {
                    $path = parent::autolib("fa")->getPhysicalPathToFile($file);
                    $this->attachments[] = ["path" => $path, "name" => empty($name) ? $file["name"] . '.' . parent::autolib("fa")->getFileExtension($file) : $name];
                }
            }
        }
	}

?>