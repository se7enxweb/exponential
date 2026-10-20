---
title: The audit trail for public bodies
audience: government, admin, project-manager
category: public-sector
order: 70
summary: For public bodies that must show who did what in their website - what Exponential's audit trail records by default (and what it does not, such as publishing), where it is kept, who may read it, how long it is kept, and the first steps on a new installation.
keywords: audit trail, audit log, accountability, who did what, sign-ins, failed logins, role changes, setting changes, content removal, publishing, hash chain, retention, audit/read, audit/manage, exp:audit, public sector
type: help
---
# The audit trail for public bodies

This page is for administrators, information security officers and records managers in public bodies. Accountability
for changes to official information is a common expectation of public bodies. Exponential's audit trail records what
happens in an installation in tamper-evident files, **on in every installation by default**
([Audit trail](../../../features/6.0/audit-trail.md), [Audit guide](../../../bc/6.0/audit.md)).

## Contents

1. [What is recorded](#what-is-recorded)
2. [What is not recorded by default](#what-is-not-recorded-by-default)
3. [Where and how long](#where-and-how-long)
4. [Who may read it](#who-may-read-it)
5. [First steps on a new installation](#first-steps-on-a-new-installation)
6. [Related pages](#related-pages)

## What is recorded

| Question | Recorded as |
|---|---|
| Who signed in, who failed to, from which network | `access.session.login`, `access.session.login.failed` (with the reason), `access.session.logout`; addresses truncated to /24 by default |
| Who removed, moved, hid or restored content | `content.node.*`, `content.object.remove`, `content.object.restore`; a subtree removal is one parent event with one child per node |
| Who changed roles, policies, users, sections, states, classes | `access.role.*`, `access.policy.*`, `access.user.*`, `content.section.*`, `content.state.*`, `content.class.*`, with values before and after |
| Who changed a setting, cleared a cache, ran a command or cronjob | `system.setting.write` (with the diff, secrets masked), `system.cache.clear`, `system.command.run`, `system.cronjob.run` |
| Who changed prices or removed an order | `commerce.*` |
| Whether anyone tampered with the log | `exp:audit verify` and the console: per channel **intact** or **broken** |

148 event names exist in a taxonomy (`domain.subject.action`) and can be switched on or off by pattern.

## What is not recorded by default

The shipped `settings/audit.ini [AuditEventSettings] Enabled[]` list leaves some content events off. The audit guide
says: "'Who published this?' is the question asked most; the shipped default leaves it off." To record creation and
publishing, add to `settings/override/audit.ini.append.php`:

```ini
[AuditEventSettings]
Enabled[]=content.object.create
Enabled[]=content.object.publish
```

The `read` channel (sampled reads) is off by default as well. Content values, passwords, tokens and secrets are never
recorded. For publishing history of a single object, the versions page also shows each version's creator and dates
([Versions of an object](../../../guides/content-history.md)).

## Where and how long

- One line of JSON per record in `var/<site>/log/audit/<channel>-<YYYY-MM-DD>.jsonl`; five channels: `content`,
  `access`, `system`, `commerce`, `read`.
- Each line carries the hash of the line before it; daily signed checkpoints; signed archive manifests.
- Retention per channel: 90 days live and 730 days archived (read: 30 and 90), removed automatically by the cronjob
  part ([4.3 Retention](../../../bc/6.0/audit.md#43-retention)). A records schedule that needs longer can extend
  `ArchiveDays` ([6.6 Compliance: long retention](../../../bc/6.0/audit.md#66-compliance-long-retention)).

## Who may read it

| Policy | Grants |
|---|---|
| `audit/read` | the Audit tab (`/audit/dashboard`), console, event view, charts, alerts, export; can be limited by `Channel` |
| `audit/manage` | archives and settings views, "Verify now" |

Only the Administrator role holds them in a new installation. A user without the policy sees nothing of the audit, and
a typed URL is refused and recorded ([3.1 Who may see what](../../../bc/6.0/audit.md#31-who-may-see-what-the-policies)).

## First steps on a new installation

1. `./console exp:audit status --allow-root-user`: audit enabled, every channel intact.
2. Make sure `php runcronjobs.php -q frequent` runs every few minutes as the site's user; the dashboard warns when the
   cronjob part has not run for an hour.
3. Check who receives alert mail: `./console exp:audit alerts recipients --allow-root-user`.
4. Back up `settings/override/audit.ini.append.php` as a secret: it holds the signing and pseudonym keys.
5. Decide whether to record publishing (above) and how long to keep archives.
6. Forward the trail off the machine (see "Forwarding the audit trail to a SIEM").

## Related pages

- [Audit trail](../../../features/6.0/audit-trail.md)
- [Audit: what happens in Exponential, recorded](../../../bc/6.0/audit.md)
- [13.14 The audit log](../../../install/13-security-hardening.md#1314-the-audit-log)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
