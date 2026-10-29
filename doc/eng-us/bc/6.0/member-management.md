# Member management: editors manage the site's members

Editors manage the accounts of the site's members: they create them, edit them (name, e-mail address, password),
enable and disable them, move them between member groups and remove them. They never reach the accounts of
administrators or of other editors, and they never assign roles or change roles, sections or setup. Every one of
these actions is recorded in the audit log, and so is every refusal.

The rules live in `settings/membermanagement.ini`, so a site can say which groups manage which other groups, which
groups nobody manages through such a rule, and which policies a manager gets. The defaults give the **Editors** group
the role **Member management** over **Members**, and protect **Administrator users** and **Editors**.

## What a new installation gets

The setup wizard and the kickstarter apply the rules after the site package's roles
(`expMemberManagement::ensure()` in `kernel/setup/steps/ezstep_create_sites.php`), for every site package and base
data. The multisite installers (`sevenx_multisite`, `sevenx_multisite_clean`, `cjw_multisite`) call the same step
from their own role step. The result with the defaults:

| Policy of the role "Member management" | Limitation | Lets an editor |
| --- | --- | --- |
| content / read | Subtree Members | see the members and their groups |
| content / read | Node: the user root (content.ini `[NodeSettings] UserRootNode`) | open the **Users** tab; the list below it shows only the groups the editor may read |
| content / create | Class user, user_group; ParentClass user_group; Subtree Members | create a member, or a group of members, in a member group |
| content / edit | Class user, user_group; Subtree Members | edit a member: name, e-mail address, login, password |
| content / remove | Class user, user_group; Subtree Members | remove a member |
| content / manage_locations | Class user, user_group; Subtree Members | move a member to another member group |
| content / versionread, versionremove, diff | Subtree Members | the versions of a member's account |
| user / preferences | – (see the guard) | enable and disable a member, the number of concurrent logins (user/setting) |

The role is assigned to the Editors group **without** a limitation: each content policy carries the managed subtree
itself, which is what lets the role also read the user root on its own (an assignment limited to Members would cut
that policy away). The Editor role keeps its own assignment (Content and Media).

## The guard

A Subtree limitation is met when **any** location of an object lies in the subtree. An administrator who also had a
location below Members would therefore be in reach of a member manager. The guard closes this:
`eZContentObject::checkAccess()` and `eZContentObjectTreeNode::checkAccess()` ask `expMemberManagement::guardAllows()`
after they allowed one of the guarded functions (`edit`, `move`, `remove`, `manage_locations`, `hide`, `translate`,
`versionremove`) on an account (an object of a class with a user account attribute, or of a class in
`GuardedClasses[]`, default `user_group`). Every location of that account below a protected group must allow the
same function on its own; if one does not, the function is refused. Administrators are not affected: their policies
allow it everywhere.

The managed group itself (Members) is kept from its managers too: a Subtree policy on `/1/5/12/` includes node 12,
so a manager could otherwise rename, move or remove the whole group with every member in it. The guard asks for the
same function at the group's parent (the user root) for the managed groups' own nodes, which a manager does not have.
Groups the managers create below Members are theirs to edit, move and remove.

`user/setting` (enable, disable, concurrent logins) is opened by `user/preferences`, a function without limitations.
With `[MemberGuardSettings] SettingRequiresEdit=enabled` (the default) it also asks for `content/edit` of the account,
so `user/preferences` alone no longer reaches every account of the installation. A site that gave `user/preferences`
to a role without `content/edit` of users loses that path; give the role `content/edit` limited to the accounts it
should reach, or set `SettingRequiresEdit=disabled`.

Moving a member into a protected group, or adding a location there, needs `content/create` at the target, which the
role gives only below Members: refused. Creating a user in a protected group: refused the same way, except in Editors,
where the role *Editor account requests* lets editors create an account that then waits for an administrator's
approval; moving a member into Editors or adding a location there stays refused for them
([user-account-approval.md](user-account-approval.md)).

## Settings: `membermanagement.ini`

Groups are named `remote:<remote id>` (the group object's or its node's remote id: the stock groups keep theirs on
every installation), `node:<node id>` or `name:<name>` (a top-level group below the user root).

```ini
[MemberManagementSettings]
MemberManagement=enabled            # disabled: the setup step and the command change nothing
Rules[]=editors_manage_members      # a [MemberRule_<name>] block each
ProtectedGroups[]=remote:9b47a45624b023b1a76c73b74d704acf   # Administrator users
ProtectedGroups[]=remote:3c160cca19fb135f83bd02d911f04db2   # Editors

[MemberGuardSettings]
Guard=enabled
GuardedClasses[]=user_group
GuardedFunctions[]=edit             # and move, remove, manage_locations, hide, translate, versionremove
SettingRequiresEdit=enabled

[MemberRule_editors_manage_members]
RoleName=Member management
ManagerGroups[]=remote:3c160cca19fb135f83bd02d911f04db2     # Editors
ManagedGroups[]=remote:5f7f0bdb3381d6a461d8c29ff53d908f     # Members
PolicySet=member_management
EntryNodes[]=users                  # read on that node alone; "users" = the user root

[MemberPolicySet_member_management]
Policies[]=content/read
Policies[]=content/create;Class=user,user_group;ParentClass=user_group
Policies[]=content/edit;Class=user,user_group
...
Policies[]=user/preferences
```

Variations go in `settings/override/membermanagement.ini.append.php` or an extension's settings:

- **Another manager group**, e.g. a "Support" group that manages the members too: add a rule with its own
  `ManagerGroups[]` (or add the group to the existing rule).
- **Partners managed by a partner desk**: a rule `ManagedGroups[]=name:Partners`, `ManagerGroups[]=name:Partner desk`.
- **Read-only member desk**: a policy set with `content/read` and `content/versionread` only.
- **No member management**: `MemberManagement=disabled` before the installation, or remove the role afterwards.

A rule is refused as a whole (the setup step skips it and names the reason in the setup log, the command prints
`FAIL`) when a managed group is, contains or lies below a protected group, when a manager group lies in the group it
would manage, when a group cannot be found, when a policy line names `module/*`, a module of the administration
(`role`, `setup`, `section`, `class`, `state`, `workflow`), an unknown class, or its own `Subtree`/`Node` limitation.
Policies are never widened: a line whose limitation cannot be resolved is left out whole.

## The audit log

The kernel's audit log ([audit.md](audit.md)) records every action of member management, from every interface (the
admin designs, the editor siteaccess, REST, commands), on Apache and on Velocity alike, in the `access` channel. Each
record names who (user id and login, siteaccess, host, address under the privacy rules, user agent), when, the
account (id and login) and what changed:

| Event | When | Before → after |
| --- | --- | --- |
| `access.user.create` | an account is created | – → login, e-mail (privacy rules), groups |
| `access.user.change` | a new version of an account is published | the changed fields as text → the same fields (never the account attribute) |
| `access.user.email.change` | the e-mail address changes | e-mail → e-mail (hashed by default) |
| `access.user.login.change` | the login changes | login → login |
| `access.user.password.change` | the password is set or changed | – (never the password or its hash) |
| `access.user.enable` / `access.user.disable` | user/setting | enabled, max_login → the same |
| `access.user.move` | an account moves to another group | group → group |
| `content.node.remove` (on), `content.node.add` (off by default: `Enabled[]=content.node.add`) | a location is removed or added | parent → parent |
| `access.user.remove` | an account is removed | login, e-mail → – |
| `content.object.restore` | an account comes back from the trash | – |
| `access.role.assign` / `access.role.unassign` | a role is assigned or unassigned | limitation |
| `access.permission.refused` | a view is refused: editing an administrator, user/setting of an editor, role/assign | – → the policy asked, limitation; target the account (id, login) when the view names one |

The log is files with a hash chain (append-only; nobody edits a record through the interface) and an index in the
site's database (`expaudit_event` and its side tables, in `share/db_schema.dba` for every engine;
`update/common/scripts/6.0/createaudittables.php` for an installation that predates them). Administrators read it at
the **Audit** tab (`audit/dashboard`, `audit/console`, policy `audit/read`, limitable by channel), filter by event, user, object, time and
text, and export a filter as CSV, JSON lines or JSON (`audit/export`). What a record may hold is
`audit.ini [AuditPrivacySettings]`: `Field[]` per field `full`, `truncate`, `hash` or `off` (`actor.ip=truncate`
keeps /24 and /48), `NeverRecord[]` drops keys everywhere (add a field identifier of the user class to keep it out of
`access.user.change`), `PseudonymiseAfterDays`. Retention is per channel (`[AuditChannel_access] LiveDays`,
`ArchiveDays`). Which events are recorded is `[AuditEventSettings] Enabled[]`/`Disabled[]` (patterns); extensions
add their own names and sinks (`[AuditSinkSettings] SinkClasses[]`: syslog, webhook, mail, or a class implementing
`expAuditSink`) without a kernel change.

The events are generic: per user, typed by name, with the actor, the target and before/after. Session tracking,
workflow and collaboration events go into the same log as further names, not into another table.

Per account, the same events are also kept in the user audit log, the table `expuser_audit`: **Users > User audit
log** (`user/audit`, policy `user/audit`) shows what happened to one member and what one editor did, with the fields
before and after, refused attempts included, and exports it as CSV. See
[User audit log](../../features/6.0/user-audit.md).

## Accounts in protected groups

The protected groups stay outside every rule: moving a user into Administrator users or Editors and editing their
accounts remain administrators' actions. Member management adds no pending or approval state of its own.

Creating a new **editor** is the one exception, and it goes through an approval: the role *Editor account requests*
lets editors create an account directly in Editors, and the workflow event *Approve user accounts* holds it until an
administrator approves it. See [Approval of new user accounts](user-account-approval.md).

## What to do on an installation that exists

```bash
php bin/php/addmembermanagement.php               # what it would create, add and assign; changes nothing
php bin/php/addmembermanagement.php --apply       # do it; checks the result, prints PASS or FAIL
php bin/php/addmembermanagement.php --apply --rule=editors_manage_members
```

Without `--apply` the script changes nothing (since 28 October 2026; before, it wrote by default and took `--dry-run`,
which is still accepted and means the same as no option). The dry run ends with
`PASS: 0 change(s) would be made (dry run, nothing changed; --apply writes)` when the installation is complete.
`--apply` together with `--dry-run` is refused (exit 2).

With `--apply` the script creates the role when it is missing, adds the planned policies the role does not have in
exactly that form (a second run adds nothing), assigns the role to the manager groups and expires the role and user caches. It never
removes a policy. After the kernel files are in place, clear the INI cache (`--clear-tag=ini`) so that
`membermanagement.ini` is read, and on Velocity restart the workers (`exp:velocity deploy --kernel`): the guard is in
the kernel's access check.

Code: `kernel/classes/expmembermanagement.php`, command `kernel/private/classes/commands/addmembermanagement.php`,
guard in `kernel/classes/ezcontentobject.php` and `kernel/classes/ezcontentobjecttreenode.php`, user/setting in
`kernel/private/classes/views/user/setting.php`, `access.user.change` in
`kernel/content/ezcontentoperationcollection.php`, `access.user.move` in `ezcontentobjecttreenode.php`. Test without a
database: `tests/tests/kernel/classes/expMemberManagementTest.php`.
