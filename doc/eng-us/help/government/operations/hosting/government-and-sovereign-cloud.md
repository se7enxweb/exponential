---
title: Government and sovereign cloud hosting
audience: government, hosting, admin, project-manager
category: public-sector
order: 30
summary: For public bodies that must or want to run Exponential in a government cloud, a sovereign cloud or a national provider's virtual machines. What the software needs from such a platform, which hosting models the sources cover, and what to ask the provider.
keywords: government cloud, sovereign cloud, public cloud, national cloud, cloud provider, virtual machines, IaaS, PaaS, managed hosting, data residency, sovereignty, procurement, cloud questionnaire
type: help
---
# Government and sovereign cloud hosting

This page is for IT architects and project managers in public bodies whose sites must run on a government cloud, a
"sovereign" cloud offered under national rules, or the virtual machines of an approved provider. It answers what such
a platform must provide for Exponential, which hosting models the project's sources describe, and which questions to
put to the provider before you sign. It describes the software; which cloud you may use is decided by your own
policies.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Exponential needs no particular cloud](#exponential-needs-no-particular-cloud)
2. [Which hosting model fits](#which-hosting-model-fits)
3. [What the platform must provide](#what-the-platform-must-provide)
4. [Questions for the provider](#questions-for-the-provider)
5. [Keeping the option to leave](#keeping-the-option-to-leave)
6. [Checklist](#checklist)

## Exponential needs no particular cloud

Exponential is a PHP application. It does not call home, needs no license server and has no cloud service of its own
that a site depends on. The administration serves its scripts locally: jQuery and the other admin scripts are loaded
from the installation, not from a CDN (`ezjscore.ini [eZJSCore] LoadFromCDN=disabled`), and the admin4 design loads no
web fonts or scripts from other hosts ([Privacy standards](../../../../standards/privacy.md)). Cloud virtual machines
are "ordinary Linux machines, in the tier of their distribution"
([Operating systems for hosts](../../../hosting/basics/operating-systems-for-hosts.md)).

Some extensions that ship with the code do contact third parties once you activate them (statistics, captchas, social
login). They are off until a site switches them on; the list is in
[Privacy standards](../../../../standards/privacy.md). Check `ActiveExtensions[]` before go-live.

## Which hosting model fits

The hosting section compares the models from what the sources say. Each applies inside a government cloud as well:

| Model | When it fits a public body | Page |
|---|---|---|
| Virtual machines (IaaS) | the default choice; your team runs PHP, the database and the engine | [VPS hosting](../../../hosting/models/vps-hosting.md) |
| Containers | your platform team standardizes on Docker or Podman | [Containers and orchestration](containers-and-orchestration.md) |
| Kubernetes | the cloud's managed Kubernetes is your organization's standard | [Kubernetes](../../../hosting/models/kubernetes.md) (tier 3) |
| Platform as a service | the provider runs PHP for you | [Platform as a service](../../../hosting/models/platform-as-a-service.md) (tier 3) |
| Managed hosting | a contractor operates the site for you | [Managed hosting](../../../hosting/models/managed-hosting.md) |

A single virtual machine with the default database (SQLite 3) is the simplest setup. Several machines need a database
server and shared storage ([High availability overview](../availability/high-availability-overview.md)).

## What the platform must provide

| Need | Why | Detail |
|---|---|---|
| PHP 8 (8.0 or later, for Velocity too), command line and web | the application and its cronjobs | [PHP versions and extensions](../../../hosting/basics/php-versions-and-extensions.md) |
| Persistent storage for `var/` and `settings/override` | uploads, logs, the audit trail, the SQLite database | [Storage layout](../../../hosting/operations/storage-layout.md) |
| A database, if not SQLite | one of the supported engines; a managed database service is fine if it is one of them | [Database choice in the official order](database-choice-in-the-official-order.md) |
| A scheduler | `runcronjobs.php` every few minutes, or Velocity's own scheduler | [Cron jobs](../../../hosting/operations/cron-jobs.md) |
| Outgoing mail | password and notification mails | [Mail delivery](../../../hosting/operations/mail-delivery.md) |
| Snapshots or backup storage in a second location | continuity | [Backups and recovery](../backups-and-recovery/README.md) |

A platform with a read-only or ephemeral file system needs `var/` on persistent storage; see
[Platform as a service](../../../hosting/models/platform-as-a-service.md).

## Questions for the provider

Put these into your procurement documents or the provider's security questionnaire. None of them is specific to
Exponential; they make sure the platform can carry the duties described elsewhere in this section.

| Question | Why it matters |
|---|---|
| In which countries and data centers are the machines, the storage and the backups? | where content, accounts and logs are (see [Data location and what is stored where](data-location-and-what-is-stored-where.md)) |
| Who at the provider can read the disks and the snapshots? | `settings/override/` holds database credentials and the audit keys |
| Which PHP versions and extensions can we install, and how fast are security updates applied? | the software needs PHP 8 with defined extensions |
| Can we run a long-lived process (Velocity) and bind ports? | decides between Velocity and a traditional web server ([Web server choices](web-server-choices.md)) |
| Is a managed PostgreSQL, MariaDB or MySQL offered, in which versions? | the supported engines and their minimum versions are in [Database support](../../../database-support.md) |
| How are snapshots kept, for how long, and in a second location? | your recovery decisions ([RPO and RTO as decisions](../backups-and-recovery/rpo-and-rto-as-decisions.md)) |
| Can we export everything (disks, database dumps) in standard formats at the end of the contract? | exit strategy |

## Keeping the option to leave

Exponential stores its data in a standard database and plain files, so moving a site is a copy of the database and
the files plus a fresh code checkout ([Moving a site between hosts](../../../hosting/migrations/moving-a-site-between-hosts.md)).
Keep that true:

- avoid provider-specific services the software does not need;
- keep code and settings in your own version control ([Configuration under version control](configuration-under-version-control.md));
- test a restore of your backups on a machine outside the provider at least once.

## Checklist

- [ ] The provider's answers to the questions above are on file.
- [ ] Persistent storage holds `var/` and `settings/override`.
- [ ] Activated extensions that contact third parties are known and justified.
- [ ] Backups are kept in a second location you control.
- [ ] An export and restore outside the provider has been tested.

## Related pages

- [Hosting](README.md) index of this folder
- [On-premise hosting](on-premise-hosting.md)
- [Choosing a hosting model](../../../hosting/models/choosing-a-hosting-model.md)
- [Compliance and GDPR for hosts](../../../hosting/security/compliance-and-gdpr.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
