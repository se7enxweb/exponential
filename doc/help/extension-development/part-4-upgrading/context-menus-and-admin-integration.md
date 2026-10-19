---
title: Context menus and admin integration
audience: developer
category: extension-development
order: 200
summary: How an old extension's context menu entries, left menu links, node view tabs and top tabs plug into the Exponential 6 admin, and how to make each work without inline code and with access checks.
keywords: context menu, popupmenu, ContextMenuTemplateArray, SubitemsContextMenuTemplateArray, menuArray, data-menu-form, left menu, Leftmenu_setup, Links, PolicyList, AdditionalTabs, admininterface.ini, menu.ini, upgrade
type: help
---
# Context menus and admin integration

Part 4, chapter 20. An extension shows up in the administration in four places: the context menus of the content
tree, the left menu, the tabs of the node view, and the top menu. All four are registered by settings that have not
changed since the old releases, so an old extension's entries still appear. What changed is how they must behave:
no inline code, and no link a user cannot open. This chapter goes through the four places.

## 1. Context menu entries

The right-click menu of the content tree and of the sub-items list collects templates from
`admininterface.ini [AdditionalMenuSettings]`. se7enxweb/ezownerchange registers its two entries like this:

```ini
<?php /* #?ini charset="utf-8"?

[AdditionalMenuSettings]
ContextMenuTemplateArray[]=popupmenu/ownercontextmenu.tpl
SubitemsContextMenuTemplateArray[]=popupmenu/ownersubitemscontextmenu.tpl

*/ ?>
```

| Setting | Menu |
|---|---|
| `ContextMenuTemplateArray[]` | the content tree's context menu |
| `SubitemsContextMenuTemplateArray[]` | the sub-items list's context menu |
| `SubMenuTemplateArray[]` | submenus of those |
| `ClassMenuTemplateArray[]`, `BookmarkMenuTemplateArray[]`, `AdvancedMenuTemplateArray[]` | the class, bookmark and advanced menus |
| `ExportMenuTemplateArray[]`, `ImportExportMenuTemplateArray[]` | export and import entries |

The settings stay. The templates are what needs work, because the old ones use inline scripts and `onmouseover` or
`onclick` attributes, which an enforced Content Security Policy blocks: the entry is drawn and does nothing.

Two fixes, depending on what the entry does:

| The entry... | Fix | Example |
|---|---|---|
| submits a form | `data-menu-form="<form id>"` on the entry, which the kernel's `ezpopupmenu.js` submits (6.0.15 and later); a small file of yours for older kernels | se7enxweb/expchangeclass, commit 88b60d9 |
| opens a URL with the node's values | the URL in a data attribute, a small file that adds the entry to `menuArray`, one `mouseover` listener | se7enxweb/ezownerchange, commit a5ebbb8 |

Both are shown in full, before and after, in
[CSP: event handler attributes to data attributes](csp-event-handlers-to-data-attributes.md).

The kernel's own menus are declared entirely in `data-menu-*` attributes (`data-menu-url`, `data-menu-form`,
`data-menu-submenu`, `data-menu-shown-if` and others; the header comment of `ezpopupmenu.js` lists them). An entry
that only needs to run in Exponential 6.0.15 or later can use those directly and needs no script at all.

> **Warning**
> Never write `var menuArray = new Array();` or `menuArray = {};` in an extension. That replaces the kernel's menus,
> and the core context menus stop opening. Add your entry to the existing object.

### Show the entry only to users who may use it

Both example templates start with an access check, so a user without the policy does not see the entry:

```text
{if fetch( 'user', 'has_access_to', hash( 'module', 'owner', 'function', 'change' ) )}
...
{/if}
```

Old templates often show the entry to everybody and let the view refuse. Add the check.

## 2. Left menu links

A link in a left menu (the Setup menu is the usual place for an extension's tools) is a key in a `[Leftmenu_*]`
section of `menu.ini`. se7enxweb/expqueue's `settings/menu.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[Leftmenu_setup]
Links[expqueue]=expqueue/dashboard
LinkNames[expqueue]=Job queue
PolicyList_expqueue[]=expqueue/view
*/ ?>
```

- `Links[<key>]`: the address.
- `LinkNames[<key>]`: the text.
- `PolicyList_<key>[]`: the policies a user needs to see the link.

In Exponential 6 the kernel also checks, by default, whether the user can open the address itself
(`menu.ini [MenuAccessSettings] CheckViewAccess=enabled`), and leaves out links the user cannot open
(`NoAccessLinks=hidden`). So an old extension's link no longer shows to users without access, even if it never had a
`PolicyList_`. Keep or add the `PolicyList_` anyway: it states the intention and covers what an address cannot tell.

### Groups in admin4

admin4 arranges the Setup menu in groups (`Groups[]`, `GroupLinks_<group>[]`). A link in no group appears under
"More". The kernel's `menu.ini` comment says an extension puts its link in a group "with
`GroupLinks_<group>[]=<key>` in its menu.ini.append.php"; the groups `ai` and `updates` exist for extensions' links.
Pick the group that fits what your tool does.

## 3. Tabs of the node view

The node view's tabs are `admininterface.ini [WindowControlsSettings] AdditionalTabs[]`, each with an
`[AdditionalTab_<id>]` section. The kernel's audit tab is a good model:

```ini
[WindowControlsSettings]
AdditionalTabs[]=audit

[AdditionalTab_audit]
Title=
Description=
NavigationPartName=ezcontentnavigationpart
HeaderTemplate=audit/node_header.tpl
Template=audit/node.tpl
```

What to check in an old extension's tab:

- its templates carry no inline code and no inline CSS (chapters 13 to 16 of this part);
- it checks access in its template, as the audit tab does for `audit/read`;
- it looks right in admin4, light and dark ([admin4 styling for extension pages](admin4-styling-for-extension-pages.md)).

A siteaccess can hide an extension's tab with `HiddenTabs[]`, which exists because "a siteaccess cannot take a tab
out of AdditionalTabs[] that an extension appends" (the comment in the kernel's `admininterface.ini`).

## 4. Top menu tabs and navigation parts

An extension with its own area of the administration may add a navigation part
(`menu.ini [NavigationPart] Part[<identifier>]=<name>`) and a top tab (`[TopAdminMenu] Tabs[]` with a
`[Topmenu_<name>]` section). Most extensions do not need one: a group of links in Setup is easier to find and keeps
the top menu short. If your old extension has a top tab, consider moving it into Setup during the upgrade, and say so
in the changelog.

`[TopAdminMenu]` also has a list of tabs to leave out, for the same reason as `HiddenTabs[]`.

## Check your work

- Right-click a node in the content tree and in the sub-items list, as an administrator and as a user without your
  policy: the entry appears only for the first, and works.
- With the browser console open: no Content Security Policy reports from your entries.
- The left menu link appears in the right group in admin4, and not for a user who cannot open it.

## Related pages

- Previous: [admin4 styling for extension pages](admin4-styling-for-extension-pages.md)
- Next: [Naming and branding during an upgrade](naming-and-branding-during-an-upgrade.md)
- [Content Security Policy](../../../features/6.0/content-security-policy.md)
- [Policy functions and access](../part-3-extension-developer/policy-functions-and-access.md)
- [Modules and views](../part-3-extension-developer/modules-and-views.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
