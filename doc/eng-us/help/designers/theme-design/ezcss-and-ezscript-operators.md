---
title: The ezcss and ezscript operators
audience: designer, developer
category: theme-design
order: 220
summary: Reference for the eight packer operators of ezjscore (ezcss, ezcss_require, ezcss_load, ezcssfiles, ezscript, ezscript_require, ezscript_load, ezscriptfiles) with every parameter, default and behavior.
keywords: ezcss, ezcss_load, ezcss_require, ezcssfiles, ezscript, ezscript_load, ezscript_require, ezscriptfiles, pack_level, media, ignore_loaded, operator reference, ezjscore operators
type: help
---
# The ezcss and ezscript operators

This page is a reference for designers and developers. All eight operators are defined by the class
`ezjscPackerTemplateFunctions` in `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php` and registered in
`extension/ezjscore/autoloads/eztemplateautoload.php`.

## Contents

1. [Signatures](#signatures)
2. [require, load and plain: how they differ](#require-load-and-plain-how-they-differ)
3. [Pack levels](#pack-levels)
4. [File names](#file-names)
5. [Examples](#examples)

## Signatures

```text
ezscript[_require|_load]( array|string scripts
                          [, string type='text/javascript'
                          [, string language=''
                          [, string charset='utf-8'
                          [, int pack_level=2 ]]]] )

ezcss[_require|_load]( array|string css_files
                       [, string media='all'
                       [, string type='text/css'
                       [, string rel='stylesheet'
                       [, string charset=''
                       [, int pack_level=3 ]]]]] )

ezscriptfiles( array|string scripts [, int pack_level=2 [, bool ignore_loaded=false ]] )
ezcssfiles( array|string css_files [, int pack_level=3 [, bool ignore_loaded=false ]] )
```

(from the class's header comment; `charset` on stylesheets is deprecated as not valid HTML.) Arrays may be nested;
they are flattened, so `ezini()` lists can be passed inside an array.

## require, load and plain: how they differ

| Call | Before any _load ran | After a _load ran |
|---|---|---|
| `ezcss_require` / `ezscript_require` | remembers the files (in the view's persistent variable), prints nothing | prints the files not printed yet |
| `ezcss_load` / `ezscript_load` | prints its files plus all remembered ones, marks the kind as loaded | prints the files not printed yet |
| `ezcss` / `ezscript` | prints its files | prints its files |
| `ezcssfiles` / `ezscriptfiles` | returns URLs of its files (minus already printed ones, unless `ignore_loaded`) | same |

Because the remembered files are kept in the persistent variable of the content view, a full view served from the
view cache still delivers its required files to the pagelayout.

## Pack levels

| Level | Effect |
|---|---|
| 0 | off: every file served on its own |
| 1 | files merged into one |
| 2 | merged, whitespace and comments removed (JavaScript and CSS optimizers of `ezjscore.ini [eZJSCore]`) |
| 3 | same as 2 (deprecated) |

The level is forced to 0 when `site.ini [TemplateSettings] DevelopmentMode=enabled`, unless
`ezjscore.ini [eZJSCore] Packer` is set (to `enabled`, `disabled` or a level 0 to 3). Stylesheets: relative `url()`
paths are rewritten to absolute ones when packed, so images and fonts still load.

## File names

- `'main.css'`: found in `stylesheets/` of the design cascade (`javascript/` for scripts).
- `'sub/file.css'`: a subfolder.
- `'/extension/x/design/y/stylesheets/file.css'`: a leading `/` means a path from the installation root, outside the
  usual subfolder.
- `'ezjsc::jquery'`, `'ezjsc::jqueryUI'`: generators of ezjscore (`[ezjscServer_ezjsc]` in `ezjscore.ini`); a name with
  `::` calls a server function instead of reading a file.

A file that is not found is skipped with a warning in the debug output ("Could not find: ..."), not an error.

## Examples

```tpl
{* all stylesheets of the design, plus whatever views required *}
{ezcss_load( array( 'base.css', 'components.css' ) )}

{* merged but not minified: keeps vendor CSS byte-identical *}
{ezcss_load( array( 'vendor.css' ), 'all', 'text/css', 'stylesheet', '', 1 )}

{* jQuery and the site script at the end of the body *}
{ezscript_load( array( 'ezjsc::jquery', 'site.js' ) )}

{* deferred bundle *}
{foreach ezscriptfiles( array( 'late.js' ), 2, true() ) as $url}<script defer src="{$url}"></script>{/foreach}
```

> **Note**
> The starter design calls `ezcss_load( ..., 'all', 'text/css', 'stylesheet', 1 )` with five arguments. Read against
> the signature, the fifth is `charset`, so the pack level stays at its default. Pass all six arguments (with `''`
> for charset) when you want to set the level.

## Related pages

- [Loading stylesheets](loading-stylesheets.md)
- [Loading JavaScript](loading-javascript.md)
- [The packer](the-packer.md)
- [ezjscore](../../../features/6.0/extensions/ezjscore.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
