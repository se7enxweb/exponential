# Changelog

Changes to the LoveStack features in Exponential, newest first. The full list, one line per
feature with its LoveStack commit and upgrade note, is in the 6.0.15 changelog:
[LoveStack features (from 2026-10-12)](../../../changelogs/6.0/6.0.15.md#lovestack-features-from-2026-10-12).

## 6.0.15 (in development)

- Added: 36 features from LoveStack (mugoweb/ezpublish-legacy, commit `e61e9fdf0c` of 23 February
  2026), rebuilt in Exponential with thanks to Mugo Web: L01–L06 security and sessions, L07–L12
  diagnostics, L13–L21 content and administration, L22–L24 rich text editor, L25–L31 developer
  APIs and settings, L32–L36 URLs and search ([overview](../lovestack.md#feature-by-feature)).
- Added: LoveStack's `site.ini [Session] SameSite` setting name is read when `CookieSameSite` is
  not set.
- Added: `./console exp:lovestack check` and `./console exp:lovestack migrate` for sites moving
  from LoveStack ([MIGRATION.md](MIGRATION.md)).
- Added: LoveStack's own test cases for behaviour Exponential has, and a test of every feature
  ([TESTS-MAPPING.md](TESTS-MAPPING.md), [TESTING.md](TESTING.md)).
- Updated: the PHP 8 cases LoveStack fixed, reproduced on PHP 8.5 and 8.1; those that still
  failed are fixed ([PHP8-REVIEW.md](PHP8-REVIEW.md)).
- Updated: shop order lists find a customer whose e-mail address has a "+" (LoveStack commit
  `5a17c08bfc`).
- Added: this documentation.

Upgrade notes in short: nothing changes on an existing site unless you switch it on, except the
plain bug fixes, the logging changes of L07 and L08, the warnings of L09 and L12, the Copy button
of L15, and the security checks of L03, L05 and L06. New installations also switch on L01, L02,
L04 (the unique login index) and L23.
