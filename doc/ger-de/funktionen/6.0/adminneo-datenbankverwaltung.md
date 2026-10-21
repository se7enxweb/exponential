# AdminNeo: die Datenbankverwaltung hinter dem Database Source Editor

Diese Seite ist für Administratoren, die wissen wollen, was das Datenbankwerkzeug in der Administrationsoberfläche kann,
oder die es für sich betreiben. AdminNeo ist ein vollständiges Werkzeug zur Datenbankverwaltung, geschrieben in PHP, ein
Fork von Adminer. In Exponential erreichen Sie es über die Extension `sevenx_dse`, hinter rollenbasierten
Berechtigungen, sodass ein Administrator der Website Tabellen ansehen und SQL in derselben Administrationsoberfläche
ausführen kann wie alles andere. Siehe [sevenx_dse](erweiterungen/sevenx_dse.md) zum Installieren und Verwenden.

| | |
|---|---|
| Repository | `se7enxweb/adminneo` (ein Fork des Projekts AdminNeo, das selbst auf Adminer beruht) |
| Neuestes Tag | 5.2.1 (07.12.2025); der Hauptbranch des Forks trägt die Entwicklung von 5.3 |
| Chronik | [adminneo](../../geschichte/oekosystem/adminneo.md): 1.121 Änderungen, die ganze Upstream-Historie vom Fork übernommen (der Fork hat im Verzeichnis keine eigenen Änderungen) |

## Was es einem Administrator von Exponential bietet

- Tabellen durchsehen, filtern, sortieren, durch Zeilen blättern und Werte direkt bearbeiten.
- SQL-Befehle mit Syntaxhervorhebung und (ab 5.1.0) Autovervollständigung ausführen.
- Tabellen, Spalten und Indizes anlegen, ändern und löschen.
- Export und Import: SQL-Dumps, CSV, TSV; Exporte können gepackt (Ausgabe-Plugins Bz2/Zip) oder als JSON oder XML
  geschrieben werden (Plugins).
- Eine aufgeräumte responsive Oberfläche mit dunklem Modus und Farbvarianten (ab 5.0.0).
- Plugins und Anpassungen: Der Fork bringt Plugins für Anmeldungen (Einmalpasswort, IP, tabellenbasiert, extern),
  Bearbeiten von Fremdschlüsseln, JSON-Vorschau, Erzeugen von Slugs, Übersetzung, einen Editor Tiny MCE,
  SQL-Protokollierung und ein Assistenten-Plugin mit, das SQL aus einem Prompt erzeugt.

AdminNeo ist eine einzelne Datei, die Sie auf einen Server hochladen können. **EditorNeo** ist sein Begleiter, der
Endbenutzern das Bearbeiten von Daten ohne Zugriff auf Struktur oder SQL bietet.

**Unterstützte Datenbanken** (README): MySQL, MariaDB, PostgreSQL, MS SQL, SQLite, Oracle, MongoDB, SimpleDB,
Elasticsearch (Beta) und ClickHouse (Alpha).

**Anforderungen:** PHP 5.4 oder neuer mit Sitzungen, um die kompilierte Datei auszuführen, PHP 7.1 oder neuer, um es aus
dem Quellcode auszuführen; das Composer-Paket nennt PHP 7.1 bis 8.5. Die Erweiterung OpenSSL wird empfohlen, damit
gespeicherte Anmeldeinformationen verschlüsselt werden.

## Es für sich verwenden (optional)

Ohne die Extension können Sie AdminNeo weiterhin direkt verwenden: Bauen Sie die einzelne Datei und laden Sie sie hoch.

```bash
git clone git@github.com:se7enxweb/adminneo.git
cd adminneo
php bin/compile.php            # writes the single-file version (see the Makefile for targets)
```

Laden Sie die kompilierte Datei an einen geschützten Ort hoch (HTTP-Authentifizierung oder eine Netzwerkbeschränkung
davor) und öffnen Sie sie in einem Browser. Lassen Sie eine Datenbankverwaltung nie für anonyme Besucher erreichbar. Das
Verzeichnis `examples` des Repositorys enthält Beispiele für Anpassungen und Plugins; das Verzeichnis `plugins` führt die
oben genannten Plugins auf.

## Höhepunkte der Releases seit Dezember 2023

| Release | Datum | Höhepunkte (aus dem Changelog des Projekts) |
|---|---|---|
| 5.0.0 | 29.05.2025 | Erstes Release von AdminNeo und EditorNeo als eigenständige Produkte: neues responsives Theme mit dunklem Modus und Farbvarianten, einfache Konfiguration, überarbeitete Plugins und Anpassungen. Version 5 bricht die Abwärtskompatibilität mit älteren, auf Adminer beruhenden Anpassungen; lesen Sie vor der Aktualisierung die Upgrade-Anleitung des Projekts. |
| 5.1.0 | 31.08.2025 | Neue Einstellungsseite mit grundlegenden Optionen der Oberfläche; Autovervollständigung im Editor für SQL-Befehle; Konfigurationsparameter `defaultServer` und `defaultDatabase`; generierte Spalten (MySQL, SQLite); berechnete Spalten (MS SQL); Unterstützung für CockroachDB; Indizes auf materialisierten Sichten (PostgreSQL); der Editor zeigt Datenbanksichten; Installation über Composer. Viele Fehlerkorrekturen, zum Beispiel am MongoDB-Treiber und an der Sortierung nach `COUNT(*)`. |
| 5.1.1 | 01.09.2025 | Korrektur am Compiler: Skript der Autovervollständigung fehlte in einer Datei mit einem einzigen Treiber. |
| 5.2.0 | 02.11.2025 | Konfigurationsparameter `sqlAutocompletion` und `relationLinks`; Links auf verweisende Tabellen; Beziehungslinks für PostgreSQL und MS SQL; Prozeduren in PostgreSQL (11+); URL-Parameter `?ext=pdo`, um PDO zu erzwingen; Korrektur der Maskierung von Zugangsdaten für webbasierte Treiber; ein Plugin, das SQL aus einem KI-Prompt erzeugt (braucht einen API-Schlüssel, den Sie bereitstellen). |
| 5.2.1 | 07.12.2025 | PostgreSQL: Verbindung über den Standard-Socket, wenn kein Server angegeben ist; Korrektur des JSON-Dump-Formats; der CSV-Export öffnet sich wieder im Browserfenster; Korrektur der Maskierung von Cookies. |
| 5.3 (main, unveröffentlicht) | 2026-03 | Erklärt die Kompatibilität mit PHP 8.5; sicherere Zufallswerte und 256 Bit Entropie in allen Zufallszeichenketten; Unterstützung neuer Systemtabellen im Plugin für Fremdschlüssel; Korrektur des Blätterns in der Auswahltabelle (Issue #181). |

Die monatliche Liste aller 1.121 Änderungen, einschließlich der kleineren Korrekturen an Oberfläche und Treibern, steht in
der [Historie des Repositorys](../../geschichte/oekosystem/adminneo.md#upstream-geschichte-im-fork-nach-monat).

## Grenzen

- Eine Datenbankverwaltung gibt vollen Zugriff auf die Daten, bei denen sie sich anmelden kann. Beschränken Sie sie nach
  Rolle, Netzwerk und Disziplin bei Sicherungen; die Extension fügt eine Sicherheitsschranke hinzu, die nach einer
  Bestätigung der Sicherung fragt.
- Das KI-SQL-Plugin sendet Ihren Prompt an einen Drittanbieterdienst, wenn Sie es einschalten. Es ist aus, solange Sie
  es nicht konfigurieren.
- Funktionen der unveröffentlichten Linie 5.3 können sich vor einem Tag ändern.

## Verwandte Seiten

- [sevenx_dse](erweiterungen/sevenx_dse.md)
- Plattformfunktionen: [Administrationsoberfläche](plattform-admin-ui-fork.md), [DXP-Skeleton](plattform-dxp-skeleton.md), [Layouts auf der Plattform](plattform-layouts-core-fork.md), [Nexus-Starter](plattform-nexus-starter.md), [Framework-Forks für PHP 8.5](plattform-php85-framework-forks.md), [Site-Bundles](plattform-site-bundles.md), [SQLite für Exponential Platform](plattform-sqlite-installation.md), [Legacy Bridge](legacy-bridge.md)
- Spezifikationen: [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md), [Paketkarte der Plattform](../../spezifikationen/6.0/platform-paketzuordnung.md), [SQLite-Installer der Plattform](../../spezifikationen/6.0/platform-sqlite-installer.md), [Bundle der Legacy Bridge](../../spezifikationen/6.0/legacy-bridge-bundle.md)
- Hinweise zur Aktualisierung: [Paket-Forks und Umbenennungen von Befehlen](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- Changelog: [Changelog der Plattform](../../../changelogs/extensions/exponential-platform.md)
- Chronik: [Übersicht über das Ökosystem](../../geschichte/oekosystem.md), [Monate des Ökosystems](../../geschichte/oekosystem/monate/2026-04.md), [Änderungsverzeichnis](../../geschichte/aenderungsverzeichnis/README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
