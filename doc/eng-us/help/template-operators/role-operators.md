---
title: Role operators
audience: developer, designer, app-creator
category: development
order: 260
summary: The 7 operators of eZTemplateRoleOperator (has_role, has_role_id, has_any_role, has_role_by_user, has_role_by_user_id, has_policy, has_policy_by_user) - what they check, why the piped input is ignored, why they are not an access check, with read-only examples rendered on alpha as the anonymous user.
keywords: has_role, has_role_id, has_any_role, has_role_by_user, has_role_by_user_id, has_policy, has_policy_by_user, role, policy, permission, eZRole, eZTemplateRoleOperator
type: help
---
# Role operators

These operators ask whether a user has a role, or a policy of a module and function, so a template can show or hide
parts of a page by role. They are registered by `eZTemplateRoleOperator`
(`lib/eztemplate/classes/eztemplateroleoperator.php`) through `lib/eztemplate/classes/eztemplateautoload.php`. All
return a boolean and read the roles with `eZUser::roles()`, which includes the roles the user gets through user groups.

The examples were rendered by a command-line run that is not logged in, so the current user is the anonymous user
(id 10 on alpha) with the role "Anonymous" (id 1). Nothing was changed.

```tpl
{def $me = fetch( 'user', 'current_user' )}
current user: id {$me.contentobject_id}, roles: {foreach $me.roles as $r}{$r.id} {$r.name}{delimiter}, {/delimiter}{/foreach}
```

Output on alpha:

```text
current user: id 10, roles: 1 Anonymous
```

## Contents

1. [Two rules before you use them](#two-rules-before-you-use-them)
2. [has_role, has_role_id and has_any_role](#has_role-has_role_id-and-has_any_role)
3. [has_role_by_user and has_role_by_user_id](#has_role_by_user-and-has_role_by_user_id)
4. [has_policy and has_policy_by_user](#has_policy-and-has_policy_by_user)
5. [Related pages](#related-pages)

## Two rules before you use them

**The piped input is ignored.** `modify()` reads only the parameters. Many examples write
`$current_user|has_role( 'Administrator' )`, but the value before the `|` plays no part: the operator checks
the user named by `user_id`, and without it the **current** user. So `$someone|has_role( ... )` silently answers for
the visitor, not for `$someone`:

```tpl
'any value'|has_role( 'Anonymous' ) = {'any value'|has_role( 'Anonymous' )|choose( 'false', 'true' )}
```

Output on alpha:

```text
'any value'|has_role( 'Anonymous' ) = true
```

Use the call form and pass the user explicitly when you mean another user.

**They are not an access check.** A role or a policy says nothing about its limitations (sections, subtrees, classes,
owners). `has_policy( 'content', 'edit' )` is true for a user who may edit one folder only. To decide whether the user
may do something with a particular object, use the object's own checks (`$node.can_edit`, `$node.object.can_read`) or
the `user` fetch `has_access_to`, and remember that the server must check again when the action is performed: a hidden
button is not a permission.

## has_role, has_role_id and has_any_role

### has_role

Whether the user has a role with this exact name (compared with `==`, so case matters).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `role_name` | string | yes | `''` |
| `user_id` | integer | no | `0` (the current user) |

Generated reference: [has_role](../../examples/template-operators/has_role.md).

### has_role_id

The same by role id.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `role_id` | integer | yes | `0` |
| `user_id` | integer | no | `0` (the current user) |

Generated reference: [has_role_id](../../examples/template-operators/has_role_id.md).

### has_any_role

Whether the user has at least one of the roles listed, or with `match_all` all of them. Each entry is a role id when
it is an integer or a string of digits, otherwise a role name.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `roles` | array of names and ids | yes | `array()` |
| `user_id` | integer | no | `0` (the current user) |
| `match_all` | boolean | no | `false` |

Generated reference: [has_any_role](../../examples/template-operators/has_any_role.md).

```tpl
has_role( 'Anonymous' ) = {has_role( 'Anonymous' )|choose( 'false', 'true' )}
has_role( 'Administrator' ) = {has_role( 'Administrator' )|choose( 'false', 'true' )}
has_role_id( 1 ) = {has_role_id( 1 )|choose( 'false', 'true' )}
has_any_role( array( 'Editor', 1 ) ) = {has_any_role( array( 'Editor', 1 ) )|choose( 'false', 'true' )}
has_any_role( array( 'Anonymous', 'Editor' ), 0, true() ) = {has_any_role( array( 'Anonymous', 'Editor' ), 0, true() )|choose( 'false', 'true' )}
```

Output on alpha:

```text
has_role( 'Anonymous' ) = true
has_role( 'Administrator' ) = false
has_role_id( 1 ) = true
has_any_role( array( 'Editor', 1 ) ) = true
has_any_role( array( 'Anonymous', 'Editor' ), 0, true() ) = false
```

## has_role_by_user and has_role_by_user_id

### has_role_by_user

Whether the given user object (an `eZUser`, such as `fetch( 'user', 'current_user' )`) has the role with this id.
Anything else (a number, an empty variable) gives false.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `user` | eZUser object | yes | `null` |
| `role_id` | integer | yes | `0` |

Generated reference: [has_role_by_user](../../examples/template-operators/has_role_by_user.md).

### has_role_by_user_id

The same with the user's content object id.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `user_id` | integer | yes | `0` |
| `role_id` | integer | yes | `0` |

Generated reference: [has_role_by_user_id](../../examples/template-operators/has_role_by_user_id.md).

```tpl
{def $me = fetch( 'user', 'current_user' )}
has_role_by_user( $me, 1 ) = {has_role_by_user( $me, 1 )|choose( 'false', 'true' )}
has_role_by_user_id( $me.contentobject_id, 1 ) = {has_role_by_user_id( $me.contentobject_id, 1 )|choose( 'false', 'true' )}
```

Output on alpha:

```text
has_role_by_user( $me, 1 ) = true
has_role_by_user_id( $me.contentobject_id, 1 ) = true
```

## has_policy and has_policy_by_user

### has_policy

Whether any role of the user has a policy for the module and function. The policies are compared by module and function
name, where `*` in a policy matches every module or every function: the Administrator role's `*` policy answers true
for any question, and a `content/*` policy for every content function. Limitations are not looked at (see the rules
above).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `module` | string, such as `content` | yes | `''` |
| `function` | string, such as `read` | yes | `''` |
| `user_id` | integer | no | `0` (the current user) |

Generated reference: [has_policy](../../examples/template-operators/has_policy.md).

### has_policy_by_user

The same for a given `eZUser` object; anything else gives false.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `user` | eZUser object | yes | `null` |
| `module` | string | yes | `''` |
| `function` | string | yes | `''` |

Generated reference: [has_policy_by_user](../../examples/template-operators/has_policy_by_user.md).

```tpl
{def $me = fetch( 'user', 'current_user' )}
has_policy( 'content', 'read' ) = {has_policy( 'content', 'read' )|choose( 'false', 'true' )}
has_policy( 'content', 'edit' ) = {has_policy( 'content', 'edit' )|choose( 'false', 'true' )}
has_policy_by_user( $me, 'user', 'login' ) = {has_policy_by_user( $me, 'user', 'login' )|choose( 'false', 'true' )}
```

Output on alpha:

```text
has_policy( 'content', 'read' ) = true
has_policy( 'content', 'edit' ) = false
has_policy_by_user( $me, 'user', 'login' ) = true
```

All examples gave the same output interpreted and compiled on alpha.

## Related pages

- [Role and policy operators for designers](../designers/templates/role-and-policy-operators.md)
- [Policy functions and limitations](../creators/app-creators/policy-functions-and-limitations.md)
- [Access control in code](../creators/app-creators/access-control-in-code.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
