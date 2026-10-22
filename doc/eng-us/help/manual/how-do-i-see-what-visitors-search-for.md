---
title: How do I see what visitors search for?
audience: editor, admin
category: manual
order: 565
summary: Setup > Search statistics lists every recorded search phrase with how often it was searched and how many results it found on average; Found nothing shows the gaps, Search now runs a phrase again, and Reset statistics clears the log. Recording is off until an administrator switches it on.
keywords: search statistics, search phrases, found nothing, most searched, reset statistics, LogSearchStats, search/stats, privacy
context: search/stats
type: help
---
# How do I see what visitors search for?

The search statistics are a list of the phrases people typed into the search of your sites. They show what readers
look for, and, more usefully, what they look for and do not find.

## Who can do this

Users whose role gives access to the **search** module, usually administrators. Someone with the right to change
settings must first switch recording on.

## Steps

1. Ask an administrator to switch recording on for the public siteaccess (`site.ini [SearchSettings]
   LogSearchStats=enabled`). It is off by default, because phrases can contain personal data.
2. Click **Setup**, then **Search statistics**.
   The page shows an **Overview** (number of searches, different phrases, the share that found nothing) and which
   siteaccesses record searches.
3. Choose what to show: **All phrases**, **Most searched**, **Found nothing** or **Fewest results**, and the order.
4. Type part of a phrase under **Find a phrase** to look for one.
5. Click **Search now** in a row to run the phrase in the administration and see what it finds today.
6. Fix what you learn: write the missing page, add the words people use to an existing title or text, or tag it.
7. To start counting afresh, click **Reset statistics** and confirm.

## What can go wrong

- **"No search has been recorded yet."** No siteaccess records searches, or nobody has searched since it was
  switched on.
- **Attention: Reset statistics removes every recorded phrase for good,** not only those on the screen.
- **The phrases contain names or e-mail addresses.** People type anything into a search field. Treat the list as
  personal data, keep it short, and reset it regularly.
- **The menu entry is missing.** Your role does not give access to the search module.

## Related pages

- [Site search and what visitors look for](../customers/day-to-day/site-search-and-what-visitors-look-for.md)
- [Search statistics and privacy](../government/building/search/search-statistics-and-privacy.md)
- [Why does search not find an item I know exists?](why-does-search-not-find-an-item.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
