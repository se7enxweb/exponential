---
title: "Lesson 3.5: Caches for template developers"
audience: trainer, developer
category: training
order: 35
summary: Learners tell the compiled template cache, the content view cache and cache blocks apart, clear exactly the cache a change needs, and write a cache-block with keys and expiry in a pagelayout part. 45 minutes.
keywords: developers course, cache, TemplateCompile, ViewCaching, CachedViewModes, cache-block, keys, expiry, subtree_expiry, ignore_content_expiry, ezcache.php, clear-tag, clear-id, template-block, eztc
type: help
---
# Lesson 3.5: Caches for template developers

Exponential is fast because it caches at several levels, and template developers meet every one of them: a change
that "does not work" is often a cache that still holds the old result. This lesson explains the three caches a
template passes through, how to clear exactly the one a change needs, and how to use `{cache-block}` in templates of
your own.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 3: Designs, overrides and debugging](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 3.4](finding-the-template-behind-a-page.md) |

## Learning objectives

After this lesson, learners can:

- describe the compiled template cache, the content view cache and cache blocks;
- clear caches by tag and by id with `php bin/php/ezcache.php`;
- choose the cache to clear for a template edit, a new override, a settings change and a stylesheet change;
- write a `{cache-block}` with `keys` and `expiry`, and know when `subtree_expiry` and `ignore_content_expiry` help.

## Trainer's notes

### How to present it

Sources: [Caches](../../../../guides/caches.md), `settings/site.ini` (`[TemplateSettings]`, `[ContentSettings]`)
and `lib/eztemplate/classes/eztemplatecachefunction.php`.

1. **Three layers** (6 minutes).

   | Cache | Setting (default) | Holds | Clear with |
   |---|---|---|---|
   | Compiled templates | `[TemplateSettings] TemplateCompile=enabled` | Each `.tpl` compiled to PHP | `--clear-tag=template` |
   | Content view cache | `[ContentSettings] ViewCaching=enabled`, `CachedViewModes=full;sitemap;pdf` | The rendered result of a node view | `--clear-tag=content`, or per node with `ezcontentcache.php` |
   | Cache blocks | `[TemplateSettings] TemplateCache=enabled` | Parts of a template wrapped in `{cache-block}` | `--clear-id=template-block` |

2. **Clearing by tag and id** (4 minutes). `php bin/php/ezcache.php --list-tags` and `--list-ids` show what
   exists. Tags group ids (`content`, `template`, `ini` ...); ids are single caches (`template-override`,
   `template-block`, `design_base`, `ezjscore-packer` ...). Avoid `--clear-all` as a habit: it costs the next
   visitors a slow page each.
3. **Which cache for which change** (4 minutes).

   | You changed | Clear |
   |---|---|
   | An existing `.tpl` | `template` (none while `TemplateCompile=disabled`), then `content` for cached pages |
   | A new override rule or new template path | `ini`, `template-override`, `template`, `content` |
   | A setting | `ini` |
   | A stylesheet or script loaded by the packer | `ezjscore-packer` and `template-block` |

4. **`cache-block`** (6 minutes). `{cache-block keys=array( $uri_string ) expiry=3600}...{/cache-block}` stores the
   output once per key combination. Without `expiry` the default is two hours. By default a cache block expires when
   any content is published; `ignore_content_expiry` turns that off, `subtree_expiry` limits it to one subtree.

### Common questions

**Should I switch compiling off on my development machine?** Many developers set `TemplateCompile=disabled` while
editing templates, so every edit is read fresh. Never on a live site.

**What does `eztc.php` do?** It compiles all templates of a siteaccess in advance (`php bin/php/eztc.php -s<sa>`),
so the first visitors after a deploy do not pay for compiling.

**Can the administration clear caches?** Yes: **Setup** > **Cache management**, or the **Clear cache** box with
**All caches**, **Content**, **Template**, **Template & content**, **Ini settings** and **Static**.

### Pitfalls

- **A cache block without keys around per-user content.** Everyone then sees the first visitor's version, for
  example their name. Put user-specific parts outside cache blocks, or key them correctly.
- **Clearing before a reload of the PHP workers.** On a server that keeps PHP code in memory (Velocity, PHP-FPM),
  clear caches after the workers have the new code, or old pages are cached again (lesson 13.2).
- **Stylesheet changes that do not show.** The packed file name is cached inside a cache block of the pagelayout:
  clear `template-block` too.

## Hands-on exercise

On the development installation, with `TemplateCompile=enabled` (the default).

1. List the cache tags and ids: `php bin/php/ezcache.php --list-tags` and `php bin/php/ezcache.php --list-ids`.
2. Change the text of the course line in `mysite_article.tpl` from lesson 3.2 and reload an article. Note whether
   the change shows.
3. Clear only the template cache, reload; then clear the content tag, reload. Note which step made the change
   visible.
4. Clear the cache of one node only:
   `php bin/php/ezcontentcache.php --clear-node=<node ID>` and check the help with `--help`.
5. Add a cache block to `mysite_article.tpl`:

   ```
   {cache-block keys=array( $node.node_id ) expiry=60}
       <p>Cached at {currentdate()|l10n( 'shortdatetime' )}</p>
   {/cache-block}
   ```

6. Clear the `template` tag and `content` tag, then reload the article twice: the time stays the same.
7. Publish a change to any item in the administration, reload: the block was renewed, because cache blocks expire
   on publishing by default.
8. Add `ignore_content_expiry` to the block's parameters, clear the template and content caches, and repeat step
   7: now the time stays until the 60 seconds are over.
9. Compile the templates of your siteaccess in advance: `php bin/php/eztc.php -s<sa>`.
10. Remove the cache block again, clear the caches, and commit.

## Expected result

- Step 3: the template clear alone is not always enough when the page itself is in the view cache; after the content
  clear the change shows.
- Step 6: two reloads show the same time; step 7 shows a new time; step 8 keeps the time until the expiry.
- Step 9 lists the compiled templates and ends without errors.
- The trainer asks each learner which caches a new override needs.

## Quiz

1. Which setting turns the content view cache on or off?
   A. `TemplateCompile` B. `ViewCaching` C. `TemplateCache` D. `DesignLocationCache`
2. What is the difference between `--clear-tag` and `--clear-id`?
3. Which caches does a new override rule with a new template need?
4. When does a `{cache-block}` without parameters expire?
5. Why must a block that shows the current user's name not be cached without the right keys?

<details>
<summary>Answers</summary>

1. **B.** `[ContentSettings] ViewCaching`.
2. A tag groups several caches; an id names one cache.
3. `ini`, `template-override`, `template`, and `content` for pages already cached.
4. After the default two hours, or earlier when content is published.
5. The first visitor's output would be shown to everyone.

</details>

## Further reading

- [Caches: what each one holds and how to clear it](../../../../guides/caches.md).
- [Preloading caches](../../../../guides/preloading-caches.md): warming pages after a deploy.
- [Operating a site](../../../../guides/operating-a-site.md): clearing by what changed.

## Related pages

- [Module 3: Designs, overrides and debugging](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
