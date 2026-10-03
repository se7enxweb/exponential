# nxc_powercontent: create and update content from code and from REST

This page is for developers who create or change content from scripts or through REST. `nxc_powercontent` ("NXC
Powercontent", originally by NXC) extends how code creates, updates and removes content:

- download files from external resources when creating or updating objects;
- give HTML for `ezxmltext` attributes;
- set or change locations (main and additional);
- a built-in debug function, convenient in command line scripts;
- **no new version is created** when an object is updated.

In the Exponential 6 period it was extended for the REST extension [ezprestapi](ezprestapi.md), where it provides the
create, update and delete calls (1.1.0, October 2024).

## List content classes in a template: `class_list`

Since 1.3.0 (12 July 2026) the `content` fetch function `class_list` lists content classes without custom PHP. It
filters by group, sorts by name and pages, mirroring the admin class list:

```
{def $classes = fetch('content', 'class_list', hash('as_object', false(), 'sorts', true()))}
{foreach $classes as $class}
    {$class.name}
{/foreach}
```

| Parameter | Meaning |
|---|---|
| `as_object` | Return objects or plain arrays |
| `group_list` | Restrict to class groups |
| `sorts` | Sort by name |
| `limit` | Number of classes |

Use it for admin dashboards, custom navigation or API responses.

## Admin: Copy selected, Hide and Unhide selected

The extension overrides the kernel's `content/action` view with its own `modules/content/action.php` (the module
`content` of the extension). That copy lacked the handlers for `CopyButton` and `HideButton`/`UnhideButton`, so in the
admin3 sub-items **More actions** menu, **Copy selected** and **Hide/Unhide selected** fell through to "Unknown content
object action" and failed silently.

Since 1.2.0 (21 June 2026) both handlers exist:

- **Copy** checks `canCreate`, collects class, section and node metadata for the browse restrictions, and shows the
  destination selector.
- **Hide/Unhide** checks `can_hide`, skips nodes already in the target state, and goes back to the parent view.

See [Subitems "More actions" menu](../../../bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md).

## Other changes

| Version | Date | Change |
|---|---|---|
| 1.1.0 and later | December 2025 | PHP 8.5 nullable type fix. |
| 1.4.0 | 22 September 2026 | Module views safe on a persistent worker (Velocity). |
| 1.4.1 | | Copyright notices name 1998 - 2026 7x & Exponential Foundation first. |
| 1.4.2 | 2 October 2026 | The **PDF export** no longer stops with a fatal error: the content cache info is read from an object instance, as PHP 8 requires. See [PDF export](../pdf-export.md). |
| 1.4.3 | | `content/edit` declares its redirect helper only once, so a persistent PHP worker can run it more than once. |

## Related pages

- [ezprestapi](ezprestapi.md)
- [Copy selected sub-items](../subitems-copy-selected.md)
- [Velocity engines](../../../bc/6.0/velocity-engines.md)
- [Chronicle](../../../history/extensions/nxc_powercontent.md) and [release notes](../../../changelogs/extensions/nxc_powercontent.md)
- [Change ledger](../../../history/ledger/nxc_powercontent.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2024-10](../../../history/extensions/months/2024-10.md), [2025-12](../../../history/extensions/months/2025-12.md), [2026-06](../../../history/extensions/months/2026-06.md), [2026-07](../../../history/extensions/months/2026-07.md), [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
