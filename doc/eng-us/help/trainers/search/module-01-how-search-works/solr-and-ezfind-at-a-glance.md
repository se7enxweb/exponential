---
title: "Lesson 1.3: Solr and ezfind at a glance"
audience: trainer, admin, developer
category: training
order: 13
summary: Learners describe what the optional ezfind extension and a Solr server add to an installation, which parts are involved, and how an installation shows which engine it uses. 25 minutes.
keywords: search course, Solr, ezfind, eZ Find, ezsolr, solr.ini, exp:solr, relevance, facets, optional search server
type: help
---
# Lesson 1.3: Solr and ezfind at a glance

The ezfind extension connects Exponential to Apache Solr, a separate search server. It is optional: nothing changes
until it is installed and switched on. This lesson gives learners the overview; Module 4 goes into the practice.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 1: How search works](README.md) |
| Duration | 25 minutes (15 presenting, 10 exercise) |
| Level | Intermediate |
| Audience | Site administrators, developers, hosting staff |
| Prerequisites | [Lesson 1.2](the-built-in-search-engine.md) |

## Learning objectives

After this lesson, learners can:

- name the three parts of a Solr setup: the `ezfind` extension, the kernel's `settings/solr.ini` and the console
  command `exp:solr`;
- explain that ezfind switches `[SearchSettings] SearchEngine` to `ezsolr` when it is active;
- list what Solr adds over the built-in engine: relevance ranking, boosting, facets, highlighting, spell checking,
  elevation;
- check with `exp:solr status` whether an installation has a Solr server configured.

## Trainer's notes

### How to present it

Source: [Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md),
[Search backends](../../../hosting/basics/search-backends.md) and
[Server control commands](../../../../features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server).

1. **The parts** (5 minutes). Draw three boxes:
   - `extension/ezfind`: the extension, a Composer package (`se7enxweb/ezfind`, suggested by the installation's
     `composer.json`). Its `settings/site.ini.append.php` sets `SearchEngine=ezsolr`,
     `SearchViewHandling=template`, `AllowEmptySearch=enabled` and `EnableWildcard=true`. Its `ezfind.ini` tunes
     relevance and features; its own `solr.ini` block `[SolrBase]` holds `SearchServerURI`, the address ezfind sends
     queries to.
   - `settings/solr.ini [SolrSettings]`: the kernel's control of a Solr server, `Enabled=false` until switched on,
     with `Url` for a running Solr or `BinaryPath` for a local instance.
   - `exp:solr`: `status`, `ping`, and `start`, `stop`, `restart` for a local instance.
2. **What it adds** (5 minutes). Use the table of
   [When Solr is worth it](../../../creators/website-creators/search-with-ezfind-and-solr.md#when-solr-is-worth-it):
   relevance ranking and boosting, facets, scale for very many objects, tag clouds from search with eztags. From the
   extension's `ezfind.ini`: highlighting of the search words in results (`[HighLighting]`), spell checking
   (`[SpellCheck]`), autocomplete (`[AutoCompleteSettings]`) and, in the administration, **Elevate**: pinning an
   object to the top of the results for a given search query.
3. **What it costs** (5 minutes). Solr is a Java server to install, run, monitor and back up; its memory and disk are
   planned separately from PHP. The repository does not state which Solr versions ezfind needs; take that from the
   extension's own repository before promising a version.

### Common questions

**Does the content finder use Solr?** No. It looks up the database directly, whatever the engine.

**Can one Solr serve several sites?** The extension's installation notes say one Solr instance can serve several
installations; each installation is told where Solr is by its own settings.

**Do editors notice the switch?** The admin search and the online editor's search dialogs use Solr when it is active,
so the results and their order can change
([The order of search results in the editor's dialogs](../../../../guides/content-model-and-editing.md#the-order-of-search-results-in-the-editors-dialogs)).

### Pitfalls

- **"Enabled" in one file is not enough.** `[SolrSettings] Enabled=true` makes `exp:solr` manage or reach a server;
  the search itself only moves to Solr when the ezfind extension is active. Learners often set only one.
- **Promising features from old screenshots.** Teach only what the installed version shows. If ezfind is not
  installed on the training site, say so and show the settings files instead.

## Hands-on exercise

Use your own training installation and a terminal in its root directory.

1. Run the Solr status check:

   ```bash
   php bin/php/console exp:solr status
   ```

2. Read the message. On a training installation without Solr it says that Solr is not configured and how to enable it.
3. Print the kernel's Solr switch:

   ```bash
   php bin/php/console exp:ini get solr.ini/SolrSettings/Enabled
   ```

4. Print the engine in effect again (`site.ini/SearchSettings/SearchEngine`) and note it.
5. Check whether the ezfind extension is present: list the folder `extension/` and look for `ezfind`.
6. Open **Setup > Extensions** in the administration and look for ezfind in the list. Note whether it is active.
7. Write one sentence: which engine does your installation use, and what would have to change to use Solr?

## Expected result

- Step 1 reports *Solr is not installed or configured on this installation*, with the lines **reason**
  (`[SolrSettings] Enabled is not true`), **search now** (the built-in index), **ezfind ext** (whether the extension
  is present) and **to enable**. Nothing is started.
- Step 3 prints `false`; step 4 prints `eZSearchEngine`.
- Steps 5 and 6 show whether the extension is installed and active; on a fresh installation it is usually neither.
- The sentence in step 7 names both changes: a Solr server configured in `solr.ini`, and the ezfind extension active.

## Quiz

1. Which value does ezfind put into `[SearchSettings] SearchEngine`?
2. Name three features Solr adds that the built-in engine does not have.
3. True or false: setting `[SolrSettings] Enabled=true` alone moves the site search to Solr.
4. What does **Elevate** do?
5. Where do you find which Solr versions ezfind supports?

<details>
<summary>Answers</summary>

1. `ezsolr` (the extension's `settings/site.ini.append.php`).
2. Any three of: relevance ranking and boosting, facets, highlighting, spell checking, autocomplete, elevation, tag
   clouds from search.
3. **False.** The ezfind extension must be active as well; `solr.ini` only tells the installation where Solr is.
4. It pins a chosen object among the very first results for a given search query.
5. In the extension's own repository; this repository does not state it
   ([Search backends](../../../hosting/basics/search-backends.md#solr-through-ez-find)).

</details>

## Further reading

- [Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md): the website
  creator's guide to switching it on.
- [Search backends](../../../hosting/basics/search-backends.md): what each engine costs to run.
- [Server control commands](../../../../features/6.0/web-server-and-solr-commands.md): the `exp:solr` reference.

## Related pages

- [Module 1: How search works](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
