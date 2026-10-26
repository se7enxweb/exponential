# Approval of new user accounts: administrators approve new editors

Editors manage the site's members ([member-management.md](member-management.md)). Since 6.0.15 they may also create
the account of a new **editor**, but that account is not published until an **administrator** approves it. Until then
it cannot sign in. The approval runs through the workflow system and the collaboration system, as the approval of
content (the workflow event *Approve*, `ezapprove`) always has; the approvers also get an e-mail with the link to the
approval.

Which user groups need approval, who approves and who needs no approval are the settings of the workflow event
**Approve user accounts** (`event_expuserapprove`), changed at Setup > Workflows like any other event. A new
installation is set up with the defaults; an installation that exists gets them from `bin/php/adduserapproval.php`.

## What happens

1. An editor creates a user in **Editors** (Users > Editors > Create here > User account) and presses *Send for
   publishing*. The workflow on content/publish (before) runs the event *Approve user accounts*.
2. The event sees a user account whose new version places it in a group that requires approval, published by someone
   who needs approval. It creates an approval item (the collaboration handler `ezapprove`, the same item the content
   approval uses), in the collaboration lists of the approvers and of the editor, and holds the publication: the
   version is *pending*, the object is not published, and the account **cannot sign in** (signing in asks for a
   published account).
3. Each approver with an e-mail address gets the mail *New user account waiting for approval: <name>*: who asked, the
   account's name and username, the group, and one link: `<administration>/collaboration/item/full/<id>`. The link
   carries no key and no token. Opening it asks the approver to sign in; it approves or denies nothing by itself.
4. The approver opens the item (from the mail, or Dashboard > Collaboration), reads the account's preview, optionally
   adds a comment, and presses **Approve** or **Deny** (each asks for a confirmation). Then "Verify it's you" asks
   (the users group, `collaboration/action other CollaborationAction_Accept CollaborationAction_Deny
   CollaborationAction_Defer`): the decision is held until the approver confirms with the password, a passkey or a
   code, and is then carried out as pressed. Content approvals are not asked
   ([Passkeys and "Verify it's you"](passkeys-and-verify-its-you.md)).
5. **Approve**: the workflow continues at once (not at the next run of the workflow cronjob): the account is published
   below Editors and can sign in. **Deny**: the version is a draft of the editor again, nothing is published, the
   account cannot sign in. The editor can change the draft and send it again, which asks for approval again.
6. The request, the approval, the denial and every refused attempt to bypass it are recorded in the audit log.

An administrator who creates an editor account publishes it directly: approvers never need approval.

## What a new installation gets

The setup wizard and the kickstarter call `expUserApproval::ensure()` after the member management step
(`kernel/setup/steps/ezstep_create_sites.php`), for every site package. It creates, when they are missing:

| What | Default |
| --- | --- |
| Workflow | **Approve new editor accounts**, in the workflow group *Standard* |
| Event | **Approve user accounts**: groups that need approval *Editors*; groups who approve *Administrator users*; groups without approval none; mail to the approvers on |
| Trigger | content / publish / before runs the workflow |
| Role | **Editor account requests**, assigned to *Editors* without a limitation: `content/read` with `Node` = the Editors group's node (to open the group), `content/create` with `Class` = user, `ParentClass` = user_group, `Node` = the Editors group's node (to create an account directly in it) |

The role gives nothing else below Editors: the accounts of other editors stay out of an editor's reach (the member
management guard keeps them so), and an editor sees no other editor in the list of the group.

## The event's settings

At Setup > Workflows > *Approve new editor accounts* > Edit, the event shows:

| Setting | Field | Meaning |
| --- | --- | --- |
| User groups whose new accounts need approval | `data_text1` | an account published into one of these groups, or below one, waits |
| Users who approve the accounts | `data_text3` | these users approve (and need no approval themselves) |
| Groups who approve the accounts | `data_text4` | every user below these groups approves |
| User groups that need no approval | `data_text2` | their members publish such accounts directly |
| Send the approvers an e-mail | `data_int1` (0 = yes, 1 = no) | the mail with the link to the item |

At least one approving user or group and one group that needs approval are required; the form refuses to store the
event without them.

**Which placements need approval.** Those of the published version that the account does not have yet and that lie in
a required group: a new account in Editors, an existing account that gets a location in Editors in the edit form, a
copy of an account into Editors. Editing an account that is already in Editors (name, password) needs none. User groups
count like accounts: a group moved or copied into Editors brings its users along.

**Who needs none.** The approving users, every member of an approving group, and the members of the groups without
approval. Everyone else waits, also an anonymous registration whose placement lies in a required group.

## Move and add location

Moving an account (or a user group) into a group that requires approval, or adding a location there, does not publish
anything, so it cannot wait for an approval. It is refused for everyone who would wait for the approval when publishing
the same placement (`expUserApproval::placementAllowed()`, asked by content/move, `eZContentObjectTreeNodeOperations::move()`
and `eZContentOperationCollection::addAssignment()`). The refusal is recorded as `access.user.approval.refused`. An
editor who wants a member to become an editor asks an administrator, or creates a new account and waits for the
approval.

## The mail

`design:workflow/eventtype/expuserapprove_mail.tpl` (HTML with a plain-text part, the look of `design:mail/layout.tpl`),
mail category `security` (essential: the mail gate never holds it back), one mail per approver. The address of the
administration is the one of the "New user registered" mail: `site.ini [UserSettings] RegistrationAdminBaseURL`, else
https:// and the SiteURL of `RegistrationAdminSiteAccess`, else that siteaccess's address. Set
`RegistrationAdminBaseURL` when the administration has a host of its own.

The link only opens the approval item. Approve and deny are the buttons of the item in the administration: there is no
one-click link, no key and no token in the mail.

## The audit log

| Event | When | Actor | Object, target, after |
| --- | --- | --- | --- |
| `access.user.approval.request` | the event holds an account for approval | who published | the account; the approval item; version, groups, number of approvers, mails sent, workflow, process |
| `access.user.approval.approve` | an approver approves | the approver | the account; the approval item; version, process, who asked |
| `access.user.approval.deny` | an approver denies | the approver | the account; the approval item; version, process, who asked |
| `access.user.approval.refused` | a move or new location into a required group is refused | who tried | the account or group; the target node; workflow, event |

All four are on by default, in the `access` channel. `access.user.create` is recorded when the account's draft is
stored, `access.user.change` and the others of [member-management.md](member-management.md) as before.

## What to do on an installation that exists

```bash
php bin/php/adduserapproval.php --dry-run        # what it would create and add
php bin/php/adduserapproval.php                  # do it; checks the result, prints PASS or FAIL
```

The command creates the workflow, its event and the trigger when they are missing. When content/publish (before)
already runs another workflow (only one workflow runs per trigger), the event goes **first** into that workflow, so an
account that waits for approval does not start the workflow's other events. It creates the role *Editor account
requests* and assigns it to the requesting groups. A second run changes nothing. Options:

| Option | Default | |
| --- | --- | --- |
| `--required=<group>` | Editors | a group whose new accounts need approval, repeatable |
| `--approvers=<group>` | Administrator users | an approving group, repeatable |
| `--requesters=<group>` | Editors | a group that gets the role, repeatable |
| `--no-role` | | no role: the requesters have their policies already |
| `--admin-id=<id>` | 14 | the creator of the workflow |

Groups are named `remote:<remote id>`, `node:<node id>` or `name:<name of a top-level group>`, as in
`membermanagement.ini`. An event that is there already gets the required and approving groups added, never removed.
After the kernel files are in place: `--clear-tag=ini` (workflow.ini lists the new event type) and on Velocity
`exp:velocity deploy --kernel`.

Other groups: run it with other options (for example `--required=name:Partners --approvers=name:Partner\ desk
--requesters=name:Partner\ desk`), or add a group to the event at Setup > Workflows. Without approval: remove the event
from the workflow, or remove the role from Editors (they can then no longer create accounts in Editors at all).

## Details

- The item is an ordinary item of the collaboration handler `ezapprove`; the inbox counts, the notification rules for
  collaboration items and the item page work as for content approvals. The table `ezapprove_items` links the item to
  the workflow process.
- After *Approve* or *Deny*, `expUserApproval::decided()` runs the waiting process once, as `cronjobs/workflow.php`
  does. If that fails, the process stays and the workflow cronjob finishes it.
- Nobody to approve (an empty approving group): the publication is refused (the version is a draft again) and the
  error log names the event, so an account never waits for ever.
- Removing a user or group removes it from every such event's lists.

Code: `kernel/classes/workflowtypes/event/expuserapprove/expuserapprovetype.php` (the event),
`kernel/classes/user/expuserapproval.php` (settings, guard, mail, decision, setup), the hook in
`kernel/classes/collaborationhandlers/ezapprove/ezapprovecollaborationhandler.php`, the command
`kernel/private/classes/commands/adduserapproval.php`, templates
`design/standard/templates/workflow/eventtype/{edit,view}/event_expuserapprove.tpl` and
`design/standard/templates/workflow/eventtype/expuserapprove_mail.tpl`. Test without a database:
`tests/tests/kernel/classes/expUserApprovalTest.php`. Guide: [Approving new editor accounts](../../guides/approving-new-editor-accounts.md).
