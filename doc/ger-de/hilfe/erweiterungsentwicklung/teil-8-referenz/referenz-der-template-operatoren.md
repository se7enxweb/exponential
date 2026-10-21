---
title: Eingebaute Template-Operatoren
audience: developer, designer
category: extension-development
order: 270
summary: Die Template-Operatoren einer Installation, gruppiert nach implementierender Klasse - Zeichenketten, Arrays, Logik, Arithmetik, Typen, Datumsangaben, URLs, Einstellungen, Übersetzung, Symbole, Zugriff, Kodierung, der Packer.
keywords: Template-Operatoren, Operatorliste, Zeichenketten, Arrays, Logik, Datum, Übersetzung, Symbole, template operators, wash, i18n, ezurl, ezdesign, ezimage, ezini, cond, first_set, count, explode, implode, datetime, l10n, json_encode, has_access_to_limitation, ezcss_load, ezscript_load, operator list
type: help
---
# Eingebaute Template-Operatoren

Teil 8, Referenz 23. Diese Seite führt die Operatoren einer laufenden Installation von Exponential 6 auf, gruppiert
nach der PHP-Klasse, die sie implementiert. Die Liste entstand, indem die Template-Engine einer Demo-Installation nach
jedem registrierten Operator gefragt wurde (Oktober 2026); sie zeigt also, was Templates aufrufen können, nicht, was
ein altes Handbuch sagte. Erweiterungen fügen eigene Operatoren hinzu; diese erscheinen nur, wenn die Erweiterung
aktiv ist. Wie jeder Operator verwendet wird, mit Beispielen, steht in der Hilfe für Designer
([Überblick über die Operatoren](../../designer/vorlagen/operatoren-ueberblick.md) und
[Operatorreferenz](../../designer/vorlagen/operatorreferenz.md)).

## Zeichenketten (eZTemplateStringOperator)

`break`, `chr`, `count_chars`, `count_words`, `downcase`, `ord`, `pad`, `shorten`, `simplify`, `trim`, `upcase`,
`upfirst`, `upword`, `wash`, `wrap`

`wash` maskiert einen Wert für HTML (oder, mit einem Parameter, für `javascript`, `email` und andere Ziele). Jeder Wert,
der nicht aus dem Text Ihres eigenen Templates stammt, geht durch ihn hindurch.

## Weitere Zeichenkettenfunktionen (eZTemplateStringsOperator)

Die Zeichenkettenfunktionen von PHP unter ihren eigenen Namen, darunter `sprintf`, `number_format`, `str_contains`,
`str_starts_with`, `str_ends_with`, `strlen`, `substr_count`, `substr_replace`, `strtr`, `stripos`, `lcfirst`,
`html_entity_decode`, `htmlentities`, `levenshtein`, `similar_text`, `soundex`, `metaphone`, `str_split`,
`str_word_count`, `chunk_split`, `bin2hex`, `hex2bin`, `quoted_printable_encode` und die Vergleichsfunktionen
(`strcmp`, `strcasecmp`, `strnatcmp` ...). Außerdem `nl2br`, `strip_tags`, `urlencode`, `rawurlencode`, `urldecode`,
`serialize`, `unserialize` und `getenv`.

## Arrays (eZTemplateArrayOperator)

`append`, `array`, `array_append`, `array_merge`, `array_prepend`, `array_sum`, `begins_with`, `compare`, `contains`,
`ends_with`, `explode`, `extract`, `extract_left`, `extract_right`, `hash`, `implode`, `insert`, `merge`, `prepend`,
`remove`, `repeat`, `replace`, `reverse`, `unique`

## Logik und Steuerung (eZTemplateLogicOperator, eZTemplateControlOperator)

`and`, `or`, `not`, `eq`, `ne`, `lt`, `le`, `gt`, `ge`, `choose`, `true`, `false`, `null`; `cond`, `first_set`

## Arithmetik (eZTemplateArithmeticOperator)

`sum`, `sub`, `mul`, `div`, `mod`, `inc`, `dec`, `abs`, `ceil`, `floor`, `round`, `int`, `float`, `min`, `max`,
`count`, `rand`, `roman`

## Typen (eZTemplateTypeOperator)

`is_array`, `is_boolean`, `is_integer`, `is_float`, `is_numeric`, `is_string`, `is_object`, `is_class`, `is_null`,
`is_set`, `is_unset`, `get_type`, `get_class`

## Datum und Gebietsschema (eZTemplateLocaleOperator)

`l10n`, `locale`, `datetime`, `currentdate`, `maketime`, `makedate`, `gettime`

## URLs, Designs, Einstellungen, Request (die URL-Operatorklasse)

`ezurl`, `ezroot`, `ezdesign`, `ezimage`, `exturl`, `ezsys`, `ezini`, `ezini_hasvariable`, `ezhttp`,
`ezhttp_hasvariable`; für Multisite-Installationen außerdem `siteaccess_url`. `ezurl` sowie die Bild- und
Design-Operatoren geben ihr Ergebnis in Anführungszeichen aus, sofern sie nicht `'no'` erhalten.

## Übersetzung (eZi18nOperator)

`i18n`, `x18n`, `d18n`: `{'Text'|i18n( 'extension/myext' )}` mit dem Kontext Ihrer Erweiterung.

## Symbole (eZWordToImageOperator)

`icon`, `icon_info`, `class_icon`, `classgroup_icon`, `action_icon`, `mimetype_icon`, `flag_icon`, `wordtoimage`

## Zugriff (eZTemplateRoleOperator, ezjscore)

`has_role`, `has_role_id`, `has_any_role`, `has_role_by_user`, `has_role_by_user_id`, `has_policy`,
`has_policy_by_user`, `has_access_to_limitation`. Für „Darf der aktuelle Benutzer X tun?“ bevorzugen Sie die
Fetch-Funktion `fetch( 'user', 'has_access_to', ... )` ([eZUser und Zugriffsprüfungen](ezuser-und-zugriff.md)).

## Kodierung und der Packer (ezjscore)

`json_encode`, `node_encode`, `xml_encode`; `ezcss`, `ezcss_load`, `ezcss_require`, `ezcssfiles`, `ezscript`,
`ezscript_load`, `ezscript_require`, `ezscriptfiles` ([Theme-Ressourcen, der Packer und CSP](../teil-6-theme-entwickler/theme-assets-der-packer-und-csp.md)).

## Weitere Kernel-Operatoren

`fetch`, `fetch_alias` (Fetch-Funktionen); `attribute`, `dump` (einen Wert beim Debuggen untersuchen); `concat`,
`indent`; `si` (Einheiten); `crc32`, `md5`, `rot13`; `texttoimage`, `imagefile`; `expinfo` (Informationen zur
Installation und zu Erweiterungen); `ezpreference`; `ezmodule`, `module_params`; `topmenu`, `treemenu`,
`content_structure_tree`; `eztoc`, `autolink`, `simpletags`, `alphabet`, `month_overview`, `ezpackage`;
`installation_name`, `is_production_system`, `exp_environment`, `exp_environment_badge`; `csp_nonce` (die Nonce der
Content Security Policy, für das seltene Inline-Skript, das bleiben muss).

## Operatoren mitgelieferter Erweiterungen

Einige Operatoren, die Templates häufig verwenden, stammen aus Erweiterungen, die mit dem Kernel ausgeliefert werden,
und existieren nur, wenn diese aktiv sind: `ezpagedata`, `ezpagedata_set`, `ezpagedata_append`, `ezkeywordlist` und
`ezarchive` (ezwebin und ezdemo), `ezcreateclasslistgroups` (ezwt), `exp_config` (expui) sowie die oben genannten
ezjscore-Operatoren.

## Selbst auflisten

Die Liste unterscheidet sich je Installation, weil Erweiterungen Operatoren hinzufügen. Um Ihre zu sehen, rendern Sie
bei eingeschalteter Debug-Ausgabe ein Template mit einem unbekannten Operator: Die Fehlermeldung nennt den Operator.
Oder fragen Sie die **Übersicht der Erweiterungspunkte** (`/setup/radsurvey`), die die Operatoren auflistet, die jede
aktive Erweiterung registriert.

## Verwandte Seiten

- Zurück: [Cache-Tags, Cache-Blöcke und das Leeren von Caches](cache-tags-und-leeren.md)
- Weiter: [Eingebaute Template-Funktionen](referenz-der-template-funktionen.md)
- [Template-Operatoren](../teil-3-erweiterungsentwickler/template-operatoren.md)
- [Die Template-Operatoren, die Builder am häufigsten verwenden](../teil-2-builder/die-wichtigsten-template-operatoren-fuer-builder.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
