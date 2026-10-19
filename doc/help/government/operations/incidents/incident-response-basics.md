---
title: Incident response basics
audience: government, admin, project-manager, hosting
category: public-sector
order: 20
summary: The common frame for every incident on a public-sector Exponential site - roles, severity, the first fifteen minutes, keeping evidence, communicating with the public, and closing with a review - with the tools Exponential provides at each stage.
keywords: incident response, incident management, severity, on-call, first response, evidence, communication, public notice, maintenance mode, audit trail, post-incident review, runbook
type: help
---
# Incident response basics

This page is for the people who handle incidents on a public website: the on-call administrator, the service owner
and the communications lead. It gives the frame every runbook in this folder follows. Your organization's own
incident process (and, where it applies, a national or sector incident reporting duty) comes first; this page shows
how Exponential fits into it.

## Contents
1. [Roles](#roles)
2. [Severity](#severity)
3. [The first fifteen minutes](#the-first-fifteen-minutes)
4. [Keeping evidence](#keeping-evidence)
5. [Communicating with the public](#communicating-with-the-public)
6. [Closing](#closing)
7. [Related pages](#related-pages)

## Roles

| Role | Does |
|---|---|
| Incident lead | decides, keeps the timeline, calls others in |
| Technical responder | works on the system with the runbooks |
| Communications | tells the public, staff and management what they need to know |
| Service owner | accepts risk decisions (take the site offline, restore a backup) |
| Security officer | joins when an attack or a data breach is possible |

In a small team one person holds several roles; name them anyway at the start.

## Severity

An example scale (adapt it): **1** the public site is down or defaced, or personal data may be exposed; **2** a major
function is broken (forms, search, publishing); **3** a degradation with a workaround. Severity decides who is called
and how fast.

## The first fifteen minutes

1. **Confirm** from outside: `curl -sI https://www.example.gov/ | head -5`, and the administration sign-in page.
2. **Look**: `./console exp:logs --tail=var/log/error.log --lines=50` and **Setup > Log files**, **Error digest**;
   the **Audit** tab for recent sign-ins and changes; `df -h` for the disk.
3. **Decide containment**: if visitors must not see the site as it is (defacement, wrong content, data exposure),
   switch on maintenance mode with a neutral message:

   ```bash
   php bin/php/maintenance.php on --message="This service is temporarily unavailable." --allow-admin --allow-ip=<your address> --allow-root-user
   ```

4. **Start a timeline**: time, who, what was seen, what was done. Exponential's audit trail records actions in the
   administration and many commands; your notes add what happened outside it.
5. Open the matching runbook in this folder.

## Keeping evidence

Before changing anything in a security incident: copy the files, a database dump, `var/log/` (with `audit/`) and the
web server and PHP logs to a place the site's user cannot write; run `./console exp:audit verify --allow-root-user`
and keep the output ([Compromised site (hosting runbook)](../../../hosting/runbooks/compromised-site.md#preserve-evidence)).

## Communicating with the public

- The maintenance page message is your first public statement: plain, no technical detail, where to get help in the
  meantime (phone, counter).
- Use the channels your organization uses for service notices; keep messages factual and update them when the
  situation changes.
- If personal data may be involved, coordinate every statement with the data protection officer and legal
  counsel ([Data breach notification framing](data-breach-notification-framing.md)).

## Closing

Bring the site back ([Bring the site back online](../../tasks/operations/bring-the-site-back-online.md)), confirm from
outside, clear caches in front of the site, tell the public, and hold a review within days
([Post-incident review](post-incident-review.md)).

## Related pages
- [Site down](site-down.md)
- [Suspected account compromise](suspected-account-compromise.md)
- [Monitoring overview](../monitoring/monitoring-overview.md)
- [Hosting runbooks](../../../hosting/runbooks/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
