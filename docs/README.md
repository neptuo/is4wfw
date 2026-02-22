# is4wfw Documentation

**is4wfw** (Information System for Web FrameWork) is a PHP-based web application framework and content management system. It uses a declarative, XML-like tag system to build web pages — mixing HTML with custom tags that map to PHP class methods.

## Documentation

| Document | Description |
|----------|-------------|
| [Architecture Overview](architecture.md) | How the framework works: tag libraries, template parser, autoregister |
| [Core Concepts](core-concepts.md) | Tags, fulltags, properties, decorators, scopes, and the edit model |
| [Getting Started](getting-started.md) | Build your first page: step-by-step tutorial |
| [Common Patterns](common-patterns.md) | Recipes for layouts, CRUD, forms, routing, permissions, and more |
| [Creating Custom Tag Libraries](custom-taglib.md) | Extend the framework with your own tags |

## Built-in Tag Reference

The framework includes a built-in interactive documentation browser in the admin panel (powered by the `Hint` tag library). Navigate to the **Docs** page in the admin UI to browse all available tags, their attributes, and properties for every registered tag library.

## Quick Example

A view file (`.view.php`) combines HTML with framework tags:

```xml
<web:frame title="Welcome">
    <var:declare name="greeting" value="Hello, World!" />
    <p><web:out text="var:greeting" /></p>

    <if:eval name="isLoggedIn">
        <if:equals value="login:isLogged" is="php:true" />
    </if:eval>
    <web:out if:passed="isLoggedIn">
        <p>Welcome back, <login:info field="name" />!</p>
    </web:out>
</web:frame>
```

Tags use namespace prefixes (like `web:`, `var:`, `if:`) that map to PHP classes via `autoregister.xml`. The template parser compiles these into executable PHP code.
