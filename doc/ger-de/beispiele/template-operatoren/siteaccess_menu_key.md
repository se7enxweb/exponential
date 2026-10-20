---
title: Template-Operator siteaccess_menu_key
audience: developer, admin
category: examples
summary: "Der Template-Operator siteaccess_menu_key: keine benannten Parameter, registriert von ezpSiteAccessMenuOperator."
keywords: Template-Operator, Operator, siteaccess_menu_key, template operator, operator, tpl, ezpSiteAccessMenuOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator siteaccess_menu_key

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `ezpSiteAccessMenuOperator` (`kernel/common/ezpsiteaccessmenuoperator.php`).
Dieselbe Klasse registriert auch: [siteaccess_menu](siteaccess_menu.md).

## Parameter

Keine.

Der Operator nimmt keine Eingabe: Er wird für sich aufgerufen, nicht nach `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{siteaccess_menu_key}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin4/templates/pagelayout.tpl` Zeile 60:

```tpl
{cache-block keys=array( $uri_cache_key, $user_hash, $current_user.contentobject_id, $admin_theme, $admin_left_size, $access_type, first_set( $module_result.navigation_part, $navigation_part.identifier ), $search_hash, siteaccess_url(), siteaccess_menu_key(), $env_badge.key, $main_wrapped, $a4_columns ) ignore_content_expiry}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezpsiteaccessmenuoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
