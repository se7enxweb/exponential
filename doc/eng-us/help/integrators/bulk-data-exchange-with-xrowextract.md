---
title: Bulk data exchange with xrowextract
audience: integrator, admin, developer
category: integration
order: 140
summary: Using the xrowextract extension as a file-based integration - exports of one or many classes as CSV, JSON or XML, imports with a dry run, content packages, schedules with delta runs, delivery to SFTP, FTP, S3, WebDAV or an HTTP endpoint with a signed webhook, and the command line.
keywords: xrowextract, export, import, CSV, JSON, XML, schedule, delta, changed-since, destination, SFTP, FTPS, S3, WebDAV, HTTP POST, webhook, X-Xrowextract-Signature, presets, jobs, file-based integration
type: help
---
# Bulk data exchange with xrowextract

This page is for integrators whose partner system exchanges **files**: a nightly product file from an ERP, a weekly
export for a data warehouse, a spreadsheet round trip with an agency. The `xrowextract` extension (required as
`~2.5.18`) is the **Export** tab of the administration. It exports and imports content as files, runs on a schedule
and delivers the files to other systems, without a line of custom code. The full feature page is
[xrowextract](../../features/6.0/extensions/xrowextract.md), the reference the
[xrowextract specification](../../specifications/6.0/xrowextract.md).

## Contents

1. [What it covers](#what-it-covers)
2. [Exports](#exports)
3. [Imports](#imports)
4. [Schedules and destinations](#schedules-and-destinations)
5. [The command line](#the-command-line)
6. [Security notes](#security-notes)
7. [xrowextract or your own code](#xrowextract-or-your-own-code)

## What it covers

| Task | Where in the administration |
|---|---|
| One class as CSV, JSON or XML, below a node or across the site, with filters and languages | **One class of content export** (`/xrowextract/csv`) |
| Every class below chosen nodes as one ZIP or TAR archive | **Multi class of content export** (`/xrowextract/archive`) |
| A CSV, JSON or XML file into content objects, with a column mapping, a preview (dry run) and apply | **Import content file** |
| Inspect, compare, install and export content packages | **Import content package** (`/xrowextract/package`) |
| Run exports and imports on a schedule, in full or as a delta, and deliver the file | **Schedules** |
| Background runs with progress, logs and cancel | **Jobs** |

## Exports

Columns are the class attributes plus special columns: object id, **remote id**, main node, parent node, URL alias,
dates, and for users login and e-mail. Filters cover dates, section, state, visibility, name, owner, depth and any
class attribute, joined with and/or. A saved **preset** turns a selection into a reusable, named export with
`{placeholders}`. Export the remote id column whenever another system will match the rows back to its own records.

## Imports

1. Upload the file (chunked and resumable, any size) and choose the class, the parent node and the language.
2. Map the columns to attributes; the page lists every column and element the importer understands.
3. Press **Preview**: a dry run that names each change and writes nothing.
4. Press **Apply**. A large import runs as a background job with a resume point.

An imported file URL (for images and files referenced by address) must be of the site's own scheme, host and port.
Scheduled imports always do a dry run first and are applied only when it found no errors.

## Schedules and destinations

A **schedule** runs a preset, a site archive, an **Export as package** or an import from a folder or a destination,
hourly, daily, weekly, monthly or on a five-field cron expression, in full or as a **delta** (only changes since the
last successful run). Start due schedules with the cronjob part:

```bash
php runcronjobs.php xrowextract
```

| Destination | Notes |
|---|---|
| SFTP | System OpenSSH client; key or password; the host key is trusted explicitly |
| FTP / FTPS | |
| Local or NAS folder | Only below `xrowextract.ini [Destinations] LocalPathRoots[]` |
| S3-compatible storage | Signature version 4 |
| WebDAV | |
| HTTP POST | Optionally a signed webhook |

With `xrowextract.ini [Notifications] WebhookSecret` set, the webhook carries
`X-Xrowextract-Signature: sha256=<HMAC-SHA256 of "<timestamp>.<sha256 of the body>">`, so the receiver can check the
sender. Notifications go by e-mail on failure (always to the owner), optionally on success, and to the webhook.
**History** lists every run with who, what, rows, size, checksum, delivery and warnings.

## The command line

Every function has a command, as `php extension/xrowextract/bin/php/<name>.php` or through the console:

| Command | What it does |
|---|---|
| `ext:xrowextract:csv` | One class as a file (`--format`, `--where`, `--changed-since`, `--preset`, `--preview`, `--user`) |
| `ext:xrowextract:archive` | A site archive |
| `ext:xrowextract:import` | An import of any size, streamed, optionally as a background job |
| `ext:xrowextract:package` | Content packages: inspect, install, export, compare |
| `ext:xrowextract:schedule` | List, run, enable, disable schedules; print crontab lines |
| `ext:xrowextract:destination` | List and test destinations, send a file, trust a host key |
| `ext:xrowextract:requirements` | What this server provides for the tool |

```bash
# articles changed in the last 30 days that are not hidden, as JSON
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 --format=json \
  --visibility=visible --date-field=modified --date=30 --output=var/tmp/recent.json --allow-root-user
```

`--user=<login>` exports with that login's read access (default `admin`): give a service account instead, so the
export cannot contain more than it should.

## Security notes

- Destination credentials are encrypted with libsodium; the key file is generated with mode 0600 at
  `settings/override/xrowextract-secrets.key`. Never commit it; losing it means entering the secrets again.
- Exports can hold personal data (user columns, collected answers): deliver them only to destinations that may
  hold such data, and set a retention on the receiving side.
- Limit the policies of `xrowextract` to the people who run exchanges; read them in the feature page's **Policies**
  section.

## xrowextract or your own code

| Choose `xrowextract` when | Write your own import when |
|---|---|
| The partner exchanges flat files with one row per object | Records must be transformed, merged or looked up in other systems first |
| Editors or administrators should be able to adjust the mapping | The mapping is part of a tested, versioned integration |
| Delivery to SFTP, S3 or HTTP is all that is needed | You need idempotent updates keyed by your own `remote_id` scheme with custom rules ([how](writing-an-import-script.md)) |

## Related pages

- [xrowextract](../../features/6.0/extensions/xrowextract.md)
- [Packages for import and export](packages-import-and-export.md)
- [Writing an export or an outbound sync](writing-an-export-or-outbound-sync.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
