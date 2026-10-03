# hcaptcha (hCaptcha datatype): chronicle

hCaptcha was created from the reCAPTCHA extension in August 2024 (release 1.0) and received the Exponential name and missing metadata files in September and October 2026. See the [feature page](../../features/6.0/extensions/hcaptcha.md).

This page lists **every one of the 16 changes** of the repository `hcaptcha` between 2024-08-06 and 2026-10-02, by month, with what kind of change each is. Read it to find out when a behaviour arrived and which release you need for it. The complete machine-made record, with sizes, is the [change ledger](../ledger/hcaptcha.md); what each release contains is in the [release notes](../../changelogs/extensions/hcaptcha.md); how to use the extension is on its [feature page](../../features/6.0/extensions/hcaptcha.md).

| Kind | Changes |
|---|---|
| feature | 5 |
| fix | 1 |
| docs | 6 |
| release | 2 |
| no user benefit | 2 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2024-08-11 | v1.0 | [`5aab54d`](https://github.com/se7enxweb/hcaptcha/commit/5aab54d) |
| 2026-09-30 | v1.1 | [`0b1a73f`](https://github.com/se7enxweb/hcaptcha/commit/0b1a73f) |
| 2026-10-02 | v1.2 | [`7b78234`](https://github.com/se7enxweb/hcaptcha/commit/7b78234) |

## Timeline

### 2024-08

The month across all extensions: [August 2024](months/2024-08.md). [Ledger of this month](../ledger/hcaptcha.md#2024-08-10-changes).

- 2024-08-06 [`713b105`](https://github.com/se7enxweb/hcaptcha/commit/713b105) (feature) Refactored extension for latest v2 captcha support and PHP8 and eZ 5 Datatype Changes and Google Recaptcha Autoloads Names Space Usage. Works with eZ 6.x
- 2024-08-05 [`ff2ec1d`](https://github.com/se7enxweb/hcaptcha/commit/ff2ec1d) (docs) Create README.md
- 2024-08-05 [`8e8f6e9`](https://github.com/se7enxweb/hcaptcha/commit/8e8f6e9) (docs) Delete README replaced with README.md
- 2024-08-06 [`0167b15`](https://github.com/se7enxweb/hcaptcha/commit/0167b15) (feature) Refactor composer support to require google/recaptcha composer package
- 2024-08-06 [`e6250a5`](https://github.com/se7enxweb/hcaptcha/commit/e6250a5) (docs) Renamed CHANGELOG File to md file extension
- 2024-08-06 [`48e1073`](https://github.com/se7enxweb/hcaptcha/commit/48e1073) (docs) Renamed CHANGELOG File to md file extension
- 2024-08-06 [`ce40504`](https://github.com/se7enxweb/hcaptcha/commit/ce40504) (feature) Added recaptcha v3 Pure JS feedback_form template override to extension standard design offering
- 2024-08-06 [`1033e65`](https://github.com/se7enxweb/hcaptcha/commit/1033e65) (feature) Refactor to use ini for siteKey data storage
- 2024-08-11 [`cd25e67`](https://github.com/se7enxweb/hcaptcha/commit/cd25e67) (feature) Mass update of internals to support hcaptcha apis. Tested as working with Exponential 6
- 2024-08-11 [`5aab54d`](https://github.com/se7enxweb/hcaptcha/commit/5aab54d) (no user benefit) Create FUNDING.yml **Release v1.0.**

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/hcaptcha.md#2026-03-1-changes).

- 2026-03-02 [`f585a15`](https://github.com/se7enxweb/hcaptcha/commit/f585a15) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/hcaptcha.md#2026-09-3-changes).

- 2026-09-30 [`5e280b0`](https://github.com/se7enxweb/hcaptcha/commit/5e280b0) (docs) The about page names the extension hCaptcha extension for Exponential
- 2026-09-30 [`a345c9e`](https://github.com/se7enxweb/hcaptcha/commit/a345c9e) (docs) The description calls the product Exponential
- 2026-09-30 [`0b1a73f`](https://github.com/se7enxweb/hcaptcha/commit/0b1a73f) (release) Version 1.1 **Release v1.1.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/hcaptcha.md#2026-10-2-changes).

- 2026-10-02 [`e9030b3`](https://github.com/se7enxweb/hcaptcha/commit/e9030b3) (fix) Fixed: Added extension.xml, which the extension lacked, and the website in ezinfo.php, so that the about page and the upgrade checks show the extension's version, license and website.
- 2026-10-02 [`7b78234`](https://github.com/se7enxweb/hcaptcha/commit/7b78234) (release) Version 1.2 **Release v1.2.**

## Related pages

- [Feature page](../../features/6.0/extensions/hcaptcha.md)
- [Release notes](../../changelogs/extensions/hcaptcha.md)
- [Change ledger](../ledger/hcaptcha.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
