---
title: Estimate hosting costs with your own figures
audience: government, project-manager, hosting
category: public-sector
order: 130
summary: Step by step, estimate what hosting an Exponential site will cost a public body over a contract term - sizing from measurements, the cost lines, in-house versus outsourced, and a worksheet - using only your own figures and quotes.
keywords: hosting costs, estimate, sizing, budget, in-house hosting, managed hosting, worksheet, total cost of ownership, procurement task
type: help
---
# Estimate hosting costs with your own figures

**Who:** the project manager with an IT specialist. **When:** while building the budget or comparing offers. The
project publishes no hosting prices; this page gives the structure, you supply the numbers. Sizing facts:
[Sizing memory, CPU and disk](../../../hosting/basics/sizing-memory-cpu-and-disk.md).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Worksheet](#worksheet)
4. [Result](#result)
5. [Pitfalls](#pitfalls)

## Before you start

- An idea of your traffic, content volume and storage (from your current site's statistics).
- At least one quote, or your data center's internal rates.

## Steps

1. **Size from measurements**, not guesses: the sizing page lists the figures the repository states and how to measure
   a real site; a proof-of-concept installation with your content helps ([Run a proof of concept on SQLite in one command](run-a-proof-of-concept-on-sqlite.md)).
   `exp:benchmark` measures speed under load ([Benchmarking](../../../../guides/benchmarking.md)).
2. **Choose environments**: production, staging, and perhaps training; each costs.
3. **Choose the model**: shared, VPS, dedicated, containers, managed ([Choosing a hosting model](../../../hosting/models/choosing-a-hosting-model.md)).
4. **List the cost lines** in the worksheet below, for each year of the term.
5. **Add people**: in-house hosting needs staff time for operations, updates and on-call; managed hosting includes some
   of it ([Hosting costs](../../procurement/total-cost-of-ownership/hosting-costs.md)).
6. **Add growth**: storage grows with uploads; logs and backups grow too.
7. **Compare** in-house and outsourced on the same lines ([On-premise vs cloud](../../procurement/total-cost-of-ownership/on-premise-vs-cloud.md)).

## Worksheet

```text
Hosting estimate - <site>, term <years>              all amounts: your own figures or quotes

                                   Year 1    Year 2    Year 3 ...
Production server(s) / plan        <...>
Staging environment                <...>
Database server (if separate)      <...>
Storage and growth                 <...>
Backups: storage off-site          <...>
TLS certificates (often free)      <...>
Monitoring / uptime checks         <...>
Staff time: operations x rate      <...>
On-call cover                      <...>
Search service (Solr), if used     <...>
Third-party services (mail, CDN)   <...>
Total per year                     <...>
```

## Result

A yearly hosting line for the total cost of ownership model, with its assumptions written down.

## Pitfalls

- Pricing production only, without staging and backups.
- Forgetting staff time when hosting in-house.
- Using another organization's sizing without measuring your own site.

## Related pages

- [Procurement and training tasks](README.md)
- [Compare hosting offers](compare-hosting-offers.md)
- [TCO worksheet template](../../procurement/total-cost-of-ownership/tco-worksheet-template.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
