---
title: Sharing files with partners
audience: businesses, admin, partner
category: business-solutions
order: 40
summary: How an Exponential extranet shares price lists, manuals, contracts and marketing material with partners and customers, lets them upload files safely into their own area, keeps downloads permission-checked, and shows who downloaded what.
keywords: file sharing, partner files, downloads, uploads, content/download, price lists, manuals, contracts, partner upload, content/create, File class, enhancedezbinaryfile, information collector, audit, upload checks, extranet documents
type: help
---
# Sharing files with partners

This page is for portal owners who replace mail attachments and file transfer links with a portal. It explains how
files are offered to partners, how partners can send files back, what keeps the wrong person from getting a file,
and what the organization can find out later.

## Contents

1. [Why a portal instead of attachments](#why-a-portal-instead-of-attachments)
2. [Offering files to partners](#offering-files-to-partners)
3. [Letting partners upload](#letting-partners-upload)
4. [Files through forms](#files-through-forms)
5. [What keeps files safe](#what-keeps-files-safe)
6. [Knowing what happened](#knowing-what-happened)
7. [Large files](#large-files)
8. [Checklist](#checklist)
9. [Related pages](#related-pages)

## Why a portal instead of attachments

| Attachments and transfer links | A portal on Exponential |
|---|---|
| A forwarded mail reaches anyone | a download checks that the person may read the file |
| Old versions stay in inboxes | the current file replaces the old one at the same address; versions are kept |
| No record of who got what | sign-ins and, if switched on, downloads are recorded in the audit trail |
| Access ends only when the link expires | removing a group's access ends it at once |

## Offering files to partners

Files are content objects of the class **File** (`ezbinaryfile`), placed in a folder of the partner area. Every
download goes through `content/download`, which checks that the person may read the object
([Files and downloads](../../creators/website-creators/files-and-downloads.md#how-a-download-works)).

| File | Place it in | Who can download |
|---|---|---|
| Brochure for all partners | *Partner material* section | every partner |
| Price list for a tier | the tier's section | partners of that tier |
| A contract for one partner | that partner's folder (subtree) | that partner's group only |

The structure is explained in [Customer and partner areas](customer-and-partner-areas.md). Download links should show
format and size, and every protected download should be tested as a user without access
([Files and downloads, Checklist](../../creators/website-creators/files-and-downloads.md#checklist)).

## Letting partners upload

A partner who must send documents back (certificates, signed forms, artwork) can upload into its own folder:

- A role with `content/create` limited to the **File** class (Class limitation) and assigned to the partner's group
  with a Subtree limitation on its folder.
- `content/edit` with `Owner = Self` if partners may replace what they uploaded, and nothing else.
- No `content/remove`, so that a partner cannot delete what your staff rely on.

Uploads are checked: since 12 October 2026 images are checked by their content against
`image.ini [ImageUploadSettings] AllowedMIMETypes[]`, for every upload path
([Behaviour changes 2026-10](../../../bc/6.0/behaviour-changes-2026-10.md)). Upload size is limited by PHP's
`upload_max_filesize` and `post_max_size`.

> **Tip**
> To be told when a partner uploads, staff follow the partner folders with **Notify me**
> ([Notifications for staff](../intranets/notifications-for-staff.md)). An *Approve* workflow can also hold partner
> uploads until staff accept them ([Approval workflows](../intranets/approval-workflows.md)).

## Files through forms

When partners send a file with a form rather than as content, the `enhancedezbinaryfile` extension provides a file
field for information collection: it keeps uploaded files in a download folder with a limit and mails collected
information with the file attached ([enhancedezbinaryfile](../../../features/6.0/extensions/enhancedezbinaryfile.md),
[Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).

## What keeps files safe

- **Permission check on every download.** Never link files directly from the storage directory; that bypasses the
  check ([Files and downloads, Protected downloads](../../creators/website-creators/files-and-downloads.md#protected-downloads)).
- **Content checks on uploads**, as above.
- **Form tokens** on every form ([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)).
- **HTTPS** for the portal ([Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md)).
- **Two-step sign-in** for partner accounts if the files are sensitive
  ([Two-factor and sign-in](../intranets/two-factor-and-sign-in.md)).
- **Backups** that include the storage directory ([Backups that restore](../reliability/backups-that-restore.md)).

## Knowing what happened

The audit trail records content changes, sign-ins and refusals by default. Its `read` channel records sampled node
views, searches and downloads, only when reads are switched on
([The audit trail, What is recorded](../../../features/6.0/audit-trail.md#what-is-recorded)). Decide with your data
protection contact whether a portal needs download records, for how long, and say so in the portal's privacy notice
([GDPR basics](../security/gdpr-basics.md)).

Each File object keeps its versions, its owner and its dates, so "which price list did Partner A have in March?" can
be answered from the versions page ([Versions of an object](../../../guides/content-history.md)).

## Large files

PHP's upload limits apply to editors and partners alike. Very large files such as videos are better hosted on a
service built for them and linked from the portal
([Files and downloads, Large files](../../creators/website-creators/files-and-downloads.md#large-files)).

## Checklist

- [ ] Each file area belongs to the right section or partner subtree.
- [ ] Downloads tested as a partner without access.
- [ ] Partner upload role limited to the File class and the partner's folder, without remove.
- [ ] Staff follow partner upload folders.
- [ ] Upload limits agreed.
- [ ] Decision on download records documented.
- [ ] Backups include stored files.

## Related pages

- [Documents and file libraries](../intranets/documents-and-file-libraries.md)
- [Files and downloads](../../creators/website-creators/files-and-downloads.md)
- [Dealer and supplier portals](dealer-and-supplier-portals.md)
- [Data retention and deletion](../security/data-retention-and-deletion.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
