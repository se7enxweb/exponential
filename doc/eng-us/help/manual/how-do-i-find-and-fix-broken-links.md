---
title: How do I find and fix broken links?
audience: admin, editor
category: manual
order: 515
summary: Setup > Link management lists every link in the content with its last check; show the invalid ones, see which objects use each, and fix the link in those objects or in one place with Edit.
keywords: broken links, link check, link management, invalid links, url/list, linkcheck cronjob, 404
context: url/list
type: help
---
# How do I find and fix broken links?

Exponential keeps one list of every link in the content and checks them with a cronjob. Fix a broken link once and
every object that uses it is fixed.

## Who can do this

Users whose role has access to the `url` module (administrators). Editors who fix the content need `content/edit` on
the objects.

## Steps

1. Open **Setup** > **Link management**.
   The page **All links** shows an overview and the date of the last link check.
2. Under **Show**, click **Invalid** (or the **Invalid** figure of the overview).
   The page lists the links that failed. **Find a link** narrows it to a domain or a path.
3. Click **View** next to a link to see the objects that use it (**Used by**).
4. Fix it:
   - if the address moved, click **Edit** and enter the new address: every object that uses the link changes;
   - if the page is gone, edit the objects and remove or replace the link.
5. If a link is fine but the check fails (a site that refuses checks), tick it and click **Mark selected valid**.

![All links with the overview, the search form and the list of links with View, Edit and Open](images/link-management.webp)

## What can go wrong

- **"The link check has not run yet".** The link check cronjob does not run. Ask the person who runs the server.
- **A link to this site is marked invalid.** The page was moved or renamed and has no address alias for the old path.
  Link to the object with **Browse** in the editor, which follows moves.
- **Broken relations, not links.** Embedded objects and relation fields that point to trashed content are listed under
  **Setup** > **Broken relations**: see [What does the content check find?](what-does-the-content-check-find.md)

## Related pages

- [Link and content checks](../creators/website-creators/link-and-content-checks.md)
- [How do I add a link in the Online Editor?](how-do-i-add-a-link-in-the-online-editor.md)
- [URLs and aliases](../../guides/urls-and-aliases.md)
- [User manual](README.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
