---
title: Erweiterungspunkte auf einen Blick
audience: developer
category: extension-development
order: 10
summary: Jede Möglichkeit, wie sich eine Erweiterung in Exponential einklinkt, auf einer Seite, gruppiert wie im RAD-Katalog des Kernels, mit dem Registrierungsmechanismus jedes Punkts und dem Kapitel, das ihn erklärt.
keywords: Erweiterungspunkte, RAD-Katalog, Registrierung, Verzeichnis, Handler, Datei, Design, Filter, extension points, RAD catalogue, registration, directory, handler, autoload, file, design, ini, override, registry, filter, Setup RAD
type: help
---
# Erweiterungspunkte auf einen Blick

Teil 8, Referenz 1. Der Kernel führt einen Katalog seiner Erweiterungspunkte (`kernel/setup/expradcatalogue.php`,
`expRADCatalogue::points()`), und die Seite **Setup > RAD** (`/setup/rad`) wird daraus aufgebaut. Der vollständige
Katalog mit der Codestelle, der registrierenden Einstellung, dem Vertrag und der Kernel-Datei jedes Punkts ist
[Erweiterungspunkte](../../../bc/6.0/rad-erweiterungspunkte.md). Diese Seite ist die Kurzübersicht.

## Wie registriert wird

| Mechanismus | Bedeutung |
|---|---|
| directory | eine Klasse in einem Verzeichnis, das der Kernel durchsucht, benannt durch eine Einstellung `RepositoryDirectories` oder `ExtensionDirectories` |
| handler | eine Klasse, die eine INI-Einstellung nennt und die über `eZExtension::getHandlerClass()` geladen wird |
| autoload | eine Klasse, die über das Autoload-Array erreicht und einmal in einer INI-Datei registriert wird |
| file | eine Datei an einem Ort, an dem der Kernel nach Namen sucht |
| design | ein Template, das über die Design-Kette gefunden wird |
| ini | nur Einstellungen |
| registry | ein Eintrag in einem Register, das `rad.ini` deklariert |
| filter | ein Listener eines Filterereignisses in `site.ini [Event] Listeners[]` |
| override | eine Kernel-Klasse, die durch eine eigene ersetzt wird: das letzte Mittel |

## Die Punkte nach Gruppen

| Gruppe | Erweiterungspunkte | Kapitel im Buch |
|---|---|---|
| Inhalte | Datentyp, Inhaltsklasse, benutzerdefiniertes XML-Tag, Ein- und Ausgabe-Handler für XML-Text, Verhalten der Informationssammlung, Regeln zum Leeren des View-Caches, Filter für asynchrones Veröffentlichen | [Datentypen](../teil-3-erweiterungsentwickler/datentypen.md), [Tutorial zum benutzerdefinierten Tag](../teil-3-erweiterungsentwickler/tutorials/von-hand-custom-tag.md) |
| Templates und Design | Template-Operator, Template-Fetch-Funktion, Attribut-Operator, Template-Funktion, Fetch-Alias, Design-Erweiterung, Satz von Template-Overrides, Symbol-Theme | [Template-Operatoren](../teil-3-erweiterungsentwickler/template-operatoren.md), [Fetch-Funktionen](../teil-3-erweiterungsentwickler/fetch-funktionen.md), [Template-Funktionen](../teil-3-erweiterungsentwickler/template-funktionen.md), [Teil 6](../teil-6-theme-entwickler/README.md) |
| Module und Views | Modul, Modul über bestehenden Tabellen, View für ein bestehendes Modul, Richtlinienfunktion und Einschränkung, Einschränkung von Inhaltsrichtlinien, REST-Provider, REST-Routenfilter, serverseitige Ajax-Funktion, in `rad.ini` deklariertes Register | [Module und Views](../teil-3-erweiterungsentwickler/module-und-views.md), [Richtlinienfunktionen](../teil-3-erweiterungsentwickler/richtlinienfunktionen-und-zugriff.md), [Ajax](../teil-3-erweiterungsentwickler/ajax-mit-ezjscore-serverfunktionen.md) |
| Workflow, Ereignisse und Aufträge | Workflow-Ereignistyp, Trigger, Benachrichtigungsereignistyp und -Handler, Cronjob-Skript und -Teil, ausgelassener Cronjob-Teil, Listener für Kernel-Ereignisse, Listener für Filterereignisse | [Workflow-Ereignistypen](../teil-3-erweiterungsentwickler/workflow-ereignistypen.md), [Cronjob-Teile](../teil-3-erweiterungsentwickler/cronjob-teile.md), [Ereignisse und Filter](../teil-3-erweiterungsentwickler/ereignisse-und-filter.md) |
| Speicher und Infrastruktur | Datenbank-Handler, Cluster-Datei-Handler, DFS-Backends, Binärdatei-Handler, Suchmaschine, Session-Handler, Mail-Transport, Handler für den statischen Cache, Bild-Handler und Bildvarianten, Redis-Caches, Panel der Debug-Leiste | [Durch Einstellungen benannte Handler](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) |
| Pakete und Shop | Paket-Handler, Zahlungs-Gateways, Mehrwertsteuer, Versand, Handler für Warenkorbinformationen und Wechselkurse | [Durch Einstellungen benannte Handler](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) |
| Benutzer, Zugriff und Sprache | URL-Alias-Filter, Filter für Mobilgeräte, REST-Filter, Handler für die Benutzeranmeldung, zusätzliche Erweiterungswurzeln, Filter für Erweiterungswurzeln, Website-Erweiterung, Einstellungen aus Code schreiben, Erweiterung für Siteaccess-Einstellungen, Übersetzung, Handler für den RSS-Import | [Übersetzungen](../teil-3-erweiterungsentwickler/uebersetzungen-in-einer-erweiterung.md), [Einstellungen](../teil-3-erweiterungsentwickler/einstellungen-in-einer-erweiterung.md) |

Der Katalog zählt 70 Erweiterungspunkte, 65 davon mit einem Werkzeug unter **Setup > RAD**.

## Was tatsächlich vorhanden ist

Der Katalog ist von Hand geschrieben. Was auf einer Installation wirklich registriert ist (die Einstellungen, Klassen
und Dateien jeder aktiven Erweiterung), listet die **Übersicht der Erweiterungspunkte** (`/setup/radsurvey`) auf, die
bei jedem Aufruf von der Festplatte gelesen wird; ihre erzeugte Referenz ist
[Die Erweiterungsoberfläche](../../../bc/6.0/rad-erweiterungsoberflaeche.md). Die Zustandsprüfung der RAD-Seite meldet
Einstellungen, die Klassen nennen, die sich nicht laden lassen, fehlende Verzeichnisse und ähnliche Fehler
([Rundgang durch die RAD-Werkzeuge](../teil-3-erweiterungsentwickler/tutorials/rundgang-durch-die-rad-werkzeuge.md)).

## Auswählen

Bevorzugen Sie den leichtesten Mechanismus, der die Aufgabe erfüllt, ungefähr in dieser Reihenfolge: ini, design,
file, directory, handler, filter, registry, autoload, override.
[Erweitern ohne Fork](../teil-5-erweitern-forken-zurueckgeben/erweitern-ohne-zu-forken.md) erklärt, warum.

## Verwandte Seiten

- Zurück: [Teil 8: Referenz der eingebauten Funktionen für Erweiterungsautoren](README.md)
- Weiter: [eZINI: Einstellungen lesen](ezini.md)
- [Erweiterungspunkte](../../../bc/6.0/rad-erweiterungspunkte.md)
- [RAD-Werkzeuge](../../../funktionen/6.0/rad-werkzeuge.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
