---
title: "Lesson 3.3: Logs per site"
audience: trainer, admin, developer
category: training
order: 33
summary: Learners send the logs of the second site into a log directory of its own with UseGlobalLogDir and LogDir, prove it with a 404 request, and plan LogVarDir for many sites. 45 minutes.
keywords: multisite and site packages, logs per site, UseGlobalLogDir, LogDir, LogVarDir, VarDir, 404.log, error.log, eZLog, eZSys::logDirectory, audit records, log viewer
type: help
---
# Lesson 3.3: Logs per site

When ten sites write into one `var/log/error.log`, finding the error of one site takes longer than fixing it. Three
settings of `site.ini [FileSettings]` give each site its own log directory, on the same disk or on another one. This
lesson moves the logs of the second site, proves it with a request that writes a log line, and shows the setting for
a whole tree of sites.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 3: Settings, logs and caches per site](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Advanced |
| Audience | Administrators and developers who operate several sites |
| Prerequisites | [Lesson 3.1](a-site-in-its-own-extension.md): the siteaccess settings of `site_b` live in `site_clubb` |

## Learning objectives

After this lesson, learners can:

- explain `LogDir`, `UseGlobalLogDir` and `LogVarDir` and their defaults;
- give one siteaccess a log directory of its own and prove where its logs go;
- plan the logs of many sites with `LogVarDir` in one line;
- name what stays where it is: `storage.log`, the audit records and messages written before the siteaccess is known.

## Trainer's notes

### How to present it

Source: [Logs, INI cache and expiry timestamps per site](../../../../features/6.0/multi-site-log-and-cache-paths.md).

1. **The settings** (6 minutes), all in `site.ini [FileSettings]`, for each siteaccess of the site:

   | Key | Default | Meaning |
   |---|---|---|
   | `LogDir` | `log` | the log directory inside `VarDir`; an absolute path is used as it is |
   | `UseGlobalLogDir` | `enabled` | `disabled` writes the debug logs and the default logs of `eZLog` into the site's log directory instead of `var/log` |
   | `LogVarDir` | empty | a tree for the logs of every site: the first directory of `VarDir` is replaced, `var/example` becomes `var_log/example`, so the log directory is `var_log/example/log` |

   Every setting keeps the behavior of before when it is not set.
2. **One line for many sites** (3 minutes). With a `VarDir` per site, `UseGlobalLogDir=disabled` and
   `LogVarDir=var_log` in the global override give every site its logs in `var_log/<site>/log`.
3. **What stays** (3 minutes). Messages written before the request is matched to a siteaccess still go to `var/log`.
   Audit records stay inside `VarDir` (`audit.ini [AuditSettings] LogDir`), because they belong with the site's
   storage, are kept for years and are checked against their hash chain. The setup log stays in `var/log`.
4. **Velocity and scripts** (3 minutes). The paths are derived again whenever the siteaccess changes, so a Velocity
   worker that serves two sites one request after another writes each site's errors into its own directory, and
   `php bin/php/ezcache.php -s <siteaccess>` and the cronjobs of a site write into that site's directories.

### Common questions

**Where do 404 answers go?** Into `404.log` in the log directory (`var/log`, or the site's directory with
`UseGlobalLogDir=disabled`), when `site.ini [RequestLogSettings] NotFoundLog` is `enabled`, the default.

**My extension writes its own log file. Does it move?** If it logs through `eZLog` or builds the path with
`eZSys::logDirectory()`, yes. One that builds the path from `VarDir` and `LogDir` itself has to be changed to use
`eZSys::logDirectory()`.

**Does System information show the site's log directory?** **Setup** > **System information**, card **Storage**,
names the log directory of the siteaccess it runs in (the administration), not those of the other sites.

**Can logs go to another disk?** Yes: an absolute `LogDir`, or a `LogVarDir` that is an absolute path.

### Pitfalls

- **Same `VarDir` for both sites.** In the lab both sites share one `VarDir`, so `LogDir=log` would be the same
  directory for both. The exercise gives `site_b` a `LogDir` of another name.
- **Permissions.** The web server (and Velocity) must be able to create and write the new directory.
- **Looking too early.** A log file appears only when something is written. Use the 404 request of the exercise.

## Hands-on exercise

Use your training installation from lesson 3.2. Replace `http://127.0.0.1:8087` with your lab's address.

1. Read the paths in effect for `site_b`:

   ```bash
   php bin/php/console exp:ini where site.ini/FileSettings/VarDir site_b
   php bin/php/console exp:ini get site.ini/FileSettings/LogDir -s site_b
   php bin/php/console exp:ini get site.ini/RequestLogSettings/NotFoundLog -s site_b
   ```

2. Give `site_b` its own log directory, in the extension's siteaccess settings:

   ```bash
   php bin/php/console exp:ini set site.ini/FileSettings/UseGlobalLogDir disabled extension:site_clubb:siteaccess:site_b
   php bin/php/console exp:ini set site.ini/FileSettings/LogDir log_site_b extension:site_clubb:siteaccess:site_b
   ```

3. Restart Velocity if it serves the lab.
4. Ask `site_b` for a page that does not exist, then the main site:

   ```bash
   curl -sI http://127.0.0.1:8087/site_b/no-such-page | head -1
   curl -sI http://127.0.0.1:8087/site/no-such-page | head -1
   ```

5. Find the new directory below the `VarDir` from step 1 (for example `ls var/site/log_site_b/`) and read the last
   line of its `404.log`.
6. Read the last line of `var/log/404.log` (or of the main site's log directory) and compare the `siteaccess=` fields.
7. Ask `site_b_ger` for a missing page too. Where does its line go, and why?
8. Write down the one-line setup for a server with fifty sites, each with `VarDir=var/<site>`.

## Expected result

- Step 4 answers `404` twice.
- Step 5: `log_site_b/404.log` exists with a line for `/site_b/no-such-page` and `siteaccess=site_b`.
- Step 6: the main site's line is in the global log directory, with `siteaccess=site`.
- Step 7: the line of `site_b_ger` goes to the global directory, because the setting was made for `site_b` only (it
  would have to go into the extension's `override/` from lesson 3.2 to cover both).
- Step 8: `UseGlobalLogDir=disabled` and `LogVarDir=var_log` in `[FileSettings]` of the global override.
- The trainer checks step 5 on one lab on the projector.

## Quiz

1. What does `UseGlobalLogDir=disabled` change?
2. `VarDir=var/shop` and `LogVarDir=var_log`. Where is the log directory?
   A. `var/shop/log` B. `var_log/shop/log` C. `var_log/log` D. `var/log`
3. Which records never move with these settings, and why?
4. An extension writes its log with a path built from `VarDir`. What should it use instead?
5. Which file receives the 404 answers of a site with its own log directory?

<details>
<summary>Answers</summary>

1. The debug logs and the default logs of `eZLog` go to the site's log directory instead of `var/log`.
2. **B.** The first directory of `VarDir` is replaced by the tree, then `LogDir` (`log`) is added.
3. The audit records: they belong with the site's storage, are kept for years and are checked against their hash
   chain ([Logs, INI cache and expiry timestamps per site](../../../../features/6.0/multi-site-log-and-cache-paths.md#what-reads-the-logs)).
4. `eZSys::logDirectory()`, or `eZLog::write()` without a directory.
5. `404.log` in that site's log directory, when `[RequestLogSettings] NotFoundLog` is `enabled`
   ([Request logs](../../../../features/6.0/request-logs.md)).

</details>

## Further reading

- [Logs, INI cache and expiry timestamps per site](../../../../features/6.0/multi-site-log-and-cache-paths.md).
- [Request logs](../../../../features/6.0/request-logs.md): `404.log` and `security.log`.
- [The log viewer](../../../../features/6.0/log-viewer.md): reading the logs in **Setup** > **Log files**.
- [Log viewer and rotation](../../../hosting/operations/log-viewer-and-rotation.md): the host's view.

## Related pages

- [Module 3: Settings, logs and caches per site](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
