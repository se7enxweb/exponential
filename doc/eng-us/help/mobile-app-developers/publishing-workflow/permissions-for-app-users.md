---
title: Permissions for app users
audience: mobile-app-developer, app-creator, developer, admin
category: mobile-apps
order: 720
summary: Set up the user accounts and roles an app runs as - integration users, read roles limited by section or subtree, write roles limited to one folder and class, the policies OAuth and API keys need, checking a user's rights through the APIs, and auditing app activity in the audit trail.
keywords: permissions, roles, policies, integration user, content/read, content/create, content/edit, content/remove, oauth/authorize, apikey/create, limitations, subtree, section, class, expsession::access, audit trail, exp:audit, access.apikey, service events, mobile app
type: help
---
# Permissions for app users

This page is for app developers and the administrators they work with. Every API call runs as a user, and the user's
roles decide everything the app can see and do. Getting the roles right is the most effective security measure an
app has. The general guide is [Roles and policies](../../../guides/roles-and-policies.md).

## Contents

1. [Policies the APIs check](#policies-the-apis-check)
2. [Roles for typical apps](#roles-for-typical-apps)
3. [Checking rights from the app](#checking-rights-from-the-app)
4. [Auditing app activity](#auditing-app-activity)

## Policies the APIs check

| Action | Policy |
|---|---|
| REST read of a node, object, list, count | `content/read` |
| REST create | `content/create` (Class, ParentClass, Section, Node, Subtree, Language limitations) |
| REST update (answers `501` after the check) | `content/edit` |
| REST remove | `content/remove` of every location and everything below |
| Allow an OAuth app on the authorize page | `oauth/authorize` (optionally limited by siteaccess) |
| Make personal API keys | `apikey/create` (optionally limited by `Scope`) |
| A remote service | the policy in its `access` declaration, or `public` / `user` |

Sources: [Permissions of the REST interface](../../../guides/api-keys.md#32-permissions-of-the-rest-interface),
[OAuth authorize policy](../../../bc/6.0/oauth-authorize-policy.md), the service declarations of `expservices`.

## Roles for typical apps

| App | Role | Policies |
|---|---|---|
| Public reader | "App reader" | `content/read` limited to the sections or subtrees the app shows |
| Members' app | the members' role, plus `oauth/authorize` | what members may read on the site |
| Field reporting | "App reporter" | `content/read` of the reports folder; `content/create` with Subtree = that folder and Class = the report class |
| Editors' companion app | the editors' roles | as in the administration, plus `oauth/authorize` |

Rules of thumb:

- One integration user per app, never a person's account and never the administrator.
- No `setup`, `class` or `user` policies for an app's user.
- Writes only below a folder made for the app, so a mistake cannot touch the rest of the site.
- Review the role whenever the app gets a new feature.

## Checking rights from the app

| Need | Service |
|---|---|
| Does the user have a module/function? | `expsession::access` (answers `yes`, `no` or `limited`) |
| What may the user do with this node? | `expnode::rights::<node id>` |
| Which classes may the user create here? | `expnode::creatableClasses::<node id>` |
| Who is signed in? | `expsession::whoami` |

Use them to hide buttons the user cannot use; the server checks again on every call regardless.

## Auditing app activity

The audit trail records what credentials and writes do, in the `access` channel and as service events:

| Events | When |
|---|---|
| `access.apikey.create`, `.revoke`, `.use` (first use), `.use.failed` | personal API keys ([Auditing and limits](../../../guides/api-keys.md#8-auditing-and-limits)) |
| `access.expservices.token.create`, `.revoke`, `.failed` | expservices tokens |
| `service.<domain>.<method>` | every successful expservices write |

An administrator reads them in **Setup > Audit** or from a shell:

```bash
./console exp:audit tail --channel=access --name='access.apikey.*' --lines=20 --allow-root-user
```

Ask for a look at the refusals (`*.failed`) after each app release: a spike means a bug in the app or someone trying
stolen credentials. The audit itself is described in [Audit](../../../bc/6.0/audit.md).

## Related pages

- [Choosing a credential](../auth/choosing-a-credential.md)
- [Access control in code](../../creators/app-creators/access-control-in-code.md)
- [Security and audit](../../../guides/security-and-audit.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
