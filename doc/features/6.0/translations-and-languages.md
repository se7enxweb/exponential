# Translations and languages

On 27 and 28 September 2026 the translation catalogues of Exponential were
brought up to date and made complete for English (`eng-US`) and German
(`ger-DE`), almost every visible text of the administration and the standard
designs became a translation string, and every shipped locale got a correct
HTML language tag.

## What is translated now

| Area | Result |
|---|---|
| Kernel, `lib` and design sources | The untranslated template carries 1,193 source strings that had been added since it was last generated. |
| `eng-US` | A new, finished translation with every message of the untranslated template, in US English; also for the `ezoe` and `ezjscore` extensions. `ezpI18n` looks up translations for `eng-US` now that it has one. |
| German | All kernel messages are present and finished, including 139 that were present but empty. The `ezoe` German translation covers every message the editor uses. Wrong terms were corrected ("Content structure" was translated as "Navigationsteil" and is "Inhalts-Struktur"). |
| Admin and admin3 designs | Every visible text is a translation string. |
| Standard, base, mysite and plain designs | Every visible text is a translation string. |
| PHP-produced texts | Setup information and the cronjob console, the RAD tool pages (counts, hints), the RAD wizards and the extension point survey (runtime messages), and menu entries (`Name` and `Tooltip` of top menu tabs, the Setup menu entries Maintenance, Cronjobs, Preload Sites and oAuth admin). |
| `ezoe` and `ezjscore` | Online editor dialogs translate their remaining texts; `ezjscore` loads its own translations. |
| New features | Package comparison and import, store dashboard, maintenance, extension loading order, role editor, class editor buttons, about and copyright pages, the form expired page. |

About two dozen extensions released versions whose visible texts are translation
strings, with German.

### Use a language

Set the language of an administrator in the user's preferences or give a
siteaccess a different locale in `site.ini [RegionalSettings]`. To check what is
still untranslated for a language:

```bash
php bin/php/ezchecktranslation.php --help
php bin/php/ezgeneratetranslationcache.php --help
```

### Make a string translatable in your own code

Use the same functions the kernel uses and a context that names the template or
class: `ezpI18n::tr( 'design/admin/shop/dashboard', 'Orders today' )` in PHP and
`{'Orders today'|i18n( 'design/admin/shop/dashboard' )}` in a template. A string
that is only built at runtime must be a literal in an `ezpI18n::tr()` call so the
extractor can see it.

## HTML language tags

Page layouts print `eZLocale::httpLocaleCode()`, which is `[HTTP] ContentLanguage`
of the locale file. Twenty locales gave a three-letter code, an obsolete
Norwegian form or a wrong region, so pages printed `lang="no-bokmaal"`,
`swe-SE` or `dut-NL`. Every shipped locale now names a valid BCP 47 tag
(ISO 639-1 language, ISO 3166 region where one exists):

| Locale file | Before | Now |
|---|---|---|
| `ara-SA` | `ara-SA` | `ar-SA` |
| `cat-ES` | `cat-ES` | `ca-ES` |
| `dut-NL` | `dut-NL` | `nl-NL` |
| `ell-GR` | `ell-GR` | `el-GR` |
| `epo-EO` | `epo-EO` | `eo` |
| `esl-MX` | `mx-esl` | `es-MX` |
| `fin-FI` | `fi` | `fi-FI` |
| `heb-IL` | `heb-IL` | `he-IL` |
| `hin-IN` | `hi-HI` | `hi-IN` |
| `hun-HU` | `hun-HU` | `hu-HU` |
| `ind-ID` | `ind-ID` | `id-ID` |
| `nno-NO` | `no-nynorsk` | `nn-NO` |
| `nor-NO` | `no-bokmaal` | `nb-NO` |
| `por-MZ` | `pt-PT` | `pt-MZ` |
| `ser-SR`, `srp-RS` | `sr-SR` | `sr-RS` |
| `slk-SK` | `sk` | `sk-SK` |
| `swe-SE` | `swe-SE` | `sv-SE` |
| `tur-TR` | `tur-TR` | `tr-TR` |
| `ukr-UA` | `ua-UA` | `uk-UA` |

The setup wizard still preselects Norwegian Bokmål for a browser that asks
for `no`. If you have CSS or JavaScript that matched the old `lang` values (for
example `[lang="no-bokmaal"]`), change the selector.

## Languages in a new installation

See [Installing in one command](install-in-one-command.md) for the language
rules of the setup wizard and the kickstarter. In short: the primary language
defaults to `eng-US` (the language the bundled data is in); other primary
languages install cleanly; the package language step no longer offers
`eng-US` for mapping; siteaccesses for two locales of one language get different
names; the setup log records the languages and the package language map; and a
reinstall over an existing site gets its content languages again. A class or
attribute created before any content language exists is named in the site's
language.

### A translation siteaccess that looks like the main site

Give it `[SiteAccessSettings] ExtensionSettingsSiteAccess=<main siteaccess>`.
See [Extension loading order](extension-loading-order.md).

## Related pages

- [Translations of the package comparison](package-compare-and-import.md)
- [Behaviour changes of September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
