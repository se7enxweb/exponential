---
title: "Story: MongoDB as a database"
audience: press
category: press-media
order: 70
summary: A feature story about Exponential 6.0.14 storing its whole content repository in MongoDB - what works, the documented limits, how it fits the six supported databases - with printable facts, sources and the claims to avoid.
keywords: story, MongoDB, document database, NoSQL, database, 6.0.14, content repository, limits, databases, PHP mongodb extension
type: help
---
# Story: MongoDB as a database

This page is for technical journalists. A content management system with a relational content model running on a
document database is unusual; Exponential 6.0.14 added it, with its limits written down.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [The documented limits](#the-documented-limits)
5. [What not to claim](#what-not-to-claim)
6. [Angles and headline ideas](#angles-and-headline-ideas)

## The story in one paragraph

"Exponential 6.0.14 (June 2026) can store the whole content repository in MongoDB instead of MySQL, PostgreSQL or
SQLite, and the setup wizard offers MongoDB next to the other database systems" ([MongoDB as the database](../../features/6.0/mongodb-database-support.md)).
An adapter turns the kernel's SQL-shaped calls into MongoDB operations. The project lists what works and what does
not, and advises readers to read the limits before choosing it for a production site.

## What is new, and when

| What | When | Source |
|---|---|---|
| MongoDB 8 as a database | 1 June 2026 | [June 2026](../../history/2026/2026-06a.md) |
| First release with it, 6.0.14 | 7 June 2026 | [The 6.0.x releases at a glance](the-6-0-x-releases-at-a-glance.md) |

## Facts you can print

| Fact | Source |
|---|---|
| MongoDB is third in the order of preference: SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Database support](../database-support.md) |
| Works: content editing and publishing, the content tree, URL aliases, roles, the admin interface, the front site, the setup wizard | [What works and what does not](../../features/6.0/mongodb-database-support.md#what-works-and-what-does-not-june-2026) |
| A complete porting reference documents every kernel class touched | [MongoDB as the database](../../features/6.0/mongodb-database-support.md) |

## The documented limits

From the feature page (June 2026): deep URL alias regeneration after moving subtrees is incomplete; role and policy
listing pages can be empty although permissions are enforced; some cronjobs are not ported; the `eztags` extension is
not usable on MongoDB; the shop checkout and `ezflow` block scheduling are untested end to end; hand-written SQL in
extensions returns nothing.

## What not to claim

| Do not say | Say instead |
|---|---|
| "Runs fully on MongoDB with every feature" | Works for the core; limits are documented |
| "Production-ready on MongoDB" | "The project advises reading the limits before production use" |
| "Experimental" (as the press kit put it in June) | It shipped in the 6.0.14 release, with documented limits |

## Angles and headline ideas

- "A content model from 2002 on a document database."
- "Six databases, one CMS."

## Related pages

- [Story: six databases](story-databases.md)
- [Story: SQLite by default](story-sqlite-by-default.md)
- [Database support](../database-support.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
