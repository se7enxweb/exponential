---
title: Hosting Exponential in the public sector
audience: government, hosting, admin, project-manager
category: public-sector
order: 10
summary: Index of the hosting pages for public bodies: where and how to run Exponential (own data center, government or sovereign cloud, containers), which database and web server, separate environments and administration, data location, hardening, configuration control and a decision record.
keywords: hosting, public sector, government, on-premise, sovereign cloud, containers, database, web server, Velocity, environments, siteaccess, data location, hardening, version control, decision record
type: help
---
# Hosting Exponential in the public sector

This folder is for in-house IT teams, architects and project managers in public bodies who decide where and how an
Exponential site runs. It covers the hosting model, the database and web server, separate environments, keeping the
administration off the public internet, where data is stored, hardening, configuration under version control and a
decision record to file with your change board. The step-by-step server pages were written for hosting companies and
live in [the hosting section](../../../hosting/hosting-companies-start-here.md); these pages link to them and add what
differs for a public body: in-house teams, government clouds, change boards, procurement constraints and continuity
duties.

## Pages

| Page | Answers |
|---|---|
| [On-premise hosting in your own data center](on-premise-hosting.md) | What a site needs from your own servers, which operating systems are tested, and who does what. |
| [Government and sovereign cloud hosting](government-and-sovereign-cloud.md) | What a government or sovereign cloud must provide, and what to ask the provider. |
| [Containers and orchestration](containers-and-orchestration.md) | How to run it in Docker, Podman or Kubernetes, and why production pins a fixed image tag. |
| [Choosing a database in the official order](database-choice-in-the-official-order.md) | The six supported databases and how to match one to your database standard. |
| [Web server choices for public-sector sites](web-server-choices.md) | Velocity, FrankenPHP or Apache and nginx with PHP-FPM, and approved software lists. |
| [Velocity for public-sector teams](velocity-for-public-sector-teams.md) | Starting, restarting, deploying, logging and assessing the recommended engine. |
| [Environments for development, test and production](environments-dev-test-prod.md) | How `EXP_ENV` gives each machine its own settings and keeps test copies quiet. |
| [Separating the administration from the public site](separating-admin-and-public-siteaccesses.md) | How to give the administration its own host name, network restriction and sign-in rules. |
| [Data location and what is stored where](data-location-and-what-is-stored-where.md) | Every place data is kept, which holds personal data, and what leaves the server. |
| [Hardening the host](hardening-the-host.md) | The hardening layers, the checks that prove them and how to map them to a baseline. |
| [Configuration under version control](configuration-under-version-control.md) | Keeping code and settings in Git and locking the administration with `ManagedByDeployment`. |
| [A hosting decision record](hosting-decision-record.md) | A template for documenting every hosting choice for a change board or audit. |

## Reading order

1. Decide the model: [On-premise hosting](on-premise-hosting.md) or [Government and sovereign cloud hosting](government-and-sovereign-cloud.md), then [Containers and orchestration](containers-and-orchestration.md) if your platform uses them.
2. Choose the components: [database](database-choice-in-the-official-order.md), [web server](web-server-choices.md), and if you choose it, [Velocity](velocity-for-public-sector-teams.md).
3. Set up the site safely: [environments](environments-dev-test-prod.md), [separate administration](separating-admin-and-public-siteaccesses.md), [hardening](hardening-the-host.md), [configuration control](configuration-under-version-control.md).
4. Document it: [data location](data-location-and-what-is-stored-where.md) and the [decision record](hosting-decision-record.md).

Then continue with [Availability](../availability/README.md) and [Backups and recovery](../backups-and-recovery/README.md).

## Related pages

- Up: [Operations](../README.md)
- Start of the section: [Governments: start here](../../government-start-here.md)
- For hosting companies: [Hosting companies start here](../../../hosting/hosting-companies-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
