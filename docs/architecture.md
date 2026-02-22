# Architecture Overview

## How is4wfw Works

is4wfw is a **tag-based web framework**. Instead of writing raw PHP to generate HTML, you write view files (`.view.php`) that contain HTML mixed with custom XML-like tags. A template parser compiles these tags into PHP code at runtime, caching the result for performance.

```
View File (.view.php)          autoregister.xml           PHP Classes
┌──────────────────────┐    ┌──────────────────────┐    ┌──────────────────────┐
│ <web:out text="Hi"/> │───>│ web → php.libs.Web   │───>│ Web.class.php        │
│ <ui:form>            │    │ ui  → php.libs.Ui    │    │ Ui.class.php         │
│   <ui:textbox .../>  │    │ var → php.libs.Var.. │    │ Variable.class.php   │
│ </ui:form>           │    └──────────────────────┘    └──────────────────────┘
└──────────────────────┘              │                            │
           │                         │                            │
           ▼                         ▼                            ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│                        TemplateParser                                        │
│  1. Reads view content                                                       │
│  2. Matches tags by prefix (regex)                                           │
│  3. Resolves prefix → PHP class via registrations                            │
│  4. Generates PHP code calling class methods                                 │
│  5. Compiles into ParsedTemplate (cached)                                    │
│  6. Executes → HTML output                                                   │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Directory Structure

```
src/app/
├── admin/                          # Admin panel application
│   └── views/                      # Admin view files
│       ├── layouts/                # Layout templates (base, main)
│       ├── controls/               # Reusable UI components
│       └── pages/                  # Individual admin pages
├── scripts/
│   └── php/
│       ├── libs/                   # Tag libraries (core of the framework)
│       │   ├── Web.class.php       # PHP class implementing tag methods
│       │   ├── Web.xml             # XML definition of tags, attributes, properties
│       │   ├── Ui.class.php
│       │   ├── Ui.xml
│       │   ├── BaseTagLib.class.php # Base class for all tag libraries
│       │   └── ...                 # ~50 tag library pairs
│       ├── classes/                # Framework internals
│       │   ├── TemplateParser.class.php
│       │   ├── ParsedTemplate.class.php
│       │   ├── ExtensionParser.class.php
│       │   └── dataaccess/         # Database access layer
│       └── autoregister.xml        # Default prefix → class mapping
├── data/                           # Application data files
└── index.php                       # Application entry point
```

## The Tag Library System

Every tag library consists of two files:

### 1. XML Definition File (e.g., `Ui.xml`)

Defines what tags are available, their attributes, and properties:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<taglib xmlns="http://schemas.neptuo.com/xsd/is4wfw/taglib.xsd">
    <comment>UI component library</comment>

    <!-- Self-closing tag: <ui:textbox name="username" /> -->
    <tag>
        <name>textbox</name>
        <function>textbox</function>          <!-- maps to PHP method -->
        <comment>Renders a text input</comment>
        <attribute>
            <name>name</name>
            <type>string</type>
            <required />
            <comment>Field name</comment>
        </attribute>
        <attribute>
            <name>default</name>
            <type>string</type>
            <comment>Default value</comment>
        </attribute>
    </tag>

    <!-- Paired tag: <ui:forEach items="...">...</ui:forEach> -->
    <fulltag>
        <name>forEach</name>
        <function>forEach</function>
        <attribute>
            <name>items</name>
            <type>string</type>
            <required />
        </attribute>
    </fulltag>

    <!-- Property: accessed as ui:count in attributes -->
    <property>
        <name>count</name>
        <get>getCount</get>
        <comment>Item count inside ui:count tag</comment>
    </property>
</taglib>
```

### 2. PHP Class File (e.g., `Ui.class.php`)

Implements the tag methods. Each tag's `<function>` maps to a method:

```php
class Ui extends BaseTagLib {

    // Called for <ui:textbox name="username" default="John" />
    public function textbox($name, $default = '') {
        $value = $this->getEditModel()->getValue($name, $default);
        return '<input type="text" name="' . $name . '" value="' . $value . '" />';
    }

    // Called for <ui:forEach items="...">body</ui:forEach>
    // $content is a callable (the compiled body template)
    public function forEach($items, $content) {
        $result = '';
        foreach ($items as $item) {
            $this->pushListModel($item);
            $result .= $content();
            $this->popListModel();
        }
        return $result;
    }
}
```

**Key rules:**
- **Tags** (self-closing) receive attributes as method parameters and return HTML strings
- **Fulltags** (paired) also receive a `$content` parameter — a callable representing the compiled body
- **Properties** are accessed via getter/setter methods

## The Autoregister System

The file `src/app/scripts/php/autoregister.xml` maps tag prefixes to PHP classes:

```xml
<autoregister>
    <reg prefix="web" class="php.libs.Web" />
    <reg prefix="ui" class="php.libs.Ui" />
    <reg prefix="var" class="php.libs.Variable" />
    <reg prefix="if" class="php.libs.Condition" />
    <reg prefix="template" class="php.libs.Template" />
    <reg prefix="edit" class="php.libs.Editor" />
    <reg prefix="login" class="php.libs.Login" />
    <!-- ... 50+ registrations -->
</autoregister>
```

When the parser encounters `<web:out text="Hello" />`, it:
1. Extracts the prefix `web`
2. Looks up `web` → `php.libs.Web` in registrations
3. Calls `Web::out("Hello")`  (the `getProperty` method mapped by `Web.xml`)

**Lazy loading**: Classes are instantiated on-demand. The first time a prefix is used, `PhpRuntime` loads the class file, reads the XML definition, and creates a global instance (e.g., `$webObject`, `$uiObject`).

Constructor parameters can be passed in the registration:

```xml
<reg prefix="mylib" class="php.libs.MyLib" param-connectionString="..." />
```

## BaseTagLib — The Shared Foundation

All tag library classes extend `BaseTagLib`, which provides:

| Helper | Returns | Purpose |
|--------|---------|---------|
| `$this->db()` | Database | Execute SQL queries |
| `$this->dataAccess()` | DataAccess | Lower-level database access |
| `$this->sql()` | SqlBuilder | Fluent SQL query builder |
| `$this->web()` | Web | Access page properties, URLs |
| `$this->login()` | Login | Current user info, auth state |
| `$this->system()` | System | System properties |
| `$this->php()` | PhpRuntime | Tag registration management |
| `$this->ui()` | Ui | UI helpers |
| `$this->js()` | Js | JavaScript/CSS resources |
| `$this->request()` | array | Request storage |
| `$this->session()` | array | Session storage |
| `$this->query()` | array | Query string storage |
| `$this->rb($key)` | string | Localization resource bundle |
| `$this->getEditModel()` | EditModel | Current form edit model |
| `$this->getFrame()` | string | Render admin frame/panel |
| `$this->redirectToUrl()` | void | HTTP redirect |
| `$this->redirectToSelf()` | void | Redirect to current page |
| `$this->view($name, $data)` | string | Render extension view |
| `$this->partialView($path)` | string | Render partial view template |
| `$this->dao($name)` | AbstractDao | Get data access object |
| `$this->select()` | Select | Create SELECT query builder |

## Template Parsing Pipeline

1. **Input**: View file content (HTML + custom tags)
2. **Registration lookup**: Parser gets current prefix→class registrations from `PhpRuntime`
3. **Regex matching**: Tags are matched by patterns for self-closing and paired tags
4. **Attribute resolution**: Static values stay as-is; references like `web:currentPage` resolve to property getters
5. **Code generation**: PHP class is generated extending `ParsedTemplate`
6. **Caching**: Compiled template is cached (keyed by content hash)
7. **Execution**: The compiled template's callable is invoked, producing HTML

Templates are cached based on content hash, so they're only recompiled when the source changes.

## View Files

View files use the `.view.php` extension and live in `views/` directories. They are **not** standard PHP files — they are processed by the template parser. Standard PHP code is not executed directly; instead, you use the framework's tag system.

```xml
<!-- views/pages/mypage.view.php -->
<web:frame title="My Page">
    <var:declare name="items" value="ce:list" />
    <ui:forEach items="var:items">
        <p><web:out text="list:name" /></p>
    </ui:forEach>
</web:frame>
```

### View Organization

- **Layouts** (`views/layouts/`): Master page templates with `<template:content />` placeholders
- **Pages** (`views/pages/`): Individual page views
- **Controls** (`views/controls/`): Reusable component fragments
