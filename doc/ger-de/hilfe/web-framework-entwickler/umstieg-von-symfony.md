---
title: Umstieg von Symfony
audience: web-framework-developer, developer
category: web-frameworks
order: 430
summary: Ein Leitfaden für Symfony-Entwickler – was sich direkt übertragen lässt (Front Controller, Kernel-Ereignisse, Konsole, Twig-ähnliche Templates, Bundles) und was nicht (Container, Routing, Doctrine, Forms, Security), welche Gewohnheiten Sie behalten und welche Sie ablegen sollten, ein Plan für die erste Woche und wann Sie stattdessen die Symfony-basierte Plattform oder die Legacy Bridge verwenden.
keywords: Symfony, Umstieg von Symfony, coming from Symfony, HttpKernel, Bundle, services.yaml, Container, Doctrine, Twig, Forms, Security, Messenger, Console, Kernel-Ereignisse, kernel events, Legacy Bridge, legacy bridge, Exponential Platform, Web-Framework-Entwickler, web framework developer
type: help
---
# Umstieg von Symfony

Diese Seite richtet sich an Entwickler, die mit Symfony arbeiten. Viele Konzepte von Exponential haben ein Gegenstück in
Symfony, einige nicht, und es gibt eine Symfony-basierte Exponential-Produktlinie, falls Sie ganz bei Symfony bleiben
möchten. Lesen Sie die Seite mit dem [Begriffswörterbuch](begriffswoerterbuch.md) zur Hand.

## Inhalt

1. [Was sich direkt übertragen lässt](#was-sich-direkt-übertragen-lässt)
2. [Was anders funktioniert](#was-anders-funktioniert)
3. [Gewohnheiten, die Sie behalten, und Gewohnheiten, die Sie ablegen](#gewohnheiten-die-sie-behalten-und-gewohnheiten-die-sie-ablegen)
4. [Ihre erste Woche](#ihre-erste-woche)
5. [Bei Symfony bleiben](#bei-symfony-bleiben)

## Was sich direkt übertragen lässt

| Symfony | Exponential 6 | Seite |
|---|---|---|
| `public/index.php`, `Kernel::handle()` | `index.php`, `ezpKernel` mit `ezpKernelWeb` | [Der Request-Lebenszyklus](der-request-lebenszyklus.md) |
| Listener für `kernel.request`, `kernel.response` | Listener für `request/input`, Filter für `response/output` | [Ereignisse und Listener](ereignisse-und-listener.md) |
| `bin/console`, `#[AsCommand]` | `bin/php/console`, `Exponential\Runnable\Command` | [Kommandozeile](cli-und-konsolenbefehle.md) |
| Bundles, `config/bundles.php` | Erweiterungen, `ActiveExtensions[]` | [Pakete](pakete-erweiterungen-und-composer.md) |
| `APP_ENV`, `when@prod` | `EXP_ENV`, Einstellungsvarianten | [Umgebungen](umgebungen-und-geheimnisse.md) |
| Messenger | `expqueue` | [Warteschlangen](warteschlangen-und-hintergrundjobs.md) |
| Translation-Komponente | `.ts`-Kataloge, Operator `i18n` | [Übersetzungen](i18n-l10n-und-rtl.md) |
| Web Profiler | Exp-Debug-Leiste | [Debugging](debugging-logging-und-fehler.md) |

## Was anders funktioniert

| Symfony | Exponential 6 | Seite |
|---|---|---|
| DependencyInjection-Container, Autowiring | kein Container; in INI benannte Handler-Klassen, Factories | [Dependency Injection](dependency-injection-und-dienste.md) |
| Routing-Komponente, Attribut-Routen | `/<module>/<view>`, deklariert in `module.php`; URL-Aliase für Inhalte | [Routing](routing-mit-modulen-und-views.md) |
| Controller, die eine `Response` zurückgeben | Modul-Views, die ein `$Result`-Array zurückgeben | [Controller](controller-als-modul-views.md) |
| Twig mit `extends` und Blöcken, automatisches Escaping | die Template-Sprache mit Overrides, `\|wash` von Hand | [Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md) |
| Doctrine-Entitäten und -Migrationen | Inhaltsklassen; `eZPersistentObject`; `.dba`-Schemadateien | [ORM](orm-und-ezpersistentobject.md), [Migrationen](migrationen-und-dba-schemadateien.md) |
| Form-Komponente | Templates, Modulaktionen, `ezformtoken` | [Formulare](formulare-und-csrf-schutz.md) |
| Security: Firewalls, Voter | Login-Handler, Rollen und Richtlinien als Daten gespeichert | [Autorisierung](autorisierung-rollen-und-richtlinien.md) |
| YAML-Konfiguration | INI-Dateien in Ebenen | [Konfiguration](konfiguration-ini-dateien.md) |
| HttpCache, ESI | rollenabhängiger HTTP-Cache, Velocity-Response-Cache; kein ESI | [HTTP-Caching](http-caching-und-reverse-proxies.md) |

## Gewohnheiten, die Sie behalten, und Gewohnheiten, die Sie ablegen

Behalten Sie: schlanke Controller mit der Logik in Diensten, Konstruktorparameter für die Testbarkeit, PHPUnit,
umgebungsspezifische Konfiguration, niemals Geheimnisse committen, ein Befehl pro Deployment-Schritt.

Legen Sie ab: die Suche nach einem Container, in dem Sie Dienste registrieren; die Erwartung von automatischem Escaping;
eine Entität und eine Migration für etwas zu schreiben, das Redakteure pflegen (machen Sie daraus eine Inhaltsklasse);
denselben Wert in fünf Ebenen zu konfigurieren, ohne `exp:ini where` zu prüfen; die Annahme, dass jeder Request einen
frischen Prozess bekommt (Velocity hält Worker vor, wie es RoadRunner tun würde).

## Ihre erste Woche

1. Installieren Sie eine Entwicklungskopie (`./console exp:install`) und lesen Sie [Exponential für Web-Framework-Entwickler](exponential-fuer-framework-entwickler.md).
2. Tag zwei: [Der Request-Lebenszyklus](der-request-lebenszyklus.md), [Siteaccesses](siteaccesses-hosts-und-mandanten.md),
   [Konfiguration](konfiguration-ini-dateien.md).
3. Tag drei: [Aufgabe: ein kleines CRUD-Modul bauen](ihr-erstes-modul-schritt-fuer-schritt.md).
4. Tag vier: [Das Inhaltsmodell für ORM-Nutzer](das-inhaltsmodell-fuer-orm-nutzer.md) und
   [Template-Auflösung](template-aufloesung-designs-und-overrides.md).
5. Tag fünf: [Server und Deployment](server-und-deployment.md) und [Testen](testen.md).

## Bei Symfony bleiben

Wenn Ihr Projekt eine Symfony-Anwendung sein muss, gibt es zwei Wege:

- **Exponential Platform DXP** ist ein reines Symfony-CMS, aufgebaut wie jedes Symfony-Projekt ([Die Plattformprodukte](die-plattformprodukte.md)).
- **Die Legacy Bridge** betreibt Exponential 6 innerhalb eines Symfony-Plattformprojekts, sodass Redakteure die
  Legacy-Administration behalten, während Sie Symfony-Code und Twig schreiben ([Die Symfony Legacy Bridge](symfony-und-die-legacy-bridge.md)).

## Verwandte Seiten

- [Umstieg von Laravel](umstieg-von-laravel.md)
- [Exponential und ein Framework kombinieren](exponential-und-ein-framework-kombinieren.md)
- [Zehn Stolperfallen für Framework-Entwickler](stolperfallen-fuer-framework-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
