---
title: Template-Operator strip_tags
audience: developer, admin
category: examples
summary: "Der Template-Operator strip_tags: keine benannten Parameter, registriert von eZTemplatePHPOperator."
keywords: Template-Operator, Operator, strip_tags, template operator, operator, tpl, eZTemplatePHPOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator strip_tags

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZTemplatePHPOperator` (`lib/eztemplate/classes/eztemplatephpoperator.php`).
Dieselbe Klasse registriert auch: [urlencode](urlencode.md), [urldecode](urldecode.md), [rawurlencode](rawurlencode.md), [rawurldecode](rawurldecode.md), [getenv](getenv.md), [serialize](serialize.md), [unserialize](unserialize.md), [nl2br](nl2br.md).

## Parameter

Die Klasse beschreibt ihre Parameter nicht (kein `namedParameterList()`); was sie liest, steht in ihrem Quelltext.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|strip_tags}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/ezinfo/about.tpl` Zeile 137:

```tpl
<li>{$software|strip_tags|wash}</li>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatephpoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
