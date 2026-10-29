---
title: How do I change how many versions of an object are kept?
audience: admin
category: manual
order: 640
summary: The limit is content.ini [VersionManagement] DefaultVersionHistoryLimit, with VersionHistoryClass[] for single classes; when an object reaches it, its oldest archived version is removed, and bin/php/cleanupversions.php trims old objects after you lower it.
keywords: versions, version history, version limit, DefaultVersionHistoryLimit, VersionHistoryClass, archived versions, cleanupversions, content.ini, VersionManagement
context: content/history
type: help
---
# How do I change how many versions of an object are kept?

Every edit creates a new version, and the version it replaces is archived. To keep the database from growing
without end, each object keeps at most a set number of versions. When an edit would go over the limit, the oldest
archived version of that object is removed.

## Who can do this

An administrator with **Setup** > **Ini settings** (a role with the module `settings`), or someone who may change the
site's settings files. Running the cleanup script needs shell access.

## Steps

1. Look up the current values: **Setup** > **Ini settings**, file `content.ini`, block `[VersionManagement]`.
   `DefaultVersionHistoryLimit` applies to all classes; each `VersionHistoryClass[]` line sets the limit of one class,
   by class ID or identifier. A new installation keeps 45 versions, and 75 for folders.
2. Change the values with **Edit** on that page and save them for every siteaccess, or write them into an override
   file, for example `settings/override/content.ini.append.php`:

   ```ini
   [VersionManagement]
   DefaultVersionHistoryLimit=20
   VersionHistoryClass[article]=50
   ```

3. Clear the INI caches ([Which cache do I clear, and how?](which-cache-do-i-clear.md)).
4. If you lowered a limit, remove the versions over it from existing objects:

   ```bash
   php bin/php/cleanupversions.php --apply
   ```

   The script waits 10 seconds before it starts; `-n` skips the wait. It removes only archived versions.

## What can go wrong

- **"Version history limit has been exceeded and no archived version can be removed by the system."** The object
  holds as many drafts and pending versions as the limit allows, so nothing can be removed. Publish or remove some
  drafts of the object, or raise its limit.
- **A limit below 2.** The system uses 2 instead and writes a warning to the debug output.
- **An old version you wanted is gone.** Removing versions cannot be undone. Raise the limit before you need the
  history, not afterwards.
- **The setting has no effect.** It was written into a file of one siteaccess only, or the INI cache was not cleared.

## Related pages

- [How do I go back to an earlier version?](how-do-i-go-back-to-an-earlier-version.md)
- [Content history](../../guides/content-history.md)
- [Versions, drafts and publishing](../cms-developers/versions-drafts-and-publishing.md)
- [Settings page](../../guides/settings-page.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
