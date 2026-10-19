---
title: Compliance tasks
audience: government, admin, editor, project-manager, developer
category: public-sector
order: 10
summary: Step-by-step compliance tasks for public bodies running Exponential - passwords and sessions, Content Security Policy and headers, the audit trail, privacy requests, logs and operations hygiene, accessibility, and records - each with prerequisites, real menu paths and commands, a check and the way back.
keywords: compliance tasks, how to, step by step, security, CSP, HSTS, audit trail, access request, erasure, log retention, file consistency, security.txt, alt text, accessibility statement, archiving, trash, versions, public sector
type: help
---
# Compliance tasks

This folder holds 51 short, practical tasks for administrators, editors and developers of public bodies. Each task names
who it is for, what you need before you start, the steps with real menu paths and commands, how to check that it worked,
and how to go back. The background is in the Governments sections on accessibility, security, privacy and records; each
task links to its section page.

Some tasks of the same family live in the people-and-permissions tasks, because they are about accounts and roles:
[Turn on two-step sign-in for staff](../people-and-permissions/turn-on-two-step-sign-in-for-staff.md),
[Help a user who lost their phone](../people-and-permissions/help-a-user-who-lost-their-phone.md),
[Switch on account lockout](../people-and-permissions/switch-on-account-lockout.md),
[Unlock a locked account](../people-and-permissions/unlock-a-locked-account.md),
[Find accounts not used for months](../people-and-permissions/find-accounts-not-used-for-months.md),
[Deactivate and move a leaver](../people-and-permissions/deactivate-and-move-a-leaver.md),
[Export the account list for a review](../people-and-permissions/export-the-account-list-for-a-review.md) and
[Give an auditor read access to the audit trail](../people-and-permissions/give-an-auditor-read-access-to-the-audit-trail.md).

## Contents

1. [Tasks in this folder](#tasks-in-this-folder)
2. [Related pages](#related-pages)

## Tasks in this folder

| Group | Task | What it does |
|---|---|---|
| Passwords and sessions | [Set the minimum password length to 12](set-the-minimum-password-length-to-12.md) | raise Exponential's minimum password length from the shipped 10 to 12 (the OWASP ASVS 4.0.3 level 1 value) or more, without locking anyone out, and check that new passwords follow it. |
| Passwords and sessions | [Add your own words to the weak password list](add-your-own-words-to-the-weak-password-list.md) | make Exponential refuse passwords built from your organization's own words (its name, the site, the city) on top of the shipped list of well-known passwords, with WeakPasswordList[] or WeakPasswordFile. |
| Passwords and sessions | [Sign out every session of a user](sign-out-every-session-of-a-user.md) | after a suspected account takeover or a lost device, end every session of one user in Exponential by changing the password (EndOtherSessions), disabling the account, or removing sessions under Setup > Sessions with the database session handler. |
| Passwords and sessions | [Retire the shared admin login](retire-the-shared-admin-login.md) | replace the shared admin account with personal administrator accounts so the audit trail names people, then disable admin (after moving UserCreatorID) or make it unusable, and keep a tested break-glass procedure. |
| Passwords and sessions | [Separate session cookies for the administration](separate-session-cookies-for-the-administration.md) | give every siteaccess its own session cookie in Exponential (SessionNameHandler=custom), so a sign-in on the public site is not a sign-in in the administration, and check the result. |
| CSP, headers and network | [Switch the administration's CSP to enforce](switch-the-admin-csp-to-enforce.md) | after the template audit and a period of clean reports, change the Content Security Policy of the administration siteaccess from report-only to enforce, deploy it to every process, and check the header. |
| CSP, headers and network | [Review CSP reports](review-csp-reports.md) | collect Content Security Policy violation reports with Exponential's own csp/report collector, read them in Setup > Log files, tell real problems from noise, and decide per source what to fix or allow. |
| CSP, headers and network | [Roll back the CSP to report-only](roll-back-csp-to-report-only.md) | when an enforced Content Security Policy blocks work, return the siteaccess to report-only in minutes, make every process send the new header, and keep collecting reports to fix the cause. |
| CSP, headers and network | [Start CSP reporting on a public site](start-csp-reporting-on-a-public-site.md) | public siteaccesses get no Content Security Policy by default; switch one to report-only with Exponential's own report collector, so you learn what your design, content and embeds would need before enforcing. |
| CSP, headers and network | [Add HSTS](add-hsts.md) | turn on HTTP Strict Transport Security for a site that is fully HTTPS, with Exponential's SecurityHeaders setting (Apache, nginx) or Velocity's HSTSMaxAge, starting short and raising the value in stages. |
| CSP, headers and network | [Set secure session cookies](set-secure-session-cookies.md) | on an HTTPS-only site, force the Secure flag on Exponential's session cookie, keep HttpOnly and SameSite=Lax, choose a session timeout with your editors, and check the flags with curl. |
| CSP, headers and network | [Check the security headers](check-the-security-headers.md) | check from outside that every Exponential siteaccess sends the expected security headers and cookie flags, that internal files are not served, and record the result as evidence for an audit. |
| CSP, headers and network | [Restrict the administration by network](restrict-the-administration-by-network.md) | limit the administration siteaccess of an Exponential site to your office network or VPN with Apache Require ip or nginx allow/deny, after giving it its own host name, and check from outside. |
| Audit trail | [Review the audit trail for a user](review-the-audit-trail-for-a-user.md) | see everything one user did (sign-ins, failed sign-ins, content moved, hidden or removed, role and setting changes) in Exponential's Audit console or with exp:audit search, narrow it by time and event, and note what you found. |
| Audit trail | [Export audit records for an investigation](export-audit-records-for-an-investigation.md) | select the audit records an investigation needs, verify the chains, export them as a signed bundle or JSON lines with their sha256, and document the hand-over so the export can serve as evidence. |
| Audit trail | [Verify the audit trail](verify-the-audit-trail.md) | check that no audit record was changed, removed or inserted with exp:audit verify or Verify now, add the check to monitoring by its exit code, and know what to do when a channel is broken. |
| Audit trail | [Route audit alerts to a security mailbox](route-audit-alerts-to-a-security-mailbox.md) | send Exponential's audit alerts (brute force, administrator role granted, mass delete, audit switched off, broken chain) to your security team's mailbox instead of the site's AdminEmail, and check the recipients. |
| Audit trail | [Restore an archived audit day](restore-an-archived-audit-day.md) | read audit records older than the 90 live days by restoring the archived day with exp:audit restore, check it against its signed manifest, search it, and know what happens to the restored copy. |
| Audit trail | [Record who published content](record-who-published-content.md) | switch on the audit events content.object.create and content.object.publish, which Exponential leaves off by default, so the audit trail answers "who published this, and when?", and check that publishing is recorded. |
| Audit trail | [Make the audit stricter for privacy](make-the-audit-stricter-for-privacy.md) | apply the audit guide's strict-privacy profile (hashed logins and addresses, no user agents, keys-only before and after values, 30 days to pseudonymization, shorter retention) where your data protection officer asks for less personal data in the audit. |
| Audit trail | [Forward the audit trail to syslog](forward-the-audit-trail-to-syslog.md) | send Exponential's audit records to your central log collector or SIEM over TLS with the syslog sink, choose the channels, test delivery with exp:audit sinks test, and keep it running. |
| Audit trail | [Rotate the audit signing key](rotate-the-audit-signing-key.md) | replace the key that signs Exponential's audit checkpoints and archive manifests, yearly or at once after a suspected leak, while keeping old keys for old archives, and back up the result. |
| Audit trail | [Extend audit retention](extend-audit-retention.md) | keep Exponential's audit archives longer than the shipped 730 days when your records schedule requires it, per channel, with an archive directory on write-once storage, and check the result. |
| Privacy requests | [Answer an access request for mail preferences](answer-an-access-request-for-mail-preferences.md) | give a person their e-mail preferences and full consent history from Exponential as JSON or CSV, from the administration or the console, check the file and hand it over safely. |
| Privacy requests | [Export audit records about a person](export-audit-records-about-a-person.md) | for an access request, export every audit record made by or about one user (pseudonymized ones included) with exp:audit export --subject-user, review it for other people's data, and hand it over. |
| Privacy requests | [Erase a user account](erase-a-user-account.md) | remove a user account for good after an erasure decision, knowing that users never go to the trash, that removal erases the account's mail preferences and anonymizes its consent log, and what to remove by hand before and after. |
| Privacy requests | [Erase mail data for an address](erase-mail-data-for-an-address.md) | erase the mail preferences and pending confirmations of an e-mail address and anonymize its consent log with exp:mail:preferences erase, for subscribers without an account, and decide whether to add a legal suppression entry. |
| Privacy requests | [Stop all optional mail to an address](stop-all-mail-to-an-address.md) | honor a "stop writing to me" request in Exponential with a suppression list entry (reason legal), which blocks every optional mail and confirmation to that address while storing only a hash, and check it. |
| Privacy requests | [Set the sender details in the mail footer](set-the-sender-details-in-the-mail-footer.md) | fill in the organization name, postal address and privacy notice link that Exponential's mail gate adds to every optional mail (they ship empty), and check them on the status page and in a test mail. |
| Privacy requests | [Export and remove form submissions](export-and-remove-form-submissions.md) | the periodic retention routine for web form submissions in Exponential: export what must be kept to your case or records system as CSV, then remove the collections on Setup > Collected information, and delete the export files. |
| Privacy requests | [Reset search statistics](reset-search-statistics.md) | check which siteaccesses record search phrases, use the figures, and remove all recorded phrases with Reset statistics on Setup > Search statistics, because phrases can contain names and case numbers. |
| Logs and operations hygiene | [Set log retention](set-log-retention.md) | give Exponential's kernel logs an age limit with MaxAgeDays per log (404, security and CSP have one already), schedule pruning with the logs cronjob part, and check with a dry run. |
| Logs and operations hygiene | [Review the security log](review-the-security-log.md) | the weekly read of Exponential's security.log in Setup > Log files - what the request classifier records, which lines need a closer look (injection or traversal with status 200 or 500), and what to do about them. |
| Logs and operations hygiene | [Restrict debug output](restrict-debug-output.md) | make sure Exponential's debug output is off in production, and when a live problem needs it, switch it on for your own address only, then remove it again and check that nothing is shown to visitors. |
| Logs and operations hygiene | [Lock settings with ManagedByDeployment](lock-settings-with-managed-by-deployment.md) | switch on ManagedByDeployment for the production administration siteaccess so the web interface cannot change settings, templates, extensions or packages, after moving those settings into version control, and check the lock. |
| Logs and operations hygiene | [Run the file consistency check](run-the-file-consistency-check.md) | compare every file of the installation with the release's checksum list in Setup > Upgrade check or on the command line, read the findings, and file the result after updates and incidents. |
| Logs and operations hygiene | [Add a security.txt file](add-security-txt.md) | publish /.well-known/security.txt (RFC 9116) for your site, which Exponential does not ship, add the one rewrite rule the shipped Apache rules need to serve it, and check it with curl. |
| Logs and operations hygiene | [Check that form tokens are active](check-that-form-tokens-are-active.md) | confirm that the ezformtoken extension protecting every Exponential form against cross-site request forgery is active on all siteaccesses, and what to do if it is missing. |
| Logs and operations hygiene | [List known vulnerabilities with composer audit](list-known-vulnerabilities-with-composer-audit.md) | check the Composer dependencies locked for your Exponential installation against known vulnerability advisories with composer audit and see newer releases with composer outdated, because the project's CI runs no dependency scan, and file the result. |
| Accessibility | [Check alternative text across images](check-alt-text-across-images.md) | find images without alternative text on a public site: with the optional Exponential Intelligence report "Missing alt text" where that extension is active, or with an accessibility checker on page samples where it is not, then fix them in drafts. |
| Accessibility | [Require alternative text on an image field](require-alt-text-on-an-image-field.md) | switch on the class attribute option "Alternative text is required" (Exponential 6.0.15 and later) for the image fields editors use, so an image cannot be stored without alternative text, and check it in the edit form. |
| Accessibility | [Run an accessibility spot check of a page](run-an-accessibility-spot-check-of-a-page.md) | a 15-minute accessibility check of one public page (automated check with an axe-based browser tool, keyboard pass, zoom, a quick screen reader pass, content review), and how to record and route findings to template or content owners. |
| Accessibility | [Publish an accessibility statement page](publish-an-accessibility-statement-page.md) | publish your accessibility statement as a page of your Exponential site (no template ships), with a stable address such as /accessibility, a link in every footer, a feedback route, and versions kept for the record. |
| Accessibility | [Keep editors on the admin4 design](keep-editors-on-the-admin4-design.md) | make admin4, the administration design Exponential tests against WCAG 2.2 AA, the design of the administration siteaccess, and limit the per-session design switch so staff with disabilities stay in the tested environment. |
| Records | [Archive last year's notices](archive-last-years-notices.md) | at the start of a year, move last year's notices into a year folder under an archive, so current lists stay short while the old notices keep their content, versions and redirecting addresses, and record what you did. |
| Records | [Restore an archived page](restore-an-archived-page.md) | bring a page back from the archive to the current site in Exponential, whichever way it was archived (moved to an archive folder, hidden, set to an archived state, or put into an archive section), and check that visitors see it. |
| Records | [Restore an item from the trash](restore-an-item-from-the-trash.md) | find a removed page in Exponential's trash by who trashed it and when, restore it to its original place (or choose another when the parent is gone), and check what the audit recorded. |
| Records | [Bring back an earlier version of a page](bring-back-an-earlier-version-of-a-page.md) | restore the earlier wording of a page from Exponential's versions page by comparing versions and making a new draft from the old one, then publishing it, so the history keeps both the mistake and the correction. |
| Records | [Keep trash items for 90 days](keep-trash-items-for-90-days.md) | empty Exponential's trash on a schedule while keeping each item for a set number of days, with content.ini [TrashSettings] KeepItemsForDays and the trashpurge cronjob part (in no part by default), and check with the command line. |
| Records | [Hide notices automatically on a date](hide-notices-automatically-on-a-date.md) | let editors give each notice a hide date and have Exponential's hide.php cronjob hide it when the date has passed, by adding a date attribute to the class and configuring content.ini [HideSettings]. |
| Records | [Raise the version limit for notices](raise-the-version-limit-for-notices.md) | keep more versions of official notices than Exponential's default 45 per object by setting VersionHistoryClass for that class, so earlier published wordings are not removed when new versions are published. |

## Related pages

- [Governments: start here](../../government-start-here.md) and the tasks index ([Tasks](../README.md))
- [Accessibility for public bodies](../../accessibility/README.md), [Security for public bodies](../../security/README.md),
  [Privacy for public bodies](../../privacy/README.md), [Records and archiving](../../records-and-archiving/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
