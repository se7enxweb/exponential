---
title: "Lesson 7.2: Activating a theme and the loading order"
audience: trainer, designer, developer
category: training
order: 72
summary: Learners activate and deactivate their theme on Setup > Extensions with a review before saving, choose between ActiveExtensions and ActiveAccessExtensions, decide which settings a theme ships, and make a theme win over a base theme with extension.xml dependencies. 45 minutes.
keywords: theme design, activate extension, Setup Extensions, Review changes, ActiveExtensions, ActiveAccessExtensions, loading order, ExtensionOrdering, extends, requires, uses, dependencies, extension.xml, SiteDesign in README
type: help
---
# Lesson 7.2: Activating a theme and the loading order

Whoever installs a theme switches it on, chooses which siteaccess uses it, and sometimes combines it with another
theme. The theme author decides how easy and safe that is: which settings the theme ships, which it leaves to the
installation, and how it declares its relation to other extensions. This lesson takes both sides.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 7: Packaging a theme](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Designers and developers who publish themes; administrators who install them |
| Prerequisites | [Lesson 7.1](theme-extension-metadata.md) |

## Learning objectives

After this lesson, learners can:

- activate and deactivate an extension on **Setup > Extensions**, reading the review before saving;
- choose between `ActiveExtensions[]` (the installation) and `ActiveAccessExtensions[]` (one siteaccess);
- split settings between the theme and the installation, and document `SiteDesign` instead of setting it;
- make a theme load before a base theme with `<extends>` in `extension.xml`.

## Trainer's notes

### How to present it

Sources: [Activating a theme](../../../designers/extensions/activating-a-theme-extension.md),
[Loading order and which theme wins](../../../designers/extensions/loading-order-for-themes.md),
[The design settings a theme ships](../../../designers/extensions/design-settings-a-theme-ships.md) and
[The Extensions page](../../../../guides/extensions-page.md).

1. **Two switches** (3 minutes). `site.ini [ExtensionSettings] ActiveExtensions[]` for the whole installation (the
   usual case); `ActiveAccessExtensions[]` in one siteaccess's settings, when the theme must not touch other siteaccesses
   at all.
2. **The page** (4 minutes). **Setup > Extensions**: each extension's card with **Activate** or **Deactivate**; the
   changes are written only after **Review changes**, which shows the resulting lines; the save keeps a backup copy.
   The page can also **Regenerate autoload arrays**.
3. **What the theme ships** (4 minutes). In the theme: `DesignExtensions[]`, its stylesheet and script lists, the
   defaults of its own settings. In the installation: `SiteDesign` for the siteaccess that uses the theme, site name,
   URLs, mail and database. A theme that set `SiteDesign` globally would change the administration and every other
   site. A theme that comes with its own siteaccess may set it in `settings/siteaccess/<siteaccess>/` of the extension.
4. **Who wins** (4 minutes). An extension loaded earlier wins for settings and templates. With
   `ExtensionOrdering=enabled` (the default), `<extends>` in your `extension.xml` makes your theme load before the base
   theme; `<requires>` and `<uses>` make it load after the named extension.

### Common questions

**Why does my theme still draw after I deactivated it?** The INI cache. Clear it, and restart Velocity on a Velocity
lab.

**Can I order extensions by hand?** Yes, in the list on **Setup > Extensions**, reviewed and then written; but a
declared dependency is more robust.

**Does deactivating delete files?** No. The folder stays; only the setting changes.

### Pitfalls

- **Deactivating on a shared lab.** Every learner works on their own installation for this lesson.
- **Reading the card before the cache is cleared.** The card shows where the extension loads after the save; reload.
- **Setting `SiteDesign` in the theme's `settings/` folder.** That is global for every siteaccess the theme is active in.

## Hands-on exercise

On your own training installation, signed in to the administration with your training administrator.

1. Open **Setup > Extensions**. Find the card of `training_theme` and read its state and position.
2. Press **Deactivate** on it, then **Review changes**. Read the lines the page would write. Apply the change with the
   button the review offers.
3. Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`; on Velocity also
   `php bin/php/console exp:velocity restart`) and reload a page of `<site>`.
4. Note what the site looks like now, and why (`SiteDesign=training_theme` names a design that is no longer searched).
5. Back on **Setup > Extensions**, activate `training_theme` again, review and apply; clear the INI cache; reload.
6. Read where `SiteDesign` is set: `php bin/php/console exp:ini where site/DesignSettings/SiteDesign <site>`. It is in
   the installation's siteaccess settings, not in the theme: good.
7. In the theme's README, add a section "Switching it on" with the two lines an installer needs: the
   `ActiveExtensions[]` line and the `SiteDesign` line for their siteaccess.
8. Write in your notes the `<dependencies>` block that would make your theme win over a base theme called
   `base_theme`, without adding it to your file (there is no such extension on the lab).

## Expected result

- Step 2: the review lists the change of `ActiveExtensions[]`; the save names the file and its backup copy.
- Step 4: the site falls back to the standard design's templates (an unstyled page) while the theme is inactive.
- Step 5: the theme draws the site again.
- Step 7: the README tells an installer how to switch the theme on without reading the code.
- The trainer checks one learner's README section and their `<dependencies>` note:
  `<dependencies><extends><extension name="base_theme" /></extends></dependencies>`.

![The Extensions page, where a theme extension is activated and its loading order shown](../../../images/trainers/td-extensions.webp)

## Quiz

1. Which setting activates a theme for only one siteaccess?
2. Why does a theme not set `SiteDesign` in its `settings/site.ini.append.php`?
3. Your theme must override templates of `base_theme`. What do you declare in `extension.xml`?
   A. `<requires>` B. `<uses>` C. `<extends>` D. nothing
4. What does **Review changes** on **Setup > Extensions** do?
5. Does deactivating an extension delete its folder?

<details>
<summary>Answers</summary>

1. `ActiveAccessExtensions[]` in that siteaccess's settings
   ([Activating a theme](../../../designers/extensions/activating-a-theme-extension.md)).
2. It would apply to every siteaccess where the theme is active, including the administration.
3. **C.** `<extends>` makes the theme load before `base_theme`, so it wins
   ([Loading order and which theme wins](../../../designers/extensions/loading-order-for-themes.md)).
4. It shows the resulting `ActiveExtensions[]` lines and the risks before anything is written.
5. No; only the setting changes.

</details>

## Further reading

- [The Extensions page](../../../../guides/extensions-page.md): every part of **Setup > Extensions**.
- [Declaring dependencies in extension.xml](../../../designers/extensions/dependencies-in-extension-xml.md).
- [Siteaccess settings inside a theme](../../../designers/extensions/siteaccess-settings-in-an-extension.md): when the
  theme ships its own site.
- [Settings files in an extension](../../../designers/extensions/settings-files-in-an-extension.md).

## Related pages

- [Module 7: Packaging a theme](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
