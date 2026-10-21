---
title: Template-Operator exp_demo_mode
audience: developer, admin
category: examples
summary: "Der Template-Operator exp_demo_mode: keine benannten Parameter, registriert von expDemoModeOperator."
keywords: Template-Operator, Operator, exp_demo_mode, template operator, operator, tpl, expDemoModeOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator exp_demo_mode

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `expDemoModeOperator` (`kernel/common/expdemomodeoperator.php`).

## Parameter

Keine.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|exp_demo_mode}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/parts/exp_demo_mode_banner.tpl` Zeile 6:

```tpl
{if exp_demo_mode()}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/expdemomodeoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
