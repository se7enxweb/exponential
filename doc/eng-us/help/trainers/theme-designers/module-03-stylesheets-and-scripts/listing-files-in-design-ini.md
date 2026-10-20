---
title: "Lesson 3.1: Listing stylesheets and scripts in design.ini"
audience: trainer, designer, developer
category: training
order: 31
summary: Learners add a stylesheet and a script to their theme through design.ini (FrontendCSSFileList, FrontendJavaScriptList), load them with ezcss_load and ezscript_load, and ask for a file from one template with ezcss_require. 45 minutes.
keywords: theme design, stylesheet, JavaScript, design.ini, CSSFileList, FrontendCSSFileList, BackendCSSFileList, JavaScriptList, FrontendJavaScriptList, ezcss_load, ezscript_load, ezcss_require, ezscript_require, ezjsc::jquery
type: help
---
# Lesson 3.1: Listing stylesheets and scripts in design.ini

In Exponential, stylesheets and scripts are not linked by hand in the pagelayout. They are named in lists in
`design.ini`, and the pagelayout asks the ezjscore packer to load the lists. A theme that follows this can be extended
by other extensions, packed into few files, and run under a Content Security Policy. This lesson sets up the lists of
`training_theme`.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 3: Stylesheets and scripts through the packer](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Designers and front-end developers |
| Prerequisites | [Module 2](../module-02-templates-overrides-and-the-pagelayout/README.md): `training_theme` with a skip link and a breadcrumb |

## Learning objectives

After this lesson, learners can:

- name the stylesheet and script lists of `design.ini` and which designs read which list;
- add a file to their theme's lists and place it in the design's `stylesheets/` or `javascript/` folder;
- load the lists in a pagelayout with `ezcss_load` and `ezscript_load`;
- ask for a file from a single template with `ezcss_require` or `ezscript_require`.

## Trainer's notes

### How to present it

Source: [Stylesheets and scripts for your site](../../../creators/website-creators/stylesheets-and-scripts.md).

1. **The lists** (5 minutes). `settings/design.ini`:

   | Setting | Holds |
   |---|---|
   | `[StylesheetSettings] FrontendCSSFileList[]` | stylesheets for public designs |
   | `[StylesheetSettings] BackendCSSFileList[]` | stylesheets for administration designs (extensions add theirs here) |
   | `[StylesheetSettings] CSSFileList[]` | the older list for both; marked deprecated in `design.ini` in favor of the two above |
   | `[JavaScriptSettings] FrontendJavaScriptList[]`, `BackendJavaScriptList[]`, `JavaScriptList[]` | the same for scripts |

   A file name is looked up in `stylesheets/` or `javascript/` along the design chain. `ezjsc::jquery` is ezjscore's
   own jQuery; a theme does not ship a copy.
2. **Loading them** (4 minutes). The wizard's `page_head.tpl` and `pagelayout.tpl` already do it:

   ```
   {ezcss_load( array( ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ),
                       ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) ) )}
   ...
   {ezscript_load( array( 'ezjsc::jquery',
                          ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' ),
                          ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
   ```

   Scripts are loaded at the end of the body, stylesheets in the head.
3. **One file for one template** (4 minutes). A view template that needs a file only on some pages calls
   `{ezcss_require( 'article.css' )}`. The view runs before the pagelayout, so the pagelayout's `ezcss_load` then
   includes the file. The four functions are defined in
   `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`.
4. **Why not a `<link>` by hand** (2 minutes). A hand-written link escapes the packer (one request per file, no
   modification time in the name) and cannot be extended or removed by settings.

### Common questions

**Should a theme still use `CSSFileList[]`?** The wizard writes it and it works; new themes and reworked heads use
`FrontendCSSFileList[]` and `FrontendJavaScriptList[]`. In this lesson learners add to the frontend lists.

**In which order are the files loaded?** In the order of the lists as the pagelayout passes them, and inside a list in
the order the settings files add entries (extensions in their loading order).

**Can another extension add a file to my theme's pages?** Yes, by adding to the same list in its own `design.ini`.
That is a reason to use the lists.

### Pitfalls

- **The file in the wrong folder.** `FrontendCSSFileList[]=training.css` looks in `stylesheets/`, not in the design
  root. A missing file leaves a warning in the debug output and is skipped.
- **Forgetting the INI cache.** A new list entry needs `--clear-tag=ini`; a new file also needs the packer and template
  caches (lesson 3.3).
- **A path with `stylesheets/` in the list.** Write the name relative to `stylesheets/`: `training.css`, or
  `parts/menu.css` for a subfolder.

## Hands-on exercise

On your training installation. `D` stands for `extension/training_theme/design/training_theme`.

1. Open `extension/training_theme/settings/design.ini.append.php` and read the `[StylesheetSettings]` and
   `[JavaScriptSettings]` blocks the wizard wrote.
2. Add, in `[StylesheetSettings]`:

   ```ini
   FrontendCSSFileList[]=training.css
   ```

3. Create `D/stylesheets/training.css` with a style for the skip link and the breadcrumb from lesson 2.2:

   ```css
   .skip-link { position: absolute; inset-inline-start: -999em; }
   .skip-link:focus { position: static; }
   .training_theme-path { margin-block: 1rem; font-size: 0.9rem; }
   ```

4. Create `D/stylesheets/article.css` with one visible rule, for example `.training_theme-article h1 { font-size: 2.5rem; }`.
5. In `D/override/templates/full/article.tpl`, add as the first line: `{ezcss_require( 'article.css' )}`.
6. Clear the caches: `php bin/php/ezcache.php --clear-tag=ini`, `php bin/php/ezcache.php --clear-id=ezjscore-packer`,
   `php bin/php/ezcache.php --clear-tag=template` and `php bin/php/ezcache.php --clear-tag=content`.
7. Open a folder page of `<site>`. In the page source, find the stylesheet `<link>` and open the file it names.
   Search it for `skip-link`.
8. Open an article page and open its stylesheet file the same way. Search it for `training_theme-article`.
9. Press Tab once on a page: the skip link becomes visible.

## Expected result

- Step 7: the page links one packed stylesheet whose content holds the rules of `training.css`.
- Step 8: on an article page the packed stylesheet also holds the rule of `article.css`; on a folder page it does not.
- Step 9: the skip link is hidden until it receives the focus.
- The trainer checks one learner's `design.ini.append.php` and the two packed files on the projector.

## Quiz

1. Which list does a public theme add its stylesheet to?
   A. `BackendCSSFileList[]` B. `FrontendCSSFileList[]` C. `AdminComponentCSSFileList[]` D. `CustomHosts[]`
2. In which folder of the design does `FrontendJavaScriptList[]=menu.js` look for the file?
3. Why does `ezcss_require( 'article.css' )` in a view template reach the pagelayout's `ezcss_load`?
4. Why should a theme not ship its own copy of jQuery?
5. Give two reasons against a hand-written `<link rel="stylesheet">` in the pagelayout.

<details>
<summary>Answers</summary>

1. **B.** The backend list is for administration designs
   ([Stylesheets and scripts for your site](../../../creators/website-creators/stylesheets-and-scripts.md)).
2. In `javascript/` of the designs along the chain.
3. The view template runs before the pagelayout and its required files are collected; `ezcss_load` then includes them.
4. ezjscore ships one (`ezjsc::jquery`), so every extension uses the same version.
5. Any two: it is not packed (one request per file), it carries no modification time in its name, other extensions and
   settings cannot change it, and it does not follow the design chain.

</details>

## Further reading

- [Stylesheets and scripts for your site](../../../creators/website-creators/stylesheets-and-scripts.md): the lists,
  the packer and the checklist.
- [jQuery 4 and the removal of YUI](../../../../features/6.0/jquery4-and-yui-removal.md): which jQuery ezjscore
  provides.
- [Design settings a theme ships](../../../designers/extensions/design-settings-a-theme-ships.md): which lists belong in
  the theme.

## Related pages

- [Module 3: Stylesheets and scripts through the packer](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
