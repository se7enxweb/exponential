---
title: Log review routine
audience: government, admin, hosting
category: public-sector
order: 340
summary: A weekly and monthly log review routine for the administrators of a public site - which Exponential logs and audit views to read, what to look for, commands to use, and how to record the review as evidence.
keywords: log review, monitoring, weekly review, security.log, audit console, error digest, csp.log, auth.log, log viewer, exp:logs, exp:audit, evidence, routine, public sector
type: help
---
# Log review routine

This page is for administrators and hosting teams. "Hardening without watching only delays the moment you learn of an
attack" ([Security hardening: Watching](../../security-hardening.md#watching-logs-the-request-classifier-and-the-audit-trail)).
Below is a routine that fits in half an hour a week, with the places to look in Exponential.

## Contents

1. [Weekly](#weekly)
2. [Monthly](#monthly)
3. [Where to look](#where-to-look)
4. [Recording the review](#recording-the-review)
5. [Related pages](#related-pages)

## Weekly

| Check | Where | Look for |
|---|---|---|
| Audit health | Audit tab, dashboard (`/audit/dashboard`) | chains intact, cronjob part ran within the hour, alerts |
| Fired alerts | **Alerts** (`/audit/alerts`) | every alert explained |
| Failed sign-ins | dashboard *Security* card; `/audit/console/(name)/access.session.login.failed` | networks or accounts with many failures |
| Role grants | `/audit/console/(name)/access.role.*` | grants you did not expect |
| Setting writes | `/audit/console/(name)/system.setting.*` | changes outside change requests |
| Security log | **Setup > Log files** > "Security (request classifier)" | a sudden rise of one rule; injection or traversal with status 200 or 500 |
| Errors | **Setup > Log files**, error digest | new errors after a change |
| CSP reports | **Setup > Log files** > "Content Security Policy reports" | new sources; violations on pages that should be clean |
| Two-step sign-in | `var/log/auth.log` in the log viewer | repeated failures for one account |

## Monthly

- `./console exp:audit verify --archives --allow-root-user` and keep the output.
- Account review light: Users > Check user accounts, **Not seen for N days** and **Locked**.
- `composer audit` and `composer outdated` (see "Patching cadence").
- Log health page in the log viewer: growth and rotation of logs ([Log viewer](../../../features/6.0/log-viewer.md#log-health)).

## Where to look

Command-line equivalents:

```bash
./console exp:audit status --allow-root-user
./console exp:audit search --name='access.session.login.failed' --from=<YYYY-MM-DD> --limit=50 --allow-root-user
./console exp:audit tail --channel=system --lines=50 --allow-root-user
./console exp:logs --archives --allow-root-user
```

The log viewer masks e-mail and IP addresses and secrets by default; keep it so for screenshots and reports. Give
`audit/read` and the log viewer (`setup/logs`) only to administrators.

## Recording the review

Keep a short entry per review: date, reviewer, what was checked, findings and actions, with links to audit console
filters (they are URLs and can be bookmarked). Over a year this is the evidence auditors ask for under logging and
monitoring requirements.

## Related pages

- [Security hardening: Watching](../../security-hardening.md#watching-logs-the-request-classifier-and-the-audit-trail)
- [Request logs and the security log](request-logs-and-the-security-log.md)
- [The log viewer](../../../features/6.0/log-viewer.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
