---
title: "Lesson 3.1: Choosing the right import or export tool"
audience: trainer, admin, developer
category: training
order: 31
summary: Learners know the import and export tools Exponential has (xrowextract, ezcsvimport, packages, the ZIP importer, ezodf, RSS import, layouts transfer, REST) and choose the right one for a source before writing any script. 45 minutes.
keywords: migrating to exponential, import tools, export tools, xrowextract, ezcsvimport, packages, ezpkg, ezmultiupload, zip import, ezodf, rss import, exp:layouts, rest api, which tool
type: help
---
# Lesson 3.1: Choosing the right import or export tool

The documentation's rule is plain: use the tools that exist first, and write a script only for what they cannot do.
This lesson gives learners a map of those tools, what each reads and writes, and the table that matches a source to
its first choice.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 3: Import and export tools](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Developers and administrators |
| Prerequisites | [Module 2](../module-02-mapping-content/README.md); a training installation with shell access and an administrator account |

## Learning objectives

After this lesson, learners can:

- name the import tools of Exponential and what each reads;
- name the export tools and what each writes;
- choose the first tool for a given source from the table "Which tool for which job";
- list the classes and the export columns of their installation with xrowextract, without writing anything.

## Trainer's notes

### How to present it

Source: [Exponential's import and export tools](../../../../install/migration/exponential-tools.md), sections 1, 2
and 5.

1. **Import tools** (8 minutes). In one sentence each:
   - **xrowextract**: the most complete importer; reads CSV, XML or JSON, creates or updates objects of one class
     below a parent node; dry run by default; background jobs and resume.
   - **ezcsvimport** (kernel): simple; `;` separator, no header row, create only.
   - **Packages** (`.ezpkg`): classes and content between Exponential installations.
   - **The ZIP importer** of the upload page (ezmultiupload): a media library in bulk.
   - **ezodf**: office documents.
   - **RSS import**: keeps pulling a feed in during a transition; not a full migration.
   - **exp:layouts**: layouts and rules as a ZIP transfer file between Exponential installations.
   - **REST and expservices**: when the importer runs on another machine.
2. **Export tools** (5 minutes). Show the table of section 2: xrowextract CSV and site archive, packages, the CSV
   export of the kernel, layouts export, bccie for form data, the database dump.
3. **Which tool for which job** (4 minutes). Show section 5. A spreadsheet goes to xrowextract with `--map`; a system
   with a REST API only needs a custom script (lesson 3.3).
4. **One honest sentence** (3 minutes). No tool reads WordPress, Drupal or Joomla exports directly; the path guides
   convert them into a format xrowextract reads, or use the script skeleton.

### Common questions

**Is xrowextract part of every installation?** It is required by the root `composer.json` of Exponential 6.0.15, like
the other extensions the tools page names. Tools of an extension are available when it is installed and active.

**Why not import through REST?** The tools page: for large imports a script on the server is faster and simpler.
Updating through REST answers 501 (not implemented yet).

**Can packages carry content from WordPress?** No. Packages are the tool for moving between Exponential
installations; they are not a format other systems produce.

### Pitfalls

- **Writing a script on day one.** Check the tools first; a CSV and `--map` often do the job.
- **Using ezcsvimport for an update.** It only creates. A second run duplicates.
- **Forgetting the files.** Exports reference images and files in the storage directory; copy it with them.

## Hands-on exercise

Use your own training installation. Every command in this exercise only reads.

1. In the installation root, list the classes that have objects below the root node:

   ```bash
   php bin/php/console ext:xrowextract:csv --list-classes --node=2
   ```

2. List the columns xrowextract can export for the class `article` (or a class from step 1):

   ```bash
   php bin/php/console ext:xrowextract:csv --list-columns --class=article
   ```

3. Preview the first rows of an export with the migration column set, without writing a file:

   ```bash
   php bin/php/console ext:xrowextract:csv --class=article --node=2 --sets=migration --preview=3
   ```

4. Note the columns that identify each row (object id, remote id) and the ones that place it in the tree (parent
   remote id, priority).
5. In the admin, open **Setup > Packages** and note which packages your installation has.
6. For each source below, write the first tool and the second tool from
   [section 5 of the tools page](../../../../install/migration/exponential-tools.md#5-which-tool-for-which-job):
   a spreadsheet of 400 products; a folder of 2 000 photos; another Exponential 6 site; a news feed; a system with
   only a REST API.

## Expected result

- Step 1 lists classes with object counts; step 2 lists attribute identifiers and special columns.
- Step 3 prints three rows whose columns include the object id, the remote id, the class identifier, the language,
  the parent's remote id and every attribute.
- Step 6: xrowextract with `--map`; the ZIP importer; packages (or a database copy when the whole site moves); RSS
  import; a custom script with the kernel API.
- The trainer checks step 3 on one screen and the five answers of step 6.

## Quiz

1. Which tool imports a CSV whose columns you name with `--map`, with a dry run by default?
   A. ezcsvimport B. xrowextract C. ezodf D. RSS import
2. What does the kernel's ezcsvimport do with an existing object on a second run?
3. Which tool moves layouts and their rules between Exponential installations?
4. True or false: packages are a good format for content coming from WordPress.
5. Why is a server-side script usually better than REST for a large import?

<details>
<summary>Answers</summary>

1. **B** ([tools page, section 1](../../../../install/migration/exponential-tools.md#1-import-into-exponential)).
2. Nothing: it creates only, so a second run makes a duplicate (tools page, section 1, `ezcsvimport`).
3. `exp:layouts export` and `exp:layouts import`
   ([tools page, section 1](../../../../install/migration/exponential-tools.md#layouts-se7enxwebexplayouts)).
4. **False.** Packages move content between Exponential installations; other systems do not produce them (tools page,
   section 1).
5. It is faster and simpler, and REST updates answer 501 (tools page, section 1, "The REST API and expservices").

</details>

## Further reading

- [Exponential's import and export tools](../../../../install/migration/exponential-tools.md): every tool with its
  real options.
- [Packages](../../../../guides/packages.md): packages in the admin and from a shell.
- [Remote services and apps](../../../../guides/remote-services-and-apps.md): the HTTP services for importers on
  another machine.

## Related pages

- [Module 3: Import and export tools](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
