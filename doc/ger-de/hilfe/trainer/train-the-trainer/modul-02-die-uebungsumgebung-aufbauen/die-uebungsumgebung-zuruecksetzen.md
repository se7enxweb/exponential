---
title: "Lektion 2.6: Die Übungsumgebung zwischen Einheiten zurücksetzen"
audience: trainer, admin
category: training
order: 26
summary: Bringen Sie eine Schulungsinstallation vor der nächsten Gruppe in einen bekannten Zustand zurück, installieren Sie nach einem Probelauf mit --force neu, bewahren Sie eine Kopie der SQLite-Datenbank auf, entfernen Sie Schulungskonten und setzen Sie niemals eine Website mit echten Daten zurück. 35 Minuten.
keywords: zurücksetzen, neu installieren, force, dry-run, probelauf, SQLite, sicherung, kopie, übungsumgebung, sauberer zustand, konten entfernen, wartung, reset, reinstall, backup, copy, training lab, clean state, remove accounts
type: help
---
# Lektion 2.6: Die Übungsumgebung zwischen Einheiten zurücksetzen

Nach einem Kurs sind die Schulungs-Websites voller halbfertiger Artikel, Testbenutzer und geänderter Einstellungen.
Die nächste Gruppe braucht einen sauberen Start. Mit SQLite und `exp:install` dauert ein Zurücksetzen Minuten, aber es
löscht auch alles und darf deshalb nie auf eine Website gerichtet werden, auf die es ankommt. Diese Lektion vermittelt
einen sicheren Ablauf.

> **Warnung**
> Eine Neuinstallation mit `--force` ersetzt die Datenbank und die Einstellungen der Installation. Führen Sie sie nur
> in einem Verzeichnis aus, das Sie für die Schulung angelegt haben.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 2: Die Übungsumgebung aufbauen](README.md) |
| Dauer | 35 Minuten (10 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer, die Kurse wiederholt durchführen |
| Voraussetzungen | [Lektion 2.3](eine-installation-pro-teilnehmer.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- zwischen einer vollständigen Neuinstallation und dem Wiederherstellen einer gespeicherten Datenbankdatei
  entscheiden;
- `exp:install --dry-run` und dann `--force` in einem Schulungsverzeichnis ausführen;
- eine „Referenzkopie“ einer Schulungsdatenbank speichern und wiederherstellen;
- Schulungskonten von einer gemeinsamen Website entfernen.

## Hinweise für Trainer

### So präsentieren Sie es

1. Zwei Strategien (4 Minuten):
   - **Neuinstallation**: `exp:install --force` ersetzt die Datenbank (mit der Voreinstellung `--db-action=remove`)
     und die Einstellungen. Sauber, immer gleich, dauert so lange wie eine Installation.
   - **Referenzkopie**: Einmal installieren, den gewünschten Zustand vorbereiten (Beispielinhalte, Konten der
     Teilnehmenden), den Server stoppen und `var/storage/sqlite3/exponential.db` und `var/storage/` beiseitekopieren.
     Zum Zurücksetzen den Server stoppen, sie zurückkopieren und die Caches leeren.
2. Zeigen Sie das Sicherheitsnetz (3 Minuten): Ohne `--force` verweigert der Befehl eine vorhandene Installation.
   Führen Sie immer zuerst `--dry-run` aus; es funktioniert auf einer vorhandenen Installation ohne `--force`.
3. Erklären Sie die Caches (3 Minuten): Leeren Sie sie nach einer Wiederherstellung mit
   `php bin/php/ezcache.php --clear-all`.

### Häufige Fragen

**Behält eine Neuinstallation die Konten meiner Teilnehmenden?** Nein. Alles in der Datenbank wird ersetzt. Legen Sie
die Konten neu an oder verwenden Sie die Referenzkopie.

**Was ist mit hochgeladenen Bildern?** Sie sind Dateien unter `var/` (dem Speicherverzeichnis der Website), nicht in
der Datenbank. Kopieren Sie das Speicherverzeichnis zusammen mit der Datenbank, wenn Sie eine Referenzkopie
aufbewahren.

### Stolperfallen

- **Das falsche Verzeichnis.** Prüfen Sie vor jedem `--force` mit `pwd`, wo Sie sind.
- **Der Server läuft noch.** Stoppen Sie den Server, bevor Sie eine SQLite-Datei kopieren, sonst kann die Kopie
  inkonsistent sein.
- **Caches.** Eine wiederhergestellte Datenbank mit alten Caches zeigt alte Seiten. Leeren Sie nach einer
  Wiederherstellung alle Caches.

## Praktische Übung

Auf der Schulungsinstallation aus Lektion 2.3:

1. Stoppen Sie den Server: `php bin/php/console exp:velocity stop`.
2. Legen Sie eine Referenzkopie an: `mkdir -p ../golden` und `cp -a var/storage ../golden/`.
3. Starten Sie den Server, melden Sie sich an und erstellen Sie irgendwo einen Testartikel (beliebiger Titel).
4. Stoppen Sie den Server. Stellen Sie wieder her: `cp -a ../golden/storage/. var/storage/`.
5. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-all`.
6. Starten Sie den Server und prüfen Sie, dass der Testartikel weg ist.
7. Testen Sie jetzt eine vollständige Neuinstallation: `pwd` (prüfen Sie das Verzeichnis!), dann
   `php bin/php/console exp:install --dry-run --url=http://127.0.0.1:8087`.
8. Wenn der Probelauf erfolgreich ist: `php bin/php/console exp:install --apply --force --url=http://127.0.0.1:8087` und
   notieren Sie das neue erzeugte Passwort.

## Erwartetes Ergebnis

- Nach der Wiederherstellung existiert der Testartikel nicht mehr.
- Nach der Neuinstallation ist die Website die frische Website `sevenx_multisite` mit einem neuen
  Administratorpasswort, und `var/log/initial-admin-password` existiert wieder.
- Der Trainer nimmt den Ablauf zum Zurücksetzen und seine Dauer in die Kurs-Checkliste auf.

## Quiz

1. Was ersetzt `exp:install --force`?
2. Warum stoppt man den Server, bevor man eine SQLite-Datenbankdatei kopiert?
3. Was müssen Sie nach dem Wiederherstellen einer Datenbankdatei ausführen?
4. Braucht `--dry-run` auf einer vorhandenen Installation `--force`?

<details>
<summary>Antworten</summary>

1. Die Datenbank (mit der Voreinstellung `--db-action=remove`) und die Einstellungen der Installation.
2. Der Server schreibt möglicherweise in die Datei; eine dann erstellte Kopie kann inkonsistent sein.
3. Die Caches leeren: `php bin/php/ezcache.php --clear-all` ([Caches](../../../../anleitungen/caches.md)).
4. Nein. Ein Probelauf funktioniert ohne `--force` und ändert nichts.

</details>

## Weiterführende Literatur

- [Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md): `--force`, `--dry-run` und
  `--db-action`.
- [Caches](../../../../anleitungen/caches.md): was die Caches sind und wie man sie leert.
- [Sicherheit bei Live-Demos](../../../fuersprecher/sicherheit-bei-live-demos.md): eine saubere Demo-Installation vorbereiten.

## Verwandte Seiten

- [Modul 2: Die Übungsumgebung aufbauen](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
