---
title: Writing and scheduling posts
audience: individual, editor, customer
category: personal-sites
order: 1622
summary: How one person writes, publishes and schedules blog posts on Exponential - the Blog post fields, drafts and preview, the publication date field, publishing at a later date with the Wait until date workflow, unpublishing at a date, and why all scheduling depends on cron.
keywords: blog post, writing, publishing, schedule, publish later, publication date, unpublish date, Wait until date, workflow, cron, drafts, preview, versions, editorial calendar
type: help
---
# Writing and scheduling posts

This page is for individuals who write their own blog posts and want some of them to appear while they are busy or
away. General writing advice is in [Writing for your own site](../personal-sites/writing-for-your-own-site.md).

## Contents

1. [The fields of a post](#the-fields-of-a-post)
2. [Writing a post](#writing-a-post)
3. [Scheduling: what is possible](#scheduling-what-is-possible)
4. [Publishing later](#publishing-later)
5. [Taking a post down at a date](#taking-a-post-down-at-a-date)
6. [A pace you can keep](#a-pace-you-can-keep)
7. [Checklist per post](#checklist-per-post)

## The fields of a post

| Field | Identifier | Use |
|---|---|---|
| Title | `title` | What the post is about; becomes the address |
| Body | `body` | The text, with headings, lists, links and images |
| Publication date | `publication_date` | The date shown on the post |
| Unpublish date | `unpublish_date` | Optional: when the post should be taken down |
| Tags | `tags` | Topics ([Tags and categories](tags-and-categories.md)) |
| Enable comments | `enable_comments` | Checkbox ([Comments on your blog](comments-on-your-blog.md)) |
| Metadata | `metadata` | Search engine title and description |

Source: `class-blog_post.xml` in the `sevenx_classes` package.

## Writing a post

1. Go to your *Blog* and **Create here** > *Blog post*.
2. Write the title and the body. Use **Store draft** often; a draft is private.
3. Add tags and decide on comments.
4. **Preview** to see it as readers will.
5. **Publish**. It appears at the top of the blog and in the feed.

Changing a published post creates a new version; the old one stays and can be restored
([Publishing and versions](../../publishing-and-versions.md), [Drafts, versions and undo](../../customers/day-to-day/drafts-versions-and-undo.md)).

## Scheduling: what is possible

| You want | What does it | Needs |
|---|---|---|
| A post that appears on a date | The *Wait until date* workflow event | Set up once by the administrator; cron |
| A post that disappears on a date | The `unpublish.php` cronjob with the `unpublish_date` field | Enabled in `settings/content.ini [UnpublishSettings]`; cron |
| A post that is hidden on a date | The `hide.php` cronjob | `settings/content.ini [HideSettings]`; cron |
| A date shown on the post | The `publication_date` field | Nothing; it is only displayed |

Source and detail: [Scheduled publishing](../../customers/day-to-day/scheduled-publishing.md).

> **Note**
> Filling in `publication_date` alone does not hold a post back: the field is shown on the post, but publishing
> still happens when you press **Publish**. Holding back needs the workflow.

## Publishing later

On a site you run yourself, you are the administrator:

1. Set up a workflow with the *Wait until date* event for the *Blog post* class and connect it to the
   "before publishing" trigger ([Workflows](../../../guides/workflows.md)).
2. Make sure cron runs the `frequent` part, which includes the workflow cronjob ([Cronjobs](../../../guides/cronjobs.md)).
3. Write the post, set the date, and press **Publish**. The version waits; it is published at the first cron run
   after the date.

Test it with a post dated ten minutes ahead before you rely on it for a holiday.

## Taking a post down at a date

Useful for a call for entries, an event announcement or a temporary offer. Fill in the `unpublish_date`; the
`unpublish.php` cronjob moves the post to the trash after the date, from where it can be restored. It must be
enabled for the class in `settings/content.ini [UnpublishSettings]`.

## A pace you can keep

- Decide a rhythm (weekly, every other week) and write it in a [content calendar](../../customers/day-to-day/content-calendar.md).
- Keep two or three posts as drafts or scheduled, so a busy week does not break the rhythm.
- Batch the work: write two posts on a quiet evening, schedule one.
- A late post is better than a rushed one; an honest "back in two weeks" post is better than silence.

## Checklist per post

- [ ] A title that says what the post is about.
- [ ] A first paragraph that works alone (it is what feeds and search results show).
- [ ] Headings and short paragraphs.
- [ ] Alternative text for every image.
- [ ] Existing tags, not new spellings of old ones.
- [ ] Comments on or off, deliberately.
- [ ] Preview checked; if scheduled, the date is right and cron runs.

## Related pages

- [Blogs](README.md)
- [Scheduled publishing](../../customers/day-to-day/scheduled-publishing.md)
- [Writing for your own site](../personal-sites/writing-for-your-own-site.md)
- [The writing assistant](../running-it/writing-assistant-expai.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
