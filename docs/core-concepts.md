# Core Concepts

## Tags and Fulltags

The framework has two types of custom elements:

### Tags (Self-Closing)

Tags produce output without enclosing content. They map to a PHP method that receives attributes as parameters and returns an HTML string.

```xml
<!-- Syntax -->
<prefix:tagName attribute1="value1" attribute2="value2" />

<!-- Examples -->
<web:out text="Hello World" />
<ui:textbox name="username" default="John" />
<web:redirect pageId="5" />
```

### Fulltags (Paired)

Fulltags wrap body content. The body is compiled into a callable and passed to the PHP method as the `$content` parameter.

```xml
<!-- Syntax -->
<prefix:tagName attribute="value">
    ...body content...
</prefix:tagName>

<!-- Examples -->
<ui:forEach items="var:myList">
    <p><web:out text="list:name" /></p>
</ui:forEach>

<web:frame title="Users">
    <p>This content appears inside the frame.</p>
</web:frame>

<edit:form submit="save">
    <ui:textbox name="title" />
</edit:form>
```

The body can contain HTML, other tags, or any mix. The fulltag's PHP method decides when (and how many times) to execute the body.

### Some Tags Support Both Forms

A tag library can define both a `<tag>` and a `<fulltag>` with the same name — the parser picks the right one based on whether you use self-closing or paired syntax.

```xml
<!-- Self-closing: returns inline value -->
<ui:count items="var:myList" />

<!-- Paired: makes count available as property inside body -->
<ui:count items="var:myList">
    <p>Total: <web:out text="ui:count" /></p>
</ui:count>
```

## Properties

Properties are values you can read (and sometimes write) from tag library instances using the `prefix:propertyName` syntax. They are used **inside attribute values**, not as standalone tags.

```xml
<!-- Reading properties in attributes -->
<web:out text="web:currentPage" />
<web:out text="login:loggedUserId" />
<var:declare name="path" value="web:currentPath" />

<!-- Properties from iteration context -->
<ui:forEach items="var:users">
    <web:out text="list:name" />        <!-- current item's "name" field -->
    <web:out text="ui:forEachIndex" />   <!-- current loop index -->
</ui:forEach>
```

Properties are resolved by calling getter methods on the tag library instance. For example, `web:currentPage` calls `Web::getCurrentPage()`.

### The `anyProperty` Pattern

Many tag libraries define an `anyProperty` — a catch-all that resolves **any** property name dynamically:

```xml
<!-- var:greeting resolves via Variable's anyProperty getter -->
<var:declare name="greeting" value="Hello" />
<web:out text="var:greeting" />

<!-- query:id reads the "id" GET parameter -->
<web:out text="query:id" />

<!-- post:username reads the "username" POST field -->
<web:out text="post:username" />

<!-- cookie:theme reads the "theme" cookie -->
<web:out text="cookie:theme" />

<!-- session:cart reads a session variable -->
<web:out text="session:cart" />
```

## Decorators

Decorators are cross-cutting attributes that modify the behavior of **any** tag. They are defined by one tag library but applied to tags from any library.

### Conditional Decorators (`if:`)

The `Condition` library provides decorators for conditional rendering:

```xml
<!-- Only render if value is truthy -->
<web:out text="var:message" if:true="var:showMessage" />

<!-- Only render if string is NOT empty -->
<web:out if:stringEmpty="var:name" if:not="true">
    <p>Name: <web:out text="var:name" /></p>
</web:out>

<!-- Only render if array is NOT empty -->
<web:out if:arrayEmpty="var:items" if:not="true">
    <p>Has items!</p>
</web:out>

<!-- Use evaluated condition result -->
<if:eval name="isAdmin">
    <if:equals value="login:isLogged" is="php:true" />
</if:eval>
<web:out if:passed="isAdmin">
    <p>Admin content here</p>
</web:out>
<web:out if:failed="isAdmin">
    <p>Please log in</p>
</web:out>
```

### Security Decorators (`security:`)

```xml
<!-- Only render if user is in "admins" group -->
<web:out security:requireGroup="admins">
    <p>Admin-only content</p>
</web:out>

<!-- Only render if user has "edit-pages" permission -->
<div security:requirePerm="edit-pages">
    <button>Edit</button>
</div>
```

### Template Body Decorator (`template:`)

```xml
<!-- Provide fulltag body from a template by ID -->
<template:include id="42" template:provideBodyById="15" />
```

Decorators are powerful because they can be attached to **any** tag without modifying the tag's definition.

## Attribute Types and Value Resolution

### Static Values

Plain string values passed directly:

```xml
<web:out text="Hello World" />
<ui:textbox name="username" />
<web:a pageId="5" text="Home" />
```

### Property References

When an attribute value contains a `prefix:property` pattern, it resolves to the property's runtime value:

```xml
<!-- web:currentPage resolves to the current page ID number -->
<web:a pageId="web:currentPage" text="Reload" />

<!-- var:title resolves to the variable's value -->
<web:out text="var:title" />

<!-- login:loggedUserId resolves to the logged-in user's ID -->
<web:out text="login:loggedUserId" />
```

### Special Values

The `PhpRuntime` library provides special constants:

```xml
php:null      <!-- PHP null value -->
php:true      <!-- PHP true value -->
php:false     <!-- PHP false value -->
```

### Prefix-Grouped Attributes (Merge Prefix)

Some attributes accept grouped key-value pairs using a prefix pattern:

```xml
<!-- param-* groups into a parameters array -->
<web:a pageId="5" param-id="42" param-tab="settings" text="Edit" />
<!-- Generates: /page?id=42&tab=settings -->

<!-- filter-* groups into filter conditions -->
<ui:forEach items="var:menuItems" filter-parentId="php:null">
    <!-- Only items where parentId is null -->
</ui:forEach>

<!-- key-* groups into key conditions for entity operations -->
<ce:form name="products" key-id="query:id">
    <!-- Edit product where id matches query string -->
</ce:form>
```

### The `anyAttribute` Pattern

Some tags accept **any** attribute name, passing them all as a key-value array to the PHP method. This is used for HTML pass-through:

```xml
<!-- Extra attributes passed to the rendered HTML element -->
<bs:column default="6" class="my-custom-class" id="sidebar">
    ...
</bs:column>
```

## Variable Scopes

The `Variable` library (`var:`) supports multiple storage scopes:

| Scope | Lifetime | Description |
|-------|----------|-------------|
| `local` | Current template execution | Default scope; cleared when template finishes |
| `request` | Current HTTP request | Available across all templates in the same request |
| `session` | Browser session | Persists across requests (stored in PHP session) |
| `temp` | Temporary | Short-lived storage |
| `cookie` | Browser cookie | Persists in the user's browser |
| `user` | Per-user | Stored per logged-in user |
| `application` | Global | Application-wide storage |

```xml
<!-- Default (request) scope -->
<var:declare name="title" value="My Page" />

<!-- Explicit session scope -->
<var:declare name="lastPage" value="web:currentPath" scope="session" />

<!-- Explicit cookie scope -->
<var:declare name="theme" value="dark" scope="cookie" />
```

## The Edit Model (Form Lifecycle)

Forms in is4wfw use an `EditModel` that goes through distinct phases:

```
registration → load → submit → save → saved → render
```

### Phases

1. **registration**: Form fields are registered (tells the model what fields exist)
2. **load**: Default/existing values are loaded into the model
3. **submit**: Form has been submitted (POST data available)
4. **save**: Business logic to persist data
5. **saved**: Post-save actions (redirect, success message)
6. **render**: The form HTML is rendered

### Usage Pattern

```xml
<edit:form submit="saveUser">
    <!-- Check if form was saved successfully -->
    <web:condition when="edit:saved">
        <admin:redirectAfterSave closePageId="5" />
    </web:condition>

    <!-- Check if form was submitted -->
    <web:condition when="edit:save">
        <!-- Perform save logic here -->
        <ce:save name="users" key-id="query:id" />
    </web:condition>

    <!-- Form fields (rendered in the render phase) -->
    <ui:textbox name="username" />
    <ui:textbox name="email" />
</edit:form>
```

The `edit:` prefix properties (`edit:save`, `edit:saved`, `edit:submit`, `edit:render`) let you check the current phase and execute logic conditionally.

## Auto Registered Tag Library Prefixes

Auto Registered Tag Libraries are defined in `autoregister.xml`:
