---
title: Guidance for regulated parties
audience: government, project-manager, website-creator, editor
category: public-sector
order: 70
summary: How an agency publishes guidance that regulated parties can rely on in Exponential - one stable address, effective dates, change notes, status as an object state, legal review, notifications and RSS for changes, and handling superseded guidance.
keywords: guidance, regulated parties, compliance, effective date, change note, superseded, withdrawn, stable URL, notifications, RSS, legal review, versions
type: help
---
# Guidance for regulated parties

This page is for project managers, website creators and editors at an agency that publishes guidance others must
follow. It describes a publishing practice built from Exponential features: the Guidance class and the status state
group of [Content model for agencies](content-model.md), the review workflow of
[Structure, sections and roles for agencies](structure-sections-and-roles.md), and the versions page.

## Contents

1. [Principles](#principles)
2. [Publishing new guidance](#publishing-new-guidance)
3. [Changing guidance](#changing-guidance)
4. [Superseding and withdrawing](#superseding-and-withdrawing)
5. [Telling regulated parties](#telling-regulated-parties)
6. [Answering "what did it say then?"](#answering-what-did-it-say-then)

## Principles

| Principle | How |
|---|---|
| One stable address per piece of guidance | one object; extra locations for other menus; never a copy |
| Effective date visible | the `effective_from` attribute, shown at the top |
| Status visible | the `guidance_status` state, shown at the top and in lists |
| Changes explained | the `change_note` attribute, one line per version |
| Reviewed before publishing | the *Approve* event for the guidance section |
| Changes announced | notifications and an RSS feed of the guidance library |

## Publishing new guidance

1. The author creates a Guidance object in the Guidance library, fills reference, summary, body, effective date and
   tags, and writes "First version" as the change note.
2. **Send for publishing**. The version becomes pending; the reviewer gets a collaboration item.
3. The reviewer opens it in **My account > Collaboration**, reads the preview and presses **Approve**, or **Deny**
   with a comment ([Workflows](../../../../guides/workflows.md)).
4. After the workflow cronjob runs, the guidance is published with the default state *In force*.
5. The web team adds extra locations under the audience folders that should list it (edit, **Locations**, **Add
   locations**).

## Changing guidance

1. The author presses **Edit**; a new draft is made from the published version.
2. The author changes the text, updates the effective date if it changes, and replaces the change note with this
   version's note, for example "Section 3 updated: new deadline for annual returns."
3. Review and publication as above. The previous version becomes *archived* and stays on the versions page.
4. A reviewer can use **Compare** on the versions page to see every change before approving
   ([Versions of an object](../../../../guides/content-history.md)).

Minor corrections (a typo, a broken link) follow the same path; agree whether they need review. **Publish without
notification** can keep minor corrections from mailing subscribers where it is enabled
([Publish without notification](../../../../features/6.0/publish-without-notification.md)).

## Superseding and withdrawing

| Situation | Do |
|---|---|
| New guidance replaces old guidance | publish the new object with **Replaces** pointing to the old; set the old object's state to *Superseded*; the old page names its successor |
| Guidance is withdrawn without replacement | set the state to *Withdrawn*; keep the page with a short explanation, or hide it if your policy says so |
| A section of guidance is withdrawn | edit, remove the section, write the change note |

States are set on the **Details** tab, box **Content state**, **Set states**; no new version is made, so the text
stays as it was approved ([Object states](../../../../guides/object-states.md)). Templates should show superseded and
withdrawn items clearly and keep them out of "current guidance" lists.

## Telling regulated parties

| Channel | Setup |
|---|---|
| E-mail on change | regulated parties with an account follow the Guidance library or a topic folder with **Notify me**; they choose at once or a digest in **My notification settings** ([Notifications: the user's guide](../../../../features/6.0/notifications.md)) |
| RSS | an RSS export of the guidance library (**Setup > RSS**) ([RSS feeds](../../../../guides/rss-feeds.md)) |
| Newsletter | a periodic summary with cjw_newsletter ([Newsletters with CJW Newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md)) |
| A "Recent changes" page | a template listing guidance by modification date with its change note |

Notifications need the notification cronjob and accounts with `notification/use`.

## Answering "what did it say then?"

1. Open the guidance object, **Manage versions**.
2. Filter by status *Archived*; find the version that was published on the date in question (each card shows created
   and modified dates).
3. **View** shows the text of that version; **Compare** shows the differences to the current one.
4. The audit trail confirms when each version was published and by whom ([Audit trail](../../../../features/6.0/audit-trail.md)).

This works only for versions still kept: raise the version history limit for the Guidance class's objects or keep a
dated PDF of each version in your records system ([PDF exports](../../../../guides/pdf-exports.md)).

## Related pages

- [Agencies](README.md)
- [Requirements emphasis for agencies](requirements-emphasis.md)
- [Publishing, versions and the trash](../../../publishing-and-versions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
