---
title: Massendatenaustausch mit xrowextract
audience: integrator, admin, developer
category: integration
order: 140
summary: Die Erweiterung xrowextract als dateibasierte Integration nutzen - Exporte einer oder vieler Klassen als CSV, JSON oder XML, Importe mit Probelauf, Inhaltspakete, Zeitpläne mit Delta-Läufen, Zustellung an SFTP, FTP, S3, WebDAV oder einen HTTP-Endpunkt mit signiertem Webhook und die Kommandozeile.
keywords: xrowextract, Export, Import, CSV, JSON, XML, Zeitplan, Delta, changed-since, Ziel, SFTP, FTPS, S3, WebDAV, HTTP POST, Webhook, X-Xrowextract-Signature, Voreinstellungen, Jobs, dateibasierte Integration, schedule, destination, presets, file-based integration
type: help
---
# Massendatenaustausch mit xrowextract

Diese Seite ist für Integratoren, deren Partnersystem **Dateien** austauscht: eine nächtliche Produktdatei aus einem
ERP, ein wöchentlicher Export für ein Data Warehouse, ein Hin und Her von Tabellen mit einer Agentur. Die Erweiterung
`xrowextract` (verlangt als `~2.5.18`) ist der Reiter **Export** der Administration. Sie exportiert und importiert
Inhalte als Dateien, läuft nach Zeitplan und stellt die Dateien anderen Systemen zu, ohne eine Zeile eigenen Code. Die
vollständige Funktionsseite ist [xrowextract](../../funktionen/6.0/erweiterungen/xrowextract.md), die Referenz die
[Spezifikation von xrowextract](../../spezifikationen/6.0/xrowextract.md).

## Inhalt

1. [Was sie abdeckt](#was-sie-abdeckt)
2. [Exporte](#exporte)
3. [Importe](#importe)
4. [Zeitpläne und Ziele](#zeitpläne-und-ziele)
5. [Die Kommandozeile](#die-kommandozeile)
6. [Hinweise zur Sicherheit](#hinweise-zur-sicherheit)
7. [xrowextract oder eigener Code](#xrowextract-oder-eigener-code)

## Was sie abdeckt

| Aufgabe | Wo in der Administration |
|---|---|
| Eine Klasse als CSV, JSON oder XML, unter einem Knoten oder websiteweit, mit Filtern und Sprachen | **One class of content export** (`/xrowextract/csv`) |
| Jede Klasse unter gewählten Knoten als ein ZIP- oder TAR-Archiv | **Multi class of content export** (`/xrowextract/archive`) |
| Eine CSV-, JSON- oder XML-Datei in Inhaltsobjekte, mit Spaltenzuordnung, Vorschau (Probelauf) und Anwenden | **Import content file** |
| Inhaltspakete ansehen, vergleichen, installieren und exportieren | **Import content package** (`/xrowextract/package`) |
| Exporte und Importe nach Zeitplan ausführen, vollständig oder als Delta, und die Datei zustellen | **Schedules** (Zeitpläne) |
| Läufe im Hintergrund mit Fortschritt, Protokollen und Abbrechen | **Jobs** |

## Exporte

Spalten sind die Klassenattribute plus besondere Spalten: Objekt-ID, **Remote-ID**, Hauptknoten, übergeordneter
Knoten, URL-Alias, Daten und bei Benutzern Login und E-Mail. Filter decken Datum, Sektion, Zustand, Sichtbarkeit, Name,
Eigentümer, Tiefe und jedes Klassenattribut ab, verknüpft mit und/oder. Eine gespeicherte **Voreinstellung** (Preset)
macht aus einer Auswahl einen wiederverwendbaren, benannten Export mit `{placeholders}`. Exportieren Sie die Spalte
mit der Remote-ID immer dann, wenn ein anderes System die Zeilen seinen eigenen Datensätzen zuordnen wird.

## Importe

1. Laden Sie die Datei hoch (in Stücken und fortsetzbar, beliebige Größe) und wählen Sie Klasse, übergeordneten Knoten
   und Sprache.
2. Ordnen Sie die Spalten Attributen zu; die Seite listet jede Spalte und jedes Element auf, das der Importer versteht.
3. Klicken Sie auf **Vorschau**: ein Probelauf, der jede Änderung benennt und nichts schreibt.
4. Klicken Sie auf **Anwenden**. Ein großer Import läuft als Hintergrundjob mit einem Wiederaufsetzpunkt.

Eine importierte Datei-URL (für Bilder und Dateien, auf die per Adresse verwiesen wird) muss Schema, Host und Port der
eigenen Website haben. Zeitgesteuerte Importe machen immer zuerst einen Probelauf und werden nur angewendet, wenn er
keine Fehler fand.

## Zeitpläne und Ziele

Ein **Zeitplan** führt eine Voreinstellung, ein Website-Archiv, ein **Export as package** oder einen Import aus einem
Ordner oder einem Ziel aus, stündlich, täglich, wöchentlich, monatlich oder nach einem fünfteiligen Cron-Ausdruck,
vollständig oder als **Delta** (nur Änderungen seit dem letzten erfolgreichen Lauf). Fällige Zeitpläne starten Sie mit
dem Cronjob-Teil:

```bash
php runcronjobs.php xrowextract
```

| Ziel | Hinweise |
|---|---|
| SFTP | OpenSSH-Client des Systems; Schlüssel oder Passwort; dem Host-Schlüssel wird ausdrücklich vertraut |
| FTP / FTPS | |
| Lokaler Ordner oder NAS | Nur unterhalb von `xrowextract.ini [Destinations] LocalPathRoots[]` |
| S3-kompatibler Speicher | Signaturversion 4 |
| WebDAV | |
| HTTP POST | Wahlweise ein signierter Webhook |

Ist `xrowextract.ini [Notifications] WebhookSecret` gesetzt, trägt der Webhook
`X-Xrowextract-Signature: sha256=<HMAC-SHA256 of "<timestamp>.<sha256 of the body>">`, sodass der Empfänger den
Absender prüfen kann. Benachrichtigungen gehen bei einem Fehler per E-Mail (immer an den Eigentümer), wahlweise auch
bei Erfolg, und an den Webhook. **Verlauf** (History) listet jeden Lauf mit wer, was, Zeilen, Größe, Prüfsumme,
Zustellung und Warnungen.

## Die Kommandozeile

Jede Funktion hat einen Befehl, als `php extension/xrowextract/bin/php/<name>.php` oder über die Konsole:

| Befehl | Was er tut |
|---|---|
| `ext:xrowextract:csv` | Eine Klasse als Datei (`--format`, `--where`, `--changed-since`, `--preset`, `--preview`, `--user`) |
| `ext:xrowextract:archive` | Ein Website-Archiv |
| `ext:xrowextract:import` | Ein Import beliebiger Größe, gestreamt, wahlweise als Hintergrundjob |
| `ext:xrowextract:package` | Inhaltspakete: ansehen, installieren, exportieren, vergleichen |
| `ext:xrowextract:schedule` | Zeitpläne auflisten, ausführen, aktivieren, deaktivieren; Crontab-Zeilen ausgeben |
| `ext:xrowextract:destination` | Ziele auflisten und testen, eine Datei senden, einem Host-Schlüssel vertrauen |
| `ext:xrowextract:requirements` | Was dieser Server für das Werkzeug bereitstellt |

```bash
# articles changed in the last 30 days that are not hidden, as JSON
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 --format=json \
  --visibility=visible --date-field=modified --date=30 --output=var/tmp/recent.json --allow-root-user
```

`--user=<login>` exportiert mit den Leserechten dieses Logins (Standard `admin`): Geben Sie stattdessen ein Dienstkonto
an, damit der Export nicht mehr enthalten kann, als er soll.

## Hinweise zur Sicherheit

- Zugangsdaten für Ziele werden mit libsodium verschlüsselt; die Schlüsseldatei wird mit Modus 0600 unter
  `settings/override/xrowextract-secrets.key` erzeugt. Checken Sie sie nie ein; geht sie verloren, müssen die
  Geheimnisse neu eingegeben werden.
- Exporte können personenbezogene Daten enthalten (Benutzerspalten, gesammelte Antworten): Stellen Sie sie nur an Ziele
  zu, die solche Daten halten dürfen, und legen Sie auf der empfangenden Seite eine Aufbewahrungsfrist fest.
- Beschränken Sie die Richtlinien von `xrowextract` auf die Personen, die den Austausch betreiben; lesen Sie sie im
  Abschnitt **Policies** der Funktionsseite nach.

## xrowextract oder eigener Code

| Wählen Sie `xrowextract`, wenn | Schreiben Sie einen eigenen Import, wenn |
|---|---|
| Der Partner flache Dateien mit einer Zeile pro Objekt austauscht | Datensätze zuerst umgewandelt, zusammengeführt oder in anderen Systemen nachgeschlagen werden müssen |
| Redakteure oder Administratoren die Zuordnung anpassen können sollen | Die Zuordnung Teil einer getesteten, versionierten Integration ist |
| Die Zustellung an SFTP, S3 oder HTTP alles ist, was gebraucht wird | Sie idempotente Aktualisierungen über Ihr eigenes `remote_id`-Schema mit eigenen Regeln brauchen ([wie](ein-importskript-schreiben.md)) |

## Verwandte Seiten

- [xrowextract](../../funktionen/6.0/erweiterungen/xrowextract.md)
- [Pakete für Import und Export](pakete-importieren-und-exportieren.md)
- [Einen Export oder eine ausgehende Synchronisation schreiben](einen-export-oder-ausgehenden-abgleich-schreiben.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
