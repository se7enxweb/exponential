---
title: "Lesson 7.3: A Valkey or Redis cache"
audience: trainer, hosting, admin
category: training
order: 73
summary: Learners explain what the sevenx_valkey_cache extension moves into Valkey or Redis, set it up on a training machine with a unique key prefix, check the keys after two page views, and plan isolation for several customers. 40 minutes.
keywords: hosting and operations, Valkey, Redis, sevenx_valkey_cache, valkeycache.ini, KeyPrefix, InstallationHash, content view cache, INI cache, template blocks, redis-cli, ezvalkeycacheclear, multi-tenant
type: help
---
# Lesson 7.3: A Valkey or Redis cache

Hosts that already run Redis or Valkey can offer it to Exponential sites: for speed, and to make caches safe for
sites served from several web nodes. The `sevenx_valkey_cache` extension moves three caches there. This lesson
covers what it stores, how to set it up, how to check it, and how several customers can share a server safely.

> **Note**
> The exercise needs a Valkey or Redis server and the PHP `redis` extension on the learner's own virtual machine. If
> the class has no such machine, run steps 1 to 3 and do the rest as a demonstration.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 7: Performance and scaling](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Advanced |
| Audience | System administrators |
| Prerequisites | [Lesson 7.2](caching-layers-proxies-and-cdn.md); Composer on the training machine |

## Learning objectives

After this lesson, learners can:

- name the three caches the extension stores (template blocks, the content view cache, the compiled INI cache) and
  the ones it does not;
- install and configure the extension with a unique `KeyPrefix`;
- check that keys appear after two page views;
- explain `InstallationHash` for one site on several nodes and for several sites on one server;
- explain why a key prefix separates keys but is not access control.

## Trainer's notes

### How to present it

Source: [Valkey and Redis](../../../hosting/performance/valkey-and-redis.md) and
[sevenx_valkey_cache](../../../../features/6.0/extensions/sevenx_valkey_cache.md).

1. **What it stores** (3 minutes). `{valkey-block ...}` template blocks, the content view cache and the compiled INI
   cache. Entries have expiry, generation locks against stampedes and node and subtree indexes, so publishing purges
   exactly what it affects. Without the extension, or on a miss, the kernel uses files as before.
2. **Requirements** (2 minutes). Exponential 6.0.15 or later; the PHP `redis` extension in every PHP that serves the
   site and in the command-line PHP; a reachable server.
3. **Setup** (4 minutes). `composer require se7enxweb/sevenx_valkey_cache`, `ActiveExtensions[]`, the settings file
   `settings/override/valkeycache.ini.append.php`, autoloads, caches, Velocity restart.
4. **Several customers** (4 minutes). A unique `KeyPrefix` per installation; `InstallationHash=auto` unless one site
   runs on several nodes (then a fixed string); `Persistent=disabled` where pools are shared. A shared server is shared
   memory: every site can read every key unless the server's own access control prevents it.
5. **What it does not cover** (2 minutes). Sessions, Velocity's response cache, the SQL query cache, compiled
   templates and image aliases stay where they are.

### Common questions

**What happens when the Valkey server is down?** A server outage turns into cache misses; test the behavior on staging.

**How do I clear everything?** `bin/php/ezvalkeycacheclear.php` clears all caches through the same handlers and
flushes the extension's keys; it is the fallback when **Clear all caches** cannot clean up after the extension is
removed.

**How long is a view cache entry kept?** 3600 seconds, a value set in the kernel's content view, not in an INI file.

### Pitfalls

- **The `redis` extension only in the web PHP.** Cron jobs and cache clears from the command line then miss it.
- **Two learners on one server with the same prefix.** Assign a prefix per learner before the exercise.
- **Flushing the whole server.** Use the key pattern with the prefix; never flush a server others use.

## Hands-on exercise

Use your own virtual machine with a Valkey or Redis server on `127.0.0.1:6379` and the PHP `redis` extension, and a
training installation you may change. Your prefix is `training-<your name>:`.

1. Check the server: `redis-cli ping` (or `valkey-cli ping`).
2. Check the PHP extension for the web and the command line: `php -m | grep -i redis`, and **Setup**, **System
   information** in the administration.
3. Add the extension: `composer require se7enxweb/sevenx_valkey_cache`.
4. Activate it in `settings/override/site.ini.append.php` under `[ExtensionSettings]` with
   `ActiveExtensions[]=sevenx_valkey_cache`.
5. Create `settings/override/valkeycache.ini.append.php` with the `[ValkeyCacheSettings]` block from the hosting page,
   and set `KeyPrefix=training-<your name>:`.
6. Regenerate autoloads, clear the caches and restart:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   php bin/php/console exp:velocity restart
   ```

7. Request a content page twice, without signing in.
8. List your keys: `redis-cli --scan --pattern 'training-<your name>:*' | head`.
9. Edit and publish the page in the administration, request it again, and list the keys once more.
10. To finish, remove the extension line and the settings file (or keep them if the next lesson uses them), clear the
    caches and restart.

## Expected result

- Step 1 prints `PONG`; step 2 lists `redis` for both PHPs.
- Step 8 lists keys that start with your prefix.
- Step 9: entries for the published page were purged and written again.
- After step 10 the site runs on file caches again.
- The trainer checks one learner's key list on the projector.

## Quiz

1. Which three caches does the extension store?
2. Which PHP needs the `redis` extension?
   A. only PHP-FPM B. only Velocity's PHP C. every PHP that serves the site and the command-line PHP D. none
3. One site runs on three web nodes. What do you set for `InstallationHash`?
4. Does a unique `KeyPrefix` stop one customer's code from reading another's keys?
5. What happens to page views when the Valkey server stops?

<details>
<summary>Answers</summary>

1. Template blocks, the content view cache and the compiled INI cache.
2. **C.** ([Valkey and Redis](../../../hosting/performance/valkey-and-redis.md#requirements))
3. A fixed string, so every node reads the same entries.
4. No. A prefix separates keys; access control between customers needs the server's own features or separate servers.
5. They become cache misses; the kernel falls back to rendering.

</details>

## Further reading

- [Valkey and Redis](../../../hosting/performance/valkey-and-redis.md)
- [sevenx_valkey_cache](../../../../features/6.0/extensions/sevenx_valkey_cache.md)
- [Multi-tenant isolation](../../../hosting/security/multi-tenant-isolation.md)
- [Caches](../../../../guides/caches.md): the administration's view.

## Related pages

- [Module 7: Performance and scaling](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
