---
title: Template-Operator chr
audience: developer, admin
category: examples
summary: "Der Template-Operator chr: keine benannten Parameter, registriert von eZTemplateStringOperator."
keywords: Template-Operator, Operator, chr, template operator, operator, tpl, eZTemplateStringOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator chr

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateStringOperator` (`lib/eztemplate/classes/eztemplatestringoperator.php`).
Dieselbe Klasse registriert auch: [upcase](upcase.md), [downcase](downcase.md), [count_words](count_words.md), [count_chars](count_chars.md), [trim](trim.md), [break](break.md), [wrap](wrap.md), [upfirst](upfirst.md), [upword](upword.md), [simplify](simplify.md), [trim](trim.md), [wash](wash.md), [ord](ord.md), [shorten](shorten.md), [pad](pad.md).

## Parameter

Keine.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|chr}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/standard/templates/setup/tests/settings_permission_error.tpl` Zeile 12:

```tpl
chmod {$:item.permission} {$:item.file} {chr(13)}{chr(10)} <br/>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatestringoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
