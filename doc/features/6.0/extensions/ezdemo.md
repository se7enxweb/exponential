# ezdemo: the demo design

This page is for anyone who wants to see a complete Exponential site, or copy templates from one. `ezdemo`
("Exponential Demo Design LS") is the demo website of Exponential: a design, settings and installer packages that show
what [ezwebin](ezwebin.md), [ezflow](ezflow.md), [ezwt](ezwt.md) and [ezodf](ezodf.md) do together. It has galleries, a
frontpage and landing page editor, flyouts, a campaign block, RSS pages and a shop. Version 6.0.9 (2 October 2026) is
current.

It requires `ezjscore` and extends `ezflow`, `ezwt` and `ezodf`.

## Use it

1. Activate the extension as a design extension: `design.ini [ExtensionSettings] DesignExtensions[]=ezdemo` (the
   extension ships that line).
2. Use the design `ezdemo` (`design/ezdemo`; the extension also carries a `design/admin` folder).
3. To reuse a template, copy it from `design/ezdemo/templates` into your own design extension. Do not edit the demo in
   place.

The scripts the design loads on public pages are the `[JavaScriptSettings] FrontendJavaScriptList[]` of its
`design.ini`: `ezjsc::jquery`, `init_ua.js`, `handle_transition.js`, `toggle_class.js` and `eztransition.js`. The
gallery, flyout and ajax search scripts (`ezgallery.js`, `ezgallerynavigator.js`, `ezflyout.js`, `ezajaxsearch.js`)
are in `design/ezdemo/javascript/`.

## What changed in the Exponential 6 releases

| Release | Change |
|---|---|
| 6.0.1 | Mass update from the parent package repository `ezdemo-ezpackage`. |
| 6.0.2, 6.0.3 | The extension names itself Exponential Demo Design LS; visible texts name Exponential; templates write `<br>` as HTML5 does; `ezinfo.php` and the license in full. |
| 6.0.4 | The **Forgot password** page does not reveal whether an address has an account and escapes what it prints ([details](../../../bc/6.0/extensions-behaviour-changes.md#2-forgot-password-pages-no-longer-tell-whether-an-address-has-an-account)). |
| 6.0.5 | **Error pages** show their own error in the page title ([details](../../../bc/6.0/extensions-behaviour-changes.md#3-error-pages-show-their-own-error-in-the-page-title)). |
| 6.0.6 | German covers every interface text of the templates: the RSS export, import and list pages, header links and search box, footer, content editor buttons, product, file, video and blog views and the frontpage editor. |
| 6.0.7 | The edit page's collapsible attribute groups use jQuery 4. |
| 6.0.8 | **YUI removed.** See below. |
| 6.0.9 | Copyright notices name "1998 - 2026 7x & Exponential Foundation" first. |

About 6.0.8: the galleries (`ezgallery`, `ezgallerynavigator`, `ezsimplegallery`) and `ezflyout` are jQuery modules
(`$.eZ.*`) with the same options and markup. Transitions are CSS (`eztransition.js`); `init_ua.js`, `toggle_class.js`
and `handle_transition.js` use the DOM. `design.ini` loads `ezjsc::jquery` instead of `ezjsc::yui3`; the landing page
editor loads jQuery; a jQuery `ezajaxsearch.js` ships with the design; the ezpage tab styles use the `.ezpage-tabs*`
classes of ezflow 6.1.4.

**Upgrade note:** if you copied the demo's gallery or flyout markup into your own design, nothing changes for your
markup: options and markup are the same, only the script behind them is jQuery. See
[YUI removal](../../../bc/6.0/yui-removal.md).

## Related pages

- [ezwebin](ezwebin.md), [ezflow](ezflow.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/ezdemo.md) and [release notes](../../../changelogs/extensions/ezdemo.md)
- [Change ledger](../../../history/ledger/ezdemo.md)
- Months: [2024-01](../../../history/extensions/months/2024-01.md), [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
