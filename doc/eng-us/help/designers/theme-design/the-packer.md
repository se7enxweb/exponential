---
title: The packer
audience: designer, developer
category: theme-design
order: 230
summary: What ezjscore's packer does with your stylesheets and scripts: merging, minifying, rewriting url() paths, naming the packed file after its sources and their time, where the file is stored, and the settings that control it.
keywords: packer, ezjscore packer, minify, merge css, bundle, packed file, cache public, AppendLastModifiedTime, DevelopmentMode, pack level, CustomHosts, JavaScriptOptimizer, CssOptimizer
type: help
---
# The packer

This page is for designers who want to understand what happens to their CSS and JavaScript between the design folder
and the browser. The packer is part of the ezjscore extension (`extension/ezjscore/classes/ezjscpacker.php`).

## Contents

1. [What it does](#what-it-does)
2. [Where packed files live](#where-packed-files-live)
3. [How a packed file is named](#how-a-packed-file-is-named)
4. [Settings](#settings)
5. [Developing with the packer off](#developing-with-the-packer-off)
6. [Troubleshooting](#troubleshooting)

## What it does

For each `ezcss_load`, `ezscript_load` (and the other packer operators) call, the packer:

1. finds every file through the design cascade;
2. at pack level 1 or more, concatenates them into one file, in the order given;
3. at level 2, runs the optimizers of `ezjscore.ini [eZJSCore]` (`JavaScriptOptimizer[]=ezjscJavascriptOptimizer`,
   `CssOptimizer[]=ezjscCssOptimizer`), which remove comments and whitespace;
4. for CSS, rewrites relative `url()` paths into absolute ones, so images and fonts referenced by your stylesheet still
   load from the packed location;
5. writes the result once and prints a single `<link>` or `<script>` tag pointing to it.

Fewer files mean fewer requests: the media design's `pagelayout/head/style.tpl` names fifteen stylesheets in one
`ezcss_load` call, and the browser fetches one file.

## Where packed files live

In the public cache of the site: `var/<site>/cache/public/stylesheets/` and `.../javascript/`. The shipped rewrite rules
serve `var/<site>/cache/public/` directly (`.htaccess_root`), so packed files never go through PHP once written.

## How a packed file is named

With `ezjscore.ini [Packer] AppendLastModifiedTime=enabled` (the default in this release) a packed file is named

```text
<md5 of the source file list and pack level>_<newest modification time of the sources>_<media>.css
```

for example `82b3f6472765825fde39b69dc69ebd0a_1792109816_all.css`. Consequences:

- **Change a source file and its time changes**, so the packed file gets a new name and browsers fetch it; no manual
  version query is needed ([No version queries on asset links](no-version-queries-on-asset-links.md)).
- Packed files can be cached by browsers for a long time.
- Old packed files stay on disk until the `ezjscore-packer` cache is cleared.

## Settings

| Setting | Default | Effect |
|---|---|---|
| `site.ini [TemplateSettings] DevelopmentMode` | `disabled` | `enabled` forces pack level 0 (every file on its own), unless `ezjscore.ini [eZJSCore] Packer` is set |
| `ezjscore.ini [eZJSCore] Packer` | not set | `disabled` = level 0 always; a number 0 to 3 = that level always; `enabled` = the level of each call even in development mode |
| `ezjscore.ini [Packer] AppendLastModifiedTime` | `enabled` | the time in the file name, as above |
| `ezjscore.ini [Packer] CustomHosts[]` | empty | serve packed `.js` or `.css` from another host, e.g. `CustomHosts[.js]=https://static.example.com` |
| `ezjscore.ini [eZJSCore] LoadFromCDN` | `disabled` | `ezjsc::jquery` from the local copy (enabled: from the CDN URLs of `ExternalScripts[]`) |

Change them in your design extension's or siteaccess's `ezjscore.ini.append.php`, never in `extension/ezjscore/settings/`.

## Developing with the packer off

On a development or preview siteaccess:

```ini
# settings/siteaccess/<preview>/site.ini.append.php
[TemplateSettings]
DevelopmentMode=enabled
```

Each stylesheet is then linked on its own, under its own name, so the browser's style inspector shows which file and line
a rule comes from. Never enable `DevelopmentMode` on a live site: it also makes the template engine check file times
on every request.

## Troubleshooting

| Symptom | Cause and fix |
|---|---|
| A file is missing from the packed CSS | not found in the cascade: the debug output has "Could not find: ..."; check the path relative to `stylesheets/` |
| Images in CSS broken after packing | an absolute `url(/...)` that points to the wrong place; use paths relative to the stylesheet |
| Admin without styles after a cache clear | the admin's head names packed files inside a template-block; clear `ezjscore-packer` and `template-block` together (`exp:velocity deploy --packer`) |
| One script error breaks every script | in a packed bundle all files share one file; fix the error or split the bundle |

## Related pages

- [Loading stylesheets](loading-stylesheets.md)
- [The packer cache and deploys](packer-cache-and-deploy.md)
- [The ezcss and ezscript operators](ezcss-and-ezscript-operators.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
