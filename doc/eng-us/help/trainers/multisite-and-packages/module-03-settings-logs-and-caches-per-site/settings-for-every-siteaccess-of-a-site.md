---
title: "Lesson 3.2: Settings for every siteaccess of a site"
audience: trainer, admin, developer
category: training
order: 32
summary: Learners put settings that apply to all siteaccesses of one site into the site extension's settings/override directory, learn the override directories for groups of siteaccesses, and prove with exp:ini where that the other sites are not affected. 45 minutes.
keywords: multisite and site packages, multi-site INI overrides, extension settings/override, override__group, ext-siteaccess-override, site extension, eZINI_CONFIG_CACHE_DIR, EXP_SITE_STRUCTURE_EZ_INI_OVERRIDE_DIR_LIST, INI cache
type: help
---
# Lesson 3.2: Settings for every siteaccess of a site

A site with three languages has three siteaccesses, and most of its settings are the same for all of them. Writing
them three times invites mistakes; writing them in the global override changes every other site too. A site
extension can carry an `override` directory whose settings apply only while one of its own siteaccesses runs. This
lesson uses it for the site moved into `site_clubb` in lesson 3.1.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 3: Settings, logs and caches per site](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Advanced |
| Audience | Administrators and developers |
| Prerequisites | [Lesson 3.1](a-site-in-its-own-extension.md): `site_clubb` carries the siteaccesses `site_b` and `site_b_ger` |

## Learning objectives

After this lesson, learners can:

- name the three per-site settings directories of a site extension and their priority;
- write a setting that applies to every siteaccess of one site and to no other;
- explain when `override__<group>` directories apply;
- find the placement `ext-siteaccess-override:<extension>` in the load order with `exp:ini where`.

## Trainer's notes

### How to present it

Source: [Per-site settings inside extensions (multi-site hosting)](../../../../features/6.0/multi-site-ini-overrides.md).

1. **The three directories** (6 minutes). For an extension `site_example`, read while one of **its** siteaccesses is
   active, highest priority first:

   | Directory | Applies to |
   |---|---|
   | `extension/site_example/settings/override/` | every siteaccess of the extension |
   | `extension/site_example/settings/override__<group>/` | every siteaccess whose name starts with `<group>__` |
   | `extension/site_example/settings/siteaccess/<siteaccess>/` | that siteaccess only |

   "Its" siteaccesses are those for which the extension has a `settings/siteaccess/<siteaccess>/` directory; the new
   directories are looked up only then, so unrelated extensions cost nothing. The installation's own
   `settings/override/` still has the last word over all three.
2. **Groups** (3 minutes). Siteaccesses named `example__eng` and `example__ger` form the group `example`;
   `override__example/` applies to both. The lab's names do not use the double underscore, so the exercise uses
   `override/`.
3. **One INI cache for every project** (3 minutes). A multi-site wrapper can set `$GLOBALS['eZINI_CONFIG_CACHE_DIR']`
   before bootstrapping, so all projects share one INI cache and clearing it once clears it for all. Without it,
   nothing changes. The opposite, an INI cache per site, is lesson 3.4.
4. **Many site extensions** (3 minutes). The constant `EXP_SITE_STRUCTURE_EZ_INI_OVERRIDE_DIR_LIST` set to `true`
   (for example in `config.php`) drops the override directories of the other `extension/site_*` extensions for the
   current request. Leave it off unless you host many sites.

### Common questions

**Does a setting in the extension's `override/` beat the global override?** No. The global `settings/override/`
still has the last word.

**Does it beat the extension's own siteaccess directory?** Yes: `override/` has a higher priority than
`settings/siteaccess/<siteaccess>/` of the same extension.

**Can `exp:ini` write into the extension's `override/` directory?** Its scopes are listed by `exp:ini scopes`; on an
installation where no scope names that directory, write the file by hand, as in the exercise.

**How do I see these files on a page?** With INI debugging on, the debug bar lists the loaded files of `site.ini`;
the placement name of such a setting is `ext-siteaccess-override:<extension>`.

### Pitfalls

- **No siteaccess directory in the extension.** Without `settings/siteaccess/<siteaccess>/` for a siteaccess, its
  `override/` is never read for it. Lesson 3.1 created both.
- **The file wrapper.** An `.ini.append.php` file starts with `<?php /*` and ends with `*/ ?>`, like the files in
  `settings/override/`: the settings sit inside a PHP comment, so the file shows nothing if it is ever requested
  from the web. Copy the wrapper exactly.
- **The INI cache.** Clear it after writing by hand (`exp:ini` does that itself).

## Hands-on exercise

Use your training installation from lesson 3.1.

1. Check the scopes the installation offers for the extension: `php bin/php/console exp:ini scopes | grep site_clubb`.
2. Create the directory and the file by hand:

   ```bash
   mkdir -p extension/site_clubb/settings/override
   ```

   Write `extension/site_clubb/settings/override/site.ini.append.php` with this content:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [SiteSettings]
   MetaDataArray[author]=Club B

   */ ?>
   ```

3. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini` (restart Velocity if it serves the lab).
4. Read the value for each siteaccess:

   ```bash
   php bin/php/console exp:ini where 'site.ini/SiteSettings/MetaDataArray[author]' site_b
   php bin/php/console exp:ini where 'site.ini/SiteSettings/MetaDataArray[author]' site_b_ger
   php bin/php/console exp:ini where 'site.ini/SiteSettings/MetaDataArray[author]' site
   ```

5. Compare the three outputs: which files are listed for `site_b`, and which for `site`?
6. Check the page: `curl -s http://127.0.0.1:8087/site_b | grep -i 'name="author"'` and the same for `/site`.
7. Now add `SiteName=Club B, all languages` to the `[SiteSettings]` block of the same file, clear the INI cache and
   open `/site_b_ger`. Explain why the German name from lesson 1.4 is gone.
8. Remove the `SiteName` line again and clear the INI cache.

## Expected result

- Step 4: for `site_b` and `site_b_ger`, the list includes `extension/site_clubb/settings/override/site.ini.append.php`
  and the value in effect is `Club B`; for `site` the file is not loaded and the value is the one the main site had.
- Step 6: the `author` meta tag reads `Club B` on `/site_b` only (when the design prints `MetaDataArray`; otherwise
  rely on step 4).
- Step 7: `/site_b_ger` shows *Club B, all languages*, because the extension's `override/` beats its siteaccess
  directory.
- The trainer checks step 4 on one lab and asks for the explanation of step 7.

## Quiz

1. Rank by priority: the extension's `settings/siteaccess/site_b/`, the extension's `settings/override/`, the global
   `settings/override/`.
2. Which siteaccesses read `extension/site_example/settings/override__shop/`?
   A. All B. Those whose names start with `shop__` C. Only `shop` D. None
3. Why is the extension's `override/` not read for the siteaccess `site`?
4. What is the placement name of such a setting in the debug output?
5. What does setting `$GLOBALS['eZINI_CONFIG_CACHE_DIR']` before bootstrapping achieve?

<details>
<summary>Answers</summary>

1. The global `settings/override/` first, then the extension's `override/`, then its `siteaccess/site_b/`
   ([Per-site settings inside extensions](../../../../features/6.0/multi-site-ini-overrides.md#where-the-files-go)).
2. **B.**
3. The extension has no `settings/siteaccess/site/` directory, so `site` is not one of its siteaccesses.
4. `ext-siteaccess-override:<extension>`.
5. Every project on the installation uses the same INI cache directory, so clearing it once clears it for all.

</details>

## Further reading

- [Per-site settings inside extensions (multi-site hosting)](../../../../features/6.0/multi-site-ini-overrides.md).
- [Specification: INI override placements](../../../../specifications/6.0/ini-override-placements.md).
- [Extensions in more than one folder](../../../../features/6.0/additional-extension-directories.md).
- [Your own INI file](../../../designers/extensions/your-own-ini-file.md).

## Related pages

- [Module 3: Settings, logs and caches per site](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
