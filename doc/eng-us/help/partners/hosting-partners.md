---
title: Hosting partners
audience: partner
category: partnership
order: 380
summary: For hosting partners - what the profile means, the hosting options the program names, what an Exponential host must provide (PHP, databases, engines, backups, security), services to offer and how hosting partners give back.
keywords: hosting partner, hosting, managed hosting, PHP, databases, Velocity, PHP-FPM, FrankenPHP, backups, security, uptime, infrastructure
type: help
---
# Hosting partners

This page is for partners with the Hosting profile. "Hosting Partners provide the secure, scalable infrastructure
essential for Exponential applications" and "Provide the infrastructure for hosting Exponential-based solutions.
They have expertise in designing the optimal environment for a specific solution"
(<https://exponential.earth/partner/hosting-partners>, <https://exponential.earth/partner/program>, read 18 October
2026).

## Hosting options the program names

The hosting partners page lists "7x Premium Hosting" and, as "Alternative Hosting Providers who are known to work
well. Note that no one has paid 7x for placement": Hosting.com ("Formerly a2hosting.com") and Amazon AWS. A hosting
partner joining the program adds its own offer to this landscape.

## What an Exponential host must provide

| Area | Requirement | Source |
|---|---|---|
| PHP | 8.0 to 8.5 for the 6.0.15 line, Velocity included (from its release 0.0.4.54) | `doc/eng-us/install/02-requirements.md` |
| PHP extensions | Among others `dom`, `xsl`, `mbstring`, `iconv`, `intl`, `zlib`, and one database extension | same |
| php.ini | `memory_limit` 64M or more, `max_execution_time` 30, `date.timezone` set, `file_uploads` on | same |
| Database | SQLite 3 (default), PostgreSQL, MongoDB, MariaDB, Oracle or MySQL, in that order of preference | [Database support](../database-support.md) |
| Operating system | See the tiers | [Operating system support](../os-support.md) |
| Engine | Velocity (recommended), FrankenPHP, or a web server with PHP-FPM | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-velocity) |
| Command line | PHP CLI with the same version and extensions as the web PHP, for cronjobs and scripts | `doc/eng-us/install/02-requirements.md` |
| Security | TLS, security headers, user separation, no exposed installer | [Security hardening](../security-hardening.md) |
| Backups | Database and storage, restore tested | [Database support](../database-support.md#backup-and-restore) |

The detailed checklist: [Hosting partner technical checklist](hosting-partner-technical-checklist.md).

## Services to offer

- Managed Exponential hosting with updates and security fixes applied.
- Staging environments for release tests ([Testing releases](testing-releases.md)).
- Backups with tested restores.
- Performance tuning: caches, engine, database.
- Migration of existing sites onto current PHP.

## Working with other partners

Hosting partners often serve implementation and creative partners' customers. Agree in writing who is responsible
for what: the server and PHP (host), the application and its extensions (implementation partner), the content
(customer).

## Giving back

- Report environment-specific bugs with exact versions ([Filing bugs well](filing-bugs-as-a-partner.md)).
- Document what works on your platform (OS, panel, container) as a tutorial; the project keeps an
  [Operating system support](../os-support.md) page that grows with such reports.

## Related pages

- [Hosting partner technical checklist](hosting-partner-technical-checklist.md)
- [Choosing hosting, database and OS](../leadership/choosing-hosting-database-and-os.md)
- [Partner profiles](partner-profiles.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
