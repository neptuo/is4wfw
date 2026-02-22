# Creating Custom Tag Libraries

This guide explains how to extend is4wfw with your own tag libraries — adding new tags, properties, and decorators that can be used in view files just like the built-in ones.

## Overview

A tag library consists of three parts:

1. **PHP class file** (`.class.php`) — Implements the tag methods
2. **XML definition file** (`.xml`) — Declares available tags, attributes, and properties
3. **Registration** in `autoregister.xml` — Maps a prefix to your class

## Step-by-Step Example

Let's create a `Greeter` tag library that provides greeting-related tags.

### 1. Create the PHP Class

Create `src/app/scripts/php/libs/Greeter.class.php`:

```php
<?php

require_once("BaseTagLib.class.php");

class Greeter extends BaseTagLib {

    private $lastGreeting = '';

    // Tag: <greet:hello name="John" />
    public function hello($name, $formal = false) {
        if ($formal) {
            $this->lastGreeting = 'Good day, ' . htmlspecialchars($name) . '.';
        } else {
            $this->lastGreeting = 'Hello, ' . htmlspecialchars($name) . '!';
        }
        return $this->lastGreeting;
    }

    // Fulltag: <greet:welcome name="John">...body...</greet:welcome>
    public function welcome($name, $content) {
        $this->lastGreeting = $name;
        return '<div class="welcome-banner">'
            . '<h2>Welcome, ' . htmlspecialchars($name) . '!</h2>'
            . $content()
            . '</div>';
    }

    // Property getter: greet:lastGreeting
    public function getLastGreeting() {
        return $this->lastGreeting;
    }

    // anyProperty getter: greet:anythingElse
    public function getProperty($name) {
        return 'Greeter property: ' . $name;
    }
}

?>
```

**Key rules for the PHP class:**
- Extend `BaseTagLib`
- Include `require_once("BaseTagLib.class.php")` at the top
- Tag methods receive attributes as parameters and **return** HTML strings (don't echo)
- Fulltag methods receive an additional `$content` parameter — a callable that returns the body's HTML
- Call `$content()` to execute the body; call it multiple times for loops, or not at all to suppress it

### 2. Create the XML Definition

Create `src/app/scripts/php/libs/Greeter.xml`:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<taglib xmlns="http://schemas.neptuo.com/xsd/is4wfw/taglib.xsd">
    <comment>A greeting tag library for demonstration purposes.</comment>

    <!-- Self-closing tag -->
    <tag>
        <name>hello</name>
        <function>hello</function>
        <comment>Renders a greeting for the given name.</comment>
        <attribute>
            <name>name</name>
            <type>string</type>
            <required />
            <comment>The person's name to greet.</comment>
        </attribute>
        <attribute>
            <name>formal</name>
            <type>bool</type>
            <default>false</default>
            <comment>Use formal greeting style.</comment>
        </attribute>
    </tag>

    <!-- Paired tag (fulltag) -->
    <fulltag>
        <name>welcome</name>
        <function>welcome</function>
        <comment>Renders a welcome banner with custom body content.</comment>
        <attribute>
            <name>name</name>
            <type>string</type>
            <required />
            <comment>The person's name.</comment>
        </attribute>
    </fulltag>

    <!-- Named property -->
    <property>
        <name>lastGreeting</name>
        <get>getLastGreeting</get>
        <comment>Returns the last greeting that was generated.</comment>
    </property>

    <!-- Catch-all property -->
    <anyProperty>
        <comment>Returns a dynamic greeting property by name.</comment>
    </anyProperty>
</taglib>
```

### 3. Register the Prefix

Add a line to `src/app/scripts/php/autoregister.xml`:

```xml
<reg prefix="greet" class="php.libs.Greeter" />
```

### 4. Use It in Views

```xml
<!-- Self-closing tag -->
<greet:hello name="World" />
<!-- Output: Hello, World! -->

<greet:hello name="Dr. Smith" formal="true" />
<!-- Output: Good day, Dr. Smith. -->

<!-- Fulltag with body -->
<greet:welcome name="John">
    <p>We're glad to have you here.</p>
</greet:welcome>
<!-- Output: <div class="welcome-banner"><h2>Welcome, John!</h2><p>...</p></div> -->

<!-- Reading a property -->
<web:out text="greet:lastGreeting" />
```

## XML Definition Reference

### `<taglib>` Root Element

```xml
<taglib xmlns="http://schemas.neptuo.com/xsd/is4wfw/taglib.xsd">
    <comment>Library description (shown in docs browser)</comment>
    <count>1</count>  <!-- Optional: max instances allowed (omit for unlimited) -->

    <!-- Tags, fulltags, properties, decorators go here -->
</taglib>
```

### `<tag>` — Self-Closing Tags

```xml
<tag>
    <name>tagName</name>              <!-- How it's used: <prefix:tagName /> -->
    <function>methodName</function>    <!-- PHP method to call -->
    <comment>Description</comment>     <!-- Documentation -->
    <lookless />                       <!-- Optional: tag produces no visible output -->
    <obsolete>Use X instead</obsolete> <!-- Optional: mark as deprecated -->

    <attribute>
        <name>attrName</name>
        <type>string</type>            <!-- string, number, bool -->
        <required />                   <!-- Optional: marks as required -->
        <default>value</default>       <!-- Optional: default value -->
        <prefix />                     <!-- Optional: enables name-* grouping -->
        <comment>Description</comment>
    </attribute>

    <!-- Accept any attribute not explicitly defined -->
    <anyAttribute>
        <comment>All extra attributes are passed as an array.</comment>
    </anyAttribute>
</tag>
```

### `<fulltag>` — Paired Tags

Same structure as `<tag>`, but the PHP method receives an extra `$content` parameter (callable):

```xml
<fulltag>
    <name>wrapper</name>
    <function>wrapper</function>
    <attribute>
        <name>class</name>
        <type>string</type>
    </attribute>
</fulltag>
```

```php
// In PHP:
public function wrapper($class, $content) {
    return '<div class="' . $class . '">' . $content() . '</div>';
}
```

### `<property>` — Named Properties

```xml
<property>
    <name>propertyName</name>
    <get>getterMethod</get>         <!-- Method returning the value -->
    <set>setterMethod</set>         <!-- Optional: method accepting a value -->
    <comment>Description</comment>
    <obsolete>Use X instead</obsolete>
</property>
```

### `<anyProperty>` — Catch-All Property

When a property name doesn't match any named `<property>`, the `anyProperty` handler is called:

```xml
<anyProperty>
    <comment>Resolves any property name dynamically.</comment>
</anyProperty>
```

The PHP method signature:
```php
// For get: receives the property name
public function getProperty($name) {
    return $this->data[$name] ?? '';
}

// For set: receives name and value
public function setProperty($name, $value) {
    $this->data[$name] = $value;
}
```

### `<anyTag>` / `<anyFulltag>` — Catch-All Tags

Handle any tag name that doesn't match a defined tag:

```xml
<anyTag>
    <comment>Handles any unmatched tag name.</comment>
</anyTag>
```

### `<decorator>` — Cross-Cutting Attributes

Decorators modify behavior of tags from **other** libraries:

```xml
<decorator>
    <function>requireGroup</function>
    <comment>Only renders the tag if user is in the specified group.</comment>
    <attribute>
        <name>requireGroup</name>
        <type>string</type>
        <required />
    </attribute>
</decorator>
```

```php
// The decorator method wraps the original tag's output
public function requireGroup($requireGroup) {
    // Return true to allow rendering, false to suppress
    return $this->login()->getGroupValue() >= $this->getGroupValue($requireGroup);
}
```

When a decorator attribute (like `security:requireGroup="admins"`) is found on any tag, the decorator method is called. If it returns false/empty, the original tag's output is suppressed.

### `<constructor>` — Constructor Parameters

Define parameters that can be passed when registering the class:

```xml
<constructor>
    <attribute>
        <name>connectionString</name>
        <type>string</type>
        <comment>Database connection string.</comment>
    </attribute>
</constructor>
```

Registration with constructor params:

```xml
<reg prefix="mydb" class="php.libs.MyDatabase" param-connectionString="..." />
```

## Attribute Types

| Type | PHP Type | Description |
|------|----------|-------------|
| `string` | string | Text value (default) |
| `number` | int/float | Numeric value |
| `bool` | bool | `true`/`false` |

### Prefix Attributes

When an attribute has `<prefix />`, it groups related attributes by prefix:

```xml
<attribute>
    <name>param</name>
    <type>string</type>
    <prefix />
</attribute>
```

Usage: `param-id="42" param-name="John"` → PHP receives `['id' => '42', 'name' => 'John']`

### Merge Prefix

When `<prefix default="merge" />` is set, the prefixed attributes are merged into the main parameters:

```xml
<attribute>
    <name>key</name>
    <type>string</type>
    <prefix default="merge" />
</attribute>
```

## Using BaseTagLib Helpers

Your tag library inherits powerful helpers from `BaseTagLib`:

```php
class MyLib extends BaseTagLib {

    public function myTag() {
        // Database access
        $rows = $this->db()->fetchAll('SELECT * FROM my_table');
        $row = $this->db()->fetchSingle('SELECT * FROM my_table WHERE id = 1');

        // Fluent query builder
        $result = $this->sql()
            ->select('*')
            ->from('my_table')
            ->where('status', '=', 'active')
            ->fetchAll();

        // Current user
        $userId = $this->login()->getUserId();
        $isLogged = $this->login()->isLogged();

        // Web properties
        $currentPage = $this->web()->CurrentPage;
        $langName = $this->web()->LanguageName;

        // System properties
        $value = $this->system()->getPropertyValue('my.setting');

        // Localization
        $text = $this->rb('button.save');

        // Redirect
        $this->redirectToUrl('/some-page');
        $this->redirectToSelf();

        // Edit model (form data)
        $model = $this->getEditModel();
        $name = $model->getValue('name');

        // List model (iteration data)
        $listModel = $this->peekListModel();

        // Render a partial view
        $html = $this->partialView('components/widget');

        // Generate a GUID
        $id = $this->newGuid();

        return $html;
    }
}
```

## File Naming Convention

Files must follow strict naming:
- PHP class: `ClassName.class.php`
- XML definition: `ClassName.xml` (same name as the class)
- Both files must be in the same directory (typically `src/app/scripts/php/libs/`)

## Lookless Tags

Mark a tag as `<lookless />` when it performs an action without producing HTML output (e.g., setting a variable, sending an email, redirecting):

```xml
<tag>
    <name>setTheme</name>
    <function>setTheme</function>
    <lookless />
    <attribute>
        <name>name</name>
        <type>string</type>
        <required />
    </attribute>
</tag>
```

## Testing Your Tag Library

1. Register your library in `autoregister.xml`
2. Create a test view file that uses your tags
3. Use the built-in **Docs** page in the admin panel to verify your XML definition is parsed correctly — it will show your tags, attributes, and descriptions
4. Check the page output to verify your tags render correctly
