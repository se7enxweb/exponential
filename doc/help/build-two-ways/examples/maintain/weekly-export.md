---
title: "Export layouts every week"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1322
summary: A scheduled exp:layouts export --all keeps a history of the layouts.
keywords: scheduled export, cron, backup, history, layouts
type: help
---
# Export layouts every week

Any layout can be restored to last week's state.

*Life cycle: Maintain, example 23 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Or a manual **Layouts > Export** on Mondays.

## On the command line

1. A crontab line on the server (as the user that runs the site):

```bash
0 3 * * 1  cd /path/to/installation && ./console exp:layouts export --all --output=var/backup/layouts-$(date +\%Y\%m\%d).zip
```

## Which to choose

Keep the files outside the web root and prune old ones.

## Check

- A new file appears each week.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Maintain examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
