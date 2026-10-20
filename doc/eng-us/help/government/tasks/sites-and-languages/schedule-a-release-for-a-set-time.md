---
title: Schedule a release for a set time
audience: government, admin, editor
category: public-sector
order: 410
summary: Task for administrators and editors: publish a page (a statistics release, an agenda) at a set date and time with the Wait until date workflow event, and know how precise the timing is.
keywords: scheduled publishing, embargo, Wait until date, workflow, release time, publish at, cronjob
type: help
---
# Schedule a release for a set time

This task is for administrators (setup) and editors (each release). Background:
[Scheduled publication and embargoes](../../transparency-and-foi/scheduled-publication-and-embargoes.md).

## Steps for administrators

1. Add a **Date and time** field "Publish at" to the class.
2. **Setup > Workflows**: a workflow with the event **Wait until date**; in the event choose the **Class**, **Update
   attributes**, the **Attribute** "Publish at", and add the combination.
3. **Setup > Triggers**: connect it to **Before publishing content** (combine with an approval workflow through a
   **Multiplexer** if one is there).
4. Make sure the `frequent` cronjob group runs often enough for your precision.

## Steps for editors

1. Prepare the page; set **Publish at** to the release time.
2. **Send for publishing**. The version waits; **Setup > Workflow processes** lists it.

## Check

The page is not public before the time, and is after the first workflow cronjob run that follows it. Check by hand at
release time.

## Problems

| Problem | Fix |
|---|---|
| Published minutes late | the cronjob interval; run it more often |
| Published at once | the date was in the past, or the class/attribute combination was not added |
| Files public too early | do not attach files to a published object before the release |

## Related pages

- [Workflows](../../../../guides/workflows.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
