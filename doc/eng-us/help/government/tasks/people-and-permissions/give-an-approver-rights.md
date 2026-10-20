---
title: Give an approver rights
audience: government, admin
category: public-sector
order: 30
summary: Step by step - make a colleague an approver of content before publication by adding them to the approvers' group named in the Approve event and making sure the group's role opens the collaboration inbox and pending versions.
keywords: approver, give approval rights, approvers group, Approve event, collaboration, versionread, sign-off, reviewer, workflow
type: help
---
# Give an approver rights

This task is for administrators. At the end, a colleague receives approval requests for one area and can approve or
deny them. Background: [Role design: approvers](../../operations/people-and-permissions/role-design-approvers.md).

## Contents
1. [Before you start](#before-you-start)
2. [If the approvers' group exists](#if-the-approvers-group-exists)
3. [If it does not exist yet](#if-it-does-not-exist-yet)
4. [Check it worked](#check-it-worked)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## Before you start

- An approval workflow is connected to **Before publishing content** on **Setup > Triggers**
  ([Set up an approval workflow for a section](set-up-an-approval-workflow-for-a-section.md)).
- You can edit user accounts (to add a location in a group) and, for a new group, roles and workflows.
- The request to make the person an approver is approved under your rules.

## If the approvers' group exists

1. Open the colleague's user account in the **Users** tree.
2. Add a location in the approvers' group (the **Locations** tab, **Add locations**, choose the group). The account keeps
   its other groups.
3. The group's roles apply at once.

## If it does not exist yet

1. Create a user group, for example "Approvers - Department of Example", under Users.
2. **Users > Roles and policies > New role** "Approver" with these policies:

   | Module | Function | Limitations |
   |---|---|---|
   | collaboration | (**Grant access to all functions**) | none |
   | content | read | the area's section or subtree |
   | content | versionread, diff | the same |
   | content | dashboard, pendinglist | none |

   The collaboration module defines no separate functions, so the policy is over the whole module; without it the
   inbox answers "The requested page could not be displayed".
3. **Save**, then **Assign** the role to the group.
4. **Setup > Workflows**, open the approval workflow, edit its **Approve** event and add the group under
   **Groups who approve content**. **OK**.
5. Add the colleague to the group as above.

## Check it worked

1. As an editor (not an approver), publish a test page in the affected area: it waits.
2. As the colleague, open **My account > Collaboration** (`/collaboration/view/summary`): the request is there.
   **My pending items** lists it under "waiting for your approval".
3. **Deny** the test with a comment; after the next workflow cronjob run, the test version is a draft again.

## Pitfalls

- An approver's own content is let through by the Approve event; if your rules forbid that, see
  [Two-person rule for publishing](../../operations/editorial-workflows/two-person-rule-for-publishing.md).
- Requests made before the colleague joined keep their old participants.
- Without `content/versionread` for the area, waiting versions are not listed for the approver.

## Related pages
- [Collaboration inbox](../../operations/editorial-workflows/collaboration-inbox.md)
- [Setting up the Approve event](../../operations/editorial-workflows/setting-up-the-approve-event.md)
- [Remove a role assignment](remove-a-role-assignment.md)
- [Workflows guide](../../../../guides/workflows.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
