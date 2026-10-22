---
title: Lesepfad für Web-Framework-Entwickler
audience: web-framework-developer, developer
category: web-frameworks
order: 30
summary: In welcher Reihenfolge Sie die Seiten dieser Zielgruppe lesen - ein Orientierungstag, der Request und seine Bausteine, Daten, die Querschnittsthemen, der Betrieb und die Brücke zu anderen Frameworks - mit Zeitangaben und den Seiten der Anwendungsentwickler und des Buchs zur Erweiterungsentwicklung, die Sie danach lesen.
keywords: Lesepfad, Lernpfad, Lehrplan, Reihenfolge, Einarbeitung, erste Woche, reading path, learning path, curriculum, onboarding, first week, Web-Framework-Entwickler, web framework developer
type: help
---
# Lesepfad für Web-Framework-Entwickler

Diese Seite richtet sich an Entwickler, die eine Lesereihenfolge möchten. Jede Etappe dauert etwa einen halben Tag;
die Aufgabe in Etappe 2 ist der praktische Teil. Überspringen Sie die Etappen, die Ihr Projekt nicht braucht.

## Inhalt

1. [Etappe 1: Orientierung](#etappe-1-orientierung)
2. [Etappe 2: der Request und Ihr erstes Modul](#etappe-2-der-request-und-ihr-erstes-modul)
3. [Etappe 3: Daten](#etappe-3-daten)
4. [Etappe 4: Querschnittsthemen](#etappe-4-querschnittsthemen)
5. [Etappe 5: Hintergrundarbeit und APIs](#etappe-5-hintergrundarbeit-und-apis)
6. [Etappe 6: der Betrieb](#etappe-6-der-betrieb)
7. [Etappe 7: andere Frameworks daneben](#etappe-7-andere-frameworks-daneben)
8. [Nach diesem Pfad](#nach-diesem-pfad)

## Etappe 1: Orientierung

1. [Exponential für Web-Framework-Entwickler](exponential-fuer-framework-entwickler.md)
2. Ihre Umstiegsseite: [Symfony](umstieg-von-symfony.md), [Laravel](umstieg-von-laravel.md),
   [Slim, CodeIgniter, Yii, CakePHP oder Laminas](umstieg-von-micro-und-klassischen-php-frameworks.md),
   [Django oder Rails](umstieg-von-django-und-rails.md), [Express oder Spring](umstieg-von-express-und-spring.md)
3. [Begriffswörterbuch](begriffswoerterbuch.md)
4. [Wann Sie Exponential wählen sollten](wann-sie-exponential-waehlen-sollten.md)

## Etappe 2: der Request und Ihr erstes Modul

1. [Der Request-Lebenszyklus](der-request-lebenszyklus.md)
2. [Siteaccesses](siteaccesses-hosts-und-mandanten.md)
3. [Routing](routing-mit-modulen-und-views.md), [Sprechende URLs](url-aliase-und-platzhalter.md)
4. [Controller](controller-als-modul-views.md), [Responses](responses-weiterleitungen-und-fehler.md)
5. [Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md), [Template-Auflösung](template-aufloesung-designs-und-overrides.md),
   [Fetch-Funktionen](fetch-funktionen-und-template-daten.md)
6. Selbst machen: [Aufgabe: ein kleines CRUD-Modul bauen](ihr-erstes-modul-schritt-fuer-schritt.md)

## Etappe 3: Daten

1. [Das Inhaltsmodell für ORM-Nutzer](das-inhaltsmodell-fuer-orm-nutzer.md)
2. [ORM: eZPersistentObject](orm-und-ezpersistentobject.md)
3. [Abfragen](inhalte-und-tabellen-abfragen.md)
4. [Migrationen und Startdaten](migrationen-und-dba-schemadateien.md)

## Etappe 4: Querschnittsthemen

1. [Konfiguration](konfiguration-ini-dateien.md), [Umgebungen und Geheimnisse](umgebungen-und-geheimnisse.md)
2. [Dependency Injection](dependency-injection-und-dienste.md), [Erweiterungspunkte](erweiterungspunkte-und-handler.md)
3. [Middleware](middleware-und-request-regeln.md), [Ereignisse und Listener](ereignisse-und-listener.md),
   [Workflows](workflows-trigger-und-domain-events.md)
4. [Authentifizierung](authentifizierung-und-sitzungen.md), [Autorisierung](autorisierung-rollen-und-richtlinien.md)
5. [Formulare und CSRF](formulare-und-csrf-schutz.md), [Validierung](validierung.md)
6. [Caching](cache-ebenen.md), [HTTP-Caching](http-caching-und-reverse-proxies.md)
7. [Übersetzungen und Rechts-nach-links-Schrift](i18n-l10n-und-rtl.md)

## Etappe 5: Hintergrundarbeit und APIs

1. [Kommandozeile](cli-und-konsolenbefehle.md)
2. [Warteschlangen](warteschlangen-und-hintergrundjobs.md), [Geplante Aufgaben](geplante-aufgaben-und-cronjobs.md)
3. [REST- und JSON-APIs](rest-und-json-apis.md), [Headless-Frontends](headless-und-entkoppelte-frontends.md)

## Etappe 6: der Betrieb

1. [Testen](testen.md), [Debugging](debugging-logging-und-fehler.md)
2. [Server und Deployment](server-und-deployment.md), [Pakete](pakete-erweiterungen-und-composer.md)
3. [Zehn Stolperfallen](stolperfallen-fuer-framework-entwickler.md), [Checklisten](checklisten-fuer-framework-entwickler.md)

## Etappe 7: andere Frameworks daneben

1. [Exponential und ein Framework kombinieren](exponential-und-ein-framework-kombinieren.md)
2. [Die Symfony Legacy Bridge](symfony-und-die-legacy-bridge.md)
3. [Die Plattformprodukte](die-plattformprodukte.md)

## Nach diesem Pfad

- Die Leitfäden für Anwendungsentwickler gehen bei jedem Thema tiefer ([Anwendungsentwickler: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md),
  [ihr Lesepfad](../ersteller/anwendungsentwickler/lesepfad-fuer-anwendungsentwickler.md)).
- Das Buch zur Erweiterungsentwicklung erklärt jeden Erweiterungspunkt Schritt für Schritt ([Erweiterungsentwicklung](../erweiterungsentwicklung/README.md)).
- Der Bereich zur Template-Sprache erklärt die Sprache ausführlich ([Die Template-Sprache](../template-sprache/README.md)).

## Verwandte Seiten

- [Web-Framework-Entwickler: hier beginnen](web-framework-entwickler-hier-beginnen.md)
- [FAQ für Web-Framework-Entwickler](faq-fuer-web-framework-entwickler.md)
- [Glossar für Web-Framework-Entwickler](glossar-fuer-framework-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
