---
title: "Lesson 3.1: The design chain"
audience: trainer, developer
category: training
order: 31
summary: Learners find the site design of a siteaccess with exp:ini where, follow the chain from the site design through the additional designs to standard, and give their course extension a design folder that joins the chain. 45 minutes.
keywords: developers course, design, design chain, SiteDesign, AdditionalSiteDesignList, StandardDesign, standard, DesignExtensions, design.ini, ActiveExtensions, exp:ini where, fallback
type: help
---
# Lesson 3.1: The design chain

A template is not looked up in one folder but in a chain of designs: the site design first, then the additional
designs, then `standard`. Knowing the chain explains why a template you change has no effect, and where your own
templates must go. In this lesson learners read the chain of their siteaccess and add their extension's design
folder to it.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 3: Designs, overrides and debugging](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Developers and template builders |
| Prerequisites | [Module 2](../module-02-the-template-language/README.md) |

## Learning objectives

After this lesson, learners can:

- name the order in which a template is searched: overrides, site design, additional designs, `standard`;
- find the site design and additional designs of a siteaccess with `exp:ini where` and `exp:ini get`;
- explain what a design folder holds (`templates/`, `override/templates/`, `stylesheets/`, `images/`,
  `javascript/`);
- add an extension's design folder to the chain with `DesignExtensions[]` and switch the extension on.

## Trainer's notes

### How to present it

Sources: [Templates and design, parts 1 and 2](../../../../guides/templates-and-design.md#1-how-a-template-is-found)
and [Extension loading order](../../../../features/6.0/extension-loading-order.md).

1. **The lookup** (4 minutes). For a template such as `node/view/full.tpl`, the first file found wins:
   1. override templates whose rules in `override.ini` match (lesson 3.2);
   2. `templates/` of the site design;
   3. the same of the additional designs;
   4. `design/standard/templates/`, the fallback, never edited.
2. **The settings** (3 minutes). `site.ini [DesignSettings]`: `SiteDesign`, `AdditionalSiteDesignList[]` and
   `StandardDesign=standard`. Each siteaccess sets its own in `settings/siteaccess/<sa>/site.ini.append.php`.
3. **Where designs live** (3 minutes). In `design/<design>/` of the installation or in
   `extension/<name>/design/<design>/`. An extension's design folders join the chain when the extension is active
   and its `settings/design.ini.append.php` says `[ExtensionSettings] DesignExtensions[]=<name>`.
4. **Order between extensions** (3 minutes). An extension loaded earlier wins for templates and settings. Show
   `exp:ini get site.ini/ExtensionSettings/ActiveExtensions`.
5. **Site versus admin designs** (2 minutes). The administration siteaccess has its own design (`admin4` on new
   installations, with `admin3` and others available); site templates never affect it, and the other way round.

### Common questions

**Why not edit `design/standard/`?** It is the kernel's fallback design and is replaced at every update. Put your
template in your own design folder.

**Can one siteaccess use the design of another?** Yes: list it in `AdditionalSiteDesignList[]`. Many sites share a
base design that way.

**What is `design_base` in the cache list?** The cached list of design folders; clear it with
`php bin/php/ezcache.php --clear-id=design_base` after adding a design folder.

### Pitfalls

- **Wrong design name.** A folder `extension/mysite_hello/design/mysite/` does nothing unless `mysite` is the site
  design or an additional design of the siteaccess. Use the name `exp:ini where` printed.
- **Extension not active.** `DesignExtensions[]` is read only from active extensions.
- **Stale caches.** New template paths need the `template-override`, `design_base` and template caches cleared.

## Hands-on exercise

On the development installation. Use the name of your public siteaccess (for an `exp:install` default, `site`)
where the steps say `<sa>`.

1. Find the site design: `php bin/php/console exp:ini where site.ini/DesignSettings/SiteDesign <sa>`.
2. List the additional designs: `php bin/php/console exp:ini get site.ini/DesignSettings/AdditionalSiteDesignList <sa>`.
3. Write the chain on paper: site design, additional designs, `standard`.
4. Find where the site design's folder is: `ls -d design/<design> extension/*/design/<design> 2>/dev/null`.
5. Create the design folder of your extension, using the site design's name:

   ```bash
   mkdir -p extension/mysite_hello/design/<design>/templates
   mkdir -p extension/mysite_hello/design/<design>/override/templates
   mkdir -p extension/mysite_hello/settings
   ```

6. Register the design and switch the extension on (module 7 explains both in depth):

   ```bash
   php bin/php/console exp:ini add design.ini/ExtensionSettings/DesignExtensions mysite_hello extension:mysite_hello
   php bin/php/console exp:ini add site.ini/ExtensionSettings/ActiveExtensions mysite_hello global
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=design_base
   ```

7. Check that the extension is listed: `php bin/php/console exp:ini get site.ini/ExtensionSettings/ActiveExtensions`.
8. Copy the kernel's line view into your design: `mkdir -p extension/mysite_hello/design/<design>/templates/node/view`
   and `cp design/standard/templates/node/view/line.tpl extension/mysite_hello/design/<design>/templates/node/view/`.
9. Add a visible line `<p>mysite_hello line view</p>` at the top of the copy, clear the template caches
   (`php bin/php/ezcache.php --clear-tag=template` and `--clear-id=template-override`), and open a page that lists
   items in the line view. If the site design or an override already provides `line.tpl`, your copy is not used:
   note which file answers instead (lesson 3.4 shows how).
10. Commit the new files in the extension's repository.

## Expected result

- Step 1 lists `settings/site.ini` and the siteaccess file, ending with `In effect:` and the design name.
- Step 6 created `extension/mysite_hello/settings/design.ini.append.php` and added a line to
  `settings/override/site.ini.append.php`.
- Step 9: either the line appears in lists, or the learner can name the file that answered first and why.
- The trainer checks each learner's written chain from step 3.

## Quiz

1. In which order are designs searched for a template?
2. Which setting names the extensions whose design folders join the chain?
   A. `ActiveExtensions[]` B. `DesignExtensions[]` C. `AdditionalSiteDesignList[]` D. `StandardDesign`
3. What does `StandardDesign=standard` mean?
4. Your copy of `line.tpl` has no effect. Name two possible reasons.
5. True or false: the admin siteaccess uses the same design chain as the public site.

<details>
<summary>Answers</summary>

1. Matching overrides, the site design, the additional designs in their order, then `standard`.
2. **B.** `[ExtensionSettings] DesignExtensions[]` in the extension's `design.ini.append.php`; the extension must
   also be active.
3. `standard` is the fallback design, searched last.
4. The design folder name is not in the siteaccess's chain; the extension is not active; an override or an
   earlier design provides the template; caches were not cleared.
5. **False.** Each siteaccess has its own `[DesignSettings]`.

</details>

## Further reading

- [Templates and design](../../../../guides/templates-and-design.md): finding the design, overrides, debugging.
- [Extension loading order](../../../../features/6.0/extension-loading-order.md): which extension wins.
- [Registering the design](../../../designers/extensions/registering-the-design.md): the same steps for a theme.
- [Additional extension directories](../../../../features/6.0/additional-extension-directories.md): extensions
  outside `extension/`.

## Related pages

- [Module 3: Designs, overrides and debugging](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
