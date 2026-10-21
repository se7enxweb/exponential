---
title: Glossary for software engineers
audience: software-engineer, developer
category: software-engineering
order: 990
summary: The Exponential terms a software engineer meets, from audit trail and baseline to zygote, each defined in one or two sentences with the page that explains it, and mapped to the nearest term of other frameworks where one exists.
keywords: glossary, terms, definitions, siteaccess, module, view, datatype, node, extension, eZINI, persistent object, Velocity, zygote, warm-up, cache block, baseline, manifest, CLA, software engineer
type: help
---
# Glossary for software engineers

Terms used on the software engineering pages, with the nearest term from other frameworks where it helps. The
extension developers' glossary has more API terms ([Glossary for extension developers](../extension-development/part-8-reference/glossary-for-extension-developers.md)).

| Term | Meaning | Elsewhere often called | Page |
|---|---|---|---|
| Audit trail | hash-chained, signed log of security-relevant events | audit log | [Observability](observability-logs-debug-and-audit.md) |
| Baseline (PHPStan) | `phpstan-baseline.neon`, the findings tolerated because they predate the check | baseline | [Static analysis and code style](static-analysis-and-code-style.md) |
| Cache block | a cached section of a template, `{cache-block}` | fragment cache | [The template engine and the design chain](template-engine-and-design-chain.md) |
| Cluster file handler | `eZClusterFileHandler`; every cache and storage file goes through it | storage abstraction | [Scaling and clustering](scaling-and-clustering.md) |
| Content class | a type of content with typed attributes, defined at run time | content type, schema | [The content model for engineers](content-model-for-engineers.md) |
| Content job | a large content operation run in the background in batches | background job | [Console, cronjobs and background work](console-cron-and-background-work.md) |
| Content view cache | the cached result of a content view | page or view cache | [Caching layers for engineers](caching-layers-for-engineers.md) |
| Cronjob part | a named group of scripts run by `runcronjobs.php` | scheduled task | [Console, cronjobs and background work](console-cron-and-background-work.md) |
| Datatype | storage, validation, editing and indexing of one kind of attribute | field type | [The content model for engineers](content-model-for-engineers.md) |
| Design chain | the stacked design directories searched for a template | theme inheritance | [The template engine and the design chain](template-engine-and-design-chain.md) |
| Engine | the server `exp:velocity` drives: `qbix`, `frankenphp` or `php` | runtime | [Application servers](application-servers.md) |
| Extension | a directory in `extension/` that adds settings, modules, designs and classes | plugin, bundle, module | [Kernel, lib and extensions: the layering](kernel-lib-and-extensions.md) |
| Fetch function | `fetch( 'module', 'function', hash( ... ) )`, the data access of templates | view helper, query | [The template engine and the design chain](template-engine-and-design-chain.md) |
| File manifest | `share/filelist.md5`, the checksum of every shipped file | checksum list | [CI gates](ci-gates.md) |
| INI cascade | the merged settings: defaults, extensions, siteaccess, override | configuration layering | [Siteaccesses and configuration](siteaccesses-and-configuration.md) |
| Kernel | `kernel/` and `lib/`, the core of Exponential | framework core | [Architecture overview](architecture-overview.md) |
| Limitation | a restriction on a policy: class, section, subtree, language | scope, condition | [Modules, views and access control](modules-views-and-access-control.md) |
| Module | a URL namespace with views and policy functions | controller group | [Modules, views and access control](modules-views-and-access-control.md) |
| Node | a location of a content object in the tree | location, page | [The content model for engineers](content-model-for-engineers.md) |
| Override (template) | a rule in `override.ini` that picks another template when conditions match | template override | [The template engine and the design chain](template-engine-and-design-chain.md) |
| Pagelayout | the outer template a view result is placed into | layout | [The request lifecycle](request-lifecycle.md) |
| Persistent object | an `eZPersistentObject` subclass mapped to one table | active record, entity | [Persistence and the database layer](persistence-and-the-database-layer.md) |
| Policy | the right to use one module function, with limitations | permission | [Modules, views and access control](modules-views-and-access-control.md) |
| Request rules | rules that allow, deny, redirect, rewrite or log after URL translation | middleware, firewall rules | [HTTP security](http-security-csp-and-proxies.md) |
| Response cache | Velocity's in-server cache of public pages | reverse proxy cache | [Caching layers for engineers](caching-layers-for-engineers.md) |
| Role | a set of policies assigned to users or groups | role | [Modules, views and access control](modules-views-and-access-control.md) |
| Siteaccess | a named configuration chosen per request: a site, the administration, a language | tenant, front, environment | [Siteaccesses and configuration](siteaccesses-and-configuration.md) |
| Upgrade note | a page in `doc/eng-us/bc/` for a change that needs action | migration guide | [Changelogs and upgrade notes](changelogs-and-bc-notes.md) |
| URL alias | a readable address translated to a module address | route, slug | [The request lifecycle](request-lifecycle.md) |
| Velocity | Exponential's application server with persistent workers | application server | [Velocity persistent workers](velocity-persistent-workers.md) |
| View | one page or endpoint of a module | action, controller method | [Modules, views and access control](modules-views-and-access-control.md) |
| Warm-up | a script run once in Velocity's parent before forking workers | preload | [Velocity persistent workers](velocity-persistent-workers.md) |
| Workflow event | a step run before or after an operation, through a trigger | hook, listener | [Events, extension points and plugins](events-extension-points-and-plugins.md) |
| Zygote | a clean process from which Velocity forks later workers | prefork template | [Velocity persistent workers](velocity-persistent-workers.md) |

## Related pages

- [Software engineer FAQ](software-engineer-faq.md)
- [Glossary for hosts](../hosting/glossary-for-hosts.md)
- [Software engineers: start here](software-engineers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
