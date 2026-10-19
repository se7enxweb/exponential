---
title: Clear caches after a settings change
audience: government, admin, hosting
category: public-sector
order: 70
summary: Step by step - make a changed INI setting take effect - clear the INI cache with the right tag, reload PHP-FPM or redeploy Velocity, then clear the page caches, and check the effective value with exp:ini.
keywords: settings change, INI cache, clear-tag=ini, exp:ini get, exp:ini where, PHP-FPM reload, Velocity deploy, cache order, settings not applied
type: help
---
# Clear caches after a settings change

This task is for administrators after a change to an `.ini` or `.ini.append.php` file. At the end the new value is in
effect everywhere. Settings are cached, and long-running PHP processes keep what they read, so the order matters
([Operating a site, section 2](../../../../guides/operating-a-site.md#2-clear-the-right-cache)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Pitfalls](#pitfalls)
5. [Related pages](#related-pages)

## Before you start

- The change is in version control and deployed (or, on a site without ManagedByDeployment, in the file).
- Shell access.

## Steps

1. Clear the INI caches (the tag clears every INI cache, including the global one):

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ```

2. Make the web processes read it again. With Velocity, one command does every step in order:

   ```bash
   ./console exp:velocity deploy --dry-run --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

   Without Velocity: reload the PHP-FPM service that serves the site (`systemctl reload <service>`; on servers with
   several PHP versions it is not always `php-fpm`).
3. If the setting changes what pages show, clear the content cache and the caches in front, **after** the reload:

   ```bash
   php bin/php/ezcache.php --clear-tag=content --allow-root-user
   ./console exp:velocity cache clear --allow-root-user
   ```

## Check it worked

```bash
./console exp:ini get 'site.ini/<Block>/<Setting>' --allow-root-user
./console exp:ini where site.ini/<Block>/<Setting> --allow-root-user
```

`where` shows which file the value comes from; a value from `settings/override` wins over a siteaccess. Then check the
behavior in the browser.

## Pitfalls

- `--clear-id=ini` misses the global INI cache; use `--clear-tag=ini`.
- Velocity's workers keep classes and settings from their start until a restart or deploy.
- A page rendered between the cache clear and the reload is cached with the old setting: clear page caches last.

Change record: setting, old and new value, date; the audit trail records settings written through `exp:ini` and the
debug bar.

## Related pages
- [Clear caches after a template change](clear-caches-after-a-template-change.md)
- [Deploy a PHP change](deploy-a-php-change.md)
- [The INI command](../../../../features/6.0/exp-ini-command.md)
- [Caches guide](../../../../guides/caches.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
