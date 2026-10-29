---
title: "Begriffswörterbuch: Framework-Begriffe in Exponential"
audience: web-framework-developer, developer
category: web-frameworks
order: 20
summary: Eine Nachschlagetabelle von den Begriffen aus Symfony, Laravel, Django, Rails, Express und Spring zum Begriff in Exponential, zur Klasse oder Einstellung, die ihn umsetzt, und zur Seite, die ihn erklärt – einschließlich der Konzepte, die kein direktes Gegenstück haben.
keywords: Wörterbuch, Begriffswörterbuch, dictionary, Konzeptkarte, concept map, Terminologie, Übersetzungstabelle, Controller, Route, Middleware, ORM, Migration, Service-Container, Ereignis, event, Warteschlange, queue, Cache, Template, Twig, Blade, Eloquent, Doctrine, Web-Framework-Entwickler, web framework developer
type: help
---
# Begriffswörterbuch: Framework-Begriffe in Exponential

Diese Seite richtet sich an Entwickler, die in den Begriffen eines anderen Frameworks denken und schnell den passenden
Begriff in Exponential suchen. Jede Zeile nennt den Begriff, was Exponential stattdessen verwendet, die Klasse oder
Einstellung, in der er zu finden ist, und die Seite, die ihn erklärt. Wo Exponential kein direktes Gegenstück hat, sagt
die Zeile das.

## Inhalt

1. [Request und Routing](#request-und-routing)
2. [Views und Templates](#views-und-templates)
3. [Daten und Persistenz](#daten-und-persistenz)
4. [Konfiguration und Verdrahtung](#konfiguration-und-verdrahtung)
5. [Sicherheit](#sicherheit)
6. [Hintergrundarbeit, APIs und Werkzeuge](#hintergrundarbeit-apis-und-werkzeuge)
7. [Kein direktes Gegenstück](#kein-direktes-gegenstück)

## Request und Routing

| Framework-Begriff | Begriff in Exponential | Wo | Lesen |
|---|---|---|---|
| Front Controller | `index.php` mit `ezpKernel` und `ezpKernelWeb` | `kernel/private/classes/ezpkernelweb.php` | [Der Request-Lebenszyklus](der-request-lebenszyklus.md) |
| Host- oder Mandantenauflösung, eine App pro Domain | Siteaccess-Erkennung | `site.ini [SiteAccessSettings] MatchOrder` | [Siteaccesses](siteaccesses-hosts-und-mandanten.md) |
| Route, URL-Muster | Modul und View (`/<module>/<view>/...`) | `extension/<ext>/modules/<module>/module.php` | [Routing](routing-mit-modulen-und-views.md) |
| Routenparameter | geordnete `params` und ungeordnete `(name)/value`-Parameter | `$ViewList[...]['params']`, `unordered_params` | [Routing](routing-mit-modulen-und-views.md#parameter) |
| Slug, Permalink | URL-Alias | `eZURLAliasML` | [Sprechende URLs](url-aliase-und-platzhalter.md) |
| Weiterleitungsroute, Rewrite | URL-Platzhalter | `eZURLWildcard` | [Sprechende URLs](url-aliase-und-platzhalter.md#url-platzhalter) |
| Controller, Action | Modul-View (Skript oder Klasse `Exponential\Runnable\ModuleView`) | `kernel/private/classes/runnable/moduleview.php` | [Controller](controller-als-modul-views.md) |
| Response-Objekt | das Array `$Result` oder Ausgabe plus `eZExecution::cleanExit()` | `eZModule` | [Responses](responses-weiterleitungen-und-fehler.md) |
| Middleware, Before-Filter | Request-Regeln; Listener für `request/input` | `settings/requestrules.ini`, `site.ini [Event]` | [Middleware](middleware-und-request-regeln.md) |
| After-Filter, Response-Middleware | Filter-Listener für `response/output` | `site.ini [Event] Listeners[]` | [Ereignisse und Listener](ereignisse-und-listener.md) |

## Views und Templates

| Framework-Begriff | Begriff in Exponential | Wo | Lesen |
|---|---|---|---|
| Twig, Blade, Jinja, ERB | die Template-Sprache (`.tpl`) | `lib/eztemplate/` | [Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md) |
| Basislayout, `extends` | das Pagelayout (`pagelayout.tpl`) um `$module_result` | Design des Siteaccess | [Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md#layouts) |
| Template-Vererbung, Theme-Override | Designkette und `override.ini` | `site.ini [DesignSettings]`, `override.ini` | [Template-Auflösung](template-aufloesung-designs-und-overrides.md) |
| View-Helper, Filter, Twig-Erweiterung | Template-Operator | `eztemplateautoload.php` | [Fetch-Funktionen und Operatoren](fetch-funktionen-und-template-daten.md) |
| View Composer, View Model | Fetch-Funktion (`fetch( 'module', 'function', hash() )`) | `function_definition.php` | [Fetch-Funktionen und Operatoren](fetch-funktionen-und-template-daten.md) |
| Partial, Include | `{include uri='design:...'}` | Template-Sprache | [Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md) |
| Fragment-Cache | `{cache-block}` | Template-Sprache | [Caching](cache-ebenen.md) |
| Übersetzungsfunktion | der Operator `i18n`, `ezpI18n::tr()` | `kernel/common/ezpi18n.php` | [Übersetzungen](i18n-l10n-und-rtl.md) |

## Daten und Persistenz

| Framework-Begriff | Begriff in Exponential | Wo | Lesen |
|---|---|---|---|
| Model, Entität, Active Record | Unterklasse von `eZPersistentObject` | `kernel/classes/ezpersistentobject.php` | [ORM](orm-und-ezpersistentobject.md) |
| Inhaltstyp, CMS-Model | Inhaltsklasse | `eZContentClass` | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md) |
| Datensatz eines Inhaltstyps | Inhaltsobjekt, seine Versionen und seine Knoten | `eZContentObject`, `eZContentObjectTreeNode` | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md) |
| Feldtyp, Spaltentyp | Datentyp | `extends eZDataType` | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md#datentypen) |
| Query Builder | Fetch-Parameter (`subTreeByNodeID()`), Bedingungen von `eZPersistentObject`, SQL über `eZDB` | `lib/ezdb/` | [Abfragen](inhalte-und-tabellen-abfragen.md) |
| Datenbankverbindung | `eZDB::instance()` | `lib/ezdb/classes/` | [Abfragen](inhalte-und-tabellen-abfragen.md) |
| Migration | `.dba`-Schemadatei plus Ihre Upgrade-Skripte | `extension/<ext>/share/db_schema.dba` | [Migrationen](migrationen-und-dba-schemadateien.md) |
| Seeder, Fixture | `db_data.dba`, Inhaltspakete, `eZContentFunctions` | `share/`, `.ezpkg` | [Migrationen](migrationen-und-dba-schemadateien.md#startdaten-und-inhalte) |

## Konfiguration und Verdrahtung

| Framework-Begriff | Begriff in Exponential | Wo | Lesen |
|---|---|---|---|
| `config/*.yaml`, `config/*.php`, `settings.py` | INI-Dateien in Ebenen | `settings/`, `extension/<ext>/settings/` | [Konfiguration](konfiguration-ini-dateien.md) |
| `.env`, `APP_ENV` | `EXP_ENV` in `config.env.php` und Einstellungsvarianten | `config.env.php.example` | [Umgebungen und Geheimnisse](umgebungen-und-geheimnisse.md) |
| Service-Container, Provider | keiner im Kernel; in INI benannte Handler-Klassen | `eZExtension::getHandlerClass()` | [Dependency Injection](dependency-injection-und-dienste.md) |
| Bundle, Paket, App | Erweiterung | `extension/<ext>/` | [Pakete](pakete-erweiterungen-und-composer.md) |
| Event Dispatcher | `ezpEvent` | `kernel/private/classes/ezpevent.php` | [Ereignisse und Listener](ereignisse-und-listener.md) |
| Model-Observer, Signale, Callbacks | Workflows und Trigger an Operationen; Ereignis-Listener | `workflow.ini`, `site.ini [Event]` | [Workflows](workflows-trigger-und-domain-events.md) |

## Sicherheit

| Framework-Begriff | Begriff in Exponential | Wo | Lesen |
|---|---|---|---|
| Authentifizierung, Guard, Firewall | Login-Handler, Sitzungen, Login-Prüfung des Siteaccess | `site.ini [UserSettings] LoginHandler[]` | [Authentifizierung](authentifizierung-und-sitzungen.md) |
| Autorisierung, Voter, Gate, Policy | Rolle, Richtlinie, Funktion, Einschränkung | `eZRole`, `eZPolicy`, `$FunctionList` | [Autorisierung](autorisierung-rollen-und-richtlinien.md) |
| CSRF-Middleware | `ezformtoken` | `extension/ezformtoken/` | [Formulare und CSRF](formulare-und-csrf-schutz.md) |
| Form Request, Validator | Modulaktionen plus Ihre eigenen Prüfungen; Datentyp-Validierung für Inhalte | `eZInputValidator` | [Validierung](validierung.md) |
| API-Token | persönliche API-Schlüssel, OAuth2, Token von expservices | `rest.ini`, Modul `apikey` | [REST- und JSON-APIs](rest-und-json-apis.md) |

## Hintergrundarbeit, APIs und Werkzeuge

| Framework-Begriff | Begriff in Exponential | Wo | Lesen |
|---|---|---|---|
| Console, artisan, manage.py | `bin/php/console` | `bin/php/` | [Kommandozeile](cli-und-konsolenbefehle.md) |
| Queue, Job, Worker | Jobs und Handler von `expqueue` | `extension/expqueue/` | [Warteschlangen](warteschlangen-und-hintergrundjobs.md) |
| Scheduler | Cronjob-Teile, ausgeführt von `runcronjobs.php` | `settings/cronjob.ini` | [Geplante Aufgaben](geplante-aufgaben-und-cronjobs.md) |
| REST-Ressource, API-Controller | REST-Provider, ezjscore-Serverfunktion, expservices | `ezprestapi`, `ezjscore.ini` | [REST- und JSON-APIs](rest-und-json-apis.md) |
| Profiler-Toolbar | die Exp-Debug-Leiste | `site.ini [DebugSettings]` | [Debugging](debugging-logging-und-fehler.md) |
| Anwendungsserver (Octane, RoadRunner) | Exponential Velocity, FrankenPHP | `settings/velocity.ini` | [Server und Deployment](server-und-deployment.md) |

## Kein direktes Gegenstück

Sprechen Sie diese Punkte früh im Projekt offen an, damit niemand danach sucht:

- **Ein allgemeiner Dependency-Injection-Container mit Autowiring.** Im Legacy-Kernel nicht vorhanden.
- **Routing per Annotation oder Attribut.** Routen werden ausschließlich in Arrays in `module.php` deklariert.
- **Migrationsklassen mit Up- und Down-Methoden und eine Migrationstabelle.** An ihre Stelle treten Schemadateien und
  Ihre eigenen Upgrade-Skripte.
- **Template-Vererbung mit Blöcken.** An ihre Stelle treten Overrides und Includes.
- **Automatisches Escaping der Ausgabe.** Templates escapen mit `|wash` dort, wo Sie es hinschreiben
  ([Kein automatisches Escaping](../template-sprache/smarty-unterschiede-im-detail.md#keine-automatische-maskierung)).

## Verwandte Seiten

- [Glossar für Web-Framework-Entwickler](glossar-fuer-framework-entwickler.md)
- [PHP-Entwickler: hier beginnen](../php-entwickler/php-entwickler-hier-beginnen.md)
- [Anwendungsersteller: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
