# hcaptcha (hCaptcha datatype): release notes

What each release of `hcaptcha` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/hcaptcha.md); the story is in the [chronicle](../../history/extensions/hcaptcha.md).

## v1.2 (2026-10-02)

**Updated**

- Fixed: Added extension.xml, which the extension lacked, and the website in ezinfo.php, so that the about page and the upgrade checks show the extension's version, license and website. ([`e9030b3`](https://github.com/se7enxweb/hcaptcha/commit/e9030b3))

1 version, merge or metadata commit not listed.

## v1.1 (2026-09-30)

**Maintenance, documentation and packaging**

- The about page names the extension hCaptcha extension for Exponential ([`5e280b0`](https://github.com/se7enxweb/hcaptcha/commit/5e280b0))
- The description calls the product Exponential ([`a345c9e`](https://github.com/se7enxweb/hcaptcha/commit/a345c9e))

2 version, merge or metadata commits not listed.

## v1.0 (2024-08-11)

**Added**

- Added recaptcha v3 Pure JS feedback_form template override to extension standard design offering ([`ce40504`](https://github.com/se7enxweb/hcaptcha/commit/ce40504))

**Updated**

- Refactored extension for latest v2 captcha support and PHP8 and eZ 5 Datatype Changes and Google Recaptcha Autoloads Names Space Usage. Works with eZ 6.x ([`713b105`](https://github.com/se7enxweb/hcaptcha/commit/713b105))
- Refactor composer support to require google/recaptcha composer package ([`0167b15`](https://github.com/se7enxweb/hcaptcha/commit/0167b15))
- Refactor to use ini for siteKey data storage ([`1033e65`](https://github.com/se7enxweb/hcaptcha/commit/1033e65))
- Mass update of internals to support hcaptcha apis. Tested as working with Exponential 6 ([`cd25e67`](https://github.com/se7enxweb/hcaptcha/commit/cd25e67))

**Maintenance, documentation and packaging**

- Create README.md ([`ff2ec1d`](https://github.com/se7enxweb/hcaptcha/commit/ff2ec1d))
- Delete README replaced with README.md ([`8e8f6e9`](https://github.com/se7enxweb/hcaptcha/commit/8e8f6e9))
- Renamed CHANGELOG File to md file extension ([`e6250a5`](https://github.com/se7enxweb/hcaptcha/commit/e6250a5))
- Renamed CHANGELOG File to md file extension ([`48e1073`](https://github.com/se7enxweb/hcaptcha/commit/48e1073))

1 version, merge or metadata commit not listed.

## Related

* [Feature page](../../features/6.0/extensions/hcaptcha.md)
* [Chronicle](../../history/extensions/hcaptcha.md)
* [Change ledger](../../history/ledger/hcaptcha.md)
* [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)

## See also

* months: [2024-08](../../history/extensions/months/2024-08.md), [2026-03](../../history/extensions/months/2026-03.md), [2026-09](../../history/extensions/months/2026-09.md), [2026-10](../../history/extensions/months/2026-10.md)
