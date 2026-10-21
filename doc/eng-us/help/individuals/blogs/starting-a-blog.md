---
title: Starting a blog
audience: individual, website-creator, editor
category: website-creation
order: 1621
summary: How one person sets up a blog on Exponential with the shipped Blog and Blog post classes - where it lives in the tree, the blog's name and description, the first posts, addresses, a feed, the menu and the decisions to make before the first reader arrives.
keywords: start a blog, set up blog, Blog class, Blog post, first post, blog name, description, URL, menu, feed, comments decision, tags list, about the author
type: help
---
# Starting a blog

This page is for individuals who want to set up a blog. On the default site package the *Blog* and *Blog post*
classes are ready; setting up is mostly decisions, then a few minutes of clicking.

## Contents

1. [Decide first](#decide-first)
2. [Where the blog lives](#where-the-blog-lives)
3. [Creating the blog](#creating-the-blog)
4. [The first three posts](#the-first-three-posts)
5. [Making it findable](#making-it-findable)
6. [If your site package has no Blog class](#if-your-site-package-has-no-blog-class)
7. [Checklist](#checklist)

## Decide first

| Decision | Options | Guidance |
|---|---|---|
| What is it about? | One topic, or your life in general | One topic finds readers faster |
| How often? | Weekly, twice a month, monthly | Pick what you can keep for a year |
| Comments? | Off, signed-in readers only, everyone | Start off or signed-in only ([Comments on your blog](comments-on-your-blog.md)) |
| Tags? | A short fixed list | Write down five to ten now ([Tags and categories](tags-and-categories.md)) |
| Your name? | Real name, first name, pen name | Decide before the first post; changing later is awkward |
| Alone or with someone? | One author, or two with their own accounts | Each author gets their own account ([Roles for your team](../../customers/day-to-day/roles-for-your-team.md)) |

## Where the blog lives

| Setup | Tree | Address example |
|---|---|---|
| The blog is the whole site | Home > Blog posts directly, or Home > *Blog* | `/` or `/Blog` |
| The blog is part of a personal site | Home > About, Work, **Notes** (*Blog*) | `/Notes` |
| Two blogs (for example two languages or two topics) | Two *Blog* objects | `/Notes`, `/Travel` |

Addresses come from names ([URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md)),
so pick a short, lasting name.

## Creating the blog

1. In the administration, go to the parent folder and **Create here** > *Blog*.
2. **Name**: what readers call it. **Description**: one or two sentences on what you write about and how often.
3. **Tags** on the blog itself are optional.
4. **Metadata**: a title and description for search engines
   ([Metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md)).
5. Publish. The blog page lists its posts, newest first, once there are some.

## The first three posts

Launch with three posts, not one: a reader who likes the first wants a second.

1. **Hello**: who you are, what the blog is about, how often you write.
2. **Your best piece** on the topic.
3. **A shorter, practical post** that shows the range.

For each: **Create here** > *Blog post* below the blog, title, body, tags, decide on comments, publish
([Writing and scheduling posts](writing-and-scheduling-posts.md)).

## Making it findable

- **Menu**: make sure the blog appears in the site's top menu
  ([Menus and navigation](../../creators/website-creators/menus-and-navigation.md)).
- **Feed**: create an RSS export of the blog ([RSS feeds for your blog](rss-feeds-for-your-blog.md)).
- **Sitemap and search engines**: [Sitemaps](../../creators/website-creators/sitemaps.md),
  [SEO basics](../../creators/website-creators/seo-basics.md).
- **Tell people**: a link from your About page, your profiles elsewhere and your mail signature.

## If your site package has no Blog class

The `_clean` packages carry the same classes without demo content. An older or custom installation may lack them;
then a *Folder* holding *Article* objects works as a simple blog, or the classes can be imported from a package
([Packages](../../../guides/packages.md)). Check **Setup > Classes** first.

## Checklist

- [ ] Topic, pace, comments policy, tag list and author name decided.
- [ ] *Blog* object with a clear name and description.
- [ ] Three posts published.
- [ ] Blog in the menu.
- [ ] RSS export created and linked.
- [ ] A test on a phone.

## Related pages

- [Blogs](README.md)
- [Writing and scheduling posts](writing-and-scheduling-posts.md)
- [Blogs: start here (customers)](../../customers/types/blog-start-here.md)
- [Content calendar](../../customers/day-to-day/content-calendar.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
