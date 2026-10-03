# ezwebin: the Website Interface design

`ezwebin` ("Exponential Website Interface LS") is the classic ready-made website for Exponential:
a design with templates for articles, blogs, events, forums, galleries, products, RSS and shop pages,
the content classes and the installer packages behind them, the page layout with menus, search box and
footer, and the edit-page templates for editors. It extends [ezwt](ezwt.md) (the website toolbar) and
requires `ezjscore`. The 6.0.x line is the Exponential release of the extension; 6.0.16 (2 October
2026) is the current one.

For a new site, [ezdemo](ezdemo.md) shows what the design looks like with demo content, and the
[simple theme](sevenx_themes_simple.md) is a lighter alternative that builds on the same classes.

## What changed in the Exponential 6 releases

| Release | Change |
|---|---|
| 6.0.1 | Mass update from the parent package repository `ezwebin-ezpackage` |
| 6.0.2 | Path check and page depth calculation fixed |
| 6.0.3 | **HTML5 markup**: XHTML self-closing slashes (`<br />`, `<input />`, `<img />`, `<meta />`, `<link />` and other void elements) and obsolete `type` and `language` attributes on `<script>`, `<style>` and stylesheet `<link>` tags are removed in 57 files. If you override these templates, expect the same cleanup in yours |
| 6.0.4 to 6.0.8 | `ezinfo.php` and `extension.xml` state name, version, copyright, license and website, so the about page and upgrade checks agree; visible texts name Exponential |
| 6.0.7 | **Forgot password**: the page no longer reveals whether an address has an account and escapes what it prints ([details](../../../bc/6.0/extensions-behaviour-changes.md#2-forgot-password-pages-no-longer-tell-whether-an-address-has-an-account)) |
| 6.0.8 | **Error pages** show their own error in the page title ([details](../../../bc/6.0/extensions-behaviour-changes.md#3-error-pages-show-their-own-error-in-the-page-title)) |
| 6.0.9, 6.0.10 | Every visible text is a translation string with German: the event calendar, forum, tag cloud, product and shop texts, the RSS export, import and list pages, the order confirmation summary and totals, blog post tags and the document import message. English and the untranslated catalogue carry every message of the German one |
| 6.0.12 | The **blog archive** operator `ezarchive()` lists the months on Oracle too (it had queries for MySQL and PostgreSQL only; Oracle gets a date computed from the epoch). Class identifier and parent node id are escaped and cast |
| 6.0.13 | The edit page's collapsible attribute groups use jQuery 4 (`.on()`) |
| 6.0.14 | Date and date/time fields use Exponential UI's calendar, `exp::datepicker`, when `expui` is active; the extension requires `se7enxweb/expui ^1.0.0.1` |
| 6.0.15 | **YUI removed**: the date edit templates load `exp::datepicker` (with `exp_config()`), and `content.css` no longer styles the YUI calendar. See [YUI removal](../../../bc/6.0/yui-removal.md) |
| 6.0.16 | The copyright notices and the about page name "1998 - 2026 7x & Exponential Foundation" first, above the eZ Systems notices, which stay as the GPL requires |

## Upgrading

* Run `php bin/php/ezpgenerateautoloads.php -e` and clear the template caches
  (`php bin/php/ezcache.php --clear-tag=template,content --allow-root-user`) after updating, so the
  HTML5 templates and the new translation files are used.
* If your site design overrides date fields, the edit page groups or the forgot password template, compare
  with the changes above.
* Install `se7enxweb/expui` (it is required) so the date fields keep a working calendar after YUI is gone.

## Related

* [ezwt](ezwt.md), [ezflow](ezflow.md), [ezdemo](ezdemo.md)
* [Chronicle](../../../history/extensions/ezwebin.md) and [release notes](../../../changelogs/extensions/ezwebin.md)
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
* [Change ledger](../../../history/ledger/ezwebin.md)
* [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
* [Month: 2024-01 (all extensions)](../../../history/extensions/months/2024-01.md)
* [Month: 2026-04 (all extensions)](../../../history/extensions/months/2026-04.md)
* [Month: 2026-07 (all extensions)](../../../history/extensions/months/2026-07.md)
* [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
* [Month: 2026-10 (all extensions)](../../../history/extensions/months/2026-10.md)
