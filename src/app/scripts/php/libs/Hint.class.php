<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/BaseGrid.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Module.class.php");

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

        private $libraryLoader;

        public function __construct() {
            $this->libraryLoader = new LibraryLoader();
            $this->setLocalizationBundle("hint");
        }

        private function formatComment($value) {
            $value = trim($value);
            $value = str_replace(PHP_EOL, '<br />', $value);
            $value = str_replace("---", '</p><hr /><p>', $value);
            return $value;
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
            $rb = $this->rb();
            $return = '';

            $xmlPath = $this->libraryLoader->getXmlPath($classPath);
            
            if (is_file($xmlPath)) {
                $xml = new SimpleXMLElement(file_get_contents($xmlPath));
                
                $links = '<div class="gray-box">';
                if (count($xml->tag) > 0 || isset($xml->anyTag)) {
                    $links .= '<div>'.$rb->get('lib.tags').': ';
                    foreach($xml->tag as $tag) {
                        $obsolete = null;
                        if (isset($tag->obsolete)) {
                            $obsolete = (string)$tag->obsolete;
                        }
                        $links .= '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . htmlspecialchars($obsolete) . '"' : '') . 'href="#tag-'.$tag->name.'">'.$tag->name.'</a> ';
                    }

                    if (isset($xml->anyTag)) {
                        $obsolete = null;
                        if (isset($xml->anyTag->obsolete)) {
                            $obsolete = (string)$xml->anyTag->obsolete;
                        }

                        $links .= (count($xml->tag) > 0 ? " - " : "") . '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . htmlspecialchars($obsolete) . '"' : '') . 'href="#anytag">any</a> ';
                    }

                    $links .= '</div>';
                }
                
                if (count($xml->fulltag) > 0 || isset($xml->anyFulltag)) {
                    $links .= '<div>'.$rb->get('lib.fulltags').': ';
                    foreach($xml->fulltag as $tag) {
                        $obsolete = null;
                        if (isset($tag->obsolete)) {
                            $obsolete = (string)$tag->obsolete;
                        }
                        $links .= '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . htmlspecialchars($obsolete) . '"' : '') . 'href="#fulltag-'.$tag->name.'">'.$tag->name.'</a> ';
                    }

                    if (isset($xml->anyFulltag)) {
                        $obsolete = null;
                        if (isset($xml->anyFulltag->obsolete)) {
                            $obsolete = (string)$xml->anyFulltag->obsolete;
                        }

                        $links .= (count($xml->fulltag) > 0 ? " - " : "") . '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . htmlspecialchars($obsolete) . '"' : '') . 'href="#anyfulltag">any</a> ';
                    }
                    
                    $links .= '</div>';
                }

                if (count($xml->property) > 0 || isset($xml->anyProperty)) {
                    $links .= '<div>'.$rb->get('lib.properties').': ';
                    foreach($xml->property as $prop) {
                        $obsolete = null;
                        if (isset($prop->obsolete)) {
                            $obsolete = (string)$prop->obsolete;
                        }
                        $links .= '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . htmlspecialchars($obsolete) . '"' : '') . 'href="#property-'.$prop->name.'">'.$prop->name.'</a> ';
                    }

                    if (isset($xml->anyProperty)) {
                        $obsolete = null;
                        if (isset($xml->anyProperty->obsolete)) {
                            $obsolete = (string)$xml->anyProperty->obsolete;
                        }

                        $links .= (count($xml->property) > 0 ? " - " : "") . '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . htmlspecialchars($obsolete) . '"' : '') . 'href="#anyproperty">any</a> ';
                    }

                    $links .= '</div>';
                }

                if (count($xml->decorator) > 0) {
                    $links .= '<div>'.$rb->get('lib.decorators').': ';
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
                        $links .= '<a ' . (($obsolete != null) ? 'class="obsolete" title="Obsolete: ' . htmlspecialchars($obsolete) . '"' : '') . 'href="#decorator-' . $identifier . '">' . $name . '</a> ';
                    }
                    $links .= '</div>';
                }

                $links .= '</div>';
            
                $return .= ''
                . '<div class="hint-lib">'
                    . '<div class="lib-head">'
                        . "<h1>$classPath</h1>"
                        . '<strong class="version">'.$rb->get('lib.count-of-instances').': '. ($xml->count ?? "*") .'</strong>'
                        . "<div class='mt-1'>$xml->comment</div>"
                    . '</div>'
                    . '<div class="clear"></div>'
                    . $links;

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
                                .'<td class="att-type' . $cssClass . '">' . $tag->attribute[$i]->type . (isset($tag->attribute[$i]->preferPropertyReference) ? " <span title='property reference'>(or prop-ref)</span>" : "") . '</td>'
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
                                .'<p>' . $this->formatComment($tag->comment) . '</p>'
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
                                .'<p>' . $this->formatComment($tag->comment) . '</p>'
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
                                .'<td class="att-comment">'. $this->formatComment($tag->anyAttribute->comment) .'</td>'
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
                                .'<p>' . $this->formatComment($tag->comment) . '</p>'
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
                                .'<p>' . $this->formatComment($tag->comment) . '</p>'
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
                                .' <small>' . (($prop->getFunction) ? 'get' : '') . ' ' . (($prop->setFunction) ? 'set' : '') . '</small>'
                                . (($obsolete != null) ? '<p><strong>Obsolete:</strong> ' . $obsolete . '</p>' : '')
                                .'<p>' . $this->formatComment($prop->comment) . '</p>'
                                .'<div class="clear"></div>'
                            .'</div>'
                        .'</div>';
                    }

                    if (isset($xml->anyProperty)) {
                        $return .= ''
                        . '<div class="lib-tag">'
                            . '<h3 id="anyproperty">' . $rb->get('lib.anyproperties') . '</h3>'
                            . '<p>' . $this->formatComment($xml->anyProperty->comment) . '</p>'
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
                                .'<p>' . $this->formatComment($decorator->comment) . '</p>'
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

        /**
         *
         *    Generates form for select taglib
         *
         */                                            
        public function selectClassPath($useFrames = false, $showMsg = false) {
            $rb = $this->rb();
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
            foreach ($this->libraryLoader->all() as $library) {
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
            
            
        public function getAdminPropertyList($useFrames = false) {
            $rb = $this->rb();
            
            $grid = new BaseGrid();
            $grid->setHeader([
                'name' => 'Name', 
                'description' => 'Description'
            ]);
            
            $grid->addRow(['name' => 'Admin.Language', 'description' => '']);
            $grid->addRow(['name' => 'Article.author', 'description' => '']);
            $grid->addRow(['name' => 'Article.editAreaHeadRows', 'description' => '']);
            $grid->addRow(['name' => 'Article.editors', 'description' => '']);
            $grid->addRow(['name' => 'Article.languageId', 'description' => '']);
            $grid->addRow(['name' => 'Article.pageSize', 'description' => '']);
            $grid->addRow(['name' => 'Frames.leaveOpened', 'description' => '']);
            $grid->addRow(['name' => 'Login.session', 'description' => '']);
            $grid->addRow(['name' => 'Page.editAreaContentRows', 'description' => '']);
            $grid->addRow(['name' => 'Page.editAreaHeadRows', 'description' => '']);
            $grid->addRow(['name' => 'Page.editAreaTLEndRows', 'description' => '']);
            $grid->addRow(['name' => 'Page.editAreaTLStartRows', 'description' => '']);
            $grid->addRow(['name' => 'Page.editAreaTextFileRows', 'description' => '']);
            $grid->addRow(['name' => 'Page.editors', 'description' => 'edit_area, monaco, tiny']);
            $grid->addRow(['name' => 'Page.monacoHeight', 'description' => '(px)']);
            $grid->addRow(['name' => 'Page.monacoTheme', 'description' => 'vs, vs-dark']);
            $grid->addRow(['name' => 'System.cms.windowsstyle', 'description' => '']);
            $grid->addRow(['name' => 'TextFiles.showFilter', 'description' => '']);
            $grid->addRow(['name' => 'Templates.showFilter', 'description' => '']);
            $grid->addRow(['name' => 'WebProject.defaultProjectId', 'description' => '']);
            
            $grid->sortByKey("name");
            
            $result = $grid->render();
            if ($useFrames == "false") {
                return $result;
            } else {
                return parent::getFrame($rb->get('properties.title').': ', $result, "", true);
            }
        }

        public function getAutoRegistered($useFrames = false) {
            $rb = $this->rb();
            
            $grid = new BaseGrid();
            $grid->setHeader([
                'prefix' => $rb->get('autoregister.prefix'), 
                'class' => $rb->get('autoregister.class')
            ]);

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

        public function autoRegistered(callable $template) {
            $xml = new SimpleXMLElement(file_get_contents(APP_SCRIPTS_PHP_PATH . 'autoregister.xml'));
            
            $items = [];
            foreach ($xml->reg as $reg) {
                $items[] = $reg;
            }

            $model = new ListModel();
            $this->pushListModel($model);
            $model->items($items);
            $model->render();

            $result = $template();

            $this->popListModel();

            return $result;
        }

        public function getAutoRegisteredList() {
            return $this->peekListModel();
        }

        public function getAutoRegisteredPrefix() {
            $attributes = $this->peekListModel()->currentItem()->attributes();
            return (string)$attributes['prefix'];
        }

        public function getAutoRegisteredClassPath() {
            $attributes = $this->peekListModel()->currentItem()->attributes();
            return (string)$attributes['class'];
        }

        public function setPropClassPath($classPath) {
            $_SESSION['select-class-path'] = $classPath;
            return $classPath;
        }

        public function getPropClassPath() {
            if ($this->hasListModel()) {
                return $this->peekListModel()->currentItem();
            }

            return $_SESSION['select-class-path'];
        }

        public function libraryList($template) {
            $libraries = $this->libraryLoader->all();
			
			$model = new ListModel();
			$this->pushListModel($model);
			
			$model->render();
			$model->items($libraries);
			$result = $template();
			
			$this->popListModel();
			return $result;
        }

		public function getLibraryList() {
			return $this->peekListModel();
		}

        private $library;
        private $tagModel;
        private $fulltagModel;
        private $propertyModel;
        private $decoratorModel;
        private $constructorModel;

        public function library(callable $template, string $classPath) {
            $xmlPath = $this->libraryLoader->getXmlPath($classPath);
            
            if (is_file($xmlPath)) {
                $oldLibrary = $this->library;
                $this->library = new SimpleXMLElement(file_get_contents($xmlPath));

                $tags = [];
                foreach ($this->library->tag as $tag) {
                    $tags[] = $this->mapTag($tag);
                }
                $this->tagModel = ListModel::create($tags);

                $fulltags = [];
                foreach ($this->library->fulltag as $fulltag) {
                    $fulltags[] = $this->mapTag($fulltag);
                }
                $this->fulltagModel = ListModel::create($fulltags);

                $properties = [];
                foreach ($this->library->property as $property) {
                    $properties[] = [
                        "name" => $property->name,
                        "hasGet" => isset($property->getFunction),
                        "hasSet" => isset($property->setFunction),
                        "comment" => trim($property->comment),
                        "obsolete" => trim($property->obsolete)
                    ];
                }
                $this->propertyModel = ListModel::create($properties);

                $decorators = [];
                foreach ($this->library->decorator as $decorator) {
                    $decorators[] = [
                        "comment" => trim($decorator->comment),
                        "attributes" => $this->mapAttributes($decorator)
                    ];
                }
                $this->decoratorModel = ListModel::create($decorators);

                $constructorModel = [];
                if (isset($this->library->constructor)) {
                    $constructorModel["comment"] = trim($this->library->constructor->comment);
                    $constructorModel["attributes"] = $this->mapAttributes($this->library->constructor);
                }

                $content = $template();
                $this->library = $oldLibrary;
                return $content;
            }
        }

        private function mapTag(SimpleXMLElement $tag) {
            return [
                "name" => (string)$tag->name,
                "lookless" => isset($tag->lookless),
                "obsolete" => trim($tag->obsolete),
                "comment" => trim($tag->comment),
                "attributes" => ListModel::create($this->mapAttributes($tag)),
                "anyAttribute" => isset($tag->anyAttribute),
                "anyAttributeComment" => isset($tag->anyAttribute) ? trim($tag->anyAttribute->comment) : null
            ];
        }

        private function mapAttributes(SimpleXMLElement $parent) {
            $attributes = [];
            foreach ($parent->attribute as $attribute) {
                $attributes[] = [
                    "name" => (string)$attribute->name,
                    "type" => isset($attribute->type) ? (string)$attribute->type : "string",
                    "prefix" => isset($attribute->prefix),
                    "required" => isset($attribute->required),
                    "default" => isset($attribute->default) ? $attribute->default : null,
                    "obsolete" => trim($attribute->obsolete),
                    "comment" => trim($attribute->comment)
                ];
            }

            return $attributes;
        }

        private function getFieldOnModel($model, $name1, $name2 = null) {
            $field = $model->field($name1);
            if ($name2) {
                $field = $field->field($name2);
            }

            return $field;
        }
        
        public function getTagList() {
            return $this->tagModel;
        }

        public function getFulltagList() {
            return $this->fulltagModel;
        }

        private function getTagField($name1, $name2 = null) {
            if ($this->tagModel && $this->tagModel->isIterate()) {
                return $this->getFieldOnModel($this->tagModel, $name1, $name2);
            }

            if ($this->fulltagModel && $this->fulltagModel->isIterate()) {
                return $this->getFieldOnModel($this->fulltagModel, $name1, $name2);
            }

            if ($this->decoratorModel && $this->decoratorModel->isIterate()) {
                return $this->getFieldOnModel($this->decoratorModel, $name1, $name2);
            }

            return null;
        }

        public function getTagName() {
            return $this->getTagField("name");
        }

        public function getTagLookless() {
            return $this->getTagField("lookless");
        }

        public function getTagComment() {
            return $this->getTagField("comment");
        }

        public function getTagObsolete() {
            return $this->getTagField("obsolete");
        }

        public function getTagAttributeList() {
            return $this->getTagField("attributes");
        }

        public function getTagAttributeName() {
            return $this->getTagField("attributes", "name");
        }
        
        public function getTagAttributeType() {
            return $this->getTagField("attributes", "type");
        }

        public function getTagAttributePrefix() {
            return $this->getTagField("attributes", "prefix");
        }

        public function getTagAttributeRequired() {
            return $this->getTagField("attributes", "required");
        }

        public function getTagAttributeDefault() {
            return $this->getTagField("attributes", "default");
        }

        public function getTagAttributeObsolete() {
            return $this->getTagField("attributes", "obsolete");
        }

        public function getTagAttributeComment() {
            return $this->getTagField("attributes", "comment");
        }

        public function getTagAnyAttribute() {
            return $this->getTagField("anyAttribute");
        }

        public function getTagAnyAttributeComment() {
            return $this->getTagField("anyAttributeComment");
        }

        public function getPropertyList() {
            return $this->propertyModel;
        }

        public function getPropertyName() {
            return $this->propertyModel->field("name");
        }

        public function getPropertyComment() {
            return $this->propertyModel->field("comment");
        }

        public function getPropertyObsolete() {
            return $this->propertyModel->field("obsolete");
        }

        public function getPropertyHasGet() {
            return $this->propertyModel->field("hasGet");
        }

        public function getPropertyHasSet() {
            return $this->propertyModel->field("hasSet");
        }

        public function getDecoratorList() {
            return $this->decoratorModel;
        }

        public function getDecoratorComment() {
            return $this->getTagField("comment");
        }

        public function getDecoratorAttributeList() {
            return $this->getTagField("attributes");
        }

        public function getConstructorComment() {
            return $this->constructorModel["comment"];
        }

        public function getConstructorAttributeList() {
            return $this->constructorModel["attributes"];
        }

        public function getAnyTag() {
            return isset($this->library->anyTag);
        }

        public function getAnyTagComment() {
            return $this->getAnyTag() ? trim($this->library->anyTag->comment) : null;
        }

        public function getAnyTagObsolete() {
            return $this->getAnyTag() ? trim($this->library->anyTag->obsolete) : null;
        }

        public function getAnyFulltag() {
            return isset($this->library->anyFulltag);
        }

        public function getAnyFulltagComment() {
            return $this->getAnyFulltag() ? trim($this->library->anyFulltag->comment) : null;
        }

        public function getAnyFulltagObsolete() {
            return $this->getAnyTag() ? trim($this->library->anyFulltag->obsolete) : null;
        }

        public function getAnyProperty() {
            return isset($this->library->anyProperty);
        }

        public function getAnyPropertyComment() {
            return $this->getAnyProperty() ? trim($this->library->anyProperty->comment) : null;
        }

        public function getAnyPropertyObsolete() {
            return $this->getAnyTag() ? trim($this->library->anyProperty->obsolete) : null;
        }

        public function getAnyPropertyHasGet() {
            return $this->getAnyProperty() ? isset($this->library->anyProperty->getFunction) : false;
        }

        public function getAnyPropertyHasSet() {
            return $this->getAnyProperty() ? isset($this->library->anyProperty->setFunction) : false;
        }

        public function getComment() {
            return trim($this->library->comment);
        }
    }

?>
