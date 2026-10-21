---
title: "Der share-Ordner: Demobilder, Dateien und Startdaten"
audience: designer, developer
category: theme-extensions
order: 220
summary: Wofür der Ordner share/ eines Themes da ist - Demobilder und -dateien, Datenbankschema und Startdaten (.dba) -, wie echte Themes ihn nutzen und die Regeln, die ihn klein, rechtlich sauber und frei von Sicherungen und personenbezogenen Daten halten.
keywords: share, share-Ordner, db_schema.dba, db_data.dba, content-images, content-files, Startdaten, Demodaten, Installer, share folder, seed data, demo data, installer
type: help
---
# Der share-Ordner: Demobilder, Dateien und Startdaten

Diese Seite ist für Designer, deren Theme mit Demo-Inhalten kommt oder Datenbanktabellen braucht. `share/` ist der Ort
für Daten, die einmal installiert statt bei jeder Anfrage gelesen werden.

## Was echte Themes dort aufbewahren

| Theme | `share/` enthält |
|---|---|
| `cjw_themes_jumper` | `content-images/`, `content-files/` (Bilder und Dateien seiner Demo-Inhalte) |
| `sevenx_themes_media` | `db_schema.dba` und `db_data.dba` (die Tabellen und Zeilen seiner Layouts) |

Eine `.dba`-Datei ist das datenbankunabhängige Format des Kernels: Ein Installer macht aus `db_schema.dba` Tabellen für
die verwendete Datenbank (SQLite, PostgreSQL, MySQL/MariaDB und andere) und lädt `db_data.dba` hinein.

## Schritt für Schritt

1. Entscheiden Sie, was das Theme installiert braucht: Bilder für Demoseiten, Beispieldokumente, zusätzliche Tabellen.
2. Legen Sie Bilder und Dateien unter `share/content-images/` und `share/content-files/` ab (oder unter eigenen Namen,
   die Sie in `INSTALL.md` dokumentieren).
3. Für Tabellen lassen Sie einen RAD-Assistenten `share/db_schema.dba` schreiben, wenn er es anbietet, oder erzeugen es
   mit den Schema-Werkzeugen des Kernels aus einer Datenbank. Halten Sie Schema und Daten in getrennten Dateien.
4. Erklären Sie in `INSTALL.md`, wie die Daten installiert werden und was sie anlegen.
5. Listen Sie vor jedem Release auf, was versioniert ist: `git ls-files share/`.

## Regeln

- **Nur Dateien, die Sie verbreiten dürfen.** Demofotos brauchen eine Lizenz, die die Weitergabe erlaubt
  ([Creative Commons für Bilder](../veroeffentlichung/creative-commons-fuer-bilder.md)).
- **Keine personenbezogenen Daten.** Startdaten enthalten nie echte Kunden, Benutzer, E-Mail-Adressen oder Passwörter.
- **Keine Sicherungen.** Kopien wie `db_data.dba.bak` gehören in Ihre eigene Sicherung, nicht ins Repository; tragen Sie
  sie in die `.gitignore` ein ([Eine .gitignore für Themes](gitignore-fuer-themes.md)).
- **Halten Sie ihn klein.** Ein Theme, das jede Installation herunterlädt, sollte keine Hunderte Megabyte an Bildern
  tragen.
- **Prüfen Sie Datenänderungen Zeile für Zeile**, nicht per Diff: Eine `.dba`-Datei ist ein Array, und das Entfernen
  einer Zeile nummeriert alle folgenden Zeilen neu.

## Verwandte Seiten

- [Demo-Inhalte für ein Theme](../veroeffentlichung/demo-inhalte.md)
- [Inhaltsklassen-Pakete mitliefern](inhaltsklassen-pakete-beilegen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
