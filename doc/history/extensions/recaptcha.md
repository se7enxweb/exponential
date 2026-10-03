# recaptcha (reCAPTCHA datatype): chronicle

The reCAPTCHA datatype was refactored for reCAPTCHA v2, PHP 8 and Exponential 6 in August 2024 and adopted the Exponential metadata in September 2026. See the [feature page](../../features/6.0/extensions/recaptcha.md).

This page lists **every one of the 18 changes** of the repository `recaptcha` between 2024-08-06 and 2026-09-30, by month, with what kind of change each is. Read it to find out when a behaviour arrived and which release you need for it. The complete machine-made record, with sizes, is the [change ledger](../ledger/recaptcha.md); what each release contains is in the [release notes](../../changelogs/extensions/recaptcha.md); how to use the extension is on its [feature page](../../features/6.0/extensions/recaptcha.md).

| Kind | Changes |
|---|---|
| feature | 6 |
| fix | 1 |
| docs | 6 |
| release | 4 |
| no user benefit | 1 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2024-08-06 | 1.4 | [`0167b15`](https://github.com/se7enxweb/recaptcha/commit/0167b15) |
| 2024-09-14 | v1.4.1 | [`88e92fe`](https://github.com/se7enxweb/recaptcha/commit/88e92fe) |
| 2024-09-14 | v1.4.2 | [`c5e7294`](https://github.com/se7enxweb/recaptcha/commit/c5e7294) |
| 2026-09-27 | v1.4.3 | [`a26e413`](https://github.com/se7enxweb/recaptcha/commit/a26e413) |
| 2026-09-28 | v1.4.4 | [`bfcb48b`](https://github.com/se7enxweb/recaptcha/commit/bfcb48b) |
| 2026-09-30 | v1.4.5 | [`fbf206e`](https://github.com/se7enxweb/recaptcha/commit/fbf206e) |

## Timeline

### 2024-08

The month across all extensions: [August 2024](months/2024-08.md). [Ledger of this month](../ledger/recaptcha.md#2024-08-8-changes).

- 2024-08-06 [`713b105`](https://github.com/se7enxweb/recaptcha/commit/713b105) (feature) Refactored extension for latest v2 captcha support and PHP8 and eZ 5 Datatype Changes and Google Recaptcha Autoloads Names Space Usage. Works with eZ 6.x
- 2024-08-05 [`ff2ec1d`](https://github.com/se7enxweb/recaptcha/commit/ff2ec1d) (docs) Create README.md
- 2024-08-05 [`8e8f6e9`](https://github.com/se7enxweb/recaptcha/commit/8e8f6e9) (docs) Delete README replaced with README.md
- 2024-08-06 [`0167b15`](https://github.com/se7enxweb/recaptcha/commit/0167b15) (feature) Refactor composer support to require google/recaptcha composer package **Release 1.4.**
- 2024-08-06 [`e6250a5`](https://github.com/se7enxweb/recaptcha/commit/e6250a5) (docs) Renamed CHANGELOG File to md file extension
- 2024-08-06 [`48e1073`](https://github.com/se7enxweb/recaptcha/commit/48e1073) (docs) Renamed CHANGELOG File to md file extension
- 2024-08-06 [`ce40504`](https://github.com/se7enxweb/recaptcha/commit/ce40504) (feature) Added recaptcha v3 Pure JS feedback_form template override to extension standard design offering
- 2024-08-06 [`1033e65`](https://github.com/se7enxweb/recaptcha/commit/1033e65) (feature) Refactor to use ini for siteKey data storage

### 2024-09

The month across all extensions: [September 2024](months/2024-09.md). [Ledger of this month](../ledger/recaptcha.md#2024-09-4-changes).

- 2024-09-14 [`c38c963`](https://github.com/se7enxweb/recaptcha/commit/c38c963) (fix) Update recaptchalib.php Bugfix for booting extension in class editor from composer installation
- 2024-09-14 [`88e92fe`](https://github.com/se7enxweb/recaptcha/commit/88e92fe) (release) Update ezinfo.php bump version number **Release v1.4.1.**
- 2024-09-14 [`8dcae38`](https://github.com/se7enxweb/recaptcha/commit/8dcae38) (feature) Update recaptchatype.php Translate german validation error messages to english for general use.
- 2024-09-14 [`c5e7294`](https://github.com/se7enxweb/recaptcha/commit/c5e7294) (release) Update ezinfo.php version bump **Release v1.4.2.**

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/recaptcha.md#2026-03-1-changes).

- 2026-03-02 [`1ef3f78`](https://github.com/se7enxweb/recaptcha/commit/1ef3f78) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/recaptcha.md#2026-09-5-changes).

- 2026-09-27 [`a26e413`](https://github.com/se7enxweb/recaptcha/commit/a26e413) (docs) The extension states its version, license and website **Release v1.4.3.**
- 2026-09-28 [`9b14348`](https://github.com/se7enxweb/recaptcha/commit/9b14348) (feature) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`bfcb48b`](https://github.com/se7enxweb/recaptcha/commit/bfcb48b) (release) Version 1.4.4 **Release v1.4.4.**
- 2026-09-30 [`f393ea8`](https://github.com/se7enxweb/recaptcha/commit/f393ea8) (docs) The description calls the product Exponential
- 2026-09-30 [`fbf206e`](https://github.com/se7enxweb/recaptcha/commit/fbf206e) (release) Version 1.4.5 **Release v1.4.5.**

## Related pages

- [Feature page](../../features/6.0/extensions/recaptcha.md)
- [Release notes](../../changelogs/extensions/recaptcha.md)
- [Change ledger](../ledger/recaptcha.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
