<?php

    session_start();

    require_once("../user/instance.inc.php");
    require_once("../app/scripts/php/includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/extensions.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/DefaultPhp.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/DefaultWeb.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/CustomTagParser.class.php");

    // ini_set('pcre.backtrack_limit', 1000000000);

    $phpObject = new DefaultPhp();
    $webObject = new DefaultWeb();
    
    $phpObject->register("cetype", "php.libs.CustomEntity");
    $phpObject->register("view", "php.libs.View");
    $phpObject->register("ui", "php.libs.Ui");

//     $Content = '<hr />
// <admin:field label="Entity Name" label-class="w90" style="background: red">
//     <input type="text" name="entity-name" />
// </admin:field>
// <hr />';
$Content = '
<filter:declare name="person" alias="p">
    <filter:and>
        <filter:equals name="lastName" value="Doe" />
        <filter:like name="firstName" startsWith="" />
        <filter:in name="typeId" values="1,3,4" />
        <filter:exists from="ce_personType" alias="t" outerColumn="typeId" innerColumn="id">
            <filter:like name="name" contains="ni" />
        </filter:exists>
    </filter:and>
</filter:declare>

<strong>Filter:</strong> <web:getProperty name="filter:person" />

<br />


<table>
    <tr>
        <th>
            <web:a pageId="web:lastPageId" text="Id" param-sort="id" />
        </th>
        <th>
            <web:a pageId="web:lastPageId" text="First Name" param-sort="firstName" />
        </th>
        <th>
            <web:a pageId="web:lastPageId" text="Last Name" param-sort="lastName" />
        </th>
        <th>Type</th>
        <th>VIP</th>
        <th></th>
    </tr>
    <var:declare name="idSort" value="" scope="request" />
    <var:declare name="firstNameSort" value="" scope="request" />
    <var:declare name="lastNameSort" value="" scope="request" />
    <web:switch when="query:sort">
        <web:case is="id">
            <var:declare name="idSort" value="asc" scope="request" />
        </web:case>
        <web:case is="firstName">
            <var:declare name="firstNameSort" value="asc" scope="request" />
        </web:case>
        <web:case is="lastName">
            <var:declare name="lastNameSort" value="asc" scope="request" />
        </web:case>
        <web:case>
            <var:declare name="idSort" value="asc" scope="request" />
        </web:case>
    </web:switch>
    
    <ce:list name="person" filter="filter:person" orderBy-id="var:idSort" orderBy-firstName="var:firstNameSort" orderBy-lastName="var:lastNameSort">
        <web:getProperty name="ce:typeId" />
        <var:declare name="personIds" value="ce:list" scope="request" select="typeId" />
        <cetype:list name="personType">
            <cetype:register name="id" />
    
            <ui:forEach items="ce:list">
                <tr>
                    <td>
                        <web:getProperty name="ce:id" />
                    </td>
                </tr> 
            </ui:forEach>
        </cetype:list>
    </ce:list>
</table>
<hr />
<web:a pageId="4" languageId="1" text="New Person" />';

// $Content = '
// <view:panel>
//     <bs:grid>
//         <view:panel>
//             <view:panel>
//                 <view:panel>
//                     <view:panel>
//                         <view:panel>
//                             <div>
//                                 <view:panel>
//                                     <view:panel>
//                                         <bs:grid>
//                                             <view:panel>
//                                                 <view:panel>
//                                                     <view:panel>
//                                                         <view:panel>
//                         Test
//                                                         </view:panel>
//                                                     </view:panel>
//                                                 </view:panel>
//                                             </view:panel>
//                                         </bs:grid>
//                                     </view:panel>
//                                 </view:panel>
//                             </div>
//                         </view:panel>
//                     </view:panel>
//                 </view:panel>
//             </view:panel>
//         </view:panel>
//     </bs:grid>
// </view:panel>';

// $Content = '
// <div>
//     <bs:grid>
//         <tr>
//             <td>
//                 <web:getProperty name="ce:id" />
//             </td>
//         </tr> 
//     </bs:grid>
// </div>
// ';

// $Content = '
// <div class="login">
//     <div class="login-head"></div>
//     <div class="login-in">
//         <login:form group="web-admins" pageId="index.view" />
//     </div>
// </div>
// ';

// $Content = '
// <bs:grid>
//     <bs:column>
//         <bs:grid>
//             <bs:column default="6">
//                 1
//             </bs:column>
//             <bs:column default="4">
//                 2
//             </bs:column>
//             <bs:column default="2">
//                 3
//             </bs:column>
//         </bs:grid>
//     </bs:column>
// </bs:grid>
// ';

    function measure($func) {
        $startTime = microtime(true);
        $func();
        $endTime = microtime(true);

        echo '<hr />';
        echo 'Duration: ' . ($endTime - $startTime) . 'ms';
    }

    function parse($parser, $content, $count, $printOutput = false) {
        for ($i=0; $i < $count; $i++) { 
            $parser->setContent($content);
            $parser->startParsing();

            if ($printOutput && $i == 0) {
                echo $parser->getResult();
            }
        }

    measure(function() {
        global $Content;
        $parser = new FullTagParser();
        parse($parser, $Content, 1);
    });

    echo '<hr />';
    echo $webObject->PageLog;

?>
