---
title: eZUser and access checks
audience: developer
category: extension-development
order: 90
summary: The current user and access checks from PHP and templates - eZUser::currentUser(), isAnonymous(), isLoggedIn(), hasAccessTo() and its accessWord yes, no or limited, object-level checks, and the user fetch functions.
keywords: eZUser, currentUser, currentUserID, isAnonymous, isLoggedIn, hasAccessTo, accessWord, limited, policies, roles, has_access_to, canRead, access check
type: help
---
# eZUser and access checks

Part 8, reference 9. `eZUser` (`kernel/classes/datatypes/ezuser/ezuser.php`) is a user account. Every request has a
current user; on a public page that is usually the anonymous user. Access is granted through roles and their
policies ([Sections, roles and access for builders](../part-2-builder/sections-roles-and-access-for-builders.md)), and
an extension declares its own policy functions ([Policy functions and access](../part-3-extension-developer/policy-functions-and-access.md)).

## The current user

| Method (static) | Returns |
|---|---|
| `eZUser::currentUser()` | the user of this request |
| `eZUser::currentUserID()` | the ID |
| `eZUser::fetch( $id, $asObject = true )`, `fetchByName( $login, ... )`, `fetchByEmail( $email, ... )` | other users |

| Method | Returns |
|---|---|
| `isAnonymous()` | `true` for the anonymous user |
| `isLoggedIn()` | `true` when signed in |
| `contentObject()` | the user's content object (name, photo ...) |
| `groups( $asObject = false )` | the user's groups |
| `roles()` | the user's roles |

## hasAccessTo()

```php
function hasAccessTo( $module, $function = false )
```

returns an array whose `accessWord` is one of:

| accessWord | Meaning |
|---|---|
| `yes` | full access |
| `no` | no access |
| `limited` | access with limitations (sections, classes, subtrees ...), listed in `policies` |

```php
<?php
$user = eZUser::currentUser();
$access = $user->hasAccessTo( 'myext', 'manage' );
if ( $access['accessWord'] === 'no' )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}
```

Run in a command line script as the anonymous user, `hasAccessTo( 'content', 'read' )` returned `limited` and
`hasAccessTo( 'setup', 'setup' )` returned `no`.

For a policy function without limitations (most extension functions), treat anything but `yes` as no access unless
your function declares limitations and you evaluate them.

## Access to content

For content, use the object's and node's own checks, which evaluate all limitations:

| Method | On |
|---|---|
| `canRead()`, `canEdit( ... )`, `checkAccess( $functionName, ... )` | `eZContentObject` |
| `canRead()`, `canEdit()`, `canCreate()`, `canRemove()` | `eZContentObjectTreeNode` |

`subTreeByNodeID()` and the content fetch functions apply read access themselves.

## In templates

```text
{if fetch( 'user', 'has_access_to', hash( 'module', 'myext', 'function', 'manage' ) )}
    <a href={'/myext/manage'|ezurl}>{'Manage'|i18n( 'extension/myext' )}</a>
{/if}
```

`fetch( 'user', 'current_user' )` gives the current user. In admin menus, `menu.ini [MenuAccessSettings]` hides links a
user cannot open ([Context menus and admin integration](../part-4-upgrading/context-menus-and-admin-integration.md)).

## Rules

- Check access in the view, even if the link to it is hidden: anyone can type a URL.
- Never compare user IDs or group names in code to grant access; grant it through a policy function, so
  administrators can assign it.
- Signing a user in from code (`eZUser::loginUser()`) is for login handlers; an ordinary extension never does it.

**Demo mode.** `eZUser::store()` keeps the stored password of an administrator while `EXP_DEMO_MODE=1`; ask `expDemoMode::protectsPassword( $user )` before changing a password so the user gets the reason. [Demo mode for developers](demo-mode-for-developers.md).

## Related pages

- Previous: [eZContentClass and its attributes](ezcontentclass.md)
- Next: [eZHTTPTool: requests, sessions and redirects](ezhttptool.md)
- [Security in extensions](../part-3-extension-developer/security-in-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
