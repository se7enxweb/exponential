---
title: Masking in logs and the log viewer
audience: government, admin
category: public-sector
order: 70
summary: For administrators of public sites - how Exponential masks e-mail addresses, IP addresses and secrets in request logs and in the log viewer, who may see unmasked values, and how to keep screenshots, downloads and support reports free of personal data.
keywords: log masking, redaction, MaskIP, MaskEmails, MaskIPs, MaskSecrets, RedactionSettings, setup/unmasklogs, Show unmasked, log viewer, request logs, support reports, personal data, public sector
type: help
---
# Masking in logs and the log viewer

This page is for administrators. Logs are full of personal data: addresses, e-mail addresses in URLs, user agents.
Exponential masks them in two places, when writing request logs and when showing any log
([Log viewer: Privacy](../../../features/6.0/log-viewer.md#privacy-what-is-masked),
[Request logs](../../../features/6.0/request-logs.md)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [When writing](#when-writing)
2. [When showing](#when-showing)
3. [Unmasked access](#unmasked-access)
4. [Good practice](#good-practice)
5. [Related pages](#related-pages)

## When writing

`site.ini [RequestLogSettings]` for `404.log` and `security.log`:

- `MaskIP=enabled` (default): the client address with its host part hidden (`203.0.x.x`, `2001:db8:x:x`); the raw
  address is never written to a file. `disabled` writes it in full, "only for a site that has a reason and a legal
  basis for it".
- In `404.log` every query value is replaced by `***`; in `security.log` query values are kept as evidence, with
  secrets, e-mail and IP addresses in them masked.
- User agents: a short hash and the first 60 characters.

Other kernel logs (`error.log`, `debug.log` ...) are written as the code writes them; they are masked when shown.

## When showing

`settings/logviewer.ini [RedactionSettings]`:

| Setting | Masks |
|---|---|
| `MaskEmails` | `jane.doe@example.com` becomes `j***@***.com` |
| `MaskIPs` | `203.0.113.7` becomes `203.0.x.x`; IPv6 keeps its first two groups |
| `MaskSecrets` | passwords in addresses, `password=`, `token=`, `api_key=` values, Bearer and Basic credentials, JSON web tokens, session ids and cookies, long keys and hashes |
| `SecretSettings[]`, `SecretGroups[]` | always masked, whatever the switches |

Downloads from the viewer are masked the same way.

## Unmasked access

A user with the policy `setup/unmasklogs` can switch **Show unmasked** on for themselves; it applies to the page, its
polls and its downloads, and the audit records that the file was read unmasked. Give the policy to very few people,
for investigations.

## Good practice

- Make screenshots and support reports with masking on, as the log viewer's documentation advises.
- When you send a log to a supplier, send a masked download, not the raw file.
- Web server access logs are outside Exponential and usually contain full addresses: set their retention and masking
  in the web server or with your host.

## Related pages

- [The log viewer](../../../features/6.0/log-viewer.md)
- [Request logs](../../../features/6.0/request-logs.md)
- [Log retention](log-retention.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
