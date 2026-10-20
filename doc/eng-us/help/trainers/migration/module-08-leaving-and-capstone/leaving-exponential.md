---
title: "Lesson 8.1: Migrating away from Exponential"
audience: trainer, admin, developer
category: training
order: 81
summary: Learners make a complete, self-describing export of an Exponential site - the xrowextract site archive, the storage, a database dump, layouts, form data - turn rich text into HTML, build the redirect table from the URL aliases, and handle users and personal data by agreed rules. 45 minutes.
keywords: migrating to exponential, leaving exponential, export, xrowextract archive, site archive, manifest, storage, database dump, layouts export, XmlText to HTML, redirect table, personal data, your content is yours
type: help
---
# Lesson 8.1: Migrating away from Exponential

A migration course is honest only if it also teaches the way out. The documentation says it plainly: your content is
yours. Exponential stores it in an open database schema and ordinary files, and has tools that write it out in plain
formats. This lesson teaches the complete export and what the target system needs from it.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 8: Leaving Exponential, and the capstone](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Administrators and developers |
| Prerequisites | [Module 3](../module-03-import-and-export-tools/README.md); shell access to a training installation |

## Learning objectives

After this lesson, learners can:

- list what to export from an Exponential site and the tool for each part;
- make a site archive with xrowextract and check it against the admin's counts;
- choose between rendering and transforming XmlText into HTML;
- build a redirect table from URL aliases and wildcards;
- explain why the site archive never contains password hashes and what that means for users.

## Trainer's notes

### How to present it

Source: [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md) and the export table
of [the tools page, section 2](../../../../install/migration/exponential-tools.md#2-export-from-exponential).

1. **What to export** (4 minutes). Show the table of section 1: the xrowextract site archive (per class a CSV with
   ids, remote id, nodes, URL alias, dates and every attribute, a `manifest.json`, a `README.txt`), single classes,
   packages for another Exponential, layouts, the storage directory, the database dump, form submissions, consent
   records, the audit log.
2. **The seven steps** (4 minutes). Freeze or note the time; site archive; storage; database dump; layouts; address
   list; check the archive against the admin's counts.
3. **Rich text** (3 minutes). Render (the site's templates, or REST v2 with `OutputFormat=xhtml`) or transform the XML
   in a script; embeds become image tags or links to the exported files.
4. **Users** (4 minutes). The archive never contains password hashes. Users set new passwords on the target, unless
   the target can verify standard PHP `password_hash()` values; decide with the data protection advisor. Export only
   the personal data the target needs.

### Common questions

**Is there a guide per target?** Yes: WordPress, Drupal, Joomla, Shopify, Contentful, site builders, and the family
products; the leaving page links them.

**Can we keep a static archive of the old site?** `exp:makestaticcache` writes static pages, or a mirroring crawl of
the live site; keep the storage for the image originals (static HTML guide, section 5).

**What stays on the old system?** Keep it read-only until the new one is signed off (leaving page, checklist).

### Pitfalls

- **Exporting without the storage.** The archive references files; copy `var/<site>/storage/` with it.
- **Archiving personal data without rules.** Keep it with access controls, or delete it as the retention rules say.
- **Forgetting other aliases.** The archive has each node's main alias; custom aliases, other languages and
  wildcards are in `ezurlalias_ml` and `ezurlwildcard`.

## Hands-on exercise

Use your own training installation. The export writes files into `var/tmp/export/` only.

1. Create the working directory:

   ```bash
   mkdir -p var/tmp/export
   ```

2. Make a site archive of everything, as a ZIP:

   ```bash
   php bin/php/console ext:xrowextract:archive --set=everything --format=zip --output=var/tmp/export/
   ```

3. List the archive and open its `manifest.json` and `README.txt`:

   ```bash
   ls -l var/tmp/export/
   unzip -l var/tmp/export/*.zip | head -30
   ```

4. Pick one class from the archive and count its rows; compare with the number of objects of that class in the admin
   (**Setup > Classes**, the class page).
5. Check that no password hash is in the archive: search the user class's CSV for the column names of
   `password_hash`. Write down what you find.
6. Export the layouts, if your installation has Exponential Layouts:

   ```bash
   php bin/php/console exp:layouts export --all --output=var/tmp/export/layouts.zip
   ```

7. Write the redirect plan: which column of the archive gives each node's main address, and which two tables hold the
   other aliases and the wildcards.
8. Write two sentences for users about passwords on the target, using `example.invalid` for any address.
9. Remove `var/tmp/export/` when you are done, after the trainer has checked step 4.

## Expected result

- Step 2 wrote an archive with one CSV per class, `manifest.json` and `README.txt`.
- Step 4: the row count of the class matches the object count in the admin (one row per object per language).
- Step 5: no password hash column.
- Step 6 wrote `layouts.zip`, or the learner noted that the installation has no layouts.
- Step 7: the URL alias column of the archive, plus `ezurlalias_ml` and `ezurlwildcard`.
- The trainer checks step 4 on one screen.

## Quiz

1. Which tool makes a complete, self-describing export of a site?
   A. `php ezpm.php export` B. The xrowextract site archive C. `exp:layouts export` D. `sevenx_dse`
2. What does the site archive deliberately never contain?
3. Name the two ways to turn XmlText into HTML for the target.
4. Where are custom URL aliases and wildcards, which the archive's main alias column does not cover?
5. True or false: the old site can be switched off as soon as the export is finished.

<details>
<summary>Answers</summary>

1. **B** ([leaving page, section 1](../../../../install/migration/leaving-exponential.md#1-what-to-export)).
2. Password hashes (section 1 and [section 5](../../../../install/migration/leaving-exponential.md#5-users-and-personal-data)).
3. Render it (templates or REST v2 with `OutputFormat=xhtml`) or transform the XML in a script
   ([section 3](../../../../install/migration/leaving-exponential.md#3-rich-text)).
4. In `ezurlalias_ml` and `ezurlwildcard`
   ([section 4](../../../../install/migration/leaving-exponential.md#4-addresses-for-the-redirects)).
5. **False.** Keep it read-only until the new one is signed off
   ([section 6](../../../../install/migration/leaving-exponential.md#6-checklist)).

</details>

## Further reading

- [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md): every export and the plan.
- [Exponential's import and export tools, section 2](../../../../install/migration/exponential-tools.md#2-export-from-exponential):
  the export tools with commands.
- [WordPress and Exponential, section 5](../../../../install/migration/wordpress.md#5-from-exponential-to-wordpress):
  one target in detail.

## Related pages

- [Module 8: Leaving Exponential, and the capstone](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
