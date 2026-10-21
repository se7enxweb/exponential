---
title: Template-Funktion debug-log
audience: developer, admin
category: examples
summary: Die Template-Funktion debug-log, registriert von eZTemplateDebugFunction.
keywords: Template-Funktion, debug-log, template function, tpl, eZTemplateDebugFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion debug-log

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateDebugFunction` (`lib/eztemplate/classes/eztemplatedebugfunction.php`).
Dieselbe Klasse verarbeitet auch: [debug-timing-point](debug-timing-point.md), [debug-accumulator](debug-accumulator.md), [debug-trace](debug-trace.md).

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin4/templates/parts/a4_mega_menu_check.tpl` Zeile 20:

```tpl
{debug-log msg=concat( 'menu.ini [TopAdminMenu] MegaMenuTabs[]=', $mmc_tab, ': no [Topmenu_', $mmc_tab, '] MegaMenuTemplate and NavigationPartIdentifier, ignored' )}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatedebugfunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
