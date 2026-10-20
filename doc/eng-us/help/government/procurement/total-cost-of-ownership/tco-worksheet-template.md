---
title: TCO worksheet template
audience: government, project-manager
category: public-sector
order: 120
summary: A copy-and-fill worksheet for the total cost of ownership of a website or intranet on Exponential, with one-off, recurring, internal, risk and exit lines, an assumptions register and a comparison block for several bids. All values are blanks.
keywords: TCO worksheet, cost template, spreadsheet template, budget template, assumptions register, bid comparison, total cost of ownership
type: help
---
# TCO worksheet template

This page is for the person who fills in the total cost of ownership of a content management system. Copy the
blocks below into a spreadsheet or document. Every value is a blank; the structure follows
[The structure of a TCO model](tco-model-structure.md) and the lines of [Cost categories](cost-categories.md).

> **Template** - adapt to your procurement rules; not legal advice.

## Contents
1. [Header](#header)
2. [Assumptions register](#assumptions-register)
3. [One-off costs](#one-off-costs)
4. [Recurring costs](#recurring-costs)
5. [Risk and exit](#risk-and-exit)
6. [Totals and comparison](#totals-and-comparison)
7. [Filling it in well](#filling-it-in-well)

## Header

```text
Project:            <name>
Option / bid:       <bidder or internal option>       Product: Exponential <version>
Horizon:            <n> years, from <date>
Currency:           <...>          Discount rate (if your rules require): <...>
Prepared by:        <name, role>   Date: <date>     Reviewed by: <name>
```

## Assumptions register

Every number used below needs a row here, with its source.

```text
ID   Assumption                          Value     Source (quote, record, PoC measurement)   Confidence (H/M/L)
A1   Horizon                             <...>     <...>                                       <...>
A2   Internal hourly rate per role       <...>     <finance department>                        <...>
A3   Supplier day rate                   <...>     <bid>                                       <...>
A4   Kernel license fee                  0         GPL-2.0-or-later, no fee                    H
A5   ...
```

## One-off costs

```text
Line                                         Qty     Unit       Rate     Amount    Assumption IDs
Tender, evaluation, proof of concept          <...>   hours      <...>    <...>     <...>
Implementation: content model                 <...>   days       <...>    <...>     <...>
Implementation: design and templates          <...>   days       <...>    <...>     <...>
Integrations                                  <...>   days       <...>    <...>     <...>
Content migration                             <...>   days       <...>    <...>     <...>
Hardening and penetration test                <...>   days       <...>    <...>     <...>
Accessibility audit and statement             <...>   days       <...>    <...>     <...>
Hosting setup                                 <...>   lump sum   -        <...>     <...>
Initial training: trainers                    <...>   days       <...>    <...>     <...>
Initial training: participants' time          <...>   hours      <...>    <...>     <...>
Internal project staff                        <...>   hours      <...>    <...>     <...>
One-off subtotal                                                          <...>
```

## Recurring costs

```text
Line                                   Per year   Years   Amount   Assumption IDs
License (kernel)                       0          <n>     0        A4
Paid extensions or services            <...>      <n>     <...>    <...>
Hosting: production                    <...>      <n>     <...>    <...>
Hosting: staging, training             <...>      <n>     <...>    <...>
Backups and storage                    <...>      <n>     <...>    <...>
Support contract                       <...>      <n>     <...>    <...>
Releases, updates, regression tests    <...>      <n>     <...>    <...>
Periodic accessibility / security test <...>      <n>     <...>    <...>
Editors' extra time                    <...>      <n>     <...>    <...>
Site administrator                     <...>      <n>     <...>    <...>
System administration (if in-house)    <...>      <n>     <...>    <...>
Onboarding training                    <...>      <n>     <...>    <...>
Contract management                    <...>      <n>     <...>    <...>
Recurring subtotal                                        <...>
```

## Risk and exit

```text
Risk                                   Probability   Impact   Allowance   Notes
Supplier change mid-horizon            <...>         <...>    <...>       see exit plans
Upgrade larger than planned            <...>         <...>    <...>       custom code volume
Security incident                      <...>         <...>    <...>       <...>
Exit at end of horizon: export, redirects, parallel running   <...>
Risk and exit subtotal                                        <...>
```

## Totals and comparison

```text
                      Option A    Option B    Option C
One-off               <...>       <...>       <...>
Recurring             <...>       <...>       <...>
Risk and exit         <...>       <...>       <...>
TCO (nominal)         <...>       <...>       <...>
TCO (discounted)      <...>       <...>       <...>
Largest 3 lines       <...>       <...>       <...>
Lowest-confidence assumptions: <IDs>
```

## Filling it in well

- Use the same horizon, rates and assumptions for every option, except where the bids really differ.
- Fill internal time from measurements in the proof of concept, not guesses
  ([Proof-of-concept plan](../evaluation/proof-of-concept-plan.md)).
- Check the shape of your result against the [worked example](worked-example.md); if one line dominates, make sure
  its assumptions are the best evidenced.
- Keep the worksheet with the tender file; a debrief may ask how price was compared.

## Related pages
- [Total cost of ownership: overview](README.md)
- [A worked TCO example](worked-example.md)
- [Procurement: overview](../README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
