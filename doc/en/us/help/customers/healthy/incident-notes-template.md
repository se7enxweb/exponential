---
title: Incident notes template
audience: customer, businesses, project-manager
category: customer-success
order: 22
summary: A template for the owner's notes during and after an incident on an Exponential site - a timeline, what visitors saw, who was called, decisions, communication, cause and actions - plus the review questions and how the site's own records help fill it in.
keywords: incident, template, notes, timeline, post-incident review, outage report, actions, evidence
type: help
---
# Incident notes template

**Template.** Copy it, fill it in during the incident and complete it afterwards. Notes taken in the moment are what
make the review useful and give you evidence for your provider, your management or an auditor. Replace every line in
angle brackets with your own facts.

## Why write it down

The plan for small teams asks the lead to "keep the time" and, with one person, to "write down the time of each"
step ([Incident response for small teams](../../businesses/reliability/incident-response-for-small-teams.md)). Your
notes also let you check your provider's account against what you saw, and against the agreed response times.

## The template

```text
INCIDENT NOTES                                    Reference: <your own number or date>

1. Summary (fill in afterwards)
   What happened:          <one or two sentences>
   Severity:               <1 critical / 2 high / 3 normal / 4 low>
   Started / noticed / resolved:  <time> / <time> / <time>   (time zone: <zone>)
   Visitors affected:      <all / some pages / editors only / one function>

2. What visitors saw
   Page text and status:   <"We'll be right back", "Something went wrong", blank, browser error ...>
   Error reference(s):     <ERR-... or none>
   Pages checked:          <addresses>

3. Timeline (one line per event, as it happens)
   <time>  <what happened or was done>                      <who>
   <time>  Noticed by <customer / monitor / staff>
   <time>  Called <host / partner>, ticket <number>
   <time>  First answer from <provider>
   <time>  Decision: <fix / roll back / maintenance page / restore>
   <time>  Customers told via <channel>
   <time>  Site back for everyone, checked by <name>

4. Recent changes before it started
   <upgrade, deploy, settings change, import, none known>

5. Communication
   Messages sent:          <time, channel, text>
   Next update promised:   <time>      Kept: <yes / no>

6. Cause and fix (from the provider's account)
   Cause:                  <cause>
   Fix:                    <fix>
   Data lost:              <none / what, for which period>

7. Response against the agreement
   Agreed first response:  <from the contract>     Actual: <time>
   Agreed restore time:    <from the contract>     Actual: <time>

8. Review (within a week, without blame)
   How did we find out, could we have found out sooner?   <answer>
   What slowed us down?                                   <answer>
   What would have prevented it?                          <answer>

9. Actions
   | Action | Owner | Due | Done |
```

## Where the facts come from

| Fact | Source |
|---|---|
| Exact times of deploys, settings changes, maintenance on and off | The audit trail's `system` channel; maintenance switches are recorded as `system.maintenance.change` ([Maintenance mode](../../../guides/maintenance-mode.md), [Audit trail for operations](../../businesses/reliability/audit-trail-for-operations.md)) |
| The technical error behind a reference | **Setup > Log files**, field **Error reference** ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)) |
| Who signed in or changed content | The audit trail ([The audit trail](../../../features/6.0/audit-trail.md)) |
| Uptime and alert times | Your external monitor ([Monitoring](monitoring.md)) |
| Response times | Your provider's ticket system |

Ask your administrator or host for these facts after the incident; you do not need to read the logs yourself.

> **Warning**
> Do not copy passwords, keys or customers' personal data into the notes, even from logs. Keep the notes with your
> other records, readable only by the people who need them.

## After the review

Put the actions into your regular routine: the [reliability scorecard](../../businesses/reliability/reliability-scorecard.md)
and the [risk register](../../leadership/risk-management.md). If the incident cost you money, the
[cost of downtime worksheet](../../businesses/costs/cost-of-downtime-worksheet.md) helps you put a figure on it for the
next contract discussion.

## Related pages

- [When the site is down](when-the-site-is-down.md)
- [Reporting problems](reporting-problems.md)
- [Reviewing your provider each year](../partners-and-hosts/reviewing-your-provider-each-year.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
