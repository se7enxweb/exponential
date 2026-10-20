---
title: "Lesson 3.2: Importing with xrowextract: dry runs and repeatable imports"
audience: trainer, admin, developer
category: training
order: 32
summary: Learners export a sample of a class with xrowextract to learn its file format, run the importer as a dry run, read its report, and see how matching on remote ids makes an import repeatable instead of duplicating. 60 minutes.
keywords: migrating to exponential, xrowextract, import, csv, dry run, apply, match, remote_id, manifest, repeatable import, migration column set
type: help
---
# Lesson 3.2: Importing with xrowextract: dry runs and repeatable imports

xrowextract is the most complete importer Exponential has. This lesson teaches the way of working the tools page
recommends: export one sample of the target class to get the format right, convert the source into that format, and
always run a dry run before `--apply`. Learners also see why the same file can be imported twice without creating
duplicates.

> **Note**
> Use a training installation, never a live site. The importer writes nothing without `--apply`; in this lesson
> learners apply only once, to their own course folder.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 3: Import and export tools](README.md) |
| Duration | 60 minutes (15 presenting, 45 exercise) |
| Level | Intermediate |
| Audience | Developers and administrators |
| Prerequisites | [Lesson 3.1](choosing-the-right-tool.md); a course folder of their own on the training installation and its node id |

## Learning objectives

After this lesson, learners can:

- export a sample of a class with the migration column set to learn the file format;
- run an import as a dry run and read its report;
- explain the three values of `--match` and what each does on a second run;
- apply an import into a given parent node and check the result in the admin.

## Trainer's notes

### How to present it

Source: [Exponential's import and export tools, xrowextract](../../../../install/migration/exponential-tools.md#xrowextract-csv-xml-and-json-import-se7enxwebxrowextract).

1. **The format comes from an export** (4 minutes). The easiest way to get the format right is to export one sample
   object of the target class and copy its columns. Every export carries a typed column manifest
   (`<file>.manifest.json`), so a file with a manifest maps back exactly.
2. **Dry run first** (3 minutes). Nothing is written without `--apply`. Show the dry run's report on the projector.
3. **Matching** (5 minutes). The importer's own help: `--match` is `remote_id` (the default, when the file has a
   remote id column), `object_id`, or `none` (always create). With the default, a second run of the same file
   updates the same objects. That is the "repeatable import" of the checklist.
4. **Foreign files** (3 minutes). A file from another system maps its columns with `--map=column=attribute,...`.
   Large files run with `--background` and resume with `--resume-from`.

### Common questions

**Can editors import in the admin?** Yes: the node view's **Import/Export** button, *Import into this item*, runs the
same importer (tools page). Check the button on your own installation before you show it.

**What about images?** Image and file columns name files; copy the storage or the source's files with the export.

**Does the import respect permissions?** The tools page says it checks the user's permissions.

### Pitfalls

- **`--match=none` with `--apply` on a file that was already imported.** That creates duplicates. Learners use
  `none` only in a dry run in this lesson.
- **Wrong parent node.** Learners write their course folder's node id on paper before they start.
- **Editing the CSV in a spreadsheet program that changes the separator or the encoding.** Use a plain text editor
  for the exercise.

## Hands-on exercise

Use your own training installation, with a course folder that holds at least one article (from an earlier course,
or create one in the admin). Work in `var/tmp/import/` below the installation root.

1. Note the node id of your course folder (shown on its page in the admin). Below it is called `<folder>`.
2. Create the working directory:

   ```bash
   mkdir -p var/tmp/import
   ```

3. Export the articles below your folder with the migration column set:

   ```bash
   php bin/php/console ext:xrowextract:csv --class=article --node=<folder> --sets=migration --output=var/tmp/import/articles.csv
   ```

4. Look at the file and its manifest:

   ```bash
   head -3 var/tmp/import/articles.csv
   ls var/tmp/import/
   ```

5. Run the import of the same file as a dry run:

   ```bash
   php bin/php/console ext:xrowextract:import --file=var/tmp/import/articles.csv --class=article --parent=<folder>
   ```

   Read the report: how many rows would be created, how many updated?
6. Run the same dry run with `--match=none` added. Compare the report.
7. In `articles.csv`, change the title of one article in a text editor (keep the remote id as it is). Save.
8. Apply the import with the default matching:

   ```bash
   php bin/php/console ext:xrowextract:import --file=var/tmp/import/articles.csv --class=article --parent=<folder> --apply
   ```

9. In the admin, open your folder. Check the title you changed and the number of articles.
10. Open the article's **Manage versions** page (or its version history) and note the new version.

## Expected result

- Step 3 wrote `articles.csv`; step 4 shows a header with the migration columns and a `.manifest.json` next to the
  file.
- Step 5: the dry run reports updates for the existing articles (matched by remote id) and writes nothing.
- Step 6: with `--match=none` the dry run reports creates for every row.
- After step 8 the folder holds the same number of articles as before; the changed article shows the new title as a
  new version.
- The trainer checks one learner's folder: no duplicates, one changed title.

## Quiz

1. What does the importer do when you leave out `--apply`?
2. Which `--match` value always creates new objects?
   A. `remote_id` B. `object_id` C. `none` D. `apply`
3. Why export one sample object of the target class before converting a foreign file?
4. What does the manifest file next to an export contain?
5. A file with 400 000 rows stopped at row 40 000. Which option continues the import?

<details>
<summary>Answers</summary>

1. It runs a dry run and writes nothing
   ([tools page, xrowextract](../../../../install/migration/exponential-tools.md#xrowextract-csv-xml-and-json-import-se7enxwebxrowextract)).
2. **C.** `none` always creates; `remote_id` is the default when the file has a remote id column (the importer's
   help text, `extension/xrowextract/bin/php/import.php`).
3. Its columns show exactly the format the importer expects (tools page, xrowextract).
4. A typed column manifest: per column its datatype, format, language and import target (tools page, xrowextract).
5. `--resume-from=40001` (with `--apply`, and `--background` for a large file) (tools page, xrowextract).

</details>

## Further reading

- [Exponential's import and export tools](../../../../install/migration/exponential-tools.md): xrowextract import,
  export and packages.
- [The common migration checklist, section 6](../../../../install/migration/checklist.md#6-build-the-import-and-rehearse-it):
  what a repeatable import is.
- [Publishing, versions and the trash](../../../publishing-and-versions.md): what a new version means for editors.

## Related pages

- [Module 3: Import and export tools](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
