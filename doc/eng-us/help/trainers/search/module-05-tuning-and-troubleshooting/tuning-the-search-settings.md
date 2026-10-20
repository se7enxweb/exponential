---
title: "Lesson 5.1: Tuning the search settings"
audience: trainer, admin
category: training
order: 51
summary: Learners change the built-in engine's search settings per siteaccess with exp:ini, switch on wildcards with a minimum length, adjust the stop word rule and the result limits, and judge each change by its effect and its cost. 30 minutes.
keywords: search course, tuning, SearchSettings, EnableWildcard, MinCharacterWildcard, StopWordThresholdValue, StopWordThresholdPercent, MaximumSearchLimit, AllowEmptySearch, MaximumSearchTextWords, siteaccess
type: help
---
# Lesson 5.1: Tuning the search settings

The built-in engine has few settings, and each one trades something: wildcards help visitors but cost the database,
the stop word rule removes noise but can hide a word that matters, limits protect the server but cut long searches.
This lesson teaches learners to change them deliberately, per siteaccess, and to undo a change.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 5: Tuning and troubleshooting](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Module 3](../module-03-searching-in-practice/README.md); a terminal in the training installation's root |

## Learning objectives

After this lesson, learners can:

- set a search setting for one siteaccess with `exp:ini set ... siteaccess:<name>` and check it with `exp:ini where`;
- switch on wildcard search with `EnableWildcard` and explain `MinCharacterWildcard`;
- explain and adjust the stop word rule (`StopWordThresholdValue`, `StopWordThresholdPercent`);
- name the limits `MaximumSearchLimit`, `MaximumSearchTextWords`, `MaximumSearchTextLength` and `AllowEmptySearch`
  and why each exists.

## Trainer's notes

### How to present it

Source: `settings/site.ini [SearchSettings]` (its comments), [Site search](../../../creators/website-creators/site-search.md#settings-that-matter)
and [Change settings from the command line: exp:ini](../../../../features/6.0/exp-ini-command.md).

1. **Where to set** (2 minutes). Search settings belong to the siteaccess where people search, usually the public one:
   `settings/siteaccess/<public siteaccess>/site.ini.append.php`, written by `exp:ini set ... siteaccess:<name>`.
   Every change is backed up to `var/backup/ini/<timestamp>/` and recorded in the audit trail.
2. **Wildcards** (3 minutes). `EnableWildcard=true` lets visitors type `salad*`. `MinCharacterWildcard=3` is the
   least number of characters before the `*`. The shipped comment warns that wildcards "might heavily influence the
   performance of the system". Try it on the training site; measure before using it on a large one (lesson 5.4).
3. **Stop words** (2 minutes). Only on sites with more than `StopWordThresholdValue` (100) objects: a word present in
   more than `StopWordThresholdPercent` (60) percent of objects is ignored. Raising the percentage keeps more words;
   lowering it removes more.
4. **Limits** (3 minutes). `MaximumSearchLimit` (30) caps results per request; `MaximumSearchTextWords` (16) and
   `MaximumSearchTextLength` (1024) cut long texts, because each word is a clause of the query and too many make a
   search fail; `AllowEmptySearch` stays `disabled` unless templates add enough limitations themselves.

### Common questions

**Why per siteaccess and not global?** The admin and the public site have different users and needs; a wildcard that
helps visitors may not be wanted in the admin, or the other way round.

**Can I change these in the browser?** The settings page of the administration shows INI settings; this course uses
`exp:ini`, which writes line by line and keeps comments.

**What about ezfind?** With ezfind active its own `site.ini.append.php` already enables wildcards and empty search; its
tuning is in `ezfind.ini` (Module 4).

### Pitfalls

- **Forgetting the INI cache.** Clear it after every change (`ezcache.php --clear-tag=ini`), or nothing seems to
  happen.
- **Global overrides that win.** A value in `settings/override` beats a siteaccess value. `exp:ini where` shows which
  file is in effect.
- **Leaving experiments behind.** End the exercise by removing what you set.

## Hands-on exercise

Use your own training installation. `<public siteaccess>` is its public siteaccess. Add `--allow-root-user` only if
you work as root.

1. Show the wildcard setting and every file that sets it:

   ```bash
   php bin/php/console exp:ini where site.ini/SearchSettings/EnableWildcard
   ```

2. On the public site, as a visitor, search for the first four letters of a word in the sample content followed by
   `*`, for example `heal*`.
3. Switch on wildcards for the public siteaccess and clear the INI cache:

   ```bash
   php bin/php/console exp:ini set site.ini/SearchSettings/EnableWildcard true siteaccess:<public siteaccess>
   php bin/php/ezcache.php --clear-tag=ini
   ```

4. Repeat step 2. Then try two letters and a `*`, for example `he*`.
5. Run step 1 again and read which file now sets the value.
6. Look at the stop word rule: print `StopWordThresholdValue` and `StopWordThresholdPercent` with `exp:ini get`, and
   estimate how many objects your training site has (the sample content plus users and media). Decide whether the
   rule applies on your site.
7. Open `var/backup/ini/` and find the backup of the file you changed in step 3.
8. Undo the change and clear the INI cache:

   ```bash
   php bin/php/console exp:ini rem site.ini/SearchSettings/EnableWildcard siteaccess:<public siteaccess>
   php bin/php/ezcache.php --clear-tag=ini
   ```

## Expected result

- Step 2 finds nothing for the partial word.
- Step 4 finds the items for four letters; for two letters it finds nothing, because `MinCharacterWildcard` is `3`.
- Step 5 lists the siteaccess file with `true` in effect.
- Step 7 shows a dated backup folder with the earlier version of the file.
- After step 8, step 1 shows the shipped `false` in effect again.
- The trainer checks step 8 on every machine.

## Quiz

1. Which command writes a search setting for the public siteaccess only?
2. Wildcards are on, `MinCharacterWildcard=3`. Does `ca*` work? Does `cak*`?
3. On a site with 80 objects, is the stop word rule applied?
   A. yes, always B. no, the threshold value of 100 objects is not exceeded C. only with wildcards D. only with ezfind
4. Why is the number of words in a search text limited?
5. A siteaccess value does not take effect. Name two reasons.

<details>
<summary>Answers</summary>

1. `php bin/php/console exp:ini set site.ini/SearchSettings/<setting> <value> siteaccess:<public siteaccess>`.
2. `ca*` does not (two characters); `cak*` does (three).
3. **B.** The rule only applies above `StopWordThresholdValue` objects (`settings/site.ini` comment).
4. Every word is a clause of the engine's query; with too many the search fails instead of giving results.
5. The INI cache was not cleared; a value in `settings/override` overrides it (check with `exp:ini where`).

</details>

## Further reading

- [Site search](../../../creators/website-creators/site-search.md#settings-that-matter): the settings table.
- [Change settings from the command line: exp:ini](../../../../features/6.0/exp-ini-command.md): scopes, backups and
  safety.
- [The settings page: see where every INI value comes from](../../../../guides/settings-page.md): reading settings in the administration.

## Related pages

- [Module 5: Tuning and troubleshooting](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
