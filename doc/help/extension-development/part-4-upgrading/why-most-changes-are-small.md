---
title: Why most of the changes are small
audience: developer, app-creator
category: extension-development
order: 10
summary: Why an extension written years ago usually needs only small changes for Exponential 6: the extension system, class names and settings stayed, and the changes come from PHP and the browser.
keywords: upgrade, old extension, compatibility, PHP 8, extension system, kept APIs, effort, estimate
type: help
---
# Why most of the changes are small

Part 4, chapter 1. This chapter is for anyone who looks at an old extension and wonders whether it is worth the
effort. It explains what stayed the same, where the changes come from, and how to estimate the work before you
start.

## What stayed the same

Exponential 6 is the continuation of the PHP content management kernel that old extensions were written for. The
parts an extension plugs into were kept on purpose:

| What an extension uses | In Exponential 6 |
|---|---|
| The `extension/<name>/` folder with `settings/`, `design/`, `modules/`, `classes/`, `autoloads/`, `translations/` | the same layout, read the same way ([The extension directory layout](../part-3-extension-developer/extension-directory-layout.md)) |
| Activation in `site.ini` `[ExtensionSettings] ActiveExtensions[]` | the same setting |
| Class names such as `eZContentObject`, `eZINI`, `eZDB`, `eZPersistentObject`, `eZDataType` | the same names; new kernel code uses an `exp` prefix, but the old classes were never renamed |
| `module.php` with `$Module`, `$ViewList`, `$FunctionList` | the same arrays |
| Template operators registered through `eztemplateautoload.php` | the same `$eZTemplateOperatorArray` |
| Datatypes, workflow event types, cronjob parts, fetch functions | the same base classes and settings |
| The template language, its operators and the design chain | the same, with operators added |

So an extension is rarely "incompatible" as a whole. What breaks is a line here and a line there.

## Where the changes come from

Almost every change you will make has one of three causes.

### 1. PHP itself moved on

Old extensions were written for PHP 5. PHP 8 turned many warnings into errors, removed functions, stopped treating a
method named after its class as the constructor, and deprecates a few more things in every minor release. That is
the largest group, and it is covered by the PHP chapters of this part, starting with
[PHP 8 errors that stop old code](php-8-errors-that-stop-old-code.md).

Most of these fixes are a single line. Here is a typical one, from se7enxweb/expsite_api, commit 0f708f4:

```php
// before
public function __construct( eZContentObject $object = null )

// after
public function __construct( ?eZContentObject $object = null )
```

One question mark. PHP 8.4 reports the old form as deprecated on every request that builds the object.

### 2. The browser became stricter

Exponential 6 can send a Content Security Policy. A policy without `'unsafe-inline'` makes the browser refuse inline
`<script>` blocks, `onclick` attributes and `style=""` attributes. An old extension's admin page may look right and
still do nothing when you click. The fix is to move code into files, which is covered by the CSP chapters, starting
with [CSP: moving inline scripts into files](csp-moving-inline-scripts-out.md).

The JavaScript libraries also moved: YUI 2 and YUI 3 were removed from the administration, and jQuery is version 4.
See [From YUI to expui](yui-to-expui.md) and [From old jQuery to jQuery 4](old-jquery-to-jquery-4.md).

### 3. The project's conventions

Exponential 6 has conventions an old extension did not know: a version in `ezinfo.php` and `extension.xml` that
matches the release, a license and a website in both, a `composer.json` that installs it, admin pages styled with the
shared admin4 components instead of private CSS. None of them stops the extension from running; all of them make it
easier to install, to trust and to maintain. See [Modernizing ezinfo.php and extension.xml](modernizing-ezinfo-and-extension-xml.md).

## What the numbers looked like

In October 2026 this project checked every extension it ships against PHP 8.0, the oldest version Exponential 6
supports. Most needed only a version bump and a wider PHP constraint in `composer.json`; a few needed real fixes.
Examples, all on GitHub under se7enxweb:

| Extension | Commit | What it needed |
|---|---|---|
| expchangeclass | 32196f0 | nothing in the code: `composer.json` allows `^8.0`, version bumped in three files |
| expsite_installer | 0c771fe | `ezinfo.php` keys quoted (bare words are an error in PHP 8) |
| expsite_api | 0f708f4 | two constructors with a nullable parameter |
| expquery_translator | 8ddf68d | 35 parameters in 27 files made explicitly nullable |
| sevenx_dse | b02ff09 | `utf8_decode()` replaced by `mb_strlen()` in two lines |
| cjw_tmv | 0bfa27b | `curl_close()` and `imagedestroy()` only on PHP before 8.0 |
| ezfind | 7ff9824 | five classes whose only constructor was a method named after the class |

The last row is the exception that proves the rule: a change of five methods, but without it search results were
empty and the indexing script stopped at once. Small changes are not always unimportant ones.

## Estimating the work before you start

Before you promise anyone a date, spend half an hour on an inventory
([Taking inventory of an old extension](inventory-an-old-extension.md)). These signs mean little work:

- The extension has no PHP of its own (only settings, templates and translations). Templates almost never need
  changes for PHP 8.
- Its PHP files pass `php -l` on PHP 8.0.
- Its admin pages have no `<script>` blocks and no `on...=` attributes.

These signs mean more work:

- It ships its own copy of a JavaScript library (YUI, an old jQuery, an old TinyMCE plugin).
- It changes kernel files or ships replacement kernel classes ("kernel overrides").
- It talks to the database with `mysql_*` functions instead of `eZDB`.
- It uses `create_function()`, `each()`, `ereg()` or `split()`.
- It depends on a service or library that no longer exists.

> **Tip**
> Upgrade in small commits, one kind of change per commit ("constructors", "nullable parameters", "inline scripts").
> If something breaks later, `git log` tells you which kind of change did it, and a reviewer can read each commit in
> a minute. See [Commit messages the project can build a changelog from](../part-5-extend-fork-contribute/commit-messages.md).

## When an upgrade is not worth it

Sometimes the honest answer is no:

- The kernel now does what the extension did. Use the kernel feature and retire the extension.
- The extension depends on an external service that shut down.
- The license does not allow you to change or publish it, and the author cannot be reached.

In those cases, write down what the extension did, find or build a replacement, and say so in your project's notes.
[Evaluating an old extension](../part-1-from-user-to-developer/evaluating-an-old-extension.md) has a longer checklist.

## Related pages

- Previous: [Part 4: Upgrading an old extension to Exponential 6](README.md)
- Next: [The upgrade checklist](the-upgrade-checklist.md)
- [PHP 8 support](../../../bc/6.0/php8.md)
- [Behavior changes for extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
