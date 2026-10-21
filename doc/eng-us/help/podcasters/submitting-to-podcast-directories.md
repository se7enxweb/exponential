---
title: Submitting to podcast directories
audience: podcaster
category: podcasting-streaming
order: 20
summary: What Exponential provides when a show is submitted to Apple Podcasts and other directories - a stable feed address with the elements Apple requires - what happens outside Exponential, and the checks to make before and after submitting.
keywords: submit podcast, Apple Podcasts, directory, podcast app, feed address, validation, submission, ownership, owner email, listing, after submission
type: help
---
# Submitting to podcast directories

This page is for producers who list a show in podcast directories. Exponential's part is the feed: an address that
answers with RSS 2.0 and the iTunes elements. The submission itself happens in each directory's own tools, with your
account there; nothing in Exponential connects to a directory.

## Contents

1. [What Exponential gives you](#what-exponential-gives-you)
2. [Before you submit](#before-you-submit)
3. [Submitting](#submitting)
4. [After you submit](#after-you-submit)
5. [What this page cannot tell you](#what-this-page-cannot-tell-you)

## What Exponential gives you

- A feed address: `https://<your site>/rss/feed/<access URL>`.
- The channel elements Apple requires, as far as you filled them in: title, description, language, artwork,
  category, explicit flag, owner ([Channel fields reference](channel-fields-reference.md)).
- Episodes with an `enclosure` carrying the real byte length and MIME type, a `guid` that does not change, and a
  `pubDate` ([Episode mapping reference](episode-mapping-reference.md)).
- Values held to what the format allows when the export is saved, "rather than stored as typed and rejected later by
  Apple, where the reason would not be visible" (comment on `setPodcastHead()` in `kernel/classes/ezrssexport.php`).

The feed is plain RSS, so directories and apps other than Apple can read it too.

## Before you submit

- [ ] At least one episode is published and in the feed. A feed without items gives a directory nothing to check.
- [ ] Owner name, owner e-mail, artwork address and category are filled in.
- [ ] The artwork address opens the image in a browser, signed out.
- [ ] Each enclosure opens and plays signed out; its `type` is an audio or video type, not
      `application/octet-stream`.
- [ ] The feed address uses `https://` and the public host name, not the administration host
      ([Checking your feed](checking-your-feed.md)).
- [ ] The access URL is the one you want to keep. Directories store it.
- [ ] The test episodes you made while setting up are gone.

## Submitting

1. Sign in to the directory's tools for podcasters with the account that will own the listing.
2. Enter the feed address.
3. Follow the directory's validation. If it reports a problem, fix it in the export or the episode, wait for the
   feed cache (1200 seconds as shipped, or ask your administrator to clear the RSS cache), and validate again.

The owner e-mail address is public in the feed and is where a directory can reach the show. Use a role address of
the show that someone reads.

## After you submit

- **Do not change the access URL.** If you must move the feed, use **Moved to** and keep the old address answering
  for a while ([Moving, pausing or ending a show](moving-pausing-or-ending-a-show.md)).
- **Do not recreate episodes.** A recreated item gets a new remote id and therefore a new `guid`, and apps show it as
  a new episode.
- **Keep the old episodes' files.** Removing an item removes its enclosure, and subscribers who downloaded the list
  get errors for it.
- **Keep the feed active.** An inactive export no longer answers ([RSS feeds](../../guides/rss-feeds.md#5-creating-and-removing)).

## What this page cannot tell you

The current rules of each directory (artwork sizes, review times, content policies, which elements they read) are set
by those directories and change. The only directory-specific facts in Exponential's code are the Apple category list
of 2019 and the required channel elements named in the code comments. Read the directory's own documentation before
you submit, and do not quote this page as a directory's rules.

## Related pages

- [Building a podcast feed](building-a-podcast-feed.md)
- [Checking your feed](checking-your-feed.md)
- [Podcaster checklists](podcaster-checklists.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
