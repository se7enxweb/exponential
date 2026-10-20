# Content language fallback: eng-US and a setting instead of a hard-coded eng-GB (6.0.15)

Read this page before you update an installation to the 5 October 2026 changes. It says what changed, how to check
it and what to do.

## What changed

| Change | How to check | What to do |
|---|---|---|
| Names and descriptions of content classes, class attributes, class groups and states are stored under a locale. When no content language is prioritized yet (the setup wizard and command line scripts create classes before the site's languages exist) and `ContentObjectLocale` is not set, `eZSerializedObjectNameList` used a hard-coded `eng-GB`. It now reads the new setting `site.ini [RegionalSettings] ContentObjectFallbackLocale`, and uses `eng-US` when the setting is missing or empty. | `php bin/php/ezexec.php` a script printing `eZSerializedObjectNameList::fallbackLanguageLocale()`; the answer is `eng-US` unless you set the setting. | Nothing, unless your sites are written in another language and you want that one as the last resort: set `ContentObjectFallbackLocale=<locale>` in `settings/override/site.ini.append.php`. |
| The shipped default `site.ini [RegionalSettings] ContentObjectLocale` is `eng-US` (was `eng-GB`). The installer always writes `ContentObjectLocale` into each siteaccess, so only a siteaccess without its own value notices. | `grep -rn ContentObjectLocale settings/siteaccess settings/override` | A siteaccess that relied on the old default and is really written in `eng-GB` sets `ContentObjectLocale=eng-GB` itself. |
| The kernel's clean data in SQL (`kernel/sql/common/cleandata.sql`, `kernel/sql/sqlite/cleandata.sql`) is in `eng-US` like `share/db_data.dba`: the language row is `eng-US` "English (American)", the class, attribute and object names are `eng-US`. Language ids are unchanged. | `grep -c eng-GB kernel/sql/common/cleandata.sql` prints 0. | Nothing for existing sites; a database created from these files has no `eng-GB`. |
| `kickstart.ini--example` no longer lists `eng-GB` as a second language; `kickstart.ini-dist` shows `eng-US` as the example primary language. A kickstart file that lists `Languages[]=eng-GB` adds that language to the new site. | Your `kickstart.ini`, `[language_options]`. | Remove `Languages[]=eng-GB` unless you want a British English translation. |
| The multisite installer packages (`sevenx_multisite`, `sevenx_multisite_clean`) name the folder attributes `tags` and `publish_date` in the configured locale, else the kernel's fallback locale; they fell back to `eng-GB`. | | Nothing. |

Unchanged on purpose: `eng-GB` is still a supported locale (`share/locale/eng-GB.ini`), the source language of the
interface texts (`Locale=eng-GB` switches text translation off), and the `i18n.ini` fallback of the interface translations.

## Removing eng-GB from an existing site

A site that got `eng-GB` only as a by-product (an old kickstart file, the fallback above, the `cjw_newsletter` class
packages before 4.2.1) can remove it on **Setup > Languages** once nothing uses it. The kernel's
`eZContentLanguage::removeLanguage()` refuses while an object or a class has a translation in it; class attribute
descriptions stored with an empty `eng-GB` entry, `eng-GB` names of drafts and URL aliases of removed nodes are not
counted by it but keep the language's bit in the database. Convert those first: move each `eng-GB` entry of a
serialized name list to `eng-US` (keeping a non-empty `eng-US` text), drop the `eng-GB` name rows of versions that have
an `eng-US` one, remove the URL aliases of removed nodes with `eZURLAliasML::removeByAction()`. Then clear the caches
`content`, `classid`, `content_language`, `sortkey`, `urlalias`, `template`, `template-block` and the INI cache.
