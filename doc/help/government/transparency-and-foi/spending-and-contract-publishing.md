---
title: Spending and contract publishing
audience: government, editor, website-creator
category: public-sector
order: 110
summary: How to publish spending reports, contracts and procurement notices with Exponential: as documents in a dated structure, as a register kept as content, or as both, with open-data exports and checks for commercially confidential and personal data.
keywords: spending, expenditure, contracts, contract register, procurement notices, transparency, budget, payments, CSV, register, public spending
type: help
---
# Spending and contract publishing

This page is for finance, procurement and web teams who publish what a public body spends and which contracts it
signs. Whether, how often and in which detail you must publish depends on your jurisdiction and your own policy. In
Exponential you can publish the information as documents, as a register of content objects, or both.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Two ways to publish](#two-ways-to-publish)
2. [Documents in a dated structure](#documents-in-a-dated-structure)
3. [A contract register as content](#a-contract-register-as-content)
4. [Machine-readable copies](#machine-readable-copies)
5. [What to check before publishing](#what-to-check-before-publishing)

## Two ways to publish

| | Documents | Register as content |
|---|---|---|
| What | the report or contract as a file (PDF, CSV) | one object per contract or payment line group, with fields |
| Effort | low | a class and data entry or import |
| Search and filter | by document title and text | by every field |
| Open data | the CSV file itself | export the register as CSV, JSON or XML |
| Good for | periodic spending reports | contract registers that people browse |

## Documents in a dated structure

1. A folder "Spending" with one folder per year, and per month or quarter if you publish that often.
2. Each report as a File object with a clear name ("Spending over threshold, March 2026") in both the document
   format (PDF) and a machine-readable format (CSV).
3. An introduction that explains thresholds, what is excluded and why.
4. An RSS export of the "Spending" folder with subnodes ([RSS feeds](../../../guides/rss-feeds.md)).

## A contract register as content

A class `contract` ([Content classes for websites](../../creators/website-creators/content-classes-for-websites.md)):

| Attribute | Datatype |
|---|---|
| Title (subject of the contract) | Text line |
| Reference | Text line |
| Supplier (organization name) | Text line |
| Value | Float or Price, with the currency stated |
| Start date, end date | Date |
| Procedure | Selection |
| Department | Selection or Tags |
| Contract document (redacted) | File |

Datasets for the register: export it with `xrowextract` (see "Exporting content as CSV, JSON or XML" in the open
data section). The audit trail records changes to every entry.

Bulk entry: `xrowextract` can import CSV, JSON or XML files into content objects with a column mapping and a dry
run ([xrowextract](../../../features/6.0/extensions/xrowextract.md)). Try an import on a test installation first.

## Machine-readable copies

Publish spending data as CSV next to any PDF, in UTF-8 with a header row, one row per payment or contract, and the
same column names every period. That makes the publication open data as well (see the open data section).

## What to check before publishing

- [ ] Payments to individuals (for example sole traders, compensation, benefits) are handled according to your
  privacy rules; names of private persons are usually removed or aggregated.
- [ ] Commercially confidential parts of contracts are redacted with recorded grounds (see "Redaction is a manual
  step").
- [ ] Totals match the official accounts; the introduction says how they were compiled.
- [ ] File names contain no personal data.

## Related pages

- [Proactive disclosure](proactive-disclosure.md)
- [xrowextract](../../../features/6.0/extensions/xrowextract.md)
- [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md): the price datatype in shop context
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
