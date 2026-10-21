---
title: The adoption timeline
audience: adopter, project-manager, businesses
category: adoption
order: 33
summary: How adopters build a timeline for an Exponential adoption from their own measurements instead of guesses - the phases, what to measure in a rehearsal, buffers, fixed dates, and a template.
keywords: timeline, schedule, phases, rehearsal, measurement, buffer, launch date, content freeze, template
type: help
---
# The adoption timeline

The project publishes no durations for an adoption or a migration. Build the timeline from your own measurements.

## Contents

1. [The phases](#the-phases)
2. [Measure, then schedule](#measure-then-schedule)
3. [Fixed dates and buffers](#fixed-dates-and-buffers)
4. [A template](#a-template)

## The phases

| Phase | Depends on |
|---|---|
| Content modeling | Number of content types and how different they are from today |
| Design and templates | Number of page types; whether a theme is reused |
| Custom extensions | Requirements the standard features do not cover |
| Migration and rehearsals | Amount of content and files; URL rules ([Migrating](../migrating/README.md)) |
| Training | Number of editors and roles |
| Launch and hypercare | Size of the site and traffic |

## Measure, then schedule

"Measure the duration in a rehearsal and plan from the measurement, as the installation book advises"
([Switching to Exponential](../../businesses/choosing/switching-to-exponential.md)). Measure in particular:

- the full migration run on a staging copy, end to end;
- reindexing the search;
- rebuilding URL aliases for every language;
- the time an editor needs for a typical task on the new site.

## Fixed dates and buffers

- Do not fix a launch date before the first rehearsal.
- Avoid launching in your busiest season ([Peak traffic planning](../../businesses/reliability/peak-traffic-planning.md)).
- Plan a content freeze for the final run, and say how long it lasts.
- Keep a buffer; the size is your decision (an example assumption: a fifth of the measured time).

## A template

```text
Phase                      Start     End      Measured from          Owner
Content modeling
Design and templates
Extensions
Migration rehearsal 1                         <measured duration>
Migration rehearsal 2
Training
Content freeze
Final migration and launch
Hypercare (first weeks)
```

## Related pages

- [Planning the adoption](README.md)
- [Planning an Exponential project](../../leadership/planning-an-exponential-project.md)
- [Release and upgrade planning](../../leadership/release-and-upgrade-planning.md)
- [Adoption roadmap](../adoption-roadmap.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
