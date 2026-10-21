---
title: Comments on your blog
audience: individual, website-creator, customer
category: personal-sites
order: 1623
summary: How comments work on an Exponential blog - the shipped Comment class, the enable comments checkbox, who may comment by default, how to restrict comments to signed-in readers, holding comments for review, fighting spam, and what is not available (no ezcomments, no threads).
keywords: comments, Comment class, enable_comments, blog comments, anonymous comments, moderation, approval, spam, CAPTCHA, hCaptcha, reCAPTCHA, form token, ezcomments, permissions, content/create, personal data
type: help
---
# Comments on your blog

This page is for individuals deciding whether readers may comment on their blog, and how to keep comments under
control alone. Comments turn a blog into a conversation; they also attract spam and, sometimes, unpleasantness you
must deal with yourself.

## Contents

1. [How comments work](#how-comments-work)
2. [Who may comment by default](#who-may-comment-by-default)
3. [Three policies to choose from](#three-policies-to-choose-from)
4. [Holding comments for review](#holding-comments-for-review)
5. [Keeping spam out](#keeping-spam-out)
6. [Deleting a comment](#deleting-a-comment)
7. [What does not exist](#what-does-not-exist)
8. [Checklist](#checklist)

## How comments work

- The *Blog post* class has an **Enable comments** checkbox (`enable_comments`).
- When it is ticked, the post's full view lists the comments stored below the post and offers a **New comment**
  button to readers who are allowed to create one; readers who are not are asked to log in or create an account
  (template `full/blog_post.tpl` in the `ezwebin` and `sevenx_themes_simple` designs).
- A comment is a content object of the *Comment* class with three fields: **subject**, **author** and **message**
  (`class-comment.xml` in the `sevenx_classes` package).

Because a comment is ordinary content, everything you know about content applies: it has versions, it can be
edited, moved to the trash, searched, and controlled by roles.

## Who may comment by default

The installer of the default site package (`sevenx_multisite`) gives the **Anonymous** role permission to create
*Comment* (and *Review*) objects in the *Standard* section (`sevenx-multi-site-installer.php`, policies for the role
"Anonymous"). In practice: on a fresh default installation, **any visitor can post a comment** on a post that has
comments enabled. The *Member* role also has comment permissions.

Check what your site actually allows in **Users > Roles** > *Anonymous* before you enable comments anywhere
([Roles and policies](../../../guides/roles-and-policies.md), [Visitor permissions](../../creators/website-creators/visitor-permissions.md)).

## Three policies to choose from

| Policy | How | Suits |
|---|---|---|
| **No comments** | Leave **Enable comments** unticked on every post | A blog you write alone and do not want to moderate |
| **Signed-in readers only** | Remove the `content/create` policy for *Comment* from *Anonymous*; keep it for *Member*; let readers register | Most personal blogs: fewer bots, readers have an account |
| **Everyone** | Keep the default; add spam protection | Blogs that want quick feedback and can moderate daily |

Registration is explained in [Members and registration](../club-sites/members-and-registration.md).

## Holding comments for review

Nothing in the shipped classes holds comments for approval by itself. Two ways exist:

1. **An approval workflow** on publishing for the *Comment* class: new comments wait until you approve them
   ([Workflows](../../../guides/workflows.md), [Approvals and review](../../customers/day-to-day/approvals-and-review.md)).
   This is the real moderation queue; it needs to be set up once.
2. **Daily reading**: comments go live at once and you remove what does not belong. Simple, but bad content is
   visible until you look.

If you choose "Everyone", choose the workflow.

## Keeping spam out

| Layer | What it does | Source |
|---|---|---|
| Form tokens | Stop forged posts from other sites | `ezformtoken`, in the default installation |
| CAPTCHA field on the *Comment* class | Asks visitors to prove they are human | `recaptcha` or `hcaptcha` datatype; keys from the service |
| Signed-in only | Bots must register first | Roles |
| Approval workflow | Nothing appears without you | Workflows |

See [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md) and
[Spam and security basics](../running-it/spam-and-security-basics.md).

## Deleting a comment

Open the post in the administration, find the comment below it, and move it to the trash (or remove it at once if
it contains something you must not keep). If someone asks you to remove a comment with their personal data, do it
and empty it from the trash too ([Archiving and the trash](../../customers/day-to-day/archiving-and-the-trash.md),
[Data retention and deletion](../../businesses/security/data-retention-and-deletion.md)).

Write a short comment policy on the blog: what you remove and why. It makes the decision easier when it comes.

## What does not exist

- **No `ezcomments` extension** in this distribution; it is not in `extension/` and not in `composer.json`.
- **No threaded replies, likes, or "notify me of replies"** for comments.
- **No import of comments** from another platform except as part of a migration you script
  ([Moving from another blog platform](moving-from-another-blog-platform.md)).

If you need more, an outside comment service embedded in the template is possible, but it sends your readers' data
to that service; weigh that for a personal blog.

## Checklist

- [ ] Policy chosen: none, signed-in only, or everyone.
- [ ] *Anonymous* role checked and matches the policy.
- [ ] Approval workflow set up if everyone may comment.
- [ ] Form token active; CAPTCHA on the *Comment* class if you allow anonymous comments.
- [ ] A comment policy published.
- [ ] You know how to remove a comment completely.

## Related pages

- [Blogs](README.md)
- [Blogs: start here (customers)](../../customers/types/blog-start-here.md)
- [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)
- [Visitor permissions](../../creators/website-creators/visitor-permissions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
