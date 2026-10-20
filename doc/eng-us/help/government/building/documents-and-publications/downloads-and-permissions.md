---
title: Downloads and permissions
audience: government, admin, website-creator
category: public-sector
order: 90
summary: Who may download which document on an Exponential site - content/download checks read access, sections and roles for restricted documents, stored PDF exports that are public, images served directly, hiding and removing documents, and how to test.
keywords: downloads, permissions, content/download, read access, restricted documents, sections, roles, members area, stored PDF, storage, images, hide, test logged out
type: help
---
# Downloads and permissions

This page is for administrators and website creators who publish documents that are not for everyone, or who must be
sure that a withdrawn document cannot still be downloaded. Sources:
[Files and downloads](../../../creators/website-creators/files-and-downloads.md),
[Sections](../../../../guides/sections.md) and [PDF exports](../../../../guides/pdf-exports.md).

## Contents

1. [How a download is checked](#how-a-download-is-checked)
2. [Restricted documents](#restricted-documents)
3. [Exceptions served without a check](#exceptions-served-without-a-check)
4. [Withdrawing a document](#withdrawing-a-document)
5. [Testing](#testing)

## How a download is checked

A document's link is `content/download/<object ID>/<attribute ID>`. The view checks that the visitor may read the
object (`content/read`) and that at least one location of it is visible (unless hidden nodes are shown), then sends the file;
a version other than the published one needs `content/versionread` (`kernel/private/classes/views/content/download.php`). So a
document is exactly as public as the object that holds it: its
section, its location and the roles decide.

## Restricted documents

| Goal | How |
|---|---|
| Documents for staff or members only | a section Anonymous may not read; a role with `content/read` for that section, assigned to the group ([Sections, 13.1](../../../../guides/sections.md#131-a-members-area)) |
| Documents for one partner group | a subtree limitation on `content/read` for that group |
| A document available until a date | `hide.php` with a hide date, or move it to a closed section ([Archive and expiry](../news-and-alerts/archive-and-expiry.md)) |

Never link a stored file path (`var/.../storage/...`) directly; that bypasses the check.

## Exceptions served without a check

| Item | Why | Care |
|---|---|---|
| Stored PDF exports (generated once) | served from `var/<site>/storage/pdf/<file name>` by the web server, public | use on-the-fly exports for restricted content ([PDF export](pdf-export.md#public-or-protected)) |
| Images (image aliases) | the shipped rewrite rules serve stored images directly for speed | do not put confidential content in images |
| Static cache pages | files served without starting Exponential | only public pages are cached; pages for signed-in users are not |
| Copies in caches, CDNs, search engines, people's computers | outside the CMS | think before publishing |

## Withdrawing a document

| Need | Action | Then |
|---|---|---|
| Stop downloads now | **Hide / unhide** on the File object (and its Publication page) | the download is refused to visitors |
| Remove it | **Remove** (trash) | restorable until the trash is emptied |
| Remove the old file from an object that stays | replace the file, then remove the archived versions holding the old one ([Versions of documents](versions-of-documents.md#removing-a-file-that-must-not-stay)) | |
| A stored PDF export contains it | **Regenerate** the export or remove it | |

## Testing

- [ ] Sign out (or use a private browser window) and open the document link: a restricted document must ask for a
  sign-in or be refused.
- [ ] Sign in as a member of the group: it downloads.
- [ ] After hiding: the link is refused logged out.
- [ ] Check the stored PDF export addresses in **Setup > PDF export** for anything that should not be public.

## Related pages

- [Documents and publications](README.md)
- [Visitor permissions](../../../creators/website-creators/visitor-permissions.md)
- [Roles and policies](../../../../guides/roles-and-policies.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
