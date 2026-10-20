---
title: "Part 2: The builder: settings, templates and overrides"
audience: developer, designer, app-creator
category: extension-development
order: 20
summary: The second part of the book. Change how Exponential behaves and looks without PHP - settings and their scopes, templates, the design chain, overrides, view modes, fetch, classes, roles, workflows and cronjobs.
keywords: book, part 2, builder, site builder, settings, ini, templates, overrides, design, view modes, fetch, content classes, roles, workflows, cronjobs
type: help
---
# Part 2: The builder: settings, templates and overrides

This is the second part of [Extension development: the book](../README.md). A **builder** changes how a site behaves
and looks without writing PHP. Most real projects are built almost entirely with the tools of this part, and every
extension developer and theme developer uses them daily, so this part is worth reading even if you came for PHP.

You will work in an extension of your own from the first chapter on: a **settings extension** for your site's
configuration and a **design extension** for its templates. That keeps every change in one folder under version
control, instead of scattered across `settings/override/` and the kernel's directories.

## Chapters

### Settings

| Chapter | What you learn |
|---|---|
| [How INI settings work](how-ini-settings-work.md) | Files, blocks, variables, arrays, hashes, comments, the INI cache |
| [Settings scopes and the order they win in](settings-scopes-and-their-order.md) | Default, extension, siteaccess, override, and the per-site directories inside extensions |
| [Siteaccess settings](siteaccess-settings.md) | What a siteaccess is, how a request picks one, what goes in its settings |
| [Settings that live in an extension](extension-settings-for-builders.md) | Moving a site's configuration into an extension |
| [Changing settings with exp:ini](the-exp-ini-command.md) | Every verb of the settings command, with dry runs and backups |
| [Settings in the administration](settings-in-the-admin.md) | Setup > Ini settings: the override chain, comparison, safe saving |
| [.ini.append.php files and keeping settings safe](append-php-files-and-safety.md) | Why the PHP wrapper, secrets, permissions, what never to commit |

### Templates

| Chapter | What you learn |
|---|---|
| [Templates for builders](templates-for-builders.md) | The template language: variables, conditions, loops, includes |
| [The design chain: where a template is found](the-design-chain.md) | Site design, additional designs, design extensions, standard |
| [Template overrides: the basics](template-overrides-basics.md) | Replacing one template for some content only |
| [Override conditions](override-conditions.md) | Every match key: class, node, parent, section, state, view mode, depth and more |
| [View modes for builders](view-modes-for-builders.md) | full, line, embed and your own |
| [Fetching content in templates](fetch-for-builders.md) | `fetch()`: lists, trees, counts, filters, sorting |
| [The template operators builders use most](template-operators-builders-use-most.md) | A working set of operators with examples |
| [Debugging templates](debugging-templates.md) | Finding why a template or an override does not apply |
| [Caching and templates](caching-and-templates.md) | The view cache, `cache-block`, and when to clear what |

### The rest of the builder's toolbox

| Chapter | What you learn |
|---|---|
| [Content classes for builders](content-classes-for-builders.md) | Designing the content model |
| [Sections, roles and access for builders](sections-roles-and-access-for-builders.md) | Who may see and do what |
| [Workflows for builders](workflows-for-builders.md) | Approval and other events on publish |
| [Cronjobs for builders](cronjobs-for-builders.md) | What runs on a schedule, and how to set it up |
| [Building a site without writing code](building-a-site-without-code.md) | A complete small site from settings, classes and templates |
| [When settings are not enough](when-settings-are-not-enough.md) | Deciding when to write PHP, and which extension point to use |

## Before you start

You need a development installation ([Setting up a development installation](../part-1-from-user-to-developer/setting-up-a-development-installation.md))
with debug output on, and you should have done [Your first hour as a developer](../part-1-from-user-to-developer/your-first-hour-as-a-developer.md).

## Related pages

- Previous: [Asking the community for help](../part-1-from-user-to-developer/asking-the-community.md)
- Next: [How INI settings work](how-ini-settings-work.md)
- [Extension development: the book](../README.md)
- [Templates and design](../../../guides/templates-and-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
