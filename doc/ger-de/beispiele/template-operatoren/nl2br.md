---
title: Template-Operator nl2br
audience: developer, admin
category: examples
summary: "Der Template-Operator nl2br: keine benannten Parameter, registriert von eZTemplateNl2BrOperator."
keywords: Template-Operator, Operator, nl2br, template operator, operator, tpl, eZTemplateNl2BrOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator nl2br

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateNl2BrOperator` (`lib/eztemplate/classes/eztemplatenl2broperator.php`).

## Parameter

Keine.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|nl2br}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/package/create/wizard_review.tpl` Zeile 22:

```tpl
<dd class="a4-package-review-text">{$rv.description|wash|nl2br}</dd>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatenl2broperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
