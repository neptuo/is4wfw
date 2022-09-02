<php:lazy docs="php.libs.Hint" />
<template:declare identifier="tag">
    <json:object>
        <utils:concat output="name" separator=":" value1="docs:autoRegisteredPrefix" value2="docs:tagName" />
        <json:key name="name" value="utils:name" />
        <json:key name="description" value="docs:tagComment" />
        <json:key name="attributes">
            <json:array>
                <ui:forEach items="docs:tagAttributeList">
                    <json:object>
                        <json:key name="name" value="docs:tagAttributeName" />
                        <json:key name="description" value="docs:tagAttributeComment" />
                    </json:object>
                </ui:forEach>
                <web:out if:true="docs:tagAnyAttribute">
                    <json:object>
                        <json:key name="name" value="*" />
                        <json:key name="description" value="docs:tagAnyAttributeComment" />
                    </json:object>
                </web:out>
            </json:array>
        </json:key>
    </json:object>
</template:declare>
<json:output>
    <json:array>
        <docs:autoRegistered>
            <ui:forEach items="docs:autoRegistered">
                <docs:library classPath="docs:autoRegisteredClassPath">
                    <ui:forEach items="docs:tagList">
                        <template:tag />
                    </ui:forEach>
                    <ui:forEach items="docs:fulltagList">
                        <template:tag />
                    </ui:forEach>
                </docs:library>
            </ui:forEach>
        </docs:autoRegistered>
    </json:array>
</json:output>