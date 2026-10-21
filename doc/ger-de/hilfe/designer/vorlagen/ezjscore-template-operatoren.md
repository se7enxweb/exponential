---
title: Template-Operatoren von ezjscore
audience: designer, developer
category: templates
order: 310
summary: Die Template-Operatoren, die die Erweiterung ezjscore hinzufügt - die Packer-Operatoren für Stylesheets und Skripte, json_encode, xml_encode und node_encode, um Skripten Daten zu übergeben, und has_access_to_limitation.
keywords: ezjscore-Operatoren, json_encode, xml_encode, node_encode, ezcss_load, ezscript_load, has_access_to_limitation, ezjscore, Daten für JavaScript, ezjscore operators, data for javascript
type: help
---
# Template-Operatoren von ezjscore

Diese Seite ist für Designer, die die Operatoren der Erweiterung ezjscore verwenden, der JavaScript- und CSS-Grundlage von
Exponential. Sie sind in `extension/ezjscore/autoloads/eztemplateautoload.php` registriert.

## Inhalt

1. [Packer-Operatoren](#packer-operatoren)
2. [Kodieren für Skripte](#kodieren-für-skripte)
3. [has_access_to_limitation](#has_access_to_limitation)

## Packer-Operatoren

`ezscript`, `ezscript_require`, `ezscript_load`, `ezscriptfiles`, `ezcss`, `ezcss_require`, `ezcss_load`, `ezcssfiles`:
siehe [Die Operatoren ezcss und ezscript](../theme-design/die-operatoren-ezcss-und-ezscript.md) und
[Stylesheets laden](../theme-design/stylesheets-laden.md).

## Kodieren für Skripte

| Operator | Gibt zurück |
|---|---|
| `json_encode` | den Wert als JSON |
| `xml_encode` | den Wert als XML |
| `node_encode` | einen Knoten als einfache Struktur (für Skripte, die Knotendaten brauchen) |

Der CSP-sichere Weg, einem Skript Daten zu geben, ist ein JSON-Block:

```tpl
<script type="application/json" id="mytheme-gallery">{json_encode( hash(
    'title', $node.name,
    'count', $node.children_count ) )}</script>
```

Das Design media übergibt seine Texte zur Einwilligung für Videos auf diese Weise (`pagelayout/head/script.tpl`). Ein Block
vom Typ `application/json` ist Daten, kein Code, also erlaubt ihn auch eine strikte Content Security Policy.

## has_access_to_limitation

Sagt, ob der aktuelle Benutzer eine Richtlinie mit einer bestimmten Einschränkung hat; wird von Administrationsoberflächen
auf Basis von ezjscore verwendet. Für Designs sind die Fetch-Funktionen für Benutzer einfacher
([Rollen- und Richtlinienoperatoren](rollen-und-richtlinien-operatoren.md)).

## Verwandte Seiten

- [Kein Inline-JavaScript](../theme-design/kein-inline-javascript.md)
- [ezjscore](../../../funktionen/6.0/erweiterungen/ezjscore.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
