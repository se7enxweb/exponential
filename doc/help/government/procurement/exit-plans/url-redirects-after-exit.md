---
title: Keeping public addresses working after an exit
audience: government, project-manager, admin, developer
category: public-sector
order: 90
summary: Why the public addresses of a government site must keep working after a change of system, where Exponential keeps every address (main aliases in the site archive, other aliases and wildcards in two tables), how to build and install the redirect table, and how to test it before and after the switch.
keywords: redirects, URL redirects, 301, permanent redirect, old addresses, link rot, URL aliases, wildcards, ezurlalias_ml, ezurlwildcard, migration, SEO, printed links
type: help
---
# Keeping public addresses working after an exit

This page is for project managers and web teams who move a public site to another system or another domain. The
addresses of a government site appear in printed letters, forms, laws and other organizations' pages; a broken
address sends citizens to an error page. A redirect table maps every old address to the address of the same content
on the new system. The sources are [Leaving Exponential, section 4](../../../../install/migration/leaving-exponential.md#4-addresses-for-the-redirects)
and [Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md).

## Contents
1. [Where Exponential keeps addresses](#where-exponential-keeps-addresses)
2. [Building the redirect table](#building-the-redirect-table)
3. [Installing it on the new system](#installing-it-on-the-new-system)
4. [Testing](#testing)
5. [Keeping redirects over time](#keeping-redirects-over-time)
6. [Checklist](#checklist)

## Where Exponential keeps addresses

| Address | Where |
|---|---|
| Main URL alias of each node | a column of the site archive, one per object |
| Custom aliases, aliases in other languages, history aliases after renames | table `ezurlalias_ml` |
| Wildcards (patterns such as `old/*`) | table `ezurlwildcard` |
| Addresses of files | `content/download` links; the files are in the storage directory |

How aliases and wildcards work on the site is explained in [URL aliases and nice URLs](../../../creators/website-creators/url-aliases-and-nice-urls.md)
and [URL wildcards](../../../creators/website-creators/url-wildcards.md). Administrators see them under
**URL translator** and **URL wildcards** ([URLs and aliases](../../../../guides/urls-and-aliases.md)).

## Building the redirect table

1. From the site archive, take each object's main alias and its object id or remote id.
2. From the database dump, add the other aliases of each node from `ezurlalias_ml` and the wildcards from
   `ezurlwildcard`.
3. After importing into the new system, record the new address of each object, matched by remote id or object id.
4. Produce a table: old address, new address, status (permanent redirect, or "gone" where content was intentionally
   not migrated).
5. Decide what happens to addresses with no match: a helpful "page moved" page with search, not a bare error.

```text
old_path                                   new_url                                  object_remote_id   rule
/services/permits/building-permit          https://www.example.gov/permits/building <remote id>        301
/old-news/*                                https://www.example.gov/news             (wildcard)         301
/about/history-2009                        -                                        <remote id>        410 gone (archived)
```

## Installing it on the new system

- Install the redirects **before** DNS switches, as the documentation says.
- Use the new system's own redirect feature or the web server's rewrite rules; prefer permanent redirects for moved
  content.
- Keep redirect rules on the old domain too if the domain changes.

## Testing

- Before the switch: request a sample of old addresses (including the most visited, from your analytics or web
  server logs) against the new system through a hosts-file entry.
- After the switch: check the most visited old addresses and the addresses used in printed material.
- Watch the new system's 404 log for missed addresses in the weeks after; add them to the table.
- On Exponential, the 404 log and the log viewer help the same way during an ordinary relaunch
  ([Log viewer](../../../../features/6.0/log-viewer.md)).

## Keeping redirects over time

Old addresses keep arriving for years from documents and bookmarks. Keep the redirect table as long as your rules
on public information allow, and keep it in the documentation handover for the next change.

## Checklist

- [ ] Main aliases from the site archive; other aliases and wildcards from the database.
- [ ] Every old address mapped, or marked gone with a reason.
- [ ] Redirects installed before DNS switches; tested with a sample.
- [ ] Printed and legally referenced addresses tested individually.
- [ ] 404 log watched after the switch; misses added.

## Related pages
- [Exit plans: overview](README.md)
- [Transition to another system](transition-to-another-system.md)
- [Migration checklist, URLs and redirects](../../../../install/migration/checklist.md#4-urls-and-redirects)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
