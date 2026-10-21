---
title: "Authorization: roles, policies and limitations"
audience: web-framework-developer, developer
category: web-frameworks
order: 250
summary: The Exponential permission system for developers used to voters, gates, policies, Django permissions or CanCan abilities - roles made of policies, module functions and limitations, assignments to users and groups limited by subtree or section, what the kernel checks before a view, how to check in PHP and templates, and how to declare permissions for your own module.
keywords: authorization, permissions, roles, policies, limitations, functions, hasAccessTo, canRead, canEdit, accessWord, FunctionList, voters, gates, Laravel policies, Django permissions, CanCanCan, Pundit, Spring Security, web framework developer
type: help
---
# Authorization: roles, policies and limitations

This page is for developers who write voters, gates, policy classes or permission decorators. Exponential's
permissions are **data**, managed by administrators in **Users and roles**: a **role** is a set of **policies**; a
policy grants one **function** of one **module** (`content/read`, `expdemo/manage`), optionally restricted by
**limitations** (only this class, this section, this subtree, this language, this siteaccess); a role is **assigned**
to users or groups, optionally limited to a subtree or section. Your code declares the functions and checks them;
administrators decide who gets them ([Policies, roles and limitations](../../standards/architecture/kernel-architecture.md#policies-roles-and-limitations)).

## Contents

1. [The model](#the-model)
2. [Declaring permissions for your module](#declaring-permissions-for-your-module)
3. [What the kernel checks for you](#what-the-kernel-checks-for-you)
4. [Checking in PHP](#checking-in-php)
5. [Checking in templates](#checking-in-templates)
6. [Compared with other frameworks](#compared-with-other-frameworks)
7. [Rules](#rules)

## The model

| Term | Class | Example |
|---|---|---|
| Role | `eZRole` | Editor |
| Policy | `eZPolicy` | `content/edit` |
| Limitation | `eZPolicyLimitation`, `eZPolicyLimitationValue` | Class: Article, Section: Standard |
| Assignment | role to user or group, optionally limited | Editor for the group Editors, limited to the subtree "News" |

The guide for administrators: [Roles, policies and unactivated users](../../guides/roles-and-policies.md).

## Declaring permissions for your module

```php
// extension/expdemo/modules/expdemo/module.php
$ViewList['manage'] = array( 'script' => 'manage.php', 'functions' => array( 'manage' ) );

// a limitation whose values are the sections, as kernel/content/module.php defines $SectionID
$SectionLimitation = array( 'name' => 'Section', 'values' => array(), 'class' => 'eZSection',
                            'function' => 'fetchList', 'parameter' => array( false ) );

$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['manage'] = array( 'Section' => $SectionLimitation );
```

`$FunctionList` names the functions and the limitations each accepts; administrators then see `expdemo/read` and
`expdemo/manage` in the policy editor ([Policy functions and limitations](../creators/app-creators/policy-functions-and-limitations.md),
[module.php reference](../creators/app-creators/module-definition-reference.md#functionlist-and-limitations)).

## What the kernel checks for you

- A view's `'functions' => array( 'manage' )` is checked before the view runs: without the function the user gets
  "access denied", an anonymous user the login page.
- That check does not evaluate limitations for your own data; it only knows the user has the function, possibly
  limited.
- Content fetches (`subTreeByNodeID()`, `fetch( 'content', ... )`) return only what the user may read, applying every
  content limitation.

## Checking in PHP

```php
$user = eZUser::currentUser();
$access = $user->hasAccessTo( 'expdemo', 'manage' );
switch ( $access['accessWord'] )
{
    case 'yes':     // unlimited
        break;
    case 'limited': // $access['policies'] holds the limitations, for example allowed section ids
        break;
    default:        // 'no'
        return $Params['Module']->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

if ( !$node->canRead() ) { /* deny */ }
if ( !$object->canEdit() ) { /* deny */ }
```

For content, ask the object or node: they apply class, section, owner, subtree, state and language limitations.
For your own tables, apply the limitations from `$access['policies']` in your query, and check ownership yourself
([Access control in code](../creators/app-creators/access-control-in-code.md)).

## Checking in templates

```text
{if fetch( 'user', 'has_access_to', hash( 'module', 'expdemo', 'function', 'manage' ) )}
  <a href={'expdemo/manage'|ezurl}>{'Manage'|i18n( 'extension/expdemo' )}</a>
{/if}
```

Hiding a link is convenience, not security: the view must check again
([Role and policy template operators](../../features/6.0/role-and-policy-template-operators.md)).

## Compared with other frameworks

| Framework | Concept | Exponential |
|---|---|---|
| Symfony | `isGranted()`, voters, `#[IsGranted]` | `hasAccessTo()`, `canRead()`, the view's `functions` |
| Laravel | gates, policy classes, `can` middleware | functions in `$FunctionList`, checks in the view |
| Django | model permissions, `@permission_required`, object permission packages | functions and limitations; object checks built in for content |
| Rails | CanCanCan abilities, Pundit policies | roles as data, edited in the administration |
| Spring | method security, ACLs | policies with limitations; content ACL-like checks built in |

The biggest difference: rules live in the database and are changed by administrators, not in code. Your code names
the functions and asks the questions.

## Rules

- Access is decided by policies, never by checking a user or group id in code or templates.
- One function per kind of action (`read`, `create`, `manage`), not per screen.
- Never grant a new function to Anonymous by default; document what to grant.
- Apply the same checks to a list as to one item: filter the query, do not hide rows in the template.
- Background work runs as the user who queued it, or as a dedicated integration user with a role made for it.

## Related pages

- [Authentication and sessions](authentication-and-sessions.md)
- [Policy function pattern](../../standards/patterns/policy-function.md)
- [Content limitation handlers](../../features/6.0/content-limitation-handlers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
