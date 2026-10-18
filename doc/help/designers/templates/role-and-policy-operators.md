---
title: Role and policy operators
audience: designer, developer
category: templates
order: 290
summary: Showing or hiding parts of a design by role or permission: the has_role and has_policy family of operators, the fetch alternatives user/has_access_to and user/can_open, and why hiding a link is never the same as protecting a page.
keywords: has_role, has_policy, has_any_role, has_access_to, can_open, permissions in templates, roles, policies, show for editors, hide for anonymous, access check
type: help
---
# Role and policy operators

This page is for designers who show something only to some users: an "Edit" link for editors, a members area link for
signed-in members.

## Contents

1. [The role and policy operators](#the-role-and-policy-operators)
2. [The fetch alternatives](#the-fetch-alternatives)
3. [Node permissions](#node-permissions)
4. [Showing is not protecting](#showing-is-not-protecting)
5. [Caching](#caching)

## The role and policy operators

Seven operators of `eZTemplateRoleOperator` (`lib/eztemplate/classes/eztemplateroleoperator.php`), described in
[Role and policy checks in templates](../../../features/6.0/role-and-policy-template-operators.md):

```tpl
{if $current_user|has_role( 'Editor' )} ... {/if}
{if $current_user|has_policy( 'content', 'edit' )} ... {/if}
{if $current_user|has_any_role( array( 'Editor', 'Administrator' ) )} ... {/if}
```

The piped value is ignored; without a `user_id` parameter the current user is checked.

> **Note**
> On some installations these operators were not registered with the template engine, so a template using them got
> an "unknown operator" warning; a registration fix is being made for 6.0.15. If an installation reports the operator as
> unknown, use the fetch functions below, which work everywhere.

## The fetch alternatives

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'edit' ) )}
    ...
{/if}

{if fetch( 'user', 'can_open', hash( 'uri', 'setup/info' ) )}
    <a href={'setup/info'|ezurl}>...</a>
{/if}
```

(`kernel/user/function_definition.php`: `has_access_to` with `module`, `function` and an optional `user_id`;
`can_open` with a `uri`.)

## Node permissions

For a specific node, the node itself knows: `$node.can_edit`, `$node.can_create`, `$node.can_remove`, `$node.can_read`
([Nodes, objects and attributes](nodes-objects-attributes.md)).

```tpl
{if $node.can_edit}
    <a href={concat( 'content/edit/', $node.contentobject_id )|ezurl}>{'Edit'|i18n( 'design/mytheme' )}</a>
{/if}
```

## Showing is not protecting

Hiding a link does not stop anyone from opening the URL. Access is enforced by roles and policies in the administration;
templates only decide what to show.

## Caching

Output that depends on the user's roles must be cached per role set: the view cache already varies by the user's roles;
a cache-block needs `$current_user.role_id_list|implode( ',' )` in its keys ([Cache-block](cache-block.md)).

## Related pages

- [Logic and comparison operators](logic-and-comparison-operators.md)
- [User and login templates](user-and-login-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
