---
title: "Part 4: Upgrading an old extension to Exponential 6"
audience: developer
category: extension-development
order: 40
summary: Part 4 of the book: bring an old extension up to Exponential 6, from the inventory and PHP 8 fixes to CSP, jQuery 4, admin4 styling, metadata and tests, with real before and after examples.
keywords: book, part 4, upgrade, old extension, PHP 8, deprecations, CSP, inline scripts, jQuery 4, YUI, admin4, ezinfo, extension.xml, composer.json, case study
type: help
---
# Part 4: Upgrading an old extension to Exponential 6

This is the fourth part of [Extension development: the book](../README.md). It is for anyone who has an extension
that was written for an older release and an older PHP: one you found on GitHub, one your team wrote ten years ago,
or one you adopted. Exponential 6 keeps the extension system the old extensions were written for, so most of them
need only small changes. This part shows which ones, in the order you meet them, and every example of a change is a
real commit from an extension this project brought up to date (the repository and the short commit hash are named
next to it, so you can read the whole change on GitHub).

## How this part is organized

| Group | Chapters | What you get |
|---|---|---|
| Getting ready | [Why most of the changes are small](why-most-changes-are-small.md), [The upgrade checklist](the-upgrade-checklist.md), [Taking inventory of an old extension](inventory-an-old-extension.md) | what to expect, the whole job on one page, and how to see what you have |
| PHP | [PHP 8 errors that stop old code](php-8-errors-that-stop-old-code.md), [PHP 8.1 to 8.5 deprecations](php-8-1-to-8-5-deprecations.md), [Removed PHP functions](removed-php-functions.md), [Old-style constructors and static calls](old-constructors-and-static-calls.md), [Nullable parameters](nullable-parameters-and-types.md), [Dynamic properties](dynamic-properties.md), [Database code](database-code-for-php-8.md) | each kind of PHP change, how to find it, how to fix it |
| Metadata | [ezinfo.php and extension.xml](modernizing-ezinfo-and-extension-xml.md), [composer.json](modernizing-composer-json.md) | the files that tell the system and Composer who you are |
| The browser | [Inline scripts](csp-moving-inline-scripts-out.md), [Event handler attributes](csp-event-handlers-to-data-attributes.md), [javascript: links and eval](csp-javascript-links-and-eval.md), [Inline CSS](inline-css-to-stylesheets.md), [YUI to expui](yui-to-expui.md), [jQuery 4](old-jquery-to-jquery-4.md) | Content Security Policy and the JavaScript libraries of Exponential 6 |
| The administration | [admin4 styling](admin4-styling-for-extension-pages.md), [Context menus](context-menus-and-admin-integration.md) | making your pages look and work like the rest of the admin |
| Finishing | [Naming and branding](naming-and-branding-during-an-upgrade.md), [Tests and CI](tests-and-ci-for-an-upgraded-extension.md) | names, credit, and keeping it working |
| Case studies | [expchangeclass](case-study-expchangeclass.md), [the layouts editor on jQuery 4](case-study-the-layouts-editor-on-jquery-4.md), [two-factor authentication](case-study-two-factor-authentication.md), [small fixes across many extensions](case-study-small-fixes-across-many-extensions.md) | whole upgrades told from start to end |

## The short version

If you read only one page of this part, read [The upgrade checklist](the-upgrade-checklist.md). If you want to
start right away:

1. Copy the extension into `extension/` of a development installation (never a live site first).
2. Run `php -l` on every PHP file with the oldest PHP you want to support (7.4 is the oldest Exponential 6 supports, 8.0 with Velocity).
3. Activate it, open its pages with the debug output on, and write down every error, warning and "Deprecated" line
   that names one of its files.
4. Fix those, one kind at a time, with the chapters of this part.
5. Open the browser console on its pages and fix what the Content Security Policy blocks.
6. Give `ezinfo.php`, `extension.xml` and `composer.json` the current version, license and website.
7. Add a test that keeps it that way.

> **Note**
> You may only upgrade, publish and share an extension the way its license allows. Read
> [Reading licenses before you reuse anything](../part-1-from-user-to-developer/reading-licenses-before-reusing.md)
> before you change an extension someone else wrote, and keep their copyright notices.

## What you need before you start

| You need | Why | Where it is explained |
|---|---|---|
| A development installation of Exponential 6 | you will break things on purpose | [Tools you need](../part-1-from-user-to-developer/tools-you-need.md) |
| Git | to see every change you make and undo it | [Develop with Git](../../develop-with-git.md) |
| The debug output | to see warnings and deprecations | [Debug output and logs for developers](../part-1-from-user-to-developer/debug-output-and-logs-for-developers.md) |
| A browser with developer tools | to see what the Content Security Policy blocks | [Content Security Policy for extensions](../part-3-extension-developer/content-security-policy-for-extensions.md) |
| Part 3 of this book, at least the anatomy | you need to know where things live | [Part 3](../part-3-extension-developer/README.md) |

## Related pages

- Previous: [Part 3: The extension developer](../part-3-extension-developer/README.md)
- Next: [Why most of the changes are small](why-most-changes-are-small.md)
- [PHP 8 support](../../../bc/6.0/php8.md) and [PHP 8.0 support](../../../bc/6.0/php-8.0-support.md)
- [Behavior changes for extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- [Extension development: the book](../README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
