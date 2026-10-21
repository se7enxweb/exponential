---
title: Service accounts, roles and policies
audience: integrator, admin, developer
category: integration
order: 350
summary: Giving an integration exactly the rights it needs - a service account per integration, a user group and a role of its own, the policies typical integrations need (content read, create, edit, remove, apikey/create, ezjscore/call), limitations by subtree, class, section and language, checking the result, and reviewing it.
keywords: service account, user, user group, role, policy, limitation, Subtree, Class, Section, Language, least privilege, content/read, content/create, content/edit, content/remove, apikey/create, ezjscore/call, oauth/authorize, accesscheck, access review
type: help
---
# Service accounts, roles and policies

This page is for administrators and integrators who set up the account an integration acts as. Every request,
script and job in Exponential runs **as a user**, and that user's roles decide what it may do. The REST API, API keys,
`expservices` and the content functions all check the same policies, so one well-built role limits the integration
on every surface. The administration side is [Roles, policies and unactivated users](../../guides/roles-and-policies.md).

## Contents

1. [One service account per integration](#one-service-account-per-integration)
2. [The policies integrations need](#the-policies-integrations-need)
3. [Limitations](#limitations)
4. [Setting it up](#setting-it-up)
5. [Checking the result](#checking-the-result)
6. [Reviewing it](#reviewing-it)

## One service account per integration

| Do | Why |
|---|---|
| A user per integration (`svc-erp-import`, `svc-crm-leads`), in a group "Integrations" | The audit trail and **Last used** of keys name the integration; one can be disabled without touching another |
| A role per integration, assigned to that user (or to a group of its own) | Changes for one integration do not widen another |
| A real but unused e-mail address on a reserved domain or the operator's mailbox | Password resets and notifications go nowhere surprising |
| A long random password, not used for signing in at all when keys or tokens are used | The password is the widest credential |
| No notification subscriptions | Imports must not mail the integration |

## The policies integrations need

| Integration does | Policies |
|---|---|
| Reads content through REST, `expservices` or an export | `content/read`, limited to the subtrees and classes it exports |
| Creates content | `content/create`, limited by class, parent class, subtree and language; plus `content/read` |
| Updates content | `content/edit`, limited the same way |
| Removes or hides content | `content/remove`, `content/hide` where needed |
| Uses a personal API key | `apikey/create` with the **Scope** limitation set to the scopes it needs |
| Calls ezjscore server functions | `ezjscore/call` with the **FunctionList** limitation for your function list |
| Lets users authorize an OAuth application | `oauth/authorize` for those users' roles (not the service account) |
| Reads collected information | `infocollector/read` |
| Runs content jobs | `content/jobs`, plus the policy of the operation itself (`content/remove`, `content/move`, `content/hide` ...) |

A create through the REST API or `eZContentFunctions` publishes when `content/create` allows it, and the publish
workflow runs as for an editor. The `$notify = false` argument of the content functions needs no policy of its own;
the **Publish without notification** button of the editor does (`content/publish_without_notification`).

## Limitations

| Limitation | Use |
|---|---|
| **Subtree** (or **Node**) | Only below the integration's folder, for example `/Products` |
| **Class** | Only the classes the integration owns (`product`, `product_category`) |
| **ParentClass** (on create) | Only below the right kind of container |
| **Section** | Only content in the section the integration feeds |
| **Language** | Only the languages it imports |
| **Assign with limitation** (Subtree or Section on the role assignment) | The same role, applied to one subtree only |

Several limitations of one policy must all be met; several values of one limitation are alternatives.

## Setting it up

1. **Users**: create the user in the group "Integrations" (create the group once, with no roles of its own).
2. **Setup > Roles and policies**: **New role** named after the integration ("ERP import").
3. Add each policy from the table above with **Grant limited access** and the limitations.
4. **Save**, then **Assign** the role to the service account.
5. If the integration uses an API key: sign in as the service account once and make the key
   ([Personal API keys for integrations](personal-api-keys-for-integrations.md)).
6. Record the role and its purpose in the interface specification.

## Checking the result

- Call a route the account must **not** reach (another subtree, another class) and expect `403`.
- Run the import's dry run as the account; a missing policy shows as a failed create.
- `php bin/php/accesscheck.php --help` shows how to check what a user may do with a node or object, and which
  limitation refuses it, from the shell.
- The audit trail shows refusals (`access.permission.refused`, `access.apikey.use.failed` with the reason `policy` or
  `permission`).

## Reviewing it

- [ ] Once a year and after every change of the integration: does the role still match what the integration does?
- [ ] Keys of the account: still used (**Last used**), still needed, end date planned?
- [ ] Integrations that were retired: account disabled, keys revoked, role removed.
- [ ] No service account has gained an administrator role "for testing".

## Related pages

- [Roles, policies and unactivated users](../../guides/roles-and-policies.md)
- [Policy functions and access](../extension-development/part-3-extension-developer/policy-functions-and-access.md)
- [Access control in code](../creators/app-creators/access-control-in-code.md)
- [Security for integrations](security-for-integrations.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
