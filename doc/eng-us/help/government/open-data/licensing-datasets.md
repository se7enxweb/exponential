---
title: Licensing datasets
audience: government, project-manager, editor
category: public-sector
order: 60
summary: For people who decide under which terms a public body's datasets are published: what an open license must allow, how to record the license on every dataset page in Exponential, and what to check before choosing one.
keywords: license, open license, licensing data, Open Definition, re-use, attribution, Open Data Directive, dataset license, selection field, terms of use
type: help
---
# Licensing datasets

This page is for managers and editors who must state under which terms each dataset may be re-used. It explains
what makes a license open, how to record the license on a dataset page in Exponential, and the questions to settle
with your legal counsel before you choose. It does not recommend a particular license: that choice belongs to your
organization.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What an open license must allow](#what-an-open-license-must-allow)
2. [Questions to settle before choosing](#questions-to-settle-before-choosing)
3. [Recording the license in Exponential](#recording-the-license-in-exponential)
4. [Showing the license to people and machines](#showing-the-license-to-people-and-machines)
5. [Data that cannot be open](#data-that-cannot-be-open)

## What an open license must allow

The [Open Definition 2.1](https://opendefinition.org/od/2.1/en/) lists what a license must permit to be "open":
use, redistribution, modification, separation of parts, compilation with other works, non-discrimination against
persons, groups or fields of use, and propagation of the same rights to everyone who receives the work. It allows a
few conditions, such as requiring attribution or that changed versions stay under the same terms.

In the EU, [Directive (EU) 2019/1024](https://eur-lex.europa.eu/eli/dir/2019/1024/oj) addresses licenses for the
re-use of public sector documents. Your national law and policy may name the licenses public bodies should use.

## Questions to settle before choosing

| Question | Why it matters |
|---|---|
| Does the body hold all rights in the data? | Data bought from or produced with third parties may carry their terms |
| Does national policy prescribe a license? | Many jurisdictions name a default for public data |
| Is attribution wanted? | Decides between licenses with and without an attribution condition |
| Does the data contain personal data? | Personal data generally is not published as open data; anonymize or aggregate first (see "Privacy checks before publishing data") |
| Are there security, commercial or third-party confidentiality reasons to hold back? | Some datasets, or columns, may need to stay unpublished |
| Who signs off? | Name the role that approves the license of each dataset |

## Recording the license in Exponential

Give the dataset class a required **License** field of the datatype Selection (`ezselection`), with one option per
license your body approved. A selection keeps the wording identical on every page and makes it possible to list
datasets by license.

1. **Setup > Classes**, group **Content**, the class `dataset`, **Edit**.
2. Under **Add attribute** choose **Selection**, press **Add**, name it `License`, identifier `license`, tick
   **Required**, untick **Translatable** (the license is the same in every language).
3. Add one option per license, with its official short name, for example the name and version as the license
   steward publishes them.
4. Press **OK**.

If you need the license text or its address on the page, add a second field of the datatype URL (`ezurl`) named
`license_url`, or keep one page per license and relate datasets to it with an object relation.

For the steps of the class editor see [Content classes for websites](../../creators/website-creators/content-classes-for-websites.md).

## Showing the license to people and machines

- **On the page**: show the license name and a link to its text near the download links, not in the footer.
- **In files**: where the format allows, add the license and source in a header or a separate `README` file in
  the distribution.
- **In feeds**: an RSS export can map one attribute to the description or category of each item
  ([RSS feeds](../../../guides/rss-feeds.md#6-editing-an-export-or-an-import)); a catalog that needs
  `dct:license` in DCAT has to be written by your own template (see "DCAT and national portals").
- **In the API**: the REST API returns every field of the object, the license field included
  ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)).

## Data that cannot be open

Not every dataset may be open. Typical reasons: personal data, security, third-party rights, or law that limits
re-use. You can still publish a description of such a dataset with "Not available for re-use" as its license
option, so people know it exists and whom to ask.

## Related pages

- [Designing a dataset class](designing-a-dataset-class.md)
- [Choosing datatypes](../../creators/website-creators/choosing-datatypes.md)
- [Licensing explained for buyers](../../business/licensing-for-buyers.md): the license of the software, which is a different question
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
