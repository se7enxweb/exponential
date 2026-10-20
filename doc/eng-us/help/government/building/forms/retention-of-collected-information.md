---
title: Retention of collected information
audience: government, admin, project-manager, editor
category: public-sector
order: 80
summary: How to keep form submissions in Exponential only as long as needed - who may read them, exporting to CSV with the audit record, removing collections per form or per submission, the copies that remain elsewhere, and a retention schedule you run by hand.
keywords: retention, collected information, form submissions, infocollector/read, export CSV, remove collections, audit data.export.csv, data.infocollection.remove, GDPR, storage limitation, schedule
type: help
---
# Retention of collected information

This page is for administrators, project managers and editors who handle what forms collect. Submissions often hold
names, addresses and messages that people expect to be used for one purpose and then deleted. Exponential stores them
until someone removes them: it has **no automatic deletion** of collected information. This page shows how to read,
export and remove them, and how to run a retention schedule. Source: [Collected information](../../../../guides/collected-information.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Who may read submissions](#who-may-read-submissions)
2. [Exporting](#exporting)
3. [Removing](#removing)
4. [Copies that remain elsewhere](#copies-that-remain-elsewhere)
5. [A retention schedule](#a-retention-schedule)

## Who may read submissions

Everything in **Setup > Collected information** (`/infocollector/overview`) needs the policy `infocollector/read`.
Give it only to the people who process the submissions ([Roles and policies](../../../../guides/roles-and-policies.md)).
The policy has no limitations: whoever has it reads the submissions of every form. If teams must not see each other's
submissions, mail each form to its own team and keep `infocollector/read` with the few people who manage retention.

## Exporting

**Export CSV** on a form's card downloads `<name>-collected.csv`: UTF-8, collection ID, sent and changed times in UTC,
the ID of the sending user, then one column per collector attribute; at most 50 000 collections, newest first. Values
that a spreadsheet would read as a formula begin with an apostrophe. Each export is recorded in the audit as
`data.export.csv` with the number of rows, never the values.

The export holds what people typed. Store it where personal data belongs, and delete it when done.

## Removing

| Remove | How |
|---|---|
| Every submission of some forms | on the overview, tick the forms, **Remove selected**, check the confirmation (it says how many collections go and that the forms stay), **Remove** |
| Some submissions of one form | open the form's **Collections**, tick them, **Remove selected** |

Removals are recorded in the audit as `data.infocollection.remove`. Removing is final; export first if the data is
still needed. The overview also counts collections of forms that were removed or are in the trash (a red figure);
restore the form from the trash to see and remove them.

## Copies that remain elsewhere

| Copy | Do |
|---|---|
| Mails sent to receivers | delete them on the same schedule ([Mail to receivers](mail-to-receivers.md)) |
| CSV exports on staff computers | delete them |
| Uploaded files of the Enhanced File datatype | the extension keeps at most `MaxFiles` files; check the storage path ([File attachments in forms](file-attachments-in-forms.md)) |
| Backups | removed data stays in backups until they expire |
| The audit trail | records the actions (export, removal), not the values; it is pseudonymized after a period ([Audit trail](../../../../features/6.0/audit-trail.md)) |

## A retention schedule

| Form | Typical period (example assumption, set your own) | Who removes |
|---|---|---|
| Contact form | until answered, plus a short period | the team that answers |
| Report of a problem | until fixed | the service team |
| Event sign-up | until the event is over | the organizer |
| Consultation responses | as the consultation's records rules say; the summary report may be kept longer | the policy team |
| Page feedback | until read | the web team |

Run it monthly:

- [ ] Open **Setup > Collected information**, sort by **first collection** (oldest first).
- [ ] For each form past its period: export if needed, then remove the old collections.
- [ ] Delete the matching mails and exports.
- [ ] Note the date in your records log.

Under the [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) in the EU, storage limitation is one of the
principles; Exponential's privacy features are summarized in [Privacy standards](../../../../standards/privacy.md).

## Related pages

- [Building forms](README.md)
- [Reading and exporting form submissions](../../../creators/website-creators/form-submissions.md)
- [Collected information](../../../../guides/collected-information.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
