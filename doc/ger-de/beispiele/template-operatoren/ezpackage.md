---
title: Template-Operator ezpackage
audience: developer, admin
category: examples
summary: "Der Template-Operator ezpackage: 2 Parameter, registriert von eZPackageOperator."
keywords: Template-Operator, Operator, ezpackage, template operator, operator, tpl, eZPackageOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator ezpackage

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZPackageOperator` (`kernel/common/ezpackageoperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class` | string | ja | `false` |
| `data` | string | nein | `false` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|ezpackage( 'class', 'data' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/package/install_header.tpl` Zeile 27:

```tpl
<img src={concat( $package|ezpackage( fileitempath, $thumbnail_list[0] ) )|ezroot} alt="{$thumbnail_list[0].name|wash}" />
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezpackageoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
