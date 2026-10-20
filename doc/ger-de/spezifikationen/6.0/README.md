# Spezifikationen von Exponential 6.0

Die Spezifikationen sind die Referenzseiten von Exponential 6.0: Datenmodelle, Klassen und Schnittstellen, jede
Einstellung mit ihrem Standardwert und Geltungsbereich, Erweiterungspunkte, Ereignisse, APIs und Befehle. Verwenden Sie
sie, wenn Sie eine genaue Antwort brauchen. Für einen ersten Rundgang durch das Produkt beginnen Sie mit
[den Anleitungen](../../anleitungen/README.md); für Begriffe verwenden Sie [das Glossar](../../../en/us/glossary.md).

Jede Seite beginnt damit, wofür sie gedacht ist und wer sie lesen sollte, gibt ihre Einstellungen als Tabellen an (Datei,
Block, Schlüssel, Standard, Geltungsbereich) und endet mit „Verwandte Seiten“.

## Kernel und Inhalte

| Seite | Inhalt |
|---|---|
| [Befehle, Cronjob-Teile und Modul-Views als Klassen](ausfuehrbare-befehle-cronjobs-ansichten.md) | Die ausführbaren Klassen, Einstiegspunkte, Ereignisse, Neuimplementierung und Listener |
| [INI-Override-Verzeichnisse und Platzierungen](ini-override-platzierungen.md) | Wie `eZINI` Einstellungsdateien ordnet; Einstellungen je Siteaccess innerhalb von Extensions |
| [Audit-Ereignismodell](audit-ereignismodell.md) | Ereignisnamen, Datensätze, Kanäle, Hash-Ketten, `audit.ini`, Erweiterungspunkte |
| [Metadaten von Extensions](extension-metadaten.md) | `ezinfo.php`, `extension.xml`, `expInfo`, die Release-Regel |
| [Installer-Protokolle und Startdaten](installer-protokolle-und-startdaten.md) | Startdatendateien, der Inhaltsbaum einer neuen Installation, Installer-Protokolle |
| [Benachrichtigungen](benachrichtigungen.md) | Ereignisse, Handler, Regeln, Sammelbenachrichtigung, der Service, Sperre und Laufprotokoll, Views, Templates |
| [Einstellungen für Benachrichtigungen](benachrichtigungen-ini.md) | `notification.ini` und die zugehörigen Einstellungen in `site.ini`, `cronjob.ini` und `menu.ini` mit Standardwerten |
| [Benachrichtigungsbefehle](benachrichtigungen-cli.md) | `exp:notification:status`, `run`, `events`, `subscriptions`: Optionen, Exit-Codes, Beispielausgaben |
| [E-Mail-Einstellungen und Einwilligung](mail-einstellungen.md) | Kategorien, Einstellungen, das Einwilligungsprotokoll, Sperrung, das Mail-Gate, Links, Tabellen, Abläufe, Einstellungen |
| [Befehle für die E-Mail-Einstellungen](mail-einstellungen-cli.md) | `exp:mail:status`, `preferences`, `suppression`, `consent`, `gate`: Aktionen, Optionen, Exit-Codes, Beispielausgaben |
| [E-Mail-Einstellungen: die Checkliste zu den Gesetzen](mail-einstellungen-compliance.md) | CAN-SPAM, DSGVO und ePrivacy, CCPA/CPRA, CASL, RFC 8058, abgebildet auf Code und Tests; die Lücken |
| [Hilfesystem](hilfesystem.md) | Hilfe und Handbücher als Inhalte: DITA-Themenklassen, Sektionen, Objektzustände, Rollen, Validator, Web, Kontexthilfe und Auslieferung als E-Book |
| [Leitfaden für Hilfethemen](hilfe-stilrichtlinien.md) | Redaktionelle Regeln für Hilfethemen: Thementypen mit Vorlagen, Schritte, Hinweise, Abbildungen, `help_id`, Felder, Terminologie |

## Datenbanken

| Seite | Inhalt |
|---|---|
| [SQLite3-Datenbanktreiber](sqlite3-datenbanktreiber.md) | `eZSQLite3DB`, PRAGMAs, Transaktionen und der einzige Schreiber |
| [Datenbanktreiber und Installer](datenbanktreiber-2026-09.md) | Änderungen an SQLite, PostgreSQL, MySQL und Oracle vom September und Oktober 2026 |
| [Verhalten der Treiber für SQLite und Oracle](datenbanktreiber-sqlite-oracle.md) | Verschoben; verweist auf die beiden Seiten oben |

## Sicherheit

| Seite | Inhalt |
|---|---|
| [Sicherheitsvorgaben vom September 2026](sicherheitsvorgaben-2026-09.md) | Sicherheits-Header, Sitzungscookie, Anmeldung, ausgelieferte Dateien, Caching |
| [Härtung der Datentypen und Eingaben](datentyp-eingabehaertung.md) | Was jeder Datentyp und jede Eingabehilfe ablehnt |
| [Die Sicherheitspatches vom August 2026](sicherheitshaertung-2026-08.md) | Sechs Befunde, jQuery 3.7.1, die Kontoprüfung |
| [Die Härtung von 6.0.13](sicherheitshaertung-6.0.13.md) | Sieben Befunde und die Stabilitätskorrekturen für PHP 8 |
| [Continuous Integration und die Testsuite](kontinuierliche-integration.md) | Die PHPUnit-Suites und der GitHub-Actions-Workflow |

## Webserver Velocity

| Seite | Inhalt |
|---|---|
| [Einstellungen und Programme der Velocity-Engine](velocity-engine-einstellungen.md) | `Q.*`-Einstellungen, Programme, Logging, Branding, Ereignisschleife |
| [Worker-Pool und Kompatibilitätsschicht von Velocity](velocity-worker-pool.md) | Worker, Poolgröße, Zygote, Zurücksetzen zwischen Anfragen |
| [Velocity: HTTP/2, Anfrageverarbeitung und Sicherheit](velocity-http2-und-sicherheit.md) | HTTP/2, Framing, Größenbeschränkungen für Anfrage-Bodys, Document Root, Header |

## Extensions

| Seite | Inhalt |
|---|---|
| [bccie](bccie.md) | Export gesammelter Informationen: Views, Dateien, Befehle, Einstellungen |
| [cjw_newsletter](cjw_newsletter.md) | Newsletter-Dashboard, Befehle, Cronjob-Teile, Hintergrundläufe und Einstellungen |
| [expservices](expservices.md) | Remote-Dienste über ezjscore |
| [explayouts_ui_api](explayouts-ui-api.md) | Die JSON-API des Layout-Editors |
| [syndication](syndikation.md) | Export und Import von Feeds |
| [xrowextract](xrowextract.md) | Export und Import von Inhalten, Pakete und Zeitpläne |

## Exponential Platform (auf Symfony basierend)

| Seite | Inhalt |
|---|---|
| [Paketübersicht der Plattform](platform-paketzuordnung.md) | Welches se7enxweb-Paket welches Upstream-Paket ersetzt |
| [Namen der Konsolenbefehle der Plattform](platform-konsolenbefehle.md) | Befehlsnamen mit `exponential:` und ihre Aliase |
| [SQLite-Installer der Plattform](platform-sqlite-installer.md) | Installationstypen und SQLite-Startdaten auf der Plattform |
| [Legacy-Bridge-Bundle](legacy-bridge-bundle.md) | Exponential 6 innerhalb der Plattform ausführen |

## Verwandte Seiten

- [Glossar](../../../en/us/glossary.md), [Anleitungen](../../anleitungen/README.md), [Geschichte](../../../en/us/history/README.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
