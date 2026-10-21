---
title: Blogs
audience: individual, website-creator, customer
category: personal-sites
order: 1620
summary: The section for people who run a blog on Exponential by themselves or with a friend - the shipped Blog and Blog post classes, writing and scheduling, tags, comments, RSS feeds and moving from another platform, with the honest limits of each.
keywords: blog, blogging, Blog class, Blog post class, posts, scheduling, tags, eztags, comments, Comment class, RSS, Atom, feeds, WordPress, migration, individual
type: help
---
# Blogs

This section is for individuals who write a blog: a personal journal, notes on a hobby, a professional blog, a
travel diary. It covers the blog you run yourself. If someone else built and hosts your blog, the owner's view is
[Blogs: start here](../../customers/types/blog-start-here.md) in the customers section.

## Contents

1. [What ships for a blog](#what-ships-for-a-blog)
2. [The pages of this section](#the-pages-of-this-section)
3. [What does not ship](#what-does-not-ship)
4. [A blog in three steps](#a-blog-in-three-steps)

## What ships for a blog

| Need | What carries it | Source |
|---|---|---|
| The blog itself | *Blog* class: name, description, tags, metadata | `class-blog.xml` in the `sevenx_classes` package |
| Posts | *Blog post* class: title, body, publication date, unpublish date, tags, enable comments, metadata | `class-blog_post.xml` |
| Comments | *Comment* class (subject, author, message), stored below the post | `class-comment.xml`; templates `full/blog_post.tpl` in `ezwebin` and `sevenx_themes_simple` |
| Tags | The `tags` keyword field (`ezkeyword`); `eztags` for a managed list | `class-blog_post.xml`; root `composer.json` |
| Feeds | RSS and Atom exports, **Setup > RSS** | kernel |
| Drafts, versions, preview | kernel | [Publishing and versions](../../publishing-and-versions.md) |
| Search engine fields | `metadata` field (`xrowmetadata`) | [Metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md) |
| Help with drafts | Exponential Intelligence | [The writing assistant](../running-it/writing-assistant-expai.md) |

## The pages of this section

| Page | Read it when |
|---|---|
| [Starting a blog](starting-a-blog.md) | You set up the blog |
| [Writing and scheduling posts](writing-and-scheduling-posts.md) | You write the first posts and want some to appear later |
| [Tags and categories](tags-and-categories.md) | You want readers to find older posts by topic |
| [Comments on your blog](comments-on-your-blog.md) | You decide whether readers may comment |
| [RSS feeds for your blog](rss-feeds-for-your-blog.md) | You want readers to follow you in a feed reader |
| [Moving from another blog platform](moving-from-another-blog-platform.md) | Your blog lives somewhere else today |

## What does not ship

- **No `ezcomments` extension.** Comments use the shipped *Comment* class; no threads, likes or reply notifications.
- **No one-click importer** from WordPress or other platforms.
- **No newsletter of new posts by default.** Readers follow the feed; a mailed digest needs `cjw_newsletter` and work
  ([Club newsletters](../club-sites/club-newsletters.md)).
- **No built-in statistics dashboard** for readers; see
  [Statistics and analytics](../../creators/website-creators/statistics-and-analytics.md).

## A blog in three steps

1. Create a *Blog* object where the blog should live, for example "Notes" under the home page
   ([Starting a blog](starting-a-blog.md)).
2. Write a first *Blog post* below it, publish, and look at it.
3. Create an RSS export for the blog and link it ([RSS feeds for your blog](rss-feeds-for-your-blog.md)).

Then keep a pace you can sustain ([Writing and scheduling posts](writing-and-scheduling-posts.md)).

## Related pages

- [Individuals: start here](../individuals-start-here.md)
- [Blogs: start here (customers)](../../customers/types/blog-start-here.md)
- [Blog reading path (customers)](../../customers/types/blog-reading-path.md)
- [Content classes for websites](../../creators/website-creators/content-classes-for-websites.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
