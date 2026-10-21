---
title: Exponential 6 und Exponential Platform Legacy
audience: admin, developer, project-manager
category: install-upgrade
order: 20
summary: Eine Exponential-6-Site in Exponential Platform Legacy (v2.5.0.x, v3.3.44.x, v4.6.23.x, v5.0.x) überführen, wo sich der Legacy-Kernel und ein Symfony-Stack eine Datenbank teilen, und zurück zu Exponential 6 allein – Ziellinie, Datenbankschritte, Konfiguration, Prüfung, Rückweg und Checkliste.
keywords: Exponential Platform Legacy, Legacy-Bridge, Hybrid, zwei Kernel, Migration, Übersetzer, legacy bridge, hybrid, dual kernel, 2.5, 3.3, 4.6, v5, ezpublish_legacy, legacy_mode, Symfony, migration, translator
type: install
---
# Exponential 6 und Exponential Platform Legacy

**Exponential Platform Legacy** (`se7enxweb/exponential-platform-legacy`) betreibt den Kernel von Exponential 6 und eine
Symfony-Plattform auf einer Datenbank, verbunden durch `se7enxweb/legacy-bridge`. Es ist der schonendste Weg, einer
Exponential-6-Site den Symfony-Stack zu geben: Die Inhalte, die Legacy-Administration, die TPL-Templates und die
Extensions arbeiten weiter, während Twig-Templates, REST v2 und die Plattform-Administration hinzukommen. Und weil auf
der Legacy-Seite nichts entfernt wird, ist der Rückweg kurz.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · Das eigene Handbuch des Produkts:
[Exponential Platform Legacy, Kapitel 11: Migration nach Exponential Platform Legacy](https://github.com/se7enxweb/exponential-platform-legacy/blob/5.x/doc/book/11-migrating-into.md)

## Inhalt

- [1. Die Release-Linien](#1-die-release-linien)
- [2. Ist es das richtige Ziel?](#2-ist-es-das-richtige-ziel)
- [3. Von Exponential 6 zu Platform Legacy](#3-von-exponential-6-zu-platform-legacy)
- [4. Wechsel auf eine neuere Linie](#4-wechsel-auf-eine-neuere-linie)
- [5. Von Platform Legacy zurück zu Exponential 6 allein](#5-von-platform-legacy-zurück-zu-exponential-6-allein)
- [6. Prüfen](#6-prüfen)
- [7. Rückweg](#7-rückweg)
- [8. Risiken](#8-risiken)
- [9. Checkliste](#9-checkliste)

## 1. Die Release-Linien

Ausgelesen aus der `composer.json` jedes Release-Tags am 2026-10-18 ([Übersicht, 1.2](README.md#12-die-release-linien-von-nexus-und-platform-legacy)):

| Linie | Neuestes Release | Generation | Symfony | PHP | Bridge |
|---|---|---|---|---|---|
| `v2.5.0.x` | `v2.5.0.4` | Fork des Kernels von eZ Platform 2.5 (`~7.5.33`) | 3.4 | `^7.1.3 \|\| ^8.1 \|\| ^8.2` | `^2.1`, mit `se7enxweb/exponential ^6.0.12` |
| `v3.3.44.x` | `v3.3.44.8` | 3.3 (`se7enxweb/oss ~3.3.0.0`) | 5.4 | `^8.0` | `^3.0.0.35` |
| `v4.6.23.x` | `v4.6.23.3` | 4.6 (`exponential-platform-dxp 4.6.x-LB-dev`) | 5.4 | `^7.4` bis `^8.5` | `^4.0.0.0` |
| `v5.0.x` | `v5.0.3.1` | v5 (`exponential-platform-dxp dev-5.x-LB`) | 7.4 | `>=8.3` | `^5.0.0.0` |

> **Warnung**
> `v5.0.3` enthält die `composer.json` der Linie 2.5. Für v5 fordern Sie `~5.0.3.1` an (so macht es auch die README des
> Produkts: `composer create-project se7enxweb/exponential-platform-legacy:~5.0.3.1`), niemals ein bloßes `^5.0`.

Eine sofort lauffähige Variante der Linie v5 ist `se7enxweb/exponential-platform-legacy-starter` (`1.0.0.0`).

## 2. Ist es das richtige Ziel?

| Sie möchten | Ist Platform Legacy richtig? |
|---|---|
| Symfony-Controller, Twig, REST v2, GraphQL **neben** der Legacy-Administration und den TPL-Seiten | Ja |
| Die öffentliche Site Seite für Seite auf Twig portieren, ohne einen Komplettumstieg auf einen Schlag | Ja |
| Nur den Legacy-Kernel, ausgeliefert von Velocity oder PHP-FPM | Nein: Bleiben Sie bei Exponential 6 allein. Das Handbuch des Produkts sagt, der Hybrid „lohnt sich nur, wenn Sie Symfony-Controller, REST v2, GraphQL oder die Plattform-Administration wollen“ |
| Einen visuellen Seitenbaukasten auf der Symfony-Seite | Sehen Sie sich [Nexus](exponential-platform-nexus.md) an, das Netgen Layouts mitbringt |
| Ein reines Symfony-Projekt ohne den Legacy-Kernel | [DXP](exponential-platform-dxp.md) |

Was sich für die Site ändert, wenn der Symfony-Stack sie ausliefert:

- **Symfony beantwortet die Anfragen**; die Legacy-Siteaccesses werden über die Bridge erreicht (`legacy_mode: true`).
  Velocity, das Exponential 6 allein ausliefert, ist nicht der Server eines Platform-Legacy-Projekts.
- **Die Zugangsdaten der Datenbank stehen nur in der Konfiguration von Symfony**; die Bridge reicht sie an den
  Legacy-Kernel weiter.
- **Bild-Aliase kommen aus den YAML-Bildvarianten**; Aliase, die nur in `image.ini` stehen, müssen dort deklariert werden.
- **Die Anmeldung an Nicht-Legacy-Siteaccesses übernimmt Symfony.** Beide Kernel lesen die Legacy-Passwort-Hashes,
  aber Plattform-Generationen ab 3.0 akzeptieren nur die Typen 6 und 7 ([17.7](../17-migrationsreferenz.md#177-passwörter-url-aliase-und-bildspeicherung)).
- **Datenbank-Engines**: Die Plattformseite unterstützt MySQL/MariaDB und PostgreSQL; SQLite wird im Schnellstart von
  v5 für die Entwicklung angeboten. MongoDB und Oracle, die Exponential 6 unterstützt, sind keine Engines der
  Plattform: Ziehen Sie eine solche Site zuerst auf MySQL, MariaDB oder PostgreSQL um ([Datenbanken](../09-datenbanken.md)).

## 3. Von Exponential 6 zu Platform Legacy

Eine Exponential-6-Datenbank ist eine Legacy-Datenbank, die den Schritt 5.4 der alten Kette bereits hinter sich hat;
Sie folgen also Kapitel 11.3 des Produkts ohne dessen Legacy-Update-Kette.

**Landen Sie zuerst auf der Linie 2.5.** Das Handbuch des Produkts: Das Plattformschema von 2.5 „ist das vollständige
Legacy-Schema“, es fehlt also nichts, was die Legacy-Site verwendet; steigen Sie danach Linie für Linie auf
([4](#4-wechsel-auf-eine-neuere-linie)).

1. **Frieren Sie ein und sichern Sie** die Datenbank und `var/<site>/storage`. Spielen Sie die Sicherung einmal auf
   einem Testrechner zurück.
2. **Bringen Sie Exponential 6 auf sein neuestes Release** ([Kapitel 11.6](../11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand))
   und prüfen Sie die Versionszeilen ([11.2](../11-aktualisieren.md#112-welche-version-betreiben-sie)).
3. **Stellen Sie unter MySQL die Tabellen auf `utf8mb4` um.** Der Plattform-Kernel 2.5 legt `utf8mb4`-Tabellen an,
   und das Legacy-Override der Distribution spricht `utf8mb4` mit dem Server; Kapitel 11.3 des Produkts enthält die
   Abfrage, die die Anweisungen `ALTER TABLE ... CONVERT TO CHARACTER SET utf8mb4` zum Prüfen und Ausführen ausgibt.
4. **Wenden Sie die Update-Dateien des Plattform-Kernels 2.5 an**, aus
   `vendor/se7enxweb/ezpublish-kernel/data/update/<engine>/`, in der Reihenfolge, die Kapitel 11.3, Schritt 3, des
   Produkts angibt. Auf einer Exponential-6-Datenbank ist das meiste, was sie tun (das breitere `password_hash`,
   `ezcontentobject_trash.trashed`, die Sequenznamen unter PostgreSQL), bereits vorhanden; Kapitel 10.7 des Produkts
   erklärt, welche Anweisungen gefahrlos wiederholt werden können. Führen Sie sie auf der Kopie aus und lesen Sie
   jeden Fehler: Ein „duplicate column“ bedeutet, dass die Änderung bereits vorhanden ist; alles andere stoppt die
   Migration.
5. **Legen Sie die Tabellen an, die der Kernel 2.5 erwartet** und die einer reinen Legacy-Datenbank fehlen (zum
   Beispiel `ezcontentclass_attribute_ml`, `eznotification`): Vergleichen Sie Ihre Tabellenliste mit
   `data/mysql/schema.sql` des installierten `se7enxweb/ezpublish-kernel` und legen Sie die fehlenden aus dieser Datei
   an ([Kapitel 16.6.1](../16-migration-von-ez-platform-und-ibexa.md#1661-was-beide-gemeinsam-haben) enthält den Vergleich).
6. **Legen Sie das 2.5-Projekt** mit einer ausdrücklich angegebenen Version an und richten Sie es auf die migrierte
   Datenbank aus, **ohne den Installer auszuführen**: Das Handbuch des Produkts warnt, dass `ezplatform:install` /
   `exponential:install` die Tabellen, die sie kennen, löschen und neu anlegen.
7. **Verschieben Sie den Speicher** nach `ezpublish_legacy/var/<site>/storage` (oder in das Verzeichnis, das Ihr
   `VarDir` nennt).
8. **Verschieben Sie die Legacy-Konfiguration**: `settings/override/` nach `ezpublish_legacy/settings/override/`, die
   Siteaccess-Verzeichnisse nach `ezpublish_legacy/settings/siteaccess/`, und entfernen Sie die Verbindungswerte aus
   `[DatabaseSettings]`.
9. **Deklarieren Sie die Siteaccesses in YAML** mit denselben Namen; die Legacy-Siteaccesses erhalten
   `legacy_mode: true`. Deklarieren Sie jeden Bild-Alias, den die Legacy-Designs verwenden, als Bildvariante.
10. **Verschieben Sie Ihre Extensions** in das Projekt und aktivieren Sie sie; erzeugen Sie die Autoloads über die
    Bridge neu (`php bin/console ezpublish:legacy:script bin/php/ezpgenerateautoloads.php`).
11. **Prüfen Sie das Schema** mit `ezsqldiff.php`, zuerst das Referenzschema und dann Ihre Datenbank (Kapitel 11.3,
    Schritt 10, des Produkts und [das Nexus-Handbuch, 12.4](https://github.com/se7enxweb/exponential-platform-nexus/blob/1.3.0.x/doc/book/12-migrating-into.md#124-from-ez-publish-4x-and-exponential-6)
    erklären die Reihenfolge der Argumente). `DROP`-Zeilen für Tabellen, die nur die Plattform hat, sind zu erwarten.
12. **Lassen Sie den Legacy-Cron über die Bridge laufen**, leeren Sie die Caches beider Seiten, indizieren Sie neu und
    prüfen Sie ([6](#6-prüfen)).

Was nicht umziehen muss: Die Inhalte, die Klassen, die Benutzer, Rollen, Sektionen, Zustände, URL-Aliase und die Daten
von Exponential Layouts bleiben in der gemeinsamen Datenbank und werden von der Legacy-Seite wie bisher gelesen.

## 4. Wechsel auf eine neuere Linie

Wechseln Sie jeweils um eine Linie, mit Kapitel 10 des Produkts
([Upgrade zwischen Release-Linien](https://github.com/se7enxweb/exponential-platform-legacy/blob/5.x/doc/book/10-upgrading-between-lines.md)):

| Schritt | Datenbank | Hinweise aus dem Handbuch des Produkts |
|---|---|---|
| 2.5 auf 3.3 | `upgrade/db/<engine>/ezplatform-2.5.latest-to-3.0.0.sql`, `ezplatform-3.2.0-to-3.3.0.sql`, `ezplatform-3.2.3-to-3.2.4.sql` (im Branch `master` von `se7enxweb/exponential-platform`) | keine der Dateien löscht eine Tabelle, die reinen Legacy-Tabellen bleiben also erhalten; die erste Datei löscht die Zeile `ezpublish-version`, die für die Legacy-Update-Dateien wiederhergestellt werden muss. Für SQLite gibt es keine Upstream-Dateien |
| 3.3 auf 4.6, 4.6 auf v5 | Kapitel 10.6 des Produkts | unter 4.6 zuerst die reinen Legacy-Tabellen anlegen; bei einer v5-Datenbank muss die Übersetzer-Extension (`se7enxweb/sevenx_exponential_platform_v5_database_translator`) der erste Eintrag von `ActiveExtensions[]` sein, weil v5 die Kerntabellen in `ibexa_*` umbenennt |

Jeder Linienwechsel ändert auch Projektstruktur und Konfiguration (zum Beispiel `app/config` zu `config/`, `web/` zu
`public/` von 2.5 auf 3.x); das Kapitel des Produkts führt diese Änderungen auf.

## 5. Von Platform Legacy zurück zu Exponential 6 allein

Die Legacy-Hälfte ist eine vollständige Exponential-6-Installation; zurückzugehen heißt, sie die Site wieder
ausliefern zu lassen.

1. **Sichern Sie** die gemeinsame Datenbank und den Speicher.
2. **Entscheiden Sie, was mit reinen Symfony-Inhalten geschieht.** RichText-Felder, Netgen Layouts oder andere reine
   Plattformdaten haben keine eigene Darstellung auf der Legacy-Seite: Listen Sie die verwendeten Feldtypen auf und
   prüfen Sie sie anhand von [Kapitel 16.6.3](../16-migration-von-ez-platform-und-ibexa.md#1663-feldtypen).
3. **Bei einer v5-Datenbank** machen Sie die Umbenennungen in `ibexa_*` rückgängig (oder lassen die
   Übersetzer-Extension aktiv) ([Kapitel 16.6.4](../16-migration-von-ez-platform-und-ibexa.md#1664-die-datenbank-schritt-für-schritt), Schritt 2).
4. **Prüfen Sie das Schema** mit `ezsqldiff.php` gegen Exponential 6 (Referenz zuerst) und ergänzen Sie die fehlenden
   Tabellen und Spalten; wenden Sie danach die aktuellen Dateien aus `update/database/<engine>/6.0/` so an, wie
   [Kapitel 11.6](../11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand) es beschreibt.
5. **Installieren Sie Exponential 6** (empfohlen ist ein Git-Klon mit Historie, [Kapitel 3](../03-den-code-beschaffen.md)),
   kopieren Sie `ezpublish_legacy/settings/override/`, `settings/siteaccess/`, die Extensions und den Speicher hinein
   und tragen Sie die Datenbankverbindung wieder in `settings/override/site.ini.append.php` ein.
6. **Liefern Sie sie aus** mit Velocity oder Apache/nginx mit PHP-FPM ([Kapitel 8](../08-die-website-ausliefern.md)); die
   Adressen der Legacy-Siteaccesses bleiben gleich; Adressen, die über Symfony-Routen ausgeliefert wurden (REST v2,
   reine Twig-Seiten), brauchen Weiterleitungen oder einen Ersatz.
7. **Passwörter**: Benutzer, die sich auf der Symfony-Seite angemeldet haben, haben Hashes vom Typ 7, die
   Exponential 6 lesen kann.

## 6. Prüfen

- Anzahl der Objekte pro Klasse und Sprache, der Benutzer und der URL-Aliase vorher und nachher.
- Die Startseite, eine Seite pro Klasse, beide Administrationen: In jeder ein Element bearbeiten und veröffentlichen.
- Eine Bildvariante, ein Upload, die Suche, die Legacy-Cronjob-Teile von Hand ausgeführt.
- `var/log` von Symfony und `ezpublish_legacy/var/log/error.log` sind frei von Fehlern.
- Das Handbuch des Produkts enthält die Befehle pro Linie (Kapitel 10.8).

## 7. Rückweg

Die Update-Dateien der Plattform ändern Zeilen und fügen Tabellen hinzu, lassen sich aber nicht durch eine weitere
SQL-Datei rückgängig machen. Der Rückweg ist die Sicherung aus Schritt 1 plus die alte Exponential-6-Installation, die
unangetastet bleibt, bis die neue eine Weile im Produktivbetrieb gelaufen ist.

## 8. Risiken

| Risiko | Gegenmaßnahme |
|---|---|
| Der Installer wird gegen die migrierte Datenbank ausgeführt | niemals ausführen; das Handbuch des Produkts wiederholt diese Warnung in jedem Kapitel |
| Zwei Konfigurationen laufen auseinander | Datenbankeinstellungen nur in Symfony; eine einzige Liste der Siteaccess-Namen |
| Bild-Aliase fehlen auf der Symfony-Seite | vor der ersten Anfrage als Varianten deklarieren |
| Site auf MongoDB oder Oracle | zuerst auf MySQL, MariaDB oder PostgreSQL umziehen |
| Alte Passwort-Hash-Typen | Benutzer vor dem Wechsel auf 3.3 oder später um ein Zurücksetzen bitten |
| Eine v5-Datenbank, die ein Legacy-Kernel ohne den Übersetzer liest | Übersetzer an erster Stelle in `ActiveExtensions[]` |

## 9. Checkliste

- [ ] Linie gewählt (auf 2.5 landen, später aufsteigen); Version ausdrücklich angegeben.
- [ ] Exponential 6 auf seinem neuesten Release; Sicherung einmal zurückgespielt.
- [ ] MySQL-Tabellen auf `utf8mb4`; Update-Dateien des Kernels 2.5 angewendet und ihre Fehler gelesen.
- [ ] Fehlende 2.5-Tabellen angelegt; `ezsqldiff.php` (Referenz zuerst) zeigt keine fehlende Tabelle oder Spalte.
- [ ] Projekt angelegt, Installer **nicht** auf den Daten ausgeführt; Speicher verschoben.
- [ ] Legacy-Einstellungen verschoben, Verbindungswerte entfernt; Siteaccesses in YAML mit `legacy_mode`.
- [ ] Bild-Aliase als Varianten deklariert; Extensions verschoben und Autoloads neu erzeugt.
- [ ] Caches auf beiden Seiten geleert, Suche neu indiziert, Cron über die Bridge.
- [ ] Frontend und beide Administrationen geprüft; Plan für den Rückweg abgestimmt.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../README-LIZENZ.md)).
