---
title: Incident readiness
audience: government, admin, hosting, project-manager
category: public-sector
order: 140
summary: What a public body has in place before an incident hits its Exponential site - contacts, runbooks, maintenance mode, logs and audit, backups, communication and reporting duties - so the first hour is spent acting, not searching.
keywords: incident, incident response, readiness, outage, compromise, runbook, maintenance mode, audit trail, logs, contacts, communication, NIS2, reporting, public sector
type: help
---
# Incident readiness

This checklist is for the site owner, site administrator and host of a public body. An incident is any event that
takes the site down, damages its data or exposes it to attack. The hosting runbooks hold the technical steps
([Runbooks for hosts](../../hosting/runbooks/README.md)); this page checks that your organization is ready to use them.

> **Note** This page describes features and common practice; it is not legal advice. Reporting duties (for example
> under the [NIS2 Directive](https://eur-lex.europa.eu/eli/dir/2022/2555/oj) or the GDPR's breach notification rules)
> depend on your organization; check them with your legal counsel.

## Contents

1. [People and contacts](#people-and-contacts)
2. [Runbooks](#runbooks)
3. [Tools you can reach for](#tools-you-can-reach-for)
4. [Evidence](#evidence)
5. [Communication and reporting](#communication-and-reporting)
6. [Practice](#practice)

## People and contacts

- [ ] A named incident lead and deputy, reachable outside office hours.
- [ ] Contacts of the host, the support supplier and your security team, with the hours they cover.
- [ ] Who may decide to take the site offline.
- [ ] The contact list stored outside the site itself (the site may be down).

## Runbooks

- [ ] The host knows where its runbooks are: [Site down](../../hosting/runbooks/site-down.md), [Slow site](../../hosting/runbooks/slow-site.md), [Compromised site](../../hosting/runbooks/compromised-site.md), [Disk full](../../hosting/runbooks/disk-full.md), [Restore from backup](../../hosting/runbooks/restore-from-backup.md), [Certificate expiry](../../hosting/runbooks/certificate-expiry.md), [Velocity will not start](../../hosting/runbooks/velocity-will-not-start.md).
- [ ] Your site-specific additions written down: where it runs, which engine, which database.
- [ ] [When something goes wrong](../../when-something-goes-wrong.md) known to the site administrator.

## Tools you can reach for

- [ ] **Maintenance mode**: **Setup > Maintenance**, or from a shell `php bin/php/maintenance.php on --message="..." --until=2h --allow-admin`; deleting `var/maintenance.json` ends it when nothing else works ([Maintenance mode](../../../guides/maintenance-mode.md)).
- [ ] **Sessions**: sign out every browser in **Setup > Sessions** after a suspected account compromise ([Sessions](../../../guides/sessions.md)).
- [ ] **Accounts**: deactivate a compromised account ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- [ ] **Caches**: clear the right cache after a fix, `php bin/php/ezcache.php --clear-id=<id>` or **Setup > Cache management** ([Caches](../../../guides/caches.md)).
- [ ] **Error references**: visitors' error messages carry a reference you can look up ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)).
- [ ] **Request filter**: request-shield can block or slow abusive clients before the kernel ([Security hardening](../../security-hardening.md#a-shield-in-front-request-shield)).

## Evidence

- [ ] The audit trail is on and its keys are backed up; verify its chain with `./console exp:audit verify` ([Security and audit](../../../guides/security-and-audit.md)).
- [ ] `var/log/security.log` records probes and attacks ([Request logs](../../../features/6.0/request-logs.md)).
- [ ] Logs are kept long enough to investigate, and copied off the server during an incident ([Log viewer](../../../features/6.0/log-viewer.md)).
- [ ] Know not to wipe a compromised server before evidence is secured ([Compromised site](../../hosting/runbooks/compromised-site.md)).

## Communication and reporting

- [ ] A prepared notice for the site's maintenance page and for social media.
- [ ] Your reporting duties listed, with deadlines and recipients.
- [ ] A way to report software vulnerabilities to the project privately, as `SECURITY.md` says ([Security standards](../../../standards/security.md#vulnerability-disclosure)).

## Practice

- [ ] A restore test this year ([Backup readiness](backup-readiness.md)).
- [ ] A tabletop exercise: walk through one runbook with the people on the contact list.
- [ ] Lessons from each incident recorded and the checklist updated.

## Related pages

- [Government checklists](README.md)
- [Emergency alert readiness](emergency-alert-readiness.md)
- [Security checklist](security-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
