---
title: Verifying a customer installation
audience: partner, hosting
category: partnership
order: 685
summary: How a partner checks a customer's Exponential installation in minutes with ./console exp:info - what runs, which versions, which extensions, what needs attention - at a handover, before a quote, after a go-live and in each maintenance visit, and how to keep the result as a dated, secret-free record.
keywords: partner, customer installation, verify, audit, handover, inventory, exp:info, exp:expfetch, system card, health hints, maintenance, quote, go-live, JSON
type: help
---
# Verifying a customer installation

This page is for implementation and hosting partners who take over, quote for or maintain a customer's site. The
system card gives you a complete, secret-free picture of an installation in one command. Reference:
[The system card](../../features/6.0/system-card.md).

## The check

```bash
cd /path/to/the/installation
./console exp:info --allow-root-user                       # read it on screen
./console exp:info --json --no-logo > customer-2026-10-20.json   # keep it
```

What to look at, in this order:

| Section | Check |
|---|---|
| Exponential | the version and line; a git checkout shows its branch and commit (is it a released version?) |
| PHP | a branch with security support (a hint says when it ends), OPcache on, the recommended extensions |
| Engine | Velocity or Apache/nginx with PHP-FPM; workers and TLS |
| Database | the type and version; no connection is an error |
| Site, Siteaccesses | the siteaccesses and their hosts, designs and languages; demo mode and debug off on a live site |
| Extensions | every active extension and its version: compare with what the contract or the handover lists |
| Cronjobs | a recent run; parts that the site's features need |
| Hints | every `warn` and `error`: each has a one-line fix |

## When to run it

- **Before a quote or a takeover**: the inventory tells you the size of an upgrade (PHP branch, Exponential line,
  extensions without a version).
- **At the go-live**: no `error` hints, demo mode and debug off, cron running. Attach the JSON to the acceptance note.
- **In each maintenance visit**: run it again and compare with the last file
  (`diff <(jq -S . old.json) <(jq -S . new.json)`): new extensions, version changes, new hints.

## Sharing it with the customer or 7x

The output never contains a password, key, token, database user or full database address, and not the values of
the Velocity envvars, so it can go into a ticket, an e-mail or a report as it is. It does contain host names and the
list of extensions; treat it as the customer's information ([Protecting customer data](protecting-customer-data.md)).

## Related pages

- [The system card](../../features/6.0/system-card.md)
- [Keeping customer sites current](keeping-customers-current.md)
- [Implementation project playbook](implementation-project-playbook.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
