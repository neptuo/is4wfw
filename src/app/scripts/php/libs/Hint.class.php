<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/BaseGrid.class.php");

    /**
     * 
     *  Class Hint.
     *  Manual for custom tags         
     *      
     *  @author     Marek SMM
     *  @timestamp  2012-01-10
     * 
     */  
    class Hint extends BaseTagLib {

        public function __construct() {
            self::setTagLibXml("Hint.xml");
            self::setLocalizationBundle("hint");
        }

        /**
         *
         *    Generates hint for selected library.
         *    C tag.
         *    
         *    @param        classPath                    library class path
         *    @param        userFrames                use frames in outpout
         *    @param        showMsg                        show error messages in output                                         
         *
         *
         */                                    
        public function showHintForLib($classPath = false, $useFrames = false, $showMsg = false) {
            global $phpObject;
            $rb = self::rb();
            $return = '';
            
            $cpArray = $phpObject->str_tr($classPath, '.');
            $xmlPath = APP_SCRIPTS_PATH;
            for ($i = 0; $i < count($cpArray); $i ++) {
                if($i < count($cpArray) - 1) {
                    $xmlPath .= $cpArray[$i] . '/';
                } else {
                    $xmlPath .= $cpArray[$i] . '.xml';
                }
            }
                
            if (is_file($xmlPath)) {
                $xml = new SimpleXMLElement(file_get_contents($xmlPath));
                
                $links = '<div class="gray-box">';
                if (count($xml->tag) > 0) {
                    $links .= '<div>'.$rb->get('lib.tags').':';
                    foreach($xml->tag as $tag) {
                        $obsolete = null;
                        if (isset($tag->obsolete)) {
                            $obsolete = (string)$tag->obsolete;
                        }
                        $links .= '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . $obsolete . '"' : '') . 'href="#tag-'.$tag->tagname.'">'.$tag->tagname.'</a> ';
                    }
                    $links .= '</div>';
                }
                
                if (count($xml->fulltag) > 0) {
                    $links .= '<div>'.$rb->get('lib.fulltags').':';
                    foreach($xml->fulltag as $tag) {
                        $obsolete = null;
                        if (isset($tag->obsolete)) {
                            $obsolete = (string)$tag->obsolete;
                        }
                        $links .= '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . $obsolete . '"' : '') . 'href="#fulltag-'.$tag->tagname.'">'.$tag->tagname.'</a> ';
                    }
                    $links .= '</div>';
                }

                if (count($xml->property) > 0) {
                    $links .= '<div>'.$rb->get('lib.properties').':';
                    foreach($xml->property as $prop) {
                        $obsolete = null;
                        if (isset($prop->obsolete)) {
                            $obsolete = (string)$prop->obsolete;
                        }
                        $links .= '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . $obsolete . '"' : '') . 'href="#property-'.$prop->propname.'">'.$prop->propname.'</a> ';
                    }
                    $links .= '</div>';
                }

                $links .= '</div>';
            
                $return .= ''
                .'<div class="hint-lib">'
                    .'<div class="lib-head">'
                        .'<h1>'.$xml->name.' ( '.$xml->classpath.'.'.$xml->classname.' )</h1>'
                        .'<strong class="version">'.$rb->get('lib.version').': '.$xml->version.', '.$rb->get('lib.count-of-instances').': '.$xml->count.'</strong>'
                    .'</div>'
                    .'<div class="clear"></div>'
                    .$links;

                if (count($xml->tag) > 0) {
                    $return .= ''
                    .'<div class="tag-h2">'
                        .'<h2>'.$rb->get('lib.tags').':</h2>'
                    .'</div>';
                
                    foreach ($xml->tag as $tag) {
                        $attributes = '';
                        for ($i = 0; $i < count($tag->attribute); $i ++) {
                            $attributeName = $tag->attribute[$i]->attname;
                            $cssClass = null;
                            $obsolete = null;
                            if (isset($tag->attribute[$i]->obsolete)) {
                                $obsolete = (string)$tag->attribute[$i]->obsolete;
                                $cssClass = ' obsolete';
                            }
                            $prefix = null;
                            if ($tag->attribute[$i]->prefix == 'true') {
                                $prefix = $rb->get('lib.prefix.yes');
                                $attributeName = $attributeName . '-*';
                            } else {
                                $prefix = $rb->get('lib.prefix.no');
                            }
                            $required = null;
                            if ($tag->attribute[$i]->attreq == 'required') {
                                $required = $rb->get('lib.attreq.yes');
                                $attributeName = '<strong>' . $attributeName . '</strong>';
                            } else if ($tag->attribute[$i]->attreq == 'implied') {
                                $required = $rb->get('lib.attreq.no');
                            }

                            $attributes .= ''
                            .'<tr>'
                                .'<td class="att-name' . $cssClass . '">' . $attributeName . '</td>'
                                .'<td class="att-req' . $cssClass . '">' . $required . '</td>'
                                .'<td class="att-prefix' . $cssClass . '">' . $prefix . '</td>'
                                .'<td class="att-type' . $cssClass . '">' . $tag->attribute[$i]->atttype . '</td>'
                                .'<td class="att-def' . $cssClass . '">' . $tag->attribute[$i]->attdef . '</td>'
                                .'<td class="att-comment">' . (($obsolete != null) ? '<span><strong>Obsolete:</strong> ' . $obsolete . '</span> ' : '') . $tag->attribute[$i]->attcomment . '</td>'
                            .'</tr>';
                        }
                        
                        if (isset($tag->anyAttribute)) {
                            $attributes .= ''
                            .'<tr>'
                                .'<td colspan="3">'.$rb->get('lib.attparams').'</td>'
                            .'</tr>';
                        }
                        
                        $obsolete = null;
                        if (isset($tag->obsolete)) {
                            $obsolete = (string)$tag->obsolete;
                        }
                        
                        $return .= ''
                        .'<div class="lib-tag">'
                            .'<div class="lib-tag-head">'
                                .'<h3 id="tag-'.$tag->tagname.'">'.$tag->tagname.'</h3>'
                                . (($obsolete != null) ? '<p><strong>Obsolete:</strong> ' . $obsolete . '</p>' : '')
                                .'<p>' . str_replace(PHP_EOL, '<br />', trim($tag->comment)) . '</p>'
                                . (isset($tag->lookless) ? '<p><strong>' . $rb->get('lib.taglookless') . '</strong></p>' : '')
                                .'<div class="clear"></div>'
                            .'</div>'
                            .'<div class="lib-tag-attrs">'
                                .((strlen($attributes) > 0) ? ''
                                    .'<table>'
                                        .'<tr>'
                                            .'<th class="att-name">'.$rb->get('lib.attname').'</th>'
                                            .'<th class="att-req">'.$rb->get('lib.attreq').'</th>'
                                            .'<th class="att-prefix">'.$rb->get('lib.prefix').'</th>'
                                            .'<th class="att-type">'.$rb->get('lib.atttype').'</th>'
                                            .'<th class="att-def">'.$rb->get('lib.attdef').'</th>'
                                            .'<th class="att-comment">'.$rb->get('lib.attcomment').'</th>'
                                        .'</tr>'
                                        .$attributes
                                    .'</table>'
                                : '<p>'.$rb->get('lib.noattrs').'</p>')
                            .'</div>'
                        .'</div>';
                    }
                }

                if (count($xml->fulltag) > 0) {
                    $return .= ''    
                    .'<div class="tag-h2">'
                        .'<h2>'.$rb->get('lib.fulltags').':</h2>'
                    .'</div>';
                            
                    foreach ($xml->fulltag as $tag) {
                        $attributes = '';
                        for ($i = 0; $i < count($tag->attribute); $i ++) {
                            $attributeName = $tag->attribute[$i]->attname;
                            $cssClass = null;
                            $obsolete = null;
                            if (isset($tag->attribute[$i]->obsolete)) {
                                $obsolete = (string)$tag->attribute[$i]->obsolete;
                                $cssClass = ' obsolete';
                            }
                            $prefix = null;
                            if ($tag->attribute[$i]->prefix == 'true') {
                                $prefix = $rb->get('lib.prefix.yes');
                                $attributeName = $attributeName . '-*';
                            } else {
                                $prefix = $rb->get('lib.prefix.no');
                            }
                            $required = null;
                            if ($tag->attribute[$i]->attreq == 'required') {
                                $required = $rb->get('lib.attreq.yes');
                                $attributeName = '<strong>' . $attributeName . '</strong>';
                            } else if ($tag->attribute[$i]->attreq == 'implied') {
                                $required = $rb->get('lib.attreq.no');
                            }

                            $attributes .= ''
                            .'<tr>'
                                .'<td class="att-name' . $cssClass . '">' . $attributeName . '</td>'
                                .'<td class="att-req' . $cssClass . '">' . $required . '</td>'
                                .'<td class="att-prefix' . $cssClass . '">' . $prefix . '</td>'
                                .'<td class="att-type' . $cssClass . '">' . $tag->attribute[$i]->atttype . '</td>'
                                .'<td class="att-def' . $cssClass . '">' . $tag->attribute[$i]->attdef . '</td>'
                                .'<td class="att-comment">' . (($obsolete != null) ? '<span><strong>Obsolete:</strong> ' . $obsolete . '</span> ' : '') . $tag->attribute[$i]->attcomment . '</td>'
                            .'</tr>';
                        }

                        if (isset($tag->anyAttribute)) {
                            $attributes .= ''
                            .'<tr>'
                                .'<td colspan="5">'.$rb->get('lib.attparams').'</td>'
                                .'<td class="att-comment">'. str_replace(PHP_EOL, '<br />', trim($tag->anyAttribute->comment)) .'</td>'
                            .'</tr>';
                        }
                        
                        $obsolete = null;
                        if (isset($tag->obsolete)) {
                            $obsolete = (string)$tag->obsolete;
                        }
                        
                        $return .= ''
                        .'<div class="lib-tag">'
                            .'<div class="lib-tag-head">'
                                .'<h3 id="fulltag-'.$tag->tagname.'">'.$tag->tagname.'</h3>'
                                . (($obsolete != null) ? '<p><strong>Obsolete:</strong> ' . $obsolete . '</p>' : '')
                                .'<p>' . str_replace(PHP_EOL, '<br />', trim($tag->comment)) . '</p>'
                                . (isset($tag->lookless) ? '<p><strong>' . $rb->get('lib.taglookless') . '</strong></p>' : '')
                                .'<div class="clear"></div>'
                            .'</div>'
                            .'<div class="lib-tag-attrs">'
                                .((strlen($attributes) > 0) ? ''
                                    .'<table>'
                                        .'<tr>'
                                            .'<th class="att-name">'.$rb->get('lib.attname').'</th>'
                                            .'<th class="att-req">'.$rb->get('lib.attreq').'</th>'
                                            .'<th class="att-prefix">'.$rb->get('lib.prefix').'</th>'
                                            .'<th class="att-type">'.$rb->get('lib.atttype').'</th>'
                                            .'<th class="att-def">'.$rb->get('lib.attdef').'</th>'
                                            .'<th class="att-comment">'.$rb->get('lib.attcomment').'</th>'
                                        .'</tr>'
                                        .$attributes
                                    .'</table>'
                                : '<p>'.$rb->get('lib.noattrs').'</p>')
                            .'</div>'
                        .'</div>';
                    }
                }

                if (count($xml->property) > 0 || isset($xml->anyProperty)) {
                    $return .= ''    
                    .'<div class="tag-h2">'
                        .'<h2>'.$rb->get('lib.properties').':</h2>'
                    .'</div>';
                
                    foreach($xml->property as $prop) {
                        $obsolete = null;
                        if (isset($prop->obsolete)) {
                            $obsolete = (string)$prop->obsolete;
                        }
                        
                        $return .= ''
                        .'<div class="lib-tag">'
                            .'<div class="lib-tag-head">'
                                .'<h3 id="property-'.$prop->propname.'">'.$prop->propname.'</h3>'
                                . (($obsolete != null) ? '<p><strong>Obsolete:</strong> ' . $obsolete . '</p>' : '')
                                .'<p>' . str_replace(PHP_EOL, '<br />', trim($prop->comment)) . '</p>'
                                .'<div class="clear"></div>'
                            .'</div>'
                        .'</div>';
                    }

                    if (isset($xml->anyProperty)) {
                        $return .= ''
                        . '<div class="lib-tag">'
                            . '<h3>' . $rb->get('lib.anyproperties') . '</h3>'
                            . '<p>' . str_replace(PHP_EOL, '<br />', trim($xml->anyProperty->comment)) . '</p>'
                        . '</div>';
                    }
                }

                $return .= '</div>';
                
            } else {
                $str = "Xml library definition doesn't exists! [".$xmlPath."]";
                if($showMsg != 'false') {
                    $return .= $str;
                }
            }
                
            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('lib.title').': '.$classPath, $return, "", true);
            }
        }
            
        /**
         *
         *    Generates form for select taglib
         *
         */                                            
        public function selectClassPath($useFrames = false, $showMsg = false) {
            $rb = self::rb();
            $return = '';
            
            if ($_POST['select-class-path-submit'] == $rb->get('select-class-path.submit')) {
                $_SESSION['select-class-path'] = $_POST['select-class-path-select'];
                if($showMsg != 'false') {
                    $return .= '<h4 class="success">'.$rb->get('select-class-path.success').'</h4>';
                }
            } else {
                if($_SESSION['select-class-path'] == '') {
                    $_SESSION['select-class-path'] = 'php.libs.DefaultWeb';
                }
            }
            
            $return .= ''
            .'<div class="select-class-path">'
                .'<form name="select-class-path" method="post" action="'.$_SERVER['REQUEST_URI'].'" class="auto-submit">'
                    .'<label for="select-class-path-select">'.$rb->get('select-class-path.label').':</label> '
                    .'<select id="select-class-path-select" name="select-class-path-select">'
                        .'<option value="php.libs.AdminUi"'.($_SESSION['select-class-path'] == 'php.libs.AdminUi' ? 'selected="selected"' : '').'>php.libs.AdminUi</option>'
                        .'<option value="php.libs.Article"'.($_SESSION['select-class-path'] == 'php.libs.Article' ? 'selected="selected"' : '').'>php.libs.Article</option>'
                        .'<option value="php.libs.BootstrapUi"'.($_SESSION['select-class-path'] == 'php.libs.BootstrapUi' ? 'selected="selected"' : '').'>php.libs.BootstrapUi</option>'
                        .'<option value="php.libs.Counter"'.($_SESSION['select-class-path'] == 'php.libs.Counter' ? 'selected="selected"' : '').'>php.libs.Counter</option>'
                        .'<option value="php.libs.CustomForm"'.($_SESSION['select-class-path'] == 'php.libs.CustomForm' ? 'selected="selected"' : '').'>php.libs.CustomForm</option>'
                        .'<option value="php.libs.CustomEntity"'.($_SESSION['select-class-path'] == 'php.libs.CustomEntity' ? 'selected="selected"' : '').'>php.libs.CustomEntity</option>'
                        .'<option value="php.libs.CustomEntityAdmin"'.($_SESSION['select-class-path'] == 'php.libs.CustomEntityAdmin' ? 'selected="selected"' : '').'>php.libs.CustomEntityAdmin</option>'
                        .'<option value="php.libs.Database"'.($_SESSION['select-class-path'] == 'php.libs.Database' ? 'selected="selected"' : '').'>php.libs.Database</option>'
                        .'<option value="php.libs.DefaultPhp"'.($_SESSION['select-class-path'] == 'php.libs.DefaultPhp' ? 'selected="selected"' : '').'>php.libs.DefaultPhp</option>'
                        .'<option value="php.libs.DefaultWeb"'.($_SESSION['select-class-path'] == 'php.libs.DefaultWeb' ? 'selected="selected"' : '').'>php.libs.DefaultWeb</option>'
                        .'<option value="php.libs.Error"'.($_SESSION['select-class-path'] == 'php.libs.Error' ? 'selected="selected"' : '').'>php.libs.Error</option>'
                        .'<option value="php.libs.File"'.($_SESSION['select-class-path'] == 'php.libs.File' ? 'selected="selected"' : '').'>php.libs.File</option>'
                        .'<option value="php.libs.FileAdmin"'.($_SESSION['select-class-path'] == 'php.libs.FileAdmin' ? 'selected="selected"' : '').'>php.libs.FileAdmin</option>'
                        .'<option value="php.libs.Form"'.($_SESSION['select-class-path'] == 'php.libs.Form' ? 'selected="selected"' : '').'>php.libs.Form</option>'
                        .'<option value="php.libs.Google"'.($_SESSION['select-class-path'] == 'php.libs.Google' ? 'selected="selected"' : '').'>php.libs.Google</option>'
                        .'<option value="php.libs.Guestbook"'.($_SESSION['select-class-path'] == 'php.libs.Guestbook' ? 'selected="selected"' : '').'>php.libs.Guestbook</option>'
                        .'<option value="php.libs.Hint"'.($_SESSION['select-class-path'] == 'php.libs.Hint' ? 'selected="selected"' : '').'>php.libs.Hint</option>'
                        .'<option value="php.libs.hp.Hotproject"'.($_SESSION['select-class-path'] == 'php.libs.hp.Hotproject' ? 'selected="selected"' : '').'>php.libs.hp.Hotproject</option>'
                        .'<option value="php.libs.Image"'.($_SESSION['select-class-path'] == 'php.libs.Image' ? 'selected="selected"' : '').'>php.libs.Image</option>'
                        .'<option value="php.libs.Inquiry"'.($_SESSION['select-class-path'] == 'php.libs.Inquiry' ? 'selected="selected"' : '').'>php.libs.Inquiry</option>'
                        .'<option value="php.libs.Js"'.($_SESSION['select-class-path'] == 'php.libs.Js' ? 'selected="selected"' : '').'>php.libs.Js</option>'
                        .'<option value="php.libs.Localization"'.($_SESSION['select-class-path'] == 'php.libs.Localization' ? 'selected="selected"' : '').'>php.libs.Localization</option>'
                        .'<option value="php.libs.Log"'.($_SESSION['select-class-path'] == 'php.libs.Log' ? 'selected="selected"' : '').'>php.libs.Log</option>'
                        .'<option value="php.libs.Login"'.($_SESSION['select-class-path'] == 'php.libs.Login' ? 'selected="selected"' : '').'>php.libs.Login</option>'
                        .'<option value="php.libs.Menu"'.($_SESSION['select-class-path'] == 'php.libs.Menu' ? 'selected="selected"' : '').'>php.libs.Menu</option>'
                        .'<option value="php.libs.Page"'.($_SESSION['select-class-path'] == 'php.libs.Page' ? 'selected="selected"' : '').'>php.libs.Page</option>'
                        .'<option value="php.libs.PageNG"'.($_SESSION['select-class-path'] == 'php.libs.PageNG' ? 'selected="selected"' : '').'>php.libs.PageNG</option>'
                        .'<option value="php.libs.Session"'.($_SESSION['select-class-path'] == 'php.libs.Session' ? 'selected="selected"' : '').'>php.libs.Session</option>'
                        .'<option value="php.libs.Sport"'.($_SESSION['select-class-path'] == 'php.libs.Sport' ? 'selected="selected"' : '').'>php.libs.Sport</option>'
                        .'<option value="php.libs.System"'.($_SESSION['select-class-path'] == 'php.libs.System' ? 'selected="selected"' : '').'>php.libs.System</option>'
                        .'<option value="php.libs.Template"'.($_SESSION['select-class-path'] == 'php.libs.Template' ? 'selected="selected"' : '').'>php.libs.Template</option>'
                        .'<option value="php.libs.User"'.($_SESSION['select-class-path'] == 'php.libs.User' ? 'selected="selected"' : '').'>php.libs.User</option>'
                        .'<option value="php.libs.Ui"'.($_SESSION['select-class-path'] == 'php.libs.Ui' ? 'selected="selected"' : '').'>php.libs.Ui</option>'
                        .'<option value="php.libs.Utilities"'.($_SESSION['select-class-path'] == 'php.libs.Utilities' ? 'selected="selected"' : '').'>php.libs.Utilities</option>'
                        .'<option value="php.libs.Variable"'.($_SESSION['select-class-path'] == 'php.libs.Variable' ? 'selected="selected"' : '').'>php.libs.Variable</option>'
                        .'<option value="php.libs.View"'.($_SESSION['select-class-path'] == 'php.libs.View' ? 'selected="selected"' : '').'>php.libs.View</option>'
                        .'<option value="php.libs.WebProject"'.($_SESSION['select-class-path'] == 'php.libs.WebProject' ? 'selected="selected"' : '').'>php.libs.WebProject</option>'
                    .'</select> '
                    .'<input type="submit" name="select-class-path-submit" value="'.$rb->get('select-class-path.submit').'" />'
                .'</form>'
            .'</div>'
            .'<div class="gray-box">'
                .'<strong>There are some special GET parameters in the system, here is list of them: </strong>'
                .'<ul>'
                    .'<li><strong>auto-login-ignore</strong> - ignore auto-login parameteres for login:login.</li>'
                    .'<li><strong>duration-stats</strong> - time required to generate response.</li>'
                    .'<li><strong>mem-stats</strong> - wfw memory cosumption stats.</li>'
                    .'<li><strong>query-stats</strong> - counts database queries per one request.</li>'
                    .'<li><strong>query-list</strong> - prints all SQL queries.</li>'
                .'</ul>'
            .'</div>';
            
            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('lib.title2').': '.$classPath, $return, "", true);
            }
        }
            
            
        public function getPropertyList($useFrames = false) {
            $rb = self::rb();
            
            $return = ''
            .'<div class="gray-box">'
                .$rb->get('properties.headline').':'
            .'</div>'
            .'<div class="gray-box">'
                .'<ul>'
                    .'<li>Admin.Language</li>'
                    .'<li>Article.author</li>'
                    .'<li>Article.editAreaHeadRows</li>'
                    .'<li>Article.editors</li>'
                    .'<li>Article.languageId</li>'
                    .'<li>Article.pageSize</li>'
                    .'<li>Frames.leaveOpened</li>'
                    .'<li>Login.session</li>'
                    .'<li>Page.editAreaContentRows</li>'
                    .'<li>Page.editAreaHeadRows</li>'
                    .'<li>Page.editAreaTLEndRows</li>'
                    .'<li>Page.editAreaTLStartRows</li>'
                    .'<li>Page.editAreaTextFileRows</li>'
                    .'<li>Page.editors</li>'
                    .'<li>System.cms.windowsstyle</li>'
                    .'<li>TextFiles.showFilter</li>'
                    .'<li>Templates.showFilter</li>'
                    .'<li>WebProject.defaultProjectId</li>'
                .'</ul>'
            .'</div>';
            
            
            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('properties.title').': '.$classPath, $return, "", true);
            }
        }

        public function getAutoRegistered($useFrames = false) {
            $rb = self::rb();
            
            $grid = new BaseGrid();
            $grid->setHeader(array(
                'prefix' => $rb->get('autoregister.prefix'), 
                'class' => $rb->get('autoregister.class')
            ));
            
            $xml = new SimpleXMLElement(file_get_contents(APP_SCRIPTS_PHP_PATH . 'autoregister.xml'));
            foreach ($xml->reg as $reg) {
                $attrs = $reg->attributes();
                $grid->addRow(array(
                    'prefix' => (string)$attrs['prefix'], 
                    'class' => (string)$attrs['class']
                ));
            }
            
            $return = ''
            . '<div class="gray-box">'
                . $rb->get('autoregister.headline')
            . '</div>'
            . '<div class="gray-box">'
                . $grid->render()
            . '</div>';

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('autoregister.title').': '.$classPath, $return, "", true);
            }
        }



        public function setPropClassPath($classPath) {
            $_SESSION['select-class-path'] = $classPath;
            return $classPath;
        }

        public function getPropClassPath() {
            return $_SESSION['select-class-path'];
        }
    }

?>
