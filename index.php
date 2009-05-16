<?php

	/*
			ok	u composeUrl, pokud je pageId z jineho projektu, vygenerovat absolutni url!
			ok	u redirectTo, pokud je pageId z jineho projektu, vygenerovat absolutni url!
			ok	dodelat web_alias
			ok	spravne vybrat defaultni prava u noveho projektu
			??	v Page a v WebProject opravit vypis zadanych prav pri novem zaznamu ktery je znovu odeslan
			??	po vytvoreni noveho projektu vytvorit i uvodni stranku
			ok	defaultni prava stranky podle rodice, pokud parentId = 0, tak z prav projektu
			ok	web projekty do textFile
			ok	presouvani stranek mezi projekty
			ok	predelat clanky
			ok	prava pro article line
			??	sloupec creator podle uid do clanku
			ok	templaty pro vse (clanky, pocitadla) do CMS a zobrazovat pres templateId
			ok	DIRECTORY & FM:
					- prava pro adresare
					- pri vypisu vypisovat vse, kam kde mam pravo READ
					- moznost zapisu vsude kde mam WRITE r
					- moznost mazat ze vsech kde mam DELETE pravo
			ok	editace adresare
			??	Counter primary key ID + WP, spise ne, moznost pouzit pocitadlo z jineho projektu
			ok	sprava uzivatelskych skupin
			??	co s uzivatelem ktery nesmi ani do jednoho projektu
			??	opravdu Counter pocita
			ok	pro article template rozdelit datum a cas
			ok	v template parsovat c-tagy (nepouzivat zadne "tpl:", ale skutecne tagy)
			??	presmerovani po loginu (napuvodni adresu)
			--	web:pair
			ok	zaskrtavatko do user mgmt Enable/Disable
			??	otevreni/zavreni framu pomoci cookies JS + Server Side
			ok	k Move To jeste Copy To
			ok	doplnit MIME TYPy
			ok	ikony do FS
			??	favicon, {mime type pro ICO} -> pridavat pouze do headu stranky
			ok	manazer skupin
			??	lepsi pouziti web aliasu
			ok	pro admina vypis logu uzivatelu a moznost jeho smazani.
			ok	pocitadlo casu do odhlaseni
	*/
  
  require_once("scripts/php/includes/settings.inc.php");
  require_once("scripts/php/libs/DefaultPhp.class.php");
  require_once("scripts/php/libs/DefaultWeb.class.php");
  
  session_start();
  
  $phpObject = new DefaultPhp();
  $webObject = new DefaultWeb();
  
  $webObject->loadPageContent();
  $webObject->flush();

?>