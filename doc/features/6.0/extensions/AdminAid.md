# AdminAid: switch user, inspect objects, translate classes

`AdminAid` is a debugging helper for administrators:

* **Switch user** (`/aid/user_switch/<user id>`): log in as any user from an admin account without the user's password, to see the site as that
  user sees it.
* **Object view** and **object list**: a detailed overview of a content object's data, and a list of all objects of a class.
* **Class translation**: an easy interface for translating the attributes of a content class.
* **Search**: look up ids in the database across configured tables.

The author's warning stands: do not run it on a live server for more than a limited time. It is a potential security risk even though in theory it
is secure. Restrict it with these settings in `aid.ini`:

| Block | Key | Default | Meaning |
|---|---|---|---|
| Aid | `UserSwitchIDLimit[]` | `14` | Only users with these ids may switch user |
| Aid | `UserClass[]` | `user` | Classes that represent a user |
| Aid | `LimitByIP` | `disabled` | Enable an IP limit |
| Aid | `IPList[]` | empty | Allowed addresses |
| SearchDatabase | `ClassList[]` and more | | Tables and columns searched |

Policy functions on module `aid`: `user_switch`, `object_view`, `class_translate`, `search`. Grant them to nobody else but your own role, set
`LimitByIP=enabled` with your address, and remove the extension from `ActiveExtensions` when you are done.

This repository received only funding metadata in the Exponential 6 period.

## Related

* [Chronicle](../../../history/extensions/AdminAid.md) and [release notes](../../../changelogs/extensions/AdminAid.md)
