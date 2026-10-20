---
title: Incident response for an Exponential site
audience: government, admin, project-manager, hosting
category: public-sector
order: 400
summary: For public bodies handling a security incident on their site - contain, preserve evidence, investigate with the audit trail and logs, rebuild from known-good sources, recover credentials and sessions, notify, and prevent a repeat, based on the project's hosting runbook.
keywords: incident response, security incident, compromise, defacement, containment, evidence, audit trail, security.log, maintenance mode, rebuild, credentials, sessions, notification, breach, lessons learned, public sector
type: help
---
# Incident response for an Exponential site

This page is for administrators, security officers and project managers of public bodies. It adapts the project's
hosting runbook ([Runbook: compromised site](../../hosting/runbooks/compromised-site.md)) to a public body's site. Signs
of an incident: defaced pages, unknown administrator accounts, files that should not exist, spam sent from the site,
an audit alert such as `admin_role_granted` or `chain_broken`.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Contain](#contain)
2. [Preserve evidence](#preserve-evidence)
3. [Investigate](#investigate)
4. [Rebuild and recover](#rebuild-and-recover)
5. [Notify](#notify)
6. [Prevent a repeat](#prevent-a-repeat)
7. [Related pages](#related-pages)

## Contain

1. Take the public site offline with maintenance mode, letting only your addresses in:
   `php bin/php/maintenance.php on --message="Maintenance" --allow-ip=<your address> --allow-root-user`
   ([Maintenance mode](../../../guides/maintenance-mode.md)). For a public body, publish essential information through
   another channel meanwhile.
2. Restrict the administration at the network level if an administrator account or session may be in the attacker's
   hands.
3. If the machine itself may be compromised, isolate it and work from a copy.
4. Do not delete anything yet.

## Preserve evidence

- Copy the files and a database dump to a place the site's user cannot write.
- Copy the logs: `var/log/` (`error.log`, `security.log`, `auth.log`, the audit directory), Velocity's logs, the web
  server's and PHP-FPM's logs.
- Export the relevant audit records in a signed bundle: `./console exp:audit export --from=<time> --format=bundle --out=<file> --allow-root-user`.
- Note the time of every action you take.

## Investigate

| Question | Where |
|---|---|
| Who signed in, changed roles, settings, passwords? | Audit tab, console filters; `./console exp:audit search` |
| Was the audit itself tampered with? | `./console exp:audit verify`; compare with the copy in your SIEM |
| Which requests looked like attacks, and did any succeed? | `security.log` (status 200 on injection or traversal) |
| Which files changed? | file consistency check; comparison with the release tag |
| Were files planted? | `var/`, `settings/`; any `.php` under `var/storage` is suspect |
| Which version ran? | `composer show se7enxweb/exponential`; compare with later release notes |

## Rebuild and recover

1. Code from a known-good source: the release tag and `composer install` from your lock file, your extensions from
   their repositories.
2. Data from the newest clean backup, or review the current database for unknown accounts, roles and content.
3. Update to the newest 6.0.x if the hole may be a known one.
4. Change credentials: administrator passwords, database password, API keys and tokens; rotate the audit signing key if
   it may have been read (`./console exp:audit key rotate --allow-root-user`).
5. End sessions: password changes sign out other sessions; remove all sessions under **Setup > Sessions** with the
   database session handler.
6. Clear all caches (`php bin/php/ezcache.php --clear-all --allow-root-user`; restart Velocity and clear its response
   cache), verify, then switch maintenance mode off.

## Notify

- Internally, as your incident procedure says (security officer, data protection officer, management).
- To authorities and affected people where the law requires it, within its deadlines (for example under the GDPR for
  personal data breaches, or national cybersecurity rules).
- To the Exponential project, privately, if a vulnerability in Exponential or an extension was the cause
  (see "Vulnerability disclosure").

## Prevent a repeat

- Review the go-live checklist and the hardening layers; make the code read-only for the site's user.
- Forward the audit trail off the machine; put request-shield in front.
- Hold a lessons-learned meeting and update this procedure.

## Related pages

- [Runbook: compromised site](../../hosting/runbooks/compromised-site.md)
- [Audit integrity and verification](audit-integrity-and-verification.md)
- [Backups and recovery](backups-and-recovery.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
