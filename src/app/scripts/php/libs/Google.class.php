<?php

   require_once("BaseTagLib.class.php");

    /**
    * 
    *  Class Google. 
    *      
    *  @author     maraf
    *  @timestamp  2018-05-04
    * 
    */
    class Google extends BaseTagLib {

        public function analytics($id) {
            $return = ''
            . "<script>" . PHP_EOL 
            . "  (function (i, s, o, g, r, a, m) {" . PHP_EOL 
            . "     i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {" . PHP_EOL 
            . "        (i[r].q = i[r].q || []).push(arguments)" . PHP_EOL 
            . "     }, i[r].l = 1 * new Date(); a = s.createElement(o)," . PHP_EOL 
            . "     m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)" . PHP_EOL 
            . "  })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');" . PHP_EOL 
            . "" . PHP_EOL 
            . "  ga('create', '" . $id . "', 'auto');" . PHP_EOL 
            . "  ga('send', 'pageview');" . PHP_EOL 
            . "</script>";

            return $return;
        }
    }

?>