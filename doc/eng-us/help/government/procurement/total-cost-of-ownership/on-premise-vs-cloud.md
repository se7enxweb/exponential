---
title: On-premise or cloud hosting for an Exponential site
audience: government, project-manager, hosting, admin
category: public-sector
order: 100
summary: A comparison of running an Exponential site in your own data center, on a private or public cloud, or with a managed host - what each model needs from the software, which costs move where, data location and control, and a decision table. No prices.
keywords: on-premise, cloud, hosting model, private cloud, public cloud, managed hosting, data center, data location, sovereignty, containers, Kubernetes, comparison, TCO
type: help
---
# On-premise or cloud hosting for an Exponential site

This page is for decision makers who compare running an Exponential site on their own infrastructure with renting
it from a cloud or managed host. Because Exponential is free software that runs on ordinary Linux servers with PHP,
all models are open to you; the choice shifts costs and control. Technical facts come from
[Choosing a hosting model](../../../hosting/models/choosing-a-hosting-model.md) and the hosting section.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [The models](#the-models)
2. [What the software needs in every model](#what-the-software-needs-in-every-model)
3. [Where costs move](#where-costs-move)
4. [Control and data location](#control-and-data-location)
5. [A decision table](#a-decision-table)
6. [Pitfalls](#pitfalls)

## The models

| Model | You operate | Provider operates | Documentation |
|---|---|---|---|
| On-premise | everything: hardware, OS, PHP, database, backups | nothing | [Dedicated servers](../../../hosting/models/dedicated-servers.md) |
| Infrastructure in a cloud (virtual machines) | OS and up | hardware, network | [VPS hosting](../../../hosting/models/vps-hosting.md) |
| Containers or Kubernetes | images, volumes, manifests | the cluster platform (if rented) | [Containers](../../../hosting/models/containers-docker-and-podman.md), [Kubernetes](../../../hosting/models/kubernetes.md) |
| Platform as a service | the application | runtime, scaling | [Platform as a service](../../../hosting/models/platform-as-a-service.md) |
| Managed hosting | content and site decisions | servers, updates, backups, monitoring | [Managed hosting](../../../hosting/models/managed-hosting.md) |

The sources place each model in the project's tier scale: containers tier 2, Kubernetes and platform as a service
tier 3, "a reasoned expectation, not a test result". Plan a test before you commit to a tier 3 model.

## What the software needs in every model

- PHP 8.0 or newer (for Velocity too) and a supported database ([Requirements](../../../../install/02-requirements.md)).
- Persistent storage for `var/` (uploads, logs, audit) - important on platforms with ephemeral file systems.
- Cron jobs for background work ([Cron jobs](../../../hosting/operations/cron-jobs.md)).
- For several web nodes: a database server and shared `var/storage`; SQLite fits one writing machine only.
- Backups of database and files together, and of the generated keys in `settings/override/`
  ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md)).

## Where costs move

| Cost | On-premise | Cloud infrastructure | Managed hosting |
|---|---|---|---|
| Hardware | yours, bought up front | in the provider's fee | in the provider's fee |
| Operations staff | yours | yours (OS and up) | mostly the provider's |
| Security patching | yours | yours above the platform | provider, as contracted |
| Scaling for peaks | buy for the peak | pay for what you use | as contracted |
| Exit | you hold everything | export volumes and database | needs an offboarding clause |

None of these is cheaper in general; compare with your own quotes in the [TCO worksheet](tco-worksheet-template.md).

## Control and data location

- On-premise and your own cloud tenancy keep data where you decide.
- With a provider, ask where data, backups and logs are stored, who can access them, and under which agreement. In
  the EU, a provider that processes personal data for you needs a data processing agreement under the
  [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj).
- Exponential's administration loads no scripts or fonts from other hosts by default, so the administration itself
  sends no data to third parties ([Privacy standards](../../../../standards/privacy.md)).
- Security duties may apply to you whichever model you choose; in the EU see the
  [NIS2 Directive](https://eur-lex.europa.eu/eli/dir/2022/2555/oj) for entities in its scope.

## A decision table

| If | Lean towards |
|---|---|
| you have operations staff and data center capacity | on-premise or private cloud |
| you need fast scaling and have cloud skills | cloud infrastructure or containers |
| you have no operations staff | managed hosting with strong offboarding terms |
| data must stay in a named country | any model whose provider guarantees it in writing |
| availability above one server is required | several nodes with a database server and shared storage |

## Pitfalls

- Choosing a platform with an ephemeral file system without persistent `var/`.
- Comparing a managed price with an on-premise price that leaves out staff time.
- No offboarding terms with a managed host ([Customer offboarding](../../../hosting/checklists/customer-offboarding.md)).

## Related pages
- [Total cost of ownership: overview](README.md)
- [Hosting costs](hosting-costs.md)
- [Hosting companies: start here](../../../hosting/hosting-companies-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
