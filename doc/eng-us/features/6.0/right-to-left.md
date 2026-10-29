# Right-to-left languages in the administration

Since 6.0.15 (#521). Builds on the [interface language](ui-switch-languages.md) choice.

When the interface language is one written right to left (Hebrew, Arabic, Persian, Urdu and the other locales listed
in [doc/eng-us/languages.md](../../languages.md), section "Right-to-left languages"), the administration turns around: the
page says `dir="rtl"` on `<html>`, the side columns change places, text starts at the right and the icons that point
along the line point the other way. A left-to-right page is exactly what it was before: the same markup byte for byte
and, as checked by screenshots, the same pixels.

Content written in a right-to-left *content* language is marked the same way where it is shown and edited, whatever
the interface language is (see "Content" below).

## Where the direction comes from

| Piece | What it does |
|---|---|
| `share/locale/<locale>.ini`, `[LanguageSettings] TextDirection=rtl` | The only source. A locale file without the key, or with any other value, is left to right. 43 locale files carry it. |
| `eZLocale::textDirection()` | `'rtl'` or `'ltr'` for a locale object. |
| `eZLocale::isRightToLeft()` | `true` for a right-to-left locale. |
| `eZLocale::textDirectionOf( 'heb-IL' )` | Static: the direction of a locale code, `'ltr'` for an unknown code. |
| `eZLocale::normalizeTextDirection( $value )` | Static: `rtl` / `right-to-left` (any case) become `'rtl'`, everything else `'ltr'`. |
| Template attributes `text_direction`, `is_right_to_left` | The same on a locale in a template, for example `$code\|locale().is_right_to_left`. |
| `$site.text_direction` | The direction of the current interface locale, set by the kernel for every page (`ezpKernelWeb`). |

## The page frame

Every administration pagelayout prints the attribute only for a right-to-left interface language:

```
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if} ...>
```

- `design/admin4/templates/pagelayout.tpl`, `design/admin4/templates/loginpagelayout.tpl`
- `design/admin4l/templates/pagelayout.tpl`
- `design/admin/templates/pagelayout.tpl`, `design/admin/templates/loginpagelayout.tpl` (the classic design)
- `extension/exp_adminui/design/adminui/templates/pagelayout.tpl` and `loginpagelayout.tpl` (AdminUI)

`first_set()` keeps a page rendered by an older kernel (no `text_direction` yet) left to right.

## How the skeleton follows the direction

The rules for the stylesheets of every administration design (they add to the
[admin design and template styling rules](admin4-design.md)):

1. **Logical properties for the layout.** `margin-inline-start` / `-end`, `padding-inline-*`, `border-inline-*`,
   `inset-inline-start` / `-end`, the logical corner radii (`border-start-start-radius` ...) and
   `text-align: start` / `end` instead of their left and right forms. Left to right they are the same thing, so the
   page does not change; right to left they swap by themselves. Converted: `components.css` (admin4 and its required
   identical copy in design/admin), `admin4.css`, `pagelayout.css` and `core.css` of admin4 and design/admin, and the
   module stylesheets `stylesheets/modules/{content,people,setup,structure,workflow}.css` (both identical copies).
2. **A mirror rule where a logical property cannot say it.** Four-value shorthands with different left and right
   values, `float` / `clear`, `background-position`, horizontal `box-shadow` offsets and asymmetric radii get a
   rule right after the rule they mirror:

   ```css
   .x { margin: 0 4px 0 12px; }
   :where([dir="rtl"]) .x { margin: 0 12px 0 4px; }
   ```

   `:where()` adds no specificity, so right to left the mirror wins and loses exactly where the original does. No
   left-to-right page matches it.
3. **Icons that point along the line are mirrored**, nothing else: `components.css` mirrors the `.a4-bi-*` chevrons,
   arrows, carets, reply/forward and sign-out icons with `scale: -1 1` (which composes with a component's own
   `rotate()`); the icons the stylesheets draw themselves (the content tree's caret, the popup submenu caret, the
   dashboard figures' arrow, the side menu groups' caret, the data table pagers' IcoMoon arrows in `core.css`) have their own mirror rule. A shape drawn with turned
   borders that points *down* keeps its physical borders (the interface switch's chevron, marked "the same in both
   directions").
4. **Content alignment chosen by an editor is not mirrored.** `text-left`, `object-left`, `align-right` and the like
   mean the same side in every language.
5. **Scripts that measure or move things use the direction.** The admin4 tab row's "More" overflow
   (`admin4.js`), the left column's drag and keyboard resizing (`ezadmin_menubar.js`), the classic design's column
   width control (`leftmenu_widthcontrol.js`) and its show/hide animations (`page_leftmenu.tpl`, `pagelayout.tpl`)
   use logical properties (`marginInlineStart`, `insetInlineEnd`) or check
   `getComputedStyle( document.documentElement ).direction`.
6. **AdminUI** keeps the reference's `style.css` unchanged. For a right-to-left language `page_head_style.tpl` and
   `loginpagelayout.tpl` link `stylesheets/style-rtl.css` in its place: a copy of `style.css` built by a script, with
   its left/right declarations written as logical properties and a `:where([dir="rtl"])` mirror rule after every rule
   a logical property cannot express (rules 1 and 2; content alignment keeps its side, rule 4). Rebuild it whenever
   `style.css` changes, never edit it by hand. AdminUI's own stylesheets (`pagelayout.css`, `core.css`,
   `content.css`, `adminui*.css`, `setup-extensions.css`, `theme/*.css`) use logical properties, so the frame and
   the pages inside it (tables, forms, paging, dashboard blocks) mirror themselves. `stylesheets/adminui-rtl.css`,
   linked last and only right to left, turns the Font Awesome and IcoMoon icons that point along the line (arrows,
   chevrons, carets, sign-in/out, indent/dedent, the pagers and the edit page's side column toggle) and places the
   content tree column's resize handle; `ngadminui/app.js` gives that column jQuery UI's west handle right to left
   (dragging to the left widens it). A left-to-right AdminUI page loads exactly what it loaded before.

New CSS in an administration design must follow 1 to 4; the design tests (`tests/tests/design`) read the logical
property names.

## Content

Independent of the interface language, text in a right-to-left content language carries `lang` and `dir="rtl"`
(listed here for completeness):

- `design/standard/templates/parts/exp_content_direction.tpl`: prints ` lang="he-IL" dir="rtl"` for a right-to-left
  content locale, nothing for a left-to-right one. Guide: [doc/eng-us/guides/content-languages.md](../../guides/content-languages.md).
- The edit forms: `content/edit_attribute.tpl` of design/standard, design/admin, admin4 and AdminUI.
- The administration's full view and preview: `node/view/admin_preview.tpl`, `node/view/full.tpl` (classic,
  AdminUI), `node/view/a4_full_body.tpl` (admin4), `preview.tpl` of every administration design.
- The online editor: TinyMCE 3 (`ezxmltext_ezoe.tpl`) and TinyMCE 8 (`ezxmltext_ezoe_tinymce8.tpl`) switch their
  `directionality` to `rtl` for a right-to-left text; the tiptap editor (`exp_oe_tiptap`) sets `dir="rtl"` on the
  editing area through its `contentDirection` option.

## Names inside the interface text

A name an editor typed (a node or object name, a user's name or login, a class or section name) is its own direction
run where the administration inserts it into its text, so an English name in a Hebrew page keeps its end punctuation
and brackets at its end ("Hello, world (test)!", not "!Hello, world (test)"). A Hebrew name in an English page is
isolated the same way.

- `dir="auto"` on an inline element that holds only the name (a link in a list, a breadcrumb step); `<bdi>` inside an
  element that is a block or a flex item (a heading, a table cell, the user menu's name), so the element keeps the
  page's alignment; `<bdi>` around the name inserted into a translated sentence ("by %name", "Below %location",
  "Versions of “%name”"), the sentence washed before or the name washed on its own.
- Shared views: `node/view/text_linked.tpl`, `content/view/text_linked.tpl`, `content/version/view/text_linked.tpl`
  (relation lists and many tables), `node/view/line.tpl` (admin4, classic: search results and the other line lists),
  the dashboard blocks of design/standard, `content/bookmark_tree.tpl`, `parts/exp_content_direction.tpl`.
- The frame: the breadcrumb (`page_toppath.tpl`), the page heading and the "Modified ... by" line of the full view, the
  user menu, the phone user line and the "Current user" box, the dashboard greeting; the lists: sub items, bookmarks,
  drafts, trash, history, pending items, details, object information.
- Scripts: the sub items table (`ezajaxsubitems_expdatatable.js`: the name link, the class, modifier and section
  cells) and the content tree (`contentstructuremenu_dynamic.js`: each name, in a right-to-left page only, because an
  isolated name is not hyphenated and the tree hyphenates long names left to right).
- The page title cannot hold markup: right to left, `page_head.tpl` puts each step of the title between U+2068 FIRST
  STRONG ISOLATE and U+2069 POP DIRECTIONAL ISOLATE (written as character references); a left-to-right title is
  unchanged. AdminUI's title is a fixed text and needs nothing.
- The admin preview of content in a left-to-right language inside a right-to-left page is marked `dir="ltr"`
  (`exp_content_direction.tpl` with `exp_dir_ltr=true()`, used by `admin_preview.tpl` and `preview.tpl`), so English
  content reads as English; a left-to-right page prints nothing new. The parameter reads the interface locale from
  site.ini `[RegionalSettings] Locale`, because `$site` is not set for a module's templates.

Left to right the pages look as before (screenshots before and after compared pixel by pixel: only sub-pixel glyph
positions at the edge of an isolated name moved).

## How to test

1. Sign in with a test administrator (never the shared `admin` account: its preferences are someone's working
   state).
2. In the right toolbar (AdminUI: the side bar's Interface menu) choose **Interface language: עברית (ישראל)**
   (Hebrew, `heb-IL`) and apply.
3. Check: `<html dir="rtl" lang="he-IL">`; the left column (content tree) at the right, the toolbar at the left; the
   header, tabs, tables and forms read from the right; carets and chevrons point left; no horizontal scroll bar.
   Check light and dark, at a wide window and at 200% zoom (about 960 px).
4. Spot check Arabic (`ara-SA`): `<html dir="rtl" lang="ar-SA">`.
5. Choose English again: the page has no `dir` attribute and looks as before.

Checked for 6.0.15 in admin4, admin4l, the classic design and AdminUI on the dashboard, a folder, an edit form and
the class list, light and dark, 1440 px and 960 px at scale 2: no horizontal overflow, no script errors, and no
accessibility finding that the left-to-right page does not have as well. Left to right, screenshots with the old
and the new stylesheets on the same pages were compared pixel by pixel (the only differences left were within
rendering noise of the live site).

## Follow-ups

- **Extension stylesheets** (extensions' `BackendCSSFileList`) still use physical left/right in places: expai
  (`expai.css`), expui (`exp/datatable.css`: the pager groups float left/right, mirrored for AdminUI in
  `adminui-rtl.css`; `dialog.css`, `datepicker.css`, `core.css`), eztags (`jstree/eztags/style.css`,
  `tagssuggest.css`, `contentstructure-tree.css`, `jqmodal.css`), ezautosave (`preview.css`, `autosave.css`),
  expqueue, ezfind (`ezajax_autocomplete.css`), sevenx_alpha_settings (`a4-showcase.css`). Convert them to logical
  properties with their next release.
- **The classic design's side columns** clip their content at the inner edge, as they do left to right; nothing new,
  but more visible with long Hebrew or Arabic labels.
- **Login page in a right-to-left language**: the interface language is a choice of a signed-in user, so the login
  page follows only the siteaccess's own locale. It is direction-safe (checked with `dir="rtl"` set by hand), but a
  right-to-left login page needs a siteaccess whose locale is right to left.
- **Bidirectional text**: names are isolated (see "Names inside the interface text"), in the pages listed there.
  Other module pages (setup, roles, classes, the edit forms' side menus) and untranslated English interface text
  inside a right-to-left page still follow the plain Unicode bidirectional algorithm; the cure for the latter is the
  translation.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
