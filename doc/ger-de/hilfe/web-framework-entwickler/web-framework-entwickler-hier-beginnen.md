---
title: "Web-Framework-Entwickler: hier beginnen"
audience: web-framework-developer, developer
category: web-frameworks
order: 1
summary: Wo Entwickler, die Symfony, Laravel, Slim, CodeIgniter, Yii, CakePHP, Laminas, Django, Rails, Express oder Spring kennen, mit Exponential beginnen - was es in ihren Begriffen ist, die erste Stunde, eine Übersicht aller Seiten, die Framework-Konzepte übertragen (Request-Lebenszyklus, Routing, Controller, Templates, ORM, Migrationen, Konfiguration, DI, Middleware, Ereignisse, Sicherheit, Caching, i18n, CLI, Warteschlangen, APIs, Testen, Deployment, die Symfony-Bridge und die Plattformprodukte) und die Seiten anderer Zielgruppen, die Sie danach nutzen.
keywords: Web-Framework-Entwickler, Framework, hier beginnen, Überblick, Übersicht, Begriffslandkarte, Übertragung, Einarbeitung, web framework developer, Symfony, Laravel, Slim, CodeIgniter, Yii, CakePHP, Laminas, Django, Rails, Express, Spring, start here, overview, index, concept map, onboarding
featured: true
type: help
---
# Web-Framework-Entwickler: hier beginnen

Dieser Bereich der Hilfe richtet sich an Entwickler, die ein Web-Framework kennen (Symfony, Laravel, Slim,
CodeIgniter, Yii, CakePHP, Laminas, Django, Rails, Express, Spring) und auf Exponential aufbauen möchten. Exponential
beschreibt sich selbst als „a professional PHP application framework with advanced CMS (content management system)
functionality“ (README), also als professionelles PHP-Anwendungsframework mit fortgeschrittener CMS-Funktionalität.
Diese Seiten übertragen, was Sie bereits kennen, in die Begriffe von Exponential, sagen klar, wo etwas anders
funktioniert, und sagen ebenso klar, wo Exponential keine direkte Entsprechung hat.

Jede Klasse, jede Einstellung und jeder Befehl, die auf diesen Seiten genannt werden, wurde am Kernel, an den
Einstellungen oder an der vorhandenen Dokumentation geprüft. Wo eine Seite mit einem anderen Framework vergleicht,
beschreibt sie, wofür dieses Framework bekannt ist, ohne es zu bewerten.

## Inhalt

1. [Exponential in einem Absatz](#exponential-in-einem-absatz)
2. [Ihre erste Stunde](#ihre-erste-stunde)
3. [Fünf Dinge, die anders sind](#fünf-dinge-die-anders-sind)
4. [Übersicht der Seiten](#übersicht-der-seiten)
5. [Seiten anderer Zielgruppen, die Sie nutzen werden](#seiten-anderer-zielgruppen-die-sie-nutzen-werden)
6. [Diese Seiten wiederfinden](#diese-seiten-wiederfinden)

## Exponential in einem Absatz

**Exponential 6** ist ein freies, quelloffenes PHP-Anwendungsframework und Content-Management-System unter der GNU GPL,
Version 2 oder später. Ein Request kommt über `index.php` herein, wird einem **Siteaccess** zugeordnet (einer benannten
Konfiguration pro Site, Sprache oder Administrationsoberfläche), von einem URL-Alias in eine Adresse der Form
`/<module>/<view>` übersetzt, gegen **Rollen und Richtlinien** geprüft, von einer **Modul-View** bearbeitet, mit der
**Template-Sprache** gerendert und in das Seitenlayout eingesetzt. Daten, die Redakteure pflegen, liegen in
**Inhaltsklassen** mit Versionen und Übersetzungen; Ihre eigenen Tabellen verwenden `eZPersistentObject`. Die
Konfiguration besteht aus **INI-Dateien in Ebenen**; der Code liegt in **Erweiterungen**, die mit Composer installiert
werden. Ausgeliefert wird über Apache oder nginx mit PHP-FPM, über **Exponential Velocity**, einen Server mit
persistenten Workern, der seit 6.0.15 Teil des Produkts ist, oder über FrankenPHP. Die Codebasis wurde ab 1999
entwickelt, zunächst unter dem Namen eZ Publish, den sein Hersteller nach fast 18 Jahren eingestellt hat; 7x hat sie
als Exponential fortgeführt ([Wer ist 7x?](../wer-ist-7x.md)).

## Ihre erste Stunde

1. **Installieren** (10 Minuten): `./console exp:install` richtet eine Site auf SQLite ein
   ([Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)).
2. **Orientieren** (10 Minuten): [Exponential für Web-Framework-Entwickler](exponential-fuer-framework-entwickler.md) und
   Ihre Umstiegsseite ([Symfony](umstieg-von-symfony.md), [Laravel](umstieg-von-laravel.md),
   [Slim, CodeIgniter, Yii, CakePHP oder Laminas](umstieg-von-micro-und-klassischen-php-frameworks.md),
   [Django oder Rails](umstieg-von-django-und-rails.md), [Express oder Spring](umstieg-von-express-und-spring.md)).
3. **Übertragen** (10 Minuten): das [Begriffswörterbuch](begriffswoerterbuch.md).
4. **Einem Request folgen** (10 Minuten): [Der Request-Lebenszyklus](der-request-lebenszyklus.md).
5. **Etwas bauen** (20 Minuten für den Anfang): [Aufgabe: ein kleines CRUD-Modul bauen](ihr-erstes-modul-schritt-fuer-schritt.md).

## Fünf Dinge, die anders sind

1. **Inhalte sind das Modell.** Redaktionelle Daten sind eine Inhaltsklasse mit Versionen, Übersetzungen und
   Berechtigungen, keine Tabelle, die Sie entwerfen ([Das Inhaltsmodell für ORM-Nutzer](das-inhaltsmodell-fuer-orm-nutzer.md)).
2. **Siteaccess vor dem Routing.** Pro Request wird eine ganze Konfiguration ausgewählt ([Siteaccesses](siteaccesses-hosts-und-mandanten.md)).
3. **Kein Service-Container.** Handler werden in INI-Einstellungen benannt ([Dependency Injection](dependency-injection-und-dienste.md)).
4. **Templates escapen nicht automatisch** und werden überschrieben statt erweitert ([Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md)).
5. **Persistente Worker.** Unter Velocity darf Code keinen Request-bezogenen Zustand in statischen Eigenschaften halten
   ([Server und Deployment](server-und-deployment.md)).

## Übersicht der Seiten

### Orientierung

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Exponential für Web-Framework-Entwickler](exponential-fuer-framework-entwickler.md) | Das Gesamtbild in Framework-Begriffen sehen |
| [Begriffswörterbuch](begriffswoerterbuch.md) | Den Begriff Ihres Frameworks nachschlagen und den von Exponential finden |
| [Lesepfad](lesepfad-fuer-web-framework-entwickler.md) | Die Seiten in einer sinnvollen Reihenfolge lesen |
| [Wann Sie Exponential wählen sollten und wann ein reines Framework](wann-sie-exponential-waehlen-sollten.md) | Für ein Projekt fair entscheiden |

### Umstieg von einem anderen Framework

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Umstieg von Symfony](umstieg-von-symfony.md) | Bundles, Kernel-Ereignisse, Doctrine, Forms und Security übertragen |
| [Umstieg von Laravel](umstieg-von-laravel.md) | Routen, Eloquent, Blade, Middleware, Queues und Artisan übertragen |
| [Umstieg von Slim, CodeIgniter, Yii, CakePHP oder Laminas](umstieg-von-micro-und-klassischen-php-frameworks.md) | Gewohnheiten aus klassischem MVC und Micro-Frameworks übertragen |
| [Umstieg von Django oder Rails](umstieg-von-django-und-rails.md) | URLconf, ORMs, die Administration, Signale und Befehle übertragen |
| [Umstieg von Express oder Spring](umstieg-von-express-und-spring.md) | Das Prozessmodell von PHP verstehen und den Rest übertragen |

### Der Request

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Der Request-Lebenszyklus](der-request-lebenszyklus.md) | Wissen, wo Ihr Code läuft, von `index.php` bis zu den Ausgabefiltern |
| [Siteaccesses: einen Request einer Site zuordnen](siteaccesses-hosts-und-mandanten.md) | Sites, Sprachen und die Administrationsoberfläche pro Request konfigurieren |
| [Routing: Module, Views und Parameter](routing-mit-modulen-und-views.md) | Adressen deklarieren und URLs erzeugen |
| [Sprechende URLs: URL-Aliase und URL-Platzhalter](url-aliase-und-platzhalter.md) | Slugs, Weiterleitungen und Umschreibungen ohne Code nutzen |
| [Controller: Modul-Views](controller-als-modul-views.md) | Eine View als Skript oder Klasse schreiben und Kernel-Views ändern |
| [Responses, Weiterleitungen, Fehler und JSON](responses-weiterleitungen-und-fehler.md) | Seiten, Weiterleitungen, Fehler und JSON zurückgeben |

### Views und Templates

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Die View-Schicht: die Template-Sprache neben Twig und Blade](templates-im-vergleich-zu-twig-und-blade.md) | Templates schreiben und die klassischen Fehler vermeiden |
| [Template-Auflösung: Designs und Overrides](template-aufloesung-designs-und-overrides.md) | Wissen, welche Datei rendert, und sie überschreiben |
| [Daten in Templates bringen: Fetch-Funktionen und Operatoren](fetch-funktionen-und-template-daten.md) | Templates Daten und Hilfsfunktionen bereitstellen |

### Daten

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Das Inhaltsmodell für ORM-Nutzer](das-inhaltsmodell-fuer-orm-nutzer.md) | Redaktionelle Daten als Klassen, Objekte, Versionen und Knoten modellieren |
| [ORM und Active Record: eZPersistentObject](orm-und-ezpersistentobject.md) | Eigene Tabellen auf Klassen abbilden |
| [Abfragen: Content-Fetches, eZDB und SQL](inhalte-und-tabellen-abfragen.md) | Inhalte und Tabellen portabel abfragen |
| [Migrationen und Startdaten: .dba-Schemadateien und Pakete](migrationen-und-dba-schemadateien.md) | Schemas und Startdaten ausliefern und aktualisieren |

### Konfiguration und Verdrahtung

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Konfiguration: INI-Dateien statt YAML, PHP-Arrays oder .env](konfiguration-ini-dateien.md) | Einstellungen lesen, schichten und ändern |
| [Umgebungen und Geheimnisse: EXP_ENV und settings/override](umgebungen-und-geheimnisse.md) | Entwicklung, Test und Produktion betreiben und Geheimnisse aus Git heraushalten |
| [Dependency Injection und Dienste: was es gibt](dependency-injection-und-dienste.md) | Testbaren Code ohne Container verdrahten |
| [Erweiterungspunkte: Handler, INI-Registrierung und Overrides](erweiterungspunkte-und-handler.md) | Die richtige Stelle zum Einklinken finden |
| [Pakete: Erweiterungen, Composer und Bundles](pakete-erweiterungen-und-composer.md) | Ihren Code strukturieren und verteilen |

### Request-Verarbeitung, Ereignisse und Sicherheit

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Middleware: Request-Regeln, Eingabe- und Ausgabe-Listener](middleware-und-request-regeln.md) | Das tun, was Middleware tut |
| [Ereignisse und Listener: ezpEvent](ereignisse-und-listener.md) | Kernel-Ereignisse abonnieren und eigene auslösen |
| [Workflows und Trigger: Einstiegspunkte an Inhaltsoperationen](workflows-trigger-und-domain-events.md) | Schritte vor und nach dem Veröffentlichen und dem Bestellabschluss ausführen |
| [Authentifizierung und Sitzungen](authentifizierung-und-sitzungen.md) | Benutzer und Maschinen anmelden |
| [Autorisierung: Rollen, Richtlinien und Einschränkungen](autorisierung-rollen-und-richtlinien.md) | Berechtigungen deklarieren und prüfen |
| [Formulare und CSRF-Schutz](formulare-und-csrf-schutz.md) | Formulare sicher verarbeiten |
| [Validierung: Eingaben, Datentypen und Escaping](validierung.md) | Eingaben validieren und Ausgaben escapen |

### Performance und Sprachen

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Caching: die Ebenen und wie man sie invalidiert](cache-ebenen.md) | Richtig cachen und das Richtige leeren |
| [HTTP-Caching und Reverse Proxies](http-caching-und-reverse-proxies.md) | Header, den HTTP-Cache und den Response-Cache nutzen |
| [Übersetzungen, Locales und Rechts-nach-links-Schrift](i18n-l10n-und-rtl.md) | Die Oberfläche und die Inhalte übersetzen |

### Hintergrundarbeit, APIs und Betrieb

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Kommandozeile: die Konsole und eZScript](cli-und-konsolenbefehle.md) | Befehle ausführen und schreiben |
| [Warteschlangen und Hintergrundjobs](warteschlangen-und-hintergrundjobs.md) | Langsame Arbeit aus Requests auslagern |
| [Geplante Aufgaben: Cronjobs](geplante-aufgaben-und-cronjobs.md) | Arbeit nach Zeitplan ausführen |
| [REST- und JSON-APIs](rest-und-json-apis.md) | Die richtige API-Schicht wählen |
| [Headless und entkoppelte Frontends](headless-und-entkoppelte-frontends.md) | Inhalte an ein anderes Frontend ausliefern |
| [Testen: PHPUnit, Datenbanktests und Prüfungen](testen.md) | Ohne Angst testen |
| [Debugging, Logging und Fehler](debugging-logging-und-fehler.md) | In einen Request hineinsehen und die Logs lesen |
| [Server und Deployment: Apache und PHP-FPM, Velocity, FrankenPHP](server-und-deployment.md) | Ausliefern und deployen |

### Exponential mit anderen Frameworks

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Exponential und ein Framework kombinieren](exponential-und-ein-framework-kombinieren.md) | Beides in einem Projekt verbinden |
| [Die Symfony Legacy Bridge für Framework-Entwickler](symfony-und-die-legacy-bridge.md) | Exponential 6 innerhalb von Symfony betreiben |
| [Die Produkte der Exponential Platform](die-plattformprodukte.md) | Eine auf Symfony basierende Distribution wählen |

### Praxis, Prüfungen und Referenz

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Aufgabe: ein kleines CRUD-Modul Schritt für Schritt bauen](ihr-erstes-modul-schritt-fuer-schritt.md) | Jedes Teil einmal von Hand bauen |
| [Zehn Stolperfallen für Framework-Entwickler](stolperfallen-fuer-framework-entwickler.md) | Die Fehler vermeiden, die alle zuerst machen |
| [Checklisten für Framework-Entwickler](checklisten-fuer-framework-entwickler.md) | Bewerten, einrichten, prüfen und live gehen |
| [FAQ für Web-Framework-Entwickler](faq-fuer-web-framework-entwickler.md) | Schnelle Antworten erhalten |
| [Glossar für Web-Framework-Entwickler](glossar-fuer-framework-entwickler.md) | Einen Begriff von Exponential nachschlagen |

## Seiten anderer Zielgruppen, die Sie nutzen werden

| Seite | Warum |
|---|---|
| [Anwendungsentwickler: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md) | Die ausführlichen Leitfäden hinter den meisten Seiten hier: Module, Datentypen, Workflows, Warteschlangen, REST, Deployment |
| [Erweiterungsentwicklung: das Buch](../erweiterungsentwicklung/README.md) | Jeder Erweiterungspunkt Schritt für Schritt, vom Benutzer bis zum Maintainer |
| [PHP-Entwickler: hier beginnen](../php-entwickler/php-entwickler-hier-beginnen.md) | Die PHP-Seite: Sprache, Konventionen und die Klassen des Kernels |
| [Softwareingenieure: hier beginnen](../softwareingenieure/softwareingenieure-hier-beginnen.md) | Architektur, Qualität und Ingenieurspraxis |
| [Die Template-Sprache](../template-sprache/README.md) | Die Sprache im Detail, mit Vergleichen zu Smarty |
| [Kernel-Architektur](../../standards/architektur/kernel-architektur.md) | Die maßgebliche Beschreibung der Schichten des Kernels |
| [Erweiterungspunkte](../../bc/6.0/rad-erweiterungspunkte.md) | Der Katalog aller Erweiterungspunkte |
| [Datenbankunterstützung](../datenbankunterstuetzung.md) | Die unterstützten Datenbanken und wie jede getestet wird |

## Diese Seiten wiederfinden

Filtern Sie in der Hilfe nach der Zielgruppe **Web-Framework-Entwickler**; die Seiten liegen im Ordner
`web-framework-developers` (in der deutschen Hilfe `hilfe/web-framework-entwickler`). Suchen Sie nach dem Namen eines
Frameworks („Symfony“, „Laravel“, „Django“), um den Vergleich zu finden, der es erwähnt.

## Verwandte Seiten

- [Erste Schritte](../erste-schritte.md)
- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
