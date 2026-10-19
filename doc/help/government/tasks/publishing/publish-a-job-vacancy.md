---
title: Publish a job vacancy
audience: government, editor
category: public-sector
order: 110
summary: For HR and web editors - publish a vacancy with the Job position class shipped in the default package (or an Article where it is not installed), with the application deadline, how to apply, and taking it down after the deadline.
keywords: job vacancy, jobs, recruitment, Job position, ng_job_position, application deadline, engagement type, how to apply, hide after deadline
type: help
---
# Publish a job vacancy

This page is for editors in HR or communications who publish vacancies. The default site package installs a
**Job position** class (`ng_job_position`) with Location, Engagement type, Application deadline, Intro text, Job
overview, Requirements and Responsibilities ([The demo content packages](../../../site-packages/sevenx-multisite/the-demo-content-packages.md)).
If your site does not have it, use an Article in a Jobs folder with the same headings.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [After the deadline](#after-the-deadline)
5. [Pitfalls](#pitfalls)

## Before you start

- The vacancy text is approved by HR.
- You know how people apply: an e-recruitment system (link), or a mailbox.
- You know the closing date and time.

## Steps

1. Open the **Jobs** folder, **Create new**, choose **Job position**.
2. **Title**: the job title as people search for it. **Teaser title** for lists if the title is long.
3. **Location**, **Engagement type** (full time, part time, fixed term).
4. **Application deadline**: date and time.
5. **Intro text**: two sentences about the role. **Job overview**, **Requirements**, **Responsibilities**: in plain
   language, as lists.
6. Put how to apply in the overview or the intro, with the link to the recruitment system or the mailbox; name the
   contact as a role ("the recruitment team").
7. **Preview**, then **Send for publishing**.

## Check the result

- [ ] Signed out, the vacancy appears in the Jobs list with its deadline.
- [ ] The application link opens the right form.
- [ ] A jobs RSS feed, if you have one, shows it when the feed is next written.

## After the deadline

| Option | How |
|---|---|
| Automatic | if the web team set `hide.php` on the class's deadline attribute (`HideDateAttributeList[ng_job_position]=application_deadline`), the vacancy is hidden at the next run after the deadline ([Archive and expiry](../../building/news-and-alerts/archive-and-expiry.md)) |
| By hand | **Hide / unhide** on the deadline day ([Hide a page temporarily](hide-a-page-temporarily.md)) |

Keep closed vacancies hidden rather than removed if your records rules ask for it.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Asking for CVs by an open mailbox in the text | spam; prefer the recruitment system or a protected form |
| Collecting applications with a website form | applications are personal data that belong in the recruitment system |
| An accessible job ad with an inaccessible application route | test the application system too |
| Forgetting to take it down | set the deadline attribute and automatic hiding |

## Related pages

- [Publishing tasks](README.md)
- [Publish a tender notice](publish-a-tender-notice.md)
- [Creating and editing content](../../../editing-content.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
