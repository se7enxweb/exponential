---
title: "Lesson 4.2: Siteaccesses for administrators"
audience: trainer, admin
category: training
order: 42
summary: Learners list the siteaccesses of an installation, read how a request reaches each one on System information, compare their settings, and know which matching settings exist for hosts and languages. 40 minutes.
keywords: site administration, siteaccess, AvailableSiteAccessList, RelatedSiteAccessList, MatchOrder, DefaultAccess, host matching, uri matching, HostMatchMethod, language redirect, system information
type: help
---
# Lesson 4.2: Siteaccesses for administrators

Visitors, editors and administrators reach the same installation through different siteaccesses: a name, a directory
of settings and a rule that decides which requests it answers. An administrator does not have to design siteaccesses,
but must be able to read them: which ones exist, how a request reaches each, and which settings differ. This lesson
teaches that reading; building new sites is the subject of the Multisite and site packages course.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 4: Sections, siteaccesses and object states](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Lesson 1.4](../module-01-orientation/the-settings-page.md) and [lesson 1.5](../module-01-orientation/ini-files-and-exp-ini.md) |

## Learning objectives

After this lesson, learners can:

- list the siteaccesses from `settings/siteaccess/` and from `AvailableSiteAccessList[]`;
- explain `MatchOrder`, `DefaultAccess` and `RelatedSiteAccessList[]`;
- read the **Siteaccesses** part of **System information**: the rule that chose a request, the resolution map, one
  card per siteaccess;
- compare the settings of two siteaccesses;
- name the settings for host matching and for the redirect to a visitor's language.

## Trainer's notes

### How to present it

Source: [System information, Siteaccesses](../../../../guides/system-information.md#5a-siteaccesses),
[10.2 Siteaccesses and site addresses](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses) and
[Siteaccess by host prefix, and the redirect to the language](../../../../features/6.0/siteaccess-host-and-language-matching.md).

1. **The definition** (3 minutes). *Siteaccess = name + settings in `settings/siteaccess/<name>/` + a rule.* An
   installation made with `exp:install` has `site`, `admin` and `editor`.
2. **The list and the rules** (5 minutes). In `site.ini [SiteAccessSettings]`: `AvailableSiteAccessList[]` (which may
   answer), `RelatedSiteAccessList[]` (which share the database), `MatchOrder` (the order of the rules, for example
   `uri;host;port`). `[SiteSettings] DefaultAccess` answers when no rule matches.
3. **Reading it in the browser** (4 minutes). **Setup > System information**, part **Siteaccesses**: *This request*,
   the *Resolution map* (rules from the top, first match decides, the default last) and *All siteaccesses* with kind,
   design, languages and the entries that reach it. **Open** opens a siteaccess where a visitor would find it.
4. **Hosts and languages** (3 minutes). With host matching, `HostMatchMethod` (`strict`, `start`, `end`, `part`) lets
   test hosts reach the live siteaccess; `DefaultHostUriMatchMapItems[]` sends `/` to the visitor's language
   (`/ger`, `/eng`). Both are off unless set.

### Common questions

**Is a siteaccess a site?** No. A site may have several siteaccesses (one per language), and the administration and
editor are siteaccesses without being sites.

**Why can the admin sign-in fail on one siteaccess and work on another?** `user/login` policies are limited by
SiteAccess (lesson 3.4).

**Why does the header have "Open the site or another siteaccess"?** It opens the public site, or another siteaccess,
at the address the matching rules give it.

### Pitfalls

- **Changing matching on a live site.** A wrong `MatchOrder` or host map can send visitors to the administration or
  nowhere. Never change it in class except on the lab, and always with a second session open.
- **Editing the wrong directory.** `settings/siteaccess/admin` is the administration; a change there for the public
  site has no effect where expected. Use `exp:ini where` to check.

## Hands-on exercise

On your training installation, signed in as an administrator, with a terminal in the root:

1. List the siteaccess directories:

   ```bash
   ls settings/siteaccess/
   ```

2. Read the list and the rules:

   ```bash
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/AvailableSiteAccessList
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/MatchOrder
   php bin/php/console exp:ini get site.ini/SiteSettings/DefaultAccess
   ```

3. Open **Setup > System information** and the folded part **Siteaccesses**. Note the rule that chose this page.
4. Read the resolution map. Which entry leads to `editor`?
5. Read the card of `site`: its design, its languages and whether it requires a login.
6. Press **Open** on the card of `editor`. Note the address it opens.
7. Open **Setup > Ini settings**, `site.ini` for `site`, and compare it with `admin` (**Compare with siteaccess**).
   Note two differences in `[SiteSettings]` or `[DesignSettings]`.
8. Use the header button **Open the site or another siteaccess** and choose the public site.
9. Read the section *The host map by the start of a host* in the feature page linked below, and write the line that
   would let `www.example.com.test.local` reach the siteaccess of `www.example.com`. Do not save it.

## Expected result

- Step 1 lists at least `site`, `admin` and `editor`.
- Step 3 names the URI element rule for `admin` on a lab installed with URL matching.
- Step 6 opens `/editor` on the lab's address.
- Step 9: `HostMatchMethod=start` (or the entry `HostMatchMapItems[]=www.example.com;site;start`).
- The trainer checks by asking a learner to explain the resolution map aloud.

## Quiz

1. What are the three parts of a siteaccess?
2. Which setting answers when no matching rule fits a request?
   A. `MatchOrder` B. `DefaultAccess` C. `RelatedSiteAccessList[]` D. `SiteURL`
3. What does `RelatedSiteAccessList[]` mean?
4. Where in the administration do you see which rule chose the current request?
5. What does `HostMatchMethod=start` change?

<details>
<summary>Answers</summary>

1. A name, a directory of settings and a matching rule.
2. **B.** `[SiteSettings] DefaultAccess`.
3. The siteaccesses that share the same database and content.
4. **Setup > System information**, part **Siteaccesses**, *This request*
   ([System information](../../../../guides/system-information.md#5a-siteaccesses)).
5. The host map matches a host that begins with the listed host, not only the exact host
   ([Host matching](../../../../features/6.0/siteaccess-host-and-language-matching.md#the-host-map-by-the-start-of-a-host)).

</details>

## Further reading

- [10.2 Siteaccesses and site addresses](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses):
  how matching is set up.
- [Siteaccess by host prefix, and the redirect to the language](../../../../features/6.0/siteaccess-host-and-language-matching.md).
- [Multi-site INI overrides](../../../../features/6.0/multi-site-ini-overrides.md): settings for several sites.
- The course Multisite and site packages goes on from here (see the [Course catalogue](../../course-catalogue.md)).

## Related pages

- [Module 4: Sections, siteaccesses and object states](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
