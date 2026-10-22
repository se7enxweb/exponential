---
title: Umstieg von Laravel
audience: web-framework-developer, developer
category: web-frameworks
order: 440
summary: Ein Leitfaden für Laravel-Entwickler - Routen, Controller, Blade, Eloquent, Migrationen, Middleware, Gates, Warteschlangen, der Scheduler, Artisan und Octane, jeweils mit ihrer Entsprechung in Exponential, was Sie ohne Code erhalten (Administrationsoberfläche, Versionen, Übersetzungen, Berechtigungen), die Gewohnheiten, die Sie anpassen sollten, und ein Plan für die erste Woche.
keywords: Umstieg von Laravel, Laravel, Eloquent, Blade, Artisan, Migrationen, Warteschlangen, Berechtigungen, coming from Laravel, migrations, middleware, gates, policies, queues, scheduler, Octane, service container, service providers, Web-Framework-Entwickler, web framework developer
type: help
---
# Umstieg von Laravel

Diese Seite richtet sich an Entwickler, die mit Laravel bauen. Laravel gibt Ihnen einen Werkzeugkasten, und Sie bauen
das CMS; Exponential gibt Ihnen ein CMS mit einem Anwendungs-Framework darunter. Vieles von dem, was Sie sonst
programmieren würden (Administrationsbildschirme, Revisionen, Übersetzungen, Berechtigungen, Slugs), ist bereits
vorhanden; für einige Annehmlichkeiten von Laravel gibt es keine Entsprechung.

## Inhalt

1. [Gegenüberstellung](#gegenüberstellung)
2. [Was Sie ohne Code erhalten](#was-sie-ohne-code-erhalten)
3. [Was Ihnen fehlen wird](#was-ihnen-fehlen-wird)
4. [Gewohnheiten, die Sie anpassen sollten](#gewohnheiten-die-sie-anpassen-sollten)
5. [Ihre erste Woche](#ihre-erste-woche)

## Gegenüberstellung

| Laravel | Exponential 6 | Seite |
|---|---|---|
| `routes/web.php` | Views in `module.php`; URL-Aliase für Inhalte | [Routing](routing-mit-modulen-und-views.md) |
| Controller | Modul-Views | [Controller](controller-als-modul-views.md) |
| Blade | die Template-Sprache | [Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md) |
| Eloquent-Modelle | Inhaltsklassen für redaktionelle Daten; `eZPersistentObject` für Ihre Tabellen | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md), [ORM](orm-und-ezpersistentobject.md) |
| Migrationen, Seeder | `.dba`-Schemadateien, Upgrade-Skripte, Pakete | [Migrationen](migrationen-und-dba-schemadateien.md) |
| Middleware | Request-Regeln, Eingabe- und Ausgabe-Listener | [Middleware](middleware-und-request-regeln.md) |
| `@csrf` | wird für angemeldete Benutzer automatisch von `ezformtoken` ergänzt | [Formulare und CSRF](formulare-und-csrf-schutz.md) |
| Form Requests, Validierungsregeln | Prüfungen im View oder in einer Validator-Klasse; Datentypen für Inhalte | [Validierung](validierung.md) |
| Gates und Policies | Funktionen in `module.php`, Rollen und Richtlinien als Daten | [Autorisierung](autorisierung-rollen-und-richtlinien.md) |
| Events, Observer | `ezpEvent`, Workflows bei Inhaltsoperationen | [Ereignisse](ereignisse-und-listener.md), [Workflows](workflows-trigger-und-domain-events.md) |
| Queues, Horizon | `expqueue`, **Setup > Job queue** | [Warteschlangen](warteschlangen-und-hintergrundjobs.md) |
| Scheduler | Cronjob-Teile | [Geplante Aufgaben](geplante-aufgaben-und-cronjobs.md) |
| Artisan | `bin/php/console` | [Kommandozeile](cli-und-konsolenbefehle.md) |
| `.env`, `config/*.php` | `EXP_ENV`, INI-Dateien | [Konfiguration](konfiguration-ini-dateien.md), [Umgebungen](umgebungen-und-geheimnisse.md) |
| `Cache::remember()` | `processCache()`, `{cache-block}`, der View-Cache | [Caching](cache-ebenen.md) |
| `__()`, `lang/` | `ezpI18n::tr()`, der Operator `i18n`, `.ts`-Dateien | [Übersetzungen](i18n-l10n-und-rtl.md) |
| Octane | die persistenten Worker von Velocity | [Server und Deployment](server-und-deployment.md) |
| Sanctum, Passport | persönliche API-Schlüssel, OAuth2 | [REST- und JSON-APIs](rest-und-json-apis.md) |

## Was Sie ohne Code erhalten

- Eine Administrationsoberfläche für jede Inhaltsklasse, mit Entwürfen, Versionen, Verlauf und Vergleich.
- Übersetzungen von Inhalten und URL-Aliase pro Sprache.
- Rollen und Richtlinien, die Administratoren bearbeiten, eingeschränkt nach Bereich, Klasse, Teilbaum, Sprache oder
  Status.
- Mehrere Websites und mehrsprachige Siteaccesses auf einer Datenbank.
- Einen Shop, Workflows mit Freigabe, Suche, Bildvarianten, einen Seiten-Baukasten (Exponential Layouts) und eine
  REST-API ([Was Exponential ist](../fuehrung/was-exponential-ist.md#was-der-kernel-leistet)).

## Was Ihnen fehlen wird

- Ein Service-Container und Service-Provider ([Dependency Injection](dependency-injection-und-dienste.md)).
- Benannte Routen und Route Model Binding.
- Die Relationen, Scopes und der Query Builder von Eloquent ([Abfragen](inhalte-und-tabellen-abfragen.md)).
- Blade-Komponenten und automatisches Escaping.
- Ein HTTP-Test-Client im selben Prozess und Model Factories ([Testen](testen.md)).

## Gewohnheiten, die Sie anpassen sollten

- Fragen Sie sich vor dem Anlegen einer Tabelle, ob Redakteure die Daten verwalten: Wenn ja, ist es eine
  Inhaltsklasse.
- Maskieren Sie jeden ausgegebenen Wert mit `|wash`.
- Führen Sie nach Codeänderungen `./console exp:velocity deploy` aus, so wie Sie sonst `octane:reload` ausführen
  würden.
- Prüfen Sie mit `./console exp:ini where`, woher eine Einstellung stammt, bevor Sie sie ändern.

## Ihre erste Woche

Folgen Sie dem Plan auf [Umstieg von Symfony](umstieg-von-symfony.md#ihre-erste-woche); er gilt ebenso für
Laravel-Entwickler, mit [Aufgabe: ein kleines CRUD-Modul bauen](ihr-erstes-modul-schritt-fuer-schritt.md) als Kernstück.

## Verwandte Seiten

- [Begriffswörterbuch](begriffswoerterbuch.md)
- [Wann Sie Exponential wählen sollten](wann-sie-exponential-waehlen-sollten.md)
- [Zehn Stolperfallen für Framework-Entwickler](stolperfallen-fuer-framework-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
