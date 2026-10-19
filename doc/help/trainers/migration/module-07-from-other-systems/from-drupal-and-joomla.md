---
title: "Lesson 7.2: From Drupal and Joomla"
audience: trainer, admin, developer
category: training
order: 72
summary: Learners plan migrations from Drupal (JSON:API, nodes, Paragraphs, taxonomy, path aliases and /node/ links) and from Joomla (the Web Services API or the database, introtext and fulltext, categories, menu-driven SEF addresses), and compare the two with WordPress. 45 minutes.
keywords: migrating to exponential, Drupal, JSON:API, Paragraphs, taxonomy, path alias, node links, Joomla, Web Services API, X-Joomla-Token, introtext, fulltext, SEF, menu items, redirects, comparison
type: help
---
# Lesson 7.2: From Drupal and Joomla

Drupal and Joomla follow the same frame as WordPress, but each has its own export and its own address traps. This
lesson teaches both side by side, so that learners can transfer the method rather than memorise one system.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 7: From other systems](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Developers and administrators |
| Prerequisites | [Lesson 7.1](from-wordpress.md) |

## Learning objectives

After this lesson, learners can:

- name the most practical export source of Drupal and of Joomla, with one limit of each;
- map Drupal content types, Paragraphs and taxonomy, and Joomla articles, categories and menu items;
- explain why Drupal's `/node/<id>` links and Joomla's menu-driven addresses need special care;
- build the address list of a Joomla site from a crawl and the server log.

## Trainer's notes

### How to present it

Sources: [Drupal and Exponential](../../../../install/migration/drupal.md) and
[Joomla and Exponential](../../../../install/migration/joomla.md).

1. **Drupal export** (4 minutes). No single "export the site" file. JSON:API delivers each content type with fields,
   relationships and the path alias; read-only by default; at most 50 resources per request, follow the `next` link;
   export with an account that sees everything.
2. **Drupal mapping** (4 minutes). Content type to class; Paragraphs to child objects of small classes, or layout
   blocks for landing pages; taxonomy to folders or `eztags`; revisions not imported; Metatag to `xrowmetadata`.
3. **Drupal addresses** (2 minutes). Path aliases become node aliases or redirecting aliases; `/node/123` is linked
   too, so add an alias or a web server redirect for every node id; carry over the Redirect module's table.
4. **Joomla export** (3 minutes). The Web Services API (Joomla 4 and 5) with a token in the `X-Joomla-Token` header,
   or the database on a copy. The guide marks two points as not verified: a core "export the site" function and the
   API's pagination parameters.
5. **Joomla mapping and addresses** (4 minutes). `introtext` and `fulltext` (split by the read-more line) to `intro`
   and `body`; categories as folders; modules as layout blocks. Addresses depend on the SEF settings and the menu:
   build the redirect table from a crawl and the server log, not from the database alone.
6. **Compare** (3 minutes). Same frame, three different exports.

### Common questions

**Why not read Drupal's database directly?** Possible, but it couples the export to the schema version; JSON:API is
documented and needs no Drupal code (Drupal guide, section 1).

**Can Joomla passwords carry over?** Test whether the hashes are standard PHP `password_hash()` values; then they
work as type 7; otherwise plan resets (Joomla guide, section 2).

**What about Joomla extensions such as K2 or shops?** Each is its own decision; inventory them early.

### Pitfalls

- **Counting pages, not items.** A JSON:API page can hold fewer items because of access checks; count against the
  inventory.
- **Deep Paragraphs nesting.** Design the child classes first; test the deepest nesting.
- **Teaching the unverified Joomla points as fact.** Say what the guide says: not verified.

## Hands-on exercise

A comparison exercise in pairs, with both guides open.

1. Make a table with three columns, **WordPress**, **Drupal**, **Joomla**, and these rows: export source, its limit,
   remote id pattern, rich text field, taxonomy, page building content, password plan, address trap.
2. Fill in the Drupal column from [the Drupal guide](../../../../install/migration/drupal.md).
3. Fill in the Joomla column from [the Joomla guide](../../../../install/migration/joomla.md).
4. Fill in the WordPress column from lesson 7.1.
5. For a Drupal site with 300 nodes linked in text as `/node/<id>`, write how you resolve those links after the
   import (a second pass by remote id) and how old `/node/<id>` addresses keep working.
6. For a Joomla site with the address `/index.php?option=com_content&view=article&id=12` and the SEF address
   `/blog/my-article`, write where each redirect lives.
7. Write the Drush commands the Drupal guide names for the other direction (Exponential to Drupal).

## Expected result

- The table has 24 filled cells; the Drupal export source is JSON:API (50 resources per page), the Joomla one the Web
  Services API or the database.
- Step 5: a second import pass that rewrites `/node/<id>` links to the new nodes by remote id, plus an alias or a web
  server redirect for every node id.
- Step 6: the query-string form in the web server, the SEF path as a redirecting custom URL alias.
- Step 7: `migrate:import`, `migrate:rollback`, `migrate:status`.
- The trainer checks the address trap row for all three systems.

## Quiz

1. How many resources does Drupal's JSON:API return per request at most?
   A. 10 B. 50 C. 100 D. unlimited
2. Why must Drupal's `/node/<id>` addresses be redirected even when every node has a path alias?
3. Where do Joomla's `introtext` and `fulltext` go?
4. Why is a Joomla site's redirect table built from a crawl and the log, not from the database alone?
5. True or false: the Joomla guide confirms a core "export the site's content" function.

<details>
<summary>Answers</summary>

1. **B** ([Drupal guide, section 1](../../../../install/migration/drupal.md#1-what-drupal-can-export)).
2. The system path is linked to as well
   ([Drupal guide, section 4](../../../../install/migration/drupal.md#4-addresses-and-redirects)).
3. To `intro` and `body` (`ezxmltext`), HTML converted
   ([Joomla guide, section 2](../../../../install/migration/joomla.md#2-mapping-joomla-to-exponential)).
4. The menu decides the path
   ([Joomla guide, section 4](../../../../install/migration/joomla.md#4-addresses-and-redirects)).
5. **False.** It was not found and is marked not verified either way
   ([Joomla guide, section 1](../../../../install/migration/joomla.md#1-what-joomla-can-export)).

</details>

## Further reading

- [Drupal and Exponential](../../../../install/migration/drupal.md): both directions with sources.
- [Joomla and Exponential](../../../../install/migration/joomla.md): both directions with sources.
- [Migrating to and from Exponential, section 4](../../../../install/migration/README.md#4-migrations-from-and-to-other-systems):
  the guides for Magento, Shopify, Contentful, AEM and site builders.

## Related pages

- [Module 7: From other systems](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
