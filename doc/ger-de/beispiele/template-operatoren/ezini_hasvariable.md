---
title: Template-Operator ezini_hasvariable
audience: developer, admin
category: examples
summary: "Der Template-Operator ezini_hasvariable: 2 Parameter, registriert von eZURLOperator."
keywords: Template-Operator, Operator, ezini_hasvariable, template operator, operator, tpl, eZURLOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator ezini_hasvariable

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZURLOperator` (`kernel/common/ezurloperator.php`).
Dieselbe Klasse registriert auch: [ezurl](ezurl.md), [ezroot](ezroot.md), [ezdesign](ezdesign.md), [ezimage](ezimage.md), [exturl](exturl.md), [ezsys](ezsys.md), [ezhttp](ezhttp.md), [ezhttp_hasvariable](ezhttp_hasvariable.md), [ezini](ezini.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `quote_val` | string | nein | `'double'` |
| `server_url` | string | nein | `'relative'` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|ezini_hasvariable( 'double', 'relative' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/children_detailed.tpl` Zeile 54:

```tpl
subitemsServer: {if ezini_hasvariable( 'ezjscServer_expsubitems', 'Class', 'ezjscore.ini' )}true{else}false{/if},
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezurloperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
