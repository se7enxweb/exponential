---
title: "Lesson 3.6: Multisite, practical task"
audience: trainer, admin, developer, hosting
category: training
order: 36
summary: A 50-minute practical task for the end of the Multisite and site packages course, read the siteaccess setup of a training installation, switch it to host matching with test host names, prove with curl which siteaccess answers, write a one-or-several-installations recommendation and restore the original settings, scored with a five-criterion rubric. 75 minutes with scoring.
keywords: assessments and practical exercises, multisite, practical task, rubric, siteaccess, MatchOrder, HostMatchMapItems, SiteURL, curl Host header, exp:ini, recommendation
type: help
---
# Lesson 3.6: Multisite, practical task

A second site starts with a matching rule. In this task the learner reads how their training installation finds its
siteaccesses, switches it to host matching with test host names, proves the result from the shell without touching
DNS, and puts everything back. A short written recommendation checks the judgment part of the course.

> **Note**
> This is the trainer's own assessment, not an official certification. Learners change the settings of their own
> training installation only. The test host names end in `.test`, which never reaches the public internet.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 3: Assessing the administration courses](README.md) |
| Duration | 75 minutes (5 briefing, 50 task, 20 scoring) |
| Level | Advanced |
| Audience | Learners at the end of the Multisite and site packages course |
| Prerequisites | [Lesson 3.5](multisite-written-test.md); a shell on the learner's own training installation, served on a local port |

## Learning objectives

Learners who pass this task have shown that they can:

- list the siteaccesses and matching rules of an installation;
- configure host matching with a host map and the matching `SiteURL` values;
- prove which siteaccess answers a host name with `curl`;
- back up and restore settings files safely;
- recommend one installation or several for a given case, with reasons.

## Trainer's notes

### How to run it

1. **Before**: one training installation per learner, installed with URL access (the default), served on a local port
   as in [Serving the lab sites](../../train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md). Note the
   names of the public and admin siteaccesses of the site package you use.
2. **Briefing**: hand out the steps and the rubric; 50 minutes.
3. **Scoring**: read the learner's notes with the `curl` outputs, and check that the settings files match their
   backups at the end.

### Common questions

**Why `curl -H 'Host: ...'` and not a browser?** The header lets the learner choose the host name without editing DNS
or the hosts file of the machine. The installation sees the same thing a browser would send.

**Does Velocity need a restart after the change?** If the site is served by Velocity, yes: it keeps settings in memory
([Caches](../../../../guides/caches.md#in-short)). Count it as part of step 6.

### Pitfalls

- **Overwriting the override file.** Learners must back it up first (step 2). Restoring from the backup is scored.
- **Upper case or a port in the map.** The host name must match exactly, in lower case and without a port; a typo
  gives the same siteaccess for both names, which is a good teaching moment.

## Hands-on exercise

The task, as handed to learners. Work in the installation root of your own training installation; write down every
command and its output.

1. List the current siteaccess settings:

   ```bash
   ./console exp:ini list site.ini/SiteAccessSettings --allow-root-user
   ```

   Write down `MatchOrder`, `AvailableSiteAccessList[]` and which siteaccess is public and which is the administration.
2. Back up `settings/override/site.ini.append.php` and the two `settings/siteaccess/<name>/site.ini.append.php` files
   of the public and admin siteaccess to a directory of your own under `var/`.
3. Add host matching to `settings/override/site.ini.append.php`, keeping URL matching as a fallback:

   ```ini
   [SiteAccessSettings]
   MatchOrder=host;uri
   HostMatchType=map
   HostMatchMapItems[]
   HostMatchMapItems[]=www.training.test;<public siteaccess>
   HostMatchMapItems[]=admin.training.test;<admin siteaccess>
   ```

4. Set `[SiteSettings] SiteURL` to `www.training.test` in the public siteaccess's file and `admin.training.test` in the
   admin siteaccess's file.
5. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
6. If the site runs on Velocity, restart it. Then test both names against your local port:

   ```bash
   curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' -H 'Host: admin.training.test' http://127.0.0.1:<port>/
   curl -s -o /dev/null -w '%{http_code}\n' -H 'Host: www.training.test' http://127.0.0.1:<port>/
   ```

7. Test an unlisted name, `-H 'Host: other.training.test'`, and write down which siteaccess answered and why.
8. Write a recommendation of five to eight sentences for this case: *A school runs a main website and a separate site
   for its alumni association. The same staff edit both, and the alumni site reuses news items from the main site.*
   One installation with two siteaccesses, or two installations?
9. Restore the three files from your backups, clear the INI cache (restart Velocity if it serves the site), and check
   that `MatchOrder` is back to what you noted in step 1.

## Expected result

- Notes with the original settings, the backups' location and the `curl` outputs.
- The admin host answered with a redirect to the login page (or the login page), the public host with `200`; the
  unlisted name fell back to URL matching and reached the default siteaccess.
- A recommendation for one installation with two siteaccesses, because content and editors are shared, mentioning
  what would change the answer (separate customers, isolation, separate backups).
- After step 9, `MatchOrder` and `SiteURL` are as they were.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Reading the setup | Nothing noted | MatchOrder only | Siteaccesses noted, roles unclear | MatchOrder, list and roles noted | The notes |
| Host map and SiteURL | Not written | Map with errors (case, port) | Map right, SiteURL missing | Map and both SiteURL values right | The settings files (before restore), the notes |
| Proof with curl | No test | One host tested | Both tested, unlisted name not explained | Both tested, fallback explained | The `curl` outputs |
| Recommendation | Missing | Answer without reasons | Reasons, no condition that would change it | Reasons and the conditions that would change it | The text |
| Backup and restore (must) | No backup | Backup, not restored | Restored, cache not cleared | Restored, cache cleared, checked | The notes, `exp:ini list` |

**Pass**: 10 of 15 points or more and **Backup and restore** at 2 or 3.

## Quiz

Debrief questions to ask after scoring.

1. Why did `MatchOrder=host;uri` keep the site working for addresses not in the map?
2. On a real server, what else must know the new host names?
3. What would you add if the site sat behind a proxy on another machine?
4. Which setting would you change if each site needed its own INI cache?

<details>
<summary>Answers</summary>

1. When no host matches, the next method, `uri`, is tried
   ([10.2](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
2. The web server or Velocity, DNS and the certificate
   ([Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md#matching-by-url-host-or-port)).
3. Its address in `[HTTPHeaderSettings] TrustedProxies[]`
   ([10.2](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
4. `[FileSettings] INICacheDir=site`
   ([Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md#logs-and-caches-per-site)).

</details>

## Further reading

- [10.2 Siteaccesses and site addresses](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses):
  the commands used in the task.
- [Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md): the decision table.
- [Site packages](../../../hosting/provisioning/site-packages.md): installing a multisite package from the start.

## Related pages

- [Lesson 3.5: Multisite, written test](multisite-written-test.md)
- [Module 3: Assessing the administration courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
