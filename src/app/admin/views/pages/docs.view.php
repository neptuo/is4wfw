<php:lazy docs="php.libs.Hint" />

<template:declare identifier="nameDocs">
    <web:out if:stringEmpty="template:obsolete" if:not="true">
        <span class="text-linethrough">
    </web:out>
    <web:out text="template:name" />
    <web:out if:stringEmpty="template:obsolete" if:not="true">
        </span>
    </web:out>
</template:declare>
<template:declare identifier="cardTitleDocs">
    <h5 class="card-title mb-0">
        <template:nameDocs name="template:name" obsolete="template:obsolete" />
    </h5>
    <web:out if:stringEmpty="template:obsolete" if:not="true">
        <span class="ml-1">
            <web:out text="template:obsolete" />
        </span>
    </web:out>
</template:declare>
<template:declare identifier="commentDocs">
    <var:declare name="commentDocs" value="template:text" />
    <utils:replaceHtmlNewLines output="var:commentDocs" input="var:commentDocs" />
    <utils:replaceString output="var:commentDocs" search="---">
        <hr>
    </utils:replaceString>
    <web:out text="var:commentDocs" />
</template:declare>
<template:declare identifier="tagDocs">
    <utils:concat output="tagId" value1="template:prefix" value2="docs:tagName" separator="-" />
    <bs:card id="utils:tagId" class="mt-2">
        <div class="d-flex align-items-center mb-3">
            <template:cardTitleDocs name="docs:tagName" obsolete="docs:tagObsolete" />
            <web:out if:true="docs:tagLookless">
                <span class="ml-1">
                    <fa5:icon prefix="fas" name="eye-slash" title="Lookless" class="text-muted" />
                </span>
            </web:out>
        </div>
        <p>
            <template:commentDocs text="docs:tagComment" />
        </p>

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
        <div class="d-flex align-items-center mb-3">
            <template:cardTitleDocs name="template:name" obsolete="template:obsolete" />
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
        </div>
        <p>
            <template:commentDocs text="template:comment" />
        </p>
    </bs:card>
</template:declare>

<div class="m-md-2 m-lg-4">
    <bs:row class="form-row">
        <bs:column default="3">
            <bs:card>
                <div class="list-group list-group-flush">
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
            <web:out if:stringEmpty="query:l" if:not="true">
                <docs:library classPath="query:l">
                    <bs:card>
                        <h3>
                            <web:out text="query:l" />
                        </h3>
                        <p>
                            <template:commentDocs text="docs:comment" />
                        </p>
                        <hr />
                        <div>
                            <h6 class="mb-0 mt-2">Tags</h6>
                            <ui:forEach items="docs:tagList">
                                <a href="#tag-<web:out text="docs:tagName" />">
                                    <template:nameDocs name="docs:tagName" obsolete="docs:tagObsolete" />
                                </a>
                            </ui:forEach>
                            <web:out if:true="docs:anyTag">
                                <a href="#tag-any">
                                    <template:nameDocs name="any" />
                                </a>
                            </web:out>
                        </div>
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
                        <div>
                            <h6 class="mb-0 mt-2">Decorators</h6>
                            <ui:forEach items="docs:decoratorList">
                                ...
                            </ui:forEach>
                        </div>
                    </bs:card>
                    <ui:forEach items="docs:tagList">
                        <template:tagDocs prefix="tag" />
                    </ui:forEach>
                    <web:out if:true="docs:anyTag">
                        <bs:card id="fulltag-any" class="mt-2">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="card-title mb-0">
                                    any
                                </h5>
                            </div>
                            <p>
                                <template:commentDocs text="docs:anyTagComment" />
                            </p>
                        </bs:card>
                    </web:out>
                    <ui:forEach items="docs:fulltagList">
                        <template:tagDocs prefix="fulltag" />
                    </ui:forEach>
                    <web:out if:true="docs:anyFulltag">
                        <bs:card id="tag-any" class="mt-2">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="card-title mb-0">
                                    any
                                </h5>
                            </div>
                            <p>
                                <template:commentDocs text="docs:anyFulltagComment" />
                            </p>
                        </bs:card>
                    </web:out>
                    <ui:forEach items="docs:propertyList">
                        <template:propertyDocs name="docs:propertyName" comment="docs:propertyComment" obsolete="docs:propertyObsolete" hasGet="docs:propertyHasGet" hasSet="docs:propertyHasSet" />
                    </ui:forEach>
                    <web:out if:true="docs:anyProperty">
                        <template:propertyDocs name="any" comment="docs:anyPropertyComment" obsolete="docs:anyPropertyObsolete" hasGet="docs:anyPropertyHasGet" hasSet="docs:anyPropertyHasSet" />    
                    </web:out>
                </docs:library>
            </web:out>
        </bs:column>
    </bs:row>
</div>