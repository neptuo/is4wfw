<?php

  /**
   *
   *  Require base tag lib class.   
   *
   */
  require_once("BaseTagLib.class.php");
  
  /**
   *
   *  Form class.
   *      
   *  @author     Marek SMM
   *  @timestamp  2009-09-10
   *
   */              
  class Form extends BaseTagLib {
  
  	private static $count = 0;
    
    /**
     *
     *  Initialize object.
     *
     */                   
    public function __construct() {
      self::setTagLibXml("Form.xml");
    }
    
    public function orderForm1() {
      $this->count ++;
      
      global $dbObject;
      global $loginObject;
      $return = '';
      $success = false;
      $submitted = false;
      $errString = '';
      $token = rand(100, 1000).rand(100, 1000);
      
			$compName = "";
			$contPerson = "";
			$contEmail = "";
			$contPhone = "";
			$contAddress = "";
			$width = (int) 0;
			$height = (int) 0;
			$doorType = 1;
			$cover = 1;
			$fillIn = 1;
			$comment = "";
      
      if($_POST['send-order-form-1'] == "Odeslat" && $_SESSION['token-order-form-1'] == $_POST['token-order-form-1']) {
      	$errors = array();
      	$submitted = true;
				$compName = $_POST['comp-name'];
				$contPerson = $_POST['cont-person'];
				$contEmail = $_POST['cont-email'];
				$contPhone = $_POST['cont-phone'];
				$contAddress = $_POST['cont-address'];
				$width = (float) str_replace(',', '.', $_POST['width']);
				$height = (float) str_replace(',', '.', $_POST['height']);
				$doorType = $_POST['door-type'];
				$cover = $_POST['cover'];
				$fillIn = $_POST['fill-in'];
				$comment = $_POST['comment'];
				
				if($compName == "") {
					$errors[] = "Musíte vyplnit jméno!";
				}
				if($contPerson == "") {
					$errors[] = "Musíte vyplnit kontaktní osobu!";
				}
				if($contEmail == "") {
					$errors[] = "Musíte vyplnit email!";
				}
				if($contPhone == "") {
					$errors[] = "Musíte vyplnit telefon!";
				}
				if($contAddress == "") {
					$errors[] = "Musíte vyplnit adresu!";
				}
				
				if(count($errors) == 0) {
					$dbObject->execute('INSERT INTO `form_order1`(`comp_name`, `cont_person`, `cont_email`, `cont_phone`, `cont_address`, `width`, `height`, `door_type`, `cover`, `fill_in`, `comment`, `timestamp`, `ip`) VALUES("'.$compName.'", "'.$contPerson.'", "'.$contEmail.'", "'.$contPhone.'", "'.$contAddress.'", '.$width.', '.$height.', '.$doorType.', '.$cover.', '.$fillIn.', "'.$comment.'", '.time().', "'.$_SERVER['REMOTE_ADDR'].'");');
					$oid = $dbObject->fetchAll('SELECT MAX(`id`) as `id` FROM `form_order1`;');
					$oid = $oid[0]['id'];
					$_GET['order1-id'] = $oid;
					
					$objCon = self::showOrder1Detail();
					
					$obsah = "Vaše poptávka byla zaznamenána";
  				$message = sprintf("Máte novou poptávka na www.plasticport.cz. \"Kyvná vrata\"\n\n");
  				$message .= $objCon;
  				
  				$headers = "From: info@plasticport.cz\n";
				  $headers .= "CONTENT-TYPE: text/html; CHARSET=utf-8";
  				mail("info@plasticport.cz, petr.dasek@portaflex.cz", "Nova poptavka ma www.plasticport.cz", $message, $headers );
					
					$success = true;
					$compName = "";
					$contPerson = "";
					$contEmail = "";
					$contPhone = "";
					$contAddress = "";
					$width = (int) 0;
					$height = (int) 0;
					$doorType = 1;
					$cover = 1;
					$fillIn = 1;
					$comment = "";
				} else {
					foreach($errors as $error) {
						$errString .= '<strong>'.$error.'</strong>';
					}
				}
			}
      
      $return .= ''
      .'<div class="order-form-1">'
      	.(($submitted) ? ($success) ? '<h4 class="success">Objednávka byla odeslána</h4>' : '<h4 class="error">Prosíme, doplňte povinná pole.</h4><div class="errors">'.$errString.'</div>' : '')
      	.'<form name="order-form-1" method="post" action="">'
      		.'<div class="comment-1">Povinné údaje jsou tučně.</div>'
      		.'<div class="comment-2">'
      			.'<div class="comment-2-1">'
      				.'<label for="comp-name-'.$this->count.'">Název společnosti:</label>'
      				.'<input type="text" name="comp-name" id="comp-name-'.$this->count.'" value="'.$compName.'" />'
      			.'</div>'
      			.'<div class="comment-2-2">'
      				.'<label for="cont-person-'.$this->count.'">Kontaktní osoba:</label>'
      				.'<input type="text" name="cont-person" id="cont-person-'.$this->count.'" value="'.$contPerson.'" />'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-3">'
      			.'<div class="comment-3-1">'
      				.'<label for="cont-email-'.$this->count.'">Email:</label>'
      				.'<input type="text" name="cont-email" id="cont-email-'.$this->count.'" value="'.$contEmail.'" />'
      			.'</div>'
      			.'<div class="comment-3-2">'
      				.'<label for="cont-phone-'.$this->count.'">Telefon:</label>'
      				.'<input type="text" name="cont-phone" id="cont-phone-'.$this->count.'" value="'.$contPhone.'" />'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-4">'
      			.'<div class="comment-4-1">'
      				.'<label for="cont-address-'.$this->count.'">Adresa:</label>'
      				.'<input type="text" name="cont-address" id="cont-address-'.$this->count.'" value="'.$contAddress.'" />'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-5">'
      			.'<span class="comment-5-1">Rozměry otvoru:</span>'
      			.'<div class="comment-5-1">'
      				.'<label for="width-'.$this->count.'">šířka[m]:</label>'
      				.'<input type="text" name="width" id="width-'.$this->count.'" value="'.$width.'" />'
      			.'</div>'
      			.'<div class="comment-5-2">'
      				.'<label for="height-'.$this->count.'">výška[m]:</label>'
      				.'<input type="text" name="height" id="height-'.$this->count.'" value="'.$height.'" />'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-6">'
      			.'<span class="comment-6-1">typ dveří:</span>'
      			.'<div class="comment-6-2">'
      				.'<input type="radio" name="door-type" value="1" id="door-type-1-'.$this->count.'"'.(($doorType == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="door-type-1-'.$this->count.'">jednokřídlová</label>'
      			.'</div>'
      			.'<div class="comment-6-3">'
      				.'<input type="radio" name="door-type" value="2" id="door-type-2-'.$this->count.'"'.(($doorType == 2) ? ' checked="checked"' : '').' />'
      				.'<label for="door-type-2-'.$this->count.'">dvoukřídlová</label>'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-7">'
      			.'<span class="comment-7-1">rám:</span>'
      			.'<div class="comment-7-2">'
      				.'<input type="radio" name="cover" value="1" id="cover-1-'.$this->count.'"'.(($cover == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="cover-1-'.$this->count.'">nerez</label>'
      			.'</div>'
      			.'<div class="comment-7-3">'
      				.'<input type="radio" name="cover" value="2" id="cover-2-'.$this->count.'"'.(($cover == 2) ? ' checked="checked"' : '').' />'
      				.'<label for="cover-2-'.$this->count.'">pozink</label>'
      			.'</div>'
      			.'<div class="comment-7-4">'
      				.'<input type="radio" name="cover" value="3" id="cover-3-'.$this->count.'"'.(($cover == 3) ? ' checked="checked"' : '').' />'
      				.'<label for="cover-3-'.$this->count.'">komaxit</label>'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-8">'
      			.'<span class="comment-8-1">výplň:</span>'
      			.'<div class="comment-8-2">'
      				.'<input type="radio" name="fill-in" value="1" id="fill-in-1-'.$this->count.'"'.(($fillIn == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="fill-in-1-'.$this->count.'">síla 5mm</label>'
      			.'</div>'
      			.'<div class="comment-8-3">'
      				.'<input type="radio" name="fill-in" value="2" id="fill-in-2-'.$this->count.'"'.(($fillIn == 2) ? ' checked="checked"' : '').' />'
      				.'<label for="fill-in-2-'.$this->count.'">síla 7mm</label>'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-10">'
      			.'<div class="comment-10-1">'
      				.'<label for="comment-'.$this->count.'">Poznámky: </label>'
      				.'<textarea rows="10" cols="60" name="comment" id="comment-'.$this->count.'">'.$comment.'</textarea>'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-11">'
      			.'<div class="comment-11-1">'
      				.'<input type="hidden" name="token-order-form-1" value="'.$token.'" />'
      				.'<input type="submit" name="send-order-form-1" value="Odeslat" />'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      	.'</form>'
      .'</div>';
      
      $_SESSION['token-order-form-1'] = $token;
      return $return;
    }
    
    public function orderForm2() {
      $this->count ++;
      
      global $dbObject;
      global $loginObject;
      $return = '';
      $success = false;
      $submitted = false;
      $errString = '';
      $token = rand(100, 1000).rand(100, 1000);
      
			$compName = "";
			$contPerson = "";
			$contEmail = "";
			$contPhone = "";
			$contAddress = "";
			$width = (int) 0;
			$height = (int) 0;
			$fixture = 1;
			$draught = 1;
			$transit1 = array();
			$heating = 1;
			$gripping1 = 1;
			$gripping2 = 1;
			$comment = "";
      
      if($_POST['send-order-form-2'] == "Odeslat" && $_SESSION['token-order-form-2'] == $_POST['token-order-form-2']) {
      	$errors = array();
      	$submitted = true;
				$compName = $_POST['comp-name'];
				$contPerson = $_POST['cont-person'];
				$contEmail = $_POST['cont-email'];
				$contPhone = $_POST['cont-phone'];
				$contAddress = $_POST['cont-address'];
				$width = (float) str_replace(',', '.', $_POST['width']);
				$height = (float) str_replace(',', '.', $_POST['height']);
				$fixture = $_POST['fixture'];
				$draught = $_POST['draught'];
				$transit1 = $_POST['transit'];
				$heating = $_POST['heating'];
				$gripping1 = $_POST['gripping-1'];
				$gripping2 = $_POST['gripping-2'];
				$comment = $_POST['comment'];
				
				if($compName == "") {
					$errors[] = "Musíte vyplnit jméno!";
				}
				if($contPerson == "") {
					$errors[] = "Musíte vyplnit kontaktní osobu!";
				}
				if($contEmail == "") {
					$errors[] = "Musíte vyplnit email!";
				}
				if($contPhone == "") {
					$errors[] = "Musíte vyplnit telefon!";
				}
				if($contAddress == "") {
					$errors[] = "Musíte vyplnit adresu!";
				}
				
				$transit = array();
				$transitDb = 0;
				foreach($transit1 as $tran) {
					$transit[$tran] = 1;
					$transitDb += $tran;
				}
				
				if(count($errors) == 0) {
					$dbObject->execute('INSERT INTO `form_order2`(`comp_name`, `cont_person`, `cont_email`, `cont_phone`, `cont_address`, `width`, `height`, `fixture`, `draught`, `transit`, `heating`, `gripping_1`, `gripping_2`, `comment`, `timestamp`, `ip`) VALUES("'.$compName.'", "'.$contPerson.'", "'.$contEmail.'", "'.$contPhone.'", "'.$contAddress.'", '.$width.', '.$height.', '.$fixture.', '.$draught.', '.$transitDb.', '.$heating.', '.$gripping1.', '.$gripping2.', "'.$comment.'", '.time().', "'.$_SERVER['REMOTE_ADDR'].'");');
					$oid = $dbObject->fetchAll('SELECT MAX(`id`) as `id` FROM `form_order2`;');
					$oid = $oid[0]['id'];
					$_GET['order2-id'] = $oid;
					
					$objCon = self::showOrder2Detail();
					
					$obsah = "Vaše poptávka byla zaznamenána";
  				$message = sprintf("Máte novou poptávka na www.plasticport.cz. \"Lamelové clony\"<br /><br />");
  				$message .= $objCon;
  				
  				$headers = "From: info@plasticport.cz\n";
				  $headers .= "CONTENT-TYPE: text/html; CHARSET=utf-8";
  				mail("info@plasticport.cz, petr.dasek@portaflex.cz", "Nova poptavka ma www.plasticport.cz", $message, $headers );
					
					$success = true;
					$compName = "";
					$contPerson = "";
					$contEmail = "";
					$contPhone = "";
					$contAddress = "";
					$width = (int) 0;
					$height = (int) 0;
					$fixture = 1;
					$draught = 1;
					$transit1 = $transit = array();
					$heating = 1;
					$gripping1 = 1;
					$gripping2 = 1;
					$comment = "";
				} else {
					foreach($errors as $error) {
						$errString .= '<strong>'.$error.'</strong>';
					}
				}
			}
      
      $return .= ''
      .'<div class="order-form-2">'
      	.(($submitted) ? ($success) ? '<h4 class="success">Poptávka byla odeslána</h4>' : '<h4 class="error">Prosíme, doplňte povinná pole.</h4><div class="errors">'.$errString.'</div>' : '')
      	.'<form name="order-form-2" method="post" action="">'
      		.'<div class="comment-1">Povinné údaje jsou tučně.</div>'
      		.'<div class="comment-2">'
      			.'<div class="comment-2-1">'
      				.'<label for="comp-name-'.$this->count.'">Název společnosti:</label>'
      				.'<input type="text" name="comp-name" id="comp-name-'.$this->count.'" value="'.$compName.'" />'
      			.'</div>'
      			.'<div class="comment-2-2">'
      				.'<label for="cont-person-'.$this->count.'">Kontaktní osoba:</label>'
      				.'<input type="text" name="cont-person" id="cont-person-'.$this->count.'" value="'.$contPerson.'" />'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-3">'
      			.'<div class="comment-3-1">'
      				.'<label for="cont-email-'.$this->count.'">Email:</label>'
      				.'<input type="text" name="cont-email" id="cont-email-'.$this->count.'" value="'.$contEmail.'" />'
      			.'</div>'
      			.'<div class="comment-3-2">'
      				.'<label for="cont-phone-'.$this->count.'">Telefon:</label>'
      				.'<input type="text" name="cont-phone" id="cont-phone-'.$this->count.'" value="'.$contPhone.'" />'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-4">'
      			.'<div class="comment-4-1">'
      				.'<label for="cont-address-'.$this->count.'">Adresa:</label>'
      				.'<input type="text" name="cont-address" id="cont-address-'.$this->count.'" value="'.$contAddress.'" />'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-5">'
      			.'<span class="comment-5-1">Rozměry otvoru:</span>'
      			.'<div class="comment-5-1">'
      				.'<label for="width-'.$this->count.'">šířka[m]:</label>'
      				.'<input type="text" name="width" id="width-'.$this->count.'" value="'.$width.'" />'
      			.'</div>'
      			.'<div class="comment-5-2">'
      				.'<label for="height-'.$this->count.'">výška[m]:</label>'
      				.'<input type="text" name="height" id="height-'.$this->count.'" value="'.$height.'" />'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-6">'
      			.'<span class="comment-6-1">umístění otvoru:</span>'
      			.'<div class="comment-6-2">'
      				.'<input type="radio" name="fixture" value="1" id="fixture-1-'.$this->count.'"'.(($fixture == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="fixture-1-'.$this->count.'">exteriér</label>'
      			.'</div>'
      			.'<div class="comment-6-3">'
      				.'<input type="radio" name="fixture" value="2" id="fixture-2-'.$this->count.'"'.(($fixture == 2) ? ' checked="checked"' : '').' />'
      				.'<label for="fixture-2-'.$this->count.'">interiér</label>'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-7">'
      			.'<span class="comment-7-1">průvan:</span>'
      			.'<div class="comment-7-2">'
      				.'<input type="radio" name="draught" value="1" id="draught-1-'.$this->count.'"'.(($draught == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="draught-1-'.$this->count.'">není</label>'
      			.'</div>'
      			.'<div class="comment-7-3">'
      				.'<input type="radio" name="draught" value="2" id="draught-2-'.$this->count.'"'.(($draught == 2) ? ' checked="checked"' : '').' />'
      				.'<label for="draught-2-'.$this->count.'">mírný</label>'
      			.'</div>'
      			.'<div class="comment-7-4">'
      				.'<input type="radio" name="draught" value="3" id="draught-3-'.$this->count.'"'.(($draught == 3) ? ' checked="checked"' : '').' />'
      				.'<label for="draught-3-'.$this->count.'">velký</label>'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-8">'
      			.'<span class="comment-8-1">průjezd:</span>'
      			.'<div class="comment-8-2">'
      				.'<input type="checkbox" name="transit[]" value="1" id="transit-1-'.$this->count.'"'.(($transit[1] == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="transit-1-'.$this->count.'">osoby</label>'
      			.'</div>'
      			.'<div class="comment-8-3">'
      				.'<input type="checkbox" name="transit[]" value="2" id="transit-2-'.$this->count.'"'.(($transit[2] == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="transit-2-'.$this->count.'">pal.vozík</label>'
      			.'</div>'
      			.'<div class="comment-8-4">'
      				.'<input type="checkbox" name="transit[]" value="4" id="transit-3-'.$this->count.'"'.(($transit[4] == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="transit-3-'.$this->count.'">vys.voz</label>'
      			.'</div>'
      			.'<div class="comment-8-5">'
      				.'<input type="checkbox" name="transit[]" value="8" id="transit-4-'.$this->count.'"'.(($transit[8] == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="transit-4-'.$this->count.'">os.auto</label>'
      			.'</div>'
      			.'<div class="comment-8-6">'
      				.'<input type="checkbox" name="transit[]" value="16" id="transit-5-'.$this->count.'"'.(($transit[16] == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="transit-5-'.$this->count.'">nákl.auta</label>'
      			.'</div>'
      			.'<div class="comment-8-7">'
      				.'<input type="checkbox" name="transit[]" value="32" id="transit-6-'.$this->count.'"'.(($transit[32] == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="transit-6-'.$this->count.'">kamiony</label>'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-9">'
      			.'<span class="comment-9-1">vytápění:</span>'
      			.'<div class="comment-9-2">'
      				.'<input type="radio" name="heating" value="1" id="heating-1-'.$this->count.'"'.(($heating == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="heating-1-'.$this->count.'">ano</label>'
      			.'</div>'
      			.'<div class="comment-9-3">'
      				.'<input type="radio" name="heating" value="2" id="heating-2-'.$this->count.'"'.(($heating == 2) ? ' checked="checked"' : '').' />'
      				.'<label for="heating-2-'.$this->count.'">ne</label>'
      			.'</div>'
      			.'<div class="comment-9-4">'
      				.'<input type="radio" name="heating" value="3" id="heating-3-'.$this->count.'"'.(($heating == 3) ? ' checked="checked"' : '').' />'
      				.'<label for="heating-3-'.$this->count.'">mrazírna</label>'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-10">'
      			.'<span class="comment-10-1">uchycení:</span>'
      			.'<div class="comment-10-2">'
      				.'<input type="radio" name="gripping-1" value="1" id="gripping-1-1-'.$this->count.'"'.(($gripping1 == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="gripping-1-1-'.$this->count.'">PVC</label>'
      			.'</div>'
      			.'<div class="comment-10-3">'
      				.'<input type="radio" name="gripping-1" value="2" id="gripping-1-2-'.$this->count.'"'.(($gripping1 == 2) ? ' checked="checked"' : '').' />'
      				.'<label for="gripping-1-2-'.$this->count.'">pozink</label>'
      			.'</div>'
      			.'<div class="comment-10-4">'
      				.'<input type="radio" name="gripping-1" value="3" id="gripping-1-3-'.$this->count.'"'.(($gripping1 == 3) ? ' checked="checked"' : '').' />'
      				.'<label for="gripping-1-3-'.$this->count.'">nerez</label>'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-11">'
      			.'<div class="comment-11-1">'
      				.'<input type="radio" name="gripping-2" value="1" id="gripping-2-1-'.$this->count.'"'.(($gripping2 == 1) ? ' checked="checked"' : '').' />'
      				.'<label for="gripping-2-1-'.$this->count.'">nad otvor</label>'
      			.'</div>'
      			.'<div class="comment-11-2">'
      				.'<input type="radio" name="gripping-2" value="2" id="gripping-2-2-'.$this->count.'"'.(($gripping2 == 2) ? ' checked="checked"' : '').' />'
      				.'<label for="gripping-2-2-'.$this->count.'">do otvoru</label>'
      			.'</div>'
      			.'<div class="comment-11-3">'
      				.'<input type="radio" name="gripping-2" value="3" id="gripping-2-3-'.$this->count.'"'.(($gripping2 == 3) ? ' checked="checked"' : '').' />'
      				.'<label for="gripping-2-3-'.$this->count.'">pojezdové</label>'
      			.'</div>'
      			.'<div class="comment-11-4">'
      				.'<input type="radio" name="gripping-2" value="4" id="gripping-2-4-'.$this->count.'"'.(($gripping2 == 4) ? ' checked="checked"' : '').' />'
      				.'<label for="gripping-2-4-'.$this->count.'">předsunuté</label>'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-12">'
      			.'<div class="comment-12-1">'
      				.'<label for="comment-'.$this->count.'">Poznámky: </label>'
      				.'<textarea rows="10" cols="60" name="comment" id="comment-'.$this->count.'">'.$comment.'</textarea>'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      		.'<div class="comment-13">'
      			.'<div class="comment-13-1">'
      				.'<input type="hidden" name="token-order-form-2" value="'.$token.'" />'
      				.'<input type="submit" name="send-order-form-2" value="Odeslat" />'
      			.'</div>'
      			.'<div class="clear"></div>'
      		.'</div>'
      	.'</form>'
      .'</div>';
      
      $_SESSION['token-order-form-2'] = $token;
      return $return;
    }
    
    public function showOrder1($detailPageId = false) {
		global $dbObject;
		global $webObject;
		$return = '';
		
		$actionUrl = '';
		if($detailPageId != false) {
			$actionUrl = $webObject->composeUrl($detailPageId);
		}
		
		if($_POST['delete-order-form-1'] == "Smazat") {
			$oId = $_POST['order1-id'];
			
			$dbObject->execute('DELETE FROM `form_order1` WHERE `id` = '.$oId.';');
			$return .= '<h4 class="success">Objednavka smazana!</h4>';
		}
		
		$orders1 = $dbObject->fetchAll('SELECT `id`, `comp_name`, `cont_person`, `cont_email`, `cont_phone`, `cont_address`, `width`, `height`, `door_type`, `cover`, `fill_in`, `comment`, `timestamp`, `ip` FROM `form_order1` ORDER BY `id` DESC;');
		if(count($orders1) > 0) {
			$return .= ''
			.'<table class="form-orders">'
				.'<tr>'
					.'<th class="order-id">Id:</th>'
					.'<th class="order-name">Datum a čas:</th>'
					.'<th class="order-name">Z IP adresy:</th>'
					.'<th class="order-name">Společnost:</th>'
					.'<th class="order-person">Osoba:</th>'
					.'<th class="order-email">Email:</th>'
					.'<th class="order-phone">Telefon:</th>'
					.'<th class="order-address">Adresa:</th>'
					.'<th class="order-action">Akce:</th>'
				.'</tr>';
			$i = 0;
			foreach($orders1 as $order) {
				$return .= ''
				.'<tr class="'.((($i % 2) == 1) ? 'even' : 'idle').'">'
					.'<td class="order-id">'.$order['id'].'</td>'
					.'<td class="order-id">'.(($order['timestamp'] != 0) ? date("d.m.Y H.i.s", $order['timestamp']) : 'Není zadáno.').'</td>'
					.'<td class="order-id">'.$order['ip'].'</td>'
					.'<td class="order-name">'.$order['comp_name'].'</td>'
					.'<td class="order-person">'.$order['cont_person'].'</td>'
					.'<td class="order-email">'.$order['cont_email'].'</td>'
					.'<td class="order-phone">'.$order['cont_phone'].'</td>'
					.'<td class="order-address">'.$order['cont_address'].'</td>'
					.'<td class="order-action">'
						.'<a target="_blank" href="'.$actionUrl.'?order1-id='.$order['id'].'">náhled celé objednávky</a> '
						.'<form name="order-delete" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
							.'<input type="hidden" name="order1-id" value="'.$order['id'].'" />'
					.'<input type="hidden" name="delete-order-form-1" value="Smazat" />'
					.'<input type="image" src="~/images/page_del.png" name="delete-order-form-1" value="Smazat" />'
				.'</form>'
					.'</td>'
				.'</tr>';
				$i ++;
			}
			$return .= '</table>';
			
			$return = parent::getFrame('Objednavky 1', $return, '', true);
		} else {
			$return .= parent::getFrame('Objednavky', '<h4 class="error">No orders!</h4>', '', true);
		}
		
		$return .= $ordersStr;
		
		return $return;
	}
    
    public function showOrder2($detailPageId = false) {
		global $dbObject;
		global $webObject;
		$return = '';
		
		$actionUrl = '';
		if($detailPageId != false) {
			$actionUrl = $webObject->composeUrl($detailPageId);
		}
		
		if($_POST['delete-order-form-2'] == "Smazat") {
			$oId = $_POST['order2-id'];
			
			$dbObject->execute('DELETE FROM `form_order2` WHERE `id` = '.$oId.';');
			$return .= '<h4 class="success">Objednavka smazana!</h4>';
		}
		
		$orders1 = $dbObject->fetchAll('SELECT `id`, `comp_name`, `cont_person`, `cont_email`, `cont_phone`, `cont_address`, `width`, `height`, `fixture`, `draught`, `transit`, `heating`, `gripping_1`, `gripping_2`, `comment`, `timestamp`, `ip` FROM `form_order2` ORDER BY `id` DESC;');
		if(count($orders1) > 0) {
			$return .= ''
			.'<table class="form-orders">'
				.'<tr>'
					.'<th class="order-id">Id:</th>'
					.'<th class="order-name">Datum a čas:</th>'
					.'<th class="order-name">Z IP adresy:</th>'
					.'<th class="order-name">Společnost:</th>'
					.'<th class="order-person">Osoba:</th>'
					.'<th class="order-email">Email:</th>'
					.'<th class="order-phone">Telefon:</th>'
					.'<th class="order-address">Adresa:</th>'
					.'<th class="order-action">Akce:</th>'
				.'</tr>';
			$i = 0;
			foreach($orders1 as $order) {
				$return .= ''
				.'<tr class="'.((($i % 2) == 1) ? 'even' : 'idle').'">'
					.'<td class="order-id">'.$order['id'].'</td>'
					.'<td class="order-id">'.(($order['timestamp'] != 0) ? date("d.m.Y H.i.s", $order['timestamp']) : 'Není zadáno.').'</td>'
					.'<td class="order-id">'.$order['ip'].'</td>'
					.'<td class="order-name">'.$order['comp_name'].'</td>'
					.'<td class="order-person">'.$order['cont_person'].'</td>'
					.'<td class="order-email">'.$order['cont_email'].'</td>'
					.'<td class="order-phone">'.$order['cont_phone'].'</td>'
					.'<td class="order-address">'.$order['cont_address'].'</td>'
					.'<td class="order-action">'
						.'<a target="_blank" href="'.$actionUrl.'?order2-id='.$order['id'].'">náhled celé objednávky</a> '
						.'<form name="order-delete" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
							.'<input type="hidden" name="order2-id" value="'.$order['id'].'" />'
					.'<input type="hidden" name="delete-order-form-2" value="Smazat" />'
					.'<input type="image" src="~/images/page_del.png" name="delete-order-form-2" value="Smazat" />'
				.'</form>'
					.'</td>'
				.'</tr>';
		$i ++;
			}
			$return .= '</table>';
			
			$return = parent::getFrame('Objednavky 2', $return, '', true);
		} else {
			$return .= parent::getFrame('Objednavky', '<h4 class="error">No orders!</h4>', '', true);
		}
		
		$return .= $ordersStr;
		
		return $return;
	}
		
	public function showOrder1Detail() {
		global $dbObject;
		$return = '';
		
		$orders1 = $dbObject->fetchAll('SELECT `id`, `comp_name`, `cont_person`, `cont_email`, `cont_phone`, `cont_address`, `width`, `height`, `door_type`, `cover`, `fill_in`, `comment`, `timestamp` FROM `form_order1` WHERE `id` = '.$_GET['order1-id'].' ORDER BY `id` DESC;');
		if(count($orders1) > 0) {
			$order = $orders1[0];
			
			if($order['door_type'] == 1) {
				$order['door_type'] = 'jednokřídlová';
			} elseif($order['door_type'] == 2) {
				$order['door_type'] = 'dvoukřídlová';
			}
			
			if($order['cover'] == 1) {
				$order['cover'] = 'nerez';
			} elseif($order['cover'] == 2) {
				$order['cover'] = 'pozink';
			} elseif($order['cover'] == 3) {
				$order['cover'] = 'komaxit';
			}
			
			if($order['fill_in'] == 1) {
				$order['fill_in'] = 'síla 5mm';
			} elseif($order['fill_in'] == 2) {
				$order['fill_in'] = 'síla 7mm';
			}
			
			$return .= ''
			.'<div class="order-form-2">'
				.'<table class="comment-2">'
					.'<tr class="comment-2-0">'
						.'<td class="lab">Datum a čas:</td>'
						.'<td class="val">'.(($order['timestamp'] != 0) ? date("d.m.Y H.i.s", $order['timestamp']) : 'Není zadáno.').'</td>'
					.'</tr>'
					.'<tr class="comment-2-1">'
						.'<td class="lab">Název společnosti:</td>'
						.'<td class="val">'.$order['comp_name'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-2">'
						.'<td class="lab">Kontaktní osoba:</td>'
						.'<td class="val">'.$order['cont_person'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-3">'
						.'<td class="lab">Email:</td>'
						.'<td class="val">'.$order['cont_email'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-4">'
						.'<td class="lab">Telefon:</td>'
						.'<td class="val">'.$order['cont_phone'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-5">'
						.'<td class="lab">Adresa:</td>'
						.'<td class="val">'.$order['cont_address'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-6">'
						.'<td class="lab">Rozměry otvoru:</td>'
						.'<td class="val">šířka[m]: <span class="s1">'.$order['width'].'</span><br />výška[m]: <span class="s2">'.$order['height'].'</span></td>'
					.'</tr>'
					.'<tr class="comment-2-7">'
						.'<td class="lab">typ dveří:</td>'
						.'<td class="val">'.$order['door_type'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-8">'
						.'<td class="lab">rám:</td>'
						.'<td class="val">'.$order['cover'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-9">'
						.'<td class="lab">výplň:</td>'
						.'<td class="val">'.$order['fill_in'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-12">'
						.'<td class="lab">Poznámka:</td>'
						.'<td class="val">'.$order['comment'].'</td>'
					.'</tr>'
				.'</table>'
			.'</div>';
		} else {
			$return .= '<h4 class="error">Nebylo zadáno id objednávky</h4>';
		}
		
		return $return;
	}
		
	public function showOrder2Detail() {
		global $dbObject;
		$return = '';
			
		$orders1 = $dbObject->fetchAll('SELECT `id`, `comp_name`, `cont_person`, `cont_email`, `cont_phone`, `cont_address`, `width`, `height`, `fixture`, `draught`, `transit`, `heating`, `gripping_1`, `gripping_2`, `comment`, `timestamp` FROM `form_order2` WHERE `id` = '.$_GET['order2-id'].' ORDER BY `id` DESC;');
		if(count($orders1) > 0) {
			$order = $orders1[0];
			$trans = $order['transit'];
			
			$transStr = '';
			if($trans >= 32) {
				$order['transit'][32] = 1;
				$trans -= 32;
				if(strlen($transStr) != 0) {
					$transStr .= ', kamiony';
				} else {
					$transStr .= 'kamiony';
				}
			}
			if($trans >= 16) {
				$order['transit'][16] = 1;
				$trans -= 16;
				if(strlen($transStr) != 0) {
					$transStr .= ', nákl.auta';
				} else {
					$transStr .= 'nákl.auta';
				}
			}
			if($trans >= 8) {
				$order['transit'][8] = 1;
				$trans -= 8;
				if(strlen($transStr) != 0) {
					$transStr .= ', os.auto';
				} else {
					$transStr .= 'os.auto';
				}
			}
			if($trans >= 4) {
				$order['transit'][4] = 1;
				$trans -= 4;
				if(strlen($transStr) != 0) {
					$transStr .= ', pal.vozík';
				} else {
					$transStr .= 'pal.vozík';
				}
			}
			if($trans >= 2) {
				$order['transit'][2] = 1;
				$trans -= 2;
				if(strlen($transStr) != 0) {
					$transStr .= ', pal.vozík';
				} else {
					$transStr .= 'pal.vozík';
				}
			}
			if($trans >= 1) {
				$order['transit'][1] = 1;
				$trans -= 1;
				if(strlen($transStr) != 0) {
					$transStr .= ', osoby';
				} else {
					$transStr .= 'osoby';
				}
			}
			
			if($order['fixture'] == 1) {
				$order['fixture'] = 'exteriér';
			} elseif($order['fixture'] == 2) {
				$order['fixture'] = 'interiér';
			}
			
			if($order['draught'] == 1) {
				$order['draught'] = 'není';
			} elseif($order['draught'] == 2) {
				$order['draught'] = 'mírný';
			} elseif($order['draught'] == 3) {
				$order['draught'] = 'velký';
			}
			
			if($order['heating'] == 1) {
				$order['heating'] = 'ano';
			} elseif($order['heating'] == 2) {
				$order['heating'] = 'ne';
			} elseif($order['heating'] == 3) {
				$order['heating'] = 'mrazírna';
			}
			
			if($order['gripping_1'] == 1) {
				$order['gripping_1'] = 'PVC';
			} elseif($order['gripping_1'] == 2) {
				$order['gripping_1'] = 'pozink';
			} elseif($order['gripping_1'] == 3) {
				$order['gripping_1'] = 'nerez';
			}
			
			if($order['gripping_2'] == 1) {
				$order['gripping_2'] = 'nad otvor';
			} elseif($order['gripping_2'] == 2) {
				$order['gripping_2'] = 'do otvoru';
			} elseif($order['gripping_2'] == 3) {
				$order['gripping_2'] = 'pojezdové';
			} elseif($order['gripping_2'] == 4) {
				$order['gripping_2'] = 'předsunuté';
			}
					
			$return .= ''
			.'<div class="order-form-2">'
				.'<table class="comment-2">'
					.'<tr class="comment-2-0">'
						.'<td class="lab">Datum a čas:</td>'
						.'<td class="val">'.(($order['timestamp'] != 0) ? date("d.m.Y H.i.s", $order['timestamp']) : 'Není zadáno.').'</td>'
					.'</tr>'
					.'<tr class="comment-2-1">'
						.'<td class="lab">Název společnosti:</td>'
						.'<td class="val">'.$order['comp_name'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-2">'
						.'<td class="lab">Kontaktní osoba:</td>'
						.'<td class="val">'.$order['cont_person'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-3">'
						.'<td class="lab">Email:</td>'
						.'<td class="val">'.$order['cont_email'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-4">'
						.'<td class="lab">Telefon:</td>'
						.'<td class="val">'.$order['cont_phone'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-5">'
						.'<td class="lab">Adresa:</td>'
						.'<td class="val">'.$order['cont_address'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-6">'
						.'<td class="lab">Rozměry otvoru:</td>'
						.'<td class="val">šířka[m]: <span class="s1">'.$order['width'].'</span><br />výška[m]: <span class="s2">'.$order['height'].'</span></td>'
					.'</tr>'
					.'<tr class="comment-2-7">'
						.'<td class="lab">umístění otvoru:</td>'
						.'<td class="val">'.$order['fixture'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-8">'
						.'<td class="lab">průvan:</td>'
						.'<td class="val">'.$order['draught'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-9">'
						.'<td class="lab">průjezd:</td>'
						.'<td class="val">'.$transStr.'</td>'
					.'</tr>'
					.'<tr class="comment-2-10">'
						.'<td class="lab">vytápění:</td>'
						.'<td class="val">'.$order['heating'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-11">'
						.'<td class="lab">uchycení:</td>'
						.'<td class="val">'.$order['gripping_1'].' - '.$order['gripping_2'].'</td>'
					.'</tr>'
					.'<tr class="comment-2-12">'
						.'<td class="lab">Poznámka:</td>'
						.'<td class="val">'.$order['comment'].'</td>'
					.'</tr>'
				.'</table>'
			.'</div>';
		} else {
			$return .= '<h4 class="error">Nebylo zadáno id objednávky</h4>';
		}
		
		return $return;
	}
		
}

?>
