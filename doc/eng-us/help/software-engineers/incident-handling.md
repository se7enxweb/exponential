---
title: Incident handling
audience: software-engineer, admin
category: software-engineering
order: 420
summary: How engineers respond to an outage, a regression or a security incident on an Exponential site - triage, the tools for each symptom (maintenance mode, repair from the browser, error references, logs, audit trail, Velocity status), rolling forward instead of rewriting releases, private vulnerability reports, and the write-up afterwards.
keywords: incident, outage, regression, triage, runbook, maintenance mode, repair, error reference, rollback, roll forward, postmortem, security incident, vulnerability, software engineer
type: help
---
# Incident handling

This page is for engineers on call for an Exponential site. It gives a sequence for any incident and points to the
runbooks for the common ones. It does not replace your organization's incident process; it fills in the
Exponential-specific steps.

## Contents

1. [The first ten minutes](#the-first-ten-minutes)
2. [Symptoms and the tools for them](#symptoms-and-the-tools-for-them)
3. [Stopping the damage](#stopping-the-damage)
4. [Fixing: roll forward](#fixing-roll-forward)
5. [Security incidents](#security-incidents)
6. [After the incident](#after-the-incident)

## The first ten minutes

1. **Confirm and scope.** Request the front page and the administration login from outside. The front page may be
   served from a cache while the database is down, so the login page tells you more
   ([Health checks and uptime](../hosting/operations/health-checks-and-uptime.md)).
2. **Check the servers.** `./console exp:velocity status` for Velocity; the PHP-FPM service and the database server
   otherwise.
3. **Read the newest errors.** Setup > Log files, or `./console exp:logs`; a visitor's error reference leads to the
   lines of their request ([Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md)).
4. **Ask what changed.** The audit trail records deploys, settings writes and cache clears with who and when:
   `./console exp:audit tail --allow-root-user`.
5. **Decide**: stop the damage first, or fix directly?

## Symptoms and the tools for them

| Symptom | First tool | Runbook |
|---|---|---|
| The site does not answer | server status, `error.log` | [Site down](../hosting/runbooks/site-down.md) |
| Pages are slow | debug bar on your address, `exp:benchmark kernel` | [Slow site](../hosting/runbooks/slow-site.md) |
| A blank page or PHP output instead of the site, `vendor/` missing | repair from the browser ([Repair](../../features/6.0/repair-from-the-browser.md)) | [Site down](../hosting/runbooks/site-down.md) |
| Velocity does not start | `exp:velocity status`, its log | [Velocity will not start](../hosting/runbooks/velocity-will-not-start.md) |
| A change "did not work" | the deploy order: code loaded before caches cleared | [Deploying a change](deploying-a-change.md) |
| Disk full | `var/` sizes in Setup > Caches, log archives | [Disk full](../hosting/runbooks/disk-full.md) |
| A certificate expired | the HTTPS configuration of the engine | [Certificate expiry](../hosting/runbooks/certificate-expiry.md) |
| A suspected compromise | `security.log`, the audit trail | [Compromised site](../hosting/runbooks/compromised-site.md) |

## Stopping the damage

- **Maintenance mode** takes the public site offline with a `503` and `Retry-After`, and brings it back with one
  command:

  ```bash
  php bin/php/maintenance.php on --message="Back at 06:00" --until=2h --allow-root-user
  php bin/php/maintenance.php off --allow-root-user
  ```

  ([Maintenance mode](../../features/6.0/maintenance-mode.md)).
- **Switch off** the extension or setting that the change introduced, through `settings/override/` and an INI cache
  clear (and a Velocity restart).
- **Restore** from backup when data is damaged ([Restore from backup](../hosting/runbooks/restore-from-backup.md)).

## Fixing: roll forward

Fix forward with a new commit and, for a release, a new version. A published tag or release is never moved,
replaced or re-cut, even when it is the cause; the next version's notes say what was wrong with the previous one
([Version numbers and immutable tags](version-numbers-and-immutable-tags.md)). On an installation made of Git clones,
you can use a fix before it is released ([Develop with Git](../develop-with-git.md)).

## Security incidents

- A vulnerability in Exponential itself is reported **privately** to the address in `SECURITY.md`, not in the public
  issue tracker. Include the version, the engine, the database, what an attacker can do and the steps to reproduce,
  and leave out passwords, secrets and personal data.
- Verify the audit trail's integrity (`./console exp:audit verify --allow-root-user`, exit status 1 when a chain is
  broken) before you rely on it as evidence.
- Rotate the secrets an attacker may have seen, from `settings/override/`.

## After the incident

- Write down the timeline, the cause, what detected it and what would have detected it earlier.
- Add the test that would have caught it ([Testing strategy](testing-strategy.md)).
- If the cause was a behavior change, check that its upgrade notes in `doc/eng-us/bc/6.0/` said what to do; if not,
  correct them ([Changelogs and upgrade notes](changelogs-and-bc-notes.md)).
- File a public issue for a bug in the software, with the diagnostics the help's **Report an error** page prepares
  ([When something goes wrong](../when-something-goes-wrong.md)).

## Related pages

- [Observability: logs, debug bar and audit trail](observability-logs-debug-and-audit.md)
- [Runbooks for hosts](../hosting/runbooks/README.md)
- [Troubleshooting](../../install/12-troubleshooting.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
