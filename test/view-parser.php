<?php

	require_once("../../user/instance.inc.php");
	require_once("../scripts/php/includes/settings.inc.php");
	require_once(APP_SCRIPTS_PHP_PATH . "includes/settings.inc.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/ExtensionParser.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");

	$content = ''
	.'<form name="inquiry-edit" method="post" action="{Server REQUEST_URI}">'
		.'<div class="gray-box">'
			.'<label class="w160" for="inquiry-question">{Resource label.question}</label>'
			.'<input type="text" name="inquiry-question" id="inquiry-question" value="{Binding question}" class="w300" />'
		.'</div>'
		.'<div class="gray-box">'
			.'<label class="w160" for="inquiry-enabled">{Resource label.enabled}</label>'
			.'<input type="checkbox" name="inquiry-enabled" id="inquiry-enabled" {BindingCondition (enabled) checked="checked"} />'
		.'</div>'
		.'<div class="gray-box">'
			.'<label class="w160" for="inquiry-allowmultiple">{Resource label.allowmultiple}</label>'
			.'<input type="checkbox" name="inquiry-allowmultiple" id="inquiry-allowmultiple" {BindingCondition (allowmultiple) checked="checked"} />'
		.'</div>'
		.'<div class="gray-box">'
			.'<input type="hidden" name="inquiry-id" value="{Binding id}" />'
			.'<input type="submit" name="inquiry-save" value="{Resource button.save}" /> '
			.'<input type="submit" name="inquiry-reset" value="{Resource button.reset}" class="confirm" /> '
		.'</div>'
	.'</form>';

	$rb = new LocalizationBundle();
	$rb->load('inquiry', 'en');
	
	$data = array();
	$data['question'] = 'Like it?';
	$data['enabled'] = 1;
	$data['allowmultiple'] = 1;
	
	// $parser = new ExtensionParser();
	// $parser->setContent($content);
	// $parser->setLocalizationBundle($rb);
	// $parser->setDataItem($data);
	// $parser->startParsing();
	// echo $parser->getResult();

	$parser = ExtensionParser::initialize('inquiry-edit', $rb, $data);
	echo $parser->parse();


?>