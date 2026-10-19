---
title: Sharing files with contractors
audience: government, editor, admin
category: public-sector
order: 50
summary: How to share documents with contractors and partners through an Exponential extranet so only they can download them: files in the party's section, downloads checked by content/download, uploads by contractors, and what not to do.
keywords: file sharing, contractors, protected downloads, content/download, extranet files, uploads, confidentiality, document exchange
type: help
---
# Sharing files with contractors

This page is for editors and administrators. Files are content objects with a file field; downloads go through
`content/download`, which checks that the visitor may read the object before it sends the file, so a file in a section
only a contractor's group may read can only be downloaded by that group
([Files and downloads](../../../creators/website-creators/files-and-downloads.md#protected-downloads)).

## Contents

1. [Publishing a file for one party](#publishing-a-file-for-one-party)
2. [Uploads by contractors](#uploads-by-contractors)
3. [What not to do](#what-not-to-do)
4. [Large and sensitive files](#large-and-sensitive-files)

## Publishing a file for one party

1. Open the party's folder in **Content structure** (it is in the party's section).
2. Under **Sub items**, **Create new**, **File**; upload; **Send for publishing**.
3. Test the download with a test account of that party, and signed out (it must be refused).

## Uploads by contractors

A role with `content/create` for the File class, limited to the party's subtree, lets contractors upload deliverables.
Add an approval workflow on that section if a project manager must accept files before others in the group see them.

## What not to do

- Do not link files from `var/.../storage/` directly; that bypasses the permission check.
- Do not put files for different parties in the same folder.
- Do not use the public site's media library for confidential files: it is usually readable by visitors.

## Large and sensitive files

Upload limits come from PHP (`upload_max_filesize`, `post_max_size`). For classified or very large material use the
channel your security rules prescribe; an extranet built with Exponential is a web publishing system, not a secure file
transfer service.

## Related pages

- [Files and downloads](../../../creators/website-creators/files-and-downloads.md)
- [User groups and sections for extranets](user-groups-and-sections-for-extranets.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
