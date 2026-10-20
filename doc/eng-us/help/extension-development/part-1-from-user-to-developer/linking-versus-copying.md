---
title: "Linking versus copying: citing sources fairly"
audience: user, developer, designer, app-creator
category: extension-development
order: 160
summary: When to link to a source, when to quote it, when you may copy and adapt it, and how to give credit in code comments, READMEs, documentation and commit messages - with the patterns this project uses.
keywords: link, copy, quote, cite, citation, attribution, credit, source, README, commit message, documentation, fair, plagiarism
type: help
---
# Linking versus copying: citing sources fairly

Part 1, chapter 16. You will learn from other people's work constantly. This chapter gives you a simple rule for
using it fairly, and the patterns this project uses to give credit in code, documentation and history.

## The rule

1. **Link** by default. A link sends the reader to the source, in its context, kept up to date by its author.
2. **Quote** a short passage when the exact words matter, with a link and the date you read it.
3. **Copy and adapt** only when the license allows it, and then **keep the notices and say where it came from**.
4. **Never** present someone else's work as yours.

| Source | Link | Quote briefly | Copy and adapt |
|---|---|---|---|
| A page on a site that says "All rights reserved" | yes | yes, short, with credit | no |
| eZpedia, this help, the free books under the GNU FDL | yes | yes | yes, under the GNU FDL, with credit |
| Code under the GPL or MIT | yes | yes | yes, keeping the license and copyright notices |
| A forum post without a license | yes | yes, short | only the idea, unless the author agrees |
| Code without any license | yes | no | no |

## Giving credit in code

When you copy or adapt code under a free license, keep its header and add your own line:

```php
<?php
/**
 * Adapted from <project> (<link to the original file or repository>), <license>.
 *
 * @copyright Copyright (C) <years> <original holder>
 * @copyright Copyright (C) <year> <you or your organization> (changes)
 * @license GNU General Public License v2.0 (or any later version)
 */
```

Many extensions in this ecosystem carry several copyright lines in `ezinfo.php` for the same reason: each line credits
someone who worked on it. `owsimpleoperator`'s `ezinfo.php`, for example, names 7x and Brookins Consulting together with
OPEN WIDE, the company that wrote the extension first. Keep such lines; add yours.

## Giving credit in a README or documentation

A short "Credits" or "Origin" section is enough:

```text
## Origin

This extension is a port of <upstream package> (<link>), published under the <license>.
The structure of its services follows the upstream package; the code was rewritten for Exponential 6.
```

The Layouts packages do exactly this: `explayouts_core`'s README says it is "an Exponential Legacy port inspired by
the `netgen/layouts-core` package", and `explayouts`' README the same for `netgen/layouts-ibexa`.

## Giving credit in commit messages

A commit message is permanent and public. When a commit brings in someone else's work, say so in its body:

```text
Added: Initial import of the class conversion extension

Forked from <original extension> and hardened for PHP 8, with a transactional
rollback so that a failed conversion leaves the content untouched.
```

That is close to the real first commit of `se7enxweb/expchangeclass` (commit `7c3f164`), which says the extension was
"forked from eZChangeclass and hardened for PHP 8". When you merge a contributor's pull request, merge it as a merge
commit so their own commits, with their names, stay in the history (see
[Pull request etiquette](../part-5-extend-fork-contribute/pull-request-etiquette.md)).

## Giving credit in a help page

This help links its sources and names files outside `doc/` as paths. When you adapt text from eZpedia or a free book
into a help page:

- name the source and link it in the text or in "Related pages",
- say that you adapted it ("adapted from ... and updated for Exponential 6"),
- keep the page under the GNU FDL (every help page ends with the Free documentation line).

## What fair use of an idea looks like

You read a forum post that solves your problem with a clever SQL query. You may:

- build your own solution using the same idea, written your own way,
- link the post in a comment above your code (`// Approach from <link>`),
- thank the author in the thread.

That is fair, honest and helps the next reader find the original discussion.

## Related pages

- Previous: [Reading licenses before you reuse anything](reading-licenses-before-reusing.md)
- Next: [Evaluating an old extension before you use it](evaluating-an-old-extension.md)
- [Attribution and copyright notices](../part-5-extend-fork-contribute/attribution-and-copyright-notices.md)
- [Rebranding ethically](../part-5-extend-fork-contribute/rebranding-ethically.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
