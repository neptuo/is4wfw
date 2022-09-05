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
<template:declare identifier="tagDocs">
    <utils:concat output="tagId" value1="template:prefix" value2="docs:tagName" separator="-" />
    <bs:card id="utils:tagId" class="mt-2">
        <div class="d-flex">
            <h5 class="card-title">
                <template:nameDocs name="docs:tagName" obsolete="docs:tagObsolete" />
                <web:out if:true="docs:tagLookless">
                    <fa5:icon prefix="fas" name="eye-slash" title="Lookless" class="text-muted" />
                </web:out>
            </h5>
            <span class="ml-1">
                <web:out text="docs:tagObsolete" />
            </span>
        </div>
        <p>
            <web:out text="docs:tagComment" />
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
                    <web:out text="docs:tagAttributeComment" />
                </bs:column>
            </bs:row>
        </ui:forEach>
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
                        <div>
                            <web:out text="docs:comment" />
                        </div>
                        <hr />
                        <div>
                            <h6 class="mb-0 mt-2">Tags</h6>
                            <ui:forEach items="docs:tagList">
                                <a href="#tag-<web:out text="docs:tagName" />">
                                    <template:nameDocs name="docs:tagName" obsolete="docs:tagObsolete" />
                                </a>
                            </ui:forEach>
                        </div>
                        <div>
                            <h6 class="mb-0 mt-2">Fulltags</h6>
                            <ui:forEach items="docs:fulltagList">
                                <a href="#fulltag-<web:out text="docs:tagName" />">
                                    <template:nameDocs name="docs:tagName" obsolete="docs:tagObsolete" />
                                </a>
                            </ui:forEach>
                        </div>
                        <div>
                            <h6 class="mb-0 mt-2">Properties</h6>
                            <ui:forEach items="docs:propertyList">
                                <a href="#property-<web:out text="docs:propertyName" />">
                                    <template:nameDocs name="docs:propertyName" obsolete="docs:propertyObsolete" />
                                </a>
                            </ui:forEach>
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
                    <ui:forEach items="docs:fulltagList">
                        <template:tagDocs prefix="fulltag" />
                    </ui:forEach>
                </docs:library>
            </web:out>
        </bs:column>
    </bs:row>
</div>