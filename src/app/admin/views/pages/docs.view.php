<php:lazy docs="php.libs.Hint" />

<template:declare identifier="decoratorNameDocs">
    <var:declare name="decoratorNameDocs" value="template:prefix" />
    <ui:forEach items="docs:tagAttributeList">
        <utils:concat output="var:decoratorNameDocs" value1="var:decoratorNameDocs" value2="docs:tagAttributeName" separator="template:separator" />
    </ui:forEach>
    <template:content name="var:decoratorNameDocs" />
</template:declare> 
<template:declare identifier="sectionDocs">
    <div class="pt-2 sticky-top" style="top:56px;background:#ccc">
        <bs:card>
            <h3 class="m-0">
                <web:out text="template:name" />
            </h3>
        </bs:card>
    </div>
</template:declare>
<template:declare identifier="nameDocs">
    <web:lookless>
        <var:declare name="nameDocsCssClass" value="" />
        <web:out if:stringEmpty="template:obsolete" if:not="true">
            <var:declare name="nameDocsCssClass" value="text-linethrough" />
        </web:out>
    </web:lookless>
    <span class="<web:out text="var:nameDocsCssClass" />">
        <web:out text="template:name" />
    </span>
</template:declare>
<template:declare identifier="cardTitleDocs">
    <div class="d-flex align-items-center mb-3">
        <h5 class="card-title mb-0">
            <template:nameDocs name="template:name" obsolete="template:obsolete" />
        </h5>
        <web:out if:stringEmpty="template:obsolete" if:not="true">
            <span class="ml-1">
                <web:out text="template:obsolete" />
            </span>
        </web:out>
        <template:content />
    </div>
</template:declare>
<template:declare identifier="commentDocs">
    <var:declare name="commentDocs" value="template:text" />
    <utils:replaceHtmlNewLines output="var:commentDocs" input="var:commentDocs" />
    <utils:replaceString output="var:commentDocs" search="---">
        <hr>
    </utils:replaceString>
    <web:out text="var:commentDocs" />
</template:declare>
<template:declare identifier="tagAttributeList">
    <ui:forEach items="docs:tagAttributeList">
        <bs:row class="form-row py-2 border-top row-hover">
            <bs:column default="3">
                <web:out if:true="docs:tagAttributeRequired">
                    <strong>
                </web:out>
                <var:declare name="tagAttributeName" value="docs:tagAttributeName" />
                <web:out if:true="docs:tagAttributePrefix">
                    <utils:concat output="var:tagAttributeName" value1="var:tagAttributeName" value2="-*" />
                </web:out>
                <web:out text="var:tagAttributeName" />
                <web:out if:true="docs:tagAttributeRequired">
                    </strong>
                </web:out>
                <web:out if:stringEmpty="docs:tagAttributeDefault" if:not="true">
                    (= <web:out text="docs:tagAttributeDefault" />)
                </web:out>
            </bs:column>
            <bs:column default="1">
                <web:out text="docs:tagAttributeType" />
            </bs:column>
            <bs:column default="8">
                <template:commentDocs text="docs:tagAttributeComment" />
            </bs:column>
        </bs:row>
    </ui:forEach>
</template:declare>
<template:declare identifier="tagDocs">
    <utils:concat output="tagId" value1="template:prefix" value2="docs:tagName" separator="-" />
    <bs:card id="utils:tagId" class="mt-2">
        <template:cardTitleDocs name="docs:tagName" obsolete="docs:tagObsolete">
            <web:out if:true="docs:tagLookless">
                <span class="ml-1">
                    <fa5:icon prefix="fas" name="eye-slash" title="Lookless" class="text-muted" />
                </span>
            </web:out>
        </template:cardTitleDocs>
        <p>
            <template:commentDocs text="docs:tagComment" />
        </p>

        <template:tagAttributeList />
        <web:out if:true="docs:tagAnyAttribute">
            <bs:row class="form-row py-2 border-top row-hover">
                <bs:column default="4">
                    any
                </bs:column>
                <bs:column default="8">
                    <template:commentDocs text="docs:tagAnyAttributeComment" />
                </bs:column>
            </bs:row>
        </web:out>
    </bs:card>
</template:declare>
<template:declare identifier="propertyDocs">
    <utils:concat output="propertyId" value1="property" value2="template:name" separator="-" />
    <bs:card id="utils:propertyId" class="mt-2">
        <template:cardTitleDocs name="template:name" obsolete="template:obsolete">
            <web:out if:true="template:hasGet">
                <small class="ml-1">
                    get
                </small>
            </web:out>
            <web:out if:true="template:hasSet">
                <small class="ml-1">
                    set
                </small>
            </web:out>
        </template:cardTitleDocs>
        <p>
            <template:commentDocs text="template:comment" />
        </p>
    </bs:card>
</template:declare>

<js:style>
    .docs-nav-links a {
        display: inline-block;
    }
</js:style>
<js:script placement="tail">
    (function() {
        const $container = $(".library-search");
        const $input = $container.find("input");
        const $links = $container.find("a");

        function filter(filter) {
            $links.removeClass("d-none");
            if (filter) {
                const regex = new RegExp(filter, "i");
                $links.filter((i, link) => !link.innerHTML.match(regex)).addClass("d-none");
                window.localStorage.setItem("library-filter", filter);
            } else {
                window.localStorage.removeItem("library-filter");
            }
        }
        
        $input.keyup(() => filter($input.val()));

        const lastFilter = window.localStorage.getItem("library-filter");
        if (lastFilter) {
            $input.val(lastFilter);
            filter(lastFilter);
        }
    })();
</js:script>

<div class="m-md-2 m-lg-4">
    <bs:row class="form-row">
        <bs:column default="3">
            <bs:card>
                <div class="list-group list-group-flush library-search">
                    <input type="text" placeholder="Search... (ctrl+/)" class="form-control mb-2" data-keybinding="ctrl+/" />
                    <docs:libraryList>
                        <ui:forEach items="docs:libraryList">
                            <web:a pageId="route:docs" class="list-group-item px-2 text-truncate" param-l="docs:classPath" text="docs:classPath" />
                        </ui:forEach>
                    </docs:libraryList>
                </div>
            </bs:card>
        </bs:column>
        <bs:column default="9">
            <var:declare name="library" value="query:l" />
            <web:out if:stringEmpty="query:l">
                <var:declare name="library" value="php.libs.Web" />
            </web:out>
            <docs:library classPath="var:library">
                <bs:card>
                    <h3>
                        <web:out text="var:library" />
                    </h3>
                    <p>
                        <template:commentDocs text="docs:libraryComment" />
                    </p>
                    <hr />
                    <if:eval name="hasTags">
                        <if:or>
                            <ui:count items="docs:tagList">
                                <if:greater value="ui:count" than="0" />
                            </ui:count>
                            <if:equals value="docs:anyTag" is="php:true" />
                        </if:or>
                    </if:eval>
                    <if:eval name="hasFulltags">
                        <if:or>
                            <ui:count items="docs:fulltagList">
                                <if:greater value="ui:count" than="0" />
                            </ui:count>
                            <if:equals value="docs:anyFulltag" is="php:true" />
                        </if:or>
                    </if:eval>
                    <if:eval name="hasProperties">
                        <if:or>
                            <ui:count items="docs:propertyList">
                                <if:greater value="ui:count" than="0" />
                            </ui:count>
                            <if:equals value="docs:anyProperty" is="php:true" />
                        </if:or>
                    </if:eval>
                    <if:eval name="hasDecorators">
                        <ui:count items="docs:decoratorList">
                            <if:greater value="ui:count" than="0" />
                        </ui:count>
                    </if:eval>
                    <div class="docs-nav-links">
                        <web:out if:passed="hasTags">
                            <div>
                                <h6 class="mb-0 mt-2">Tags</h6>
                                <ui:forEach items="docs:tagList">
                                    <a href="#tag-<web:out text="docs:tagName" />"><template:nameDocs name="docs:tagName" obsolete="docs:tagObsolete" /></a>
                                </ui:forEach>
                                <web:out if:true="docs:anyTag">
                                    <a href="#tag-any">
                                        <template:nameDocs name="any" />
                                    </a>
                                </web:out>
                            </div>
                        </web:out>
                        <web:out if:passed="hasFulltags">
                            <div>
                                <h6 class="mb-0 mt-2">Fulltags</h6>
                                <ui:forEach items="docs:fulltagList">
                                    <a href="#fulltag-<web:out text="docs:tagName" />">
                                        <template:nameDocs name="docs:tagName" obsolete="docs:tagObsolete" />
                                    </a>
                                </ui:forEach>
                                <web:out if:true="docs:anyFulltag">
                                    <a href="#fulltag-any">
                                        <template:nameDocs name="any" />
                                    </a>
                                </web:out>
                            </div>
                        </web:out>
                        <web:out if:passed="hasProperties">
                            <div>
                                <h6 class="mb-0 mt-2">Properties</h6>
                                <ui:forEach items="docs:propertyList">
                                    <a href="#property-<web:out text="docs:propertyName" />">
                                        <template:nameDocs name="docs:propertyName" obsolete="docs:propertyObsolete" />
                                    </a>
                                </ui:forEach>
                                <web:out if:true="docs:anyProperty">
                                    <a href="#property-any">
                                        <template:nameDocs name="any" />
                                    </a>
                                </web:out>
                            </div>
                        </web:out>
                        <web:out if:passed="hasDecorators">
                            <div>
                                <h6 class="mb-0 mt-2">Decorators</h6>
                                <ui:forEach items="docs:decoratorList">
                                    <template:decoratorNameDocs separator=", ">
                                        <var:declare name="decoratorDisplayName" value="template:name" />
                                    </template:decoratorNameDocs>
                                    <template:decoratorNameDocs prefix="decorator" separator="-">
                                        <var:declare name="decoratorIdName" value="template:name" />
                                    </template:decoratorNameDocs>
                                    <a href="#<web:out text="var:decoratorIdName" />">
                                        <template:nameDocs name="var:decoratorDisplayName" />
                                    </a>
                                </ui:forEach>
                            </div>
                        </web:out>
                    </div>
                </bs:card>
                <bs:card class="mt-2" if:true="docs:constructor">
                    <template:cardTitleDocs name="constructor" />
                    <p>
                        <template:commentDocs text="docs:constructorComment" />
                    </p>
                    
                    <template:tagAttributeList />
                </bs:card>
                
                <web:out if:passed="hasTags">
                    <template:sectionDocs name="Tags" />
                    <ui:forEach items="docs:tagList">
                        <template:tagDocs prefix="tag" />
                    </ui:forEach>
                    <web:out if:true="docs:anyTag">
                        <bs:card id="fulltag-any" class="mt-2">
                            <template:cardTitleDocs name="any" />
                            <p>
                                <template:commentDocs text="docs:anyTagComment" />
                            </p>
                        </bs:card>
                    </web:out>
                </web:out>
                <web:out if:passed="hasFulltags">
                <template:sectionDocs name="Fulltags" />
                    <ui:forEach items="docs:fulltagList">
                        <template:tagDocs prefix="fulltag" />
                    </ui:forEach>
                    <web:out if:true="docs:anyFulltag">
                        <bs:card id="tag-any" class="mt-2">
                            <template:cardTitleDocs name="any" />
                            <p>
                                <template:commentDocs text="docs:anyFulltagComment" />
                            </p>
                        </bs:card>
                    </web:out>
                </web:out>
                <web:out if:passed="hasProperties">
                    <template:sectionDocs name="Properties" />
                    <ui:forEach items="docs:propertyList">
                        <template:propertyDocs name="docs:propertyName" comment="docs:propertyComment" obsolete="docs:propertyObsolete" hasGet="docs:propertyHasGet" hasSet="docs:propertyHasSet" />
                    </ui:forEach>
                    <web:out if:true="docs:anyProperty">
                        <template:propertyDocs name="any" comment="docs:anyPropertyComment" obsolete="docs:anyPropertyObsolete" hasGet="docs:anyPropertyHasGet" hasSet="docs:anyPropertyHasSet" />    
                    </web:out>
                </web:out>
                <web:out if:passed="hasDecorators">
                    <template:sectionDocs name="Decorators" />
                    <ui:forEach items="docs:decoratorList">
                        <template:decoratorNameDocs separator=", ">
                            <var:declare name="decoratorDisplayName" value="template:name" />
                        </template:decoratorNameDocs>
                        <template:decoratorNameDocs prefix="decorator" separator="-">
                            <var:declare name="decoratorIdName" value="template:name" />
                        </template:decoratorNameDocs>
                        <bs:card id="var:decoratorIdName" class="mt-2">
                            <template:cardTitleDocs name="var:decoratorDisplayName" />
                            <p>
                                <template:commentDocs text="docs:decoratorComment" />
                            </p>
                    
                            <template:tagAttributeList />
                        </bs:card>
                    </ui:forEach>
                </web:out>
            </docs:library>
        </bs:column>
    </bs:row>
</div>