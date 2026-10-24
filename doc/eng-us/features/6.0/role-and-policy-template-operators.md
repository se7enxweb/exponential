# Role and policy checks in templates

This page is for template developers who show or hide parts of a page by role or permission. Seven operators do it
in one line, without fetching users and looping over policies yourself. Added in January 2026 (release 6.0.12); a matching PHP method,
`eZRole::hasPolicy()`, lets your own code ask the same question.

## The seven operators

All operators live in the class `eZTemplateRoleOperator`
(`lib/eztemplate/classes/eztemplateroleoperator.php`). They return `true` or
`false`.

**The piped value is ignored.** An operator needs something on its left, so write
`$current_user|has_role('Editor')`, but the answer depends only on the
parameters. Without `user_id` the **current user** is checked.

| Operator | Parameters (in order) | True when |
|---|---|---|
| `has_role` | `role_name`, optional `user_id` | the user has a role with this name |
| `has_role_id` | `role_id`, optional `user_id` | the user has the role with this id |
| `has_any_role` | `roles` (array of names and/or ids), optional `user_id`, optional `match_all` | the user has at least one of the roles; with `match_all=true()` all of them |
| `has_role_by_user` | `user` (an `eZUser` object), `role_id` | that user object has the role |
| `has_role_by_user_id` | `user_id`, `role_id` | the user with that id has the role |
| `has_policy` | `module`, `function`, optional `user_id` | one of the user's roles has a policy for that module and function |
| `has_policy_by_user` | `user` (an `eZUser` object), `module`, `function` | the same, for that user object |

In `has_any_role`, an entry that is an integer (or a string of digits) is taken
as a role id; anything else as a role name.

## Examples

```
{* a block for editors only *}
{if $current_user|has_role('Editor')}
    <a href={'content/edit'|ezurl}>Edit</a>
{/if}

{* administrator or publisher *}
{if $current_user|has_any_role(array('Administrator', 'Publisher'))}
    ...
{/if}

{* must hold both roles, mixing names and ids *}
{if $current_user|has_any_role(array('Editor', 2), 0, true())}
    ...
{/if}

{* does the visitor's role set contain a content/edit policy? *}
{if $current_user|has_policy('content', 'edit')}
    <a href={concat('content/edit/', $node.contentobject_id)|ezurl}>Edit this</a>
{/if}

{* a specific other user, by id *}
{if has_role_by_user_id(42, 1)}
    ...
{/if}
```

(The `user_id` parameter is the second one of `has_any_role`, so the third
example passes `0` to mean "the current user" and sets `match_all` to true.
The last example asks about user 42 and role 1: `has_role_by_user_id` takes
both as parameters; a value piped in, as in `42|has_role_by_user_id(1)`, is
ignored like everywhere else, so that form asks about user 1 and role 0 and is always
false. `$current_user` is set in the pagelayout; in other templates define it
with `{def $current_user = fetch( 'user', 'current_user' )}`.)

## Use in PHP

```php
$role = eZRole::fetchByName( 'Editor' );
if ( $role && $role->hasPolicy( 'content', 'edit' ) )
{
    // the role has a content/edit policy
}
```

`eZRole::hasPolicy( $moduleName, $functionName = false )` returns true when the
role has a policy for the module and, if `$functionName` is given, for that
function. With `false` it answers for any function of the module.

## Know the limits

- `has_policy` reads the **policy rows** of the roles. It says "this user has a
  role with a content/edit policy"; it does not evaluate limitations (sections,
  subtrees, owner), so a content/edit policy limited to one section counts. A
  policy for every module (`*`, as the Administrator role has) or for every
  function of a module (`content/*`) counts too, as it does for the kernel's
  access check (before 23 October 2026 it did not, and the Administrator got
  false). The PHP method `eZRole::hasPolicy()` compares names only and does not
  treat `*` that way. To ask whether the user may edit one particular node, use
  the node's `can_edit` attribute instead.
- For checks that guard something security relevant, do not rely on hiding a
  link; the module view still checks permissions, hiding is a convenience.
- Operators evaluate on every use; keep them out of long loops and put the answer
  in a variable with `{def $is_editor = $current_user|has_role('Editor')}`.

## History

The first version (10 January 2026) was called `member_of_role` and its
siblings (`member_of_role_id`, `member_of_any_role`, `member_of_role_by_user`,
`member_of_role_by_user_id`) in a class `eZTemplateMemberOfRoleOperator`. The
policy operators were added the same day, the class was renamed
`eZTemplateRoleOperator` on 11 January and the operators were renamed to the
`has_` names. The `member_of_*` names were never part of a release; if you
copied an example from a development snapshot, replace `member_of_` by `has_`.

Until 18 October 2026 the class was not registered with the template engine, so a template that used one of the
operators got "unknown operator". Since then `lib/eztemplate/classes/eztemplateautoload.php` registers all seven;
`eZTemplateRoleOperatorRegistrationTest` checks the registration and renders each operator.

On 23 October 2026 `has_policy` and `has_policy_by_user` began to count policies for every module or function
(`*`), `has_role_by_user` answers false instead of failing when it gets no user object (as `has_policy_by_user`
already did), and a `user_id` given as a string (`'0'`, `$node.object.owner_id`) is read as a number.
`eZTemplateRoleOperatorLiveTest` renders every example on this page, interpreted and compiled, for the anonymous user,
the admin user and a user with two test roles.

The idea comes from the community extension `bcmemberofrole`.

## Related pages

- [Roles: policy IDs, sorting and order buttons](role-policy-order.md), [paging the role and policy screens](../../bc/6.0/role-policy-paging.md)
- [String operators](string-template-operators.md)
- [Changelog 6.0.12](../../../changelogs/6.0/6.0.12.md)
- [Chronicle: January 2026](../../history/2026/2026-01.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
