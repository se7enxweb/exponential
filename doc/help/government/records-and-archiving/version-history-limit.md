---
title: The version history limit
audience: government, admin, project-manager
category: public-sector
order: 40
summary: For administrators and records managers - how many versions Exponential keeps per object (45 by default, per class configurable), what happens when the limit is reached, how to raise it for record-keeping classes, and the cleanup script when lowering it.
keywords: version history limit, DefaultVersionHistoryLimit, VersionHistoryClass, content.ini, VersionManagement, archived versions, cleanupversions.php, records, retention of versions, notices, public sector
type: help
---
# The version history limit

This page is for administrators and records managers. Exponential does not keep every version forever. When an object
reaches its limit, publishing a new version removes the oldest archived one
([Versions of an object: A new draft from an old version](../../../guides/content-history.md#3-a-new-draft-from-an-old-version)).
For records, know the limit and set it per class.

## Contents

1. [The settings](#the-settings)
2. [Choosing values](#choosing-values)
3. [Raising or lowering](#raising-or-lowering)
4. [Related pages](#related-pages)

## The settings

`settings/content.ini [VersionManagement]`:

```ini
[VersionManagement]
DefaultVersionHistoryLimit=45
VersionHistoryClass[1]=75
```

- `DefaultVersionHistoryLimit`: versions kept per object, for every class without its own value.
- `VersionHistoryClass[<class id or class identifier>]`: a limit for one class (class identifiers are accepted since
  4.1.1, as the settings file notes).

## Choosing values

| Class | Typical choice | Why |
|---|---|---|
| Official notices, decisions, regulations | a high limit | every published wording may matter later |
| News articles | the default | |
| Frequently edited pages (opening hours, contact) | the default or lower | many small edits |

A high limit costs database space; it does not slow down normal use much. For full certainty about what was
published, do not rely on versions alone: export the record to your records system when it is published (see
"Exporting content").

## Raising or lowering

In `settings/override/content.ini.append.php`:

```ini
[VersionManagement]
VersionHistoryClass[notice]=500
```

Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

- **Raising** affects future publishing; versions already removed do not come back.
- **Lowering** does not remove anything by itself; the settings file says you can run `bin/php/cleanupversions.php` to
  remove the archived versions over the new limit. Do that only after your records manager agrees, and after a backup.

Version removals are recorded in the audit as `content.version.remove`.

## Related pages

- [Versions and content history](versions-and-content-history.md)
- [Versions of an object](../../../guides/content-history.md)
- [Content model and editing](../../../guides/content-model-and-editing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
