<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ViewHelper.class.php");

    /**
     * 
     *  Class View.	     
     *      
     *  @author     Marek SMM
     *  @timestamp  2011-08-22
     * 
     */
    class Menu extends BaseTagLib {

        public function __construct() {
            self::setLocalizationBundle("view");
        }

        /* ======================= TAGS ========================================= */

        public function showXmlMenu($path) {
            global $webObject;
            $return = '';

            $xml = new SimpleXMLElement(file_get_contents(ViewHelper::resolveViewRoot($path)));
            $i = 0;

            $return .= '<div class="menu"><ul class="ul-1">';

            foreach ($xml->item as $item) {
                $i++;
                $attrs = $item->attributes();
                if (isset($attrs['security:requireGroup'])) {
                    global $loginObject;
                    $ok = false;
                    foreach ($loginObject->getGroups() as $group) {
                        if ($group['name'] == $attrs['security:requireGroup']) {
                            $ok = true;
                            break;
                        }
                    }
                    if (!$ok) {
                        continue;
                    }
                }

                if (isset($attrs['security:requirePerm'])) {
                    global $loginObject;
                    $perm = $loginObject->getGroupPerm($attrs['security:requirePerm'], $loginObject->getMainGroupId(), false, 'false');
                    if($perm['value'] != 'true') {
                        continue;
                    }
                }

                $name = $attrs['name'];
                if(isset($attrs['name-' . $webObject->LanguageName])) {
                    $name = $attrs['name-' . $webObject->LanguageName];
                }
                $url = ViewHelper::resolveUrl($attrs['url']);
                if ($url == '/' . $_REQUEST['WEB_PAGE_PATH']) {
                    $active = true;
                } else {
                    $active = false;
                }

                $url = $webObject->addSpecialParams($url);

                $return .= ''
                . '<li class="menu-item li-' . $i . (($active) ? ' active-item' : '') . '">'
                    . '<div class="link' . (($active) ? ' active-link' : '') . '">'
                        . '<a href="' . $url . '"' . ((isset($attrs['rel'])) ? ' rel="' . $attrs['rel'] . '"' : '') . ' title="' . $name . '">'
                            . '<span>' . $name . '</span>'
                        . '</a>'
                    . '</div>'
                . '</li>';
            }

            $return .= '</ul></div>';

            return $return;
        }

        /* ============================= FUNCTIONS =========================================== */
    }

?>
