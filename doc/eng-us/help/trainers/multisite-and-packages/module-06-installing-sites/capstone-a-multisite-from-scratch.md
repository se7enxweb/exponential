---
title: "Lesson 6.5: Capstone, a multisite from scratch"
audience: trainer, admin, developer
category: training
order: 65
summary: Learners build a two-brand multisite on a fresh installation on their own, from exp:install to host matching, a language redirect, a site extension, logs per site and a content package, and the trainer checks it against an eight-point checklist. 90 minutes.
keywords: multisite and site packages, capstone, practical task, exp:install, sevenx_multisite_clean, host matching, DefaultHostUriMatchMapItems, site extension, exp:ini move-all, UseGlobalLogDir, ezpm, checklist
type: help
---
# Lesson 6.5: Capstone, a multisite from scratch

The last lesson brings the course together. Each learner builds, on their own and from an empty directory, a small
multisite for a brief, using the commands of modules 1 to 6. The trainer helps only when asked and checks the result
against a checklist at the end. The checklist is the trainer's own assessment, not an official certification; the
partner program's certifications are separate ([Certifications](../../../partners/certifications.md)).

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 6: Installing sites with the kickstarter and exp:install](README.md) |
| Duration | 90 minutes (10 briefing, 70 work, 10 checking) |
| Level | Advanced |
| Audience | Learners who completed modules 1 to 5 and lessons 6.1 to 6.4 |
| Prerequisites | A fresh code directory per learner; the course pages open for reference |

## Learning objectives

After this lesson, learners can:

- plan and install a multisite on their own with `exp:install`;
- add a site with its own host and a language siteaccess with a redirect to the visitor's language;
- keep that site's settings in a site extension, with logs of its own;
- package the site's content and prove each step with a command or a request.

## Trainer's notes

### How to present it

1. **The brief** (5 minutes). Read it aloud and put it on the projector:

   *A cooperative runs two brands from one office: "North" at `north.localhost` in English, and "South" at
   `south.localhost` in English (`/en`) and German (`/de`), with visitors of `south.localhost/` sent to their language.
   South's settings live in its own extension `site_south`, its logs in a directory of their own. At the end, the
   content of South is exported as a package for the agency that will translate it.*
2. **Rules** (3 minutes). Work alone; use the course pages and `--help`; write down every command in a text file
   (`capstone-commands.txt`), because the commands are part of the result. Addresses at `example.invalid` only.
3. **Hints, only when asked**. Point to the lesson, not the answer: install (6.2), second site (1.3), language
   siteaccess (1.4), host matching (2.2), host and path (2.3), redirect (2.4), site extension (3.1), logs (3.3),
   package (5.3).
4. **Checking** (10 minutes). Walk the checklist below with each learner, or let pairs check each other with it and
   check two learners yourself.

### Common questions

**May I use `sevenx_multisite` instead of the clean package?** Yes; the clean package keeps the tree small, which
makes the checks quicker.

**Must North and South be separate folders?** Yes: each brand has a root folder of its own, as in lesson 1.3.

**What if I run out of time?** Stop where you are and check the points you reached; the commands file shows what was
planned.

**Is the checklist graded?** That is the trainer's decision; the checklist is the trainer's own assessment, not an
official certification; the partner program's certifications are separate
([Certifications](../../../partners/certifications.md)).

### Pitfalls

- **Starting in the wrong directory.** The capstone needs a fresh directory, never the installation of earlier
  modules.
- **Host map before the siteaccesses exist.** Create and register the siteaccesses first, then match them.
- **Forgetting caches.** Clear the INI cache after each settings change (and restart Velocity if it serves the lab).

## Hands-on exercise

The steps are the brief broken down; learners choose the commands.

1. Install with `exp:install` on SQLite, URL matching, the package `sevenx_multisite_clean`, the title "Cooperative"
   and `--email=webmaster@example.invalid`. Change the administrator password and delete the password file.
2. Create the folders `North` and `South` below the top folder and note their node IDs.
3. Create the siteaccesses `north`, `south_en` and `south_de` (copies of `site`), each with its `SiteName`,
   `IndexPage`, `DefaultPage` and `RootNode`; `south_de` with German regional settings and `eng-US` as fallback.
4. Register them in `AvailableSiteAccessList[]`, `RelatedSiteAccessList[]` and `SiteList[]`.
5. Match `north.localhost` to `north` with the host probe, and `south.localhost` with `en` and `de` to the two South
   siteaccesses with `host_uri`.
6. Send visitors of `south.localhost/` to `/de` when their browser wants German and to `/en` otherwise.
7. Move the settings of `south_en` and `south_de` into a new, activated extension `site_south`.
8. Give South's siteaccesses a log directory of their own.
9. Package the subtree `South` without classes and templates and export it to `var/tmp/capstone/`.
10. Serve the directory and run the checks of the checklist with `curl`.

## Expected result

The trainer's checklist; each point is proved by the command or request in brackets.

1. The installation answers, and the administrator password file is gone
   (`ls var/log/initial-admin-password` fails).
2. `curl -s -H 'Host: north.localhost' <lab address>/` shows the name *North*.
3. `curl -s -H 'Host: south.localhost' <lab address>/en` shows South in English, `/de` in German.
4. `curl -sI -H 'Host: south.localhost' -H 'Accept-Language: de' <lab address>/` answers `302` to `/de`, and with
   `Accept-Language: en` to `/en`.
5. `exp:ini where site.ini/SiteSettings/SiteName south_de` names a file in `extension/site_south/`.
6. A request to `south.localhost/en/no-such-page` writes its line into South's own `404.log`.
7. `php ezpm.php validate var/tmp/capstone/<file>.ezpkg` reports the package valid.
8. `capstone-commands.txt` lists the commands in order and contains no password.

## Quiz

1. In which order did you have to create siteaccesses, register them and match them, and why?
2. Which probe matched `south.localhost/de`?
   A. `uri` B. `host` C. `host_uri` D. `port`
3. Why did `south.localhost/` need a redirect entry, and not a `HostUriMatchMapItems[]` entry with an empty path?
4. Where does the value of `SiteName` for `south_de` come from after step 7?
5. Name one thing you would do differently on a live server.

<details>
<summary>Answers</summary>

1. Create, register, then match: a probe that finds a name not in `AvailableSiteAccessList[]` does not count.
2. **C.**
3. An entry with an empty path would take `/` for one siteaccess for every visitor; the redirect chooses by the
   browser's language ([Lesson 2.4](../module-02-matching-requests/the-redirect-to-the-language.md)).
4. From `extension/site_south/settings/siteaccess/south_de/site.ini.append.php`, unless a file loaded later sets it.
5. For example: real host names with DNS, certificates and `SiteURL`; a database server; a backup before every
   reinstall; logs and caches planned for many sites; `.ezpkg` files kept outside the document root.

</details>

## Further reading

- The whole course: [Multisite and site packages: course overview](../README.md).
- [Setting up several sites in one installation](../../../creators/website-creators/multisite-setup.md).
- [Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md).
- [10. After installing](../../../../install/10-after-installing.md).

## Related pages

- [Module 6: Installing sites with the kickstarter and exp:install](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
