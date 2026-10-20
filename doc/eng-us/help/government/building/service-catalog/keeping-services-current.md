---
title: Keeping services current
audience: government, website-creator, project-manager, admin
category: public-sector
order: 80
summary: How to keep service pages correct over time in Exponential - an owner per page, a required review date, a template list of overdue services, versions for what changed, and a monthly routine.
keywords: content upkeep, review date, content owner, overdue list, fetch, attribute_filter, currentdate, sections, owner, versions, editorial routine, content governance
type: help
---
# Keeping services current

This page is for website creators, project managers and administrators who keep a service catalog correct after
launch. Service information goes stale quietly: fees change, offices move, rules change. Exponential has no built-in
review reminder; this page shows how to build one from a review date attribute, a template list and a routine.

## Contents

1. [An owner for every service](#an-owner-for-every-service)
2. [The review date](#the-review-date)
3. [A list of overdue services](#a-list-of-overdue-services)
4. [Recording what changed](#recording-what-changed)
5. [A monthly routine](#a-monthly-routine)

## An owner for every service

| Who | Recorded as |
|---|---|
| The department that owns the service | the section of the service ([Sections](../../../../guides/sections.md)) |
| The person who maintains the page | the object's owner, changeable ([Changing owners](../../../../guides/changing-owners.md)) |
| The subject expert who confirms the facts | a role name in the procedure, not on the page |

When someone leaves, change the owner of their pages; **Users > Check user accounts** helps find accounts of people who
left ([Check user accounts](../../../../features/6.0/user-hygiene.md)).

## The review date

The Service class has a required **Next review** date ([Modeling services](modeling-services.md)). Typical cycles:

| Service | Review at least |
|---|---|
| Fees and charges | yearly, before new rates apply |
| Legal requirements | when the law changes, and yearly |
| Contact, hours | through the contact point objects; each service yearly |
| Stable information services | every two years |

## A list of overdue services

A page in a restricted section (only staff may read it) with a template that lists services whose review date has
passed:

```text
{def $overdue = fetch( 'content', 'tree', hash(
        'parent_node_id', ezini( 'NodeSettings', 'RootNode', 'content.ini' ),
        'class_filter_type', 'include',
        'class_filter_array', array( 'service' ),
        'attribute_filter', array( array( 'service/review_date', '<', currentdate() ) ),
        'sort_by', array( 'attribute', true(), 'service/review_date' ),
        'main_node_only', true() ) )}
<table>
  <thead><tr><th scope="col">Service</th><th scope="col">Review was due</th><th scope="col">Section</th></tr></thead>
  <tbody>
  {foreach $overdue as $s}
    <tr><td><a href={$s.url_alias|ezurl}>{$s.name|wash}</a></td>
        <td>{$s.data_map.review_date.content.timestamp|l10n( 'shortdate' )}</td>
        <td>{$s.object.section_id}</td></tr>
  {/foreach}
  </tbody>
</table>
{undef $overdue}
```

Do not cache this page for long. The sub-items table of the admin can also show columns and export them as CSV,
which is enough for a one-off check ([Content model and editing, 5](../../../../guides/content-model-and-editing.md#5-the-sub-items-table)).

## Recording what changed

| Need | Exponential |
|---|---|
| What the page said before | **Manage versions**, **Compare** ([Versions of an object](../../../../guides/content-history.md)) |
| Who changed it | versions and the audit trail ([Audit trail](../../../../features/6.0/audit-trail.md)) |
| Telling people who follow the service | notifications on publish; **Publish without notification** for minor corrections where enabled ([Publish without notification](../../../../features/6.0/publish-without-notification.md)) |

## A monthly routine

- [ ] Open the overdue list; send each owner their services.
- [ ] Check **Setup > Link management** for broken links on service pages.
- [ ] Check **Setup > Search statistics** for phrases without results (if enabled).
- [ ] Read new feedback in **Setup > Collected information**; act, then remove what is no longer needed.
- [ ] Look at **My pending items** for approvals that wait too long.

## Related pages

- [Building a service catalog](README.md)
- [Measuring and improving](measuring-and-improving.md)
- [Maintaining your website](../../../creators/website-creators/maintaining-your-website.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
