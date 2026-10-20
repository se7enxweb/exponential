---
title: File attachments in forms
audience: government, website-creator, admin
category: public-sector
order: 60
summary: How visitors can attach a file to a form in Exponential with the Enhanced File datatype of enhancedezbinaryfile, its storage limit and mail behavior, the risks of accepting files from the public, and when to use a dedicated system instead.
keywords: file upload, attachment, form, enhancedezbinaryfile, Enhanced File, MaxFiles, DownloadPath, filecheck, upload risks, malware, personal data, information collector
type: help
---
# File attachments in forms

This page is for website creators and administrators who need visitors to attach a file to a form, for example a
photo of a damaged road sign or a supporting document. The kernel's File datatype (`ezbinaryfile`) is for editors; for
information collection Exponential uses the **Enhanced File** datatype of the `enhancedezbinaryfile` extension
([enhancedezbinaryfile](../../../../features/6.0/extensions/enhancedezbinaryfile.md)). Accepting files from anyone
on the internet is a risk; read the whole page before switching it on.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What the datatype does](#what-the-datatype-does)
2. [Setting it up](#setting-it-up)
3. [Risks and measures](#risks-and-measures)
4. [When to use another system](#when-to-use-another-system)

## What the datatype does

| Behavior | Detail |
|---|---|
| Upload in a collector attribute | shown as **Enhanced File** in the class editor |
| Storage with a limit | `module.ini [RemoveFiles] MaxFiles=25`: only that many uploaded files are kept; `DownloadPath=original/attachments` |
| Mail | the collected information is mailed as a multi-part mail with the file attached, when there is an attachment |
| Failed mail | files are stored below `var/<siteaccess>/storage/original/collected/<filetype>` |
| Template check | the `filecheck` operator tests whether a file still exists before linking it |

`MaxFiles=0` keeps an unlimited number of files, which the extension's own documentation advises against for sites
with anonymous uploads. The default installation activates the extension
([Extensions it activates](../../../site-packages/sevenx-multisite/extensions-it-activates.md)).

## Setting it up

1. In the form class (**Setup > Classes**), add an attribute of the datatype **Enhanced File**, tick
   **Information collector**, and decide whether it is required.
2. Set the limits in `settings/override/module.ini.append.php` (`[RemoveFiles] MaxFiles`), and the PHP upload limit
   (`upload_max_filesize`) on the server. Disk space needed is at most `MaxFiles` times `upload_max_filesize`.
3. Make sure the form template posts with `enctype="multipart/form-data"`.
4. Test: send a file logged out, check the mail with the attachment (file transport first,
   [Mail to receivers](mail-to-receivers.md)) and the stored file.

## Risks and measures

| Risk | Measure |
|---|---|
| Malware in uploaded files | scan files on the server (an antivirus on the storage path or the mail gateway); never open attachments on unprotected workstations |
| Files used to store or distribute illegal content | keep `MaxFiles` small; files are not public, they are kept in storage and mailed; check that the download path is not served by the web server |
| Personal data in documents (IDs, medical letters) | ask only for what you need; say so on the form; remove files once processed |
| Very large files | PHP and web server upload limits |
| Spam with attachments | form tokens, moderation, a CAPTCHA after assessment ([Spam protection and third parties](spam-protection-and-third-parties.md)) |
| Mail size limits | the receiving mail server may refuse large attachments; test with the largest allowed file |

Exponential sanitizes SVG uploads and refuses dangerous file names such as `shell.php.jpg` in the editor's upload
([Content model and editing](../../../../guides/content-model-and-editing.md#4-the-online-editor-and-tinymce-8),
[Security standards](../../../../standards/security.md)); it does not scan files for malware.

## When to use another system

| Situation | Use |
|---|---|
| Applications with several required documents | the case or application system |
| Documents with special categories of personal data | a system approved for that data |
| Many uploads a day | a system with scanning, quotas and case handling |
| Uploads that must be kept as records | a records or case system, not mail boxes |

For most public forms, asking people to describe the problem and offering a separate route for documents is safer
than accepting uploads.

## Related pages

- [Building forms](README.md)
- [Retention of collected information](retention-of-collected-information.md)
- [Files and downloads](../../../creators/website-creators/files-and-downloads.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
