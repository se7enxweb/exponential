---
title: Council meetings and local services
audience: government, project-manager, website-creator, admin
category: public-sector
order: 70
summary: How a municipality publishes its meeting cycle in Exponential - calendar, agenda and papers, the meeting itself, minutes after approval, decisions - and how it keeps service pages correct with owners, review dates and a monthly check.
keywords: council meetings, agenda, meeting papers, minutes, decisions, committee, meeting cycle, service pages, review date, content ownership, local services
type: help
---
# Council meetings and local services

This page is for web officers, democratic services staff and website creators at a city, town or council. It covers
the two things that set a municipal site apart: the meeting cycle of the council, and the upkeep of many service pages
by a small team. It uses the Meeting and Service classes proposed in
[Content model for municipal and local government](content-model.md); every product feature named is documented in
the page linked next to it.

## Contents

1. [The meeting cycle](#the-meeting-cycle)
2. [Publishing each stage](#publishing-each-stage)
3. [Corrections and the record](#corrections-and-the-record)
4. [Keeping service pages correct](#keeping-service-pages-correct)
5. [A monthly routine](#a-monthly-routine)

## The meeting cycle

| Stage | Typical timing (set by your local rules) | What is published |
|---|---|---|
| Calendar | at the start of the year | a Meeting object per meeting, status *scheduled* |
| Agenda and papers | a number of days before the meeting | agenda text, papers as File objects related to the meeting |
| Meeting | the day | a link to a recording or stream if there is one |
| Draft minutes | after the meeting, if your rules publish drafts | a File object marked as draft |
| Approved minutes | after approval at the next meeting | the File object of the approved minutes |
| Decisions | after the meeting | Decision objects or a decisions list on the meeting page |

Folders: `Council and democracy > Meetings > <year>`, one Meeting object per meeting, its papers stored in a
`Papers` folder below it or in the media library and related from the meeting.

## Publishing each stage

1. **Calendar.** Create the meetings of the year with **Create multiple new** (up to 50 at once) below the year folder
   ([Content model and editing, 6](../../../../guides/content-model-and-editing.md#6-edit-many-items-in-one-form-multi-node-edit)).
2. **Agenda.** Edit the meeting: fill the agenda, add papers with **Upload new** in the Papers field (each upload
   becomes a File object), set the status to *agenda published*, press **Send for publishing**.
3. **Embargoed papers.** If papers may only appear at a set time, a *Wait until date* workflow event on the Meeting
   class can hold the version until a date attribute is reached; the workflow cronjob then publishes it
   ([Workflows](../../../../guides/workflows.md)). Test it before relying on it.
4. **Minutes.** Upload the approved minutes as a File object, relate it in the meeting's Minutes field, set the status
   to *minutes approved*, publish.
5. **Notify.** Residents who follow the Meetings folder get a notification mail when the notification cronjob runs
   ([Notifications](../../../../guides/notifications-administrator.md)); an RSS export of the folder serves feed
   readers ([RSS feeds](../../../../guides/rss-feeds.md)).

Papers are often produced by a committee management system. Uploading its PDFs is fine; make sure each paper also has
a meaningful name, and that the agenda text on the page is readable without opening the PDFs.

## Corrections and the record

| Situation | Do |
|---|---|
| A typo in the agenda page | edit and publish; the earlier version stays in the version history |
| A wrong paper was published | upload the correct file as a new version of the same File object, and add a line "Corrected on <date>" to the agenda |
| A paper must be withdrawn (for example personal data published by mistake) | hide or remove the File object at once; check old versions too, because an archived version still holds the file, and remove those versions in **Manage versions** ([Versions of an object](../../../../guides/content-history.md)) |
| Minutes approved with changes | publish the approved text; keep the draft as an archived version or a clearly labeled file, as your rules require |

Removing versions is final. Agree with your records officer before removing anything from the record; a mistaken
publication of personal data usually also needs your data protection officer.

## Keeping service pages correct

| Measure | How |
|---|---|
| An owner per page | the department's section, and the owner field of the object ([Changing owners](../../../../guides/changing-owners.md)) |
| A review date | the required `review_date` attribute of the Service class |
| One source for contact data | Contact point objects related from service pages |
| One source for fees | fees that change yearly in one place (a page or a related object), not copied into many pages |
| Feedback | a short "Was this page useful?" form ([Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md)) |

## A monthly routine

- [ ] List services whose review date has passed; send each owner their list.
- [ ] Check **Setup > Collected information** for new feedback; act on it, then remove what is no longer needed.
- [ ] Run or read the link check (**Setup > Link management**, the `linkcheck.php` cronjob in the infrequent part).
- [ ] Look at **Search statistics** for searches without results and add the missing words to the pages.
- [ ] Look at **My pending items** and **Setup > Workflow processes** for approvals that wait too long.

## Related pages

- [Municipal and local government](README.md)
- [Collected information](../../../../guides/collected-information.md)
- [Link and content checks](../../../creators/website-creators/link-and-content-checks.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
