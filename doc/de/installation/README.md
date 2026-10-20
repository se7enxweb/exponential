# Exponential 6.0 installieren und betreiben: das Handbuch

Dieses Handbuch führt Sie von einem leeren Server zu einer Exponential-6.0-Site im Produktivbetrieb. Es behandelt die
Prüfung der Voraussetzungen, die Beschaffung des Codes, die Wahl und Ausführung eines Installers, die Auslieferung der
Site, die Wahl einer Datenbank und den gesunden Betrieb der Site danach, bis hin zu Upgrades, Fehlerbehebung und
Absicherung. Jedes Kapitel steht für sich und endet mit Verweisen auf die ergänzende Dokumentation in diesem
Repository und auf die offiziellen Quellen außerhalb davon.

Wenig Zeit? [Die Kurzanleitung zur Installation](../../en/us/INSTALL.md) enthält den Schnellstart und verweist zurück auf die
Kapitel hier.

## Inhalt

### Teil I: Vor der Installation

| Kapitel | Inhalt |
|---|---|
| [1. Einführung: Was Sie installieren](01-einfuehrung.md) | Der Kernel, Extensions, Siteaccesses (site, admin, editor) und Designs; die drei Installationsmethoden; Konventionen und Glossar |
| [2. Voraussetzungen](02-voraussetzungen.md) | PHP 8.0 bis 8.5, jede PHP-Erweiterung und wozu sie dient, die php.ini-Einstellungen, die das Setup prüft, Datenbanken, Composer, Webserver, Betriebssysteme, Dimensionierung; die Übersichten [Datenbankunterstützung](../../en/us/help/database-support.md) und [Unterstützte Betriebssysteme](../../en/us/help/os-support.md) |
| [3. Den Code beschaffen](03-den-code-beschaffen.md) | Versionen und Tags, Git-Klone mit Historie (empfohlen), `composer create-project`, Installation unter PHP 8.0 bis 8.3, das Velocity-Paket, Extensions als Pakete, ein Rundgang durch das Installationsverzeichnis |

### Teil II: Installieren

| Kapitel | Inhalt |
|---|---|
| [4. Eine Installationsmethode wählen](04-installationsweg-waehlen.md) | Ein Installer mit drei Oberflächen, Site-Pakete, was eine Installation schreibt, unbeaufsichtigte Installationen in CI und Containern |
| [5. Der Setup-Assistent](05-setup-assistent.md) | Der Browser-Installer Seite für Seite: jedes Feld, seine Prüfungen und was es schreibt; einen abgebrochenen Assistenten neu starten |
| [6. Der Kickstarter](06-kickstarter.md) | Unbeaufsichtigte Installationen aus `kickstart.ini`: Befehle, die vollständige Referenz der Einstellungen, `DatabaseAction`, kommentierte Beispiele, Probeläufe, erneute Ausführung |
| [7. Die Installation über die Konsole: `exp:install`](07-installation-ueber-die-konsole.md) | Ein Befehl mit Optionen statt einer Einstellungsdatei: jede Option, Probelauf, Exit-Status, Beispiele je Datenbank |

### Teil III: Die Site betreiben

| Kapitel | Inhalt |
|---|---|
| [8. Die Site ausliefern](08-die-website-ausliefern.md) | Exponential Velocity, das HTTP und HTTPS selbst ausliefert, ohne separaten Webserver; FrankenPHP; Apache und nginx mit PHP-FPM; Berechtigungen; Reverse Proxys |
| [9. Datenbanken](09-datenbanken.md) | Die unterstützten Datenbanken in ihrer offiziellen Reihenfolge (SQLite 3, die Voreinstellung; PostgreSQL; MongoDB; MariaDB; Oracle; MySQL): SQLite 3 als Produktivdatenbank unter hoher Last (WAL, Schreibwarteschlangen, Caching, Skalierung, Sicherungen), jede Server-Engine, ein Vergleich, der Wechsel der Engine und Microsoft SQL Server |
| [10. Nach der Installation: das Betriebshandbuch](10-nach-der-installation.md) | Erste Anmeldung, Siteaccesses, Cron, Mail, Benachrichtigungen und Newsletter, Caches, Autoloads, Sicherungen, Logs, Überwachung, Performance, Extensions |

### Teil IV: Den Betrieb aufrechterhalten

| Kapitel | Inhalt |
|---|---|
| [11. Upgrade](11-aktualisieren.md) | Welche Version Sie betreiben, die Update-Dateien, die Wege von 3.x, 4.x und 5.x sowie von jeder 6.0.x bis heute |
| [12. Fehlerbehebung](12-fehlerbehebung.md) | Symptom, Ursache und Lösung für jede Phase: Composer, der Assistent, der Kickstarter, Laufzeit, Datenbanken, Velocity, Caches, Anmeldung |
| [13. Sicherheitshärtung für den Produktivbetrieb](13-sicherheitshaertung.md) | Was der Server niemals herausgeben darf, Geheimnisse, Debug-Ausgaben, der Admin-Siteaccess, Passwörter und Sitzungen, Formular-Tokens, Header und HTTPS, das Audit-Protokoll |

### Teil V: Migration zu, innerhalb von und weg von Exponential

Beginnen Sie bei **[der Migrationsübersicht](migration/README.md)**: einer geprüften Tabelle aller Exponential-Produkte
und ihrer Versionen, einer Entscheidungshilfe und einer Anleitung je Weg, innerhalb der Exponential-Familie (Platform
Legacy, Nexus, DXP, Exponential Platform, Exponential Basic, ältere Releases) und von oder zu anderen Systemen
(WordPress, Drupal, Joomla, Magento, Shopify, Contentful, Adobe Experience Manager, Wix, Squarespace, GoDaddy,
statische Sites, JavaScript-, Symfony- und ColdFusion-Anwendungen), mit [der gemeinsamen Checkliste](migration/checkliste.md),
[den Import- und Export-Werkzeugen](migration/exponential-tools.md) und [der Zuordnung der Inhaltsmodelle](migration/inhaltsmodell-zuordnen.md).

Die folgenden Kapitel nennen die alten Produkte (eZ Publish, eZ Platform, Ibexa) nur, um das System zu bezeichnen,
von dem Sie migrieren. Jedes geht die Migrationsseiten des jeweiligen Herstellers Punkt für Punkt durch und sagt, wo
Exponential sich unterscheidet.

| Kapitel | Inhalt |
|---|---|
| [14. Migration von der 4.x-Linie](14-migration-von-4x.md) | eZ Publish 3.10 und 4.0 bis 4.7 unter PHP 5: Bestandsaufnahme, die Datenbank Schritt für Schritt, Dateien und Cluster, Einstellungen, Designs, Portierung von Extensions auf PHP 8, Passwörter, Suche, Überprüfung, häufige Probleme, Rückkehr zum alten Stand |
| [15. Migration vom 5.x-Legacy-Stack](15-migration-von-5x-legacy.md) | eZ Publish 5.x mit dem Symfony-Stack: welche Variante Sie betreiben, Herauslösen des Legacy-Teils, YAML zu INI, Feldtypen gegenüber Datentypen, Bildvarianten, Twig zu TPL, REST, Caches, häufige Probleme |
| [16. Migration von eZ Platform und Ibexa](16-migration-von-ez-platform-und-ibexa.md) | eZ Platform 1.x bis 3.x und Ibexa DXP/OSS zu Exponential Platform Nexus (der gleichwertige Symfony-Weg) oder zum Exponential-Legacy-Kernel, mit einer Entscheidungstabelle |
| [17. Migrationsreferenz](17-migrationsreferenz.md) | Nachschlagetabellen: jede Kernel-Tabelle, Datentypen und Feldtypen, INI- und YAML-Einstellungen, TPL und Twig, die PHP-API, Passwort-Hashes, URL-Aliase und Bildpfade, Datenprüfungen, Werkzeuge, Glossar |

## Welche Kapitel brauche ich?

| Ihre Situation | Lesen Sie |
|---|---|
| Eine erste Testinstallation auf einem Laptop | [Kurzanleitung](../../en/us/INSTALL.md), dann die Kapitel [3](03-den-code-beschaffen.md) und [7](07-installation-ueber-die-konsole.md) |
| Eine Produktiv-Site auf einem Server, ohne separaten Webserver | Kapitel [2](02-voraussetzungen.md), [3](03-den-code-beschaffen.md), [5](05-setup-assistent.md) oder [6](06-kickstarter.md), [8](08-die-website-ausliefern.md) (Velocity), [9](09-datenbanken.md) (SQLite), [10](10-nach-der-installation.md), [13](13-sicherheitshaertung.md) |
| Eine Produktiv-Site hinter Apache oder nginx mit MySQL oder PostgreSQL | Kapitel [2](02-voraussetzungen.md), [3](03-den-code-beschaffen.md), [5](05-setup-assistent.md) oder [6](06-kickstarter.md), [8](08-die-website-ausliefern.md), [9](09-datenbanken.md), [10](10-nach-der-installation.md), [13](13-sicherheitshaertung.md) |
| Unbeaufsichtigte Installationen in CI oder Containern | Kapitel [4](04-installationsweg-waehlen.md), [6](06-kickstarter.md), [7](07-installation-ueber-die-konsole.md) |
| Upgrade einer bestehenden Site | Kapitel [11](11-aktualisieren.md), [12](12-fehlerbehebung.md) |
| Umzug einer Site von eZ Publish 3.x oder 4.x | Kapitel [14](14-migration-von-4x.md), [17](17-migrationsreferenz.md), dann [11](11-aktualisieren.md) für die Update-Kette |
| Umzug einer Site von eZ Publish 5.x | Kapitel [15](15-migration-von-5x-legacy.md), [17](17-migrationsreferenz.md) |
| Umzug einer Site von eZ Platform oder Ibexa | Kapitel [16](16-migration-von-ez-platform-und-ibexa.md), [17](17-migrationsreferenz.md) |
| Wechsel zwischen Exponential-Produkten, von einem anderen CMS oder Website-Baukasten, oder weg von Exponential | [Die Migrationsübersicht](migration/README.md) |
| Etwas funktioniert nicht | Kapitel [12](12-fehlerbehebung.md) |

## Weitere Dokumentation

- [Erste Schritte](../anleitungen/erste-schritte.md), [Eine Site betreiben](../anleitungen/betrieb-einer-website.md) und [Upgrades](../anleitungen/aktualisieren.md): die Aufgaben-Anleitungen.
- [Hinweise zur Abwärtskompatibilität für 6.0](../../en/us/bc/6.0/): was sich geändert hat und was eine Site deswegen tun muss, nach Thema.
- [Changelogs](../../changelogs/6.0/): was jede 6.0.x-Release geändert hat.
- [Das README des Projekts](../../../README.md): ein Überblick über Exponential und seine Funktionen.

## Öffentliche Demos

Sie installieren eine **öffentliche Demo**, deren Administrator-Anmeldung veröffentlicht ist? Setzen Sie die
Umgebungsvariable `EXP_DEMO_MODE=1` für die Installation und danach für jeden PHP-Prozess der Demo: Die Installer
akzeptieren dann das veröffentlichte Passwort (`publish`, `publishing`), und Administratorpasswörter können weder
geändert noch zurückgesetzt werden. Die Kapitel 5, 6, 7 und 13 enthalten je einen Hinweis dazu; die ganze Geschichte
steht unter [Demo-Modus](../../en/us/bc/6.0/demo-mode.md), die Einrichtung auf einem Server unter
[Rezept: eine öffentliche Demo unter LiteSpeed und Plesk betreiben](../../en/us/help/hosting/servers/run-a-public-demo-on-litespeed-and-plesk.md).
