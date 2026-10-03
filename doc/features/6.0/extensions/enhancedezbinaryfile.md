# enhancedezbinaryfile: a file datatype with information collection

This page is for site builders who need forms where visitors attach a file. `enhancedezbinaryfile` ("Enhanced
eZBinary File Type") is a file upload datatype, shown as **Enhanced File** in the class editor, built for
**information collection** (forms that visitors fill in).

It differs from the kernel's `ezbinaryfile` in three ways:

- it keeps uploaded files in a download folder, with a limit;
- it mails collected information as a multi-part mail when a file is attached;
- it offers a template operator that tests whether the file still exists.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `module.ini` | `RemoveFiles` | `MaxFiles` | `25` | Number of uploaded files kept on the server. Disk space needed is at most `MaxFiles` times `upload_max_filesize`. `0` keeps an unlimited number, which is not advisable for a site with anonymous uploads. |
| `module.ini` | `RemoveFiles` | `DownloadPath` | `original/attachments` | Where files are kept. When a mail fails, the files are stored below `var/<siteaccess>/storage/original/collected/<filetype>`. |

## Use it in a template

Test a file before you link to it with the `filecheck` operator:

```
{if eq($attribute.data_text|filecheck,true)}...{/if}
```

The multi-part mail is sent only when there is an attachment.

## What changed in the Exponential 6 releases

| Version | Change |
|---|---|
| 4.4.0, 4.4.1 (January 2024, 27 September 2026) | `composer.json`; the extension states its version, license and website. |
| 4.4.2 | Every visible text is a translation string, with German. "%filename successfully uploaded." is one message instead of the file name followed by a fragment. |
| 4.4.3 | The about page names the extension "Enhanced eZBinary File Type" (it showed the directory name); the description names Exponential. |
| 4.4.4 | Copyright notices name 1998 - 2026 7x & Exponential Foundation first. |

## Related pages

- [Chronicle](../../../history/extensions/enhancedezbinaryfile.md) and [release notes](../../../changelogs/extensions/enhancedezbinaryfile.md)
- [Change ledger](../../../history/ledger/enhancedezbinaryfile.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
