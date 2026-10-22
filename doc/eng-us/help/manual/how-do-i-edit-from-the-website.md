---
title: How do I edit content from the website itself?
audience: editor
category: manual
order: 465
summary: On sites whose design shows the website toolbar, sign in on the public site; the toolbar above each page creates, edits, moves and removes content in place, adds locations, sets the sorting, and in edit mode publishes, stores or discards the draft; copying, the trash and most settings stay in the administration.
keywords: website toolbar, front-end editing, ezwt, create here, edit, move, remove, add locations, sorting, store draft, manage versions, translate, import, export, replace
type: help
---
# How do I edit content from the website itself?

Some site designs show a **website toolbar** at the top of each page when an editor is signed in on the public site.
It works on the page you are looking at, so you can fix a typo or add an article where you see it, without opening the
administration. The toolbar comes from the extension *ezwt* and appears in the designs that include it (the classic
website designs do); a site built on another design may not show it.

## Who can do this

Editors whose role has the policy `websitetoolbar/use` (it can be limited to some classes), plus the usual
`content/create`, `content/edit` and `content/remove` rights for what they change. Sign in on the public site with your
usual account.

## Steps

1. Sign in on the public site and open the page you want to work on.
   The toolbar appears above the page.
2. Use its buttons on the current page:
   - **Create here**: choose a class in the list next to it, then click the button; the new item is created below
     the current page;
   - **Edit**: edit the current page;
   - **Move** and **Remove**: move the page elsewhere or remove it (it goes to the trash);
   - **Add locations**: show the page in another place as well;
   - **Sorting**: change the order of the items below the current page.
3. In the edit form, the toolbar offers **Send for publishing**, **Store draft**, **Store draft and exit**,
   **Discard draft**, **Preview** and **Manage versions**, and **Translate** with **Translate from** for a
   translation.
4. When you are done, click **Send for publishing**, or **Store draft and exit** to finish later.

On classes set up for it, the toolbar also has **Import**, **Replace** and **Export** for word processor documents:
see [How do I import or export a word processor document?](how-do-i-import-or-export-a-text-document.md)

## What can go wrong

- **No toolbar appears.** You are not signed in, your role lacks `websitetoolbar/use` for this class, or the site's
  design does not include the toolbar. Use the administration instead.
- **A button is missing.** The toolbar offers only what your role allows on this page; **Create here** lists only
  classes you may create there.
- **You need to copy, restore from the trash or remove a version.** These are done in the administration. The toolbar's
  link to the administration (if your site has one) opens the same page there.
- **A draft is left behind.** Leaving the edit form with the browser's Back button keeps the draft. Use the toolbar's
  buttons, and tidy **My drafts** now and then.

## Related pages

- [Creating and editing content](../editing-content.md)
- [What do I do about a "Possible edit conflict"?](what-do-i-do-about-a-possible-edit-conflict.md)
- [How do I change the order of sub items?](how-do-i-change-the-order-of-sub-items.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
