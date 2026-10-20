---
title: "Lesson 6.6: Capstone, a multisite launch with a newsletter"
audience: trainer, admin, developer, editor
category: training
order: 66
summary: A one-and-a-half-day capstone for administrators, add a second site for an invented organization to a training installation with its own siteaccess and host name, keep search current for both, launch a newsletter list with double opt-in and a first edition sent to files only, and write the launch checklist, scored with a seven-criterion rubric. One and a half days plus presentations.
keywords: assessments and practical exercises, capstone, multisite, siteaccess, host matching, SiteURL, search, newsletter, cjw_newsletter, file transport, example.invalid, launch checklist, rubric
type: help
---
# Lesson 6.6: Capstone, a multisite launch with a newsletter

An invented organization adds a second site to its installation and launches it with a newsletter. The learner sets
up the siteaccess, keeps search working for both sites, and runs the first newsletter cycle, entirely to files.

> **Note**
> This is the trainer's own assessment, not an official certification. **No mail is sent.** Every mail transport of
> the training installation, the site's `Transport` and all three newsletter `TransportMethod*`, is `file`, and every
> subscriber has an address at `example.invalid`. Test host names end in `.test`.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 6: Capstone projects](README.md) |
| Duration | One and a half days: 15 minutes briefing, about 7 hours work with two checkpoints, 15 minutes presentation per learner, 45 minutes scoring |
| Level | Advanced |
| Audience | Learners at the end of Multisite and site packages and Newsletters (Search helps) |
| Prerequisites | Those courses; a training installation with the newsletter extension, a shell, served on a local port |

## Learning objectives

Learners who pass this capstone have shown that they can:

- add a public siteaccess for a second site with its own content root and host name;
- prove which siteaccess answers which host;
- keep search results correct for both sites;
- run a newsletter launch with double opt-in and a first edition, without sending mail;
- write a launch checklist that covers DNS, certificates, mail sender and consent.

## Trainer's notes

### How to run it

1. **Before**: one training installation per learner with the newsletter extension and its tree, all transports
   `file` (set by you), no crontab. Decide whether learners create the second siteaccess by hand or from a multisite
   site package your course used ([Site packages](../../../hosting/provisioning/site-packages.md)).
2. **Checkpoints**: day 1 midday (second siteaccess answers its test host), end of day 1 (search and the newsletter
   list with confirmed test subscribers).
3. **Presentation**: the learner shows both hosts with `curl`, a search on each site, the newsletter files and the
   launch checklist.

### Pitfalls

- **A transport left at `sendmail`.** Check all transports yourself before day 1; a learner's check is the second
  safeguard, not the first.
- **Search mixing both sites.** Learners may need the course's material on limiting search to a subtree; accept any
  documented way.

## Hands-on exercise

The brief, as handed to learners.

*The Lakeside Arts Festival (an invented festival) runs its main site on your installation and now launches a second
site for its youth program, with a newsletter for parents.*

1. Check every mail transport (site and newsletter) and write the values down. Do not continue unless all are `file`.
2. Create a content root for the youth site with five items.
3. Add a public siteaccess for it, with its own `SiteURL`, and host matching for `www.festival.test` (main) and
   `youth.festival.test` (youth), keeping URL matching as a fallback.
4. Prove with `curl` and the `Host` header which siteaccess answers each name and an unlisted one.
5. Make search on the youth site find the youth content, and check that a new item is found on both sites as it
   should be.
6. Create a newsletter list for parents; subscribe three test subscribers at `example.invalid` and confirm them from
   the mail files.
7. Write and send the first edition through the two cron steps run by hand; read the edition files.
8. Unsubscribe one subscriber and note the state.
9. Write the launch checklist for going live for real: DNS, web server or Velocity host names, certificate, the
   newsletter's real transport and sender domain with SPF, consent records, bounce handling, cron lines, monitoring.
10. Restore the installation's original matching settings at the end, or leave them as your trainer instructs.

## Expected result

- Notes with all transports at `file`; `curl` outputs showing the right siteaccess per host.
- Search results correct on both sites.
- The parents list with two confirmed and one removed subscriber; edition files in the mail directory.
- A launch checklist that would let an administrator go live safely.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Mail safety (must) | A transport not `file` or a real address | Checked, not all | All `file`, not recorded | All checked and recorded, only `example.invalid` | Notes |
| Second siteaccess | None | Created, does not answer | Answers, `SiteURL` wrong | Answers with own root and `SiteURL` | `curl`, settings |
| Host matching proof | None | One host | Both, fallback unexplained | Both and the fallback explained | `curl` outputs |
| Search | Not checked | Mixed results | Correct on one site | Correct on both, new item checked | Searches |
| Newsletter cycle | None | List only | Subscribed and confirmed, edition not sent | Confirmed, sent by cron to files, unsubscribe done | Mail directory, list |
| Launch checklist | None | A few items | Most items, mail or consent missing | DNS, host names, certificate, mail sender and SPF, consent, bounces, cron, monitoring | Checklist |
| Clean-up | Settings left broken | Partly restored | Restored, not checked | Restored or left as instructed, checked | `exp:ini list` |

**Pass**: 14 of 21 points or more and **Mail safety** at 3.

## Quiz

Debrief questions after the presentations.

1. Which three things must know a new host name besides the settings?
2. Which newsletter transport is `sendmail` as shipped?
3. Why does the launch checklist name SPF for every address family the server sends from?
4. What does a parent's double opt-in protect?

<details>
<summary>Answers</summary>

1. The web server or Velocity, DNS and the certificate
   ([Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md#matching-by-url-host-or-port)).
2. `TransportMethodPreview`, for test sends ([10.5](../../../../install/10-after-installing.md#newsletters-cjw_newsletter)).
3. Large providers reject mail from an address family the SPF record does not list
   ([10.4](../../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending)).
4. That only people who really asked for the newsletter receive it
   ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md)).

</details>

## Further reading

- [Multisite, practical task](../module-03-administration-courses/multisite-practical-task.md) and
  [Newsletters, practical task](../module-05-newsletter-and-migration/newsletter-practical-task.md)
- [Mail consent and bounces](../../../hosting/operations/mail-consent-and-bounces.md)
- [New customer site go-live checklist](../../../hosting/checklists/new-customer-site-go-live.md)

## Related pages

- [Module 6: Capstone projects](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
