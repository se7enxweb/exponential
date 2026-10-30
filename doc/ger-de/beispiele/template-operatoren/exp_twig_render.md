---
title: Template-Operator exp_twig_render
audience: developer, admin
category: examples
summary: "Der Template-Operator exp_twig_render: 1 Parameter, registriert von expTwigOperator."
keywords: Template-Operator, Operator, exp_twig_render, template operator, operator, tpl, expTwigOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator exp_twig_render

Registriert in `lib/twig/classes/eztemplateautoload.php` von der Klasse `expTwigOperator` (`lib/twig/classes/exptwigoperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `file` | string | ja | `''` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|exp_twig_render( 'file' )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/twig/classes/eztemplateautoload.php`, `lib/twig/classes/exptwigoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
