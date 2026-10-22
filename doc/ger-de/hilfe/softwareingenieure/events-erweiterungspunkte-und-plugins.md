---
title: Events, Erweiterungspunkte und Plugins
audience: software-engineer, developer
category: software-engineering
order: 180
summary: Wie Exponential um Verhalten ergänzt wird, ohne es zu forken - der prozessinterne Event-Bus ezpEvent mit notify und filter, in Einstellungen benannte Handler, Datentypen, Workflow-Event-Typen, Template-Operatoren, Fetch-Funktionen, Login- und Cluster-Handler, ersetzbare Views und Module und warum ein Override einer Kernel-Klasse das letzte Mittel ist.
keywords: Erweiterungspunkte, Plugins, ezpEvent, Events, Filter, Listener, Hooks, Handler, Einstellungen, RunnableSettings, Workflow, Datentyp, Operator, Kernel-Override, idempotent, Softwareingenieur, extension points, plugins, filters, listeners, hooks, handlers, settings, datatype, operator, kernel override
type: help
---
# Events, Erweiterungspunkte und Plugins

Diese Seite richtet sich an Ingenieure, die ändern müssen, was Exponential tut, ohne seinen Code zu ändern. Fast jeder
Teil des Kernels ist über eine Einstellung austauschbar, die eine Klasse nennt; der prozessinterne Event-Bus ergänzt
Hooks an festen Punkten einer Anfrage. Die vollständige Liste ist
[Erweiterungspunkte auf einen Blick](../erweiterungsentwicklung/teil-8-referenz/erweiterungspunkte-auf-einen-blick.md), und die
registrierten Punkte sind in [RAD-Erweiterungspunkte](../../bc/6.0/rad-erweiterungspunkte.md) beschrieben.

## Inhalt

1. [Die Reihenfolge der Bevorzugung](#die-reihenfolge-der-bevorzugung)
2. [Der Event-Bus: ezpEvent](#der-event-bus-ezpevent)
3. [In Einstellungen benannte Handler](#in-einstellungen-benannte-handler)
4. [Eine View oder ein Modul ersetzen](#eine-view-oder-ein-modul-ersetzen)
5. [Das letzte Mittel: ein Kernel-Override](#das-letzte-mittel-ein-kernel-override)
6. [Regeln für Listener und Plugins](#regeln-für-listener-und-plugins)

## Die Reihenfolge der Bevorzugung

1. **Eine Einstellung.** Viele Verhaltensweisen sind bereits Schalter oder Listen; prüfen Sie zuerst die mitgelieferten
   `settings/*.ini`.
2. **Ein Template-Override** für die Darstellung ([Die Template-Engine und die Design-Kette](template-engine-und-design-kette.md)).
3. **Ein registrierter Erweiterungspunkt**: ein Datentyp, Workflow-Event, Operator, eine Fetch-Funktion, ein Handler oder
   ein Event-Listener.
4. **Eine ersetzte View oder ein ersetztes Modul**, über `[RunnableSettings]` oder einen Modul-Override einer
   Erweiterung.
5. **Ein Override einer Kernel-Klasse**, nur wenn nichts von oben passt.

## Der Event-Bus: ezpEvent

`ezpEvent::getInstance()` (`kernel/private/classes/ezpevent.php`) bietet zwei Aufrufe:

| Aufruf | Verwendung |
|---|---|
| `notify( $name, $params )` | Listenern mitteilen, dass etwas geschehen ist |
| `filter( $name, $value )` | Listener einen Wert ändern und zurückgeben lassen |

Globale Listener werden in `site.ini [Event] Listeners[]=<event>@<callback>` konfiguriert. Zu den Events, die der Kernel
auslöst, gehören:

| Event | Wann |
|---|---|
| `request/preinput`, `request/input` | zu Beginn der Weiterleitungsschleife |
| `response/preoutput`, `response/output` | Ausgabefilter, bevor die Antwort gesendet wird |
| `content/cache`, `content/cache/all`, `content/class/cache` | View-Caches für Inhalte werden geleert |
| `image/removeAliases` | Bildvarianten werden entfernt |
| `content/edit/access`, `content/download/access`, `collaboration/item/access` | Zugriffsfilter, die ablehnen können |
| `runnable/<kind>/before`, `runnable/<kind>/after` | um klassenbasierte Views, Befehle und Cronjob-Teile |

Die Datei kennzeichnet `ezpEvent` als private API, die sich ändern kann; Erweiterungen verlassen sich trotzdem darauf,
daher werden Änderungen daran als kompatibilitätsrelevant behandelt
([Events und Filter](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ereignisse-und-filter.md)).

## In Einstellungen benannte Handler

| Erweiterungspunkt | Einstellung | Anleitung |
|---|---|---|
| Datentypen | `content.ini [DataTypeSettings]` | [Eigene Datentypen](../ersteller/anwendungsentwickler/eigene-datentypen.md) |
| Workflow-Event-Typen | `workflow.ini [EventSettings]` | [Einen Workflow-Event-Typ schreiben](../ersteller/anwendungsentwickler/einen-workflow-ereignistyp-schreiben.md) |
| Template-Operatoren und -Funktionen | `site.ini [TemplateSettings] ExtensionAutoloadPath[]` | [Template-Operatoren](../ersteller/anwendungsentwickler/template-operatoren-schreiben.md) |
| Fetch-Funktionen | die `function_definition.php` des Moduls | [Eine Fetch-Funktion schreiben](../ersteller/anwendungsentwickler/eine-eigene-fetch-funktion-schreiben.md) |
| Cronjob-Teile | `cronjob.ini` | [Einen Cronjob-Teil schreiben](../ersteller/anwendungsentwickler/einen-cronjob-teil-schreiben.md) |
| Benachrichtigungs-Handler | `notification.ini` | [Benachrichtigungen und E-Mail](../ersteller/anwendungsentwickler/benachrichtigungen-und-mail.md) |
| Handler für Inhaltseinschränkungen | siehe die Funktionsseite | [Handler für Inhaltseinschränkungen](../../funktionen/6.0/handler-fuer-inhaltseinschraenkungen.md) |
| Cluster-Datei-Handler | `file.ini [ClusteringSettings] FileHandler` | [Skalierung und Clustering](skalierung-und-clustering.md) |
| Jeder Handler, den eine Einstellung nennt | das Muster | [Handler, die Einstellungen benennen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) |

## Eine View oder ein Modul ersetzen

- Eine klassenbasierte View lässt sich über `site.ini [RunnableSettings] Implementation[]` ersetzen.
- Ein ganzes Kernel-Modul kann durch das gleichnamige Modul einer Erweiterung ersetzt werden
  ([Override von Modulen durch Erweiterungen](../../funktionen/6.0/modulueberschreibung-durch-extensions.md)).

## Das letzte Mittel: ein Kernel-Override

Ein Override einer Kernel-Klasse ersetzt eine Kernel-Klasse durch eine Kopie in einer Erweiterung. Jede Aktualisierung
des Kernels kann Ihre Kopie dann still brechen; sie braucht also einen Test und eine Notiz in Ihrer Checkliste für
Aktualisierungen ([Kernel-Overrides, das letzte Mittel](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/kernel-overrides-der-letzte-ausweg.md)).

## Regeln für Listener und Plugins

- **Registrierungen müssen idempotent sein.** Unter Velocity häuft sich ein Listener an, der in einem langlebigen Worker
  einmal je Anfrage angehängt wird ([Nebenläufigkeit, Statics und langlebige Prozesse](nebenlaeufigkeit-statics-und-langlebige-prozesse.md)).
- Listener-Namen sollten Zeichenketten `Class::method` sein, damit sie konfigurierbar sind.
- Eine neue Klasse, die eine mitgelieferte Einstellung nennt, sollte dort, wo der Kernel sie lädt, mit `class_exists()`
  abgesichert werden.
- Zugriffsentscheidungen laufen über die Zugriffsfilter, nie über eine Prüfung einer Benutzer-ID.

## Verwandte Seiten

- [Kernel, lib und Erweiterungen: die Schichtung](kernel-lib-und-erweiterungen.md)
- [Erweitern ohne zu forken](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/erweitern-ohne-zu-forken.md)
- [Katalog der Entwurfsmuster](../../standards/muster/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
