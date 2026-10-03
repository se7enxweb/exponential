# ezdemo: the demo design

`ezdemo` ("Exponential Demo Design LS") is the demo website of Exponential: a design, a set of settings
and installer packages that show what [ezwebin](ezwebin.md), [ezflow](ezflow.md), [ezwt](ezwt.md) and
[ezodf](ezodf.md) can do together, with galleries, a frontpage and landing page editor, flyouts, a campaign
block, RSS pages and a shop. Use it to see the system working, and as a source of templates to copy.
Version 6.0.9 (2 October 2026) is current.

It requires `ezjscore` and extends `ezflow`, `ezwt` and `ezodf`.

## Use it

Activate the extension as a design extension: `design.ini [ExtensionSettings] DesignExtensions[]=ezdemo` (the extension ships that line) and use the design
`ezdemo` (`design/ezdemo`; the extension also carries a `design/admin` folder). The scripts the design loads on the public pages are the `[JavaScriptSettings]
FrontendJavaScriptList[]` of its `design.ini`: `ezjsc::jquery`, `init_ua.js`, `handle_transition.js`, `toggle_class.js` and `eztransition.js`, with the gallery, flyout and ajax
search scripts (`ezgallery.js`, `ezgallerynavigator.js`, `ezflyout.js`, `ezajaxsearch.js`) in `design/ezdemo/javascript/`. Copy a template you like from
`design/ezdemo/templates` into your own design extension; do not edit the demo in place.

## What changed in the Exponential 6 releases

| Release | Change |
|---|---|
| 6.0.1 | Mass update from the parent package repository `ezdemo-ezpackage` |
| 6.0.2, 6.0.3 | The extension names itself Exponential Demo Design LS; visible texts name Exponential; templates write `<br>` as HTML5 does; `ezinfo.php` and the license in full |
| 6.0.4 | **Forgot password** page does not reveal whether an address has an account and escapes what it prints ([details](../../../bc/6.0/extensions-behaviour-changes.md#2-forgot-password-pages-no-longer-tell-whether-an-address-has-an-account)) |
| 6.0.5 | **Error pages** show their own error in the page title ([details](../../../bc/6.0/extensions-behaviour-changes.md#3-error-pages-show-their-own-error-in-the-page-title)) |
| 6.0.6 | German covers every interface text of the templates: the RSS export, import and list pages, header links and search box, footer, content editor buttons, product, file, video and blog views and the frontpage editor |
| 6.0.7 | The edit page's collapsible attribute groups use jQuery 4 |
| 6.0.8 | **YUI removed.** The galleries (`ezgallery`, `ezgallerynavigator`, `ezsimplegallery`) and `ezflyout` are jQuery modules (`$.eZ.*`) with the same options and markup; transitions are CSS (`eztransition.js`); `init_ua.js`, `toggle_class.js` and `handle_transition.js` use the DOM. `design.ini` loads `ezjsc::jquery` instead of `ezjsc::yui3`; the landing page editor loads jQuery; a jQuery `ezajaxsearch.js` ships with the design; the ezpage tab styles use the `.ezpage-tabs*` classes of ezflow 6.1.4 |
| 6.0.9 | Copyright notices name "1998 - 2026 7x & Exponential Foundation" first |

If you copied the demo's gallery or flyout markup into your own design, nothing changes for your markup:
options and markup are the same, only the script behind them is jQuery. See
[YUI removal](../../../bc/6.0/yui-removal.md).

## Related

* [ezwebin](ezwebin.md), [ezflow](ezflow.md)
* [Chronicle](../../../history/extensions/ezdemo.md) and [release notes](../../../changelogs/extensions/ezdemo.md)
