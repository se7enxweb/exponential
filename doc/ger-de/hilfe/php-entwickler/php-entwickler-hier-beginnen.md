---
title: "PHP-Entwickler: hier beginnen"
audience: php-developer, developer
category: php-development
order: 1
summary: Wo PHP-Entwickler beginnen - was Exponential als PHP-Codebasis ist, Ihre erste Stunde, die Regeln, denen jede PHP-Änderung folgt, und eine Übersicht aller Seiten für PHP-Entwickler, vom Lebenszyklus einer Anfrage und der PHP-8.0-Kompatibilität über Modul-Views, Datentypen, Sicherheit, Debugging, Velocity-Worker, Tests und statische Analyse bis zum Mitwirken.
keywords: PHP-Entwickler, hier beginnen, Übersicht, Index, Kernel, Erweiterungen, Coding-Standard, statische Analyse, Sicherheit, Mitwirken, PHP developer, PHP developers, start here, overview, index, kernel, extensions, PHP 8, coding standard, PHPStan, PHPUnit, Velocity, security, debugging, Composer, contributing
featured: true
type: help
---
# PHP-Entwickler: hier beginnen

Dieser Abschnitt der Hilfe richtet sich an PHP-Entwickler, die PHP-Code an oder für Exponential schreiben: Kernel-Korrekturen,
Erweiterungen, Module und Views, Datentypen, persistente Objekte, Fetch-Funktionen, Template-Operatoren, Workflow-Ereignistypen,
Cronjob-Teile, Befehle, JSON- und REST-Endpunkte sowie die zugehörigen Tests. Er erklärt die Konventionen der Codebasis, die
Regeln, die das Projekt bei jeder Änderung prüft, und wie sich Code unter jeder Laufzeitumgebung verhält, mit echten
Klassennamen, Dateien und Befehlen des Repositorys.

Jede Aussage auf diesen Seiten wurde am Code, an den Einstellungen oder an der vorhandenen Dokumentation der Linie 6.0.15
geprüft. Wo sich diese Seiten mit den Seiten für Anwendungsentwickler und dem Buch für Erweiterungsentwickler überschneiden,
verweisen sie dorthin, statt sie zu wiederholen.

## Inhalt

1. [Exponential in einem Absatz, für PHP-Entwickler](#exponential-in-einem-absatz-für-php-entwickler)
2. [Ihre erste Stunde](#ihre-erste-stunde)
3. [Sieben Regeln für jede PHP-Änderung](#sieben-regeln-für-jede-php-änderung)
4. [Übersicht der Seiten für PHP-Entwickler](#übersicht-der-seiten-für-php-entwickler)
5. [Seiten anderer Zielgruppen, die Sie nutzen werden](#seiten-anderer-zielgruppen-die-sie-nutzen-werden)
6. [Diese Seiten wiederfinden](#diese-seiten-wiederfinden)

## Exponential in einem Absatz, für PHP-Entwickler

**Exponential** ist ein freies und quelloffenes PHP-Content-Management-System und Anwendungs-Framework, lizenziert unter der
GNU GPL, Version 2 oder später. Seine Codebasis wird seit 1999 entwickelt, zuerst unter dem Namen eZ Publish, einem Produkt,
das sein Hersteller nach fast 18 Jahren eingestellt hat; 7x hat es am Leben erhalten, und es wird von 7x gemeinsam mit der
Exponential Foundation geführt ([Wer ist 7x?](../wer-ist-7x.md), [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)).
Für einen PHP-Entwickler ist es eine große, ausgereifte Codebasis mit eigenen Konventionen: PHP 8.0 bis 8.5, generierte
Klassen-Maps statt PSR-4, geschichtete INI-Einstellungen, die über `eZINI` gelesen werden, `eZPersistentObject` für Tabellen,
eine kompilierte Template-Sprache, sechs unterstützte Datenbanken (standardmäßig SQLite 3, PostgreSQL, MongoDB, MariaDB,
Oracle, MySQL) und Velocity, ein Anwendungsserver mit persistenten Workern, als Teil des Produkts
([Exponential für PHP-Entwickler](exponential-fuer-php-entwickler.md)).

## Ihre erste Stunde

1. **Orientieren Sie sich** (10 Minuten): [Exponential für PHP-Entwickler](exponential-fuer-php-entwickler.md) und
   [Ein Rundgang durch den Quellbaum](ein-rundgang-durch-den-quellbaum.md).
2. **Verfolgen Sie eine Anfrage** (10 Minuten): [Wie eine Anfrage in PHP abläuft](wie-eine-anfrage-in-php-ablaeuft.md).
3. **Richten Sie einen Checkout ein** (20 Minuten): [Einen PHP-Entwicklungs-Checkout einrichten](ein-php-entwicklungs-checkout.md), mit
   `make devtools` und `make hooks`.
4. **Lernen Sie die Regeln** (10 Minuten): [Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md) und
   [Der Coding-Standard](der-coding-standard.md).
5. **Bauen Sie etwas** (10 Minuten für den Anfang): [Schreiben Sie Ihre erste Modul-View](ihre-erste-modul-view-schreiben.md).

Den gesamten Abschnitt in Reihenfolge finden Sie unter [Der Lesepfad für PHP-Entwickler](lesepfad-fuer-php-entwickler.md).

## Sieben Regeln für jede PHP-Änderung

1. **Läuft unter PHP 8.0 bis 8.5.** Keine Syntax und keine ungeschützte Funktion, die neuer als 8.0 ist
   ([Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md)).
2. **Folgt dem Hausstil.** `array()`, Leerzeichen innerhalb von Klammern, keine Typdeklarationen; `make phpcs` und
   `make phpstan` melden nichts Neues ([Der Coding-Standard](der-coding-standard.md)).
3. **Trägt ein Präfix im Namen.** `exp` für Projektcode, Ihr eigenes für Ihren Code; Kernel-Bezeichner `eZ*` ändern sich nie
   ([Klassenbenennung und das Präfix exp](klassenbenennung-und-das-praefix-exp.md)).
4. **Vertraut keiner Eingabe und maskiert jede Ausgabe.** Casts, Allow-Listen, `escapeString()`, `|wash`, das Formular-Token,
   Berechtigungsprüfungen an jedem Einstiegspunkt ([Sicherheit in PHP-Code](sicherheit-in-php-code.md)).
5. **Behält keinen Zustand zwischen Anfragen.** Korrekt unter den persistenten Workern von Velocity
   ([Fallen durch statischen Zustand](fallen-durch-statischen-zustand.md)).
6. **Funktioniert auf jeder unterstützten Datenbank.** Portables SQL oder persistente Objekte
   ([Code für jede unterstützte Datenbank](code-fuer-jede-unterstuetzte-datenbank.md)).
7. **Kommt mit einem Test.** Wo möglich ohne Datenbank ([PHPUnit ohne Datenbank](phpunit-ohne-datenbank.md)).

## Übersicht der Seiten für PHP-Entwickler

### Orientierung

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Exponential für PHP-Entwickler](exponential-fuer-php-entwickler.md) | Verstehen, in welcher Art von Codebasis Sie arbeiten |
| [Ein Rundgang durch den Quellbaum](ein-rundgang-durch-den-quellbaum.md) | Kernel, Bibliotheken, Befehle, Einstellungen und Tests finden |
| [Wie eine Anfrage in PHP abläuft](wie-eine-anfrage-in-php-ablaeuft.md) | Wissen, wo Ihr Code läuft und welche Ereignisse Sie nutzen können |
| [Einen PHP-Entwicklungs-Checkout einrichten](ein-php-entwicklungs-checkout.md) | Einen Checkout mit den Werkzeugen und Hooks bekommen |
| [Der Lesepfad für PHP-Entwickler](lesepfad-fuer-php-entwickler.md) | Den Abschnitt in Reihenfolge lesen |

### PHP-Versionen und Code-Regeln

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Unterstützte PHP-Versionen: 8.0 bis 8.5](unterstuetzte-php-versionen.md) | Den Bereich kennen und wissen, wie jede Version getestet wird |
| [Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md) | Neuere Syntax und Funktionen vermeiden, Benötigtes absichern |
| [Hinweise zur PHP-8-Migration für Ihren Code](hinweise-zur-php-8-migration-fuer-ihren-code.md) | Älteren Code auf PHP 8 bringen |
| [Der Coding-Standard (phpcs.xml.dist)](der-coding-standard.md), [Warum der Code keine Typdeklarationen hat](warum-keine-typdeklarationen.md) | Im Hausstil schreiben und verstehen, warum |
| [Klassenbenennung und das Präfix exp](klassenbenennung-und-das-praefix-exp.md), [Autoloads und Klassen-Maps](autoloads-und-klassen-maps.md) | Klassen benennen und auffindbar machen |
| [Einstellungen mit eZINI lesen](einstellungen-mit-ezini-lesen.md) | Konfiguration korrekt lesen |

### Bausteine

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Schreiben Sie Ihre erste Modul-View](ihre-erste-modul-view-schreiben.md), [Modul-Views als Klassen](modul-views-als-klassen.md) | Seiten hinzufügen und Kernel-Views ändern, ohne sie zu kopieren |
| [Einen Konsolenbefehl schreiben](einen-konsolenbefehl-schreiben.md), [Einfache CLI-Skripte mit eZScript und ezexec](einfache-cli-skripte-mit-ezscript.md) | Kommandozeilenwerkzeuge hinzufügen |
| [Einen Cronjob-Teil schreiben](einen-cronjob-teil-schreiben.md) | Arbeit zeitlich planen |
| [Eine Fetch-Funktion schreiben](eine-fetch-funktion-schreiben.md), [Einen Template-Operator in PHP schreiben](einen-template-operator-in-php-schreiben.md) | Templates Daten und Hilfsfunktionen geben |
| [Einen Datentyp schreiben](einen-datentyp-schreiben.md), [Einen Workflow-Ereignistyp schreiben](einen-workflow-ereignistyp-schreiben.md) | Feldtypen und Workflow-Schritte hinzufügen |
| [Ereignisse, Listener und Filter](ereignisse-listener-und-filter.md), [Erweiterungspunkte aus PHP](erweiterungspunkte-aus-php.md) | Sich in den Kernel einklinken, ohne ihn zu bearbeiten |

### Daten und APIs

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [SQL mit eZDB, sicher](sql-mit-ezdb.md), [Code für jede unterstützte Datenbank](code-fuer-jede-unterstuetzte-datenbank.md) | Sicheres, portables SQL schreiben |
| [Definitionen persistenter Objekte im Detail](definitionen-persistenter-objekte-im-detail.md) | Daten in eigenen Tabellen speichern |
| [ezjscore-JSON-Endpunkte in PHP](ezjscore-json-endpunkte-in-php.md), [REST und entfernte Dienste aus PHP](rest-und-entfernte-dienste-aus-php.md) | Daten über HTTP ausliefern |
| [Composer für PHP-Entwickler](composer-fuer-php-entwickler.md) | Abhängigkeiten verwalten und Erweiterungen paketieren |

### Sicherheit

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Sicherheit in PHP-Code](sicherheit-in-php-code.md) | Jede Sicherheitsregel in einem Durchgang anwenden |
| [Eingaben validieren und casten](eingaben-validieren-und-casten.md), [Ausgaben aus PHP maskieren](ausgaben-aus-php-maskieren.md) | Ein- und Ausgaben sicher behandeln |
| [CSRF-Schutz und ezformtoken](csrf-und-ezformtoken.md) | POSTs schützen, auch aus JavaScript |

### Debugging und Performance

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Debugging mit eZDebug](debugging-mit-ezdebug.md), [Die Debug-Leiste für PHP-Entwickler](die-debug-leiste-fuer-php-entwickler.md) | In eine Anfrage hineinsehen; ein Debug-Panel hinzufügen |
| [Logs, der Log-Viewer und Anfrage-Logs](logs-log-viewer-und-anfrage-logs.md) | Logs schreiben und lesen; einer Fehlerreferenz folgen |
| [PHP-Code profilieren](php-code-profilieren.md), [OPcache und Cache-Vorladen](opcache-und-cache-vorladen.md) | Messen, beschleunigen und vorwärmen |

### Laufzeitumgebungen

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Persistente Velocity-Worker für PHP-Entwickler](persistente-velocity-worker-fuer-php-entwickler.md), [Fallen durch statischen Zustand](fallen-durch-statischen-zustand.md) | Code schreiben, der unter persistenten Workern korrekt ist |
| [Der Kernel-Service-Container](der-kernel-service-container.md) | Den Kernel nach gemeinsam genutzten Objekten fragen, eigene definieren, ihren Gültigkeitsbereich wählen |
| [FrankenPHP für PHP-Entwickler](frankenphp-fuer-php-entwickler.md) | Unter FrankenPHP ausführen und testen |
| [Das Engine-Archiv (phar) für PHP-Entwickler](das-engine-phar-fuer-php-entwickler.md) | Pfade korrekt halten, wenn der Kernel aus einem Archiv läuft |

### Tests, Qualität und Mitwirken

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [PHPUnit ohne Datenbank](phpunit-ohne-datenbank.md), [Datenbank- und Live-Tests](datenbank-und-live-tests.md) | Ihren Code auf der richtigen Ebene testen |
| [Statische Analyse mit PHPStan](statische-analyse-mit-phpstan.md), [Git-Hooks und make-Ziele](git-hooks-und-make-ziele.md) | Die Prüfungen des Projekts lokal ausführen |
| [CI und das Nachstellen einer roten Prüfung](ci-und-eine-rote-pruefung-nachstellen.md) | Eine rote CI-Prüfung beheben |
| [PHP-Änderungen beitragen](php-aenderungen-beitragen.md) | Eine Änderung in den Kernel bringen |

### Referenz

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Checklisten für PHP-Entwickler](checklisten-fuer-php-entwickler.md) | Ihre Arbeit in einem Durchgang prüfen |
| [FAQ für PHP-Entwickler](faq-fuer-php-entwickler.md), [Glossar für PHP-Entwickler](glossar-fuer-php-entwickler.md) | Schnelle Antworten und Begriffe |

## Seiten anderer Zielgruppen, die Sie nutzen werden

| Seite | Warum PHP-Entwickler sie lesen |
|---|---|
| [Anwendungsentwickler: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md) | Vollständige Anwendungen bauen: Content-API, Warteschlangen, REST, Deployment, Skalierung |
| [Das Buch für Erweiterungsentwickler](../erweiterungsentwicklung/README.md) | Das vollständige Buch für Entwickler von Erweiterungen, von den ersten Schritten bis zum Release, mit einer API-Referenz in Teil 8 |
| [Software-Ingenieure: hier beginnen](../softwareingenieure/softwareingenieure-hier-beginnen.md) | Architektur, Evaluierung und Engineering-Praxis rund um Exponential |
| [Web-Framework-Entwickler: hier beginnen](../web-framework-entwickler/web-framework-entwickler-hier-beginnen.md) | Exponential im Vergleich zu und in Verbindung mit anderen Web-Frameworks |
| [Die Template-Sprache](../template-sprache/README.md), [Template-Operatoren](../template-operatoren/README.md) | Die Templates, die Ihr PHP-Code mit Daten versorgt |
| [Datenbankunterstützung](../datenbankunterstuetzung.md) | Die sechs Datenbanken, ihre Versionen, Einstellungen und Testabdeckung |
| [Mit Git entwickeln](../mit-git-entwickeln.md) | Mit dem Repository arbeiten |
| [Freie Dokumentation](../freie-dokumentation.md) | Was Sie mit diesen Seiten tun dürfen |

## Diese Seiten wiederfinden

- Filtern Sie in der Hilfe nach der Zielgruppe **PHP-Entwickler**, oder öffnen Sie **Alle Seiten** und den Ordner `php-developers`.
- Jede Seite hat **Auf GitHub bearbeiten**. Wenn eine Aussage hier nicht mehr zum Code passt, sagen Sie es uns dort oder im
  [Issue-Tracker der Community](https://github.com/se7enxweb/exponential-community/issues).

## Verwandte Seiten

- [Exponential für PHP-Entwickler](exponential-fuer-php-entwickler.md)
- [Der Lesepfad für PHP-Entwickler](lesepfad-fuer-php-entwickler.md)
- [Anwendungsentwickler: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
