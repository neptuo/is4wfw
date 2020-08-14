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
                if (count($xml->tag) > 0 || isset($xml->anyTag)) {
                    $links .= '<div>'.$rb->get('lib.tags').':';
                    foreach($xml->tag as $tag) {
                        $obsolete = null;
                        if (isset($tag->obsolete)) {
                            $obsolete = (string)$tag->obsolete;
                        }
                        $links .= '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . $obsolete . '"' : '') . 'href="#tag-'.$tag->name.'">'.$tag->name.'</a> ';
                    }

                    if (isset($xml->anyTag)) {
                        $obsolete = null;
                        if (isset($xml->anyTag->obsolete)) {
                            $obsolete = (string)$xml->anyTag->obsolete;
                        }

                        $links .= ' - <a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . $obsolete . '"' : '') . 'href="#anytag">any</a> ';
                    }

                    $links .= '</div>';
                }
                
                if (count($xml->fulltag) > 0 || isset($xml->anyFulltag)) {
                    $links .= '<div>'.$rb->get('lib.fulltags').':';
                    foreach($xml->fulltag as $tag) {
                        $obsolete = null;
                        if (isset($tag->obsolete)) {
                            $obsolete = (string)$tag->obsolete;
                        }
                        $links .= '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . $obsolete . '"' : '') . 'href="#fulltag-'.$tag->name.'">'.$tag->name.'</a> ';
                    }

                    if (isset($xml->anyFulltag)) {
                        $obsolete = null;
                        if (isset($xml->anyFulltag->obsolete)) {
                            $obsolete = (string)$xml->anyFulltag->obsolete;
                        }

                        $links .= ' - <a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . $obsolete . '"' : '') . 'href="#anyfulltag">any</a> ';
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
                        $links .= '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . $obsolete . '"' : '') . 'href="#property-'.$prop->name.'">'.$prop->name.'</a> ';
                    }
                    $links .= '</div>';
                }

                if (count($xml->decorator) > 0) {
                    $links .= '<div>'.$rb->get('lib.decorators').':';
                    foreach($xml->decorator as $decorator) {
                        $obsolete = null;
                        if (isset($decorator->obsolete)) {
                            $obsolete = (string)$decorator->obsolete;
                        }
                        
                        $attributeNames = [];
                        for ($i = 0; $i < count($decorator->attribute); $i ++) {
                            $attributeNames[] = (string)$decorator->attribute[$i]->name;
                        };
                        $name = implode(", ", $attributeNames);
                        $identifier = implode("-", $attributeNames);
                        $links .= '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . $obsolete . '"' : '') . 'href="#decorator-' . $identifier . '">' . $name . '</a> ';
                    }
                    $links .= '</div>';
                }

                $links .= '</div>';
            
                $return .= ''
                .'<div class="hint-lib">'
                    .'<div class="lib-head">'
                        .'<h1>'.$xml->name.' ('.$xml->classpath.'.'.$xml->classname.')</h1>'
                        .'<strong class="version">'.$rb->get('lib.count-of-instances').': '.$xml->count.'</strong>'
                    .'</div>'
                    .'<div class="clear"></div>'
                    .$links;

                if (count($xml->tag) > 0 || isset($xml->anyTag)) {
                    $return .= ''
                    .'<div class="tag-h2">'
                        .'<h2>'.$rb->get('lib.tags').':</h2>'
                    .'</div>';
                
                    foreach ($xml->tag as $tag) {
                        $attributes = '';
                        for ($i = 0; $i < count($tag->attribute); $i ++) {
                            $attributeName = $tag->attribute[$i]->name;
                            $cssClass = null;
                            $obsolete = null;
                            if (isset($tag->attribute[$i]->obsolete)) {
                                $obsolete = (string)$tag->attribute[$i]->obsolete;
                                $cssClass = ' obsolete';
                            }
                            $prefix = null;
                            if (isset($tag->attribute[$i]->prefix)) {
                                $prefix = $rb->get('lib.prefix.yes');
                                $attributeName = $attributeName . '-*';
                            } else {
                                $prefix = $rb->get('lib.prefix.no');
                            }
                            $required = null;
                            if (isset($tag->attribute[$i]->required)) {
                                $required = $rb->get('lib.attreq.yes');
                                $attributeName = '<strong>' . $attributeName . '</strong>';
                            } else {
                                $required = $rb->get('lib.attreq.no');
                            }

                            $attributes .= ''
                            .'<tr>'
                                .'<td class="att-name' . $cssClass . '">' . $attributeName . '</td>'
                                .'<td class="att-req' . $cssClass . '">' . $required . '</td>'
                                .'<td class="att-prefix' . $cssClass . '">' . $prefix . '</td>'
                                .'<td class="att-type' . $cssClass . '">' . $tag->attribute[$i]->type . '</td>'
                                .'<td class="att-def' . $cssClass . '">' . $tag->attribute[$i]->default . '</td>'
                                .'<td class="att-comment">' . (($obsolete != null) ? '<span><strong>Obsolete:</strong> ' . $obsolete . '</span> ' : '') . $tag->attribute[$i]->comment . '</td>'
                            .'</tr>';
                        }
                        
                        if (isset($tag->anyAttribute)) {
                            $attributes .= ''
                            .'<tr>'
                                .'<td colspan="5">'.$rb->get('lib.attparams').'</td>'
                                .'<td class="att-comment">' . $tag->anyAttribute->comment . '</td>'
                            .'</tr>';
                        }
                        
                        $obsolete = null;
                        if (isset($tag->obsolete)) {
                            $obsolete = (string)$tag->obsolete;
                        }
                        
                        $return .= ''
                        .'<div class="lib-tag">'
                            .'<div class="lib-tag-head">'
                                .'<h3 id="tag-'.$tag->name.'">'.$tag->name.'</h3>'
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

                    if (isset($xml->anyTag)) {
                        $tag = $xml->anyTag;
                        
                        $obsolete = null;
                        if (isset($tag->obsolete)) {
                            $obsolete = (string)$tag->obsolete;
                        }

                        $return .= ''
                        .'<div class="lib-tag">'
                            .'<div class="lib-tag-head">'
                                .'<h3 id="anytag">(any other tag)</h3>'
                                . (($obsolete != null) ? '<p><strong>Obsolete:</strong> ' . $obsolete . '</p>' : '')
                                .'<p>' . str_replace(PHP_EOL, '<br />', trim($tag->comment)) . '</p>'
                                . (isset($tag->lookless) ? '<p><strong>' . $rb->get('lib.taglookless') . '</strong></p>' : '')
                                .'<div class="clear"></div>'
                            .'</div>'
                        .'</div>';
                    }
                }

                if (count($xml->fulltag) > 0 || isset($xml->anyFulltag)) {
                    $return .= ''    
                    .'<div class="tag-h2">'
                        .'<h2>'.$rb->get('lib.fulltags').':</h2>'
                    .'</div>';
                            
                    foreach ($xml->fulltag as $tag) {
                        $attributes = '';
                        for ($i = 0; $i < count($tag->attribute); $i ++) {
                            $attributeName = $tag->attribute[$i]->name;
                            $cssClass = null;
                            $obsolete = null;
                            if (isset($tag->attribute[$i]->obsolete)) {
                                $obsolete = (string)$tag->attribute[$i]->obsolete;
                                $cssClass = ' obsolete';
                            }
                            $prefix = null;
                            if (isset($tag->attribute[$i]->prefix)) {
                                $prefix = $rb->get('lib.prefix.yes');
                                $attributeName = $attributeName . '-*';
                            } else {
                                $prefix = $rb->get('lib.prefix.no');
                            }
                            $required = null;
                            if (isset($tag->attribute[$i]->required)) {
                                $required = $rb->get('lib.attreq.yes');
                                $attributeName = '<strong>' . $attributeName . '</strong>';
                            } else {
                                $required = $rb->get('lib.attreq.no');
                            }

                            $attributes .= ''
                            .'<tr>'
                                .'<td class="att-name' . $cssClass . '">' . $attributeName . '</td>'
                                .'<td class="att-req' . $cssClass . '">' . $required . '</td>'
                                .'<td class="att-prefix' . $cssClass . '">' . $prefix . '</td>'
                                .'<td class="att-type' . $cssClass . '">' . $tag->attribute[$i]->type . '</td>'
                                .'<td class="att-def' . $cssClass . '">' . $tag->attribute[$i]->default . '</td>'
                                .'<td class="att-comment">' . (($obsolete != null) ? '<span><strong>Obsolete:</strong> ' . $obsolete . '</span> ' : '') . $tag->attribute[$i]->comment . '</td>'
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
                                .'<h3 id="fulltag-'.$tag->name.'">'.$tag->name.'</h3>'
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

                    if (isset($xml->anyFulltag)) {
                        $tag = $xml->anyFulltag;
                        
                        $obsolete = null;
                        if (isset($tag->obsolete)) {
                            $obsolete = (string)$tag->obsolete;
                        }
                        
                        $return .= ''
                        .'<div class="lib-tag">'
                            .'<div class="lib-tag-head">'
                                .'<h3 id="anyfulltag">(any other full tag)</h3>'
                                . (($obsolete != null) ? '<p><strong>Obsolete:</strong> ' . $obsolete . '</p>' : '')
                                .'<p>' . str_replace(PHP_EOL, '<br />', trim($tag->comment)) . '</p>'
                                . (isset($tag->lookless) ? '<p><strong>' . $rb->get('lib.taglookless') . '</strong></p>' : '')
                                .'<div class="clear"></div>'
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
                                .'<h3 id="property-'.$prop->name.'">'.$prop->name.'</h3>'
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

                if (count($xml->decorator) > 0) {
                    $return .= ''    
                    .'<div class="tag-h2">'
                        .'<h2>'.$rb->get('lib.decorators').':</h2>'
                    .'</div>';
                    
                    foreach ($xml->decorator as $decorator) {
                        $attributeNames = [];
                        $attributes = '';
                        for ($i = 0; $i < count($decorator->attribute); $i ++) {
                            $attributeName = $decorator->attribute[$i]->name;
                            $attributeNames[] = $attributeName;
                            $cssClass = null;
                            $obsolete = null;
                            if (isset($decorator->attribute[$i]->obsolete)) {
                                $obsolete = (string)$decorator->attribute[$i]->obsolete;
                                $cssClass = ' obsolete';
                            }
                            $prefix = null;
                            if (isset($decorator->attribute[$i]->prefix)) {
                                $prefix = $rb->get('lib.prefix.yes');
                                $attributeName = $attributeName . '-*';
                            } else {
                                $prefix = $rb->get('lib.prefix.no');
                            }
                            $required = null;
                            if (isset($decorator->attribute[$i]->required)) {
                                $required = $rb->get('lib.attreq.yes');
                                $attributeName = '<strong>' . $attributeName . '</strong>';
                            } else {
                                $required = $rb->get('lib.attreq.no');
                            }

                            $attributes .= ''
                            .'<tr>'
                                .'<td class="att-name' . $cssClass . '">' . $attributeName . '</td>'
                                .'<td class="att-req' . $cssClass . '">' . $required . '</td>'
                                .'<td class="att-prefix' . $cssClass . '">' . $prefix . '</td>'
                                .'<td class="att-type' . $cssClass . '">' . $decorator->attribute[$i]->type . '</td>'
                                .'<td class="att-def' . $cssClass . '">' . $decorator->attribute[$i]->default . '</td>'
                                .'<td class="att-comment">' . (($obsolete != null) ? '<span><strong>Obsolete:</strong> ' . $obsolete . '</span> ' : '') . $decorator->attribute[$i]->comment . '</td>'
                            .'</tr>';
                        }
                        
                        $obsolete = null;
                        if (isset($decorator->obsolete)) {
                            $obsolete = (string)$decorator->obsolete;
                        }
                        
                        $return .= ''
                        .'<div class="lib-tag">'
                            .'<div class="lib-tag-head">'
                                .'<h3 id="decorator-' . implode("-", $attributeNames) . '">' . implode(", ", $attributeNames) . '</h3>'
                                . (($obsolete != null) ? '<p><strong>Obsolete:</strong> ' . $obsolete . '</p>' : '')
                                .'<p>' . str_replace(PHP_EOL, '<br />', trim($decorator->comment)) . '</p>'
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

        private $libraries = [
            "php.libs.AdminUi",
            "php.libs.Article",
            "php.libs.BootstrapUi",
            "php.libs.Counter",
            "php.libs.CustomForm",
            "php.libs.CustomEntity",
            "php.libs.CustomEntityAdmin",
            "php.libs.Database",
            "php.libs.Editor",
            "php.libs.ErrorHandler",
            "php.libs.File",
            "php.libs.FileAdmin",
            "php.libs.FileUrl",
            "php.libs.Filter",
            "php.libs.FontAwesome",
            "php.libs.Google",
            "php.libs.Guestbook",
            "php.libs.Hint",
            "php.libs.hp.Hotproject",
            "php.libs.Image",
            "php.libs.Inquiry",
            "php.libs.Js",
            "php.libs.Language",
            "php.libs.Localization",
            "php.libs.Log",
            "php.libs.Login",
            "php.libs.Menu",
            "php.libs.Page",
            "php.libs.PhpRuntime",
            "php.libs.Post",
            "php.libs.QueryString",
            "php.libs.PageNG",
            "php.libs.Session",
            "php.libs.Sort",
            "php.libs.Sport",
            "php.libs.System",
            "php.libs.Template",
            "php.libs.User",
            "php.libs.Ui",
            "php.libs.Utilities",
            "php.libs.Validation",
            "php.libs.Variable",
            "php.libs.View",
            "php.libs.Web",
            "php.libs.WebProject"
        ];
            
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
                parent::redirectToSelf();
            } else {
                if ($_SESSION['select-class-path'] == '') {
                    $_SESSION['select-class-path'] = 'php.libs.Web';
                }
            }

            $options = "";
            foreach ($this->libraries as $library) {
                $options .= "<option value='$library'" . ($_SESSION['select-class-path'] == $library ? "selected='selected'" : "") . ">$library</option>";
            }

            
            $return .= ''
            .'<div class="select-class-path">'
                .'<form name="select-class-path" method="post" action="'.$_SERVER['REQUEST_URI'].'" class="auto-submit">'
                    .'<label for="select-class-path-select">'.$rb->get('select-class-path.label').':</label> '
                    .'<select id="select-class-path-select" name="select-class-path-select">'
                        . $options
                    .'</select> '
                    .'<input type="submit" name="select-class-path-submit" value="'.$rb->get('select-class-path.submit').'" />'
                .'</form>'
            .'</div>'
            .'<div class="gray-box">'
                .'There are some special GET parameters in the system, here is list of them:<br />'
                . "<strong>Debug mode must be enabled to make them work</strong>"
                .'<ul>'
                    .'<li><strong>auto-login-ignore</strong> - ignore auto-login parameteres for login:login.</li>'
                    .'<li><strong>duration-stats</strong> - time required to generate response.</li>'
                    .'<li><strong>mem-stats</strong> - wfw memory cosumption stats.</li>'
                    .'<li><strong>parser-stats</strong> - details about parsing custom tags.</li>'
                    .'<li><strong>query-stats</strong> - counts database queries per one request.</li>'
                    .'<li><strong>query-list</strong> - prints all SQL queries.</li>'
                .'</ul>'
            .'</div>';
            
            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('lib.title2').': ', $return, "", true);
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
                return parent::getFrame($rb->get('properties.title').': ', $return, "", true);
            }
        }

        public function getAutoRegistered($useFrames = false) {
            $rb = self::rb();
            
            $grid = new BaseGrid();
            $grid->setHeader([
                'prefix' => $rb->get('autoregister.prefix'), 
                'class' => $rb->get('autoregister.class')
            ]);

            foreach (parent::php()->getDefaultRegistrations() as $prefix => $classPath) {
                $grid->addRow([
                    'prefix' => $prefix, 
                    'class' => $classPath
                ]);
            }
            
            $xml = new SimpleXMLElement(file_get_contents(APP_SCRIPTS_PHP_PATH . 'autoregister.xml'));
            foreach ($xml->reg as $reg) {
                $attrs = $reg->attributes();
                $grid->addRow([
                    'prefix' => (string)$attrs['prefix'], 
                    'class' => (string)$attrs['class']
                ]);
            }

            $grid->sortByKey("prefix");
            
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
                return parent::getFrame($rb->get('autoregister.title'), $return, "", true);
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
