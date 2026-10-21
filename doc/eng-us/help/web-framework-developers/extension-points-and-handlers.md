---
title: "Extension points: handlers, INI registration and overrides"
audience: web-framework-developer, developer
category: web-frameworks
order: 200
summary: Where framework developers register services, tags, listeners and plugins, Exponential registers extension points in INI settings, directories and designs - the nine registration mechanisms, a map of the most used extension points to their framework counterparts, and Setup > RAD, which lists and scaffolds them.
keywords: extension points, plugins, hooks, handler, registration, INI, RAD, setup/rad, datatype, template operator, fetch function, workflow event, REST provider, login handler, session handler, override, bundle, service provider, web framework developer
type: help
---
# Extension points: handlers, INI registration and overrides

This page is for developers who extend a framework by registering services, tagging them, subscribing listeners or
writing plugins. Exponential is extended in a handful of repeating ways, all listed in one catalog:
`expRADCatalogue::points()` (`kernel/setup/expradcatalog.php`), shown at **Setup > RAD** (`/setup/rad`) with 70
extension points, most of them with a wizard that writes the boilerplate ([Extension points](../../bc/6.0/rad-extension-points.md),
[RAD tools](../../features/6.0/rad-tools.md)).

## Contents

1. [The registration mechanisms](#the-registration-mechanisms)
2. [The extension points you will use first](#the-extension-points-you-will-use-first)
3. [Framework counterparts](#framework-counterparts)
4. [Order of preference](#order-of-preference)

## The registration mechanisms

From [How things are registered](../../bc/6.0/rad-extension-points.md#how-things-are-registered):

| Mechanism | How it works | Nearest framework idea |
|---|---|---|
| directory | a class in a directory the kernel scans, named by a `RepositoryDirectories` or `ExtensionDirectories` setting | auto-discovery by convention |
| handler | a class named by an INI setting, created through `eZExtension::getHandlerClass()` | a configured service implementation |
| autoload | a class reached through the extension autoload map | autowired classes |
| file | a file in a place the kernel looks by name | convention over configuration |
| design | a template in a design, found through the design chain | template path override |
| ini | settings only | configuration |
| registry | entries of a registry that `rad.ini` declares | tagged services |
| filter | a listener of a filter event in `site.ini [Event] Listeners[]` | event subscriber that returns a value |
| override | a kernel class replaced through the override autoload | class replacement, monkey patching |

## The extension points you will use first

| You want | Extension point | Registered by |
|---|---|---|
| New addresses and screens | module and views | `module.ini [ModuleSettings] ExtensionRepositories[]` |
| A permission for them | policy function and limitation | `$FunctionList` in `module.php` |
| Template helpers | template operator | `site.ini [TemplateSettings] ExtensionAutoloadPath[]` |
| Data for templates | fetch function | `function_definition.php` next to `module.php` |
| A new field type | datatype | `content.ini [DataTypeSettings] ExtensionDirectories[]`, `AvailableDataTypes[]` |
| A step in a business process | workflow event type | `workflow.ini [EventSettings]` |
| Code at a kernel event | kernel event listener | `site.ini [Event] Listeners[]` |
| Scheduled work | cronjob part | `cronjob.ini` |
| A JSON endpoint for your pages | ezjscore server function | `ezjscore.ini [ezjscServer_<name>]` |
| REST routes | REST provider | `rest.ini` |
| Another way to sign in | user login handler | `site.ini [UserSettings] LoginHandler[]` |
| Your templates and stylesheets | design extension | `design.ini [ExtensionSettings] DesignExtensions[]` |

Each row is documented with its contract and kernel file in the catalog, and most have a page among the
[application creator guides](../creators/app-creators/app-creators-start-here.md).

## Framework counterparts

| Framework | You would | In Exponential |
|---|---|---|
| Symfony | write a bundle with `services.yaml`, tags and compiler passes | write an extension with `settings/*.ini.append.php` entries |
| Laravel | write a package with a service provider | the same: the extension's settings are its "provider" |
| Django | add an app to `INSTALLED_APPS` | add the extension to `ActiveExtensions[]` |
| Rails | write an engine or a Railtie | an extension |
| Spring | add a starter with auto-configuration | an extension; INI settings take the place of auto-configuration |

## Order of preference

1. **Settings only** where a setting exists.
2. **A registered extension point**: a module, an operator, a handler, a listener.
3. **Re-implementation of a runnable** (`[RunnableSettings] Implementation[]`) for one kernel command, cronjob part or
   view.
4. **A kernel class override**, the last resort: nothing registers it by name, and two extensions replacing the same
   class do not know of each other.

Never edit files of the kernel or of another extension in place.

## Related pages

- [Dependency injection and services](dependency-injection-and-services.md)
- [RAD extension surface](../../bc/6.0/rad-extension-surface.md)
- [Anatomy of an extension](../creators/app-creators/extension-anatomy.md)
- [Extension development: the book](../extension-development/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
