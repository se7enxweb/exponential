---
title: "Lesson 5.1: What the caches hold"
audience: trainer, admin
category: training
order: 51
summary: Learners read the Caches page, name the six cache groups and what each holds, tell cache ids from tags, and read when and by whom a cache was last cleared. 35 minutes.
keywords: site administration, caches, cache management, setup/cache, content view cache, template cache, ini cache, image alias, cache id, cache tag, last cleared, velocity
type: help
---
# Lesson 5.1: What the caches hold

Exponential caches almost everything it can: rendered views, compiled templates, settings, image sizes, whole pages.
Caches make the site fast, and a stale cache is behind most "my change does not show" questions. Before clearing
anything, an administrator should know what each cache holds. This lesson reads **Setup > Cache management** without
pressing a single clear button.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 5: Caches](README.md) |
| Duration | 35 minutes (15 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators and support staff |
| Prerequisites | [Module 1](../module-01-orientation/README.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- open the Caches page and name its six groups;
- say what the content view cache, the template caches, the INI caches and the image aliases hold;
- explain the difference between a cache's id and its tags;
- read the column of the last clear and who made it;
- explain which caches Apache with PHP-FPM and Velocity share and which they do not.

## Trainer's notes

### How to present it

Source: [Caches](../../../../guides/caches.md) and [Operating a site](../../../../guides/operating-a-site.md).

1. **Why caches** (2 minutes). Rendering a page from the database and templates every time is slow. Each cache keeps
   one result so the next request is fast. A cleared cache is made again on the next requests, which are slower
   until then.
2. **The page** (3 minutes). **Setup > Cache management** opens the page **Caches**: figures (caches, enabled,
   groups, size once measured, last clear), the buttons **Clear all caches…**, **Clear content caches…**,
   **Clear template caches…**, **Clear INI caches…** and **Measure sizes**, then **Search** and the **Groups**.
3. **The groups** (5 minutes): **Content and views** (the content view cache, URL aliases, user information),
   **Templates** (compiled templates, template blocks, the override cache), **INI and settings**, **Images** (image
   aliases), **Pages, queries and the server** (the HTTP cache, the SQL query cache, Velocity's response cache,
   OPcache and APCu), **Other** (caches of extensions).
4. **Ids and tags** (2 minutes). Each cache shows its id (`content`, `template-block`, `ini` ...) and tags
   (`content`, `template`, `ini` ...). One tag clears every cache carrying it. After a settings change clear by the
   tag `ini`, not the id.
5. **Shared or not** (3 minutes). Cache files live in `var/`, which Apache with PHP-FPM and Velocity share. Velocity
   keeps settings in memory (restart it after clearing INI caches) and has its own response cache.

### Common questions

**Is it safe to look at this page on a live site?** Yes, reading is safe. Pressing **Measure sizes** reads every cache
file; on a big site it takes up to three seconds of server time.

**Who may open it?** Users with `setup/managecache`. Every button posts with the form token.

**Where does "last cleared" come from?** The audit trail records every clear, from the page, System information,
`exp:cache` and `ezcache.php`, naming the user or the operating system user of a shell.

### Pitfalls

- **Pressing a clear button out of curiosity.** On the lab it is harmless; on a live site every clear makes the next
  visitors wait. In this lesson nobody clears.
- **Expecting a cache per page.** The content view cache holds views per object, view mode and user permissions, not
  per address.

## Hands-on exercise

On your training installation, signed in as an administrator:

1. Open **Setup > Cache management**. Note the figures at the top: how many caches, how many enabled, how many groups.
2. Press **Measure sizes**. Note the total size and the largest cache.
3. Choose the group **Content and views** under **Groups**. Find the content view cache: its id, its tags and its
   directory.
4. Choose the group **Templates**. Which cache holds the compiled templates? Which one holds template blocks?
5. Choose **INI and settings**. Which caches carry the badge about restarting Velocity?
6. Choose **Pages, queries and the server**. Is the HTTP cache on or off on your lab? Is Velocity's response cache
   shown?
7. Type `image` in **Search**. Which cache remains?
8. Read the column of the last clear for the content view cache: when, and by whom?
9. Fill in a table on paper with three columns (cache, what it holds, tag) for five caches of your choice.

## Expected result

- Learners name the six groups and give the id and tag of the content view cache (`content`, tag `content`).
- Step 5: the INI caches carry the badge about restarting Velocity.
- Step 7 leaves the image alias cache (`imagealias`).
- The trainer checks the table of step 9 with two learners.

![The list of caches with what each holds, its size and Clear selected](../../../images/trainers/sa-cache-list.webp)

## Quiz

1. Which group holds the image aliases?
   A. **Content and views** B. **Templates** C. **Images** D. **Other**
2. What is the difference between a cache's id and its tags?
3. After changing a setting, which tag do you clear?
4. Why must Velocity be restarted after clearing INI caches?
5. Where does the page take the information "last cleared ... by" from?

<details>
<summary>Answers</summary>

1. **C.** ([Caches, the groups](../../../../guides/caches.md#2-the-groups)).
2. The id names one cache; a tag is shared by several caches, and clearing a tag clears all of them.
3. `ini`.
4. A running Velocity has read the settings into memory ([Caches, Velocity](../../../../guides/caches.md#4-velocity)).
5. The audit trail (`system.cache.clear`) and the kernel's expiry times.

</details>

## Further reading

- [Caches](../../../../guides/caches.md): every cache and button of the page.
- [Caching layers](../../../hosting/performance/caching-layers.md): the twelve layers from the browser to OPcache.
- [Operating a site](../../../../guides/operating-a-site.md): look before you clear.

## Related pages

- [Module 5: Caches](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
