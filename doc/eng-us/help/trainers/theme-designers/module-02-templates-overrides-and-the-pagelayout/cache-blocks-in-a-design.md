---
title: "Lesson 2.4: Cache blocks in a design"
audience: trainer, designer, developer
category: training
order: 24
summary: Learners explain why the pagelayout needs cache blocks, wrap their header menu in one with the right keys, see a publish refresh it, and find the bug of a block whose keys forget the user. 45 minutes.
keywords: theme design, cache-block, keys, expiry, subtree_expiry, ignore_content_expiry, template-block cache, content view cache, ViewCaching, pagelayout performance, debug bar
type: help
---
# Lesson 2.4: Cache blocks in a design

The content view cache keeps the main area of each page, but the pagelayout runs on every request. A menu built from
a fetch in the pagelayout therefore costs database work on every page. Cache blocks keep such parts. They are also
the source of the most embarrassing theme bug there is: one visitor's name shown to another. This lesson teaches both
sides.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 2: Templates, overrides and the pagelayout](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Designers and front-end developers |
| Prerequisites | [Lesson 2.2](the-pagelayout.md): the greeting in `page_footer.tpl`; an Editor or administrator account on the training site |

## Learning objectives

After this lesson, learners can:

- explain which part of a page the content view cache keeps and why the pagelayout is not part of it;
- wrap an expensive part of a template in `{cache-block}` with keys that cover everything its output depends on;
- choose between `expiry`, `subtree_expiry` and `ignore_content_expiry`;
- recognize and fix a cache block whose keys forget the user.

## Trainer's notes

### How to present it

Source: [View cache and cache blocks](../../../creators/website-creators/view-cache-and-cache-blocks.md) and
[The pagelayout, cache blocks](../../../creators/website-creators/the-pagelayout.md#cache-blocks).

1. **Two caches** (4 minutes). `site.ini [ContentSettings] ViewCaching=enabled` keeps the output of `content/view`
   per node, view mode, language, siteaccess, roles and more. The pagelayout is not part of it and runs every time.
2. **The cache block** (5 minutes).

   ```
   {cache-block keys=array( $module_result.uri, $current_user.contentobject_id )}
     ... expensive menu ...
   {/cache-block}
   ```

   The stored output is reused while the keys match and the block has not expired. Without `keys`, a block is the same
   for every page.
3. **Expiry** (4 minutes). `expiry` in seconds (default two hours, `0` never by time); `subtree_expiry` clears the block
   only when something below a node is published; `ignore_content_expiry` keeps it through publishing. By default a
   publish clears all cache blocks, which is why a new folder shows in the menu.
4. **The bug** (4 minutes). A greeting "Signed in as Anna" inside a block keyed only by the address is stored for the
   first visitor and shown to everyone after. Either add `$current_user.contentobject_id` to the keys, or keep
   per-user output outside cache blocks. Show it live in the exercise.
5. **Clearing** (3 minutes). `php bin/php/ezcache.php --clear-id=template-block` clears every cache block.

### Common questions

**How do I know whether a block was used?** With debug output on, the Exp Debug bar shows a **CB** figure (cache
blocks used and present on the page) ([Exp Debug bar](../../../../features/6.0/exp-debug-bar.md)).

**Should the whole pagelayout be one block?** Shipped themes often wrap most of it in one block keyed by the address
and the user. Several smaller blocks with fitting keys are easier to reason about in class.

**Why is my menu stale after I renamed a folder?** The block was not cleared, for example because of
`ignore_content_expiry` or a `subtree_expiry` elsewhere. Clear `template-block` and check the parameters.

### Pitfalls

- **Testing while signed in as the same user.** The key bug shows only with two different users. Use a private window
  for the second.
- **Keys that are too wide.** A key that changes on every request (a time, a random value) stores a new block every
  time and fills the disk.
- **Menus that differ by permission.** A fetch only returns what the current user may read. On a site where signed-in
  users see more top-level items than visitors, the menu block needs the user (or the user's roles) in its keys too.
- **Leaving the bug in.** End the exercise with the fixed footer and check it again.

## Hands-on exercise

On your training installation. Files are below `extension/training_theme/design/training_theme/templates/`.

1. Open `page_header.tpl`. Wrap the `<nav>` with its `{def}` and `{undef}` in a cache block with one constant
   key (the menu is the same on every page and for every user):

   ```
   {cache-block keys=array( 'training_theme_menu' )}
   ...the def, the nav and the undef...
   {/cache-block}
   ```

2. Check it with `php bin/php/eztemplatecheck.php -s<site> <file>` and reload two pages of `<site>`.
3. In the administration, create and publish a folder **Training menu test** directly below the top of the site's
   content tree.
4. Reload a page of `<site>`: the folder appears in the menu, because publishing cleared the cache blocks.
5. Now provoke the bug. In `page_footer.tpl`, wrap the greeting from lesson 2.2 in a block keyed only by a constant:

   ```
   {cache-block keys=array( 'training_theme_footer' )}
   ...the greeting line...
   {/cache-block}
   ```

6. Clear the cache blocks: `php bin/php/ezcache.php --clear-id=template-block`.
7. Sign in on `<site>` with your account and load a page: the footer greets you.
8. Open a private browser window, which is not signed in, and load the same page. Note what the footer says.
9. Fix the keys: `keys=array( 'training_theme_footer', $current_user.contentobject_id )`. Clear `template-block`
   again and repeat steps 7 and 8.
10. Remove the folder **Training menu test** (move it to the trash) and reload: the menu no longer shows it.

## Expected result

- Step 4 and step 10: the menu follows publishing and removal without a manual cache clear.
- Step 8: the anonymous window shows the signed-in learner's greeting; this is the bug.
- Step 9: the anonymous window shows no greeting, the signed-in one shows the greeting.
- The trainer checks the final `page_footer.tpl` of two learners: the user is part of the keys, or the greeting is
  outside the block.

## Quiz

1. Which part of a page does the content view cache keep, and which part runs on every request?
2. What happens to all cache blocks, by default, when content is published?
3. A block prints "Hello, <name>" and has `keys=array( $module_result.uri )`. What goes wrong?
4. Which parameter clears a block only when something below a given node is published?
   A. `expiry` B. `keys` C. `subtree_expiry` D. `ignore_content_expiry`
5. Which command clears every cache block?

<details>
<summary>Answers</summary>

1. The main area of `content/view` pages is kept; the pagelayout runs every time
   ([View cache and cache blocks](../../../creators/website-creators/view-cache-and-cache-blocks.md)).
2. They are cleared, so menus show new content.
3. The first visitor's greeting is stored per address and shown to every other visitor of that page.
4. **C.**
5. `php bin/php/ezcache.php --clear-id=template-block`.

</details>

## Further reading

- [View cache and cache blocks](../../../creators/website-creators/view-cache-and-cache-blocks.md): parameters and
  debugging stale output.
- [Performance and caching for websites](../../../creators/website-creators/performance-and-caching.md): the wider
  picture.
- [Caches](../../../../guides/caches.md): every cache of an installation and how to clear it.

## Related pages

- [Module 2: Templates, overrides and the pagelayout](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
