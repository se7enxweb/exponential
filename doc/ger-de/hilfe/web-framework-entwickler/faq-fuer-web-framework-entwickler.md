---
title: FAQ für Web-Framework-Entwickler
audience: web-framework-developer, developer
category: web-frameworks
order: 520
summary: Kurze Antworten auf die Fragen, die Entwickler aus Symfony, Laravel, Django, Rails, Express und Spring am häufigsten zu Exponential stellen - ist es ein Framework, wo ist der Container, kann ich Twig oder Composer-Bibliotheken nutzen, wie funktionieren Migrationen, gibt es ein ORM, Asynchronität, GraphQL, Tests, Worker wie bei Octane, und wo fange ich an.
keywords: FAQ, Fragen, Antworten, Framework, Container, Twig, Composer, Migrationen, ORM, GraphQL, PSR, Octane, asynchron, Tests, Einstieg, questions, answers, migrations, async, tests, start, Web-Framework-Entwickler, web framework developer
type: help
---
# FAQ für Web-Framework-Entwickler

Diese Seite richtet sich an Entwickler mit einer kurzen Frage. Jede Antwort verlinkt auf die Seite mit den
Einzelheiten.

## Inhalt

1. [Ist Exponential ein Framework oder ein CMS?](#ist-exponential-ein-framework-oder-ein-cms)
2. [Wo ist der Service-Container?](#wo-ist-der-service-container)
3. [Gibt es einen Router mit benannten Routen?](#gibt-es-einen-router-mit-benannten-routen)
4. [Gibt es ein ORM?](#gibt-es-ein-orm)
5. [Wie funktionieren Migrationen?](#wie-funktionieren-migrationen)
6. [Kann ich Twig oder Blade verwenden?](#kann-ich-twig-oder-blade-verwenden)
7. [Kann ich Composer-Bibliotheken verwenden?](#kann-ich-composer-bibliotheken-verwenden)
8. [Folgt es PSR-7 und PSR-15?](#folgt-es-psr-7-und-psr-15)
9. [Gibt es Middleware?](#gibt-es-middleware)
10. [Gibt es eine Entsprechung zu Octane oder RoadRunner?](#gibt-es-eine-entsprechung-zu-octane-oder-roadrunner)
11. [Gibt es GraphQL?](#gibt-es-graphql)
12. [Wie teste ich?](#wie-teste-ich)
13. [Wie führe ich Hintergrundjobs aus?](#wie-führe-ich-hintergrundjobs-aus)
14. [Welche Datenbanken?](#welche-datenbanken)
15. [Wo fange ich an?](#wo-fange-ich-an)

## Ist Exponential ein Framework oder ein CMS?

Beides. Seine README nennt es „a professional PHP application framework with advanced CMS (content management system)
functionality“, also ein professionelles PHP-Anwendungsframework mit fortgeschrittener CMS-Funktionalität
([Exponential für Web-Framework-Entwickler](exponential-fuer-framework-entwickler.md)).

## Wo ist der Service-Container?

Im Kernel von Exponential 6 gibt es keinen. Klassen werden über erzeugte Klassenzuordnungen gefunden, konfigurierbare
Implementierungen über Handler-Einstellungen ([Dependency Injection und Dienste](dependency-injection-und-dienste.md)).

## Gibt es einen Router mit benannten Routen?

Nein. Adressen folgen dem Schema `/<module>/<view>/<parameters>`, deklariert in `module.php`; Inhalte erhalten
automatisch URL-Aliase ([Routing](routing-mit-modulen-und-views.md), [Sprechende URLs](url-aliase-und-platzhalter.md)).

## Gibt es ein ORM?

`eZPersistentObject` bildet Ihre eigenen Tabellen auf Klassen ab; Inhalte verwenden die Content-API mit Versionen und
Übersetzungen ([ORM](orm-und-ezpersistentobject.md), [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md)).

## Wie funktionieren Migrationen?

Tabellen werden in datenbankneutralen `.dba`-Dateien beschrieben; Sie liefern einen Installationsbefehl und
Upgrade-Schritte aus. Einen Migrations-Runner gibt es nicht ([Migrationen](migrationen-und-dba-schemadateien.md)).

## Kann ich Twig oder Blade verwenden?

Nicht in Exponential 6 allein. Twig steht zur Verfügung, wenn Exponential 6 über die Legacy Bridge innerhalb von
Symfony läuft ([Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md#kann-ich-stattdessen-twig-verwenden)).

## Kann ich Composer-Bibliotheken verwenden?

Ja. `autoload.php` lädt den Autoloader von Composer, und Erweiterungen sind Composer-Pakete
([Pakete](pakete-erweiterungen-und-composer.md)).

## Folgt es PSR-7 und PSR-15?

Der Kernel ist älter als diese Standards: Requests werden über `eZHTTPTool` gelesen, Responses sind Ergebnis-Arrays oder
direkte Ausgaben ([Umstieg von Slim, CodeIgniter, Yii, CakePHP oder Laminas](umstieg-von-micro-und-klassischen-php-frameworks.md#psr-standards)).

## Gibt es Middleware?

Request-Regeln, Listener auf `request/input` und Filter auf `response/output` decken die meisten Anwendungsfälle von
Middleware ab ([Middleware](middleware-und-request-regeln.md)).

## Gibt es eine Entsprechung zu Octane oder RoadRunner?

Ja: Exponential Velocity führt die Installation in persistenten Workern aus und ist seit 6.0.15 Teil von Exponential;
FrankenPHP ist die Alternative ([Server und Deployment](server-und-deployment.md)).

## Gibt es GraphQL?

Nicht in Exponential 6; seine APIs sind REST, ezjscore und expservices. Die auf Symfony basierenden Plattformprodukte
liefern GraphQL mit ([REST- und JSON-APIs](rest-und-json-apis.md), [Die Plattformprodukte](die-plattformprodukte.md)).

## Wie teste ich?

Mit PHPUnit; die meisten Tests ohne Datenbank, Integrationstests gegen eine Wegwerfdatenbank
([Testen](testen.md)).

## Wie führe ich Hintergrundjobs aus?

Mit der Job-Warteschlange `expqueue`, Cronjob-Teilen und Inhaltsjobs ([Warteschlangen](warteschlangen-und-hintergrundjobs.md)).

## Welche Datenbanken?

SQLite (die Voreinstellung), PostgreSQL, MongoDB, MariaDB, Oracle und MySQL ([Datenbankunterstützung](../datenbankunterstuetzung.md)).

## Wo fange ich an?

Bei [Web-Framework-Entwickler: hier beginnen](web-framework-entwickler-hier-beginnen.md), danach beim
[Lesepfad](lesepfad-fuer-web-framework-entwickler.md).

## Verwandte Seiten

- [Begriffswörterbuch](begriffswoerterbuch.md)
- [Glossar für Web-Framework-Entwickler](glossar-fuer-framework-entwickler.md)
- [FAQ für Anwendungsentwickler](../ersteller/anwendungsentwickler/faq-fuer-anwendungsentwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
