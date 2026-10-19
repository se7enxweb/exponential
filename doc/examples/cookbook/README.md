---
title: Cookbook
audience: developer, admin
category: examples
order: 1
summary: Worked examples for the common tasks of building on Exponential, from an extension skeleton to a custom datatype, a cronjob and a Velocity deploy, each checked against the code of this release.
keywords: cookbook, examples, how to, recipe, extension, content class, template override, template operator, datatype, module, view, workflow event, cronjob, REST, JSON, ezjscore, siteaccess, multisite, translations, caching, Velocity, deploy
type: example
---
# Cookbook

Each page of the cookbook builds one thing from start to finish: the files you write, the settings that switch them
on, the command that checks them and what goes wrong most often. The pages build on each other through one example
extension, **expexample**, so the code of one page is reused by the next. Read
[An extension skeleton](extension-skeleton.md) first if you have never made an extension.

Every example was written from the code of this release and checked where that is cheap: PHP files with `php -l`,
templates with `bin/php/eztemplatecheck.php`, XML files with `xmllint`. Each page says what was checked and lists the
source files it was derived from. Nothing here was run against a production database; try every example on a copy of
your site first.

## The recipes

| Task | Page | What it touches |
|---|---|---|
| Start an extension of your own | [An extension skeleton](extension-skeleton.md) | `extension/<name>/`, `ezinfo.php`, `extension.xml`, `composer.json`, `ActiveExtensions[]`, the autoload array |
| Create a content class | [Create a content class](content-class.md) | Setup > Classes, or `eZContentClass` and `eZContentClassAttribute` in a script |
| Change how one page or class looks | [A template override](template-override.md) | `override.ini.append.php`, `design/<design>/override/templates/`, the override cache |
| Add a template operator | [A custom template operator](custom-template-operator.md) | `autoloads/eztemplateautoload.php`, `[TemplateSettings] ExtensionAutoloadPath[]` |
| Add a page with its own address and policy | [A custom module and view](custom-module-view.md) | `modules/<module>/module.php`, `module.ini [ModuleSettings]`, `$FunctionList`, Roles and policies |
| Store a new kind of field | [A custom datatype](custom-datatype.md) | `datatypes/<type>/<type>type.php`, `content.ini [DataTypeSettings]`, the datatype templates |
| Run code when content is published | [A workflow event](workflow-event.md) | `eventtypes/event/<type>/`, `workflow.ini [EventSettings]`, Setup > Workflows and Triggers |
| Run code on a schedule | [A cronjob](cronjob.md) | `cronjobs/`, `cronjob.ini [CronjobSettings]` and `[CronjobPart-<part>]`, the crontab |
| Read content as JSON from a script or the browser | [REST and JSON calls](rest-json-fetch.md) | ezjscore server functions, `ezjscore.ini [ezjscServer_<name>]`, the REST API below `/api/` |
| Serve several sites from one installation | [Several sites: siteaccesses](multisite-siteaccess.md) | `site.ini [SiteAccessSettings]`, `settings/siteaccess/<name>/`, `VarDir`, `CacheVarDir` |
| Translate the texts of an extension | [Translations](translations.md) | `translations/<locale>/translation.ts`, `ezpI18n::tr()`, `\|i18n`, `TranslationExtensions[]` |
| Make pages fast and keep them current | [Caching](caching.md) | the view cache, `cache-block`, `ezcache.php`, Velocity's response cache |
| Put a PHP change live | [Deploying with Velocity](velocity-deploy.md) | `exp:velocity deploy` and its steps |

## The example extension at a glance

```text
extension/expexample/
├── ezinfo.php                         what the extension is (Setup > About, Setup > Extensions)
├── extension.xml                      the same, read first; the same version number
├── composer.json                      when it is installed with Composer
├── autoloads/                         template operators (custom-template-operator.md)
├── classes/                           classes found through the autoload array (rest-json-fetch.md)
├── cronjobs/                          cronjob scripts (cronjob.md)
├── datatypes/expcolor/                a datatype (custom-datatype.md)
├── eventtypes/event/explogpublish/    a workflow event (workflow-event.md)
├── modules/expexample/                a module with two views (custom-module-view.md)
├── design/standard/templates/         its templates; design/standard/javascript/ its scripts
├── translations/ger-DE/               its German texts (translations.md)
└── settings/                          *.ini.append.php: what switches each part on
```

The names follow the project's convention for new code: the extension, its module and its classes start with `exp`
(`expexample`, `expExample...`), never with `ez`.

## Reference material

The cookbook shows how; the reference areas list what exists, generated from the code:

- [Template operators](../template-operators/README.md) and [fetch functions](../fetch-functions/README.md)
- [Module views](../module-views/README.md) and [policy functions](../policies/README.md)
- [INI settings](../ini-settings/README.md), [datatypes](../datatypes/README.md),
  [workflow events](../workflow-events/README.md), [cronjob parts](../cronjobs/README.md)
- [Commands](../commands/README.md) and [extension points](../extension-points/README.md)

The coding rules and the pattern catalogue (how code, templates and documentation are written in this project) are
in [the standards](../../standards/README.md); the cookbook follows them and does not repeat them.

## Related pages

- [Examples](../README.md): every example area, including the web server configurations.
- [Extensions: find, install, configure, build and release](../../guides/extensions.md)
- [Templates and design](../../guides/templates-and-design.md)
- [RAD tools](../../features/6.0/rad-tools.md): the wizards that write most of these skeletons for you.
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
