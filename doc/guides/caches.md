# Caches: what each one holds and how to clear it

This guide explains the administration page **Setup > Caches** (`/setup/cache`): the caches of an installation in
groups, what each holds, how big it is, when it was last cleared, what clearing it reaches on Apache with PHP-FPM
and on Exponential Velocity, and the commands that do the same from a shell.

It is written for administrators. Everything below was checked against the code (`kernel/classes/expcachecatalogue.php`,
`kernel/classes/expcachemanager.php`, `kernel/private/classes/views/setup/cache.php`) and tried on the
demonstration server (alpha.se7enx.com) on 6 October 2026, clearing only the template block cache and the image
aliases.

[Guides](README.md) · Related guides: [System information](system-information.md),
[Operating a site](operating-a-site.md), [Preloading caches](preloading-caches.md), [Deploying](deploying.md)

## In short

- **Setup > Caches** needs the `setup/managecache` policy. Every button posts the form with its form token; a post
  without it is refused.
- The page lists every cache in six groups: **Content and views**, **Templates**, **INI and settings**, **Images**,
  **Pages, queries and the server**, **Other** (caches of extensions that fit no other group).
- For each cache: its name and what it holds, its id and tags, its directory (relative to the installation), its
  size and number of files (**Measure sizes**, `/setup/cache/(sizes)/1`, at most three seconds), when and by whom it was last
  cleared where the installation records it, and badges: **restart Velocity after clearing**, **also in Velocity's
  response cache**, **disabled in the settings**.
- Clear **selected** caches (tick them, **Clear selected**, confirm the list), a **group** (**Clear this group…**),
  by **tag** (**Clear content / template / INI caches…**) or **all** (**Clear all caches…**). Every clear names
  what goes first, in place, and ends with a notice: what was cleared, how many milliseconds it took, what to do
  next, and the command.
- **Search** narrows the list by name, id, tag or directory; the **Groups** buttons show one group.
- The cache files are in `var/`, which Apache with PHP-FPM and Velocity share: clearing a cache here reaches both.
  Two things do not: Velocity keeps settings in memory (restart it after clearing the INI caches) and answers pages
  from its own response cache for a few seconds (clear it in the last group or with `./console exp:velocity cache clear`).

## 1. The overview

The figures at the top count the caches, the enabled ones and the groups, show the total size and files once
sizes are measured, and the latest time content views, template blocks or user information were expired.

## 2. The groups

| Group | Caches (ids) |
|---|---|
| Content and views | `content`, `classid`, `sortkey`, `urlalias`, `rss_cache`, `user_info_cache`, `content_tree_menu`, `state_limitations`, `content_language`, `rest` |
| Templates | `template`, `template-block`, `template-override`, `texttoimage`, `design_base`, `ezjscore-packer` |
| INI and settings | `global_ini`, `ini`, `codepage`, `chartrans`, `active_extensions`, `translation`, `sslzones`, `rest-routes` |
| Images | `imagealias` |
| Pages, queries and the server | `exphttpcache`, `querycache`, and the server's own caches below |
| Other | caches of extensions, grouped here unless their tag is content, template, ini, i18n or image |

**Pages, queries and the server** also holds, as rows with their own buttons:

- **Whole pages (HTTP cache)**: Clear HTTP cache, Remove dead entries, Reset counters;
- **Database query results (SQL query cache)**: Clear query cache, Reset the counters;
- **SQL profile of every request**: on or off;
- **Velocity response cache**: its files and size, when it was last cleared, and **Clear Velocity's response cache**;
- **OPcache** and **APCu** of the server process that answered the page: Reset OPcache, Empty APCu.

The HTTP cache, query cache and SQL profile buttons are the same as on Setup > System information: both pages
post the same fields to `expCacheManager::sharedActionFromPost()`.

## 3. Last cleared

The column says when each cache was last cleared and, from the audit trail, by whom: "just now",
"12 minutes ago", "3 hours ago", else the date, with "by admin" or "from a shell, os:root" below.

- **The audit trail** (`system.cache.clear`) records every clear: through this page, Setup > System information,
  `./console exp:cache` and `bin/php/ezcache.php` (one record per run, so also the clears of
  `exp:velocity deploy`). A shell is named by its operating system user (`os:root`, `os:alpha`). A run that clears
  nothing (`--list-ids`, `--list-tags`, an unknown id) writes no record; if the record cannot be written, the
  caches are cleared anyway and a warning goes to stderr.
- The page reads it once per view: one query of the audit index (the newest 200 clears) and the end of the newest
  system file of each audit directory (at most 3000 lines each), for clears the indexer has not reached yet.
  Records are split between `var/log/audit` (commands without a siteaccess) and `var/<site>/log/audit`; both are
  read. Who cleared is shown to users who may read the system channel of the audit (`audit/read`).
- The kernel also records an expiry time for content views, template blocks, user information, translations,
  image aliases and the content tree menu. The newer of the two wins; an audit record up to five seconds before
  the expiry is the same clear and names who made it. The page that shows the result of a clear already names you,
  before your own record is written.
- A cache cleared neither way says "not recorded".

## 4. Velocity

- **INI and settings** (`global_ini`, `ini`, `active_extensions`, `sslzones`): a running Velocity has read the
  settings into memory. After clearing them, run `./console exp:velocity restart --allow-root-user`.
- **Content, templates and the HTTP cache**: Velocity's response cache can still answer a page made before the
  clear, for its lifetime (a few seconds on alpha). Clear it with the button or `./console exp:velocity cache clear`.
- **OPcache and APCu** belong to the server process that answered; the other server keeps its own.

## 5. From a shell

```
php bin/php/ezcache.php --clear-id=template-block,content --allow-root-user   one cache or several, by id
php bin/php/ezcache.php --clear-tag=template --allow-root-user                 by tag
php bin/php/ezcache.php --clear-all --allow-root-user                          everything
./console exp:cache clear --id=content --dry-run --allow-root-user             what would be cleared, with sizes
./console exp:velocity cache clear                                            Velocity's response cache
./console exp:velocity restart --allow-root-user                               Velocity, after settings changed
```

Each group and each result notice shows the command for exactly the caches it clears.

## 6. Usual questions

**A page still shows the old version after clearing.** Clear Velocity's response cache, or wait a few seconds. If
it is a settings change, restart Velocity.

**A cache is greyed out.** It is disabled in the settings (for example the SSL zones cache), so there is nothing to
clear.

**Measure sizes says "≥".** The three-second budget ran out before every file was counted.
