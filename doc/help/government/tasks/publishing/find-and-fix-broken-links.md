---
title: Find and fix broken links
audience: government, editor
category: public-sector
order: 440
summary: For editors and web officers - find links marked invalid in Setup > Link management, see which pages use them, fix the address once for every page or edit the pages, and know how and when the link check runs.
keywords: broken links, link check, Link management, url/list, invalid links, linkcheck.php, Used by, Edit address, Mark selected valid, external links
type: help
---
# Find and fix broken links

This page is for editors and web officers who keep links working. Links to other sites break when those sites change.
Exponential checks links with a cronjob and lists them in **Setup > Link management** (`/url/list`)
([URLs and aliases, 1 and 2](../../../../guides/urls-and-aliases.md#1-the-link-list)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- You can open **Setup > Link management** (ask the web team for access, or for a monthly list).
- The link check runs: it is the `linkcheck.php` script in the infrequent cronjob part. The page's **Last link check**
  figure says when it last ran.

## Steps

1. **Setup > Link management**. Click the **Invalid** figure (or **Show** > Invalid).
2. For each card: the address, its badges, **Checked** time, and **Used by** (up to three pages, and "and N more"
   leading to the link's own page with every version that uses it).
3. Open the address (**Open**) to see what is wrong: gone, moved, or temporarily down.
4. Fix it:
   - the address moved and every page should use the new one: **Edit** on the card changes the address in one place
     for every object that uses it;
   - the link should go: edit each page in **Used by** and remove or replace the link;
   - the site refuses automated checks but works for people: **Mark selected valid** (the next check may change it back).
5. Links inside the site to content that was removed or hidden show as invalid too: link to the replacement page.

## Check the result

- [ ] The card's state changes after the next link check (or mark it by hand).
- [ ] The pages in **Used by** work when you click the link signed out.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Treating "not decided" as broken | 401, 403, 429 and 503 answers are not decided; check by hand |
| Editing the address when only one page should change | **Edit** changes it for every page; edit the page instead |
| Removing pages without redirects | creates broken links elsewhere ([Redirect old addresses after a reorganization](redirect-old-addresses-after-a-reorganization.md)) |

## Related pages

- [Publishing tasks](README.md)
- [Add a link to an external service](add-a-link-to-an-external-service.md)
- [Link and content checks](../../../creators/website-creators/link-and-content-checks.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
