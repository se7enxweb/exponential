---
title: Umstieg von Slim, CodeIgniter, Yii, CakePHP oder Laminas
audience: web-framework-developer, developer
category: web-frameworks
order: 450
summary: Ein Leitfaden für Entwickler, die von Slim, CodeIgniter, Yii, CakePHP und Laminas (Zend Framework) kommen - wie deren Controller, Modelle, Views, Konfiguration, Middleware und Module sich auf Exponential abbilden lassen, welche Konventionen Ihnen vertraut vorkommen werden und was an einem inhaltsorientierten Framework anders ist.
keywords: Umstieg, Slim, CodeIgniter, Yii, CakePHP, Laminas, Zend Framework, Mezzio, MVC, Controller, Modelle, Module, coming from, controllers, models, views, active record, modules, middleware, PSR-7, PSR-15, Web-Framework-Entwickler, web framework developer
type: help
---
# Umstieg von Slim, CodeIgniter, Yii, CakePHP oder Laminas

Diese Seite richtet sich an Entwickler, deren PHP-Hintergrund eines der klassischen MVC-Frameworks oder ein
Micro-Framework ist. Die gute Nachricht: Der PHP-Stil von Exponential (Klassen mit statischen Hilfsmethoden, Active
Record, Konvention vor Konfiguration, Arrays als Konfiguration) steht CodeIgniter, Yii 1 und CakePHP näher als
Frameworks, die stark auf einen Container setzen. Der Unterschied: Exponential ist inhaltsorientiert; ein
Inhaltsmodell, Berechtigungen und eine Administrationsoberfläche sind bereits vorhanden.

## Inhalt

1. [Die MVC-Bausteine](#die-mvc-bausteine)
2. [Framework für Framework](#framework-für-framework)
3. [PSR-Standards](#psr-standards)
4. [Was für Sie neu ist](#was-für-sie-neu-ist)

## Die MVC-Bausteine

| Baustein | In Ihrem Framework | In Exponential 6 |
|---|---|---|
| Controller | eine Klasse mit Action-Methoden | ein Modul-View pro Adresse ([Controller](controller-als-modul-views.md)) |
| Modell | Active Record (CodeIgniter-Modelle, Yii `ActiveRecord`, CakePHP-Tables und -Entities) oder ein Table Gateway (Laminas) | `eZPersistentObject` für Ihre Tabellen, die Inhalts-API für Inhalte ([ORM](orm-und-ezpersistentobject.md)) |
| View | PHP-Templates, Twig oder Plates | die Template-Sprache ([Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md)) |
| Routen | eine Routendatei oder Konventionen | Konventionen nach `/<module>/<view>` ([Routing](routing-mit-modulen-und-views.md)) |
| Konfiguration | PHP-Arrays | INI-Dateien in Ebenen ([Konfiguration](konfiguration-ini-dateien.md)) |
| Module, Plugins | HMVC-Module, Yii-Module, CakePHP-Plugins, Laminas-Module | Erweiterungen ([Pakete](pakete-erweiterungen-und-composer.md)) |

## Framework für Framework

| Framework | Vertraut | Anders |
|---|---|---|
| **Slim** | kleiner Kern, Sie wählen die Teile; Denken in Middleware | keine PSR-15-Pipeline; stattdessen Request-Regeln und Listener ([Middleware](middleware-und-request-regeln.md)) |
| **CodeIgniter** | Adressen nach `/controller/method/param` sehen aus wie `/module/view/param`; Helper und Libraries | Berechtigungen, Sitzungen und CSRF sind anders eingebaut ([Authentifizierung](authentifizierung-und-sitzungen.md), [Formulare und CSRF](formulare-und-csrf-schutz.md)) |
| **Yii** | Active Record, Behaviors, Module, ein Code-Generator (Gii) | die RAD-Werkzeuge übernehmen die Rolle von Gii ([RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md)); RBAC sind Rollen und Richtlinien, als Daten gespeichert ([Autorisierung](autorisierung-rollen-und-richtlinien.md)) |
| **CakePHP** | Konventionen, Bake, Plugins, ORM mit Tables | Schemadateien statt eines Migrations-Plugins ([Migrationen](migrationen-und-dba-schemadateien.md)) |
| **Laminas, Mezzio** | Module, Zusammenführen von Konfiguration, Event-Manager | kein Service-Manager ([Dependency Injection](dependency-injection-und-dienste.md)); `ezpEvent` statt des Event-Managers ([Ereignisse](ereignisse-und-listener.md)) |

## PSR-Standards

Exponential 6 wird mit Composer installiert und lädt den Autoloader von Composer, sodass PSR-4-Bibliotheken in Ihrer
Erweiterung funktionieren. Der Kernel selbst ist älter als PSR-7 und PSR-15: Requests werden über `eZHTTPTool` und
`eZSys` gelesen, Responses sind ein Ergebnis-Array oder eine direkte Ausgabe, und es gibt keinen PSR-11-Container.
Planen Sie nicht damit, PSR-7-Objekte durch den Kernel zu reichen.

## Was für Sie neu ist

- **Inhaltsklassen** ersetzen die meisten Tabellen, die Sie sonst entwerfen würden ([Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md)).
- **Siteaccesses** wählen pro Request eine ganze Konfiguration aus ([Siteaccesses](siteaccesses-hosts-und-mandanten.md)).
- **Template-Overrides** wählen Templates nach Klasse, Knoten oder View-Modus aus ([Template-Auflösung](template-aufloesung-designs-und-overrides.md)).
- **Workflows** klinken sich in das Veröffentlichen und den Bestellabschluss ein ([Workflows](workflows-trigger-und-domain-events.md)).
- **Persistente Worker** unter Velocity: kein Zustand pro Request in statischen Variablen ([Server und Deployment](server-und-deployment.md)).

Beginnen Sie mit [Aufgabe: ein kleines CRUD-Modul bauen](ihr-erstes-modul-schritt-fuer-schritt.md); es wird sich anfühlen wie
der erste Controller, den Sie in Ihrem Framework geschrieben haben.

## Verwandte Seiten

- [Begriffswörterbuch](begriffswoerterbuch.md)
- [Umstieg von Symfony](umstieg-von-symfony.md), [Umstieg von Laravel](umstieg-von-laravel.md)
- [Autoloads und Klassenbenennung](../ersteller/anwendungsentwickler/autoloads-und-klassenbenennung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
