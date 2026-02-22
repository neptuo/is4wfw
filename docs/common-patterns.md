# Common Patterns

This document covers recurring patterns and best practices for building applications with is4wfw, drawn from real usage in the admin panel.

## Layout Composition

### Master Layout with Content Slot

Layouts use `<template:content />` as the insertion point for page content:

```xml
<!-- views/layouts/main.view.php -->
<html>
<head>
    <title><web:out text="web:pageTitle" /></title>
    <bs:resources />
    <fa5:resources />
    <js:style path="~/css/app.css" />
</head>
<body>
    <nav class="navbar">
        <web:a pageId="1" text="Home" class="navbar-brand" />
    </nav>

    <bs:container>
        <template:content />
    </bs:container>

    <js:script placement="tail" path="~/js/app.js" />
</body>
</html>
```

### Dynamic Layout Selection

Switch layouts based on a variable or condition:

```xml
<var:declare name="templateName" value="main" />

<web:switch when="var:templateName">
    <web:case is="php:null">
        <router:render />
    </web:case>
    <web:case>
        <layouts:include identifier="var:templateName">
            <router:render />
        </layouts:include>
    </web:case>
</web:switch>
```

## CRUD Page Pattern

The standard create/read/update/delete pattern used throughout the admin panel:

### List + Edit on Same Page

```xml
<web:frame title="Products">
    <!-- DELETE action -->
    <web:condition when="query:delete">
        <ce:deleter name="products" id="query:delete">
            <web:redirectToSelf />
        </ce:deleter>
    </web:condition>

    <!-- EDIT form (shown when editId is present) -->
    <web:condition when="query:editId">
        <edit:form submit="saveProduct">
            <web:condition when="edit:saved">
                <admin:redirectAfterSave
                    closePageId="web:currentPage" />
            </web:condition>

            <web:condition when="edit:save">
                <ce:save name="products" key-id="query:editId" />
            </web:condition>

            <ce:form name="products" key-id="query:editId">
                <admin:field label="Name">
                    <ui:textbox name="name" class="form-control" />
                </admin:field>
                <admin:field label="Price">
                    <ui:textbox name="price" class="form-control" />
                </admin:field>
                <admin:saveButtons closePageId="web:currentPage" />
            </ce:form>
        </edit:form>
    </web:condition>

    <!-- LIST (always visible) -->
    <ce:list name="products">
        <ui:grid items="ce:list" class="table">
            <ui:column header="Name" value="list:name" />
            <ui:column header="Price" value="list:price" />
            <ui:columnTemplate header="">
                <web:a pageId="web:currentPage" param-editId="list:id"
                    text="Edit" class="btn btn-sm btn-primary" />
                <admin:deleteButton hiddenField="delete"
                    confirmValue="list:id" />
            </ui:columnTemplate>
        </ui:grid>
    </ce:list>

    <admin:newButton pageId="web:currentPage" text="Add Product"
        paramName="editId" param="0" />
</web:frame>
```

### Admin Edit Mode Pattern

The `admin:edit` helper simplifies detecting new vs. edit modes:

```xml
<admin:edit id="query:id">
    <web:condition when="admin:edit">
        <!-- Editing existing record -->
        <h3>Edit: <web:out text="admin:editTitle" /></h3>
    </web:condition>
    <web:condition when="admin:new">
        <!-- Creating new record -->
        <h3>New Item</h3>
    </web:condition>
</admin:edit>
```

## Form Handling

### The `edit:form` Lifecycle

Every form follows the same phase pattern:

```xml
<edit:form submit="formName">
    <!-- Phase: saved (after successful save, typically redirect) -->
    <web:condition when="edit:saved">
        <web:redirectToSelf />
    </web:condition>

    <!-- Phase: save (process form data) -->
    <web:condition when="edit:save">
        <!-- Save to database, send email, etc. -->
    </web:condition>

    <!-- Phase: render (display form fields) -->
    <ui:textbox name="field1" />
    <ui:textarea name="field2" />
    <button type="submit">Submit</button>
</edit:form>
```

**Important**: Check `edit:saved` before `edit:save` — the redirect in `saved` prevents the form from re-rendering after a successful save.

### Form with Validation

```xml
<edit:form submit="register">
    <web:condition when="edit:save">
        <!-- Set up validation rules -->
        <val:required key="name" />
        <val:email key="email" />

        <!-- Only save if validation passes -->
        <if:eval name="isValid">
            <!-- Check val:hasErrors or similar -->
        </if:eval>

        <ce:save name="users" />
    </web:condition>

    <div class="form-group">
        <label>Name</label>
        <ui:textbox name="name" class="form-control" />
        <val:message key="name" />
    </div>
    <div class="form-group">
        <label>Email</label>
        <ui:textbox name="email" class="form-control" />
        <val:message key="email" />
    </div>
</edit:form>
```

### Filter Forms (Search/Filter)

```xml
<ui:filter session="productFilter" pageId="web:currentPage">
    <ui:textbox name="search" default="" />
    <ui:dropdownlist name="categoryId"
        source="categories" display="name" value="id"
        emptyText="All" />
    <button type="submit">Filter</button>
</ui:filter>
```

Filter values persist in the session, so they survive page navigation.

## Conditional Rendering Patterns

### Simple Value Check

```xml
<web:condition when="var:status" is="active">
    <span class="badge badge-success">Active</span>
</web:condition>
```

### Negation (Else)

```xml
<web:condition when="var:status" is="active">
    <span class="badge badge-success">Active</span>
</web:condition>
<web:condition when="var:status" is="active" isInverted="true">
    <span class="badge badge-secondary">Inactive</span>
</web:condition>
```

### Existence Check (Non-Empty)

```xml
<!-- Check if query parameter exists -->
<web:condition when="query:editId">
    <!-- Show edit form -->
</web:condition>
```

### Complex Boolean Logic

```xml
<if:eval name="showAdvanced">
    <if:and>
        <if:equals value="login:isLogged" is="php:true" />
        <if:or>
            <if:equals value="var:role" is="admin" />
            <if:equals value="var:role" is="editor" />
        </if:or>
        <if:not>
            <if:equals value="var:maintenance" is="php:true" />
        </if:not>
    </if:and>
</if:eval>

<web:out if:passed="showAdvanced">
    <!-- Advanced options -->
</web:out>
```

### String Checks

```xml
<!-- Check if string contains substring -->
<if:eval name="hasIcon">
    <if:stringContains value="var:cssClass" part="icon" />
</if:eval>

<!-- Check string length -->
<if:eval name="isLong">
    <if:stringLength value="var:description" min="100" />
</if:eval>
```

## Data Iteration Patterns

### Basic Loop with Grid

```xml
<ce:list name="products">
    <ui:grid items="ce:list" class="table table-striped">
        <ui:column header="Name" value="list:name" />
        <ui:column header="Price" value="list:price" />
        <ui:columnBoolean header="In Stock" value="list:inStock" />
        <ui:columnDateTime header="Added" value="list:created" format="d.m.Y" />
    </ui:grid>
</ce:list>
```

### Nested Loops (Hierarchical Data)

```xml
<ui:forEach items="var:categories">
    <h3><web:out text="list:name" /></h3>
    <ul>
        <ui:forEach items="var:allProducts" filter-categoryId="list:id">
            <li><web:out text="list:productName" /></li>
        </ui:forEach>
    </ul>
</ui:forEach>
```

### Number Iterator

```xml
<ui:numberIterator from="1" to="5">
    <span>Star <web:out text="ui:numberIterator" /></span>
</ui:numberIterator>
```

### First/Last Item

```xml
<ui:first items="var:articles">
    <div class="featured">
        <h2><web:out text="list:title" /></h2>
    </div>
</ui:first>
```

## Routing Patterns

### File-Based Routing

```xml
<router:fromPath>
    <router:file path="dashboard" name="dashboard">
        <p>Dashboard content</p>
    </router:file>

    <router:directory path="settings">
        <router:file path="profile" name="settings-profile">
            <p>Profile settings</p>
        </router:file>
        <router:file path="security" name="settings-security">
            <p>Security settings</p>
        </router:file>
    </router:directory>

    <!-- Wildcard fallback -->
    <router:file path="*" name="not-found">
        <p>Page not found</p>
    </router:file>
</router:fromPath>

<router:render />
```

### Named Routes for Links

```xml
<!-- Declare routes -->
<route:declare name="dashboard" url="/dashboard" />
<route:declare name="userEdit" url="/users/edit" />

<!-- Use route URLs in links -->
<a href="<web:out text="route:dashboard" />">Dashboard</a>

<!-- Check if a route is active -->
<route:use name="dashboard">
    <li class="<web:out if:true="route:isActive" text="active" />">
        <a href="<web:out text="route:url" />">Dashboard</a>
    </li>
</route:use>
```

### Routing with View Includes

```xml
<router:fromPath>
    <router:file path="users" name="users">
        <views:include identifier="pages/users" />
    </router:file>
    <router:file path="articles" name="articles">
        <views:include identifier="pages/articles" />
    </router:file>
</router:fromPath>

<router:render />
```

## Permission Gating

### Page-Level Protection

```xml
<!-- Redirect unauthenticated users -->
<login:redirectWhenNotLogged pageId="3" />

<!-- Require specific group -->
<login:authorized all="admins">
    <!-- Entire page content -->
</login:authorized>
```

### Element-Level Protection

```xml
<web:out security:requireGroup="admins">
    <button>Delete All</button>
</web:out>

<web:out security:requirePerm="edit-articles">
    <web:a pageId="10" text="Edit" />
</web:out>
```

### Multiple Group Check

```xml
<!-- User must be in ALL listed groups -->
<login:authorized all="admins,editors">
    <p>Admin editors only</p>
</login:authorized>

<!-- User must be in ANY of the listed groups -->
<login:authorized any="admins,editors,moderators">
    <p>Staff content</p>
</login:authorized>
```

## Variable Building Pipeline

Build complex values step by step:

```xml
<!-- Start with a base class -->
<var:declare name="cssClass" value="card" />

<!-- Conditionally append classes -->
<web:condition when="var:isActive" is="true">
    <utils:concat output="var:cssClass" separator=" "
        value1="var:cssClass" value2="card-active" />
</web:condition>

<web:condition when="var:isHighlighted" is="true">
    <utils:concat output="var:cssClass" separator=" "
        value1="var:cssClass" value2="card-highlighted" />
</web:condition>

<!-- Use the built CSS class -->
<div class="<web:out text="var:cssClass" />">
    ...
</div>
```

## Localization

### Setting Up

```xml
<loc:use name="myapp">
    <!-- All loc: references inside resolve from the "myapp" bundle -->
    <h1><web:out text="loc:page.title" /></h1>
    <p><web:out text="loc:welcome.message" /></p>
    <button><web:out text="loc:button.save" /></button>
</loc:use>
```

### Multi-Language Content

```xml
<lang:list>
    <ui:forEach items="lang:list">
        <a href="<web:out text="lang:url" />">
            <web:out text="lang:natural_name" />
        </a>
    </ui:forEach>
</lang:list>
```

## Pagination

```xml
<paging:container size="10" number="query:page">
    <ce:list name="products" paging="paging:container">
        <ui:forEach items="ce:list">
            <p><web:out text="list:name" /></p>
        </ui:forEach>
    </ce:list>

    <!-- Pagination links -->
    <bs:paging>
        <ui:forEach items="paging:list">
            <bs:pageLink
                url="web:currentPage"
                isActive="paging:number"
                param-page="paging:number" />
        </ui:forEach>
    </bs:paging>
</paging:container>
```

## JSON API Endpoints

### Returning JSON

```xml
<json:output>
    <json:object>
        <json:key name="status" value="ok" />
        <json:key name="count" value="var:total" type="number" />
        <json:key name="items">
            <json:array>
                <ui:forEach items="var:items">
                    <json:object>
                        <json:key name="id" value="list:id" type="number" />
                        <json:key name="name" value="list:name" />
                    </json:object>
                </ui:forEach>
            </json:array>
        </json:key>
    </json:object>
</json:output>
```

### Consuming JSON Input

```xml
<json:input>
    <web:condition when="edit:save">
        <var:declare name="inputName" value="edit:name" />
        <!-- Process JSON body fields -->
    </web:condition>
</json:input>
```

## Error Handling

```xml
<error:boundary name="dataLoad" logException="true">
    <!-- Code that might fail -->
    <ce:list name="external_data">
        <ui:forEach items="ce:list">
            <web:out text="list:value" />
        </ui:forEach>
    </ce:list>
</error:boundary>

<web:out error:boundaryFailed="dataLoad">
    <bs:alert color="danger">
        <p>Failed to load data. Please try again later.</p>
    </bs:alert>
</web:out>
```

## Reusable Controls (Template Declare)

Define reusable components within a view:

```xml
<!-- Define a reusable alert component -->
<template:declare identifier="statusBadge">
    <template:attribute name="status" type="string" />

    <web:switch when="template:status">
        <web:case is="active">
            <span class="badge badge-success">Active</span>
        </web:case>
        <web:case is="pending">
            <span class="badge badge-warning">Pending</span>
        </web:case>
        <web:case>
            <span class="badge badge-secondary"><web:out text="template:status" /></span>
        </web:case>
    </web:switch>
</template:declare>

<!-- Use it throughout the page -->
<template:statusBadge status="active" />
<template:statusBadge status="pending" />
<template:statusBadge status="list:status" />
```

## PHP Class Registration

Register and use additional PHP classes on-demand:

```xml
<!-- Register a class with a prefix -->
<php:using prefix="myHelper" class="php.libs.MyHelper" />

<!-- Now use it as tags -->
<myHelper:doSomething param="value" />

<!-- Or with lazy loading (loaded only when first used) -->
<php:lazy myHelper="php.libs.MyHelper" />
```
