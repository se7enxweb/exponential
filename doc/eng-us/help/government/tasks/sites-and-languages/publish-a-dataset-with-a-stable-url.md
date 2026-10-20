---
title: Publish a dataset with a stable URL
audience: government, editor
category: public-sector
order: 220
summary: Task for data publishers: create a dataset page with its files in the catalog, give it a short permanent alias to cite, and check the addresses as a visitor.
keywords: dataset, stable URL, permanent address, URL alias, cite as, open data, publish dataset, distribution
type: help
---
# Publish a dataset with a stable URL

This task is for editors in a data team. Background: [Publishing dataset files](../../open-data/publishing-dataset-files.md)
and [Stable URLs for datasets](../../open-data/stable-urls-for-datasets.md).

## Before you start

- A dataset class and a catalog folder exist; you may create datasets there.
- The file passed the privacy check; the license is decided.

## Steps

1. Open the catalog folder; under **Sub items**, **Create new**, **Dataset**. Fill in title, description, publisher,
   contact mailbox, license, dates. **Send for publishing**.
2. In the new dataset, **Create new**, **Distribution** (or **File**); upload the file with a stable, lower-case name
   (`street-lights.csv`). Publish.
3. On the dataset's **URL aliases** tab, add a short alias such as `data/street-lights`.
4. Print it on the page as "Cite as: https://www.example.gov/data/street-lights" (in the description or your template).

## Check

```bash
curl -sI https://www.example.gov/data/street-lights | head -1
```

answers `200` or a `301` to the page. Signed out, the page and the file download open.

## Problems

| Problem | Fix |
|---|---|
| Download refused for visitors | the catalog's section is not readable by Anonymous |
| The alias does not open | a cache, or a typo; check the URL aliases tab |

## Related pages

- [Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
