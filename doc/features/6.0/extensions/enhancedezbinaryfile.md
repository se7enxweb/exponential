# enhancedezbinaryfile: a file datatype with information collection

`enhancedezbinaryfile` ("Enhanced eZBinary File Type") is a file upload datatype (name in the class editor: **Enhanced File**) built for
**information collection**: forms that visitors fill in and that carry a file. It differs from the kernel's `ezbinaryfile` by keeping
uploaded files in a download folder with a limit, mailing collected information as a multi-part mail when a file is attached, and offering
a template operator to test whether the file still exists.

From the extension's own documentation:

* `module.ini [RemoveFiles] MaxFiles` is the number of uploaded files kept on the server; the disk space needed is at most
  `MaxFiles` times `upload_max_filesize`. `0` keeps an unlimited number, which is not advisable for a site with anonymous uploads.
* `[RemoveFiles] DownloadPath` (default `original/attachments`) is where files are kept; when a mail fails the files are stored below
  `var/<siteaccess>/storage/original/collected/<filetype>`.
* The `filecheck` operator tests a file before you link to it: `{if eq($attribute.data_text|filecheck,true)}...{/if}`.
* The multi-part mail is sent only when there is an attachment.

## What changed in the Exponential 6 releases

* 4.4.0, 4.4.1 (January 2024, 27 September 2026): `composer.json`; the extension states its version, license and website.
* 4.4.2: every visible text is a translation string with German; "%filename successfully uploaded." is one message instead of the file
  name followed by a fragment.
* 4.4.3: the about page names the extension "Enhanced eZBinary File Type" (it showed the directory name); the description names Exponential.
* 4.4.4: copyright notices name 1998 - 2026 7x & Exponential Foundation first.

## Related

* [Chronicle](../../../history/extensions/enhancedezbinaryfile.md) and [release notes](../../../changelogs/extensions/enhancedezbinaryfile.md)
* [Change ledger](../../../history/ledger/enhancedezbinaryfile.md)
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
* [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
