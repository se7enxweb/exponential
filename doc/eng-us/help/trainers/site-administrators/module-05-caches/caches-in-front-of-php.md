---
title: "Lesson 5.5: The caches in front of PHP"
audience: trainer, admin
category: training
order: 55
summary: Learners compare Velocity's response cache, the role-aware HTTP cache and the static cache, explain why the static cache ships off, and read the headers that show which cache answered. 35 minutes.
keywords: site administration, velocity response cache, http cache, httpcache.ini, static cache, StaticCache, makestaticcache, caching layers, X-Exp-Cache, headers, opcache, apcu
type: help
---
# Lesson 5.5: The caches in front of PHP

Some caches answer a request before Exponential renders anything: Velocity's response cache, the role-aware HTTP cache
and the static cache. They make a site very fast and are also why a visitor can see an old page after everything
else was cleared. This lesson compares them, explains why the static cache ships off, and shows how to see which cache
answered.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 5: Caches](README.md) |
| Duration | 35 minutes (15 presenting, 20 exercise) |
| Level | Advanced |
| Audience | Site administrators and support staff |
| Prerequisites | [Lesson 5.4](preloading-caches.md) |

## Learning objectives

After this lesson, learners can:

- name the caches in front of PHP, whether each ships on or off, and whom each serves;
- explain why the static cache ships off and what replaces it;
- read the status of these caches with `exp:cache status`;
- read response headers to tell which cache answered;
- clear each of them, after the new code is live.

## Trainer's notes

### How to present it

Source: [Caching layers](../../../hosting/performance/caching-layers.md),
[Static cache](../../../hosting/performance/static-cache.md) and
[Operating a site, the caches in front of PHP](../../../../guides/operating-a-site.md#the-caches-in-front-of-php).

1. **The three** (6 minutes):

   | | Velocity response cache | Role-aware HTTP cache | Static cache |
   |---|---|---|---|
   | Ships | on under Velocity | off | off |
   | Serves | anonymous visitors | anonymous and signed-in, per permission context | anonymous visitors, as files |
   | Cleared by | `exp:velocity cache clear`; entries expire | purged on content change; `--clear-id=exphttpcache` | regenerated on publish |

2. **Why the static cache ships off** (3 minutes). With it on, every publish regenerated the affected pages on every
   public siteaccess over HTTP inside the editor's request, so a publish took seconds. Velocity's response cache gives
   visitors the same benefit without that cost. `site.ini [ContentSettings] StaticCache=disabled` is the shipped
   value; leave it so unless a host decides otherwise.
3. **Seeing which cache answered** (3 minutes). The HTTP cache sends `X-Exp-Cache: HIT`, `STALE`, `MISS (reason)` or
   `BYPASS (reason)`. Check headers with the browser's developer tools or `curl -I`.
4. **The order of clearing** (3 minutes). Clear these caches **after** PHP-FPM or Velocity runs new code; a page
   rendered by the old code in between is stored again.

### Common questions

**A visitor sees an old page; I cleared everything.** Check Velocity's response cache, the HTTP cache, a static file,
a proxy or CDN in front, and the browser cache, in that order.

**Can I switch the static cache on for a training exercise?** Not needed: read the generator's help instead. On a
live site it is a host's decision (see [Hosting and operations](../../hosting-operations/README.md)).

**What about OPcache and APCu?** They cache PHP code and data per PHP process. The Caches page has **Reset OPcache**
and **Empty APCu** for the process that answered; the other server keeps its own.

### Pitfalls

- **Testing while signed in.** Velocity's response cache and the static cache never serve signed-in users. Test in a
  private window.
- **Clearing too early in a deployment.** Old code refills the caches. Teach the order now; the Hosting course
  automates it.

## Hands-on exercise

On your training installation, with a terminal in the root and a private browser window:

1. Read the status of the caches in front of PHP:

   ```bash
   php bin/php/cache.php status
   ```

   Note for each line whether it is on or off.
2. Read the static cache setting:

   ```bash
   php bin/php/console exp:ini where site.ini/ContentSettings/StaticCache
   ```

3. Read the static cache generator's options without running it:

   ```bash
   php bin/php/makestaticcache.php --help
   ```

4. Read the status of Velocity's response cache:

   ```bash
   php bin/php/cache.php velocity status
   ```

5. In the private window, open the public front page twice. Then read the headers of the page:

   ```bash
   curl -sI http://localhost:8088/
   ```

6. Edit and publish the front page's title (or any visible article) in the administration, then reload the private
   window at once and again after half a minute.
7. Clear the response cache and reload:

   ```bash
   php bin/php/console exp:velocity cache clear
   ```

8. Open **Setup > Cache management**, group **Pages, queries and the server**, and read the rows for the HTTP cache,
   the query cache, the SQL profile, Velocity's response cache, OPcache and APCu. Do not press their buttons.

## Expected result

- Step 1 ends with `PASS  status read`; the static cache and the HTTP cache are off, Velocity's response cache is on.
- Step 2 shows `StaticCache=disabled` in effect.
- Step 6: the change may appear only after the response cache entry expires or is cleared (step 7).
- The trainer checks by asking each learner to name the first three caches to check when a visitor sees an old page.

## Quiz

1. Which of the three caches ships on?
   A. The static cache B. The HTTP cache C. Velocity's response cache D. None
2. Why does the static cache ship off?
3. Which header tells what the role-aware HTTP cache did?
4. Why clear the caches in front of PHP only after the new code runs?
5. Name two caches that never serve a signed-in visitor.

<details>
<summary>Answers</summary>

1. **C.** Under Velocity ([Caching layers](../../../hosting/performance/caching-layers.md#the-layers-at-a-glance)).
2. Every publish regenerated the affected pages over HTTP inside the editor's request, which made publishing slow
   ([Static cache](../../../hosting/performance/static-cache.md#why-it-ships-off)).
3. `X-Exp-Cache`.
4. Otherwise a page rendered by the old code in between is stored again.
5. Velocity's response cache and the static cache.

</details>

## Further reading

- [Caching layers](../../../hosting/performance/caching-layers.md): all twelve layers.
- [Static cache](../../../hosting/performance/static-cache.md) and
  [Static cache generator](../../../../features/6.0/static-cache-generator.md).
- [Velocity response cache](../../../../features/6.0/velocity-response-cache.md).
- [HTTP caching](../../../../bc/6.0/http-caching.md).

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
