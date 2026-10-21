---
title: "Deploying: planning the hosting and checking the server"
audience: admin, builder, developer
category: books
order: 550
summary: The hosting questions the book asks before a go-live (visitors, system size, security, reporting, budget, time, shared or dedicated), a load test with siege, the documentation to keep, and where today's deployment guides are.
keywords: deploy, hosting requirements, visitors, concurrency, bandwidth, security, reporting, load test, siege, benchmark, documentation, disaster recovery, go live, Learning book
type: help
---
# Deploying: planning the hosting and checking the server

Chapter 7 of the book is not about installing a server. It is about what the system needs to work well where you deploy it,
and about getting the answers before they cost money. The checklist below is the book's, made current. The mechanics of
going live are in [Deploying Exponential](../../guides/deploying.md).

## Define the hosting requirements first

| Question | What to decide |
|---|---|
| **Number of visitors** | Users per hour, concurrent users, total users. These set the size of the server and whether you need caches in front. |
| **System size** | Disk for the files (`var/`) and the database; the bandwidth, which grows with images and downloads. |
| **Security** | What the site holds (payment or medical details, personal data) and who may reach it: the internet, an extranet for authenticated users, or a closed intranet. See [Security hardening](../security-hardening.md). |
| **Reporting** | Who needs visitor numbers and bandwidth, and from which statistics tool (the book names three log analyzers; use what your host provides, or an analytics service). |
| **Budget, time limits** | The live date, the date the environment is ready, and the time to deploy. |
| **Shared or dedicated** | Speed of service, the number of sites that share the server, and the price of extra bandwidth. |

Unforeseen problems at deployment turn directly into cost. The book's last line of the list still applies: **if in doubt,
do not go live.**

## Is the server powerful enough?

Test before you launch. The book uses `siege`, a load tester, with a number of concurrent users for an hour against your own
site, and checks the failed requests and response times. Exponential has a built-in measuring command that does the same
for pages, cached and uncached, and compares runs against a baseline: [Benchmarking Exponential](../../guides/benchmarking.md).
Test with the caches in the state they will have live, and test a page that does work (search, a long list), not only the front
page.

## Keep the documentation

The book's list of what a hosting document holds is worth keeping for every site: contact details (who to call and when), the
hardware or virtual machine, the operating system and software versions, the patching process, DNS and network
information, access control (who has which login), an **upgrade roadmap**, and the **disaster recovery** plan (what is backed up,
how often, and who has restored it from the backup at least once). [Operating a site](../../guides/operating-a-site.md) and
[Upgrading](../../guides/upgrading.md) are the guides to build it from.

## Preparing the Linux environment

The Linux specifics of the book (the packages of a distribution of its day) are replaced by [Requirements](../../install/02-requirements.md)
and [Choosing an install method](../../install/04-choosing-an-install-method.md). The point of the section stays: set up the
server to the requirements of the installation, with the right PHP extensions and a `memory_limit` that passes the system
check ([installation problems](installation-problems-then-and-now.md)). Then run the setup wizard or the console install
([the setup wizard](the-setup-wizard-then-and-now.md)), and run the cronjobs.

Next: [a standards-compliant site](a-standards-compliant-site.md).

## Source and license

Adapted from chapter 7, "Deploying eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: the checklist kept, the tools and the server preparation replaced by Exponential 6 guides.
