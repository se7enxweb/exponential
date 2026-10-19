---
title: The upgrade checklist
audience: developer, app-creator
category: extension-development
order: 20
summary: The whole job of bringing an old extension to Exponential 6 on one page: preparation, PHP, metadata, browser, administration, tests and release, each step with the chapter that explains it.
keywords: upgrade checklist, old extension, PHP 8, CSP, jQuery 4, ezinfo, extension.xml, composer.json, admin4, tests, release
type: help
---
# The upgrade checklist

Part 4, chapter 2. This is the whole upgrade on one page. Print it, copy it into an issue of your repository, or
tick it off in your notes. Each line links the chapter that explains it. The order is the one that wastes the least
time: things that stop the extension from loading first, things that only look wrong last.

## 0. Before you change anything

- [ ] Read the license. You may upgrade and share the extension only as it allows
      ([Reading licenses before you reuse anything](../part-1-from-user-to-developer/reading-licenses-before-reusing.md)).
- [ ] Put the extension under Git, if it is not already, and commit it unchanged. That first commit is your
      "before" ([Develop with Git](../../develop-with-git.md)).
- [ ] If it came from someone else's repository, fork it on GitHub rather than copying it, so the history and the
      credit stay ([Forking on GitHub, step by step](../part-5-extend-fork-contribute/forking-on-github.md)).
- [ ] Work on a development installation, never on a live site.
- [ ] Take the inventory ([Taking inventory of an old extension](inventory-an-old-extension.md)).

## 1. Make it load

- [ ] `php -l` on every PHP file, with PHP 8.0 if you want to support the oldest PHP Exponential 6 runs on, and with
      the newest PHP you use ([PHP 8 errors that stop old code](php-8-errors-that-stop-old-code.md)).
- [ ] Fix `ezinfo.php` if its keys are bare words ([Modernizing ezinfo.php and extension.xml](modernizing-ezinfo-and-extension-xml.md)).
- [ ] Activate the extension and regenerate the autoloads (`php bin/php/ezpgenerateautoloads.php -e`).
- [ ] Clear the caches (`php bin/php/ezcache.php --clear-all`).
- [ ] Open the front page and the admin. Both must still load.

## 2. Make it run without errors

- [ ] Turn on the debug output on your development copy
      ([Debug output and logs for developers](../part-1-from-user-to-developer/debug-output-and-logs-for-developers.md)).
- [ ] Open every page, run every view, every cronjob part and every CLI script of the extension.
- [ ] Fix every fatal error and every warning that names a file of the extension:
  - [ ] constructors named after the class ([Old-style constructors and static calls](old-constructors-and-static-calls.md))
  - [ ] instance methods called statically (same chapter)
  - [ ] removed functions ([Removed PHP functions and their replacements](removed-php-functions.md))
  - [ ] `count()` on things that are not countable, string functions given `null`
        ([PHP 8 errors that stop old code](php-8-errors-that-stop-old-code.md))
  - [ ] database code ([Database code for PHP 8 and every engine](database-code-for-php-8.md))

## 3. Make it quiet

- [ ] Fix every "Deprecated" line from the extension's files
      ([PHP 8.1 to 8.5 deprecations](php-8-1-to-8-5-deprecations.md)):
  - [ ] implicitly nullable parameters ([Nullable parameters and type declarations](nullable-parameters-and-types.md))
  - [ ] dynamic properties ([Dynamic properties](dynamic-properties.md))
  - [ ] SPL methods without return types
  - [ ] functions deprecated in 8.2 to 8.5 (`utf8_decode()`, `curl_close()`, `imagedestroy()` and others)

## 4. Make it describe itself

- [ ] `ezinfo.php`: quoted keys, `Name`, `Version`, `Copyright`, `License`, `Info_url`
      ([Modernizing ezinfo.php and extension.xml](modernizing-ezinfo-and-extension-xml.md)).
- [ ] `extension.xml`: the same version, license and website.
- [ ] `composer.json`: type, PHP constraint, license, the installer name ([Modernizing composer.json](modernizing-composer-json.md)).

## 5. Make it work in the browser

- [ ] Open every page with the browser's developer tools; read the console and the network tab.
- [ ] Move inline `<script>` blocks into files ([CSP: moving inline scripts into files](csp-moving-inline-scripts-out.md)).
- [ ] Replace `onclick=` and the other event attributes ([CSP: event handler attributes to data attributes](csp-event-handlers-to-data-attributes.md)).
- [ ] Replace `href="javascript:..."`, `eval()` and string timers ([CSP: javascript: links, eval and string timers](csp-javascript-links-and-eval.md)).
- [ ] Move `<style>` blocks and `style=""` attributes into stylesheets ([Inline CSS to stylesheets](inline-css-to-stylesheets.md)).
- [ ] Replace YUI ([From YUI to expui](yui-to-expui.md)).
- [ ] Use the jQuery ezjscore loads, and replace calls jQuery 4 removed ([From old jQuery to jQuery 4](old-jquery-to-jquery-4.md)).

## 6. Make it belong in the administration

- [ ] Style admin pages with the shared components and tokens, in light and dark mode
      ([admin4 styling for extension pages](admin4-styling-for-extension-pages.md)).
- [ ] Context menu entries, left menu entries and tabs without inline code
      ([Context menus and admin integration](context-menus-and-admin-integration.md)).
- [ ] Translations for every interface text (`i18n` with your own context).

## 7. Names and credit

- [ ] Product names in texts and documentation ("Exponential"), never rename kernel identifiers
      ([Naming and branding during an upgrade](naming-and-branding-during-an-upgrade.md)).
- [ ] Keep the original authors' copyright notices; add your own beside them
      ([Attribution and copyright notices](../part-5-extend-fork-contribute/attribution-and-copyright-notices.md)).

## 8. Keep it working

- [ ] A test that runs without a database: every PHP file lints, no inline scripts or styles in templates
      ([Tests and CI for an upgraded extension](tests-and-ci-for-an-upgraded-extension.md)).
- [ ] A GitHub Actions workflow that runs it on every push.

## 9. Release it

- [ ] Choose the next version from the tags that exist ([Choosing the next version number](../part-7-releasing/choosing-the-next-version.md)).
- [ ] Bump the version in `ezinfo.php`, `extension.xml` (and `composer.json` if it has one) in one commit before the tag.
- [ ] Write the changelog: what changed, and what was wrong with the previous version, if anything.
- [ ] Tag, push, create the GitHub release; check that Packagist picked it up.
- [ ] Tell the original author: open a pull request or an issue with your fixes
      ([Contributing back with pull requests](../part-5-extend-fork-contribute/contributing-back-with-pull-requests.md)).

The whole release procedure is in [Part 7: Releasing your extension](../part-7-releasing/README.md).

## How long does it take?

The sources do not give averages, and every extension is different. From the upgrades told in the case studies of
this part:

| Kind of extension | What the case studies show |
|---|---|
| Settings and templates only | often nothing beyond step 4 |
| A few PHP classes | an afternoon for steps 1 to 4 |
| Admin pages with JavaScript | steps 5 and 6 are most of the work |
| Its own copy of a JavaScript application | a project of its own ([Case study: the layouts editor on jQuery 4](case-study-the-layouts-editor-on-jquery-4.md)) |

## Related pages

- Previous: [Why most of the changes are small](why-most-changes-are-small.md)
- Next: [Taking inventory of an old extension](inventory-an-old-extension.md)
- [Behavior changes for extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
