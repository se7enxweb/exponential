---
title: Policy functions and access checks
audience: developer, app-creator
category: extension-development
order: 110
summary: Protect a module: policy functions and limitations in module.php, views that require them, eZUser::hasAccessTo() with its yes, no and limited answers, and granting through roles.
keywords: policy, FunctionList, functions, limitation, hasAccessTo, accessWord, limited, role, permission, access denied, module security, eZUser, limitation values
type: help
---
# Policy functions and access checks

Part 3, chapter 11. A view without a policy function can be opened by anyone who reaches the site. This chapter shows
how to declare the functions of your module, how the kernel enforces them, and how to check access yourself when a
view needs more than "may or may not".

## Declare functions

In `module.php`:

```php
<?php
$Module = array( 'name' => 'Hello' );

$ViewList = array();
$ViewList['world'] = array( 'script' => 'world.php', 'params' => array( 'Name' ), 'functions' => array( 'read' ) );
$ViewList['admin'] = array( 'script' => 'admin.php', 'functions' => array( 'administrate' ) );

$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['administrate'] = array();
```

- `$FunctionList` lists what roles can grant: **Users > Roles and policies** now offers `hello/read` and
  `hello/administrate`.
- `'functions'` on a view says what a user needs. The kernel checks it **before** the view runs and shows the access
  denied page otherwise; your script does not need to repeat it.
- `'functions' => array( 'read', 'administrate' )` means either one is enough.

Anonymous visitors get nothing until you grant the function to the Anonymous role; that is the safe default.

## Limitations

A function can be limited. The kernel's `content/read` is limited by class, section, owner, group, node, subtree and
object state (`kernel/content/module.php`):

```php
$FunctionList['read'] = array( 'Class' => $ClassID,
                               'Section' => $SectionID,
                               'Owner' => $Assigned,
                               'Group' => $AssignedGroup,
                               'Node' => $Node,
                               'Subtree' => $Subtree );
```

A limitation is defined by where its possible values come from. From the expai extension
(`modules/expai/module.php`):

```php
$Provider = array( 'name' => 'Provider', 'values' => array(), 'class' => 'expAiPolicy',
                   'function' => 'providers', 'parameter' => array() );
$FunctionList['model_use'] = array( 'Provider' => $Provider );
```

| Key | Meaning |
|---|---|
| `name` | the limitation's name shown in the role editor |
| `values` | a fixed list of `array( 'Name' => ..., 'value' => ... )`, or empty when a class provides them |
| `class`, `function`, `parameter` | a static method that returns the possible values |

The kernel shows the choices in the role editor. **Your view must enforce a limitation itself**: the kernel only
checks that the user has the function in some form.

## Checking access from PHP

`eZUser::hasAccessTo( $module, $function = false )` (`kernel/classes/datatypes/ezuser/ezuser.php`) answers with an
array whose `accessWord` is:

| `accessWord` | Meaning | Also returned |
|---|---|---|
| `yes` | the user may, without limits | |
| `no` | the user may not | |
| `limited` | the user may, within limitations | `policies`: the limitations of each policy |

```php
<?php
$user = eZUser::currentUser();
$access = $user->hasAccessTo( 'hello', 'administrate' );

if ( $access['accessWord'] === 'no' )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}
if ( $access['accessWord'] === 'limited' )
{
    // collect the values of one limitation from every policy that grants the function
    $allowed = array();
    foreach ( $access['policies'] as $limitations )
    {
        if ( isset( $limitations['Provider'] ) )
        {
            $allowed = array_merge( $allowed, (array)$limitations['Provider'] );
        }
    }
    // ... allow only what is in $allowed
}
```

For content, use the content object's own checks: `$object->canRead()`, `$node->canEdit()` and the like, which apply
every content limitation correctly. Never re-implement content permissions.

## In templates

```text
{if fetch( 'user', 'has_access_to', hash( 'module', 'hello', 'function', 'administrate' ) )}
    <a href={'/hello/admin'|ezurl}>Administration</a>
{/if}
```

Hiding a link is a courtesy; the view's `'functions'` is the protection.

## Granting it

Roles are configured in the administration. For an extension that needs its function granted on installation (for
example to the Editor role), ship a command or document the step. The kernel's own example is
`./console exp:addexphelpreadpolicy`, which grants `exphelp/read` and lists the roles first with `--dry-run`.

## Checking a user

```bash
./console exp:accesscheck --help
```

answers what a user may do with a node or object and which limitation refuses it.

## Related pages

- Previous: [View parameters, unordered parameters and POST](view-parameters.md)
- Next: [View results, the path and the pagelayout](view-results-and-the-pagelayout.md)
- [Sections, roles and access for builders](../part-2-builder/sections-roles-and-access-for-builders.md)
- [eZUser and access checks](../part-8-reference/ezuser-and-access.md)
- [Content policy limitations of extensions](../../../features/6.0/content-limitation-handlers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
