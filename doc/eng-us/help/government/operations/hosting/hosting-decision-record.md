---
title: A hosting decision record
audience: government, hosting, project-manager, admin
category: public-sector
order: 130
summary: For project managers and architects in public bodies who must document how an Exponential site is hosted. A decision record template with every choice the software asks for, the options and where each option is described, ready to file with a change board or an audit.
keywords: decision record, architecture decision, hosting decision, template, documentation, change board, audit, procurement, database choice, web server choice, backups, availability, data location
type: help
---
# A hosting decision record

This page is for project managers, architects and IT leads in public bodies who must write down how a site is
hosted and why, for a change advisory board, an audit, a procurement file or the team that takes over later. It gives
a decision record template with every choice Exponential asks for, the options the sources support and where each is
explained. Fill it in once per site and keep it with the site's documentation. The hosting companies' version is in
[Choosing a web server](../../../hosting/servers/choosing-a-web-server.md) and
[Choosing a hosting model](../../../hosting/models/choosing-a-hosting-model.md).

## Contents

1. [How to use the template](#how-to-use-the-template)
2. [The template](#the-template)
3. [Facts to collect from the running site](#facts-to-collect-from-the-running-site)
4. [Keeping it current](#keeping-it-current)
5. [Checklist](#checklist)

## How to use the template

- One record per site (or per installation serving several sites).
- Each row: the decision, the reason, and the risk you accept. "We chose X because our database team runs X" is a
  perfectly good reason.
- Write team names, not people's names, so the record does not age with staff changes.
- Name figures only when you measured or decided them; mark assumptions as "example assumption".

## The template

| Decision | Options in the sources | Our choice | Reason | Accepted risk | Read |
|---|---|---|---|---|---|
| Hosting location and model | own data center; government or sovereign cloud; virtual machine; containers; Kubernetes (tier 3); managed hosting | | | | [On-premise hosting](on-premise-hosting.md), [Government and sovereign cloud hosting](government-and-sovereign-cloud.md) |
| Operating system | tier 1 or 2 recommended | | | | [Operating system support](../../../os-support.md) |
| PHP version | 8.0 or later; 8.1 or later for Velocity | | | | [PHP versions and extensions](../../../hosting/basics/php-versions-and-extensions.md) |
| Database | 1. SQLite 3 · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL | | | | [Choosing a database in the official order](database-choice-in-the-official-order.md) |
| Web server or engine | Velocity (recommended); FrankenPHP; Apache or nginx with PHP-FPM | | | | [Web server choices](web-server-choices.md) |
| TLS termination | the engine; the web server; a central proxy | | | | [TLS and HTTPS](../../../hosting/security/tls-and-https.md) |
| Administration access | own host name; network restriction; VPN | | | | [Separating the administration from the public site](separating-admin-and-public-siteaccesses.md) |
| Environments | development, test, production with `EXP_ENV` | | | | [Environments for development, test and production](environments-dev-test-prod.md) |
| Configuration control | version control; `ManagedByDeployment` | | | | [Configuration under version control](configuration-under-version-control.md) |
| Number of web servers | one; several behind a load balancer | | | | [High availability overview](../availability/high-availability-overview.md) |
| Caches in front of PHP | Velocity response cache; HTTP cache; static cache (ships off) | | | | [Velocity response cache and HTTP cache](../availability/velocity-response-cache-and-http-cache.md) |
| Backups | what, how often, where, how long | | | | [What to back up](../backups-and-recovery/what-to-back-up.md) |
| Recovery objectives | RPO and RTO as decided by the organization | | | | [RPO and RTO as decisions](../backups-and-recovery/rpo-and-rto-as-decisions.md) |
| Mail | sendmail, SMTP, file (test) | | | | [Mail delivery](../../../hosting/operations/mail-delivery.md) |
| Search | built-in; Solr through ezfind | | | | [Search backends](../../../hosting/basics/search-backends.md) |
| Data location | inventory of every place data is kept | | | | [Data location and what is stored where](data-location-and-what-is-stored-where.md) |
| Active extensions contacting third parties | none, or listed and justified | | | | [Privacy standards](../../../../standards/privacy.md) |
| Monitoring | external uptime check; internal status commands | | | | [Health checks and uptime](../../../hosting/operations/health-checks-and-uptime.md) |

## Facts to collect from the running site

Attach these outputs to the record; they show what really runs, not what was planned:

```bash
php -v | head -1
./console exp:velocity ext check --allow-root-user
./console exp:system:info --json --allow-root-user > system-info.json
grep -rn "DatabaseImplementation" settings/override settings/siteaccess/*/site.ini.append.php
./console exp:cache status --allow-root-user
```

`exp:system:info` prints the facts and health checks of **Setup > System information**
([Sizing memory, CPU and disk](../../../hosting/basics/sizing-memory-cpu-and-disk.md)). Review the JSON before you file
it outside the IT team; it describes your server in detail. The page's support report is built to leave out what
should not be shared ([System information](../../../../guides/system-information.md)).

## Keeping it current

Update the record with every change that touches a row: a new PHP version, a database move, a second web server, a
new extension that contacts a third party. A change request that does not mention the record is a hint that the
record is out of date. Review it at least once a year, together with the recovery decisions and a restore test.

## Checklist

- [ ] Every row has a choice, a reason and an accepted risk.
- [ ] The facts from the running site are attached and match the choices.
- [ ] The record names teams, not people.
- [ ] It is filed where the change board and auditors find it.
- [ ] A review date is set.

## Related pages

- [Hosting](README.md) index of this folder
- [Designing hosting plans](../../../hosting/business/designing-hosting-plans.md) for a provider's view of the same choices
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
