---
title: Understanding caches
audience: student
category: learning
order: 20
summary: Caches explained for learners - why Exponential keeps stored results, the content, template and INI caches you will meet in exercises, how to clear the smallest cache from the administration or the shell, what the php engine and Velocity add, and how to tell a cache problem from a real error.
keywords: cache, caches, content cache, template cache, INI cache, clear cache, ezcache, exp:cache, velocity cache, stale page, learner, explanation
type: help
---
# Understanding caches

A **cache** holds stored results that make pages faster; Exponential has many (content view, template, INI, HTTP,
static, SQL query, response) ([Glossary](../../glossary.md#c)). Learners meet caches when a change they made does not
appear. This page explains which cache to suspect and how to clear only that one.

## Contents

1. [Why caches exist](#why-caches-exist)
2. [The three caches you meet first](#the-three-caches-you-meet-first)
3. [Clearing in the administration](#clearing-in-the-administration)
4. [Clearing from the shell](#clearing-from-the-shell)
5. [When the page is still old](#when-the-page-is-still-old)
6. [Cache problem or real error](#cache-problem-or-real-error)

## Why caches exist

Building a page means reading settings, finding templates, compiling them and fetching content. Caches keep the
results, so the next visitor gets the page without that work. Exponential renews them when content is published,
"so clearing by hand is the exception" ([Which cache do I clear?](../manual/which-cache-do-i-clear.md)).

## The three caches you meet first

| You changed | Cache to clear | Typical exercise |
|---|---|---|
| Content, and a page shows the old version | **Content** (content view cache) | Publishing in [project 2](project-02-a-small-site-structure.md) |
| A template or a design file, or you added a new template path | **Template** caches | [project 3](project-03-a-template-override.md) |
| A setting in an `.ini` file | **INI** caches | [project 5](project-05-a-multilingual-site.md) |
| You do not know, or several of these | **All caches** | Rarely: everything is rebuilt afterwards |

Source: [Which cache do I clear?](../manual/which-cache-do-i-clear.md). The rule: clear the smallest cache that solves
the problem.

## Clearing in the administration

- **Quick:** the **Clear cache** box in the right column: choose a cache and click **Clear**.
- **Full choice:** **Setup > Cache management**. The **Caches** page groups every cache (**Content and views**,
  **Templates**, **INI and settings**, **Images**, **Pages, queries and the server**, **Other**) and has buttons for
  content, template, INI or all caches.

You need the `setup/managecache` policy. The administrator of your study site has it.

## Clearing from the shell

From the installation root ([Caches](../../guides/caches.md#5-from-a-shell)):

```bash
php bin/php/ezcache.php --clear-tag=content --allow-root-user      # the content caches
php bin/php/ezcache.php --clear-tag=template --allow-root-user     # the template caches
php bin/php/ezcache.php --clear-tag=ini --allow-root-user          # the settings caches
php bin/php/ezcache.php --clear-all --allow-root-user              # everything
php bin/php/console exp:cache clear --id=content --dry-run --allow-root-user   # what would be cleared
```

Leave out `--allow-root-user` when you are not `root`. A dry run is a good habit: it shows what a clear would
remove, with sizes.

The template guide adds one rule for overrides: a **new** template path needs the override and template caches
cleared once ([Templates and design](../../guides/templates-and-design.md#4-make-a-change-with-an-override)).

## When the page is still old

| Cause | What to do | Source |
|---|---|---|
| The browser shows its own copy | Reload with Shift held, or use a private window | [Which cache do I clear?](../manual/which-cache-do-i-clear.md#what-can-go-wrong) |
| Velocity's response cache answers for a few seconds | `php bin/php/console exp:velocity cache clear --apply`, or wait | [Caches](../../guides/caches.md#4-velocity) |
| A running Velocity holds settings in memory | `php bin/php/console exp:velocity restart --allow-root-user` after clearing INI caches | [Caches](../../guides/caches.md#4-velocity) |
| A settings change on a server with PHP-FPM | The web server's PHP must reload; ask whoever deploys | [Which cache do I clear?](../manual/which-cache-do-i-clear.md#what-can-go-wrong) |

On a study site served with the `php` engine, the first two rows are the usual ones.

## Cache problem or real error

| Sign | Likely |
|---|---|
| The old content or markup appears, without an error | A cache |
| An error page, a white page or a 500 status | A real error: read the logs ([Reading logs and errors](reading-logs-and-errors.md)) |
| The change appears in the administration's preview but not on the site | A cache on the site side |
| The change appears nowhere, even after clearing all caches | The change was not saved where you think: check the siteaccess and the file ([Understanding siteaccesses and settings](understanding-siteaccesses-and-settings.md)) |

Write in your [study journal](keeping-a-study-journal.md) each time a cache was the answer, with which one. After a
few weeks you will know your caches by heart.

## Related pages

- [Caches](../../guides/caches.md)
- [Which cache do I clear?](../manual/which-cache-do-i-clear.md)
- [Reading logs and errors](reading-logs-and-errors.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
