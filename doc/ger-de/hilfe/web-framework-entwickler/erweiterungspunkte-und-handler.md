---
title: "Erweiterungspunkte: Handler, INI-Registrierung und Overrides"
audience: web-framework-developer, developer
category: web-frameworks
order: 200
summary: Wo Framework-Entwickler Dienste, Tags, Listener und Plugins registrieren, registriert Exponential Erweiterungspunkte in INI-Einstellungen, Verzeichnissen und Designs - die neun Registrierungsmechanismen, eine Übersicht der meistgenutzten Erweiterungspunkte mit ihren Framework-Gegenstücken und Setup > RAD, das sie auflistet und Gerüste dafür erzeugt.
keywords: Erweiterungspunkte, Plugins, Hooks, Handler, Registrierung, INI, RAD, setup/rad, Datentyp, Template-Operator, Fetch-Funktion, Workflow-Ereignis, REST-Provider, Login-Handler, Session-Handler, Override, Bundle, Service Provider, extension points, plugins, hooks, registration, datatype, template operator, fetch function, workflow event, Web-Framework-Entwickler, web framework developer
type: help
---
# Erweiterungspunkte: Handler, INI-Registrierung und Overrides

Diese Seite richtet sich an Entwickler, die ein Framework erweitern, indem sie Dienste registrieren, mit Tags versehen,
Listener anmelden oder Plugins schreiben. Exponential wird auf eine Handvoll wiederkehrender Arten erweitert, die alle in
einem einzigen Katalog aufgeführt sind: `expRADCatalogue::points()` (`kernel/setup/expradcatalog.php`), angezeigt unter
**Setup > RAD** (`/setup/rad`) mit 70 Erweiterungspunkten, die meisten davon mit einem Assistenten, der den
Grundcode schreibt ([Erweiterungspunkte](../../bc/6.0/rad-erweiterungspunkte.md),
[RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md)).

## Inhalt

1. [Die Registrierungsmechanismen](#die-registrierungsmechanismen)
2. [Die Erweiterungspunkte, die Sie zuerst nutzen werden](#die-erweiterungspunkte-die-sie-zuerst-nutzen-werden)
3. [Gegenstücke in Frameworks](#gegenstücke-in-frameworks)
4. [Reihenfolge der Bevorzugung](#reihenfolge-der-bevorzugung)

## Die Registrierungsmechanismen

Aus [Wie Dinge registriert werden](../../bc/6.0/rad-erweiterungspunkte.md#wie-dinge-registriert-werden):

| Mechanismus | Funktionsweise | Nächstliegende Framework-Idee |
|---|---|---|
| directory | eine Klasse in einem Verzeichnis, das der Kernel durchsucht und das durch eine Einstellung `RepositoryDirectories` oder `ExtensionDirectories` benannt ist | automatische Erkennung per Konvention |
| handler | eine Klasse, die durch eine INI-Einstellung benannt und über `eZExtension::getHandlerClass()` erzeugt wird | eine konfigurierte Dienstimplementierung |
| autoload | eine Klasse, die über die Autoload-Tabelle der Erweiterung erreicht wird | automatisch verdrahtete Klassen (Autowiring) |
| file | eine Datei an einem Ort, an dem der Kernel nach Namen sucht | Konvention vor Konfiguration |
| design | ein Template in einem Design, gefunden über die Design-Kette | Override des Template-Pfads |
| ini | nur Einstellungen | Konfiguration |
| registry | Einträge einer Registry, die `rad.ini` deklariert | getaggte Dienste |
| filter | ein Listener eines Filter-Ereignisses in `site.ini [Event] Listeners[]` | Event Subscriber, der einen Wert zurückgibt |
| override | eine Kernel-Klasse, die über das Override-Autoload ersetzt wird | Klassenersetzung, Monkey Patching |

## Die Erweiterungspunkte, die Sie zuerst nutzen werden

| Sie möchten | Erweiterungspunkt | Registriert durch |
|---|---|---|
| Neue Adressen und Bildschirme | Modul und Views | `module.ini [ModuleSettings] ExtensionRepositories[]` |
| Eine Berechtigung dafür | Richtlinienfunktion und Einschränkung | `$FunctionList` in `module.php` |
| Template-Hilfsfunktionen | Template-Operator | `site.ini [TemplateSettings] ExtensionAutoloadPath[]` |
| Daten für Templates | Fetch-Funktion | `function_definition.php` neben `module.php` |
| Einen neuen Feldtyp | Datentyp | `content.ini [DataTypeSettings] ExtensionDirectories[]`, `AvailableDataTypes[]` |
| Einen Schritt in einem Geschäftsprozess | Workflow-Ereignistyp | `workflow.ini [EventSettings]` |
| Code bei einem Kernel-Ereignis | Kernel-Event-Listener | `site.ini [Event] Listeners[]` |
| Geplante Arbeit | Cronjob-Teil | `cronjob.ini` |
| Einen JSON-Endpunkt für Ihre Seiten | ezjscore-Serverfunktion | `ezjscore.ini [ezjscServer_<name>]` |
| REST-Routen | REST-Provider | `rest.ini` |
| Eine weitere Anmeldemethode | Login-Handler für Benutzer | `site.ini [UserSettings] LoginHandler[]` |
| Ihre Templates und Stylesheets | Design-Erweiterung | `design.ini [ExtensionSettings] DesignExtensions[]` |

Jede Zeile ist im Katalog mit ihrem Vertrag und ihrer Kernel-Datei dokumentiert, und die meisten haben eine eigene Seite
in den [Leitfäden für Anwendungsentwickler](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md).

## Gegenstücke in Frameworks

| Framework | Sie würden | In Exponential |
|---|---|---|
| Symfony | ein Bundle mit `services.yaml`, Tags und Compiler Passes schreiben | eine Erweiterung mit Einträgen in `settings/*.ini.append.php` schreiben |
| Laravel | ein Paket mit einem Service Provider schreiben | dasselbe: die Einstellungen der Erweiterung sind ihr „Provider“ |
| Django | eine App zu `INSTALLED_APPS` hinzufügen | die Erweiterung zu `ActiveExtensions[]` hinzufügen |
| Rails | eine Engine oder einen Railtie schreiben | eine Erweiterung |
| Spring | einen Starter mit Auto-Configuration hinzufügen | eine Erweiterung; INI-Einstellungen treten an die Stelle der Auto-Configuration |

## Reihenfolge der Bevorzugung

1. **Nur Einstellungen**, wo es eine Einstellung gibt.
2. **Ein registrierter Erweiterungspunkt**: ein Modul, ein Operator, ein Handler, ein Listener.
3. **Neuimplementierung eines Runnables** (`[RunnableSettings] Implementation[]`) für einen Kernel-Befehl, einen
   Cronjob-Teil oder eine View.
4. **Ein Override einer Kernel-Klasse**, das letzte Mittel: Nichts registriert es beim Namen, und zwei Erweiterungen,
   die dieselbe Klasse ersetzen, wissen nichts voneinander.

Bearbeiten Sie niemals Dateien des Kernels oder einer anderen Erweiterung direkt.

## Verwandte Seiten

- [Dependency Injection und Dienste](dependency-injection-und-dienste.md)
- [RAD-Erweiterungsoberfläche](../../bc/6.0/rad-erweiterungsoberflaeche.md)
- [Aufbau einer Erweiterung](../ersteller/anwendungsentwickler/aufbau-einer-erweiterung.md)
- [Erweiterungsentwicklung: das Buch](../erweiterungsentwicklung/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
