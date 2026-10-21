---
title: Operatoren im Überblick
audience: designer, developer
category: templates
order: 170
summary: Was Template-Operatoren sind, die beiden Aufrufstile, woher Operatoren kommen (die Template-Bibliothek, der Kernel, PHP-Funktionen in template.ini, Erweiterungen), die Familien, die Designer am häufigsten verwenden, und wie Sie herausfinden, ob es einen Operator auf einem Siteaccess gibt.
keywords: Operatoren, Template-Operatoren, Pipe, Operatorfamilien, Zeichenketten-Operatoren, Array-Operatoren, URL-Operatoren, i18n, fetch, Operatoren von Erweiterungen, unbekannter Operator, PHPOperatorList, operators, template operators, pipe, operator families, string operators, array operators, url operators, extension operators, unknown operator
type: help
---
# Operatoren im Überblick

Diese Seite ist für Designer, die das Gesamtbild der Template-Operatoren wollen: der Funktionen, die in Templates Werte
berechnen. Die [Operatorreferenz](operatorreferenz.md) listet jeden auf.

## Inhalt

1. [Zwei Aufrufstile](#zwei-aufrufstile)
2. [Woher Operatoren kommen](#woher-operatoren-kommen)
3. [Die Familien](#die-familien)
4. [Gibt es diesen Operator hier?](#gibt-es-diesen-operator-hier)

## Zwei Aufrufstile

```tpl
{$title|upcase}                 {* pipe: input on the left *}
{upcase( $title )}              {* call *}
{$text|shorten( 100, '...' )}   {* pipe with parameters *}
{$items|count|gt( 0 )}          {* chained *}
```

Manche Operatoren nehmen keine Eingabe und werden nur aufgerufen: `fetch( ... )`, `array( ... )`, `hash( ... )`, `true()`,
`currentdate()`, `ezini( ... )`.

## Woher Operatoren kommen

| Quelle | Registriert in | Beispiele |
|---|---|---|
| Die Template-Bibliothek | `lib/eztemplate/classes/eztemplateautoload.php` | `array`, `hash`, `count`, `eq`, `cond`, `wash`, `shorten`, `l10n`, `fetch`, `sprintf` |
| Der Kernel | `kernel/common/eztemplateautoload.php` | `ezurl`, `ezroot`, `ezdesign`, `ezimage`, `ezini`, `ezhttp`, `i18n`, `csp_nonce`, `module_params`, `topmenu` |
| PHP-Funktionen per Einstellung | `template.ini [PHP] PHPOperatorList[]` | `urlencode`, `urldecode`, `rawurlencode`, `rawurldecode` |
| Erweiterungen | `extension/<ext>/autoloads/eztemplateautoload.php`, wenn die Erweiterung aktiv und in `site.ini [TemplateSettings] ExtensionAutoloadPath[]` aufgeführt ist | `ezcss_load`, `json_encode` von ezjscore; `ezpagedata` von ezwebin; `expl_*` der Layouts |

## Die Familien

| Familie | Seite |
|---|---|
| Zeichenketten: `upcase`, `shorten`, `trim`, `wash`, `concat`, PHP-Zeichenkettenfunktionen | [Zeichenketten-Operatoren](zeichenketten-operatoren.md), [PHP-Zeichenkettenfunktionen als Operatoren](php-zeichenkettenfunktionen-als-operatoren.md) |
| Arrays und Hashes: `array`, `hash`, `append`, `merge`, `contains`, `implode`, `explode`, `extract` | [Array-Operatoren](array-operatoren.md) |
| Logik: `eq`, `ne`, `gt`, `and`, `or`, `not`, `is_set` | [Logik- und Vergleichsoperatoren](logik-und-vergleichsoperatoren.md) |
| Mathematik: `sum`, `sub`, `mul`, `div`, `mod`, `round`, `count` | [Mathematische Operatoren](mathematische-operatoren.md) |
| Auswahl: `cond`, `first_set`, `choose` | [cond und first_set](cond-und-first-set.md) |
| Maskierung: `wash` | [Wash und Maskierung](wash-und-maskierung.md) |
| URLs und Design-Dateien: `ezurl`, `ezroot`, `ezdesign`, `ezimage` | [URL-Operatoren](url-operatoren.md) |
| Übersetzung und Locale: `i18n`, `l10n`, `datetime`, `currentdate` | [Der Operator i18n](der-i18n-operator.md), [Datum, Zahlen und l10n](datum-zahlen-und-l10n.md) |
| Einstellungen und Anfrage: `ezini`, `ezhttp`, `ezpreference` | [Der Operator ezini](der-ezini-operator.md), [Der Operator ezhttp](der-ezhttp-operator.md) |
| Daten: `fetch` | [Fetch-Grundlagen](fetch-grundlagen.md) |
| Assets: `ezcss_load`, `ezscript_load`, `json_encode` | [Template-Operatoren von ezjscore](ezjscore-template-operatoren.md) |

## Gibt es diesen Operator hier?

Einen Operator aus einer Erweiterung gibt es nur auf Siteaccesses, auf denen die Erweiterung aktiv ist. Ein unbekannter
Operator wird in der Debug-Ausgabe als „Operator '<name>' is not registered“ gemeldet, und das Template gibt dafür nichts aus.
Die erzeugten Beispielseiten der Hilfe listen Operatoren mit Beispielen auf, wo vorhanden. Wenn Sie von einem Operator einer
Erweiterung abhängen, sagen Sie es im README Ihres Themes.

## Verwandte Seiten

- [Operatorreferenz](operatorreferenz.md)
- [TPL-Syntax: Grundlagen](tpl-syntax-grundlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
