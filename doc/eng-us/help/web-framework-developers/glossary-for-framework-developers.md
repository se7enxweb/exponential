---
title: Glossary for web framework developers
audience: web-framework-developer, developer
category: web-frameworks
order: 530
summary: The Exponential terms a framework developer meets, in alphabetical order, each with a one-line definition, the nearest framework word and the page that explains it - from action and attribute to workflow and wash.
keywords: glossary, terms, definitions, vocabulary, siteaccess, module, view, node, object, class, datatype, policy, limitation, extension, override, pagelayout, fetch, operator, cache-block, web framework developer
type: help
---
# Glossary for web framework developers

This page is for developers who meet an Exponential word and want its meaning and its nearest framework equivalent.
The reverse direction, from framework terms to Exponential, is the [concept dictionary](concept-dictionary.md).

| Term | Meaning | Nearest framework word | Read |
|---|---|---|---|
| Action | a POST button mapped to a name in `module.php` | form submit handler | [Forms](forms-and-csrf-protection.md) |
| Attribute (class) | a field definition of a content class | field, column | [The content model](the-content-model-for-orm-users.md) |
| Attribute (object) | one field's value in one version and language | property value | [The content model](the-content-model-for-orm-users.md) |
| Cache block | `{cache-block}`: a cached template fragment | fragment cache | [Caching](caching-layers.md) |
| Content class | a type of content with attributes | model, entity, content type | [The content model](the-content-model-for-orm-users.md) |
| Content object | one piece of content with its versions and translations | record | [The content model](the-content-model-for-orm-users.md) |
| Cronjob part | a named group of scheduled scripts | scheduled task | [Scheduled tasks](scheduled-tasks-and-cronjobs.md) |
| Datatype | the type of an attribute: storage, editing, validation, display | field type | [The content model](the-content-model-for-orm-users.md#datatypes) |
| Design | a directory of templates and assets in the design chain | theme | [Template resolution](template-resolution-designs-and-overrides.md) |
| `.dba` file | a database-neutral schema or data file | schema dump, migration | [Migrations](migrations-and-dba-schema-files.md) |
| Event (`ezpEvent`) | an in-process notify or filter event | event, signal, hook | [Events and listeners](events-and-listeners.md) |
| Event type (workflow) | a class that does one step of a workflow | listener, process step | [Workflows](workflows-triggers-and-domain-events.md) |
| `EXP_ENV` | the environment name of a machine | `APP_ENV` | [Environments](environments-and-secrets.md) |
| Extension | the unit of code: modules, classes, templates, settings | bundle, package, app | [Packages](packages-extensions-and-composer.md) |
| Fetch function | a data source templates call with `fetch()` | view composer, template tag | [Fetch functions](fetch-functions-and-template-data.md) |
| Function (policy) | a permission a module declares, such as `content/read` | permission, ability | [Authorization](authorization-roles-and-policies.md) |
| Handler | a class named in an INI setting that implements a subsystem | configured service | [Dependency injection](dependency-injection-and-services.md) |
| INI file | a settings file in layers | configuration file | [Configuration](configuration-ini-files.md) |
| Limitation | a restriction of a policy: class, section, subtree, language | voter condition, scope | [Authorization](authorization-roles-and-policies.md) |
| Module | the first part of an address and its directory of views | route group, controller | [Routing](routing-with-modules-and-views.md) |
| Node | one location of an object in the content tree | tree node, page | [The content model](the-content-model-for-orm-users.md) |
| Object state | a status per object usable in policies and rules | state machine state | [Workflows](workflows-triggers-and-domain-events.md) |
| Operation | a kernel action with triggers, such as `content_publish` | domain operation | [Workflows](workflows-triggers-and-domain-events.md) |
| Operator | a template function or filter written in PHP | Twig filter, helper | [The view layer](templates-vs-twig-and-blade.md) |
| Override | a rule in `override.ini` that swaps a template by conditions | template override | [Template resolution](template-resolution-designs-and-overrides.md) |
| Package | a `.ezpkg` with classes, content or an extension | fixture bundle | [Migrations](migrations-and-dba-schema-files.md#seed-data-and-content) |
| Pagelayout | the base layout around every view's output | base layout | [Responses](responses-redirects-and-errors.md#the-pagelayout) |
| Persistent object | an `eZPersistentObject` subclass mapped to a table | active record | [ORM](orm-and-ezpersistentobject.md) |
| Policy | a function granted in a role, possibly limited | permission rule | [Authorization](authorization-roles-and-policies.md) |
| Remote ID | a stable external key of an object or node | UUID, natural key | [The content model](the-content-model-for-orm-users.md) |
| Request rule | a configured rule that allows, denies, redirects or rewrites a request | middleware | [Middleware](middleware-and-request-rules.md) |
| Role | a set of policies assigned to users or groups | role | [Authorization](authorization-roles-and-policies.md) |
| Section | a group of content for permissions and design | area | [The content model](the-content-model-for-orm-users.md#sections-states-and-relations) |
| Siteaccess | a named configuration chosen per request | tenant, site, host config | [Siteaccesses](siteaccesses-hosts-and-tenants.md) |
| Trigger | the link between an operation and a workflow, before or after | event subscription | [Workflows](workflows-triggers-and-domain-events.md) |
| URL alias | the readable address of a node | slug, permalink | [Pretty URLs](url-aliases-and-wildcards.md) |
| URL wildcard | a pattern that redirects or forwards addresses | redirect route | [Pretty URLs](url-aliases-and-wildcards.md#url-wildcards) |
| Velocity | Exponential's persistent-worker web and application server | application server | [Servers and deployment](servers-and-deployment.md) |
| Version | one revision of a content object | revision | [The content model](the-content-model-for-orm-users.md#versions-and-translations) |
| View | the code behind one address of a module | controller action | [Controllers](controllers-as-module-views.md) |
| Wash | the escaping operator, `\|wash` | auto-escape, `e()` | [Validation](validation.md#output-escape-where-you-print) |
| Workflow | a list of event types run on an operation | process, pipeline | [Workflows](workflows-triggers-and-domain-events.md) |

## Related pages

- [Concept dictionary](concept-dictionary.md)
- [Glossary for journalists](../press/glossary-for-journalists.md)
- [PHP developers: start here](../php-developers/php-developers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
