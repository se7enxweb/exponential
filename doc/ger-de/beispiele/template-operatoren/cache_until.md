---
title: Template-Operator cache_until
audience: developer, admin
category: examples
summary: "Der Template-Operator cache_until: 1 Parameter, registriert von expCacheUntilOperator."
keywords: Template-Operator, Operator, cache_until, template operator, operator, tpl, expCacheUntilOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator cache_until

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `expCacheUntilOperator` (`kernel/common/expcacheuntiloperator.php`).
Dieselbe Klasse registriert auch: [seconds_until](seconds_until.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `until` | mixed | nein | `null` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|cache_until( $until )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/expcacheuntiloperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
