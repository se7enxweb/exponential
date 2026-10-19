---
title: "Lesson 5.2: Clearing caches in the administration"
audience: trainer, admin
category: training
order: 52
summary: Learners clear the smallest cache that fixes a problem from the Caches page and from the right column, read the result notice, and know the extra step Velocity needs. 35 minutes.
keywords: site administration, clear cache, clear selected, clear this group, clear content caches, clear all caches, right column, result notice, velocity response cache, stale page
type: help
---
# Lesson 5.2: Clearing caches in the administration

"Clear the cache" is the most repeated advice in content management, and the most overused. Clearing everything
works, but makes the whole site slow for a while. This lesson teaches the rule "clear the smallest thing that fixes
the problem" with the buttons of the Caches page and the quick clear in the right column.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 5: Caches](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators and support staff |
| Prerequisites | [Lesson 5.1](what-the-caches-hold.md) |

## Learning objectives

After this lesson, learners can:

- choose which cache to clear for a given change;
- clear selected caches, a group, a tag or everything from the Caches page;
- use **Clear cache** in the right column of the administration;
- read the result notice: what was cleared, how long it took, what to do next;
- clear Velocity's response cache when a page still looks old.

## Trainer's notes

### How to present it

Source: [Caches](../../../../guides/caches.md) and
[Clear the right cache](../../../../guides/operating-a-site.md#2-clear-the-right-cache).

1. **Which cache** (3 minutes). Put the table on the projector:

   | You changed | Clear |
   |---|---|
   | A setting | INI caches (tag `ini`), then restart Velocity |
   | A template | template caches (tag `template`) |
   | Content looks stale | content caches (tag `content`) |
   | A new template or extension | the template override cache and `design_base` |
   | Unsure | everything, as a last resort |

2. **The buttons** (3 minutes). Tick caches and **Clear selected**; **Clear this group…** per group;
   **Clear content caches…**, **Clear template caches…**, **Clear INI caches…** by tag; **Clear all caches…**. Every
   clear first names what goes, then ends with a notice: what was cleared, how many milliseconds it took, what to do
   next and the command for the shell.
3. **The right column** (2 minutes). On every administration page, the right column has **Clear cache** with a list
   (**All caches**, **Content**, **Template**, **Template & content**, **Ini settings**, **Static**) and **Clear**. On
   an item's page it also offers the content of that node or subtree.
4. **Velocity** (2 minutes). A page can still look old for a few seconds after a clear, from Velocity's response
   cache. Clear it with **Clear Velocity's response cache** in the last group, or wait.

### Common questions

**The page still shows the old version.** Clear Velocity's response cache or wait a few seconds; for a settings change,
restart Velocity.

**Why is a cache greyed out?** It is disabled in the settings, so there is nothing to clear.

**What does "Expiring all view cache" in the log mean?** A change touched more nodes than
`[ContentSettings] CacheThreshold` (250 by default), so every view cache was expired at once. Expected after moving a
large subtree; worth a look if it happens on ordinary edits.

### Pitfalls

- **"Clear all" as a habit.** It works and hides the cause. Ask learners to name the cache before they clear.
- **Clearing before the code is live.** On a server, clear page caches after PHP-FPM or Velocity runs the new code;
  otherwise an old page is stored again. Lesson 4.1 of [Hosting and operations](../../hosting-operations/README.md) comes back to it.

## Hands-on exercise

On your training installation, signed in as an administrator:

1. Open **Setup > Ini settings** and change `SiteName` for the siteaccess `site` (as in lesson 1.4) and **Save**.
   Note which caches the notice says were cleared.
2. Open **Setup > Cache management**. Tick only the content view cache and press **Clear selected**. Read the
   confirmation, confirm, and read the result notice: time taken and next step.
3. Press **Clear template caches…**, read the list it names, and confirm.
4. In the group **Templates**, press **Clear this group…**, read the list, and press cancel or go back without
   confirming.
5. Open the `News` folder in **Content structure**. In the right column choose **Content** under **Clear cache**
   and press **Clear**. Note which choices the list offers on a node page.
6. Edit an article in `News`, change its title and publish. Open the public page at once, then reload it after a few
   seconds.
7. In the group **Pages, queries and the server**, press **Clear Velocity's response cache** and reload the public page.
8. Read the column of the last clear for the content view cache: it now names you.
9. Undo the `SiteName` change of step 1 on the settings page.

## Expected result

- Each clear ends with a notice naming the caches, the milliseconds and the shell command.
- Step 5 offers the node and subtree choices in the right column on a node page.
- Step 6: the change shows after publishing (publishing clears the affected view caches itself); a short delay is
  Velocity's response cache.
- Step 8 names the learner's account.
- The trainer checks one learner's result notice and asks which shell command it showed.

## Quiz

1. You changed a template. Which caches do you clear?
   A. INI B. Template C. All D. Image aliases
2. What does the result notice of a clear tell you?
3. A page still looks old a few seconds after a clear on a Velocity site. Why?
4. Do you need to clear the content cache after publishing an article?
5. Where is the quick clear available on every administration page?

<details>
<summary>Answers</summary>

1. **B.** The template caches (tag `template`).
2. What was cleared, how long it took, what to do next, and the command for the shell
   ([Caches](../../../../guides/caches.md#in-short)).
3. Velocity's response cache still answers for its lifetime; clear it or wait.
4. Normally no: publishing clears the view caches of the affected pages.
5. **Clear cache** in the right column.

</details>

## Further reading

- [Caches](../../../../guides/caches.md): the page and its usual questions.
- [Operating a site](../../../../guides/operating-a-site.md#2-clear-the-right-cache): which cache for which change.
- [Cache clears that move directories aside](../../../../features/6.0/cache-clear-rename-aside.md): why clears do not
  make requests wait.

## Related pages

- [Module 5: Caches](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
