---
title: Access control in code
audience: app-creator, developer
category: app-creation
order: 590
summary: Apply Exponential's permissions in every layer of an application - the current user, policy checks with limitations, content-level checks, running background work as the right user, integration users, and recording access decisions in the audit trail.
keywords: access control, permissions, authorization, eZUser, currentUser, hasAccessTo, limitations, canRead, canEdit, background jobs, integration user, audit, app creator
type: help
---
# Access control in code

This page is for application creators who make sure each user sees and does only what roles allow. The kernel checks
the module function before a view runs; everything finer is your code's job
([Policy functions and limitations](policy-functions-and-limitations.md)).

## Contents

1. [Who is the user?](#who-is-the-user)
2. [Three levels of checks](#three-levels-of-checks)
3. [Background work](#background-work)
4. [Integration users](#integration-users)
5. [Recording decisions](#recording-decisions)

## Who is the user?

```php
$user = eZUser::currentUser();
if ( !$user->isRegistered() ) { /* anonymous */ }
$userID = eZUser::currentUserID();
```

In web requests the user comes from the session or an API credential; in scripts, cronjobs and workers it is anonymous
unless the code logs one in.

## Three levels of checks

| Level | Check | Example |
|---|---|---|
| Function | the view's `functions` in `module.php`, or `hasAccessTo( 'expdemo', 'manage' )` | may this user manage at all? |
| Limitation | evaluate `$access['policies']` when `accessWord` is `limited` | only in these sections |
| Object | `$node->canRead()`, `$object->canEdit()`, `$object->checkAccess( 'remove' )`; your own owner checks for your tables | this item |

Check at every level that applies, in every entry point. A list of items must apply the same checks as one item: filter
the query, do not hide rows in the template.

## Background work

- A queue job runs as the user who queued it (`user_id` of expqueue); check permissions as that user, as a web request
  would ([Queues with expqueue](queues-with-expqueue.md)).
- A cronjob that writes content logs in a dedicated user with a role made for it.
- Never run background work as the administrator "to be safe": it hides permission bugs and makes the audit useless.

## Integration users

For an integration (an import, an API client), create a user with a role holding only what the integration needs,
limited to its subtree or section. Give it its own API key or OAuth client, so it can be revoked alone
([OAuth and API keys](oauth-and-api-keys.md)).

## Recording decisions

Write security-relevant actions and refusals to the audit trail, with IDs and never secrets or personal content, so
administrators can review them ([Audit trail](../../../features/6.0/audit-trail.md), [Audit](../../../bc/6.0/audit.md)).

## Related pages

- [Policy functions and limitations](policy-functions-and-limitations.md)
- [Security for applications](security-for-applications.md)
- [Permission checks for many assignments](../../../features/6.0/permission-checks-for-many-assignments.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
