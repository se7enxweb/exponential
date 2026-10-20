---
title: Emergency publishing bypass
audience: government, admin, project-manager
category: public-sector
order: 90
summary: How a public body can publish urgent information (an emergency notice, a closure, a public safety message) without waiting for the normal approval, while keeping the bypass small, deliberate and on the record in the audit trail.
keywords: emergency publishing, urgent notice, break glass, bypass approval, excluded user groups, crisis communication, public safety, audit, approval workflow, after-the-fact review
type: help
---
# Emergency publishing bypass

This page is for web managers and communications leads who must be able to publish urgent public information at
once, even when approvers are unavailable, and still meet their governance rules. Exponential has no separate
emergency mode; the bypass is built from the Approve event's **Excluded user groups** field and the audit trail.
Sources: [Workflows](../../../../guides/workflows.md) and [Audit trail](../../../../features/6.0/audit-trail.md).

## Contents
1. [The design: break glass, then review](#the-design-break-glass-then-review)
2. [Setting it up](#setting-it-up)
3. [Using it](#using-it)
4. [Reviewing it afterwards](#reviewing-it-afterwards)
5. [Alternatives](#alternatives)
6. [Related pages](#related-pages)

## The design: break glass, then review

- A user group "Emergency publishers" is listed under **Excluded user groups** in the Approve event: "Users in these
  groups publish without approval" ([Workflows, section 3.2](../../../../guides/workflows.md#32-set-up-the-approve-event)).
- The group is **empty** in normal times, or holds two or three named people on call.
- Putting someone into the group is the "break glass" act. It is an administrator action, and it is recorded.
- Every publication is recorded in the audit trail's `content` channel, so the review afterwards has the facts.

## Setting it up

1. Create the user group "Emergency publishers" under Users. Give it no roles: members keep their own editor roles;
   the group only exempts them from approval.
2. Add it to **Excluded user groups** of every Approve event that guards public content.
3. Make adding a member visible. Adding an account to a group is adding a location; switch on the audit events
   **Location add** and **Main location change**:

   ```ini
   # settings/override/audit.ini.append.php
   [AuditEventSettings]
   Enabled[]=content.node.add
   Enabled[]=content.node.main
   ```

   ([Security and audit, step 3](../../../../guides/security-and-audit.md#3-read-the-audit-trail-5-minutes)).
   Removing a location is recorded by default.
4. Write the procedure: who may authorize the bypass, who adds the member, how long membership lasts, who reviews.

## Using it

1. The authorizing manager asks an administrator to add the editor to "Emergency publishers" (add a location of the
   user in the group). For a standing on-call group, skip this step.
2. The editor publishes the notice; it goes live at once.
3. The administrator removes the extra location again as soon as the notice is out.
4. If caches delay the change, clear them as for any urgent change ([Stale cache](../incidents/stale-cache.md)).

If even the editor is unavailable, an administrator (full access) can publish; approvers' own content and
administrators are subject to the same audit record.

## Reviewing it afterwards

Within the agreed time (for example the next working day):

```bash
./console exp:audit search --name='content.*' --from='<time of the incident>' --limit=50 --allow-root-user
./console exp:audit search --name='content.node.*' --from='<time of the incident>' --allow-root-user
```

Or in the **Audit** tab, **Console**, filtered by channel `content` and the time window. Check: who was added to the
group, what was published, when the membership ended. The normal approver then reads the published notice and
records the after-the-fact approval in your ticket.

## Alternatives

| Alternative | When |
|---|---|
| A standing on-call group in Excluded user groups | frequent urgent publishing (for example a public safety office) |
| Approvers' group large enough to be reachable at any hour | when no bypass is acceptable |
| A pre-approved page that is only unhidden | known scenarios (closures): prepare and approve in advance, keep hidden, unhide when needed (`content/hide`) |
| Maintenance mode with a message | the whole site must show one message ([Maintenance mode](../availability/maintenance-mode-and-planned-downtime.md)) |

## Related pages
- [Two-person rule for publishing](two-person-rule-for-publishing.md)
- [Approval workflows overview](approval-workflows-overview.md)
- [Incident response basics](../incidents/incident-response-basics.md)
- [Audit trail](../../../../features/6.0/audit-trail.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
