# nxc_powercontent: create and update content from code and from REST

`nxc_powercontent` ("NXC Powercontent", originally by NXC) extends how code creates, updates and removes content:

* download files from external resources when creating or updating objects;
* give HTML for `ezxmltext` attributes;
* set or change locations (main and additional);
* a debug function built in, convenient in command line scripts;
* when an object is updated **no new version is created**.

In the Exponential 6 era it was extended for use by the REST extension [ezprestapi](ezprestapi.md), where it provides the create, update and delete
calls (1.1.0, October 2024).

## Template fetch: `class_list` (1.3.0, 12 July 2026)

A new `content` fetch function lists content classes without custom PHP, filterable by group, sortable by name and paginated, mirroring the admin
class list:

```
{def $classes = fetch('content', 'class_list', hash('as_object', false(), 'sorts', true()))}
{foreach $classes as $class}
    {$class.name}
{/foreach}
```

Parameters: `as_object`, `group_list`, `sorts`, `limit`. Use it for admin dashboards, custom navigation or API responses.

## Admin: Copy selected, Hide and Unhide selected (1.2.0, 21 June 2026)

The extension overrides `kernel/content/action.php`, and its copy lacked the handlers for `CopyButton` and `HideButton`/`UnhideButton`. From the admin3
sub items **More actions** menu, **Copy selected** and **Hide/Unhide selected** fell through to "Unknown content object action" and failed silently.
Both handlers were added: Copy checks `canCreate`, collects class, section and node metadata for the browse restrictions and shows the destination
selector; Hide/Unhide checks `can_hide`, skips nodes already in the target state and goes back to the parent view. See
[Subitems "More actions" menu](../../../bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md).

## Other changes

* 1.1.0 and later: PHP 8.5 nullable type fix (December 2025).
* 1.4.0 (22 September 2026): module views safe on a persistent worker (Velocity); 1.4.3: `content/edit` declares its redirect helper only once, so a persistent
  PHP worker can run it more than once.
* 1.4.2 (2 October): the **PDF export** no longer stops with a fatal error: the content cache info is read from an object instance, as PHP 8 requires. See
  [PDF export](../pdf-export.md).
* 1.4.1: copyright notices name 1998 - 2026 7x & Exponential Foundation first.

## Related

* [ezprestapi](ezprestapi.md)
* [Chronicle](../../../history/extensions/nxc_powercontent.md) and [release notes](../../../changelogs/extensions/nxc_powercontent.md)
