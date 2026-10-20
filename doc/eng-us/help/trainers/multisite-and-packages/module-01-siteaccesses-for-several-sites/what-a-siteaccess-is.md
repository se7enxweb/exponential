---
title: "Lesson 1.1: What a siteaccess is"
audience: trainer, admin, developer
category: training
order: 11
summary: Learners explain what a siteaccess is, find the siteaccesses of an installation in its settings files, and say which file wins when several set the same value. 45 minutes.
keywords: multisite and site packages, siteaccess, AvailableSiteAccessList, RelatedSiteAccessList, DefaultAccess, settings/siteaccess, settings/override, load order, exp:ini where
type: help
---
# Lesson 1.1: What a siteaccess is

Everything in this course rests on one idea: an installation of Exponential is entered through **siteaccesses**. A
siteaccess is a name, a directory of settings and a rule that decides which requests it answers. Several public
sites, the administration and the Editor are all siteaccesses of the same installation. This lesson makes the idea
concrete before any site is added.

> **Note**
> Every exercise of this course runs on a training installation, never on a live site. Learners change settings
> files and clear caches; on a live site that would change what visitors see.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 1: Siteaccesses for several sites](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Administrators and developers who will run several sites on one installation |
| Prerequisites | A training installation of their own with shell access (installed with `php bin/php/console exp:install`); basic use of a terminal |

## Learning objectives

After this lesson, learners can:

- name the three parts of a siteaccess: its name, its settings directory and its matching rule;
- list the siteaccesses of an installation from `settings/siteaccess/` and from `AvailableSiteAccessList[]`;
- explain the load order of settings files and say which file wins for a plain value;
- use `exp:ini where` to see every file that sets a value and the value in effect;
- tell `RelatedSiteAccessList[]` from `AvailableSiteAccessList[]`.

## Trainer's notes

### How to present it

Sources: [10.2 Siteaccesses and site addresses](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses),
[Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md) and
[Changing settings from the command line: exp:ini](../../../../bc/6.0/console-exp-ini.md).

1. **The definition** (5 minutes). Write on the board: *siteaccess = name + settings in
   `settings/siteaccess/<name>/` + a rule for which requests it answers.* Every installation has at least a public
   siteaccess and the administration; `exp:install` and the kickstarter also create the Editor. The directories are
   always `site`, `admin` and `editor` (and `adminui` when the `exp_adminui` extension is present); the URL paths,
   hosts or ports that lead to them are chosen at install time.
2. **Where the list lives** (5 minutes). Show `settings/override/site.ini.append.php`, block
   `[SiteAccessSettings]`: `AvailableSiteAccessList[]` lists the siteaccesses that may answer,
   `RelatedSiteAccessList[]` the ones that share the same database (the kernel's comment in `settings/site.ini`
   says exactly that), and `MatchOrder` the order of the rules (`uri;host;port` as shipped). `[SiteSettings]
   DefaultAccess` is the siteaccess that answers when no rule matched.
3. **The load order** (6 minutes). Draw four boxes from bottom to top: the kernel defaults in `settings/*.ini`,
   the settings of active extensions, `settings/siteaccess/<name>/`, and `settings/override/`, which has the last
   word. A plain value takes the last file that sets it; arrays add up unless a file resets them with an empty
   `Name[]` line. Module 3 adds the per-site override directories of site extensions to this picture.
4. **Seeing it live** (4 minutes). Run `exp:ini where` on the projector (step 5 of the exercise) and read the
   numbered list aloud: it is the load order, printed for one setting.

### Common questions

**Is a siteaccess the same as a site?** No. A site is what visitors see; it can have several siteaccesses, for
example one per language. The administration and the Editor are siteaccesses too, and they are not sites.

**Do siteaccesses have their own database?** Not normally. The siteaccesses of `RelatedSiteAccessList[]` share one
database and one content tree. Separate databases mean separate installations, which lesson 1.4 compares.

**Why is `admin` the `DefaultAccess` in `settings/site.ini`?** That is the kernel's default for an empty URL; an
installation writes its own value into the override. Always read the value in effect, not the default file.

**Can I edit `settings/site.ini`?** Do not: it is replaced on every upgrade. Settings go into
`settings/override/`, a siteaccess directory or an extension. `exp:ini` refuses the `default` scope unless
`--allow-default` is given.

### Pitfalls

- **Reading the wrong file.** Learners open `settings/site.ini`, find `MatchOrder=uri;host;port` and conclude that
  is what the site uses. Make them run `exp:ini get` or `exp:ini where`, which show the value in effect.
- **Running as root.** On a shared training server, `exp:ini` and other scripts need `--allow-root-user` when run as
  root; prefer the user that serves the site.
- **Velocity.** If the training site runs on Exponential Velocity, a settings change reaches it only after
  `php bin/php/console exp:velocity restart`. This lesson only reads settings, so it does not matter yet.

## Hands-on exercise

Use your own training installation, installed with `php bin/php/console exp:install` as in
[7. The console install](../../../../install/07-console-install.md). Work in its root directory (the one that holds
`index.php`).

1. List the siteaccess directories:

   ```bash
   ls settings/siteaccess/
   ```

2. Read the siteaccesses that may answer, and the ones that share the database:

   ```bash
   php bin/php/console exp:ini get 'site.ini/SiteAccessSettings/AvailableSiteAccessList[]'
   php bin/php/console exp:ini get 'site.ini/SiteAccessSettings/RelatedSiteAccessList[]'
   ```

3. Compare the two lists with the directories of step 1. Note any directory that is not in the first list.
4. Read the matching rules and the default:

   ```bash
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/MatchOrder
   php bin/php/console exp:ini get site.ini/SiteSettings/DefaultAccess
   ```

5. See the load order for the site's name, for the public siteaccess:

   ```bash
   php bin/php/console exp:ini where site.ini/SiteSettings/SiteName site
   ```

6. Run the same command for the administration (`... SiteName admin`). Note which file sets the value in each case.
7. Print every scope the installation has:

   ```bash
   php bin/php/console exp:ini scopes
   ```

8. Sign in to the administration of your training site and open **Setup** > **System information**. Find the card
   **Siteaccesses** and compare its count, its default and its match line with steps 2 and 4.
9. Write down, in one sentence each, what the siteaccesses `site`, `admin` and `editor` are for.

## Expected result

- Step 1 shows at least `admin`, `editor` and `site` (and `adminui` when `exp_adminui` is present).
- Step 2 lists the same names; step 4 shows the `MatchOrder` the installer wrote (`uri` for a default
  `exp:install`) and the default siteaccess.
- Step 5 prints a numbered list that starts with `settings/site.ini` (scope `default`) and ends with the file whose
  value is **In effect**.
- Step 8: **System information** names the same number of siteaccesses and the same default.
- The trainer checks by asking two learners to read their `where` output aloud and name the winning file.

![One siteaccess of the installation: the media site on its own address](../../../images/trainers/ms-site-a.webp)

## Quiz

1. Which three parts make a siteaccess?
2. Which list names the siteaccesses that share one database?
   A. `AvailableSiteAccessList[]` B. `RelatedSiteAccessList[]` C. `SiteList[]` D. `MatchOrder`
3. A value is set in `settings/siteaccess/site/` and in `settings/override/`. Which one is in effect for `site`?
4. True or false: you change the default of a setting by editing `settings/site.ini`.
5. Which command shows every file that sets `SiteName` and the value in effect?

<details>
<summary>Answers</summary>

1. A name, a directory of settings in `settings/siteaccess/<name>/`, and a rule that decides which requests it
   answers ([10.2](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
2. **B.** The kernel's comment in `settings/site.ini`: "A list of related siteaccesses sharing the same database".
3. The value in `settings/override/`: the global override loads last and has the last word.
4. **False.** `settings/site.ini` holds the kernel defaults and is replaced on upgrades; set values in an override,
   a siteaccess directory or an extension.
5. `php bin/php/console exp:ini where site.ini/SiteSettings/SiteName` (optionally with the siteaccess as the last
   argument).

</details>

## Further reading

- [10.2 Siteaccesses and site addresses](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses):
  the reference for this module.
- [Changing settings from the command line: exp:ini](../../../../bc/6.0/console-exp-ini.md): every action and scope.
- [Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md): the host's view.
- [Setting up several sites in one installation](../../../creators/website-creators/multisite-setup.md): the
  website creator's view.

## Related pages

- [Module 1: Siteaccesses for several sites](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
