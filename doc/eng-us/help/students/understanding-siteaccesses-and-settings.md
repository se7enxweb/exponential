---
title: Understanding siteaccesses and settings
audience: student
category: learning
order: 19
summary: An explanation for learners of siteaccesses (site, admin, editor and others) and INI settings - what a siteaccess is, the settings files and their .ini.append.php overrides, the order in which scopes win, how to ask the installation with exp:ini where, and the mistakes learners make most often.
keywords: siteaccess, settings, INI, site.ini, ini.append.php, override, scope, exp:ini, where, design, configuration, explanation, learner
type: help
---
# Understanding siteaccesses and settings

Almost everything Exponential does is decided by a setting, and every request comes in through a siteaccess. When a
change "has no effect", one of these two ideas is usually the reason. This page explains both for learners and shows
how to ask the installation itself.

## Contents

1. [Siteaccesses](#siteaccesses)
2. [Settings files](#settings-files)
3. [Which file wins](#which-file-wins)
4. [Ask the installation](#ask-the-installation)
5. [Common mistakes](#common-mistakes)
6. [Exercises](#exercises)

## Siteaccesses

A **siteaccess** is one way of reaching the site (by URL, host or port) with its own settings and design
([Glossary](../../glossary.md#s)). Every installation has three, always called `site`, `admin` and `editor`
([Installing Exponential 6.0](../../INSTALL.md#1-what-you-get-and-the-quick-start)):

| Siteaccess | For | With `exp:install` on its defaults |
|---|---|---|
| `site` | Visitors | the path `/site` |
| `admin` | Administrators | the path `/admin` |
| `editor` | Editors: the administration for content only, without setup, design and developer tools | the path `/editor` |

A site package can add more. The default `sevenx_multisite` adds `bold` and `bold_ger` for its second website
([Site packages for learners](site-packages-for-learners.md)). A multilingual site usually has one public siteaccess
per language ([Content languages](../../guides/content-languages.md#5-showing-a-language-sitelanguagelist-and-siteaccesses)).

Each siteaccess has its own folder of settings, `settings/siteaccess/<name>/`.

## Settings files

- The defaults are in `settings/*.ini`: `site.ini`, `content.ini`, `design.ini`, `override.ini` and many more.
- You **never edit these files**: they are replaced at every upgrade. You add files with the same name and the
  suffix `.ini.append.php` in a place that is read after them.
- A setting is named by file, block and variable: `site.ini [SiteSettings] SiteName`, or for the settings command
  `site.ini/SiteSettings/SiteName`.

Source: [How INI settings work](../extension-development/part-2-builder/how-ini-settings-work.md).

## Which file wins

After the default file, the kernel reads the other places in this order, and **a later file wins**:

1. the per-siteaccess folders inside extensions,
2. `settings/siteaccess/<siteaccess>/`,
3. `extension/<ext>/settings/` of each active extension,
4. `settings/override/`.

Source: [Settings scopes and the order they win in](../extension-development/part-2-builder/settings-scopes-and-their-order.md#the-order).
Note the surprise in step 3: a value an extension ships beats the same value in `settings/siteaccess/<sa>/`.

## Ask the installation

You do not have to work the order out by hand. `exp:ini where` shows every file that sets a value, in load order,
and the value in effect ([Templates and design](../../guides/templates-and-design.md#2-find-out-which-design-you-use)):

```bash
php bin/php/console exp:ini where site/DesignSettings/SiteDesign site
```

The output lists the files, for example `settings/site.ini` with `SiteDesign=admin` and then the siteaccess file,
and ends with `In effect:` and the design used. Replace `site` at the end with the siteaccess you study. The command
can also read, set and add values; see [exp:ini](../../features/6.0/exp-ini-command.md) and
[The exp:ini command](../extension-development/part-2-builder/the-exp-ini-command.md).

After you change a setting, the INI cache may still hold the old value. Clear it as described in
[Understanding caches](understanding-caches.md).

## Common mistakes

| Mistake | What happens | Instead |
|---|---|---|
| Editing `settings/site.ini` | Lost at the next upgrade | Use an `.ini.append.php` file in `settings/override/` or a siteaccess folder |
| Changing the `site` siteaccess and looking at `bold` | No change where you look | Find the siteaccess of the page you look at |
| Setting a value in `settings/siteaccess/<sa>/` that an extension also sets | The extension's value wins | `settings/override/`, or the extension's own siteaccess folder |
| Writing `true` where the default uses `enabled` | The switch does not work | Use the form the default file uses |
| Forgetting the cache | The old value stays | Clear the INI cache |
| Putting a password into a settings file you commit | The password is published | Keep `settings/override/` and its secrets out of public repositories |

## Exercises

1. List the folders in `settings/siteaccess/` of your study site. Which siteaccesses exist?
2. Run `exp:ini where site/DesignSettings/SiteDesign` for two of them. Do they use the same design?
3. Find `SiteLanguageList` for the `site` siteaccess with `exp:ini where site/RegionalSettings/SiteLanguageList site`.
   Which languages does it show, in which order?
4. Explain to a fellow learner, in three sentences, why a value from an extension can beat a siteaccess value.

The trainers' course [Multisite and site packages](../trainers/multisite-and-packages/README.md) and the builder
chapters [Siteaccess settings](../extension-development/part-2-builder/siteaccess-settings.md) go further.

## Related pages

- [Understanding caches](understanding-caches.md)
- [Learning path: administrator](learning-path-administrator.md)
- [Understanding the content model](understanding-the-content-model.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
