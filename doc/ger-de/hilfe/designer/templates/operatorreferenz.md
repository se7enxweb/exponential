---
title: Operatorreferenz
audience: designer, developer
category: templates
order: 320
summary: Jeder Template-Operator der Template-Bibliothek und des Kernels, gruppiert nach der Datei, die ihn registriert, mit einer Zeile zu seinem Zweck, dazu die Operatoren, die die wichtigsten designbezogenen Erweiterungen hinzufügen, und wie Sie die Operatoren Ihrer eigenen Installation auflisten.
keywords: Operatorreferenz, alle Operatoren, Liste der Template-Operatoren, eztemplateautoload, Kernel-Operatoren, Bibliotheksoperatoren, Operatoren von Erweiterungen, Operatornamen, operator reference, all operators, template operators list, kernel operators, library operators, extension operators, operator names
type: help
---
# Operatorreferenz

Diese Seite listet die Template-Operatoren von Exponential danach auf, wo sie registriert sind. Die Zweckangaben sind kurz;
die am Ende verlinkten Seiten zu den Familien erklären die wichtigen mit Beispielen.

## Inhalt

1. [Die Template-Bibliothek](#die-template-bibliothek)
2. [Der Kernel](#der-kernel)
3. [PHP-Funktionen per Einstellung](#php-funktionen-per-einstellung)
4. [Designbezogene Erweiterungen](#designbezogene-erweiterungen)
5. [Die Operatoren Ihrer Installation auflisten](#die-operatoren-ihrer-installation-auflisten)

## Die Template-Bibliothek

Registriert in `lib/eztemplate/classes/eztemplateautoload.php`:

| Operatoren | Klasse | Zweck |
|---|---|---|
| `array`, `hash` | eZTemplateArrayOperator | ein Array oder einen Hash bilden |
| `append`, `prepend`, `merge` (älter: `array_append`, `array_prepend`, `array_merge`) | eZTemplateArrayOperator | Einträge hinzufügen, Arrays verbinden |
| `contains`, `compare`, `begins_with`, `ends_with` | eZTemplateArrayOperator | Prüfungen auf Arrays und Zeichenketten |
| `extract`, `extract_left`, `extract_right`, `reverse`, `unique`, `insert`, `remove`, `replace`, `repeat` | eZTemplateArrayOperator | Teile entnehmen, ändern |
| `implode`, `explode`, `array_sum` | eZTemplateArrayOperator | verbinden, aufteilen, summieren |
| `fetch`, `fetch_alias` | eZTemplateExecuteOperator | eine Fetch-Funktion aufrufen ([Fetch-Grundlagen](fetch-grundlagen.md)) |
| `l10n`, `locale`, `datetime`, `currentdate`, `maketime`, `makedate`, `gettime` | eZTemplateLocaleOperator | Formatierung nach Locale und Datumsangaben |
| `attribute`, `dump` | eZTemplateAttributeOperator | die Attribute eines Werts zeigen (Debugging) |
| `nl2br` | eZTemplateNl2BrOperator | Zeilenumbrüche zu `<br>` |
| `concat`, `indent` | eZTemplateTextOperator | Texte verbinden, einrücken |
| `si` | eZTemplateUnitOperator | Zahlen mit Einheitenpräfixen (Größen) |
| `lt`, `gt`, `le`, `ge`, `eq`, `ne`, `null`, `not`, `true`, `false`, `or`, `and`, `choose` | eZTemplateLogicOperator | Vergleiche und Logik |
| `is_array`, `is_boolean`, `is_integer`, `is_float`, `is_numeric`, `is_string`, `is_object`, `is_class`, `is_null`, `is_set`, `is_unset`, `get_type`, `get_class` | eZTemplateTypeOperator | Typprüfungen |
| `cond`, `first_set` | eZTemplateControlOperator | einen Wert wählen |
| `sum`, `sub`, `inc`, `dec`, `div`, `mod`, `mul`, `max`, `min`, `abs`, `ceil`, `floor`, `round`, `int`, `float`, `count`, `roman`, `rand` | eZTemplateArithmeticOperator | Arithmetik |
| `texttoimage`, `image`, `imagefile` | eZTemplateImageOperator | als Bilder dargestellter Text (veraltet) |
| `upcase`, `downcase`, `count_words`, `count_chars`, `trim`, `break`, `wrap`, `upfirst`, `upword`, `simplify`, `wash`, `chr`, `ord`, `shorten`, `pad` | eZTemplateStringOperator | Text formen und maskieren |
| `crc32`, `md5`, `rot13` | eZTemplateDigestOperator | Prüfsummen |
| `expinfo` | eZTemplateExpInfoOperator | Metadaten von Erweiterungen: `expinfo()` die aktiven, `expinfo( 'all' )`, `expinfo( '<name>' )` |
| `strlen`, `substr`, `sprintf`, `str_contains`, `number_format`, `strip_tags` und etwa fünfzig weitere | eZTemplateStringsOperator | PHP-Zeichenkettenfunktionen ([PHP-Zeichenkettenfunktionen als Operatoren](php-zeichenkettenfunktionen-als-operatoren.md)) |

## Der Kernel

Registriert in `kernel/common/eztemplateautoload.php`:

| Operatoren | Zweck |
|---|---|
| `ezurl`, `ezroot`, `ezdesign`, `ezimage`, `exturl`, `ezsys` | URLs und Design-Dateien ([URL-Operatoren](url-operatoren.md)) |
| `ezhttp`, `ezhttp_hasvariable` | Werte der Anfrage ([Der Operator ezhttp](der-ezhttp-operator.md)) |
| `ezini`, `ezini_hasvariable` | Einstellungen ([Der Operator ezini](der-ezini-operator.md)) |
| `i18n`, `x18n`, `d18n` | Übersetzung ([Der Operator i18n](der-i18n-operator.md)) |
| `alphabet` | die Buchstaben für Filter von A bis Z, aus `content.ini [AlphabeticalFilterSettings]` |
| `month_overview` | Daten für einen Monatskalender |
| `autolink` | URLs in Text zu Links machen (`template.ini [AutoLinkOperator]`) |
| `simpletags` | einfaches Markup zu HTML (`template.ini [SimpleTagsOperator]`) |
| `treemenu`, `content_structure_tree`, `topmenu` | Daten für Menüs und Bäume |
| `wordtoimage`, `mimetype_icon`, `class_icon`, `classgroup_icon`, `action_icon`, `icon`, `flag_icon`, `icon_info` | Icons der Icon-Themes |
| `ezpreference` | eine Einstellung des aktuellen Benutzers |
| `siteaccess_url`, `siteaccess_menu`, `siteaccess_menu_key` | Links zu anderen Siteaccesses (Sprachumschalter) |
| PHP-Funktionen aus `template.ini [PHP] PHPOperatorList[]` | siehe unten |
| `module_params` | Modul und View der aktuellen Anfrage |
| `ezpackage` | Paketinformationen |
| `eztoc` | ein Inhaltsverzeichnis von Rich Text |
| `ezmodule` | Modulinformationen (siehe `doc/eng-us/features/3.10/ezmodule_template_operator.txt`) |
| `installation_name`, `is_production_system` | der Name der Installation und ob sie produktiv ist |
| `audit_label` | Beschriftungen des Audit-Protokolls |
| `exp_environment`, `exp_environment_badge` | die Umgebung (Entwicklung, Staging, Produktion) und ihre Plakette |
| `csp_nonce` | die Nonce der Content Security Policy der Anfrage ([CSP-sicheres Design](../../../../eng-us/help/designers/theme-design/csp-safe-design.md)) |

`kernel/private/eztemplate/eztemplateautoload.php` fügt `language_switcher` hinzu.

## PHP-Funktionen per Einstellung

`settings/template.ini [PHP] PHPOperatorList[]` stellt PHP-Funktionen unter Operatornamen bereit; mitgeliefert: `urlencode`,
`urldecode`, `rawurlencode`, `rawurldecode`.

## Designbezogene Erweiterungen

Nur verfügbar, wo die Erweiterung aktiv ist:

| Erweiterung | Operatoren |
|---|---|
| ezjscore | `ezscript`, `ezscript_require`, `ezscript_load`, `ezscriptfiles`, `ezcss`, `ezcss_require`, `ezcss_load`, `ezcssfiles`, `json_encode`, `xml_encode`, `node_encode`, `has_access_to_limitation` ([Template-Operatoren von ezjscore](ezjscore-template-operatoren.md)) |
| ezwebin | `ezpagedata`, `ezpagedata_set`, `ezpagedata_append`, `ezkeywordlist`, `ezarchive`, `eztagcloud` |
| explayouts | `expl_first`, `expl_has`, `expl_parse`, `expl_strip`; Funktion `explblock` |
| eztags | `eztags_parent_string`, `eztagscloud`, `latest_tags`, `tag_icon`, `user_limitations` |
| ezstarrating | `fetch_by_starrating`, `fetch_starrating_data`, `fetch_starrating_stats` |
| ezoe | `ezoe_ini_section` |
| expui | `exp_config` |
| sevenx_themes_media | 54 Operatoren des Themes media (`intro`, `title`, `path`, `content_link`, `image`, `ng_image_alias`, `site_url`, `json_ld` ...), siehe seine `doc/USAGE.md` |

## Die Operatoren Ihrer Installation auflisten

Auf einer Entwicklungsinstallation sind die für einen Siteaccess registrierten Operatoren die Schlüssel von
`eZTemplate::factory()->Operators`; ein Entwickler kann sie mit einem kleinen Skript ausgeben, das über
`php bin/php/ezexec.php` läuft. Operatoren von Erweiterungen erscheinen nur, wenn die Erweiterung für diesen Siteaccess aktiv
und in `[TemplateSettings] ExtensionAutoloadPath[]` aufgeführt ist.

## Verwandte Seiten

- [Operatoren im Überblick](operatoren-ueberblick.md)
- [Funktionsreferenz](funktionsreferenz.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
