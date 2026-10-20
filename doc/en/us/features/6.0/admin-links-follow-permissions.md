# Admin links follow permissions

This page is for administrators who set up roles for editors, and for template authors who show admin links. Since
2026-10-02 the admin dashboard, the top menu and the left menus show a link only to a user who can open the address
behind it. Before, an editor saw the Design, Newsletter and Export tabs and left-menu entries such as Users, Tags or
Trash that the role did not allow, and got an "access denied" page on click.

## What is checked

The kernel class `expViewAccess` answers "can this user open this address?" the same way the request itself would.
It checks:

- the URL alias;
- the policy functions of the module view and their limitations (a view without functions needs the module);
- the siteaccess's `[SiteAccessRules]` and `RequireUserLogin`;
- the `user/login` SiteAccess limitation;
- for `content/view` and `content/edit`, the node or object.

This comes on top of the `PolicyList_<name>[]` entries of `menu.ini`, which stay for what an address cannot tell. A
left menu with no link left is hidden as a whole.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/menu.ini` | `MenuAccessSettings` | `CheckViewAccess` | `enabled` | siteaccess or override |
| `settings/menu.ini` | `MenuAccessSettings` | `NoAccessLinks` | `hidden` (`disabled` shows the name without a link, as the 4.x releases did) | siteaccess or override |

Show the shipped values:

```bash
grep -n "CheckViewAccess\|NoAccessLinks" settings/menu.ini
```

## Use it in your own template

The fetch function `user/can_open` (defined in `kernel/user/function_definition.php`) gives the same answer:

```
{if fetch( 'user', 'can_open', hash( 'uri', 'setup/cache' ) )}
    <a href={'setup/cache'|ezurl}>Caches</a>
{/if}
```

## Check an installation

1. Sign in as an editor with a restricted role. The Design, Newsletter and Export tabs and the entries without a
   policy are gone.
2. Sign in as Administrator. Nothing is missing.

To take a tab out for everybody on one siteaccess, whatever the policies say, use
[HiddenTabs](hidden-admin-tabs.md).

## Related pages

- [Hide admin tabs: HiddenTabs](hidden-admin-tabs.md)
- [The admin4 design](admin4-design.md)
- [Setup wizard and editor siteaccess](setup-wizard-and-editor-siteaccess.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- [October 2026 chronicle](../../history/2026/2026-10.md)
