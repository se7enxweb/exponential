---
title: "Dependency Injection und Dienste: was es gibt"
audience: web-framework-developer, developer
category: web-frameworks
order: 190
summary: Eine ehrliche Antwort auf die Frage nach dem Service-Container – der Legacy-Kernel hat keinen eigenen; Klassen werden über eine erzeugte Klassenzuordnung, Singletons und in INI-Einstellungen benannte Handler-Klassen gefunden; wie Sie trotzdem testbare Dienste schreiben, wie austauschbare Implementierungen funktionieren und was sich ändert, wenn der Kernel über die Legacy Bridge innerhalb von Symfony läuft.
keywords: Dependency Injection, dependency injection, DI, Service-Container, service container, IoC, Autowiring, autowiring, Service Provider, service provider, Dienste, services, Singleton, singleton, instance, Handler, handler, eZExtension::getHandlerClass, ezpExtensionOptions, Implementation, Autoload, autoload, Testbarkeit, testability, Symfony-Container, Symfony container, Laravel container, Spring, Web-Framework-Entwickler, web framework developer
type: help
---
# Dependency Injection und Dienste: was es gibt

Diese Seite richtet sich an Entwickler, die einen Service-Container erwarten: die DI-Komponente von Symfony, den
Container und die Service Provider von Laravel, den Application Context von Spring, den Service Manager von Laminas.
**Der Kernel von Exponential 6 hat keinen allgemeinen Dependency-Injection-Container.** Er ist entstanden, bevor
Container in PHP üblich waren. Was er stattdessen bietet, ist unten beschrieben, zusammen mit einem Weg, Code zu
schreiben, der genauso testbar ist wie über einen Container verdrahteter Code.

## Inhalt

1. [Was der Kernel stattdessen verwendet](#was-der-kernel-stattdessen-verwendet)
2. [In Einstellungen benannte Handler-Klassen](#in-einstellungen-benannte-handler-klassen)
3. [Dienste in einer Erweiterung schreiben](#dienste-in-einer-erweiterung-schreiben)
4. [Eine Implementierung ersetzen](#eine-implementierung-ersetzen)
5. [Innerhalb von Symfony](#innerhalb-von-symfony)
6. [Was Sie Ihrem Team sagen sollten](#was-sie-ihrem-team-sagen-sollten)

## Was der Kernel stattdessen verwendet

| Container-Funktion | Was der Kernel tut |
|---|---|
| Autoloading von Diensten | eine erzeugte Klassenzuordnung: `autoload/ezp_kernel.php` für den Kernel, `var/autoload/ezp_extension.php` für Erweiterungen (`php bin/php/ezpgenerateautoloads.php -e`), dazu der Autoloader von Composer |
| Gemeinsam genutzte Dienste | statische Methoden `instance()`: `eZDB::instance()`, `eZINI::instance( 'site.ini' )`, `eZHTTPTool::instance()`, `ezpEvent::getInstance()`, `eZTemplate::factory()` |
| Konfigurierbare Implementierungen | in INI-Einstellungen benannte Handler-Klassen, erzeugt von `eZExtension::getHandlerClass()` |
| Einen Dienst dekorieren oder ersetzen | `site.ini [RunnableSettings] Implementation[]` für Befehle, Cronjob-Teile und Views; ein Kernel-Klassen-Override als letztes Mittel |
| Getaggte Dienste, Collector | Listen in INI-Einstellungen (`Listeners[]`, `AvailableDataTypes[]`, `RuleProviders[]` ...) |
| Konfigurationsparameter | Werte aus `eZINI` ([Konfiguration](konfiguration-ini-dateien.md)) |

## In Einstellungen benannte Handler-Klassen

Viele Subsysteme des Kernels fragen eine INI-Einstellung, welche Klasse sie verwenden sollen, und
`eZExtension::getHandlerClass()` erzeugt sie. Zum Beispiel der statische Cache:

```php
$options = new ezpExtensionOptions( array( 'iniFile'     => 'site.ini',
                                           'iniSection'  => 'ContentSettings',
                                           'iniVariable' => 'StaticCacheHandler' ) );
$handler = eZExtension::getHandlerClass( $options );
```

Das ist Service Location über Konfiguration: Das Austauschen der Implementierung ist eine Änderung der Einstellungen in
Ihrer Erweiterung, keine Codeänderung im Kernel. Derselbe Mechanismus steckt hinter dem Sitzungs-Handler
(`site.ini [Session] Handler`), dem Mail-Transport (`site.ini [MailSettings] TransportAlias[]`), dem Cluster-Datei-Handler
(`file.ini [ClusteringSettings] FileHandler`) und der Suchmaschine (`site.ini [SearchSettings] SearchEngine`);
Login-Handler werden in `site.ini [UserSettings] LoginHandler[]` benannt und über das Verzeichnis gefunden.
**Setup > RAD** listet jeden dieser Erweiterungspunkte mit seiner Einstellung auf
([Erweiterungspunkte](erweiterungspunkte-und-handler.md)).

## Dienste in einer Erweiterung schreiben

Sie können Code im Container-Stil auch ohne Container schreiben:

1. Legen Sie die Arbeit in einfache Klassen mit Konstruktorparametern (`expDemoArchiveService`), nicht in Views.
2. Geben Sie jeder Klasse für den Produktivbetrieb eine kleine statische Factory, die sie aus den Einstellungen baut:
   `expDemoArchiveService::fromSettings()` liest `eZINI` und übergibt die Werte an den Konstruktor.
3. Views, Befehle, Cronjob-Teile, Fetch-Funktionen und Queue-Handler rufen die Factory auf; Tests rufen den Konstruktor
   mit Attrappen auf ([Testen](testen.md)).
4. Halten Sie keinen Zustand pro Request in statischen Eigenschaften: Unter Velocity bedient ein Prozess viele Requests
   ([Server und Deployment](server-und-deployment.md)).

Die eigenen Dienste des Kernels (`Exponential\Service\Trash`, `Exponential\Service\SessionGarbageCollector` in
`kernel/private/classes/services/`) folgen als statische Helfer der Regel „eine Klasse für View, Befehl und Cronjob-Teil“
([Dienste](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md#dienste-eine-implementierung-für-die-view-den-befehl-und-den-cronjob-teil)).

Composer-Bibliotheken funktionieren in einer Erweiterung, weil `autoload.php` den Autoloader von Composer lädt. Bringt
eine Bibliothek, die Sie verwenden, einen eigenen Container mit, behalten Sie ihn innerhalb Ihrer Erweiterung; der
Kernel sieht ihn nicht.

## Eine Implementierung ersetzen

| Sie möchten | Verwenden Sie |
|---|---|
| Ein Kernel-Subsystem austauschen (Sitzungen, Mail, Dateispeicher, Suche, Login) | seine Handler-Einstellung |
| Einen Kernel-Befehl, einen Cronjob-Teil oder eine View ändern | `Implementation[<class>]=<your subclass>` in `[RunnableSettings]` |
| Eine beliebige andere Kernel-Klasse ändern | ein Kernel-Klassen-Override über den Override-Autoload (`EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` und `php bin/php/ezpgenerateautoloads.php -o`): der schwerste Mechanismus, ein letztes Mittel |

## Innerhalb von Symfony

Läuft der Kernel über die Legacy Bridge innerhalb einer Symfony-Plattforminstallation, kann der Web-Kernel den
Service-Container von Symfony erhalten (`ezpKernelWeb::hasServiceContainer()`, `getServiceContainer()`), und der
Symfony-Code um ihn herum verwendet Dependency Injection wie gewohnt. Das ist eine Eigenschaft des Bridge-Aufbaus, nicht
einer eigenständigen Exponential-6-Installation ([Symfony Legacy Bridge](symfony-und-die-legacy-bridge.md)).

## Was Sie Ihrem Team sagen sollten

- Es gibt keinen Container, in dem man Dienste registriert; verdrahten Sie Ihre Klassen mit Factories und Einstellungen.
- Suchen Sie nach einer vorhandenen Handler-Einstellung, bevor Sie eine Kernel-Klasse ableiten.
- Bearbeiten Sie nie `kernel/` oder `lib/`; ein Update würde Ihre Änderung ersetzen.

## Verwandte Seiten

- [Erweiterungspunkte: Handler, INI-Registrierung und Overrides](erweiterungspunkte-und-handler.md)
- [Muster: INI-Plugin-Punkt](../../standards/muster/ini-plugin-punkt.md)
- [Muster: Singleton](../../standards/muster/singleton.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
