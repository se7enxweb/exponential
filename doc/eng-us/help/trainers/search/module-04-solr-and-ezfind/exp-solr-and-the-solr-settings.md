---
title: "Lesson 4.2: exp:solr and the Solr settings"
audience: trainer, admin, hosting
category: training
order: 42
summary: Learners tell the kernel's [SolrSettings] block from ezfind's [SolrBase] block in solr.ini, configure a running Solr with Url, and check it with exp:solr status and ping, as a monitoring check would. 30 minutes.
keywords: search course, exp:solr, solr.ini, SolrSettings, SolrBase, Enabled, Url, BinaryPath, Host, Port, SearchServerURI, ConnectionTimeout, status, ping, monitoring
type: help
---
# Lesson 4.2: exp:solr and the Solr settings

Two blocks of `solr.ini` talk about Solr, and learners mix them up. The kernel's `[SolrSettings]` tells the console
command `exp:solr` where Solr is and whether it may start it. The extension's `[SolrBase]` tells ezfind where to send
index and search requests. This lesson separates the two and makes `exp:solr` part of the learner's routine.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 4: Solr and ezfind](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Advanced |
| Audience | Site administrators and hosting staff |
| Prerequisites | [Lesson 4.1](installing-ezfind-and-solr.md): ezfind active and the bundled Solr running |

## Learning objectives

After this lesson, learners can:

- explain the difference between `solr.ini [SolrSettings]` (kernel, `exp:solr`) and `solr.ini [SolrBase]` (ezfind);
- configure `[SolrSettings] Enabled=true` with `Url` for a running Solr, in `settings/override/solr.ini.append.php`;
- run `exp:solr status` and `exp:solr ping` and read their answers;
- explain what `start`, `stop` and `restart` do with `BinaryPath` and with `Url`.

## Trainer's notes

### How to present it

Source: [Server control commands](../../../../features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server)
and [Search backends](../../../hosting/basics/search-backends.md#the-expsolr-command).

1. **Two blocks** (4 minutes). Write both on the board:
   - `[SolrSettings]` (kernel `settings/solr.ini`): `Enabled` (`false` as shipped), `Url` (a running Solr; then it
     is treated as managed elsewhere), `BinaryPath` (a local Solr control binary, used when `Url` is empty), `Host`
     and `Port` (`127.0.0.1`, `8983`), `DataDir`, `RunDir` and `LogDir` (`var/vc/solr/run`, `var/vc/solr/log`),
     `StartTimeout` (`60`).
   - `[SolrBase]` (the extension's `solr.ini`): `SearchServerURI` (`http://localhost:8983/solr/ezp-default`),
     `ConnectionTimeout` (`10`), `ProcessTimeout` (`20`), `ProcessMaxRetries` (`2`), optional authentication, and
     `Shards[]` for several cores.
   Both must point to the same Solr; only `[SolrBase]` decides where searches go.
2. **The verbs** (3 minutes). `status`: configured or not, and whether it answers. `ping`: is it alive.
   `start`, `stop`, `restart`: run Solr's own control for a local instance with `BinaryPath`; with `Url` they report
   that Solr is managed elsewhere. Until Solr is configured, every verb says so and `start` exits non-zero, so a
   monitoring check never mistakes "not configured" for "running".
3. **Monitoring** (3 minutes). The hosting guide's tip: add `exp:solr status` to monitoring for every site with Solr.
   The command's exit status tells the states apart: `0` done or running, `1` configured but failed or not answering,
   `3` not installed or not enabled, `4` managed elsewhere (`start` or `stop` with `Url` set). `--json` prints the
   result as JSON. `ping` asks Solr's `/solr/admin/info/system` below the base URL.

### Common questions

**Why does exp:solr say "not configured" while ezfind searches fine?** `[SolrSettings] Enabled` is still `false`.
ezfind does not need it; `exp:solr` does.

**Can exp:solr start the bundled Solr?** `start` runs a Solr control binary named in `BinaryPath`. The bundled Solr
of lesson 4.1 is started with `java -jar start.jar`; for it, set `Url` and let `exp:solr` check it.

**Where do I put a Solr password?** ezfind's `[SolrBase]` has `SearchServerAuthentication`,
`SearchServerAuthenticationMethod` (basic) and `SearchServerUserPass`. Keep such values in `settings/override`,
never in a repository, and never in course material.

### Pitfalls

- **Setting Url with a core path.** The kernel's `Url` is the base URL with scheme and port, as in the
  documentation's example `http://127.0.0.1:8983`; the core belongs in ezfind's `SearchServerURI`. Rehearse
  `ping` against your lab's Solr before the class.
- **Editing settings/solr.ini.** Overrides go to `settings/override/solr.ini.append.php`; `exp:ini ... global` writes
  there.

## Hands-on exercise

Use your training installation from lesson 4.1, with the bundled Solr still running in its own terminal. Add
`--allow-root-user` only if you work as root.

1. Run `php bin/php/console exp:solr status` and read the **reason** line.
2. Print every file that sets the kernel switch and ezfind's address:

   ```bash
   php bin/php/console exp:ini where solr.ini/SolrSettings/Enabled
   php bin/php/console exp:ini where solr.ini/SolrBase/SearchServerURI
   ```

3. Configure the kernel block for the running Solr:

   ```bash
   php bin/php/console exp:ini set solr.ini/SolrSettings/Enabled true global
   php bin/php/console exp:ini set solr.ini/SolrSettings/Url http://127.0.0.1:8983 global
   php bin/php/ezcache.php --clear-tag=ini
   ```

4. Run `exp:solr status` and `exp:solr ping` again.
5. Run `php bin/php/console exp:solr stop`. Read what it says, and check in the other terminal that Solr is still
   running.
6. Stop Solr in its terminal with Ctrl+C. Run `exp:solr status` and `ping`, and note the exit status (`echo $?`).
7. Start Solr again as in lesson 4.1 and run `exp:solr ping` once more.
8. Write the line you would add to a monitoring system for this site.

## Expected result

- Step 1 says Solr is not configured, because `[SolrSettings] Enabled is not true`.
- Step 2 lists the kernel's and the extension's `solr.ini`, and after step 3 the override file.
- Step 4 reports a configured Solr that answers.
- Step 5 reports that Solr is managed elsewhere (a `Url` is set), stops nothing, and exits with `4`.
- Step 6 reports that Solr does not answer, with exit status `1`; step 7 answers again.
- The trainer checks the monitoring line of step 8: `exp:solr status` or `ping`, run as the site's user.

## Quiz

1. Which block decides where ezfind sends its searches?
   A. `[SolrSettings]` B. `[SolrBase]` C. `[SearchSettings]` D. `[SiteSettings]`
2. What do `start` and `stop` do when `[SolrSettings] Url` is set?
3. What is the shipped value of `[SolrSettings] Enabled`?
4. Why does `exp:solr start` exit non-zero on an installation without Solr?
5. Where do overrides of `solr.ini` belong?

<details>
<summary>Answers</summary>

1. **B.** `SearchServerURI` in ezfind's `[SolrBase]`.
2. They report that Solr is managed elsewhere and do nothing
   ([Server control commands](../../../../features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server)).
3. `false`.
4. So that a monitoring check never mistakes "not configured" for "running".
5. In `settings/override/solr.ini.append.php` (or a siteaccess's settings), never in `settings/solr.ini`.

</details>

## Further reading

- [Server control commands](../../../../features/6.0/web-server-and-solr-commands.md): the `exp:solr` reference and
  the `[SolrSettings]` table.
- [Health checks and uptime](../../../hosting/operations/health-checks-and-uptime.md): adding checks to monitoring.
- [Search backends](../../../hosting/basics/search-backends.md#the-expsolr-command): the command from a host's point
  of view.

## Related pages

- [Module 4: Solr and ezfind](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
