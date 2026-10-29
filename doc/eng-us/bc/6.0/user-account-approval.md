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
   code, and is then carried out as pressed. Content approvals are not asked; the approval of an account by the
   section based setup is ([Passkeys and "Verify it's you"](passkeys-and-verify-its-you.md)).
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

`site.ini [UserSettings] AccountApprovalMode` chooses what the setup step creates: `group` (the default, the event
above), `section` (the alternative below) or `none`.

## Two ways: by group (the default) or by section

The approval of new staff accounts can be set up in two ways. **Choose one.** Both on the same trigger would ask the
approvers twice for one account, so the command refuses the second way while the first is there, and warns the other
way round.

| | By group (default) | By section (optional) |
| --- | --- | --- |
| Event | *Approve user accounts* (`event_expuserapprove`) | the plain *Approve* event (`event_ezapprove`) the kernel has always had |
| What decides | the group the account is placed in | the section of the account: a section **Staff** (identifier `staff`) for *Administrator users* and *Editors* |
| Moving a member into Editors, adding a location there | refused for whoever would wait | not covered: a move publishes nothing (keep `content/move` and the member management guard as they are) |
| Requesters' role | created (*Editor account requests*) | not created: give the requesters `content/create` in Editors yourself, or keep the role of the group based setup |
| Settings | groups that need approval, approvers, groups without approval, mail on or off | sections, approving users and groups, groups without approval, languages, *new objects only* |
| Mail with the link to the item in the administration | yes | yes (for an account or user group) |
| "Verify it's you", decision carried out at once, audit log | yes | yes (for an account or user group) |
| Set up by | the setup step, `bin/php/adduserapproval.php --apply` | `bin/php/adduserapproval.php --mode=section --apply`, or the setup step with `AccountApprovalMode=section` |

**Choose by group** when the placement must decide (accounts can be moved or get a second location), when groups
other than staff need approval, or when the editors should get exactly the policy to create accounts in Editors. It is
what a new installation gets.

**Choose by section** when you prefer only the stock approval event, when sections already drive your policies
(for example a *Staff* section that anonymous users and members cannot read), or when you want the approval event's
own options, such as languages. Note that the accounts of Administrator users and Editors then leave the section
*Users*: policies limited to that section no longer cover them. The command lists such policies before it changes
anything.

### The section based setup

```bash
php bin/php/adduserapproval.php --mode=section            # dry run: what it would do
php bin/php/adduserapproval.php --mode=section --apply    # do it; checks the result, prints PASS or FAIL
```

It creates, when they are missing: the section **Staff** (navigation part *Users*), assigns it at the *Administrator
users* and *Editors* nodes to every object below them that had the group's section (a new account placed there later
gets it from its group), and the plain *Approve* event, first in the workflow that content/publish (before) runs (or in
a new workflow **Approve new staff accounts** with that trigger):

| Approval event setting | Value |
| --- | --- |
| Sections | Staff |
| Groups who approve | Administrator users |
| User groups without approval | Administrator users |
| Version | new objects only (the first version: editing an existing account asks nothing) |
| Languages | all |

| Option | Default | |
| --- | --- | --- |
| `--apply` | | make the changes; without it the section setup only lists them |
| `--staff=<group>` | Administrator users, Editors | a group whose accounts go into the section Staff, repeatable |
| `--approvers=<group>` | Administrator users | an approving group, repeatable |
| `--exempt=<group>` | Administrator users | a group whose members need no approval, repeatable |
| `--admin-id=<id>` | 14 | the creator of the workflow |

It refuses (FAIL, nothing changed) while the event *Approve user accounts* runs on content/publish (before). To switch
from the group based approval: remove that event from its workflow at Setup > Workflows, then run the command. To
switch back: remove the approval event of section Staff from the workflow and run `bin/php/adduserapproval.php --apply`. The
section can stay; it changes nothing by itself.

What the plain approval event adds for a user account or user group (`expUserApproval::sectionRequested()`,
`decided()`, `modeOfItem()`): every approver gets the same mail as with the group based event, whether or not they
subscribed to collaboration notifications; Approve and Deny ask "Verify it's you", are carried out at once and are
recorded as `access.user.approval.approve` and `.deny` with `mode: section` in the audit log and the user audit log.

### More examples with the same mechanism

Both ways are ordinary workflow events, so other groups and situations are set up at Setup > Workflows:

- **Self-registered members approved by moderators.** Group based: add *Members* to the groups that need approval of
  the event *Approve user accounts* and a *Moderators* group to the groups who approve. Section based: give *Members*
  a section of its own (for example *Community*), add it to the sections of an approval event and *Moderators* to its
  approving groups. The anonymous registration then waits for a moderator.
- **Two approvers for a new administrator.** Add a second event after the first one in the same workflow, with other
  approvers (for example a *Security* group); the account is published only when both approved. With the section
  based setup, give *Administrator users* a section of their own (for example *Admins*) and a second approval event
  for it.

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

The collaboration notifications that people subscribe to (My account > Notification settings > Collaboration) use the
same address since 6.0.15: the templates `notification/handler/ezcollaboration/view/plain.tpl`,
`ezapprove/approve.tpl` and `ezapprove/author.tpl` link to `{$collaboration_item.admin_url}`
(`eZCollaborationItem::adminURL()`), not to the SiteURL of the siteaccess that published the content or ran the
notification cronjob, where the item cannot be opened. An override of these templates that builds the link itself
keeps working as before.

## The audit log

| Event | When | Actor | Object, target, after |
| --- | --- | --- | --- |
| `access.user.approval.request` | the event holds an account for approval | who published | the account; the approval item; version, groups, number of approvers, mails sent, workflow, process |
| `access.user.approval.approve` | an approver approves | the approver | the account; the approval item; version, process, who asked, mode (`group` or `section`) |
| `access.user.approval.deny` | an approver denies | the approver | the account; the approval item; version, process, who asked, mode (`group` or `section`) |
| `access.user.approval.refused` | a move or new location into a required group is refused | who tried | the account or group; the target node; workflow, event |

All four are on by default, in the `access` channel, for both ways (the section based request carries `mode:
section`). As `access.user.*` events about an account they are also kept in the user audit log (Users > the account >
Audit, [user-audit.md](../../features/6.0/user-audit.md)). `access.user.create` is recorded when the account's draft is
stored, `access.user.change` and the others of [member-management.md](member-management.md) as before.

## What to do on an installation that exists

```bash
php bin/php/adduserapproval.php                  # dry run: what it would create and add
php bin/php/adduserapproval.php --apply          # do it; checks the result, prints PASS or FAIL
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
`kernel/classes/user/expuserapproval.php` (settings, guard, mail, decision, setup),
`kernel/classes/user/expuserapprovalsection.php` (the section based setup), the hooks in
`kernel/classes/workflowtypes/event/ezapprove/ezapprovetype.php` and
`kernel/classes/collaborationhandlers/ezapprove/ezapprovecollaborationhandler.php`, the command
`kernel/private/classes/commands/adduserapproval.php`, templates
`design/standard/templates/workflow/eventtype/{edit,view}/event_expuserapprove.tpl` and
`design/standard/templates/workflow/eventtype/expuserapprove_mail.tpl`. Test without a database:
`tests/tests/kernel/classes/expUserApprovalTest.php`. Guide: [Approving new editor accounts](../../guides/approving-new-editor-accounts.md).
