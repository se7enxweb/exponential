---
title: The user audit log
audience: admin
category: account
order: 11
summary: Users > User audit log answers who did what to an account, when and from where, and who was refused: one line per event with the fields before and after, filtered by user, action, result and dates, and exported as CSV; passwords are never recorded.
keywords: user audit log, audit, who changed what, account history, user history, member management, refused, failed sign-in, role assigned, export CSV, data protection officer, user/audit, privacy, retention
context: user/audit
type: help
---
# The user audit log

The user audit log answers one question for any account of the site: **who did what to it, when and from where**,
and who tried and was refused. Use it after an editor managed members, when someone asks why their account changed,
when a sign-in looks suspicious, or to review new accounts and role changes.

It lists one line per event about a user account or a user group, from every interface: the administration, the
Editor, the public site, the REST interface and the commands.

## Open it

In the administration, click **Users** in the top menu, then **User audit log** in the left column (the address is
`https://www.example.com/admin/user/audit`). The page needs the permission `user/audit`: administrators have it.
Whoever holds it sees the events of **every** account, so give it only to roles that should, for example a role
with `user/audit` alone for a data protection officer.

## What a line shows

| Column | What it holds |
|---|---|
| **When** | date and time of the event |
| **Action** | what happened, for example `access.user.create`, `access.user.change`, `access.user.disable`, `access.session.login.failed`, with the result below it (*Done*, *Refused* or *Failed*) and the reason |
| **Account** | the account or user group the event was about |
| **By** | who acted: a user, or *system* for a command or a cronjob |
| **Where** | the part of the installation (siteaccess), the page, and the network address as the privacy rules allow (shortened by default) |
| **Before** / **After** | the fields that changed, for example the e-mail address before and after (hashed by default) |

Passwords, password hashes, tokens and other secrets are **never** recorded.

## What is recorded

- an account created, changed, moved, enabled, disabled, locked, unlocked or removed, and its e-mail address or
  username changed;
- a password changed or reset, and a reset requested (never the password itself);
- a registration waiting for approval, approved or denied, and a new editor's account approved or denied;
- a role assigned to or taken from an account or a group;
- sign-ins, failed sign-ins, sign-outs, *Verify it's you* and ended sessions; passkeys added or used;
- every refused attempt, for example an editor trying to edit an administrator's account.

So the work of [member management](managing-members.md) is covered: what an editor did to a member, and every time
an editor reached for an account they may not change.

## Narrow the list

Under **Narrow the list**:

1. **User**: a username or a user ID. It finds the events **about** that account **and** the events that user
   **caused**, so one search shows both what happened to a member and what an editor did. Clicking a username in the
   list searches for it.
2. **Action**: one event, or a branch of them: `access.user` finds every `access.user.*` event.
3. **Result**: *Any result*, *Done*, *Refused* or *Failed*.
4. **From** and **To**: days, both included.
5. Press **Show**; **Clear** removes the filters.

In the list, the username under **Account** or **By** is a link: it shows every event of that account.

## Export

**Export the list as CSV** downloads the whole filtered list (one line per event, before and after as JSON), for a
review or a request for information. The export itself is recorded. Treat the file like the data it contains: it
names people and what they did.

## How long events are kept

The page says how long: by default 730 days. The cronjob part `useraudit` (in the infrequent group) removes older lines
(`settings/audit.ini [UserAuditSettings] RetentionDays`). The audit files of the site keep their own copy with their
own retention.

## Example

A member says their e-mail address was changed without their knowledge. Open the user audit log, enter the member's
username under **User**, choose the action `access.user.email.change` and press **Show**. The line names the editor
who changed it, when, and from the Editor; a search for that editor's username then shows what else they did that
day.

## When the page is missing or empty

| What you see | Why |
|---|---|
| No **User audit log** in the left column | Your role does not have `user/audit`, or the installation predates 6.0.15. |
| *The table of the user audit log is missing.* | An installation made before 6.0.15: a developer runs `php update/common/scripts/6.0/createuseraudittable.php`. |
| *Recording is switched off* | `[UserAuditSettings] UserAudit=disabled`; the list shows what was recorded before. |
| *This page needs an SQL database* | On MongoDB the audit files keep the events; there is no table. |

## Related pages

- [Managing members](managing-members.md)
- [Setting up accounts for editors and administrators](setting-up-editor-and-admin-accounts.md)
- [How do I find out who changed what?](manual/how-do-i-find-out-who-changed-what.md), for content
- [Security and audit](../guides/security-and-audit.md)
- [User audit log](../features/6.0/user-audit.md), the technical description with every setting
- [Free documentation](free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](partners/partners-start-here.md) and [How to apply](partners/how-to-apply.md).
<!-- exp-partner-block:end -->
