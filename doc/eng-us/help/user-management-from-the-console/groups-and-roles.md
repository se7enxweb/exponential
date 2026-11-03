---
title: "Groups and roles"
audience: admin, developer, hosting
category: administration
order: 6
summary: exp:user:groups lists, creates, renames, moves and deletes user groups; exp:user:roles lists, assigns and unassigns roles of a user or group, optionally limited to a subtree or section; exp:user:whoami says which account the console acts as.
keywords: exp:user:groups, exp:user:roles, exp:user:whoami, user groups, create user group, rename group, move group, delete group, role assignment, assign role, unassign role, subtree limitation, section limitation, --as, UserCreatorID, who am I, administrator
type: help
---
# Groups and roles

A group and a role are named by node id or name, and by name or id. When two groups have the same name, use the node
id.

## What you'll have at the end

A user group tree that fits the site, the right roles on the right accounts and groups, and certainty about which
account the console acts as.

## User groups

```bash
./console exp:user:groups
./console exp:user:groups list Editors
./console exp:user:groups create --name="Regional editors" --parent=Editors --apply
./console exp:user:groups rename "Regional editors" --name="Local editors" --apply
./console exp:user:groups move "Local editors" --to=Members --apply
./console exp:user:groups delete "Local editors" --apply
```

| Action | What it does |
|---|---|
| `list` (default) | the user group tree below a group (default the top of the Users tree): node id, name, direct members, subgroups |
| `create` | a new group named `--name`, below `--parent` (default the top of the Users tree) |
| `rename <group>` | gives the group the name `--name` |
| `move <group>` | moves the group below the group `--to` |
| `delete <group>` | removes the group, as the administration removes it |

`delete` refuses the top of the Users tree, and a group that still has members or subgroups. Move them first with
`exp:user:edit --add-group --remove-group` or `exp:user:groups move`.

## Roles

```bash
./console exp:user:roles list jane
./console exp:user:roles list group:Editors
./console exp:user:roles assign Editor group:"Local editors" --subtree=2345 --apply
./console exp:user:roles assign Anonymous jane --section=standard --apply
./console exp:user:roles unassign Editor group:"Local editors" --subtree=2345 --apply
```

The target is a user (login, e-mail address or id) or `group:<node id or name>`.

| Action | What it does |
|---|---|
| `list <target>` | the role assignments of the user or group, each with its limitation and where it comes from (the account or a group above it) |
| `assign <role> <target>` | assigns the role; `--subtree=<node id>` or `--section=<section id or identifier>` limits it |
| `unassign <role> <target>` | removes the role; with `--subtree` or `--section` only that limited assignment |

The audit records `access.role.assign` and `access.role.unassign`. The role caches are cleared as in the
administration, so the change applies at the next request.

**Note:** A role assigned to a group applies to every member at any depth. Prefer groups over single accounts: a
person who changes jobs then only changes group.

## Who the console acts as

```bash
./console exp:user:whoami
./console exp:user:whoami --as=jane
```

The command names the account the console acts as: the user of `[UserSettings] UserCreatorID`, or the one of `--as`.
It lists its roles and says whether it has every right.

The commands do not check the policies of that account. Whoever can run the console on the server can manage every
account, so keep shell access to the people who administer the site. `--as` decides whose name the audit records.

## Related pages

- [Roles and policies](../../guides/roles-and-policies.md): what a role and a limitation are.
- [Onboard a new editor](onboard-a-new-editor.md): a group and a role for a new person.
- [Find, create and change accounts](find-create-and-change-accounts.md#change-an-account): add and remove group
  memberships of an account.
