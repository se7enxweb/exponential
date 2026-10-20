---
title: Publishing tasks
audience: government, editor
category: public-sector
order: 10
summary: Step-by-step publishing tasks for editors at councils, agencies, schools and other public bodies - agendas, minutes, notices, alerts, press releases, documents, events, menus, versions, approvals, translations and more - each with prerequisites, steps, a result check and pitfalls.
keywords: publishing tasks, editor, how to, council agenda, minutes, public notice, alert, press release, document, event, menu, versions, approval, translation, tags, RSS
type: help
---
# Publishing tasks

This folder is for editors at public bodies: the people who publish an agenda on Monday, a notice on Tuesday and a
correction on Friday. Each page is one task, with what you need before you start, the steps with the labels the
administration shows, how to check the result, and the pitfalls. The tasks use features documented in this
repository; where Exponential has no feature for something, the page says so and gives the practical alternative.

Some tasks depend on how your site was set up (which classes exist, which workflows run). The class and setting names
used are those of the default installation and of the building guides of this Governments help; ask your web team
where your site differs.

## Contents

1. [Meetings, notices and alerts](#meetings-notices-and-alerts)
2. [News, documents and events](#news-documents-and-events)
3. [Pages, menus and addresses](#pages-menus-and-addresses)
4. [Versions, approval and languages](#versions-approval-and-languages)
5. [Forms, feeds and subscribers](#forms-feeds-and-subscribers)
6. [Upkeep](#upkeep)

## Meetings, notices and alerts

| Task | The answer in one line |
|---|---|
| [Publish a council meeting agenda](publish-a-council-meeting-agenda.md) | Fill the meeting's agenda, relate the papers, publish before the deadline |
| [Publish meeting minutes after approval](publish-meeting-minutes-after-approval.md) | Upload the approved minutes, relate them to the meeting, update its status |
| [Publish a public notice with an expiry date](publish-a-public-notice-with-an-expiry-date.md) | A notice with a hide date that the `hide.php` cronjob uses |
| [Publish an urgent alert on the front page](publish-an-urgent-alert-on-the-front-page.md) | A notice in the folder the front page shows; check through the caches |
| [Correct a published notice and keep the history](correct-a-published-notice-and-keep-the-history.md) | Edit, note the correction, publish; the old text stays in versions |

## News, documents and events

| Task | The answer in one line |
|---|---|
| [Publish a press release](publish-a-press-release.md) | Create, attach images with alternative text, send for approval or publish |
| [Schedule a news item](schedule-a-news-item.md) | A *Wait until date* workflow event holds publication until a date attribute |
| [Upload a PDF report with an accessible summary](upload-a-pdf-report-with-an-accessible-summary.md) | A File object for the report and an HTML summary page that relates it |
| [Add alternative text to images](add-alternative-text-to-images.md) | The image field's alternative text, in the edit form or the upload dialog |
| [Publish a job vacancy](publish-a-job-vacancy.md) | The Job position class with an application deadline |
| [Publish a tender notice](publish-a-tender-notice.md) | A notice with documents, a deadline and a link to the procurement platform |
| [Publish a consultation and collect responses](publish-a-consultation-and-collect-responses.md) | A consultation with collector fields; responses in **Setup > Collected information** |
| [Publish an event](publish-an-event.md) | **Create new** > **Event** in the calendar, with times, place and accessibility |
| [Cancel or postpone an event](cancel-or-postpone-an-event.md) | Keep the page, change the status and dates, tell people |
| [Publish a photo gallery](publish-a-photo-gallery.md) | A Gallery with images uploaded in one go, each with alternative text |
| [Embed a video with captions and transcript](embed-a-video-with-captions-and-transcript.md) | Captions and a transcript on the page, the video embedded or linked |
| [Publish a data table](publish-a-data-table.md) | An accessible table in rich text, plus the data as a CSV file |
| [Publish a handbook as a PDF export](publish-a-handbook-as-a-pdf-export.md) | **Setup > PDF export** of the handbook's subtree |
| [Upload many files at once](upload-many-files-at-once.md) | The upload page: many files, titles, **Keep as drafts** |
| [Replace a document with a new version](replace-a-document-with-a-new-version.md) | Edit the File object, choose the new file, publish; the address stays |

## Pages, menus and addresses

| Task | The answer in one line |
|---|---|
| [Publish a service page](publish-a-service-page.md) | The Service class: who, cost, how to apply, contact, review date |
| [Update opening hours or contact details](update-opening-hours-or-contact-details.md) | Edit the contact point once; every service that relates it shows the change |
| [Publish a profile of an official](publish-a-profile-of-an-official.md) | Public role information only, with consent for the photo |
| [Add a page to the menu](add-a-page-to-the-menu.md) | A menu class directly below the right folder; order by priority |
| [Move a page](move-a-page.md) | **Move**, choose the new parent; the old address redirects |
| [Hide a page temporarily](hide-a-page-temporarily.md) | **Hide / unhide**; nothing below it is shown either |
| [Remove an outdated page and restore it](remove-an-outdated-page-and-restore-it.md) | **Remove** moves it to the trash; **Restore** puts it back |
| [Add a related documents list](add-a-related-documents-list.md) | A relation field or embeds in the text, never copies |
| [Tag content with eztags](tag-content-with-eztags.md) | Type in the Tags field, choose from the suggestions |
| [Add a link to an external service](add-a-link-to-an-external-service.md) | A clear link text, the external address, a note that it leaves the site |
| [Create a landing page with layouts](create-a-landing-page-with-layouts.md) | **Layouts**, **New layout**, add blocks, map it to the page, publish |
| [Reorder pages in a folder](reorder-pages-in-a-folder.md) | Sort by priority and set priorities, or drag in the sub-items list |
| [Give a page a short address](give-a-page-a-short-address.md) | The **URL aliases** page of the node: **Create new alias** |
| [Redirect old addresses after a reorganization](redirect-old-addresses-after-a-reorganization.md) | Aliases for single pages, **URL wildcards** for groups |
| [Publish one item in several places](publish-one-item-in-several-places.md) | **Add locations**; one object, several addresses |
| [Copy a page as a starting point](copy-a-page-as-a-starting-point.md) | **Copy**, then edit the copy; nothing links to it yet |
| [Publish a frequently asked questions page](publish-a-frequently-asked-questions-page.md) | Questions as headings, short answers, links to the full pages |

## Versions, approval and languages

| Task | The answer in one line |
|---|---|
| [Preview before publishing](preview-before-publishing.md) | **Preview** in the edit form, in the siteaccess and device size you choose |
| [Send a page for approval](send-a-page-for-approval.md) | **Send for publishing** in an approval section; follow it in **My pending items** |
| [Approve or reject a page](approve-or-reject-a-page.md) | **My account > Collaboration**, open the request, **Approve** or **Deny** |
| [Translate a page into another language](translate-a-page-into-another-language.md) | **New translation**, translate with the original beside it, publish |
| [Compare two versions](compare-two-versions.md) | **Manage versions**, **Compare** or the two pickers |
| [Restore an earlier version](restore-an-earlier-version.md) | **Make a new draft** from the old version, then publish it |
| [Check who changed a page](check-who-changed-a-page.md) | The versions page and the **Audit** tab |
| [Recover a lost draft](recover-a-lost-draft.md) | **My drafts**, then **Edit** |
| [Edit many items at once](edit-many-items-at-once.md) | **More actions** > **Edit selected**, then **Publish all** |
| [Mark content under review with object states](mark-content-under-review-with-object-states.md) | **Details** tab, **Content state**, **Set states** |
| [Write a plain-language summary](write-a-plain-language-summary.md) | Three sentences: what, who, what to do |
| [Set a meta description and social image](set-a-meta-description-and-social-image.md) | The Meta Data field of the item |

## Forms, feeds and subscribers

| Task | The answer in one line |
|---|---|
| [Create an RSS feed of notices](create-an-rss-feed-of-notices.md) | **Setup > RSS**, **New export**, source the Notices folder |
| [Publish without notifying subscribers](publish-without-notifying-subscribers.md) | **Publish without notification**, where the site offers it |
| [Follow changes with notifications](follow-changes-with-notifications.md) | **Notify me** on a folder; digests in **My notification settings** |
| [Prepare a newsletter edition](prepare-a-newsletter-edition.md) | A cjw_newsletter edition, a test send, then scheduling |
| [Publish a poll](publish-a-poll.md) | **Create new** > **Poll**, options, results page |
| [Export form responses to CSV](export-form-responses-to-csv.md) | **Setup > Collected information**, **Export CSV** |
| [Remove form responses that are no longer needed](remove-form-responses-that-are-no-longer-needed.md) | **Remove selected** after export, on schedule |

## Upkeep

| Task | The answer in one line |
|---|---|
| [Find and fix broken links](find-and-fix-broken-links.md) | **Setup > Link management**, then the pages that use a broken link |
| [Refresh a page that shows old content](refresh-a-page-that-shows-old-content.md) | **Clear cache** on the node; ask the web team about other caches |

Up: [Governments: start here](../../government-start-here.md). For planning, read [Levels of government](../../levels/README.md);
for setting things up, the [building guides](../../building/README.md).

## Related pages

- [Creating and editing content](../../../editing-content.md)
- [Publishing, versions and the trash](../../../publishing-and-versions.md)
- [Editors 101 course](../../../trainers/editors-101/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
