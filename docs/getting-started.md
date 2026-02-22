# Getting Started

This guide walks you through building pages with is4wfw, starting from the basics and building up to a complete interactive page.

## Your First View File

Create a file with the `.view.php` extension. Despite the extension, these are **not** standard PHP files — they are processed by the framework's template parser.

```xml
<!-- views/pages/hello.view.php -->
<h1>Hello, World!</h1>
<p>This is a simple page.</p>
```

Plain HTML works as-is. The power comes from mixing in framework tags.

## Outputting Dynamic Values

Use `<web:out>` to output values:

```xml
<!-- Static text -->
<web:out text="Welcome to is4wfw!" />

<!-- Current page title -->
<web:out text="web:currentPageTitle" />

<!-- Current timestamp -->
<web:out text="web:currentTime" />
```

The `text` attribute accepts either static strings or property references (`prefix:propertyName`).

## Working with Variables

Declare variables with `<var:declare>` and read them back with `var:name`:

```xml
<!-- Declare a variable -->
<var:declare name="siteName" value="My Website" />

<!-- Use it in output -->
<h1><web:out text="var:siteName" /></h1>

<!-- Variables can hold property values -->
<var:declare name="currentUser" value="login:loggedUserId" />

<!-- Overwrite a variable -->
<var:declare name="siteName" value="Updated Name" />
```

### Building Strings

Use `<utils:concat>` to combine multiple values:

```xml
<utils:concat output="greeting" separator=" "
    value1="Hello," value2="var:userName" />
<web:out text="utils:greeting" />
<!-- Output: "Hello, John" -->
```

## Conditional Rendering

### Simple Conditions with `web:condition`

```xml
<var:declare name="role" value="admin" />

<web:condition when="var:role" is="admin">
    <p>You are an administrator.</p>
</web:condition>

<!-- Inverted condition (else) -->
<web:condition when="var:role" is="admin" isInverted="true">
    <p>You are a regular user.</p>
</web:condition>
```

### Switch/Case

```xml
<web:switch when="var:color">
    <web:case is="red">
        <p style="color: red;">Red!</p>
    </web:case>
    <web:case is="blue">
        <p style="color: blue;">Blue!</p>
    </web:case>
    <web:case>
        <!-- Default case (no "is" attribute) -->
        <p>Unknown color</p>
    </web:case>
</web:switch>
```

### Advanced Conditions with `if:eval`

For complex boolean logic, use the Condition library:

```xml
<!-- Evaluate a named condition -->
<if:eval name="canEdit">
    <if:and>
        <if:equals value="login:isLogged" is="php:true" />
        <if:equals value="var:role" is="editor" />
    </if:and>
</if:eval>

<!-- Use the result -->
<web:out if:passed="canEdit">
    <button>Edit</button>
</web:out>

<web:out if:failed="canEdit">
    <p>You don't have edit permissions.</p>
</web:out>
```

### Inline Conditional Decorators

Apply conditions directly as attributes on any tag:

```xml
<!-- Show only if variable is truthy -->
<web:out text="var:message" if:true="var:showMessage" />

<!-- Show only if string is NOT empty -->
<web:out if:stringEmpty="var:name" if:not="true">
    <p>Hello, <web:out text="var:name" />!</p>
</web:out>
```

## Links and Navigation

### Page Links

```xml
<!-- Link to a page by ID -->
<web:a pageId="5" text="Home" />

<!-- Link with parameters -->
<web:a pageId="10" param-id="42" param-tab="details" text="View Details" />
<!-- Generates: <a href="/page-url?id=42&tab=details">View Details</a> -->

<!-- Link with CSS class -->
<web:a pageId="5" class="btn btn-primary" text="Go Home" />
```

### Getting URLs Without Links

```xml
<!-- Get URL as a string -->
<var:declare name="homeUrl" value="web:url" />
```

### Reading Query String Parameters

```xml
<!-- Read "id" from ?id=42 -->
<var:declare name="itemId" value="query:id" />

<!-- Use directly in attributes -->
<web:out text="query:name" />
```

## Iterating Over Data

### ForEach Loop

```xml
<ui:forEach items="var:userList">
    <div class="user-card">
        <h3><web:out text="list:name" /></h3>
        <p>Email: <web:out text="list:email" /></p>
        <small>Item #<web:out text="ui:forEachIndex" /></small>
    </div>
</ui:forEach>
```

Inside `<ui:forEach>`, access the current item's fields with `list:fieldName` and the loop index with `ui:forEachIndex`.

### Filtering Items

```xml
<!-- Only iterate items where parentId is null -->
<ui:forEach items="var:menuItems" filter-parentId="php:null">
    <li><web:out text="list:title" /></li>
</ui:forEach>

<!-- Only items where status equals "active" -->
<ui:forEach items="var:products" filter-status="active">
    <li><web:out text="list:name" /></li>
</ui:forEach>
```

### Checking for Empty/Non-Empty Data

```xml
<ui:any items="var:results">
    <p>Found some results:</p>
    <ui:forEach items="var:results">
        <web:out text="list:title" />
    </ui:forEach>
</ui:any>

<ui:empty items="var:results">
    <p>No results found.</p>
</ui:empty>
```

## Data Grids

Display tabular data with `<ui:grid>`:

```xml
<ui:grid items="var:users" class="table">
    <!-- Simple value column -->
    <ui:column header="Name" value="list:name" />
    <ui:column header="Email" value="list:email" />

    <!-- Boolean column -->
    <ui:columnBoolean header="Active" value="list:enabled"
        trueText="Yes" falseText="No" />

    <!-- Date column -->
    <ui:columnDateTime header="Created" value="list:created_at"
        format="d.m.Y H:i" />

    <!-- Custom template column -->
    <ui:columnTemplate header="Actions">
        <web:a pageId="10" param-id="list:id" text="Edit" />
    </ui:columnTemplate>
</ui:grid>
```

## Templates and Layouts

### Including Templates

Include reusable templates by identifier:

```xml
<!-- Include a template defined in the database -->
<template:include id="42" />

<!-- Include by identifier name -->
<template:include identifier="header" />

<!-- Include with parameters -->
<template:include identifier="userCard" param-name="John" param-role="Admin" />
```

### Declaring Inline Templates

Define reusable template fragments in your view:

```xml
<template:declare identifier="badge">
    <template:attribute name="text" type="string" />
    <template:attribute name="color" type="string" default="primary" />
    <span class="badge badge-<web:out text="template:color" />">
        <web:out text="template:text" />
    </span>
</template:declare>

<!-- Use it -->
<template:badge text="New" color="success" />
<template:badge text="Draft" color="warning" />
```

### Layout Composition

Layouts use `<template:content />` as a placeholder for child content:

```xml
<!-- views/layouts/main.view.php -->
<html>
<head>
    <title><web:out text="web:pageTitle" /></title>
    <bs:resources />
</head>
<body>
    <nav><!-- navigation --></nav>
    <main>
        <template:content />
    </main>
    <footer>© 2024</footer>
</body>
</html>
```

Pages rendered inside this layout have their content inserted at `<template:content />`.

## Forms

### Basic Form

```xml
<edit:form submit="saveContact">
    <!-- After successful save, redirect -->
    <web:condition when="edit:saved">
        <web:redirectTo pageId="5" />
    </web:condition>

    <!-- On submit, perform save -->
    <web:condition when="edit:save">
        <email:send to="admin@example.com" subject="New Contact">
            Name: <web:out text="edit:name" />
            Message: <web:out text="edit:message" />
        </email:send>
    </web:condition>

    <!-- Form fields -->
    <div>
        <label>Name</label>
        <ui:textbox name="name" />
    </div>
    <div>
        <label>Message</label>
        <ui:textarea name="message" />
    </div>
    <button type="submit">Send</button>
</edit:form>
```

### Available Input Controls

```xml
<ui:textbox name="title" />
<ui:passwordbox name="password" />
<ui:textarea name="description" />
<ui:checkbox name="agree" />
<ui:filebox name="attachment" />
<ui:rangebox name="rating" />

<!-- Dropdown from database entity -->
<ui:dropdownlist name="categoryId"
    source="categories" display="name" value="id"
    emptyText="-- Select --" />

<!-- Checkbox list -->
<ui:checkboxlist name="tagIds"
    source="tags" display="name" value="id" />
```

## Loading Resources

```xml
<!-- Bootstrap CSS/JS -->
<bs:resources />

<!-- FontAwesome icons -->
<fa5:resources />

<!-- Custom CSS -->
<js:style path="~/css/app.css" />

<!-- Custom JavaScript -->
<js:script placement="tail" path="~/js/app.js" />

<!-- Inline CSS -->
<js:style>
    .my-class { color: red; }
</js:style>

<!-- Inline JavaScript -->
<js:script placement="tail">
    console.log("Page loaded");
</js:script>
```

## Routing

Set up URL-based routing to render different views:

```xml
<router:fromPath>
    <router:directory path="admin">
        <router:file path="dashboard" name="dashboard">
            <!-- Dashboard view content -->
        </router:file>
        <router:file path="users" name="users">
            <!-- Users view content -->
        </router:file>
        <router:file path="*" name="not-found">
            <!-- Fallback/404 -->
        </router:file>
    </router:directory>
</router:fromPath>

<router:render />
```

The router matches the URL path, and `<router:render />` outputs the matched route's content.

## Authentication

### Login Form

```xml
<edit:form submit="login">
    <web:condition when="edit:save">
        <login:login group="web-admins" username="edit:username" password="edit:password">
            <web:redirectTo pageId="1" />
        </login:login>
    </web:condition>

    <ui:textbox name="username" />
    <ui:passwordbox name="password" />
    <button type="submit">Log In</button>
</edit:form>
```

### Checking Login State

```xml
<web:out if:true="login:isLogged">
    <p>Welcome, <login:info field="name" />!</p>
</web:out>

<!-- Redirect if not logged in -->
<login:redirectWhenNotLogged pageId="3" />

<!-- Show content only for specific groups -->
<login:authorized all="admins">
    <p>Admin-only content</p>
</login:authorized>
```

## Working with Custom Entities

Custom entities are database tables managed through the framework:

### Listing Records

```xml
<ce:list name="products">
    <ui:forEach items="ce:list">
        <p><web:out text="list:name" /> - $<web:out text="list:price" /></p>
    </ui:forEach>
</ce:list>
```

### Creating/Editing Records

```xml
<ce:form name="products" key-id="query:id">
    <ui:textbox name="name" />
    <ui:textbox name="price" />
    <button type="submit">Save</button>
</ce:form>
```

### Deleting Records

```xml
<ce:deleter name="products" id="query:id">
    <web:redirectTo pageId="5" />
</ce:deleter>
```

## Localization

```xml
<!-- Set up localization bundle -->
<loc:use name="myapp">
    <!-- Use localized strings -->
    <h1><web:out text="loc:page.title" /></h1>
    <p><web:out text="loc:welcome.message" /></p>
</loc:use>
```

## Complete Example

Here's a complete page that lists users with editing capability:

```xml
<loc:use name="admin">
    <web:frame title="loc:users.title">
        <!-- Handle delete action -->
        <web:condition when="query:delete">
            <ce:deleter name="users" id="query:delete">
                <web:redirectToSelf />
            </ce:deleter>
        </web:condition>

        <!-- List users in a grid -->
        <ce:list name="users">
            <ui:grid items="ce:list" class="table table-striped">
                <ui:column header="Name" value="list:name" />
                <ui:column header="Email" value="list:email" />
                <ui:columnBoolean header="Active" value="list:enabled" />
                <ui:columnTemplate header="Actions">
                    <web:a pageId="web:currentPage" param-editId="list:id" text="Edit" class="btn btn-sm btn-primary" />
                    <web:a pageId="web:currentPage" param-delete="list:id" text="Delete" class="btn btn-sm btn-danger" />
                </ui:columnTemplate>
            </ui:grid>

            <ui:empty items="ce:list">
                <p>No users found.</p>
            </ui:empty>
        </ce:list>

        <!-- Edit form (shown when editId is in query string) -->
        <web:condition when="query:editId">
            <edit:form submit="saveUser">
                <web:condition when="edit:saved">
                    <web:redirectToSelf />
                </web:condition>

                <web:condition when="edit:save">
                    <ce:save name="users" key-id="query:editId" />
                </web:condition>

                <ce:form name="users" key-id="query:editId">
                    <div class="form-group">
                        <label>Name</label>
                        <ui:textbox name="name" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <ui:textbox name="email" class="form-control" />
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </ce:form>
            </edit:form>
        </web:condition>
    </web:frame>
</loc:use>
```

## Next Steps

- **[Common Patterns](common-patterns.md)** — Learn common recipes and best practices
- **[Creating Custom Tag Libraries](custom-taglib.md)** — Extend the framework with your own tags
- **Admin Docs Browser** — Use the built-in docs page in the admin panel to explore all tag attributes and properties interactively
