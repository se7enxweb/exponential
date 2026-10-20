---
title: Glossary for extension developers
audience: developer, designer, app-creator, user
category: extension-development
order: 380
summary: The words this book uses, in one alphabetical list - from access word, append file and attribute to view mode, view parameter and workflow - each with a short definition and the chapter that explains it.
keywords: glossary, terms, definitions, extension, siteaccess, design, override, datatype, node, object, class, module, view, operator, fetch, workflow, trigger, packer, CSP, token
type: help
---
# Glossary for extension developers

Part 8, reference 34, the last page of the book. The terms of Exponential development, as this book uses them.

| Term | Meaning | See |
|---|---|---|
| access word | the answer of `hasAccessTo()`: `yes`, `no` or `limited` | [eZUser and access checks](ezuser-and-access.md) |
| admin4 | the current administration design, with shared components and design tokens | [admin4 components and tokens](admin4-components-and-tokens.md) |
| append file | an extension's or siteaccess's `<name>.ini.append.php` that adds to a kernel INI file | [INI files an extension author meets](ini-files-for-extension-authors.md) |
| attribute | one field of a content object, of a datatype defined by its class attribute | [eZContentObject](ezcontentobject.md) |
| autoload array | the generated map from class names to files (`var/autoload/`, `autoload/`) | [Autoloads and class naming](../part-3-extension-developer/autoloads-and-class-naming.md) |
| cache block | a part of a template whose output is cached (`cache-block`) | [Cache tags, cache blocks and clearing caches](cache-tags-and-clearing.md) |
| class (content class) | the type of a content object: its identifier and attributes | [eZContentClass and its attributes](ezcontentclass.md) |
| cluster file handler | the layer through which files under `var/` are read and written, so several servers share them | [eZFile, eZDir and cluster file handlers](ezfile-ezdir-and-cluster-files.md) |
| Content Security Policy (CSP) | a header that tells the browser which scripts and styles it may run | [CSP: moving inline scripts into files](../part-4-upgrading/csp-moving-inline-scripts-out.md) |
| context menu | the right-click menu of the content tree, extended through `admininterface.ini` | [Context menus and admin integration](../part-4-upgrading/context-menus-and-admin-integration.md) |
| cronjob part | a group of cronjob scripts run together | [Cronjob parts](../part-3-extension-developer/cronjob-parts.md) |
| datatype | the code behind a kind of attribute: editing, storage, display | [The eZDataType API](ezdatatype-api.md) |
| design | a named set of templates and files, searched in the design chain | [Themes are extensions](../part-6-theme-developers/themes-are-extensions.md) |
| design chain | the ordered list of designs (and their extension folders) a siteaccess searches | [The design chain](../part-2-builder/the-design-chain.md) |
| design extension | an extension that contributes design folders (`DesignExtensions[]`) | [The anatomy of a design extension](../part-6-theme-developers/design-extension-anatomy.md) |
| design token | a named value (`--a4-ink`) used instead of a fixed color or size | [Dark mode and design tokens in themes](../part-6-theme-developers/dark-mode-and-tokens-in-themes.md) |
| expui (Exponential UI) | the administration's JavaScript library, `Exp` | [The expui JavaScript API](expui-javascript-api.md) |
| extension | a folder under `extension/` that adds to or changes the kernel without editing it | [What an extension is](../part-1-from-user-to-developer/what-an-extension-is.md) |
| extension point | a registered way for an extension to plug in | [Extension points at a glance](extension-points-at-a-glance.md) |
| fetch function | a read operation of a module, called from templates with `fetch()` | [Built-in fetch functions by module](fetch-functions-reference.md) |
| fork | a copy of a repository that you maintain | [When to fork, and when not to](../part-5-extend-fork-contribute/when-to-fork.md) |
| form token | the per-session secret every POST of a signed-in user must carry | [ezformtoken and CSRF protection](ezformtoken-and-csrf.md) |
| handler | a class named by a setting that provides a service (mail transport, image handler) | [Handlers named by settings](../part-3-extension-developer/handlers-named-by-settings.md) |
| kernel | the core of Exponential: `kernel/`, `lib/`, `settings/`, the shipped designs | |
| kernel override | an extension's replacement of a kernel class: the last resort | [Kernel overrides: the last resort](../part-3-extension-developer/kernel-overrides-the-last-resort.md) |
| module, view | a group of pages under one URL prefix, and one page of it | [eZModule and module results](ezmodule.md) |
| node | a location of a content object in the content tree | [eZContentObjectTreeNode](ezcontentobjecttreenode.md) |
| object (content object) | one piece of content, with versions and translations | [eZContentObject](ezcontentobject.md) |
| override (template override) | a rule that picks another template for certain content | [Template overrides](../part-2-builder/template-overrides-basics.md) |
| packer | ezjscore's bundling of stylesheets and scripts | [Theme assets, the packer and CSP](../part-6-theme-developers/theme-assets-the-packer-and-csp.md) |
| package (`.ezpkg`) | an archive of classes, content or an extension for the package system | [Packages (.ezpkg) and the package creator](../part-7-releasing/packages-ezpkg.md) |
| pagelayout | the template that frames every page | [View results, the path and the pagelayout](../part-3-extension-developer/view-results-and-the-pagelayout.md) |
| persistent object | a class mapped to a table row (`eZPersistentObject`) | [eZPersistentObject](ezpersistentobject.md) |
| policy, role, limitation | a permission (module and function), a set of them given to users, and its restriction | [Policy functions and access](../part-3-extension-developer/policy-functions-and-access.md) |
| RAD tools | the wizards of **Setup > RAD** that write extension code | [The RAD tools tour](../part-3-extension-developer/tutorials/the-rad-tools-tour.md) |
| remote ID | a stable, installation-independent ID of an object or node | [eZContentObject](ezcontentobject.md) |
| server function | a PHP method callable through `/ezjscore/call/` | [ezjscore server functions reference](ezjscore-server-functions-reference.md) |
| siteaccess | one entry point of an installation, with its own settings and designs | [Siteaccess settings](../part-2-builder/siteaccess-settings.md) |
| tag (Git) | a name for one commit, used as a release version; permanent once pushed | [Tags and releases are permanent](../part-7-releasing/tags-are-permanent.md) |
| template operator, template function | `{$x\|operator}` and `{function}...{/function}` | [Built-in template operators](template-operators-reference.md) |
| trigger | the link between an operation (publish) and a workflow | [Triggers and workflows for developers](../part-3-extension-developer/triggers-and-workflows-for-developers.md) |
| Velocity | the application server of Exponential, required by `composer.json` since 6.0.15 | |
| view mode | how content is shown: full, line, embed and your own | [View modes for builders](../part-2-builder/view-modes-for-builders.md) |
| view parameter | a `(name)/value` pair in a URL | [View parameters](../part-3-extension-developer/view-parameters.md) |
| workflow, workflow event | a list of steps run by a trigger, and one step | [The eZWorkflowType API](ezworkflowtype-api.md) |

**Demo mode** - `EXP_DEMO_MODE=1` in the environment: administrator passwords cannot be changed or reset (the `expDemoMode` API, the store guard in `eZUser::store()`), and the installers accept the published demo password. [Demo mode for developers](demo-mode-for-developers.md).

## Related pages

- Previous: [expservices: remote services for extensions](expservices.md)
- Next: [Book index: every topic from A to Z](../index-of-topics.md)
- [Extension development: the book](../README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
