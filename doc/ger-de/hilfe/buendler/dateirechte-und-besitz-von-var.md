---
title: Dateirechte und Besitz von var/ in einem Bundle
audience: bundler, hosting
category: bundling
order: 390
summary: Welche Verzeichnisse einer gebündelten Installation beschreibbar sein müssen und von wem, die Einrichtung mit einem Benutzer oder einer Gruppe, die gehärteten Dateimodi, die Sie statt der für alle beschreibbaren Standards ausliefern, das SQLite-Verzeichnis, die Worker von Velocity und wie Sie Images bauen, in denen var/ nie root gehört.
keywords: Rechte, Besitzrechte, Dateimodi, beschreibbar, Benutzer, Gruppe, permissions, ownership, var/, settings, design, extension, chown, chmod, setgid, umask, StorageDirPermissions, StorageFilePermissions, TemporaryPermissions, LogFilePermissions, root, Velocity User, bundle
type: help
---
# Dateirechte und Besitz von var/ in einem Bundle

Diese Seite richtet sich an Bündler, die Besitzrechte und Modi der ausgelieferten Dateien festlegen.
"Rechteprobleme gehören bei jeder PHP-Anwendung zu den häufigsten Supportfällen; bei Exponential entstehen sie fast
immer, weil zwei Benutzer dieselben Dateien schreiben"
([Beschreibbare Verzeichnisse und Rechte](../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)).

## Inhalt

1. [Was beschreibbar sein muss](#was-beschreibbar-sein-muss)
2. [Wer schreibt](#wer-schreibt)
3. [Ein Benutzer oder eine Gruppe](#ein-benutzer-oder-eine-gruppe)
4. [Auszuliefernde Modi](#auszuliefernde-modi)
5. [Bauen ohne Dateien im Besitz von root](#bauen-ohne-dateien-im-besitz-von-root)
6. [Checkliste](#checkliste)

## Was beschreibbar sein muss

Die Prüfung `directory_permissions` des Einrichtungsassistenten liest `settings/setup.ini [directory_permissions]
CheckList`: `design`, `extension`, `settings`, `settings/override`, `settings/siteaccess`,
`settings/siteaccess/admin`, `var` und dessen Unterverzeichnisse für Cache, Protokolle, Speicher und Autoload.

| Verzeichnis | Muss beschreibbar bleiben |
|---|---|
| `var/` und alles darunter | immer |
| `settings/`, `settings/override`, `settings/siteaccess` | solange Administratoren Einstellungen im Browser ändern |
| `design/`, `extension/` | nur solange Pakete aus der Administration installiert werden |

"Alles andere (`kernel/`, `lib/`, `vendor/`, `index.php`) muss nur lesbar sein. Ein Codebaum, den das PHP der Website
nicht ändern kann, ist ein starker Schutz."

## Wer schreibt

Das PHP des Webservers, die Worker von Velocity, Cronjobs (`runcronjobs.php`) sowie Konsolenbefehle und Composer, die
ein Administrator ausführt. "Eine Cache-Datei, die `root` von der Kommandozeile geschrieben hat, kann der Benutzer der
Website danach nicht ersetzen, und die Website versagt an merkwürdigen Stellen."

## Ein Benutzer oder eine Gruppe

| Einrichtung | Wie |
|---|---|
| Ein Benutzer (empfohlen) | PHP-FPM-Pool `user = <site user>`; Velocity `[ServerSettings] User` und `Group`; Cron in der Crontab des Benutzers der Website; Konsole und Composer als Benutzer der Website |
| Eine Gruppe | eine gemeinsame Gruppe, beschreibbare Verzeichnisse gruppenschreibbar und mit setgid, Umask `0002` für jeden Prozess |

Die Gruppeneinrichtung von der Hosting-Seite:

```bash
sudo chgrp -R <site group> design extension settings var
sudo find var settings -type d -exec chmod 2775 {} +
```

Für Velocity: "Velocity als root zu starten ist in Ordnung: Nur sein Elternprozess bleibt root, und root-Worker werden
abgelehnt, sofern nicht `AllowRootWorkers=enabled` gesetzt ist (ausgeliefert `disabled`)."

## Auszuliefernde Modi

"Die ausgelieferten Werte sind `0777` und `0666`, für alle beschreibbar; `site.ini` selbst empfiehlt `0770` und `0660`
mit dem richtigen Benutzer und der richtigen Gruppe." Ein Bundle sollte die gehärteten Werte in den Einstellungen
ausliefern, die es installiert:

```ini
# settings/override/site.ini.append.php
[FileSettings]
StorageDirPermissions=0770
StorageFilePermissions=0660
TemporaryPermissions=0770
LogFilePermissions=0660
```

Weil der Installer `settings/override/site.ini.append.php` schreibt, wenden Sie diese Werte nach der Installation an
(oder über die Einstellungen Ihres Seitenpakets), nicht durch das Ausliefern einer befüllten Datei. Das
SQLite-Verzeichnis braucht dieselbe Behandlung: "Verzeichnismodus `2775` und eine Umask von `0002`" für eine
gemeinsame Gruppe ([SQLite-Bundles](sqlite-bundles.md)).

## Bauen ohne Dateien im Besitz von root

- Führen Sie `composer install`, `ezpgenerateautoloads.php` und den Installer in Ihrem Build und beim ersten Start
  **als Benutzer der Website** aus.
- Muss ein Schritt als root laufen, schließen Sie mit einer Reparatur der Besitzrechte der beschreibbaren
  Verzeichnisse ab, bevor das Image versiegelt wird.
- Die Installer lehnen root nicht ab ("beide akzeptieren `--allow-root-user` und ignorieren es"), "aber von `root`
  angelegte Dateien können später für den Webserver nicht beschreibbar sein"
  ([4.6](../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)).
- In Containern gilt: "Legen Sie die Benutzer-ID für Image und Volume einmal fest und behalten Sie sie bei."

## Checkliste

- [ ] Ein dokumentierter Benutzer (oder eine Gruppe) besitzt `var/`, `settings/`, `design/`, `extension/`.
- [ ] Codeverzeichnisse sind für das PHP der Website schreibgeschützt, wo das Bundle es zulässt.
- [ ] Gehärtete Modi in `[FileSettings]` nach der Installation angewendet.
- [ ] Keine Datei des Bundles unter `var/` gehört root.
- [ ] Anleitungen für Cron und Konsole nennen den Benutzer der Website.

## Verwandte Seiten

- [Beschreibbare Verzeichnisse und Rechte](../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)
- [13.11 Dateirechte und Besitzrechte](../../installation/13-sicherheitshaertung.md#1311-dateiberechtigungen-und-eigentümer)
- [Dateimodi](../../bc/6.0/dateimodi.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
