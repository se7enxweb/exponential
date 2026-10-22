---
title: Erweiterungspunkte aus PHP
audience: php-developer, developer
category: php-development
order: 305
summary: Wo sich PHP-Code in Exponential einklinkt, ohne den Kernel zu bearbeiten - die Registrierungsmechanismen (directory, handler, autoload, file, design, ini, registry, filter, override), die Erweiterungspunkte, die ein PHP-Entwickler am häufigsten nutzt, Setup > RAD und seine Übersicht und wie Sie den leichtesten Mechanismus wählen.
keywords: Erweiterungspunkte, Handler, Registrierung, Kernel-Override, Filter, Datentyp, Sitzungs-Handler, Mail-Transport, extension points, RAD, expRADCatalogue, handler, getHandlerClass, RepositoryDirectories, ExtensionDirectories, autoload, kernel override, registry, filter, RunnableSettings Implementation, setup/rad, radsurvey, datatype, REST provider, session handler, mail transport
type: help
---
# Erweiterungspunkte aus PHP

Diese Seite richtet sich an PHP-Entwickler, die Verhalten ändern oder hinzufügen möchten und den richtigen Ort zum
Einklinken suchen. Exponential wird auf einer Handvoll Arten erweitert, die sich wiederholen.
`kernel/setup/expradcatalogue.php` (`expRADCatalogue::points()`) listet jeden Erweiterungspunkt auf, und **Setup > RAD**
(`/setup/rad`) wird daraus gebildet: 70 Erweiterungspunkte, 65 davon mit einem Werkzeug. Die vollständige Liste mit
jeder INI-Zeile ist [Erweiterungspunkte](../../bc/6.0/rad-erweiterungspunkte.md).

## Inhalt

1. [Die Mechanismen](#die-mechanismen)
2. [Die Punkte, die PHP-Entwickler am häufigsten nutzen](#die-punkte-die-php-entwickler-am-häufigsten-nutzen)
3. [Den leichtesten Mechanismus wählen](#den-leichtesten-mechanismus-wählen)
4. [Durch Einstellungen benannte Handler](#durch-einstellungen-benannte-handler)
5. [Die RAD-Seiten und die Übersicht](#die-rad-seiten-und-die-übersicht)

## Die Mechanismen

| Mechanismus | Wie er gefunden wird | Beispiel |
|---|---|---|
| directory | eine Klasse in einem Verzeichnis, das der Kernel durchsucht, benannt durch eine Einstellung `...Directories` | Datentypen, Workflow-Ereignistypen |
| handler | eine durch eine Einstellung benannte Klasse, geladen über `eZExtension::getHandlerClass()` | Sitzungs-Handler, Cluster-Datei-Handler, Mail-Transport |
| autoload | eine Klasse, die über einen Autoload-Pfad einer Erweiterung erreicht wird | Template-Operatoren (`ExtensionAutoloadPath[]`) |
| file | eine Datei an einem Ort, an dem der Kernel nach Namen sucht | `function_definition.php`, `module.php` |
| design | ein Template in einem Design, gefunden über die Design-Kette | Override-Templates |
| ini | nur Einstellungen | die meisten Verhaltensschalter |
| registry | Einträge einer Registry, die `rad.ini` deklariert | Einstellungen der Debug-Leiste, Anbieter von Systemkarten |
| filter | ein Listener eines filter-Ereignisses in `site.ini [Event] Listeners[]` | `response/output`, `httpcache/store/headers` |
| override | eine über `var/autoload/ezp_override.php` ersetzte Kernel-Klasse | das letzte Mittel |

## Die Punkte, die PHP-Entwickler am häufigsten nutzen

| Sie möchten | Punkt | Seite |
|---|---|---|
| einen Feldtyp hinzufügen | Datentyp | [Einen Datentyp schreiben](einen-datentyp-schreiben.md) |
| einen Workflow-Schritt hinzufügen | Workflow-Ereignistyp | [Einen Workflow-Ereignistyp schreiben](einen-workflow-ereignistyp-schreiben.md) |
| Seiten hinzufügen | Modul und Views | [Schreiben Sie Ihre erste Modul-View](ihre-erste-modul-view-schreiben.md) |
| eine Kernel-View, einen Befehl oder einen Cronjob-Teil ändern | Unterklasse über `[RunnableSettings] Implementation[]` | [Modul-Views als Klassen](modul-views-als-klassen.md#eine-kernel-view-neu-implementieren) |
| Templates Daten geben | Fetch-Funktion | [Eine Fetch-Funktion schreiben](eine-fetch-funktion-schreiben.md) |
| Templates eine Hilfsfunktion geben | Template-Operator | [Einen Template-Operator in PHP schreiben](einen-template-operator-in-php-schreiben.md) |
| auf den Kernel reagieren | Listener für Kernel-Ereignisse | [Ereignisse, Listener und Filter](ereignisse-listener-und-filter.md) |
| einen Wert unterwegs ändern | Listener für filter-Ereignisse | [Ereignisse, Listener und Filter](ereignisse-listener-und-filter.md#ein-filter-listener) |
| JSON für Ihr eigenes JavaScript | serverseitige Ajax-Funktion | [ezjscore-JSON-Endpunkte in PHP](ezjscore-json-endpunkte-in-php.md) |
| neue REST-Routen | REST-Provider | [REST und entfernte Dienste aus PHP](rest-und-entfernte-dienste-aus-php.md) |
| eine Richtlinie mit Einschränkungen hinzufügen | Richtlinienfunktion und Einschränkung | [Richtlinienfunktionen und Einschränkungen](../ersteller/anwendungsentwickler/richtlinienfunktionen-und-einschraenkungen.md) |
| zeitlich geplante Arbeit | Cronjob-Skript und -Teil | [Einen Cronjob-Teil schreiben](einen-cronjob-teil-schreiben.md) |
| einen Befehl | Befehl einer Erweiterung | [Einen Konsolenbefehl schreiben](einen-konsolenbefehl-schreiben.md) |

## Den leichtesten Mechanismus wählen

Bevorzugen Sie in dieser Reihenfolge:

1. **Einstellungen** (`ini`), wenn eine Einstellung bereits tut, was Sie brauchen.
2. **Ein Template-Override** (`design`), wenn sich nur die Ausgabe ändert.
3. **Einen registrierten Punkt** (directory, handler, autoload, file, registry): Der Kernel ruft Ihre Klasse über ihren
   Namen auf, und mehrere Erweiterungen können nebeneinander bestehen.
4. **Einen Listener oder Filter**, wenn Sie auf das reagieren oder das anpassen, was der Kernel tut.
5. **Eine Unterklasse über `[RunnableSettings] Implementation[]`**, wenn sich eine Methode einer Kernel-View, eines
   Befehls oder eines Cronjob-Teils ändern muss.
6. **Einen Kernel-Override**, nur wenn nichts anderes funktioniert. Nichts registriert den Ersatz unter seinem Namen,
   zwei Erweiterungen, die dieselbe Klasse ersetzen, kollidieren still, und Sie müssen jedem Kernel-Update von Hand
   folgen ([Kernel-Overrides, das letzte Mittel](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/kernel-overrides-der-letzte-ausweg.md)).

## Durch Einstellungen benannte Handler

Viele Infrastrukturteile (Sitzung, Cluster-Dateien, Mail-Transport, Suchmaschine, Bild-Handler, statischer Cache) sind
durch eine Einstellung benannte Klassen, geladen mit `eZExtension::getHandlerClass()`. Ihre Klasse implementiert
dasselbe Interface oder erweitert die Klasse des Kernels, und die Einstellung nennt sie. Ein in einer Einstellung
benannter Handler muss existieren, bevor die Einstellung live geht; andernfalls schlägt jede Anfrage fehl, die ihn
braucht. Stellen Sie zuerst den Code bereit, danach die Einstellungen, und sichern Sie mit `class_exists()` ab, wo eine
fehlende Klasse keine Seiten brechen darf ([Durch Einstellungen benannte Handler](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md)).

## Die RAD-Seiten und die Übersicht

- `/setup/rad` zeigt jeden Erweiterungspunkt, seinen Mechanismus, die Einstellung, die ihn registriert, und ein
  Werkzeug, das ein Grundgerüst dafür schreibt (Datentyp, Template-Operator, Workflow-Ereignis, Modul,
  Einstellungserweiterung ...) ([RAD-Werkzeuge](../ersteller/anwendungsentwickler/rad-werkzeuge.md), [Die Funktion RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md)).
- `/setup/radsurvey` zählt, was Ihre Installation an jedem Punkt registriert, die Runnables und ihre
  Neuimplementierungen, und jeden Eintrag, der nicht funktionieren kann (eine Klasse, die nicht existiert, ein Ersatz,
  der die zu ersetzende Klasse nicht erweitert).

## Verwandte Seiten

- [Erweiterungspunkte auf einen Blick](../erweiterungsentwicklung/teil-8-referenz/erweiterungspunkte-auf-einen-blick.md)
- [Die RAD-Erweiterungsoberfläche](../../bc/6.0/rad-erweiterungsoberflaeche.md)
- [Erweitern ohne Fork](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/erweitern-ohne-zu-forken.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
