---
title: Template-Operator urlencode
audience: developer, admin
category: examples
summary: "Der Template-Operator urlencode: keine benannten Parameter, registriert von eZTemplatePHPOperator."
keywords: Template-Operator, Operator, urlencode, template operator, operator, tpl, eZTemplatePHPOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator urlencode

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZTemplatePHPOperator` (`lib/eztemplate/classes/eztemplatephpoperator.php`).
Er ruft die PHP-Funktion `urlencode()` auf, aufgeführt in `settings/template.ini` `[PHP] PHPOperatorList[]`.
Dieselbe Klasse registriert auch: [urldecode](urldecode.md), [rawurlencode](rawurlencode.md), [rawurldecode](rawurldecode.md).

## Parameter

Die Klasse beschreibt ihre Parameter nicht (kein `namedParameterList()`); was sie liest, steht in ihrem Quelltext.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|urlencode}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/content/search.tpl` Zeile 48:

```tpl
{let adv_url=concat( '/content/advancedsearch/', $search_text|count|gt( 0 )|choose('', concat( '?SearchText=', $search_text|urlencode ) ) )|ezurl}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatephpoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
