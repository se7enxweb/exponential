---
title: Review the security log weekly
audience: government, admin, project-manager
category: public-sector
order: 160
summary: Step by step - a weekly review of security.log through the error digest and the log viewer - categories and rules, high-confidence lines that got a 200, floods, new targets - with a short record for the security team.
keywords: security log, weekly review, security.log, request classifier, probes, injection, traversal, scanner, threshold, error digest, security monitoring, record
type: help
---
# Review the security log weekly

This task is for administrators or security officers. At the end of a 15-minute review you know what attackers tried
this week and whether anything needs action. `security.log` holds requests the classifier names a probe, traversal,
injection, attack tool or 404 burst, with any status; nothing is blocked by it
([Request logs: 404 and security](../../operations/monitoring/request-logs-404-and-security.md)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [What needs action](#what-needs-action)
4. [The record](#the-record)
5. [Related pages](#related-pages)

## Before you start

- `setup/logs`, or shell access.

## Steps

1. **Setup > Log files > Error digest** for the week: `/setup/logsdigest/security/(hours)/168`. The groups are
   `<category>/<rule>` with counts.
2. Compare with last week's record: new rules firing, big jumps.
3. Look for lines with high confidence and status 200:

   ```bash
   ./console exp:logs --search='confidence=high 200 ' --file=var/log/security.log --rotations
   ```

4. Floods: `threshold/404-rate` lines (more than 30 not-found answers a minute from one client).
5. Check the audit side for the same week: `./console exp:audit alerts list --allow-root-user` and fired
   `brute_force` alerts on the **Audit** tab, **Alerts**.

## What needs action

| Finding | Action |
|---|---|
| Probes for software you do not run (other CMSs) | none; normal background |
| Injection or traversal with status 200 | ask a developer to confirm the response did not leak data |
| Many requests from few networks | give the pattern to whoever runs the layer in front ([DDoS and probe floods](../../operations/incidents/ddos-and-probe-floods.md)) |
| A new target path on your own site | check that page's input handling |
| `brute_force` alerts | [Suspected account compromise](../../operations/incidents/suspected-account-compromise.md) if an account succeeded later |

## The record

Week, top five rules with counts, anything unusual, actions. Keep it with your security records; it shows a continuous
monitoring practice to auditors.

## Related pages
- [Read the error digest](read-the-error-digest.md)
- [Audit alerts](../../operations/monitoring/audit-alerts.md)
- [Security log (hosting)](../../../hosting/operations/security-log.md)
- [Security hardening](../../../security-hardening.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
