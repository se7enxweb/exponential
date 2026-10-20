---
title: "Lesson 3.3: After a stylesheet change"
audience: trainer, designer, developer
category: training
order: 33
summary: Learners clear exactly the caches a stylesheet or script change needs (packer, template, template-block), use a hard reload instead of a version query, and know the one deploy command for a Velocity server. 30 minutes.
keywords: theme design, stylesheet change, cache clear, ezjscore-packer, template-block, template cache, hard reload, version query, exp:velocity deploy, packer, stale stylesheet
type: help
---
# Lesson 3.3: After a stylesheet change

"I changed the CSS and nothing happened" is the most common sentence in a theme course. The packed file, the cache
block that holds its link and the browser's copy each remember the old version. This short lesson gives learners one
routine that always works, and one rule they must not break: no version query on a link.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 3: Stylesheets and scripts through the packer](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Beginner |
| Audience | Designers and front-end developers |
| Prerequisites | [Lesson 3.2](the-ezjscore-packer.md) |

## Learning objectives

After this lesson, learners can:

- name the three places that remember an old stylesheet and the cache that clears each;
- run the clear routine after a stylesheet or script change;
- force the browser to fetch the new file with a hard reload;
- explain why a `?v=` query on a link is not used.

## Trainer's notes

### How to present it

Sources: [Stylesheets and scripts for your site, after a change](../../../creators/website-creators/stylesheets-and-scripts.md#after-a-change)
and [Styling the administration, after a stylesheet change](../../../../bc/6.0/admin4-styling.md#after-a-stylesheet-change).

1. **Three memories** (4 minutes). Draw them on the board:

   | Remembers | Cleared by |
   |---|---|
   | the packed file | `php bin/php/ezcache.php --clear-id=ezjscore-packer` |
   | the link to it, in compiled templates and in cache blocks of the pagelayout | `--clear-tag=template` and `--clear-id=template-block` |
   | the browser's copy | a hard reload (Ctrl+Shift+R, or Cmd+Shift+R on a Mac) |

   With `AppendLastModifiedTime=enabled` a new file gets a new name anyway; the clears make sure the page links that
   new name.
2. **No version query** (2 minutes). Do not add `?v=...` to a stylesheet or script link: the modification time in the
   packed name already does that, and the project's rule forbids version queries. A browser that still shows the old
   look needs a hard reload.
3. **On a Velocity server** (2 minutes). Velocity keeps its own response cache. On a server that runs it, one command
   does the whole routine after a stylesheet change:

   ```bash
   php bin/php/console exp:velocity deploy --packer
   ```

   `--packer` clears the packed scripts and styles right before the template-block cache. Add `--dry-run` to see the
   steps first.
4. **New template files** (2 minutes). A stylesheet change needs no `template-override` clear; a new template file
   does (lesson 2.3).

### Common questions

**Why does the change show for me but not for a colleague?** Your browser fetched the new file; theirs still holds
the old one. A hard reload on their side.

**Do I need all of this with the packer off?** With level 0 each file is linked at its own address; the browser's copy
is the main obstacle, so a hard reload is usually enough. The routine still works.

**Why not clear all caches every time?** It works, but on a real site it is slow and clears the content view cache for
every page. Learn the targeted clear.

### Pitfalls

- **Clearing only the packer.** The pagelayout's cache block still links the old name, which may no longer exist:
  the page loses its styles. Clear `template-block` too.
- **Editing in the wrong design.** The change was made in a file that the chain never reaches. Check with the page
  source which file the packed bundle came from (packer off, lesson 3.2).
- **Testing behind a proxy or CDN.** Their caches are outside Exponential; the trainer's lab should not have one.

## Hands-on exercise

On your training installation, packer on.

1. In `training.css` of your design, change the breadcrumb's font size from `0.9rem` to `1.1rem`. Save.
2. Reload the page normally. Note whether the breadcrumb changed.
3. Read the stylesheet link in the page source and write down the file name.
4. Run the routine:

   ```bash
   php bin/php/ezcache.php --clear-id=ezjscore-packer
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-id=template-block
   ```

   If your lab is served by Velocity, also clear its response cache: `php bin/php/console exp:velocity cache clear`.
5. Reload with a hard reload. Check the breadcrumb.
6. Read the stylesheet link again and compare its name with step 3.
7. If your lab runs on Velocity, run `php bin/php/console exp:velocity deploy --packer --dry-run` and read the list of
   steps it would run. Do not run it without `--dry-run` on a shared server.
8. Write the routine of step 4 on a card next to your keyboard.

## Expected result

- Step 2: the change may or may not show, depending on caches and the browser.
- Step 5: the breadcrumb shows the new size.
- Step 6: the number between the underscores in the name is higher than in step 3.
- Step 7: the dry run lists the steps, among them the packer and template-block caches, and changes nothing.
- The trainer asks two learners to say the three memories and their clears without notes.

## Quiz

1. Which three caches does the routine clear after a stylesheet change?
2. Why can the page lose all its styles if only the packer cache is cleared?
3. A colleague still sees the old color after your routine. What should they do?
   A. Add `?v=2` to the link B. Clear all caches again C. A hard reload D. Restart the database
4. Which command does the whole routine on a Velocity server?
5. True or false: a stylesheet change needs the `template-override` cache cleared.

<details>
<summary>Answers</summary>

1. `ezjscore-packer`, the template cache and `template-block`
   ([Stylesheets and scripts for your site](../../../creators/website-creators/stylesheets-and-scripts.md#after-a-change)).
2. A cache block in the pagelayout still links the old packed file name, which no longer exists.
3. **C.** Version queries are not used; the modification time in the name already changes it.
4. `php bin/php/console exp:velocity deploy --packer`.
5. **False.** Only a new template file needs it.

</details>

## Further reading

- [Stylesheets and scripts for your site](../../../creators/website-creators/stylesheets-and-scripts.md): the checklist.
- [Caches](../../../../guides/caches.md): every cache and its id.
- [Velocity response cache](../../../../features/6.0/velocity-response-cache.md): the extra cache on Velocity servers.

## Related pages

- [Module 3: Stylesheets and scripts through the packer](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
