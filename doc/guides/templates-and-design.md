# Templates and design: change what a page looks like

In this guide you will find out which design your site uses, see which template file produced a piece of a page, change it with an override that you can undo, check the syntax, and switch the administration interface to the `admin4` design. Follow it top to bottom; each part ends in something you can check. It takes about fifteen minutes.

Every command is run from the installation root, the directory that contains `index.php`. When you are logged in as `root`, add `--allow-root-user` (the examples write it out). You need a running site: [Getting started](getting-started.md) gets you there in minutes.

## 1. How a template is found

A page is made by `.tpl` files (the template language is described in part 6). Exponential looks for a template, for example `node/view/full.tpl`, in this order and uses the first file it finds:

1. the **override templates** whose rules in `override.ini` match the page (a class, a node, a section ...), found in `design/<design>/override/templates/` of the site design and of the extensions that carry design files;
2. the plain `templates/` directory of the site design, `design/<design>/templates/`;
3. the same directories of the additional designs;
4. the `standard` design, `design/standard/templates/`, which is the fallback and which you never edit.

A **design** is a directory with `templates/`, `override/templates/`, `stylesheets/`, `images/` and `javascript/`. It lives in `design/` of the installation or in `extension/<name>/design/<design>/`. An extension that carries a design is switched on with `DesignExtensions[]=<name>` in its `settings/design.ini.append.php`.

## 2. Find out which design you use

```bash
./console exp:ini where site/DesignSettings/SiteDesign eng --allow-root-user
```

Replace `eng` with the siteaccess you are looking at (the folders of `settings/siteaccess/`). Expected output on the shipped multisite setup:

```
site.ini/DesignSettings/SiteDesign (load order of siteaccess eng)
 1. settings/site.ini                                            scope default
      SiteDesign=admin
 2. settings/siteaccess/eng/site.ini.append.php                  scope siteaccess:eng
      SiteDesign=media
In effect:
      SiteDesign=media
```

The last line is your design. Its files are in `design/media/` or in an extension, here `extension/sevenx_themes_media/design/media/`. To see the other designs of a siteaccess:

```bash
./console exp:ini get site/DesignSettings/AdditionalSiteDesignList eng --allow-root-user
```

Reference: [Extension loading order](../features/6.0/extension-loading-order.md), [Additional extension directories](../features/6.0/additional-extension-directories.md).

## 3. See which template wrote which markup

Switch on template path comments. They are for development: they add markup to every template, so never leave them on for a production siteaccess or for feeds.

```bash
./console exp:ini set site/TemplateSettings/ShowTemplatePathComments enabled siteaccess:eng --allow-root-user
php bin/php/cache.php clear --tag=template --allow-root-user
```

Reload a page of the site, open "view source" and search for `START`. Expected: each piece is wrapped, for example

```html
<!-- START design/media/templates/pagelayout/footer.tpl -->
...
<!-- STOP design/media/templates/pagelayout/footer.tpl -->
```

When an override answered, the original name follows in brackets: `<!-- START .../full/article.tpl (node/view/full.tpl) -->` reads "the file `article.tpl` answered the request `node/view/full.tpl`". Switch it off again with the same command and `disabled`, and clear the template cache again.

If you prefer a click path, the same switch is in the debug bar: [Exp Debug bar](../features/6.0/exp-debug-bar.md), tab Settings, group templates. Details: [Template path comments](../features/6.0/template-path-comments.md).

## 4. Make a change with an override

You will restyle one article (one object) without touching anything else. An override has two parts: the template file and a rule saying when it applies.

### Click path (no files, undo in one click)

1. In the administration interface open **Design > Template Editor**.
2. Open `node/view/full.tpl` of your design.
3. Press **New override**, choose *Default copy*, enter the **Object ID** of the article and press **Create**.
4. Edit the new template, add a visible line such as `<p>Override works</p>` and save.
5. Reload the article on the site. The line is there, and with the path comments on you see `START ... (node/view/full.tpl)` for it.
6. To undo, tick the override on the list and press **Remove selected**: the rule and the file are deleted.

The editor writes `settings/siteaccess/<siteaccess>/override.ini.append.php` for you, orders overrides by drag and drop and keeps a copy of the file before it writes. See [Template editor](../features/6.0/template-editor-overrides.md).

### File path (what you keep in version control)

1. Create the template, in the override directory of your design:

   ```bash
   mkdir -p design/media/override/templates/full
   cp design/standard/templates/node/view/full.tpl design/media/override/templates/full/my_article.tpl
   ```

   Use the design name you found in part 2. If your design lives in an extension, create the files there instead.
2. Add a rule in `settings/siteaccess/eng/override.ini.append.php` (or in `settings/override/override.ini.append.php` for every siteaccess):

   ```ini
   <?php /* #?ini charset="utf-8"?

   [full_my_article]
   Source=node/view/full.tpl
   MatchFile=full/my_article.tpl
   Subdir=templates
   Match[object]=1234

   */ ?>
   ```

   `Source` is the template being replaced, `MatchFile` your file relative to `override/templates/`, `Subdir` is always `templates`, `Match[...]` the conditions (`class_identifier`, `node`, `object`, `section`, `parent_node`, `viewmode` and more; all must hold). Rules without a `Priority` come after rules with one; the lowest number is tried first.
3. A new template path needs the override and template caches cleared once:

   ```bash
   php bin/php/cache.php clear --id=template-override --allow-root-user
   php bin/php/cache.php clear --tag=template --allow-root-user
   php bin/php/cache.php clear --tag=content --allow-root-user
   ```

   Expected: each ends with `PASS`.
4. Reload the article; the path comment shows your file.

Later edits to the same file need no clear while the template cache is disabled; with it enabled run the `--tag=template` line again. Which of several matching overrides wins, and how an extension's overrides are ranked: [Template editor](../features/6.0/template-editor-overrides.md#reorder-by-drag-and-drop-september-2026) and [Extension loading order](../features/6.0/extension-loading-order.md). An extension can also override the templates of a module: [Extension module override](../features/6.0/extension-module-override.md).

## 5. Check the syntax before you look at the browser

```bash
php bin/php/eztemplatecheck.php -seng design/media/override/templates/full/my_article.tpl --allow-root-user
```

Expected: `Template file valid: design/media/override/templates/full/my_article.tpl`. A whole design or directory can be given instead of a file (`design/`). A syntax error is reported with the template and the line; the compiler's warnings on the page name the template and line too.

## 6. The template language in ten minutes

A template is HTML with `{ ... }` expressions. Comments are `{* ... *}`.

| You write | Meaning |
|---|---|
| `{$node.name\|wash}` | Print a variable. `wash` escapes HTML; use it on anything a user typed. |
| `{def $items = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 5 ) )}` | Declare a variable (`{def}` once per template; `{set $items = ...}` changes it later; `{undef $items}` frees it). `fetch` reads data, `hash` builds an array of named values. |
| `{foreach $items as $child}` ... `{/foreach}` | Loop. `{delimiter}` and `{sequence}` help with separators and zebra rows. |
| `{if $node.depth\|gt( 2 )}` ... `{else}` ... `{/if}` | Condition. Operators are written as pipes: `gt`, `lt`, `eq`, `and`, `or`, `not`. |
| `{cond( $a, 'yes', 'no' )}` | Inline condition. |
| `<a href={$child.url_alias\|ezurl}>` | Build a link that respects the siteaccess. `ezurl` is written without quotes in an attribute. |
| `<img src={'logo.png'\|ezimage}>` | Find an image in the design's `images/`. |
| `{'Search'\|i18n( 'design/media/pagelayout' )}` | A translated string. |
| `{attribute_view_gui attribute=$node.data_map.title}` | Show a content attribute with its datatype template. |
| `{node_view_gui content_node=$child view='line'}` | Show a node with its `node/view/line.tpl`, and the override for it. |
| `{include uri='design:parts/header.tpl' title=$node.name}` | Include another template; `design:` names a path inside the design. |

A complete, small template to try in the override from part 4 (it lists the children of the article's parent, ten at most):

```
<h1>{$node.name|wash}</h1>
{def $siblings = fetch( 'content', 'list', hash( 'parent_node_id', $node.parent_node_id, 'limit', 10 ) )}
<ul>
{foreach $siblings as $sibling}
    <li><a href={$sibling.url_alias|ezurl}>{$sibling.name|wash}</a></li>
{/foreach}
</ul>
{undef $siblings}
```

Expected: the heading followed by a list of links. Reference lists of every operator: [String template operators](../features/6.0/string-template-operators.md), [Role and policy template operators](../features/6.0/role-and-policy-template-operators.md).

## 7. Debugging templates

When a page is wrong, work down this list:

| Question | Do this |
|---|---|
| Which file wrote it? | Template path comments (part 3). |
| Why this file and not mine? | Check the rule: `Source`, `MatchFile` and `Match[...]`, then clear `template-override` and `template`. `./console exp:ini where override/<block>/MatchFile eng --allow-root-user` lists the files that define your block. |
| Is the syntax right? | `eztemplatecheck.php` (part 5). |
| Which templates, how long, how many queries? | The [Exp Debug bar](../features/6.0/exp-debug-bar.md): tabs Templates, Timing, SQL. Debug output itself is switched on for yourself with `[DebugSettings] DebugOutput=enabled` and `DebugByUser`/`DebugByIP`; see [Debug output improvements](../features/6.0/debug-output-improvements.md). |
| The page does not change after an edit | Clear by what you changed: [Operating a site, part 2](operating-a-site.md). A changed stylesheet also needs the template-block cache cleared; see that guide. |
| Stale pages for a while | Content view cache: `php bin/php/cache.php clear --tag=content --allow-root-user`. |

## 8. Switch the administration interface to admin4

`admin4` is a complete admin design with a light and a dark mode that does not need `admin3`, `admin2` or `admin` to be present. Set it for the admin siteaccess only:

```bash
./console exp:ini set site/DesignSettings/SiteDesign admin4 siteaccess:admin --allow-root-user
php bin/php/cache.php clear --all --allow-root-user
```

Expected: both end with `PASS`; reload the administration interface and the header carries a light/dark toggle after the search box. To go back, set `SiteDesign` to `admin3` and clear again. To try it first without changing anything, open the siteaccess `admintest_admin4`. Everything about it, including how to write your own styles on top (`design/admin4/stylesheets/admin4.css`, no cascade layers): [The admin4 design](../features/6.0/admin4-design.md); the earlier responsive design: [admin3](../features/6.0/admin3-responsive-admin.md).

## Where to go next

- [Content model and editing](content-model-and-editing.md): the classes and attributes your templates show.
- [Operating a site](operating-a-site.md): caches, cronjobs and repairs.
- [Deploying](deploying.md): move your design to a server.
- Feature pages: [Template editor](../features/6.0/template-editor-overrides.md), [Template path comments](../features/6.0/template-path-comments.md), [Icon themes in extensions](../features/6.0/icon-themes-in-extensions.md), [Hidden admin tabs](../features/6.0/hidden-admin-tabs.md).
- Upgrade notes: [Debug bar](../bc/6.0/debug-bar.md), [Pagination settings](../bc/6.0/pagination-settings.md).
