---
title: "Lesson 1.2: Inventory and scope"
audience: trainer, admin, developer, project-manager
category: training
order: 12
summary: Learners take a complete inventory of a site before a migration (addresses, content types, media, users, data that is not content, integrations, custom code, legal rules) and decide the scope and the owner of each part. 60 minutes.
keywords: migrating to exponential, inventory, scope, content freeze, address list, crawl, content types, users, personal data, owners, migration checklist
type: help
---
# Lesson 1.2: Inventory and scope

A migration can only move what someone has listed. This lesson teaches the inventory of the common migration
checklist and the scope decisions that follow from it, and has learners take a real inventory of their training
installation as if it were the old site.

> **Note**
> Use a training installation, never a live site. The inventory queries in this lesson only read, but the habit of
> working on a copy starts here.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 1: Planning a migration](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Administrators, developers and project managers |
| Prerequisites | [Lesson 1.1](choosing-a-path.md); a training installation with shell access |

## Learning objectives

After this lesson, learners can:

- list the eight parts of a migration inventory from the common checklist;
- explain why a crawl and the server log are needed in addition to a sitemap;
- count content per class and language on an Exponential installation;
- assign a decision and an owner to each part of the scope table.

## Trainer's notes

### How to present it

Source: [The common migration checklist](../../../../install/migration/checklist.md), sections 1 and 2.

1. **Why first** (3 minutes). Everything later (mapping, redirects, tests, the cut-over window) is measured against
   the inventory. A part missing from it is a part that will be found after go-live.
2. **The eight parts** (8 minutes). Go through section 1 of the checklist: every address, every content type with
   counts per language, media, users and groups, data that is not content (forms, comments, orders, newsletters),
   integrations, custom code, legal and retention.
3. **Addresses** (4 minutes). A sitemap misses orphan pages; the access log of the last months and the list of
   addresses that other sites link to complete it. This list later drives the redirects (lesson 2.3).
4. **Scope and owners** (5 minutes). Show the table in section 2: content, design, addresses, users, features,
   freeze. Each row needs a decision and a named owner.

### Common questions

**How far back should the access log go?** The checklist says "the last months". Agree on a period with the site
owner and write it into the plan.

**Do we have to move every user?** No. Moving accounts or asking users to register again is a scope decision, made by
the administrator together with the data protection officer (checklist, section 2).

**What about personal data nobody needs any more?** The checklist advises leaving it behind: move only what the new
site needs, and document the legal basis.

**What if the old site is an Exponential site?** The same inventory applies; chapter 14.3 adds the queries for
version, datatypes, password hash types, charset and cluster mode (lesson 4.1).

### Pitfalls

- **Counting only published pages.** Ask for drafts, archived items and other languages too, then decide.
- **Forgetting data that is not content.** Form submissions and newsletter subscribers are often the most sensitive
  data on a site. List them even if they will be archived.
- **A scope table without names.** "The team" is not an owner. Insist on a person per row.

## Hands-on exercise

Use your own training installation, installed with `php bin/php/console exp:install` as in
[the console install](../../../../install/07-console-install.md). It stands in for "the old site". The default
installation uses SQLite, with the database in `var/storage/sqlite3/exponential.db`; open it read-only.

1. Copy the checklist of section 1 of [the common migration checklist](../../../../install/migration/checklist.md)
   into a text file named `inventory.md` in your notes.
2. In a terminal, in the installation root, open the database read-only:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db
   ```

3. Count the content objects and the nodes:

   ```sql
   SELECT COUNT(*) FROM ezcontentobject;
   SELECT COUNT(*) FROM ezcontentobject_tree;
   ```

4. Count the objects per class:

   ```sql
   SELECT c.identifier, COUNT(*) FROM ezcontentobject o
     JOIN ezcontentclass c ON c.id = o.contentclass_id AND c.version = 0
    GROUP BY c.identifier ORDER BY 2 DESC;
   ```

5. List the datatypes in use (the query of [chapter 14.3](../../../../install/14-migrating-from-4x.md#143-take-stock-of-the-old-site)):

   ```sql
   SELECT data_type_string, COUNT(*) FROM ezcontentclass_attribute
    WHERE version = 0 GROUP BY data_type_string ORDER BY 2 DESC;
   ```

6. Count the users by password hash type:

   ```sql
   SELECT password_hash_type, COUNT(*) FROM ezuser GROUP BY password_hash_type;
   ```

7. Leave the database with `.quit`. Note the size of `var/` with `du -sh var/`.
8. Fill in the rest of `inventory.md` from what you know about the training site: integrations, custom code, legal
   rules. Write "none" where there is none; do not leave a line empty.
9. Copy the scope table of section 2 of the checklist and fill in a decision and an owner (a role, for the exercise)
   for each row.

## Expected result

- `inventory.md` has every item of section 1 with a number, a list or "none".
- The class count shows the classes of the installed site package with their numbers of objects.
- The datatype list shows kernel datatypes such as `ezstring` and `ezxmltext`, and possibly extension datatypes.
- The hash type count shows the types in use (type 7, `php_default`, is the default of Exponential 6).
- The scope table has a decision and an owner in every row.
- The trainer checks by asking each learner for three numbers (objects, nodes, classes) and one scope decision with
  its reason.

## Quiz

1. Why is a sitemap not enough as the list of old addresses?
2. Which of these is **not** one of the inventory items of the common checklist?
   A. Integrations B. Custom code C. The color palette of the design D. Legal and retention
3. Who typically decides whether user accounts move? Name the two roles of the scope table.
4. True or false: personal data you no longer need is best moved anyway, in case it is needed later.
5. What does the inventory drive later in the project? Name two things.

<details>
<summary>Answers</summary>

1. A sitemap misses orphan pages; crawl the site and take the access log as well
   ([checklist, section 1](../../../../install/migration/checklist.md#1-inventory-know-what-you-have)).
2. **C.** The design is a scope decision (carry over, refresh or new), not an inventory item
   ([checklist, section 2](../../../../install/migration/checklist.md#2-decide-the-scope-and-the-owner-of-each-part)).
3. The administrator and data protection (checklist, section 2).
4. **False.** It is best left behind; move only what you need (checklist, sections 1 and 11).
5. Any two of: the redirects, the counts after each rehearsal, the tests, the scope decisions
   ([checklist, sections 4, 6 and 7](../../../../install/migration/checklist.md)).

</details>

## Further reading

- [The common migration checklist](../../../../install/migration/checklist.md): the inventory and scope sections in
  full.
- [Chapter 14.3: Take stock of the old site](../../../../install/14-migrating-from-4x.md#143-take-stock-of-the-old-site):
  the extra queries for an old Exponential site.
- [Chapter 16.4: Before you start](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#164-before-you-start-inventory-freeze-and-backups):
  the inventory for a Symfony-based source.

## Related pages

- [Module 1: Planning a migration](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
