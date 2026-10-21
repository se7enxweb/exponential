---
title: Template-Operator siteaccess_menu
audience: developer, admin
category: examples
summary: "Der Template-Operator siteaccess_menu: 1 Parameter, registriert von ezpSiteAccessMenuOperator."
keywords: Template-Operator, Operator, siteaccess_menu, template operator, operator, tpl, ezpSiteAccessMenuOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator siteaccess_menu

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `ezpSiteAccessMenuOperator` (`kernel/common/ezpsiteaccessmenuoperator.php`).
Dieselbe Klasse registriert auch: [siteaccess_menu_key](siteaccess_menu_key.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `user` | object | nein | `null` |

Der Operator nimmt keine Eingabe: Er wird für sich aufgerufen, nicht nach `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{siteaccess_menu( $user )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin4/templates/parts/a4_header_site.tpl` Zeile 19:

```tpl
{if $current_user.is_logged_in}{set $a4_site_menu = siteaccess_menu( $current_user )}{/if}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezpsiteaccessmenuoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
