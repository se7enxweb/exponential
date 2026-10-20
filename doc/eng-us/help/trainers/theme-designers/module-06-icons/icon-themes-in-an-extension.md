---
title: "Lesson 6.1: Icon themes in an extension"
audience: trainer, designer, developer
category: training
order: 61
summary: Learners explain how Exponential finds a class or file type icon (theme, additional themes, standard theme, default), ship their own icon theme in their extension with icon.ini and IconExtensions, and see it in the administration's lists. 45 minutes.
keywords: theme design, icon theme, icon.ini, IconSettings, Theme, StandardTheme, AdditionalThemeList, IconExtensions, ClassMap, ClassIcons, crystal, crystal-admin, bootstrap-icons, DesignTheme, class icon, mimetype icon
type: help
---
# Lesson 6.1: Icon themes in an extension

The small icons beside items in the administration's lists and tree come from an **icon theme**: a folder of images
with an `icon.ini` that maps classes and file types to files. Since July 2026 an extension can ship its own theme, so a
custom icon no longer means copying files into the core. In this lesson learners give a content class of the training
site an icon of their own.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 6: Icons](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Designers and developers |
| Prerequisites | [Module 2](../module-02-templates-overrides-and-the-pagelayout/README.md): `training_theme` active; an image editor or a small PNG file |

## Learning objectives

After this lesson, learners can:

- explain the order in which an icon is looked for;
- name the settings of `icon.ini` (`Theme`, `StandardTheme`, `AdditionalThemeList[]`, `IconExtensions[]`,
  `DesignTheme[]`);
- create an icon theme in their extension with sizes and a class map;
- explain why admin4 shows the `bootstrap-icons` theme whatever `Theme` says.

## Trainer's notes

### How to present it

Source: [Icon themes in extensions](../../../../features/6.0/icon-themes-in-extensions.md) and
[The admin4 design, icons](../../../../features/6.0/admin4-design.md#icons).

1. **The lookup** (4 minutes). An icon is found in: the current theme (`Theme`), first in the folders of the extensions
   listed in `IconExtensions[]`, then in `Repository` (`share/icons`); each theme of `AdditionalThemeList[]` in the same
   order of places; `StandardTheme`; finally the theme's `Default` icon, so a missing file never gives a broken image.
2. **The settings** (4 minutes). `settings/icon.ini`: `Repository=share/icons`, `Theme=crystal`, `StandardTheme=crystal`,
   `AdditionalThemeList[]` empty, `IconExtensions[]` empty. Show `share/icons/`: `crystal`, `crystal-admin`,
   `bootstrap-icons`.
3. **admin4** (3 minutes). `icon.ini [IconSettings] DesignTheme[admin4]=bootstrap-icons` (and `admin4l`) gives admin4
   one SVG icon set whatever the siteaccess's `Theme`; it is searched right after the design theme. For a class it
   does not map, the operators take the PNG of the siteaccess's `Theme`, but the content tree shows the theme's
   `Default`: a class of your own is added to `ClassMap` in an `icon.ini.append.php`.
4. **The theme's files** (4 minutes). One folder per size and group (`32x32/apps/job.png`,
   `16x16_indexed/apps/job.png`), and the theme's own `icon.ini` with `[IconSettings] Sizes[]` and
   `[ClassIcons] ClassMap[<class identifier>]`.

### Common questions

**Which class identifiers exist on the training site?** **Setup > Classes** lists them; the identifier is shown on each
class's page.

**Are icons in an extension slow?** No; they are plain static files served by the web server.

**Can I override one icon of crystal-admin without copying the whole theme?** Yes: a theme of your own in
`AdditionalThemeList[]` that holds only that icon, with `StandardTheme` as the fallback.

### Pitfalls

- **The wrong theme name.** The folder name below `icons/` and the name in `Theme` or `AdditionalThemeList[]` must be
  identical.
- **Testing in admin4 only.** admin4 prefers `bootstrap-icons`; for this exercise the learner's theme must also be in
  the admin4 lookup, or the classic design is used to see the PNG (step 9).
- **Forgetting the caches.** INI changes need the INI cache cleared; lists that show icons may be in the content view
  cache.

## Hands-on exercise

On your training installation. Choose one content class identifier of the training site that has items in a folder
(for example `article`) and write it down as `<class>`.

1. Create the folders:

   ```bash
   mkdir -p extension/training_theme/icons/training-icons/32x32/apps extension/training_theme/icons/training-icons/16x16_indexed/apps
   ```

2. Put a 32 x 32 PNG and a 16 x 16 PNG of your own (or two copies of one file from `share/icons/crystal-admin/`) in the
   two `apps/` folders, named `<class>.png`.
3. Write `extension/training_theme/icons/training-icons/icon.ini`:

   ```ini
   #?ini charset="utf-8"?
   [IconSettings]
   Sizes[]
   Sizes[normal]=32x32
   Sizes[small]=16x16_indexed

   [ClassIcons]
   Default=mimetypes/empty.png
   ClassMap[<class>]=apps/<class>.png
   ```

4. Write `extension/training_theme/settings/icon.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [ExtensionSettings]
   IconExtensions[]=training_theme

   [IconSettings]
   AdditionalThemeList[]=training-icons

   */ ?>
   ```

5. Clear the caches: `php bin/php/ezcache.php --clear-tag=ini` and `php bin/php/ezcache.php --clear-tag=content`.
6. Read the lookup as the administration sees it:
   `php bin/php/console exp:ini where icon/IconSettings/AdditionalThemeList admin`.
7. In the administration, open the folder that holds items of `<class>` and look at the icons in the sub items list.
8. Compare with an item of another class.
9. To see the PNG where admin4 uses its SVG set, switch the administration design for your session to **Classic** with
   the **Interface** tool (lesson 1.4), look again, and switch back.

## Expected result

- Step 6 lists `extension/training_theme/settings/icon.ini.append.php` with `AdditionalThemeList[]=training-icons`.
- Step 7 or step 9: items of `<class>` show the learner's icon where the theme in effect does not already map the class;
  other classes keep their icons.
- The trainer checks one learner's list on the projector and explains any case where `bootstrap-icons` or the
  siteaccess's `Theme` answered first.

## Quiz

1. Put in order: `StandardTheme`, `Theme`, the theme's `Default`, `AdditionalThemeList[]`.
2. Which setting makes Exponential search an extension's `icons/` folder?
3. Why does admin4 show SVG icons whatever the siteaccess's `Theme`?
4. What does the theme's `ClassMap[job]=apps/job.png` say?
5. True or false: a missing icon file shows a broken image.

<details>
<summary>Answers</summary>

1. `Theme`, `AdditionalThemeList[]`, `StandardTheme`, `Default`
   ([Icon themes in extensions](../../../../features/6.0/icon-themes-in-extensions.md#how-an-icon-is-found)).
2. `icon.ini [ExtensionSettings] IconExtensions[]=<extension>`.
3. `icon.ini [IconSettings] DesignTheme[admin4]=bootstrap-icons` gives the design its own theme.
4. Items of the class `job` use `apps/job.png` of the size folder that is asked for.
5. **False.** The theme's `Default` icon is used.

</details>

## Further reading

- [Icon themes that live in your extension](../../../../features/6.0/icon-themes-in-extensions.md): the settings
  table.
- [Icon support (guide)](../../../../bc/6.0/ICONS.md): the class behind it and upgrade notes.
- [Third-party fonts, icons and libraries](../../../designers/distribution/third-party-fonts-icons-and-libraries.md):
  licenses of icons you ship (module 8).

## Related pages

- [Module 6: Icons](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
