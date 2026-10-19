---
title: "Lesson 4.1: Installing ezfind and a Solr server"
audience: trainer, admin, developer, hosting
category: training
order: 41
summary: Learners add the ezfind extension to their training installation with Composer, activate it, create its table, start the Solr server the extension ships, and confirm that the site search now runs on ezsolr. 35 minutes.
keywords: search course, ezfind, install, composer require, se7enxweb/ezfind, ActiveExtensions, ezfind_elevate_configuration, Solr, start.jar, ezp-default, Java
type: help
---
# Lesson 4.1: Installing ezfind and a Solr server

This lesson turns a training installation with the built-in engine into one that searches with Solr. Learners follow
the documented steps in order, because each one depends on the one before: the package, the autoloads, the
activation, the table, the Solr server, the caches.

> **Note**
> Do this only on your own training installation. On a live site, adding a search engine is a planned change with a
> rehearsal on a copy, as in [Lesson 1.4](../module-01-how-search-works/choosing-a-search-backend.md).

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 4: Solr and ezfind](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Advanced |
| Audience | Site administrators, developers, hosting staff |
| Prerequisites | [Module 2](../module-02-indexing/README.md); a training installation on MySQL or PostgreSQL; Composer, network access and a Java runtime on the machine |

## Learning objectives

After this lesson, learners can:

- add the extension with `composer require se7enxweb/ezfind:~3.0.7` and regenerate the autoloads;
- activate it in `[ExtensionSettings] ActiveExtensions[]` with `exp:ini add`;
- create the extension's table from its schema file and say what it is for;
- start the Solr server shipped in `extension/ezfind/java` and check that it answers;
- confirm with `exp:ini get` that `[SearchSettings] SearchEngine` is now `ezsolr`.

## Trainer's notes

### How to present it

Source: [Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md#switching-it-on),
[Search backends](../../../hosting/basics/search-backends.md#solr-through-ez-find), and the extension's own
`extension/ezfind/doc/INSTALL.md` once installed.

1. **The package** (2 minutes). ezfind is a Composer package, suggested by the installation's `composer.json`:
   `composer require se7enxweb/ezfind:~3.0.7`, then `php bin/php/ezpgenerateautoloads.php -e`.
2. **Activation** (2 minutes). `ActiveExtensions[]=ezfind` in `settings/override/site.ini.append.php`. Its own
   `site.ini.append.php` then sets `SearchEngine=ezsolr`. The extension's notes ask for it to be listed before
   design extensions whose templates it should override.
3. **The table** (2 minutes). The extension adds one table, `ezfind_elevate_configuration`, used by elevation
   (lesson 4.4). It ships schema files for MySQL, PostgreSQL and Oracle in `extension/ezfind/sql/`; that is why this
   module's lab uses MySQL or PostgreSQL rather than the default SQLite.
4. **The Solr server** (4 minutes). The installed copy carries a Solr in `extension/ezfind/java` with a core named
   `ezp-default`, the core that ezfind's `solr.ini [SolrBase] SearchServerURI` points to
   (`http://localhost:8983/solr/ezp-default`). Its `ezinfo.php` names the Solr version it bundles; read it on your copy.
   The extension's notes start it with `java -Dezfind -jar start.jar` from that folder and ask for a Java runtime;
   rehearse with the Java version of your lab machines before the class.

### Common questions

**Do we have to use the bundled Solr?** No. `SearchServerURI` can point to any Solr with a core configured for
ezfind, and the kernel's `[SolrSettings] Url` lets `exp:solr` check it (lesson 4.2). Which Solr versions ezfind
supports is a question for the extension's own repository.

**What if the site uses SQLite?** The extension ships no SQLite schema file. Use MySQL or PostgreSQL for this lab, or
ask the extension's maintainers.

**Can one Solr serve several installations?** The extension's notes say yes; each installation has its own
installation id in the index.

### Pitfalls

- **Forgetting the autoloads.** Without `ezpgenerateautoloads.php -e` the classes of the extension are not found and
  the search fails with an error. It is the most common mistake in this lesson.
- **Solr not running.** With ezfind active and no Solr, the site search fails. Start Solr before testing, and keep the
  terminal that runs it open.
- **Root-owned files.** If Solr runs as root, its data and log files belong to root. On a training machine that is
  acceptable; on a server, run Solr as its own user.

## Hands-on exercise

Use your own training installation on MySQL or PostgreSQL (for example installed with
`php bin/php/console exp:install --db=mysql`, see [Installing from the command line](../../../../install/07-console-install.md)),
and two terminals in its root. Add `--allow-root-user` only if you work as root.

1. Add the package:

   ```bash
   composer require se7enxweb/ezfind:~3.0.7
   ```

2. Regenerate the extension autoloads:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   ```

3. Activate the extension and clear the INI cache:

   ```bash
   php bin/php/console exp:ini add "site.ini/ExtensionSettings/ActiveExtensions[]" ezfind global
   php bin/php/ezcache.php --clear-tag=ini
   ```

4. Create the table with your database's client, from the file for your engine, for example for MySQL:

   ```bash
   mysql -u <database user> -p <database name> < extension/ezfind/sql/mysql/schema.sql
   ```

5. In the second terminal, start the bundled Solr and leave it running:

   ```bash
   cd extension/ezfind/java && java -Dezfind -jar start.jar
   ```

6. In the first terminal, print the engine in effect and the address ezfind will use:

   ```bash
   php bin/php/console exp:ini get site.ini/SearchSettings/SearchEngine
   php bin/php/console exp:ini get solr.ini/SolrBase/SearchServerURI
   ```

7. Clear all caches so the extension's templates are found:

   ```bash
   php bin/php/ezcache.php --clear-all
   ```

8. Search in the administration for a word from the sample content. Note the result (lesson 4.3 builds the index).

## Expected result

- Step 1 adds `extension/ezfind`; step 2 ends without errors.
- Step 3 writes the line to `settings/override/site.ini.append.php` (`exp:ini` names the file).
- Step 5 prints Solr's start-up log and keeps running.
- Step 6 prints `ezsolr` and `http://localhost:8983/solr/ezp-default`.
- Step 8 runs without an error but finds little or nothing: the Solr index is still empty.
- The trainer checks step 6 on every machine before moving on.

## Quiz

1. Put these steps in order: activate the extension, create the table, `composer require`, regenerate autoloads,
   start Solr.
2. What does ezfind change in `[SearchSettings]` when it is active?
3. What is the table `ezfind_elevate_configuration` for?
   A. the search index B. elevation configurations C. search statistics D. the delayed-indexing queue
4. Why does this lab use MySQL or PostgreSQL?
5. A search fails right after activating ezfind. Name two likely causes.

<details>
<summary>Answers</summary>

1. `composer require`, regenerate autoloads, activate, create the table, start Solr (the table and Solr can swap).
2. `SearchEngine=ezsolr` (and `SearchViewHandling=template`, `AllowEmptySearch=enabled`, `EnableWildcard=true`).
3. **B.**
4. The extension ships schema files for MySQL, PostgreSQL and Oracle, not for SQLite.
5. Any two of: autoloads not regenerated; Solr not running; caches not cleared; `SearchServerURI` pointing elsewhere.

</details>

## Further reading

- [Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md): the switching-on
  steps for website creators.
- [Composer and Git on servers](../../../hosting/basics/composer-and-git-on-servers.md): adding packages on a server.
- [Chapter 3: Getting the code](../../../../install/03-getting-the-code.md): extensions as Composer packages.

## Related pages

- [Module 4: Solr and ezfind](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
