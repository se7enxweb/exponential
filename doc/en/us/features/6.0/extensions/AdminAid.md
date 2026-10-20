# AdminAid: switch user, inspect objects, translate classes

This page is for administrators who debug a site. `AdminAid` is a debugging helper with four tools:

| Tool | What it does |
|---|---|
| **Switch user** (`/aid/user_switch/<user id>`) | Log in as any user from an admin account, without the user's password, to see the site as that user sees it. |
| **Object view** and **object list** | A detailed overview of a content object's data, and a list of all objects of a class. |
| **Class translation** | A simple interface for translating the attributes of a content class. |
| **Search** | Look up ids in the database across configured tables. |

## Use it safely

The author's warning stands: do not run it on a live server for more than a limited time. It is a potential
security risk, even though in theory it is secure.

1. Grant the policy functions of module `aid` (`user_switch`, `object_view`, `class_translate`, `search`) to your own
   role only.
2. Set `LimitByIP=enabled` and put your address in `IPList[]`.
3. Activate the extension and do your work.
4. Remove the extension from `ActiveExtensions` when you are done.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `aid.ini` | `Aid` | `UserSwitchIDLimit[]` | `14` | Only users with these ids may switch user |
| `aid.ini` | `Aid` | `UserClass[]` | `user` | Classes that represent a user |
| `aid.ini` | `Aid` | `LimitByIP` | `disabled` | Enable an IP limit |
| `aid.ini` | `Aid` | `IPList[]` | empty | Allowed addresses |
| `aid.ini` | `SearchDatabase` | `ClassList[]` and more | | Tables and columns searched |

This repository received only funding metadata in the Exponential 6 period.

## Related pages

- [Chronicle](../../../history/extensions/AdminAid.md) and [release notes](../../../changelogs/extensions/AdminAid.md)
- [Change ledger](../../../history/ledger/AdminAid.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2024-01](../../../history/extensions/months/2024-01.md), [2026-03](../../../history/extensions/months/2026-03.md)
