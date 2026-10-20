---
title: "Lesson 3.3: CSV import and export"
audience: trainer, admin
category: training
order: 33
summary: Learners prepare a CSV file of test subscribers at example.invalid, import it with the column mapping (preview, dry run, consent source), and export the list again. 45 minutes.
keywords: newsletters with cjw newsletter, csv import, csv export, column mapping, consent source, dry run, subscriber export, delimiter, import list
type: help
---
# Lesson 3.3: CSV import and export

Organizations rarely start with an empty list: addresses come from an old system, a trade fair or another tool.
The import brings them in; the export takes them out for a report or a move. In this lesson learners import a small
file of test subscribers and export it again, and learn why every import needs a recorded consent source.

> **Note**
> Use only made-up people with addresses at `example.invalid`. Never bring a real subscriber file into a training
> installation. An import with the column mapping sends no confirmation mail.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 3: Subscribers](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Administrators and senior editors |
| Prerequisites | [Lesson 3.2](managing-users-and-subscriptions.md); a text editor to write a CSV file |

## Learning objectives

After this lesson, learners can:

- prepare a CSV file with a header row in the expected columns;
- import it with **CSV import with column mapping**: upload, map, preview, dry run, import;
- explain the consent source and why an import sends no double opt-in mail;
- name the rows an import skips and why;
- export a list's subscribers to CSV.

## Trainer's notes

### How to present it

Sources: [Importing and exporting](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#importing-and-exporting)
and the import guide of the extension (`importexport.md`, linked from the
[feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).

1. **Two imports** (3 minutes). The import of 4.1 (`subscription_list_csvimport`) reads columns in a fixed order.
   The import with a column mapping (4.2) lets you choose which column fills which field, shows a preview and makes
   a dry run. Teach the mapping import.
2. **The steps** (5 minutes). Upload (file, delimiter, encoding, header row, consent source, note), map the columns
   (guessed from the column names; exactly one must be the e-mail address), preview of the first ten rows (new,
   update, skip), dry run, import.
3. **Consent** (4 minutes). The consent source is required for the real import. It records where the people agreed.
   No double opt-in mail is sent, because consent was given where the addresses were collected: you must be able to
   prove it.
4. **What is skipped** (3 minutes). Invalid or repeated addresses, addresses on the suppression list or blacklist,
   people who switched the newsletter off or unsubscribed before, and hard bounces. An import never subscribes
   someone again who left.

### Common questions

**Which delimiter?** Semicolon, comma, tab or pipe. The shipped default of the 4.1 import is `;`
(`DefaultCsvDelimiter`).

**Does an import duplicate existing users?** No; an existing address is updated, never duplicated.

**Where is the file kept?** Under the installation's `var` directory, in the newsletter's import folder; only files
from that folder are read.

**Can I run the import from the shell?** Yes: `ext:cjw_newsletter:import --import-id=<id> --dry-run`, then without
`--dry-run` ([specification](../../../../specifications/6.0/cjw_newsletter.md#console-commands)).

**Is the export safe to open in a spreadsheet?** Cells that start with `=`, `+`, `-` or `@` are defused so they are
not run as formulas.

### Pitfalls

- **No header row.** Without names the mapping cannot guess; tell the form whether the first row holds names.
- **Wrong encoding.** Names with accents break when the encoding is wrong. Save the file as UTF-8.
- **A blank consent source.** The dry run works, the import refuses. Agree a wording in class, for example
  `Training course, made-up data`.
- **Keeping exports.** An export is personal data. Delete it after the exercise.

## Hands-on exercise

Use the training installation. Replace `<name>` with your first name.

1. In a text editor, write this file and save it as `subscribers-<name>.csv` in UTF-8:

   ```text
   email;first_name;last_name
   dora-<name>@example.invalid;Dora;Test
   emil-<name>@example.invalid;Emil;Test
   fay-<name>@example.invalid;Fay;Test
   dora-<name>@example.invalid;Dora;Repeated
   not-an-address;Nobody;Test
   ```

2. Open your list's page, press **Subscriptions**, then **Import CSV**.
3. Choose **CSV import with column mapping**.
4. Choose your file, the semicolon as delimiter, UTF-8, and tick that the first row holds the column names.
5. As consent source, type `Training course, made-up data`. Upload.
6. Check the mapping: `email` must be the e-mail address; `first_name` and `last_name` the names.
7. Read the preview: which rows are new, which are skipped, and why?
8. Run the dry run and read its report.
9. Run the import and read the report.
10. Back on your list's subscriptions, find Dora, Emil and Fay with their status.
11. Open the export of the list (the CSV export page links to the subscriber export), keep the statuses confirmed
    and approved, look at the preview and download the CSV.
12. Open the CSV, check the rows, then delete the downloaded file.

## Expected result

- The preview and the reports show three new rows and two skipped ones ("Repeated in the file" and "Not a valid
  e-mail address").
- Dora, Emil and Fay are approved subscribers of the learner's list. No confirmation mail file was written for them.
- The import is listed under **Imports** in the left menu.
- The export file listed the list's subscribers and was deleted afterwards.
- The trainer checks the **Imports** page and one list's subscriptions.

## Quiz

1. Which field must exactly one column be mapped to?
2. Why does an import with the column mapping send no double opt-in mail?
3. Which of these rows is imported?
   A. an address on the blacklist B. a person who unsubscribed before C. a new valid address
   D. an address that hard bounced
4. What does the dry run change in the database?
5. Why should you delete an export after use?

<details>
<summary>Answers</summary>

1. The e-mail address (import guide, linked from the [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
2. Consent was given where the addresses were collected, and the consent source records it; you must be able to
   prove it (import guide).
3. **C.** The others are skipped (import guide).
4. Nothing; it checks and counts every row.
5. It holds personal data; keep exports only as long as needed ([Consent](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#consent)).

</details>

## Further reading

- [Newsletter lists and subscriptions](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#importing-and-exporting):
  importing and exporting.
- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md#security-and-robustness-review-4115):
  how the import and export were hardened.
- [Compliance and GDPR](../../../hosting/security/compliance-and-gdpr.md): keeping personal data.

## Related pages

- [Module 3: Subscribers](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
