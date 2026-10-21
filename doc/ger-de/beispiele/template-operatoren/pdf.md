---
title: Template-Operator pdf
audience: developer, admin
category: examples
summary: "Der Template-Operator pdf: 1 Parameter, registriert von eZPDF."
keywords: Template-Operator, Operator, pdf, template operator, operator, tpl, eZPDF
type: generated
generated: exp:help:examples
---
# Der Template-Operator pdf

Registriert in `lib/ezpdf/classes/eztemplateautoload.php` von der Klasse `eZPDF` (`lib/ezpdf/classes/ezpdf.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `operation` | string | ja | `''` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|pdf( 'operation' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/base/override/templates/pdf/article/xml_paragraph.tpl` Zeile 1:

```tpl
{pdf(newline)}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/ezpdf/classes/eztemplateautoload.php`, `lib/ezpdf/classes/ezpdf.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
