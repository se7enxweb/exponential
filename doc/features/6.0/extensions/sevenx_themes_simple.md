# sevenx_themes_simple: the simple theme

`sevenx_themes_simple` is the light, responsive design that ships with the default Exponential
installation: a header with logo, a multi-level dropdown menu and a cart icon, a footer, article, blog,
gallery, product and basket templates, a favicon set and the stylesheet `main.css` with its responsive
companion `main.res.css`. It is built on the classes of [ezwebin](ezwebin.md) and the toolbar of
[ezwt](ezwt.md), and installs the ezwebin based packages the default installation needs. The design is
named `simple`; an `Exponential` logo and banner replace the earlier product logo.

It was first imported on 25 January 2024 (1.0.0) and reached 1.0.21 on 2 October 2026.

## Use it

Activate it as a design extension (`design.ini [ExtensionSettings] DesignExtensions[]=sevenx_themes_simple`). The extension
carries siteaccess defaults (`ezjscore.ini` and `override.ini`) for the `site` and `sevenx_site_user` siteaccesses (the `site`
one since 1.0.5), which is what the default installation expects.

Template overrides, stylesheets and scripts come from `design.ini` of the extension:

| Block | Key | Value |
|---|---|---|
| StylesheetSettings | `CSSFileList[]` | `websitetoolbar.css`, `libs/fontawesome/css/all.min.css`, `magnific-popup.css`, `main.css`, `main.res.css` |
| JavaScriptSettings | `JavaScriptList[]` | `ezjsc::jquery`, `jquery.magnific-popup.js`, `main.js` |

The theme is "composer managed": do not edit its files in place; override templates in your own design
extension, listed before it in `ActiveAccessExtensions`.

## What changed in the Exponential 6 era

| Release | Change |
|---|---|
| 1.0.0 to 1.0.2 | Initial import, favicons matching the stock logo, logo in the page header, website toolbar styles, template overrides for the default siteaccess, responsive photo galleries, cart button in the header, billboard banners; the extension directory renamed for compatibility |
| 1.0.3, 1.0.4 | Product name and logo replaced by Exponential in templates, `composer.json` and the project URL; `page_footer.tpl` made easier to hide the "Powered by Exponential" notice |
| 1.0.5, 1.0.6 | Extension-based default settings for the `site` siteaccess (matches what the default installation expects of `sevenx_site_user`); copyright year |
| 1.0.7 to 1.0.10 | The floating website toolbar no longer covers the site menus or pushes content down by several rem; cart alignment and inline image overflow; main sections padding |
| 1.0.11 | **Multi-level dropdown menu**: a third level opens as a fly-out under the second level; the active path (top, second and third level) is highlighted in orange and the exact current page is bold on a light-orange background; "active" and "open" are separate, so a deep page does not auto-expand the menu; a right-pointing toggle marks parents; image templates default to the uploaded original and constrain image width |
| 1.0.12 | HTML5 markup cleanup: XHTML slashes, duplicate meta tags, conditional logo width and height and obsolete attributes removed |
| 1.0.14 | Header menu cart icon positioning on small screens |
| 1.0.15 | **Error pages show their own error in the title** ([details](../../../bc/6.0/extensions-behaviour-changes.md#error-page-titles)) |
| 1.0.16 | The footer background is a relative path that resolves wherever the extension directory is served from |
| 1.0.17, 1.0.18 | Every visible text is a translation string with German: product labels, the error page, the basket, the footer ("Powered by" is one sentence with the link as a placeholder), blog post tags and the order confirmation summary and total |
| 1.0.19 | Magnific Popup works on jQuery 4 (`Array.isArray`, `typeof`, `.on('click')`, `.trigger('focus')`); no shipped template loads it today |
| 1.0.20 | **YUI removed**: the siteaccess `ezjscore.ini` files carry no YUI library paths, CDN URLs or loader options, the stylesheets no longer style the YUI calendar (date fields use Exponential UI's), and the star rating view loads the jQuery script only. A new `extension.xml` ships |
| 1.0.21 | The about page names "1998 - 2026 7x & Exponential Foundation" first |

## Related

* [ezwebin](ezwebin.md), [ezwt](ezwt.md), [ezstarrating](ezstarrating.md)
* [Chronicle](../../../history/extensions/sevenx_themes_simple.md) and [release notes](../../../changelogs/extensions/sevenx_themes_simple.md)
* [YUI removal](../../../bc/6.0/yui-removal.md)
