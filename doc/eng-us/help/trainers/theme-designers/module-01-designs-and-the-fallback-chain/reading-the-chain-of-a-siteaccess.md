---
title: "Lesson 1.2: Reading the design chain of a siteaccess"
audience: trainer, designer, developer
category: training
order: 12
summary: Learners read the design chain of a siteaccess with exp:ini (SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions) and predict which file a template lookup will find. 45 minutes.
keywords: theme design, design chain, fallback, SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions, siteaccess, exp:ini, where, get, extension order
type: help
---
# Lesson 1.2: Reading the design chain of a siteaccess

Every siteaccess has its own chain of designs, and Exponential takes the first file it finds along that chain. A
designer who can read the chain can predict which template, stylesheet or image a page will use before opening the
browser. This lesson teaches learners to read it from the settings with one command.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 1: Designs and the fallback chain](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Beginner |
| Audience | Designers and front-end developers |
| Prerequisites | [Lesson 1.1](what-a-design-is.md); shell access to a training installation |

## Learning objectives

After this lesson, learners can:

- name the four settings that make up a siteaccess's design chain;
- read where a setting comes from with `exp:ini where`, and its value with `exp:ini get`;
- put the places of a lookup in the right order: overrides, site design, additional designs, standard design;
- explain how the order of extensions decides between two files of the same design name.

## Trainer's notes

### How to present it

Source: [Designs, siteaccesses and the design fallback chain](../../../creators/website-creators/designs-and-the-fallback-chain.md)
and [The exp:ini command](../../../../features/6.0/exp-ini-command.md).

1. **The order** (6 minutes). For a template such as `node/view/full.tpl`, Exponential uses the first match in:
   1. the override templates whose `override.ini` rules match the page;
   2. `templates/` of the **site design**, `site.ini [DesignSettings] SiteDesign`;
   3. the same folders of each **additional design**, `AdditionalSiteDesignList[]`, in the order listed;
   4. the **standard design**, `StandardDesign=standard` in `settings/site.ini`.

   Each step looks in `design/<name>/` and in every design extension (`DesignExtensions[]`), extensions in their
   loading order.
2. **Read it, do not guess** (6 minutes). Run on the projector, with a siteaccess of your installation:

   ```bash
   php bin/php/console exp:ini where site/DesignSettings/SiteDesign <site>
   ```

   The output lists each file that sets the value in load order, with its scope, and ends with `In effect:`. Point
   out that `settings/site.ini` says `SiteDesign=admin`: the default is overridden by the siteaccess file.
3. **The rest of the chain** (4 minutes). `exp:ini get site/DesignSettings/AdditionalSiteDesignList <site>` prints
   the additional designs; `exp:ini where design/ExtensionSettings/DesignExtensions <site>` lists every extension that
   adds design folders. `get` prints the value only; `where` prints every file that sets it, which is what you need for
   arrays that many extensions add to.
4. **Extension order** (4 minutes). Two extensions may both carry `design/simple/templates/pagelayout.tpl`. The one
   that loads first wins. The order is changed in **Setup > Extensions** or with `ActiveAccessExtensions[]`
   ([Extension loading order](../../../../features/6.0/extension-loading-order.md)); module 7 returns to it.

### Common questions

**Why does `settings/site.ini` say `SiteDesign=admin`?** It is the built-in default. Every siteaccess sets its own in
`settings/siteaccess/<site>/site.ini.append.php`.

**What is the empty `AdditionalSiteDesignList[]` line?** An array reset: it clears what earlier files added, so the
list that follows is exactly the siteaccess's own.

**Can I set the design for every siteaccess at once?** You can, in `settings/override/`, but then the administration
gets your public design too. Set it per siteaccess.

### Pitfalls

- **`get` says "Not found".** `exp:ini get` reads the global and siteaccess scopes; a value that only extensions set
  (such as `DesignExtensions[]`) is listed by `where`. Use `where` when in doubt.
- **Running the command from the wrong directory.** It must run in the installation root.
- **As root.** On a shared training server the command may ask for `--allow-root-user`. Better: run it as the user
  who owns the files.

## Hands-on exercise

On your training installation, in the installation root. Replace `<site>` with the name of a public siteaccess (a
folder of `settings/siteaccess/` that is not an administration). Nothing in this exercise writes a file.

1. List the siteaccesses: `ls settings/siteaccess/`. Choose one public siteaccess and write its name down.
2. Read the site design:

   ```bash
   php bin/php/console exp:ini where site/DesignSettings/SiteDesign <site>
   ```

3. Note the value after `In effect:` and the file that set it.
4. Read the additional designs:

   ```bash
   php bin/php/console exp:ini get site/DesignSettings/AdditionalSiteDesignList <site>
   ```

5. Read the standard design: `php bin/php/console exp:ini get site/DesignSettings/StandardDesign <site>`.
6. List the design extensions:

   ```bash
   php bin/php/console exp:ini where design/ExtensionSettings/DesignExtensions <site>
   ```

7. Write the chain on a sheet, top to bottom: site design, additional designs, `standard`; and beside each design name
   the places where a folder of that name exists (`design/<name>/` and `extension/*/design/<name>/`; use
   `ls -d design/<name> extension/*/design/<name>`).
8. Choose the template `pagelayout.tpl`. Using your sheet, predict which file the siteaccess uses. Check your
   prediction with `ls` on each place in order; the first that has `templates/pagelayout.tpl` is the answer.
9. Repeat steps 2 to 8 for the administration siteaccess (usually `admin`) and compare the two chains.

## Expected result

- The sheet shows two different chains: the public siteaccess with its site design (for example `plain` or the
  design of a theme) and the administration with an admin design and its additional admin designs.
- Step 8: each learner names one concrete file path for `pagelayout.tpl` and can say why the files further down the
  chain are not used.
- The trainer checks two learners' sheets on the projector, and confirms one prediction with template path comments
  in lesson 1.3.

## Quiz

1. Put in order of lookup: standard design, additional designs, override templates, site design.
2. Which command shows every settings file that sets `SiteDesign` for a siteaccess, and the value in effect?
3. True or false: `StandardDesign` is usually `standard`.
4. Two active extensions both carry `design/simple/templates/pagelayout.tpl`. Which one is used?
   A. The one with the newer file B. The one that loads first C. Neither; the standard design D. Both are merged
5. Why should `SiteDesign` for a public site not be set in `settings/override/`?

<details>
<summary>Answers</summary>

1. Override templates, site design, additional designs, standard design
   ([Templates and design, part 1](../../../../guides/templates-and-design.md#1-how-a-template-is-found)).
2. `php bin/php/console exp:ini where site/DesignSettings/SiteDesign <site>`.
3. **True.** `settings/site.ini` sets `StandardDesign=standard`, the fallback you never edit.
4. **B.** Extensions are searched in their loading order.
5. `settings/override/` applies to every siteaccess, so the administration would get the public design as well.

</details>

## Further reading

- [Designs, siteaccesses and the design fallback chain](../../../creators/website-creators/designs-and-the-fallback-chain.md):
  reading and changing the chain, with common mistakes.
- [Change settings from the command line: exp:ini](../../../../features/6.0/exp-ini-command.md): every action and
  scope.
- [Extension loading order](../../../../features/6.0/extension-loading-order.md): which extension wins.
- [Loading order for themes](../../../designers/extensions/loading-order-for-themes.md): the same question from a
  theme author's side.

## Related pages

- [Module 1: Designs and the fallback chain](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
