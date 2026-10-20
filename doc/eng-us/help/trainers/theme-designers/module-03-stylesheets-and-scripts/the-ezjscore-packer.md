---
title: "Lesson 3.2: The ezjscore packer"
audience: trainer, designer, developer
category: training
order: 32
summary: Learners explain what the packer does at each level, switch it off and on for one siteaccess to compare the page source, find the packed files on disk and read their names. 45 minutes.
keywords: theme design, ezjscore, packer, Packer, pack level, DevelopmentMode, AppendLastModifiedTime, CustomHosts, CssOptimizer, JavaScriptOptimizer, packed file, cache/public, ezjscore.ini
type: help
---
# Lesson 3.2: The ezjscore packer

The packer turns the lists of lesson 3.1 into as few files as possible, minifies them and puts the modification time
into each file name. With it off, every file is linked on its own, which is easier to debug. Designers switch between
the two many times a day; this lesson makes them confident about which mode they are in and why.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 3: Stylesheets and scripts through the packer](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Designers and front-end developers |
| Prerequisites | [Lesson 3.1](listing-files-in-design-ini.md) |

## Learning objectives

After this lesson, learners can:

- describe what the packer does at levels 0, 1, 2 and 3;
- tell from the page source whether the packer is on;
- switch it off and on for one siteaccess with `[eZJSCore] Packer`;
- find a packed file on disk and read the modification time in its name.

## Trainer's notes

### How to present it

Sources: [Stylesheets and scripts for your site, the packer](../../../creators/website-creators/stylesheets-and-scripts.md#the-packer)
and `extension/ezjscore/settings/ezjscore.ini` (show the file on the projector).

1. **What it does** (5 minutes). The levels, as the code applies them:

   | Level | Result |
   |---|---|
   | 0 | no packing: each file is linked at its own address |
   | 1 | the files of one call are combined into one file |
   | 2 | combined and passed through the optimizers (`JavaScriptOptimizer[]`, `CssOptimizer[]`) |
   | 3 | as 2, with the stylesheet optimizer's further steps |

   Stylesheets default to level 3, scripts to level 2.
2. **Who decides** (4 minutes). `ezjscore.ini [eZJSCore] Packer` is commented out by default; then the packer follows
   `site.ini [TemplateSettings] DevelopmentMode`: `enabled` means level 0, `disabled` (the default) means packing.
   Set `Packer=disabled` (or `0`) to force it off, or a number `0` to `3` to force a level.
3. **The name** (3 minutes). `[Packer] AppendLastModifiedTime=enabled`: the newest modification time of the files is
   part of the packed file's name, like `<hash>_<time>_all.css`. A changed file gives a new name, so browsers fetch
   it although packed files are cached for a long time. The packed files live under the installation's cache
   directory, in `public/stylesheets/` and `public/javascript/`.
4. **Other hosts** (2 minutes). `[Packer] CustomHosts[.css]` and `[.js]` serve packed files from another host. Mention
   it; it is a hosting decision.

### Common questions

**Why does the page link the file twice with different names after an edit?** It does not; the old name is in a
cached page or a cache block. Lesson 3.3 clears the right caches.

**Should production run with `DevelopmentMode=enabled`?** No. It switches packing off and serves every file
separately. Keep it for development siteaccesses.

**Where do I see what was packed?** With debug output on, the debug report has a section "CSS/JS files loaded with
'ezjscPacker' during request" listing each packed file and its level.

### Pitfalls

- **Switching the packer for every siteaccess.** Use the siteaccess scope; the administration should stay packed.
- **Editing the packed file.** It is generated and replaced at the next change. Edit the source in the design.
- **Mixing up the two settings files.** `Packer` is in `ezjscore.ini`, `DevelopmentMode` in `site.ini`.

## Hands-on exercise

On your training installation, `<site>` drawn by `training_theme`.

1. Open a page of `<site>` and its page source. Write down the `<link rel="stylesheet">` and `<script>` elements: how
   many are there, and what do their file names look like?
2. Switch the packer off for your siteaccess only:

   ```bash
   php bin/php/console exp:ini set ezjscore.ini/eZJSCore/Packer disabled siteaccess:<site>
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-id=template-block
   ```

3. Reload the page source. Write down the stylesheet and script elements again and compare with step 1.
4. Open `training.css` through its link: it is your file exactly as written.
5. Switch the packer back on by removing the setting:

   ```bash
   php bin/php/console exp:ini rem ezjscore.ini/eZJSCore/Packer siteaccess:<site>
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-id=template-block
   ```

6. Reload the page source: you are back to the result of step 1.
7. Find the packed stylesheet on disk. Take its file name from the page source and search for it:
   `find var -name '<file name>'`.
8. Open the file in an editor: the rules of several stylesheets, minified, one after the other.
9. Read the number between the two underscores of the name. Run `stat -c %Y` on your `training.css` and on the other
   source stylesheets and compare.

## Expected result

- Step 1: one packed stylesheet link for the head list and one packed script for the body list, with long hashed
  names ending in `_all.css` and `.js`.
- Step 3: one link per source file, at the files' own addresses in the design folders.
- Step 9: the number in the name is the newest modification time among the source files of that bundle.
- The trainer checks that every learner ends with the packer back on (`exp:ini where ezjscore.ini/eZJSCore/Packer
  <site>` shows no siteaccess entry).

## Quiz

1. `Packer` is not set in `ezjscore.ini`. What decides whether files are packed?
2. What does level 0 mean?
   A. Packed and minified B. Combined but not minified C. Each file linked on its own D. Files not loaded
3. Why is the modification time part of the packed file's name?
4. Which setting serves packed scripts from another host?
5. A learner edits a packed file in the cache directory to fix a color. Why is that wrong?

<details>
<summary>Answers</summary>

1. `site.ini [TemplateSettings] DevelopmentMode`: `enabled` switches packing off, `disabled` leaves it on.
2. **C.**
3. So that a changed file gets a new name and browsers fetch it, although packed files are cached for a long time
   (`[Packer] AppendLastModifiedTime=enabled`).
4. `ezjscore.ini [Packer] CustomHosts[.js]`.
5. The packed file is generated; the next change or cache clear replaces it. The fix belongs in the source stylesheet.

</details>

## Further reading

- [Stylesheets and scripts for your site](../../../creators/website-creators/stylesheets-and-scripts.md): the packer's
  settings in one table.
- [ezjscore](../../../../features/6.0/extensions/ezjscore.md): the extension behind the packer.
- [Performance and caching for websites](../../../creators/website-creators/performance-and-caching.md): what the
  packer does for page speed.

## Related pages

- [Module 3: Stylesheets and scripts through the packer](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
