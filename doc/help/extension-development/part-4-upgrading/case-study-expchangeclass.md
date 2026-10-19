---
title: "Case study: from eZChangeclass to expchangeclass"
audience: developer, app-creator
category: extension-development
order: 230
summary: One upgrade told from its commits: how a 2007 extension that changes an object's class became expchangeclass on PHP 8, with transactions, a policy, translations, CSP-safe menus and nine releases.
keywords: case study, expchangeclass, eZChangeclass, fork, PHP 8, transaction, rollback, static call, PHP 4 constructor, policy, CSP, admin4, release, upgrade
type: help
---
# Case study: from eZChangeclass to expchangeclass

Part 4, chapter 23. This chapter follows one upgrade from start to finish, using the history of
se7enxweb/expchangeclass on GitHub. It shows the order the work was done in, why, and how each step became a
release. Every claim here comes from the extension's commits, its README or its changelog.

## What the extension does

It changes the content class of an existing content object, mapping each attribute of the old class onto one of the
new: for one object from the administration (an entry in the context menu), or for every object of a class from the
command line.

## Where it came from

The original extension, eZChangeclass, dates from 2007 (its copyright line in `ezinfo.php`) and is GPL-licensed.
The README of expchangeclass explains that two forks of it were in circulation, and that neither was safe to run:

- One fork kept "the original PHP 4 shape": a class `conversionFunctions` with a `conversionFunctions()`
  constructor and non-static methods that the module called statically. On PHP 8 it "stops at the line that does
  the work" with `Non-static method conversionFunctions::customConverter() cannot be called statically`.
- The other fork fixed that, but "a conversion that failed part way through was never rolled back, and the batch
  script could not terminate if an object refused to convert".

So the new extension "takes the OpenContent code as its base, takes the attribute pre-selection and packaging from
the Netgen fork, and fixes what neither had." That is a good model: read every existing version first, take the
best of each, and say where each part came from.

## The timeline

| Date | Commit | Step | Chapter of this part |
|---|---|---|---|
| 2026-09-13 | 7c3f164 | initial import of the fork, hardened for PHP 8, with transactional rollback | [PHP 8 errors](php-8-errors-that-stop-old-code.md), [Database code](database-code-for-php-8.md) |
| 2026-09-13 | 5fc6818 | an include left commented out made module views die when the autoload array did not carry a class | [Taking inventory](inventory-an-old-extension.md) |
| 2026-09-16 | a28b3da | the sub-items menu entry had the same element id as the node menu's | [Context menus](context-menus-and-admin-integration.md) |
| 2026-09-28 | 9f1e911, 6062a43 | every visible text a translation string, German added; version 1.0.2 | [Naming and branding](naming-and-branding-during-an-upgrade.md) |
| 2026-09-30 | f5dadff, ffc1e4c | the about page names it "Exponential Change Class"; 1.0.3 | [ezinfo.php and extension.xml](modernizing-ezinfo-and-extension-xml.md) |
| 2026-10-02 | ac8b9b6, dc0d533 | command line scripts, cronjob parts and module views became classes the files call; 1.0.4 | |
| 2026-10-02 | b86989f, e936f91 | menu entries only for users who may convert a class; 1.0.5 | [Context menus](context-menus-and-admin-integration.md) |
| 2026-10-05 | 32196f0 | installs and runs on PHP 8.0; 1.0.6 | [composer.json](modernizing-composer-json.md) |
| 2026-10-16 | c2a4f41 | more translations | |
| 2026-10-17 | e2cd128 | result boxes use the shared admin notice instead of `style=""` | [Inline CSS](inline-css-to-stylesheets.md), [admin4 styling](admin4-styling-for-extension-pages.md) |
| 2026-10-17 | 4958e02 | the parameter file shown after a conversion is escaped | |
| 2026-10-17 | 88b60d9, 2fa5a66 | context menu entries without inline handlers; 1.0.9 | [Event handler attributes](csp-event-handlers-to-data-attributes.md) |

Note the rhythm: a fix, then a version bump, then the next fix. Each release is small enough to describe in one
line, and a site can take any of them.

## Step 1: make it safe before making it pretty

The first commit already contained the fixes that matter for content. From the changelog of 1.0.0:

- "Conversion runs in one transaction and rolls back as a unit."
- "`die()` inside the attribute loop replaced with exceptions, so a failure can be rolled back and reported instead of
  ending the request mid-write."
- "The attribute mapping is validated against both class data maps before anything is written."
- "Converter methods declared static, matching how they are invoked."
- "Relation list to bloc converter rewritten on DOMDocument, replacing the removed PHP 4 eZXML API."
- "Text to ezxmltext conversion escapes the value first; content containing an ampersand or an angle bracket no
  longer produces invalid xml."

An extension that rewrites content must never leave it half-changed. That came first, before any styling.

The calling convention the README shows after the change:

```php
$error = null;
if ( !conversionFunctions::convertObject( $objectID, $classID, $mapping, $error ) )
{
    // content is exactly as it was
}
```

A result and an error message instead of `die()`, so the module can show what happened.

## Step 2: bugs found by reading the settings

The changelog records one bug that no PHP version would have reported: `changeclass.ini` declared the group
`[ezstring]` twice. In INI files, "a repeated group does not merge: the second occurrence wins" (README), so one
conversion silently did not exist. The fix was one group per source datatype. Read an old extension's settings as
carefully as its PHP.

## Step 3: access

"The module declared no functions, so the only possible grant was all-or-nothing on the module." The fork added a
`changeclass/convert` policy function, and commit b86989f shows the menu entries only to users who hold it:

```text
{if fetch( 'user', 'has_access_to', hash( 'module', 'changeclass', 'function', 'convert' ) )}
```

See [Policy functions and access](../part-3-extension-developer/policy-functions-and-access.md).

## Step 4: texts, names and credit

Commit 9f1e911 made every visible text a translation string. Commit f5dadff gave it a readable name on the about
page. The original author stays credited in `ezinfo.php` (an `Includes` entry naming eZChangeclass with its
copyright and license), in `composer.json` (`authors`, with the role "Original author of eZChangeclass") and in the
README, which links the original repository.

## Step 5: the oldest PHP

Commit 32196f0 widened the PHP constraint to 8.0 only after checking the code "with PHPCompatibility for PHP 8.0,
linted and loaded with PHP 8.0.30, and served by PHP 8.0 on a full installation". The version went up in
`composer.json`, `extension.xml` and `ezinfo.php` in the same commit.

## Step 6: the browser

The last steps were about the Content Security Policy:

- e2cd128: the conversion result boxes "with style="" attributes and fixed red and green borders" became the shared
  `a4-notice` component with `is-ok` or `is-bad`, which "follows the admin tokens in light and dark mode", plus a
  small stylesheet loaded through `BackendCSSFileList[]`. A test without a database keeps the admin templates free of
  inline CSS.
- 88b60d9: the context menu entries "declare their form in data-menu-form", with a small script for older kernels,
  and a second test keeps the templates free of inline code.

Along the way, 4958e02 escaped a value printed without `wash`: an upgrade is a good moment to look at every place
where a template prints data.

## What to take from it

1. Read every existing version and fork before you start; credit each one you take from.
2. Fix what can damage content first.
3. Release often, one subject per release, with the version in all three files.
4. Add a test for each kind of problem when you fix it, so it stays fixed.
5. Leave the visual work for last, but do it: it is what users see.

## Related pages

- Previous: [Tests and CI for an upgraded extension](tests-and-ci-for-an-upgraded-extension.md)
- Next: [Case study: the layouts editor on jQuery 4](case-study-the-layouts-editor-on-jquery-4.md)
- [expchangeclass on GitHub](https://github.com/se7enxweb/expchangeclass)
- [Case study: porting a package from another project with credit](../part-5-extend-fork-contribute/case-study-ported-from-another-project.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
