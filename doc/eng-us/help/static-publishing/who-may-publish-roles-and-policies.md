---
title: "Who may publish: roles and policies"
audience: admin, website-creator, project-manager
category: administration
order: 20
summary: The four policies of static publishing - staticpublish/read, build, publish and setup - each limited to targets if you like, which steps ask Verify it's you, and how to let an editor publish one target.
keywords: static publishing, policies, roles, permissions, staticpublish/read, staticpublish/build, staticpublish/publish, staticpublish/setup, Target limitation, verify it's you, reauthentication, editor, Administrator, ReauthGroup_system, audit
type: help
---
# Who may publish: roles and policies

Static publishing has four policy functions in the module `staticpublish`. In a new installation only the role
Administrator has them. Each takes the limitation **Target**, so a role can be allowed some targets only.

## What you'll have at the end

A role that lets the right people build and publish the right targets.

## The policies

| Policy | Lets a user | Pages |
|---|---|---|
| `staticpublish/read` | see the targets, their builds, previews, history and jobs; the dashboard block | Static publishing, Preview, History, a job's page |
| `staticpublish/build` | build a target, throw a build away | Build, Discard |
| `staticpublish/publish` | publish a build, roll back to an earlier publish | Publish, Roll back |
| `staticpublish/setup` | make and change targets, their deploy key, switch static publishing on or off | New target, Settings, Deploy key |

Making a new target and the switch need `staticpublish/setup` **without** a Target limitation: a limited role can
only change the targets it names. Every page also needs `staticpublish/read` for its target. A user without it gets
"access denied", and the menu entry and the dashboard block do not show.

## Verify it's you

These steps ask for the password again (site.ini `[ReauthGroup_system]`): **Publish**, **Roll back**, **Save** in the
wizard, **Make the key** and the switch. Seeing, building, the preview and Discard never ask.

## Let an editor publish one target

1. **Roles and policies** > the editors' role > **New policy**.
2. Module `staticpublish`, function `read`, limitation **Target**: the target. Save.
3. The same for `build` and `publish`.

The editor now sees only that target and can build and publish it, but not change its settings.

## The record

Every publish, rollback, saved target and deploy key is in the audit log (`system.staticpublish.*`), with who did
it. Credentials are recorded by name only, never their value.

## Related pages

- [Static publishing](README.md)
- [Build, preview and publish](build-preview-and-publish.md)
- [History and rollback](history-and-rollback.md)

## Next step

[Make your first target with the setup wizard](the-setup-wizard.md).
