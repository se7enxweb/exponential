# LoveStack features in Exponential: the detailed pages

Exponential 6.0.15 brings 36 features and a set of fixes from **LoveStack**, Mugo Web's
distribution of the legacy kernel originally published by eZ Systems
(`mugoweb/ezpublish-legacy`), into the Exponential distribution. Each one is rebuilt in
Exponential's own APIs, documented and tested, with thanks to Mugo Web.

Start with the overview, [LoveStack features in Exponential 6](../lovestack.md): what each
feature does, who it is for, how to switch it on, and the LoveStack commit it comes from. The
pages here go into detail.

**If you never ran LoveStack, there is nothing to do.** The features are part of 6.0.15; any
that could change how an existing site behaves is off until you turn it on.

## The pages

| Page | For | What it holds |
|---|---|---|
| [GETTING_STARTED.md](GETTING_STARTED.md) | everyone | does this concern me, what changes on my site, a glossary |
| [FEATURES.md](FEATURES.md) | administrators, editors | every feature in one table per area, with its default on an upgrade and on a new installation |
| [API.md](API.md) | developers | the classes, methods, settings, template functions and console commands |
| [MIGRATION.md](MIGRATION.md) | sites moving from LoveStack | step by step, from the backups to your own code |
| [TESTING.md](TESTING.md) | developers, reviewers | the tests of each feature and how to run them |
| [TESTS-MAPPING.md](TESTS-MAPPING.md) | developers, reviewers | LoveStack's own test cases and where they are in Exponential's suite |
| [PHP8-REVIEW.md](PHP8-REVIEW.md) | developers | LoveStack's PHP 7.2 to 8.3 fixes, site by site, and what Exponential needed |
| [L01-callers.md](L01-callers.md) | administrators | who calls `ezjscnode`, and what requiring a policy for it changes |
| [FAQ.md](FAQ.md) | everyone | common questions |
| [ROADMAP.md](ROADMAP.md) | everyone | how the work was done, and what comes next |
| [CHANGELOG.md](CHANGELOG.md) | everyone | the releases that carry these features |

The task guides of the documentation describe each feature where you would look for it (roles,
security and audit, content and editing, templates, URLs, operating a site); the overview links
to the right section for every feature.

## In numbers

Both projects come from the same legacy kernel; their histories part on 9 March 2016 (upstream
commit `3429aa91bb`). Compared up to LoveStack's latest commit, `e61e9fdf0c` of 23 February 2026:

| | |
|---|---|
| LoveStack changes compared | 272 commits by Mugo Web and contributors, 983 files |
| Already in Exponential before the port | many of them, often in a different form: PHP 7 and 8 compatibility, SameSite cookies, password hashing, the trash date, WebP, SQLite, and more ([list](../lovestack.md#already-in-exponential)) |
| Features brought over | 36 (L01 to L36), plus LoveStack's session `SameSite` setting name and the PHP 8 review |
| Not brought over | 14 items, each with its reason ([list](../lovestack.md#not-brought-over-and-why)) |

## About LoveStack

LoveStack is developed by Mugo Web at <https://github.com/mugoweb/ezpublish-legacy> and
published on Packagist as
[`mugoweb/ezpublish-legacy`](https://packagist.org/packages/mugoweb/ezpublish-legacy). When the
port was made (12 October 2026), its latest release was `v2025.03.1-1` (1 March 2025), its
latest commit was from 23 February 2026, and its `composer.json` supported PHP 7.3–7.4 and
8.1–8.3. Exponential 6 supports PHP 8.1 to 8.5.

Both projects are licensed under the GNU GPL version 2 (Exponential: version 2 or later). Code
in Exponential that derives from a LoveStack change keeps every existing copyright notice and
names the LoveStack commit it came from; see
[Licence and credits](../lovestack.md#licence-and-credits).
