---
title: Template-Operator expinfo
audience: developer, admin
category: examples
summary: "Der Template-Operator expinfo: keine benannten Parameter, registriert von eZTemplateExpInfoOperator."
keywords: Template-Operator, Operator, expinfo, template operator, operator, tpl, eZTemplateExpInfoOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator expinfo

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateExpInfoOperator` (`lib/eztemplate/classes/eztemplateexpinfooperator.php`).

## Parameter

Keine.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|expinfo}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/setup/setupmenu_figure.tpl` Zeile 21:

```tpl
{* expinfo( 'active' ): the active extensions that exist, ActiveExtensions[] and ActiveAccessExtensions[] together *}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateexpinfooperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
