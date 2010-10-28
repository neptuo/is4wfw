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
			ok	web:pair
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
			ok	styly do Wysiwygu pro webovy projekt -> samostatnou tabulku! -> pripojovat pres samosattny php soubor, ten vybere style podle session selected project
			??	Page pos????
			ok	priznak cachetime do editace stranky a do urlcache -> cachovat vygenerovane stranky ;) -> vyber z doby ulozeni cache, zkontrolovat reseni v parsePages
			--	moznost vyrabet vlastni framy pres web:frame
					- dodelat aby ten reg exp fungoval i pro obsah na vice radku ...
			!!	odstranovani html znacek ve wysiwygu
			ok	zobrazit pocet zbyvajicich znaku do max delky stranky
			ok	spravne ukladani prav kdyz nemam pravo mazat !!!!!!!!!!!!!!!!!!!!!!!!!
			--	templaty pro menu -> pak bude mozne injektovat treba rady clanku atd .
			--	vypis obsahu logu (log pro jednotlive projekty zvlast)
			ok	otestovat web:pair -> na galerii 
			??	pridavani text filu se stejnym nazvem ... OK??
			ok	do tempLoadedContent vybirat stranky ve spravnem poradi!
			ok	BUG v pridavani clanku na papayateam ...
			--	copy pages -> pouziti SELECT INTO ??
			ok	deklarace chybovych stranek v editaci projektu
			--	Knihovna JS, pro js utilitky ... napr. citac body do vyprseni prihlaseni
			ok	tag posledni update stranky, web:lastPageUpdate
			ok	copy right years - neco jako 2000 - 2009
			??	"counter manager" -> implementovat pouze rucne ..
			ok	zajistit "smazani" cachovaneho soubory pri clear url cache & cachovani stranky (nebo pri vytvoreni nove urlcache vytvaret take novy cache soubor, VZDY!)
			--	web:currentUrl -> vrati aktualni adresu ;)
			ok	slozkovani v PHP LIBS 
			!!	nahravani souboru se stejnym jmenem ale jinou koncovkou -> BUG
			--	pres file.php pristup k templatum.
			--	v url projektu "*" ... default pro vsechny url
			ok	v url projektu nebo stranky "/".
			--	sprava pro web pairs.
			--	v registraci noveho uzivatele asociace s pairs.
			--	moznost mit ruzna url pro http a https
			--	moznost mit nazev jazykove verze pred domenou ( cs.epapaya.cz )
			ok	velka pismena v url stranky -> upravit Page.class.php -> pri uprave url velka zmensuje na mala!
			--	menu, order by name ...
	*/
  
  require_once("scripts/php/includes/settings.inc.php");
  require_once("scripts/php/includes/database.inc.php");
  require_once("scripts/php/includes/version.inc.php");
  require_once("scripts/php/includes/extensions.inc.php");
  require_once("scripts/php/libs/DefaultPhp.class.php");
  require_once("scripts/php/libs/DefaultWeb.class.php");
  
  session_start();
  
  $phpObject = new DefaultPhp();
  $webObject = new DefaultWeb();
  
  require_once("scripts/php/includes/postinit.inc.php");
  require_once("scripts/php/includes/autoupdate.inc.php");
  
  $webObject->processRequest();

?>