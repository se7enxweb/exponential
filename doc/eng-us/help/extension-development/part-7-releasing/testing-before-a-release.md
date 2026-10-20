---
title: Testing before a release
audience: developer, designer, app-creator
category: extension-development
order: 110
summary: What to run before you tag: lint on the oldest and newest PHP, the extension's tests, metadata checks, a clean install from the repository with Composer, and every feature by hand - a tag cannot be corrected.
keywords: testing, release, php -l, tests, xmllint, composer install, clean install, smoke test, oldest PHP, newest PHP, checklist
type: help
---
# Testing before a release

Part 7, chapter 11. A tag cannot be corrected after it is pushed ([Tags and releases are permanent](tags-are-permanent.md)),
so the time to find mistakes is before. This chapter lists what to check, from seconds to an hour, on the exact
commit you will tag.

## 1. The tree is the release

```bash
git status            # nothing to commit, working tree clean
git log -1 --oneline  # the commit you will tag
```

Uncommitted changes are not in the release, whatever your local site shows.

## 2. Automated checks (minutes)

| Check | Command |
|---|---|
| Every PHP file parses, on the oldest and newest PHP, with deprecations shown | `find . -name '*.php' -not -path './vendor/*' -print0 \| xargs -0 -n1 php -d error_reporting=-1 -l 2>&1 \| grep -v '^No syntax errors'` (once per PHP binary) |
| The extension's own tests | `for t in tests/*test.php; do php "$t" \|\| break; done`, or `vendor/bin/phpunit`, whatever the extension uses |
| Metadata parses | `php -l ezinfo.php`, `xmllint --noout extension.xml`, `composer validate` |
| Translations parse | `xmllint --noout translations/*/translation.ts` |
| Versions agree | `grep -n "Version\|<version>" ezinfo.php extension.xml` |

The CI workflow should run the same on every push ([Tests and CI for an upgraded extension](../part-4-upgrading/tests-and-ci-for-an-upgraded-extension.md));
before a release, look at its result for this exact commit.

## 3. A clean install (15 minutes)

Your development site has the extension as a working copy, with every cache and setting you made along the way. A
user's site has none of that. Install the commit you will tag into a fresh development site the way users will:

```json
{
    "repositories": [ { "type": "vcs", "url": "https://github.com/your-account/myext" } ],
    "require": { "your-account/myext": "dev-main" }
}
```

```bash
composer update your-account/myext
```

Then follow your own README, word for word: activate, clear caches, import packages, set settings. Every step the
README forgets shows up here.

## 4. Use it (30 minutes)

- Open every page and view of the extension, as an administrator and as a user with only the extension's policies.
- Run every cronjob part and CLI script once.
- Try the error cases: an empty form, a wrong ID, a missing setting.
- With the debug output on: no error, warning or deprecation from the extension's files.
- With the browser console open: no script errors, no Content Security Policy reports.
- Admin pages in light and dark mode.

## 5. Upgrade from the previous release

Install the previous release, use it, then update to the new commit. Check that settings, content and tables
survive, and that the changelog's upgrade notes are complete. If the update needs a step (a cache, a script), it
belongs in the upgrade notes ([Changelogs](changelogs.md)).

## When something fails

Fix it, commit, and start the list again from step 1 on the new commit. Never tag a commit that failed a check
"because the fix is trivial": the fix is a new commit, and the tag goes on that.

## Related pages

- Previous: [Packages (.ezpkg) and the package creator](packages-ezpkg.md)
- Next: [Announcing a release](announcing-a-release.md)
- [Tests for extensions](../part-3-extension-developer/tests-for-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
