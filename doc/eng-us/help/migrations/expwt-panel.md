---
title: "Migrations in the website sidebar"
audience: admin, developer
category: administration
order: 4
summary: The read-only Migrations panel of the website sidebar (expwt) on the site's own pages - the run going on, the failed and pending migrations, the orphans and the last run - and how it leads to Setup > Migrations.
keywords: migrations, website sidebar, expwt, migrations panel, pending migrations, failed migrations, last run, read-only, migration/read, expwt/use
type: help
---
# Migrations in the website sidebar

The website sidebar (the extension expwt) has a **Migrations** panel: the state of the installation's migrations on
every page of the site, without going to the administration. It only shows. Running, resuming and rolling back stay
in **Setup > Migrations** ([Run, dry run and rollback](run-dry-run-and-rollback.md)): the sidebar has no button that
changes anything here.

**Who sees the panel:** users who may use the sidebar (`expwt/use`) and have `migration/read`. Without that policy,
or on a kernel without migrations, there is no Migrations tab.

## What the panel shows

- **A run that is active now**: the installation lock, what it does, who started it and since when;
- the **failed** migrations, each with its source and description;
- the **pending** migrations (id, source, description; the first 50, the rest counted);
- the migrations **recorded, but whose file is gone** (the orphans of the status);
- the **last run**: its state, when it started and finished, who ran it, its caption, tag and push state, and the
  message of a failed run.

Each item has **Open in the administration**: the status, or the page of its run
([The status and the history](status-and-history.md)).

The number of pending, failed and recorded-without-file migrations is a badge on the panel's tab, counted at most
once a minute.

## Related pages

- [Migrations in the administration](README.md)
- [The status and the history](status-and-history.md)
- [The website sidebar](../../features/6.0/website-sidebar.md)
- [Migrations: the guide](../../guides/migrations.md)
