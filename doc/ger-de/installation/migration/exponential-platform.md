---
title: Exponential 6 und Exponential Platform (nur Symfony)
audience: admin, developer, project-manager
category: install-upgrade
order: 23
summary: Umzüge zwischen Exponential 6 und Exponential Platform 3.2.9, der reinen Symfony-Distribution auf Basis des letzten freien eZ Platform 3.2 – wann sie das richtige Ziel ist, der Weg über die Generation 2.5 und die Datenbankdateien von 2.5 auf 3.0, Templates und Code, der Rückweg, Prüfung, Zurückrollen und Checkliste.
keywords: Exponential Platform, 3.2.9, Symfony 5, ezplatform-kernel, nur Symfony, ohne Legacy, Migration, Symfony only, no legacy, upgrade/db, ezplatform-2.5.latest-to-3.0.0.sql, migration
type: install
---
# Exponential 6 und Exponential Platform (nur Symfony)

**Exponential Platform** (`se7enxweb/exponential-platform`, Standard-Branch `3.2`) ist die reine Symfony-Distribution,
nach eigenen Worten „based on the last free versions of eZ Platform“ (auf Basis der letzten freien Versionen von eZ
Platform), mit „No Exponential 6.x Legacy Support“ (ohne Unterstützung für Exponential 6.x Legacy). Ihr einziges
GitHub-Release ist `v3.2.9` (2025-09-28). Ihre `composer.json` setzt die Forks der Generation 3.2 voraus
(`se7enxweb/ezplatform-kernel ~1.3.42`, `se7enxweb/ezplatform-admin-ui ~2.3.39`, REST, GraphQL, die Feldtypen RichText, Matrix und
Query, Solr), dazu Symfony 5 und PHP `^7.3 || ^8.1` bis `^8.4`.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · Installationshinweise im Repository des Produkts: `doc/eng-us/INSTALL.md`

## Inhalt

- [1. Ist es das richtige Ziel?](#1-ist-es-das-richtige-ziel)
- [2. Von Exponential 6 zu Exponential Platform 3.2](#2-von-exponential-6-zu-exponential-platform-32)
- [3. Von Exponential Platform 3.2 weiter oder zurück](#3-von-exponential-platform-32-weiter-oder-zurück)
- [4. Prüfen, Rückweg, Checkliste](#4-prüfen-rückweg-checkliste)

## 1. Ist es das richtige Ziel?

Die Generation 3.2 ist älter als die Generation 3.3, die die anderen Exponential-Produkte mitbringen (Platform Legacy
`v3.3.44.x`, Nexus `1.1.0.x`, OSS `v3.3.0.x`). Für neue Arbeiten ist Folgendes vorzuziehen:

| Sie möchten | Besseres Ziel |
|---|---|
| Nur Symfony, aktuelles PHP und Symfony | [DXP 2.x](exponential-platform-dxp.md) (v5, Symfony 7.4) oder DXP 1.x (4.6) |
| Symfony plus den Legacy-Kernel | [Platform Legacy](exponential-platform-legacy.md) |
| Eine Site mit einem Seitenbaukasten | [Nexus](exponential-platform-nexus.md) |

Wählen Sie Exponential Platform 3.2.9, wenn ein bestehendes Projekt bereits darauf oder auf eZ Platform 3.0 bis 3.2 läuft und Sie
diese Generation beibehalten möchten.

## 2. Von Exponential 6 zu Exponential Platform 3.2

Die Inhaltstabellen sind gemeinsam, aber das 3.x-Schema hat die reinen Legacy-Tabellen entfernt, und die Plattform liest weder XmlText
noch die alten Passworttypen. Der Weg:

1. **Kommen Sie auf der Generation 2.5 an**, mit dem Legacy-Kernel daneben:
   [Platform Legacy `v2.5.0.x`, Abschnitt 3](exponential-platform-legacy.md#3-von-exponential-6-zu-platform-legacy).
2. **Wandeln Sie dort XmlText in RichText um** (`ezxmltext:convert-to-richtext`, [DXP-Seite, Schritt 2](exponential-platform-dxp.md#2-von-exponential-6-zu-dxp)).
3. **Sorgen Sie dafür, dass jeder aktive Benutzer den Hash-Typ 6 oder 7 hat** (Zurücksetzen oder eine Anmeldung am Legacy-Kernel, der den Hash dabei neu erzeugt).
4. **Spielen Sie die Datei von 2.5 auf 3.0** dieses Produkts ein: `upgrade/db/<engine>/ezplatform-2.5.latest-to-3.0.0.sql` (MySQL
   und PostgreSQL, Branch `3.2`). Das Handbuch von Platform Legacy beschreibt, was sie tut: Sie entfernt die Zeilen
   `ezpublish-version` und `ezplatform-release` und fügt `ezplatform-release` `3.0.0` ein, verbreitert
   `ezcontentclass_attribute.data_text1`, fügt `is_thumbnail` hinzu, fügt `ezkeyword_attribute_link.version` hinzu und befüllt es.
   Für 3.0 auf 3.1 oder 3.1 auf 3.2 gibt es keine Dateien. Eine SQLite-Datei gibt es nicht.
5. **Legen Sie das 3.2.9-Projekt an** (`composer create-project se7enxweb/exponential-platform:v3.2.9` oder ein Git-Klon des
   Tags), richten Sie `.env.local` (`DATABASE_URL`) auf die migrierte Datenbank aus und führen Sie den Installer darauf **nicht** aus.
6. **Verschieben Sie den Speicher** in das `var_dir`/`storage_dir` der Plattform unterhalb des Web-Roots, damit die im
   Bild-XML und in `ezimagefile` gespeicherten Pfade weiterhin auf Dateien zeigen; die Pfadschemata beider Seiten und die Prüfungen stehen in
   [17.7](../17-migrationsreferenz.md#177-passwörter-url-aliase-und-bildspeicherung) und 17.8. Konfigurieren Sie die
   Bildvarianten, bauen Sie die URL-Aliase und den Suchindex neu auf.
7. **Schreiben Sie die Darstellung neu**: TPL zu Twig, INI zu YAML, Extensions zu Bundles
   ([17.4](../17-migrationsreferenz.md#174-einstellungen-ini-und-yaml), [17.5](../17-migrationsreferenz.md#175-templates-tpl-und-twig)).
8. **Archivieren Sie die reinen Legacy-Tabellen**, die Sie nicht mehr nutzen.

Die Seiten des Herstellers zum Schritt von 2.5 auf 3.x sind aufgeführt in
[Kapitel 16.3](../16-migration-von-ez-platform-und-ibexa.md#163-die-seiten-des-herstellers-und-wo-dieses-kapitel-sie-abdeckt).

## 3. Von Exponential Platform 3.2 weiter oder zurück

| Richtung | Weg |
|---|---|
| 3.2 zu 3.3 | `upgrade/db/<engine>/ezplatform-3.2.0-to-3.3.0.sql` (legt `ibexa_setting` an) und `ezplatform-3.2.3-to-3.2.4.sql`, im Branch `master`; dann Platform Legacy `v3.3.44.x`, Nexus `1.1.0.x` oder OSS `v3.3.0.x` |
| 3.2 zu DXP | zuerst 3.3, dann der [DXP-Weg](exponential-platform-dxp.md#2-von-exponential-6-zu-dxp) ab Schritt 5 |
| 3.2 zurück zu Exponential 6 | „Weg B“ aus [Kapitel 16.6](../16-migration-von-ez-platform-und-ibexa.md#166-weg-b-hinunter-zum-legacy-kernel-von-exponential-60): `ezsqldiff.php` (zuerst die Referenz) legt die reinen Legacy-Tabellen neu an, die Versionszeilen werden gesetzt, RichText wird gerendert oder umgewandelt (16.6.3), YAML wird zu INI und Twig zu TPL; die Zeile „3.0 bis 3.3“ in 16.6.9 nennt die Einzelheiten, einschließlich des Standardwerts, den `ezkeyword_attribute_link.version` für die Inserts des Legacy-Kernels braucht |
| 3.2 neben dem Legacy-Kernel | auf 3.3 ankommen und Platform Legacy `v3.3.44.x` verwenden |

## 4. Prüfen, Rückweg, Checkliste

Prüfen Sie mit den Anzahlen pro Inhaltstyp und Sprache, der Administration, einer Bearbeitung mit Veröffentlichung, den Bildvarianten, der Suche und
`doctrine:schema:validate`. Gehen Sie mit der vor jedem Schritt erstellten Sicherung zurück; behalten Sie die alte Installation, bis die
neue im Produktivbetrieb gelaufen ist.

- [ ] 3.2.9 als richtiges Ziel bestätigt (oder eine neuere Generation gewählt).
- [ ] Generation 2.5 über Platform Legacy erreicht; XmlText umgewandelt; Hashes 6/7.
- [ ] `ezplatform-2.5.latest-to-3.0.0.sql` an einer Kopie eingespielt; Fehler gelesen.
- [ ] Projekt aus `v3.2.9`; Installer nicht auf die Daten angewendet; Speicher verschoben; Aliase und Index neu aufgebaut.
- [ ] Templates, Konfiguration und Code neu geschrieben; reine Legacy-Tabellen archiviert.
- [ ] Weiterleitungen für jede Adresse, die sich geändert hat.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../README-LIZENZ.md)).
