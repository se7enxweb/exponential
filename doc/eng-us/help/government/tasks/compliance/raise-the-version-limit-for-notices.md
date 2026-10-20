---
title: Raise the version limit for notices
audience: government, admin
category: public-sector
order: 520
summary: Step by step - keep more versions of official notices than Exponential's default 45 per object by setting VersionHistoryClass for that class, so earlier published wordings are not removed when new versions are published.
keywords: version history limit, VersionHistoryClass, DefaultVersionHistoryLimit, content.ini, VersionManagement, notices, archived versions, records, cleanupversions.php, public sector
type: help
---
# Raise the version limit for notices

For administrators, on a records manager's request. When an object reaches its version limit, publishing removes the
oldest archived version. The default is 45 per object; classes can have their own limit
([The version history limit](../../records-and-archiving/version-history-limit.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- The class identifier (Setup > Classes) and the limit the records manager wants.
- Raising the limit affects future publishing only; versions already removed do not come back.

## Steps

1. In `settings/override/content.ini.append.php`:

   ```ini
   [VersionManagement]
   VersionHistoryClass[notice]=500
   ```

   (class identifiers are accepted, as are class ids; the shipped file also sets `VersionHistoryClass[1]=75`).
2. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

## Check that it worked

- `./console exp:ini get content.ini/VersionManagement/VersionHistoryClass --allow-root-user` lists your entry.
- On a notice with many versions, publishing no longer removes the oldest archived version until it reaches the new
  limit.

Never lower a limit and run `bin/php/cleanupversions.php` without the records manager's agreement: that removes archived
versions over the new limit.

## Related pages

- [The version history limit](../../records-and-archiving/version-history-limit.md)
- [Versions and content history](../../records-and-archiving/versions-and-content-history.md)
- [Bring back an earlier version of a page](bring-back-an-earlier-version-of-a-page.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
