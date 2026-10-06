# Classic menus: Setup > Menus (classic), menu.ini and menu templates

This guide explains the classic menu settings of Exponential: what the page **Setup > Menus (classic)**
(`/visual/menuconfig`) does, which sites it matters for and which it does not, what each of its four arrangements
shows a visitor, the recipes most sites need, and how to draw classic menus in templates of your own. It ends with a
short history and the usual problems.

It is written for site builders who still run a site on one of the classic designs, and for administrators who meet
the page and wonder whether to touch it. Every rule and file below was checked against the code of this repository
on 6 October 2026, and every template example was rendered against the content of a real installation.

[Guides](README.md) · Related: [Templates and design](templates-and-design.md) ·
[Exponential Layouts](../bc/6.0/LAYOUTS.md)

## In short

- The classic designs (`design/base`, `ezwebin`, and the demo and simple designs built on them) draw a **top menu**
  and a **left menu** from three settings in `menu.ini [SelectedMenu]`: `CurrentMenu`, `TopMenu` and `LeftMenu`.
  The page Setup > Menus (classic) picks one of four arrangements per siteaccess and writes those three lines.
- **Sites built with Exponential Layouts do not read them.** Their menus are blocks in a layout, set in the layout
  editor (Layouts tab, `/explayouts_ui/dashboard`). The administration interface does not read them either.
- The page finds out by itself which siteaccess uses them: it reads the page layout each siteaccess resolves to.
  Siteaccesses where the settings do nothing are folded away with the reason, and saving for one of them needs a
  tick that says you know it changes no page.
- **Saving writes only the three lines**, into `settings/siteaccess/<siteaccess>/menu.ini.append.php`, keeps a copy
  of the file as it was in `var/backup/ini`, and clears the INI cache, the compiled page layouts and the template
  blocks. No restart of PHP-FPM or Velocity is needed.

## 1. When to use this page, and when Layouts

| Your siteaccess | Its menus come from | Use |
|---|---|---|
| A classic design (`base`, `ezwebin`, a design of yours that includes `{menu name=TopMenu}` or `$pagedata.top_menu`) | `menu.ini [SelectedMenu]` and `[MenuContentSettings]` | this page, or the files below |
| Exponential Layouts (its page layout calls `fetch( 'explayouts', 'resolve_layout' )`) | a menu block in a layout zone | the layout editor |
| An administration design (`admin`, `admin2`, `admin3`, `admin4`, `admin4l`, `adminui`, the editor) | `menu.ini [TopAdminMenu]` and the `Leftmenu_*` sections | not this page |

On the page, every siteaccess of `site.ini [SiteAccessSettings] RelatedSiteAccessList` gets one of these labels:

- **Uses these settings**: its page layout, or a template the page layout includes, reads them.
- **Uses these settings, with Layouts**: it reads them and also renders Layouts.
- **Not used: renders through Layouts**.
- **Administration siteaccess**.
- **Not used by its design**: its page layout draws no classic menu.
- **No page layout found**.

The page opens on the first siteaccess that uses the settings. When none does, it says so at the top: nothing needs
to be done there.

## 2. The four arrangements

`menu.ini [MenuSettings] AvailableMenuArray` lists them; each has a group with its title and its two templates.
Saving one writes these lines (exactly what the page shows under "Template, content and the lines it writes"):

| Arrangement | Lines written | Top | Left |
|---|---|---|---|
| Only top menu | `CurrentMenu=TopOnly` `TopMenu=flat_top` `LeftMenu=` | one row: the pages directly below the start page | none |
| Left menu | `CurrentMenu=LeftOnly` `TopMenu=` `LeftMenu=flat_left` | none | the pages directly below the start page, opened further below the page the visitor is in |
| Double top menu | `CurrentMenu=DoubleTop` `TopMenu=double_top` `LeftMenu=` | two rows: the pages below the start page and, under them, the pages of the first-level page the visitor is in | none |
| Left and top | `CurrentMenu=LeftTop` `TopMenu=flat_top` `LeftMenu=sub_left` | one row, as above | the pages of the first-level page the visitor is in, one level |

What the templates fetch, read from `design/base/templates/menu/` and `extension/ezwebin/design/ezwebin/templates/menu/`:

- **Classes.** The top menu and both rows of the double top menu list only objects of the classes in
  `menu.ini [MenuContentSettings] TopIdentifierList`; the left menus use `LeftIdentifierList`.
- **Order.** Each list is sorted the way its parent page sorts its children.
- **Hidden pages** are left out, as for every visitor, as long as `site.ini [SiteAccessSettings] ShowHiddenNodes`
  is `false` (the default).
- **The start page.** ezwebin's templates start from `content.ini [NodeSettings] RootNode` (and
  `site.ini [SiteSettings] RootNodeDepth` for sub sites). The `design/base` templates `flat_top` and `double_top`
  always start from node 2, the top of the content tree. On a site whose start page is not node 2 they list the
  wrong pages; use ezwebin's templates or your own (section 5).
- **Which file draws it** depends on the siteaccess's designs: the page names the file each template resolves to.
  On a site with `ezwebin` before `base`, `flat_top`, `double_top` and `flat_left` come from ezwebin and `sub_left`
  from `design/base`.
- **ezwebin hides the left menu** on pages of the classes in `menu.ini [MenuSettings] HideLeftMenuClasses`
  (`frontpage`, `blog`, `blog_post` by default).

Each arrangement on the page has a small preview built from the installation's own pages: the start page, the top
menu pages, and the pages of the first top menu page that has pages of its own, as a visitor there would see them.

## 3. Recipes

Each recipe gives the file, the exact lines, and what a visitor sees. Put them in the siteaccess you mean; the page
shows the same recipes with that siteaccess's name filled in. None of them does anything for a siteaccess that
renders through Exponential Layouts: there a menu is a block in the layout editor.

### Top menu only

`settings/siteaccess/<siteaccess>/menu.ini.append.php`

```ini
[SelectedMenu]
CurrentMenu=TopOnly
TopMenu=flat_top
LeftMenu=
```

A visitor sees one row of links under the header, the pages below the start page, and no left column.

### Top menu plus a left menu for the current section

```ini
[SelectedMenu]
CurrentMenu=LeftTop
TopMenu=flat_top
LeftMenu=sub_left
```

A visitor sees the same row; inside a first-level page (say "Showcase") a left column lists that page's pages. On
the start page there is no left column.

### Limit the menus to folders and landing pages

```ini
[MenuContentSettings]
TopIdentifierList[]
TopIdentifierList[]=folder
TopIdentifierList[]=frontpage
LeftIdentifierList[]
LeftIdentifierList[]=folder
LeftIdentifierList[]=frontpage
```

The empty `TopIdentifierList[]` line clears the list read from the files before (`settings/menu.ini`, ezwebin's
`menu.ini.append.php`), so only the classes that follow are menu items. `frontpage` is ezwebin's landing page class.
A visitor sees only folders and landing pages in the menus; articles, links and other pages stay reachable but are
no longer menu items. This applies to every arrangement.

### Hide one page from the menus

Hide the page in the content structure (its context menu: **Hide / unhide**). The menu templates fetch pages as
visitors see them, so a hidden page is left out of every classic menu as long as this stays as it is by default:

`settings/siteaccess/<siteaccess>/site.ini.append.php`

```ini
[SiteAccessSettings]
ShowHiddenNodes=false
```

A visitor no longer sees the page, in the menus or anywhere else; editors still see it in the administration. To
keep the page on the site but out of the menus, give it a class that is not in the lists of the recipe above.

### A different menu per siteaccess

Every siteaccess reads its own `settings/siteaccess/<name>/menu.ini.append.php`. Save on the page once per
siteaccess, or put the lines in the two files:

`settings/siteaccess/mysite_en/menu.ini.append.php`

```ini
[SelectedMenu]
CurrentMenu=TopOnly
TopMenu=flat_top
LeftMenu=
```

`settings/siteaccess/mysite_de/menu.ini.append.php`

```ini
[SelectedMenu]
CurrentMenu=LeftTop
TopMenu=flat_top
LeftMenu=sub_left
```

Visitors of `mysite_en` get a top menu only, visitors of `mysite_de` a top menu and a left column.

A value in `settings/override/menu.ini.append.php` is read after the siteaccess files and wins over all of them. The
page warns when that is the case: saving there would change nothing until the override is removed.

## 4. Which designs draw classic menus

A menu appears only where a page layout includes it, so the settings do nothing on their own. In this repository:

| Design | Classic menus |
|---|---|
| `design/base` | its page layout draws them, with `{menu name=TopMenu}` and `{menu name=LeftMenu}`; templates `double_top`, `flat_left`, `flat_top`, `sub_left` |
| `extension/ezwebin/design/ezwebin` | its page layout draws them, through `$pagedata.top_menu` and `$pagedata.left_menu` (`page_topmenu.tpl`, `page_leftmenu.tpl`); templates `double_top`, `flat_left`, `flat_top` |
| `design/simple`, `extension/sevenx_themes_simple/design/simple` | has menu templates (`double_top`, `dropdown`, `flat_top`), but the top and side menu areas of its page layout are commented out: the settings do nothing until a page layout of yours includes a menu |
| `extension/sevenx_themes_media/design/media`, `extension/explayouts/design/standard` | render through Exponential Layouts; menus are layout blocks |
| `design/standard`, `design/plain`, `design/mysite`, the portal designs of `expservices` | no menu templates, and their page layouts draw no classic menu |

The page lists the designs of the installation it runs in, found the same way.

## 5. Use in your templates

Each example below is the same text the page shows with a Copy button. Each was rendered through the template engine
against a real installation's content, as the anonymous visitor: no template errors or warnings, and the links of the
real pages it must list.

### Draw the chosen menus from a page layout

Put it where the menus go in your `pagelayout.tpl`. `{menu name=TopMenu}` includes `design:menu/<TopMenu>.tpl` and is
resolved when the page layout is compiled, which is why saving on the page deletes the compiled page layouts. The
cache-block keys are those of `design/base`: the address, and the roles and limitations of the user, so every visitor
gets the selected item of the page and only what their rights allow. The siteaccess is part of every cache-block key
by itself, and the block expires whenever content is published.

```smarty
{* In your pagelayout.tpl: draw the menus menu.ini [SelectedMenu] names.
   {menu name=TopMenu} includes design:menu/<TopMenu>.tpl, chosen when the page layout is compiled.
   ezwebin's menu templates also read $pagedata and $current_node_id; with that design, define them
   first, as its page layout does:
   {def $pagedata = ezpagedata() $current_node_id = $pagedata.node_id} *}
{def $menu_user = fetch( 'user', 'current_user' )}
{cache-block keys=array( $uri_string, $menu_user.role_id_list|implode( ',' ), $menu_user.limited_assignment_value_list|implode( ',' ) )}
<nav class="topmenu" aria-label="Main">
    {menu name=TopMenu}
</nav>
{if ezini( 'SelectedMenu', 'LeftMenu', 'menu.ini' )}
<nav class="leftmenu" aria-label="Section">
    {menu name=LeftMenu}
</nav>
{/if}
{/cache-block}
{undef $menu_user}
```

### A top menu template of your own

The visible children of the start page (`content.ini [NodeSettings] RootNode`) of the `TopIdentifierList` classes,
with the item the visitor is in marked. Unlike the `design/base` templates it follows `RootNode`.

```smarty
{* design:menu/my_top.tpl: the pages below the start page, of the classes in
   menu.ini [MenuContentSettings] TopIdentifierList, in the start page's sort order.
   fetch() leaves hidden pages out for visitors (ShowHiddenNodes=false). *}
{def $root_id = ezini( 'NodeSettings', 'RootNode', 'content.ini' )
     $root = fetch( 'content', 'node', hash( 'node_id', $root_id ) )
     $items = fetch( 'content', 'list', hash( 'parent_node_id', $root_id,
                                              'sort_by', $root.sort_array,
                                              'class_filter_type', 'include',
                                              'class_filter_array', ezini( 'MenuContentSettings', 'TopIdentifierList', 'menu.ini' ) ) )
     $in_path = first_set( $module_result.path[1].node_id, 0 )
     $here = first_set( $module_result.node_id, 0 )}
{if $items}
<ul class="menu-top">
{foreach $items as $item}
    <li{if eq( $item.node_id, $in_path )} class="selected"{/if}><a href={$item.url_alias|ezurl}{if eq( $item.node_id, $here )} aria-current="page"{/if}>{$item.name|wash}</a></li>
{/foreach}
</ul>
{/if}
{undef $root_id $root $items $in_path $here}
```

### A left menu for the current section

The pages of the first-level page the visitor is in, of the `LeftIdentifierList` classes, with the current page
marked; nothing on the start page.

```smarty
{* design:menu/my_section_left.tpl: the pages of the first-level page the visitor is in
   ($module_result.path[1]; ezwebin's $pagedata.path_array holds the same path), of the
   classes in menu.ini [MenuContentSettings] LeftIdentifierList. Nothing on the start page. *}
{def $section_id = first_set( $module_result.path[1].node_id, 0 )}
{if $section_id}
{def $section = fetch( 'content', 'node', hash( 'node_id', $section_id ) )
     $items = fetch( 'content', 'list', hash( 'parent_node_id', $section_id,
                                              'sort_by', $section.sort_array,
                                              'class_filter_type', 'include',
                                              'class_filter_array', ezini( 'MenuContentSettings', 'LeftIdentifierList', 'menu.ini' ) ) )
     $here = first_set( $module_result.node_id, 0 )}
<h2><a href={$section.url_alias|ezurl}>{$section.name|wash}</a></h2>
{if $items}
<ul class="menu-left">
{foreach $items as $item}
    <li><a href={$item.url_alias|ezurl}{if eq( $item.node_id, $here )} aria-current="page"{/if}>{$item.name|wash}</a></li>
{/foreach}
</ul>
{/if}
{undef $section $items $here}
{/if}
{undef $section_id}
```

### Where the files go

Never edit a shipped design: an update replaces it. A design extension of your own is found before the shipped
designs, and the extra `[MenuSettings]` entry offers your templates as a fifth arrangement on the page.

```ini
# Your own menu templates belong in a design extension, never in a shipped design.
#
# extension/mysite_menus/settings/design.ini.append.php
[ExtensionSettings]
DesignExtensions[]=mysite_menus

# extension/mysite_menus/design/mysite/templates/menu/my_top.tpl
# extension/mysite_menus/design/mysite/templates/menu/my_section_left.tpl
#   ("mysite" is a design in your siteaccess's SiteDesign or AdditionalSiteDesignList)

# settings/siteaccess/<siteaccess>/menu.ini.append.php
# a fifth arrangement, offered on Setup > Menus (classic) from then on
[MenuSettings]
AvailableMenuArray[]=MyTopAndSection

[MyTopAndSection]
TitleText=My top and section menus
TopMenu=my_top
LeftMenu=my_section_left
```

After adding the files, clear the template override cache and the INI cache (`php bin/php/ezcache.php
--clear-id=template-override --allow-root-user` and `--clear-tag=ini`), and run `exp:velocity cache clear` when
Velocity serves the site.

### With Exponential Layouts

A siteaccess that renders through Layouts draws its menus with blocks in a layout zone, so none of these templates
apply there. Classic and Layouts siteaccesses can live side by side in one installation, each with its own design.

## 6. What saving does

1. Takes the arrangement only if it is one of `AvailableMenuArray`; anything else posted is refused with a message.
2. Checks that the siteaccess is one of `RelatedSiteAccessList`, that its name is letters, digits, `_` and `-`, and
   that `settings/siteaccess/<name>` exists and, with links resolved, lies inside `settings/siteaccess`.
3. Writes the three lines of `[SelectedMenu]` through the INI editor of `exp:ini`: only the touched lines change,
   comments, other sections and the `<?php /* ... */ ?>` wrapper stay, a missing file is created with the wrapper,
   the old file is copied to `var/backup/ini`, the owner and mode are kept, and the change is recorded in the audit
   (`system.setting.write`).
4. Reads the file back; if it does not hold the three values or the wrapper is broken, puts the old file back.
5. Clears the INI caches (`global_ini`, `ini`), the compiled page layouts of the siteaccess's cache directory and the
   template blocks. Every request reads the settings again, under Apache and Velocity alike, so nothing needs a
   restart; pages kept in a response cache show the old menus until that cache is cleared.

Before 6.0.15 the page saved every `menu.ini` setting the siteaccess read into that file, which froze all the other
menu settings (the administration tabs among them) for the siteaccess. It now writes the three lines only.

The form keeps its old field names (`CurrentSiteAccess`, `SelectCurrentSiteAccessButton`, `MenuType`, `StoreButton`)
and works without javascript. `/visual/menuconfig/(siteaccess)/<name>` opens the page for one siteaccess. The page
needs the rights of the `visual` module, and the editor siteaccess refuses that module as before.

## 7. A short history

The classic designs build every page from one page layout, and that page layout asks `menu.ini` which menus to draw.
This page has chosen that since the first versions: four arrangements of a top and a left menu, saved per siteaccess.

Exponential Layouts replaced that for new sites: a layout holds zones and blocks, a menu is one of the blocks, and a
rule picks the layout for each page. A siteaccess built that way never reads these settings. The page stays for sites
that still use a classic design, and since 6.0.15 it says for each siteaccess whether it matters there.

## 8. When something does not behave

| You see | Why | What to do |
|---|---|---|
| Saving says it worked, the site is unchanged | the siteaccess renders through Layouts, or its design draws no classic menu | read the label of the siteaccess; use the layout editor |
| The page warns that `settings/override` decides the value | `settings/override/menu.ini.append.php` sets `[SelectedMenu]` | remove the lines there |
| The top menu lists the wrong pages | a `design/base` template on a site whose start page is not node 2 | use ezwebin's templates or the example in section 5 |
| A page is missing from the menu | hidden, or its class is not in `TopIdentifierList` / `LeftIdentifierList` | unhide it or add its class |
| "The web server cannot write" | the file or directory is not writable for the PHP-FPM user | fix the owner, or put the lines in the file by hand |

## References

- Page: `kernel/visual/menuconfig.php`, `kernel/private/classes/views/visual/menuconfig.php`,
  `design/admin/templates/visual/menuconfig.tpl` and `design/admin4/templates/visual/menuconfig.tpl`
- Detection: `kernel/classes/classicmenu/expclassicmenusiteaccessinspector.php`
- Reading and writing: `kernel/classes/classicmenu/expclassicmenusettings.php`; template examples in
  `kernel/classes/classicmenu/examples/`
- Tests: `tests/tests/kernel/classes/classicmenu/expClassicMenuSettingsTest.php`
- Settings: `settings/menu.ini` (`[MenuSettings]`, `[SelectedMenu]`, the four arrangement groups,
  `[MenuContentSettings]`), `extension/ezwebin/settings/menu.ini.append.php`
- Templates: `design/base/templates/pagelayout.tpl`, `design/base/templates/menu/*.tpl`,
  `extension/ezwebin/design/ezwebin/templates/menu/*.tpl`, `lib/eztemplate/classes/eztemplatemenufunction.php`,
  `extension/ezwebin/autoloads/ezpagedata.php`
