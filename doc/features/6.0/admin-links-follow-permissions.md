# Admin links follow permissions: a tab or menu link shows only to a user who can open it

Added 2026-10-02. An editor used to see the Design, Newsletter and Export tabs and left-menu entries such as Users,
Tags or Trash that the role did not allow, and got an "access denied" page when clicking them. The admin dashboard,
the top menu and the left menus now check the address behind each link and leave out the ones the signed-in user
cannot open.

## What is checked

The kernel class `expViewAccess` answers "can this user open this address?" the way the request itself would: the
URL alias, the policy functions of the module view with their limitations (a view without functions needs the
module), the siteaccess's `[SiteAccessRules]`, `RequireUserLogin`, the `user/login` SiteAccess limitation, and the
node or object of `content/view` and `content/edit`. It comes on top of the `PolicyList_<name>[]` entries of
`menu.ini`, which stay for what an address cannot tell.

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/menu.ini` | `MenuAccessSettings` | `CheckViewAccess` | `enabled` | siteaccess or override |
| `settings/menu.ini` | `MenuAccessSettings` | `NoAccessLinks` | `hidden` (`disabled` shows the name without a link, as the 4.x releases did) | siteaccess or override |

A left menu with no link left is left out too.

## Use it in your own template

```
{if fetch( 'user', 'can_open', hash( 'uri', 'setup/cache' ) )}
    <a href={'setup/cache'|ezurl}>Caches</a>
{/if}
```

The fetch function `user/can_open` is defined in `kernel/user/function_definition.php`.

## Check an installation

- Sign in as an editor with a restricted role: the Design, Newsletter and Export tabs and the entries without a
  policy are gone. Sign in as Administrator: nothing is missing.
- `grep -n "CheckViewAccess\|NoAccessLinks" settings/menu.ini` shows the shipped values.
- To take a tab out for everybody on one siteaccess whatever the policies say, use
  [HiddenTabs](hidden-admin-tabs.md).

Related: [behaviour changes of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md),
[admin4 design](admin4-design.md), [setup wizard and editor siteaccess](setup-wizard-and-editor-siteaccess.md),
[October 2026 chronicle](../../history/2026/2026-10.md).

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [hidden admin tabs](hidden-admin-tabs.md).
