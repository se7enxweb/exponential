---
title: Government cloud and hosting models
audience: government, admin, project-manager
category: public-sector
order: 40
summary: Which hosting models Exponential is documented for (shared, VPS, dedicated, containers, Kubernetes, platform as a service, managed) with the project's own support tiers, and the questions a public body asks before running it in a government or commercial cloud.
keywords: government cloud, hosting model, cloud, VPS, dedicated server, containers, Docker, Podman, Kubernetes, PaaS, managed hosting, tiers, sovereignty
type: help
---
# Government cloud and hosting models

This page is for IT managers and architects who decide where Exponential runs: a government cloud, a commercial
cloud region, or rented servers. Exponential does not depend on one provider; the hosting help describes each model
and rates it with the project's own scale of tiers ([Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md)).

## Contents

1. [The project's tiers](#the-projects-tiers)
2. [The models](#the-models)
3. [Questions for a cloud offer](#questions-for-a-cloud-offer)
4. [Several web nodes](#several-web-nodes)
5. [Decide and document](#decide-and-document)

## The project's tiers

The hosting help uses four tiers: 1 tested here, 2 known to work, 3 expected to work, 4 not possible today. A tier 3
entry is "a reasoned expectation, not a test result" ([The four tiers](../../os-support.md#the-four-tiers)).
For a public body, a tier 3 model means: plan a test installation and an acceptance test before committing.

## The models

| Model | Page | Note for public bodies |
|---|---|---|
| Shared hosting | [Shared hosting](../../hosting/models/shared-hosting.md) | rarely suitable for official sites: little isolation and control |
| Virtual private server | [VPS hosting](../../hosting/models/vps-hosting.md) | common for one site; you or the provider operate the OS |
| Dedicated server | [Dedicated servers](../../hosting/models/dedicated-servers.md) | for large sites or many sites with isolation |
| Containers (Docker, Podman) | [Containers](../../hosting/models/containers-docker-and-podman.md) | tier 2 in the sources; mind persistent volumes |
| Kubernetes | [Kubernetes](../../hosting/models/kubernetes.md) | tier 3; replicas need shared storage, sessions and one cron |
| Platform as a service | [Platform as a service](../../hosting/models/platform-as-a-service.md) | tier 3; `var/` must be on persistent storage |
| Managed hosting | [Managed hosting](../../hosting/models/managed-hosting.md) | a provider operates it; contract who does what |

## Questions for a cloud offer

| Question | Why |
|---|---|
| In which country and region are servers, storage and backups? | Data location rules (see "Data location") |
| Who can access the systems, from where, under which law? | Access by the provider's staff and authorities |
| Is the database a managed service, and which engine and version? | Must be one of the supported databases |
| Is the file storage persistent and shared between nodes? | Content files live under `var/` |
| Can cronjobs run reliably, once per installation? | Workflows, notifications and the audit need them |
| What do backups cover, how long are they kept, how is a restore tested? | Your records and continuity duties |
| How do you get all data and files out at the end? | Exit (see "Exit planning") |

## Several web nodes

More than one web server for one site needs shared file storage, a shared database, sessions that all nodes see,
cron on one node only, and a deploy that updates all nodes together ([Several web nodes](../../hosting/operations/several-web-nodes.md),
[Clustering and shared storage](../../hosting/operations/clustering-and-shared-storage.md)).

## Decide and document

Write down the model, the provider, the regions, the database, the backup location and the operator of each layer.
Review it when the contract renews.

## Related pages

- [Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md)
- [Operating system support](../../os-support.md)
- [Self-hosting on premise](self-hosting-on-premise.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
