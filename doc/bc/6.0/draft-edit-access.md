# Edit access to objects that were never published (6.0.15)

An object that was never published (a new object whose first version is still a draft, or was rejected by an
approval workflow) has no nodes yet. Several edit checks treated such an object as if it could only be at version 1
and as if it had no location at all. In 6.0.15 they read the location the object will be published under: the parent
of the main node assignment of its current version (`eZContentObject::draftParentNodeIDArray()`,
`draftMainNodeAssignment()`).

This page is for administrators who keep roles and approval workflows, and for extensions that call the access
checks.

## Who may now edit what

| Who | Before | Now |
| --- | --- | --- |
| Someone with **create** access under the planned parent (no edit policy, or none that matches), at **version 2 or later** of the object | denied | may edit |
| Someone with an **edit** policy limited by **Subtree** to the subtree the object will be published in (an approver) | denied, unless owner | may edit |
| Someone whose role with **edit** is **assigned for that subtree** (User_Subtree, "assign with limitation: subtree") | denied, unless owner | may edit |
| The same people for **read**, **remove**, **pdf**, **diff**, **versionread** and the other functions | denied | denied (unchanged) |
| An edit policy or assignment for **another** subtree | denied | denied (unchanged) |
| The owner of the draft | allowed | allowed (unchanged, checked first) |
| Published objects | | unchanged |

The create rule applied before, but only at version 1. Approval workflows edit a rejected first version as version 2
(content/edit makes a new version), so from then on only the owner, or someone with an unlimited edit policy, could
edit it. The rule now applies at every version, and the same rule is used everywhere it was written out:
`eZContentObject::checkAccess()`, `eZContentObjectTreeNode::checkAccess()` (the preview node of content/versionview),
`eZContentObjectVersion::checkAccess()` and content/removeeditversion. It reads the **main** node assignment (it read
the first one), and without a node assignment or parent object it denies instead of ending in "Call to a member function
attribute() on null". The common code is `eZContentObject::draftCreateAccess()`.

Someone else's draft stays closed for reading: a read policy for the subtree does not show drafts in
`content/history`, `ezjscnode::load` or a fetch. Editing a version still needs to be its creator
(`eZContentObjectVersion::checkAccess( 'edit' )` is unchanged); an approver who edits someone else's version gets the
usual choice to edit a copy as a new version.

## How a site notices

- **Approvers with subtree-limited editor roles** get the "Edit" link of a pending item in the collaboration inbox
  (`content/edit/<object>/<version>`) working for objects that were never published. If a site relied on approvers
  *not* being able to edit such drafts, give the approver role no `content/edit` for that subtree (approval needs
  only `content/read` and `content/versionread`).
- **Editors with create access** can open and discard (`content/removeeditversion`) the drafts of new objects at
  version 2 and later, as they could at version 1. A site that used the version number as a guard ("after the first
  rejection, only the owner") loses that guard; there is no setting for it.
- `canEdit()` / `can_edit` on such objects in templates can now be true for these users, so links and buttons that
  depend on it appear. The Edit and Publish actions of `content/versionview` still also require the version's
  creator.
- Nothing changes for objects that have been published once, nor for anonymous users (they have no create access by
  default).

## Checking it

- Without a database: `tests/tests/kernel/classes/eZContentObjectDraftLocationTest.php` (the choice of the main
  assignment, the planned parent, the create rule with stand-in objects).
- On a running installation: `tests/tests/kernel/classes/eZContentObjectDraftAccessLiveTest.php` creates its own users
  (never published, addresses at `example.invalid`), roles and drafts and removes them again. It is skipped where
  there is no installation.
- With a test database: the `@group database` tests in `tests/tests/kernel/classes/eZContentObjectTest.php`.
