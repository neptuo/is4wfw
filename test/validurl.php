<pre><?php

    require_once("../user/instance.inc.php");
    require_once("../app/scripts/php/includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/UrlUtils.class.php");

    function x($string) {
        $url = UrlUtils::toValidUrl($string);
        echo $url . PHP_EOL;
    }

    x("räksmörgås och köttbullar");
    x("sdk jaskldj askdjal  lskdj áěščý áýíáf ýsdífá  ýíěášýč áíýdfíá ýěšýěí í ěí ěíěí &&&");
    x("Bílý koník, jež běží přes překážky, si radostně vízká!");
    x("Víte, čš/toto/&&ěšč se používalo pro /kurzívu/.");



?></pre>