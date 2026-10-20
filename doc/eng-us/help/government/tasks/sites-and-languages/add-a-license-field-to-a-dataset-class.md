---
title: Add a license field to a dataset class
audience: government, admin, website-creator
category: public-sector
order: 230
summary: Task for administrators: add a required License selection to the dataset class with the licenses your body approved, and fill it for existing datasets.
keywords: license field, dataset class, selection datatype, ezselection, required field, open license, class editor
type: help
---
# Add a license field to a dataset class

This task is for administrators. Background: [Licensing datasets](../../open-data/licensing-datasets.md) and
[Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md).

## Before you start

The list of approved licenses, each with its official name and version.

## Steps

1. **Setup > Classes**, group **Content**, the class `dataset`, **Edit**.
2. Under **Add attribute** choose **Selection**, press **Add**.
3. Name `License`, identifier `license`; tick **Required**; untick **Translatable**.
4. Add one option per approved license, plus "Not available for re-use" if you describe closed datasets.
5. **OK**. Every existing dataset gets an empty license field.
6. Open each existing dataset, choose its license, publish.

## Check

A new dataset cannot be published without a license; the dataset page and the REST API's fields show it.

## Problems

| Problem | Cause |
|---|---|
| Existing datasets still empty | adding a field does not fill it; edit each one |
| You need the license text on the page | add a URL field `license_url` or relate to a license page |

## Related pages

- [Designing a dataset class](../../open-data/designing-a-dataset-class.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
