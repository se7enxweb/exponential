---
title: Eine Website zwischen Hostern umziehen
audience: hosting, admin
category: hosting
order: 50
summary: Eine Exponential-6-Website von einem Server oder Hoster auf einen anderen umziehen, ohne ihre Version zu ändern - was kopiert wird, was frisch geholt wird, die Einstellungen, die den alten Server nennen, die Befehle in Reihenfolge und die Prüfungen danach.
keywords: Umzug, Übertragung, Serverumzug, Wechsel des Hosters, Website kopieren, rsync, Backup, Wiederherstellung, settings override, Siteaccess, Datenbankverbindung, Caches, Autoloads, Velocity, DNS, Berechtigungen, move, transfer, server move, host change, copy site, restore, database connection, caches, autoloads, permissions
type: help
---
# Eine Website zwischen Hostern umziehen

Diese Seite ist für Systemadministratoren, die eine Exponential-6-Website von einer Maschine auf eine andere umziehen,
zwischen Hostern oder innerhalb eines Hosters, in derselben Version. Sie beantwortet: was Sie kopieren, was Sie neu holen,
welche Einstellungen die alte Maschine nennen und in welcher Reihenfolge die Befehle laufen.

Wenn sich auch die Version ändert, lesen Sie zuerst [Von älteren Exponential-Versionen](kunden-von-aelteren-versionen-migrieren.md);
der Ablauf auf der Seite des Kunden steht auf [Kunden zu Ihrem Hosting migrieren](kunden-zu-ihrem-hosting-migrieren.md).

## Inhalt

1. [Was umzieht und was nicht](#was-umzieht-und-was-nicht)
2. [Vor dem Umzug](#vor-dem-umzug)
3. [Der Umzug, Schritt für Schritt](#der-umzug-schritt-für-schritt)
4. [Einstellungen, die die alte Maschine nennen](#einstellungen-die-die-alte-maschine-nennen)
5. [Prüfungen danach](#prüfungen-danach)

## Was umzieht und was nicht

| Element | Umziehen? | Wie |
|---|---|---|
| Die Datenbank | ja | die Werkzeuge der Engine für Backup und Wiederherstellung ([Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)) |
| `var/storage` (und `var/<site>/storage`) | ja | Archiv oder `rsync` |
| `settings/override`, `settings/siteaccess` | ja | sie enthalten Passwörter: vertraulich übertragen |
| Eigene Erweiterungen und Designs | ja, vorzugsweise aus ihren Git-Repositories | `git clone` |
| Der Kernel und die paketierten Erweiterungen | frisch in derselben Version holen | Git-Checkout des Tags oder `composer install` aus der Lock-Datei |
| `var/cache`, `var/<site>/cache` | nein | wird neu erzeugt |
| `var/log` | optional | für spätere Untersuchungen |

Quelle der Liste: [10.9 Backups und Wiederherstellung](../../../../en/us/install/10-after-installing.md#109-backups-and-restore).

## Vor dem Umzug

- Das Ziel erfüllt die Anforderungen, mit derselben PHP-Hauptversion oder einer neueren unterstützten
  ([Anforderungen auf einen Blick](../grundlagen/anforderungen-auf-einen-blick.md)).
- Dieselbe Datenbank-Engine ist installiert, oder Sie planen einen Wechsel der Engine
  ([Die Datenbank-Engine wechseln](die-datenbank-engine-einer-kunden-website-wechseln.md)).
- Die PHP-Erweiterungen, die die Website nutzt, sind auf dem Ziel installiert.
- Die Time-to-Live im DNS der Domains der Website ist gesenkt.
- Eine Wiederherstellung des neuesten Backups auf dem Ziel wurde einmal getestet.

## Der Umzug, Schritt für Schritt

1. **Wartungsmodus an, auf der alten Maschine**, damit zwischen dem Backup der Datenbank und dem der Dateien kein
   Schreibvorgang geschieht:

   ```bash
   php bin/php/maintenance.php on --message="Moving to a new server" --until=1h --allow-root-user
   ```

2. **Sichern** Sie die Datenbank mit dem Werkzeug der Engine und die Dateien:

   ```bash
   tar czf files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess
   ```

3. **Bringen Sie den Code auf dem Ziel an seinen Platz**: denselben Tag des Kernels (`git checkout v6.0.<n>`) und
   `composer install`, dazu eigene Erweiterungen aus Git.
4. **Stellen Sie** die Datenbank **wieder her** und entpacken Sie die Dateien; setzen Sie Eigentümer und Modus, die der
   Webserver oder die Engine des Ziels braucht ([Beschreibbare Verzeichnisse und Berechtigungen](../grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)).
5. **Passen Sie die Einstellungen an**, die unten aufgeführt sind.
6. **Erzeugen Sie die Autoloads neu und leeren Sie die Caches**:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

7. **Starten Sie die Engine** (oder laden Sie PHP-FPM neu), leeren Sie den Antwort-Cache von Velocity, wenn es die Website
   ausliefert (`php bin/php/console exp:velocity cache clear --allow-root-user`).
8. **Testen Sie** über einen Eintrag in der hosts-Datei, und schalten Sie dann das DNS um.
9. **Lassen Sie die alte Maschine im Wartungsmodus**, bis die DNS-Änderung alle erreicht hat. Der Wartungsmodus liegt in
   `var/maintenance.json` auf der alten Maschine, die das Datei-Backup oben nicht enthält; das Ziel ist also nicht im
   Wartungsmodus, es sei denn, Sie schalten ihn dort ein. Wenn Sie das getan haben (um in Ruhe zu testen), beenden Sie ihn
   mit `php bin/php/maintenance.php off --allow-root-user`
   ([10.9](../../../../en/us/install/10-after-installing.md#maintenance-windows)).

## Einstellungen, die die alte Maschine nennen

| Einstellung | Datei | Ändern auf |
|---|---|---|
| `[DatabaseSettings]` Server, Port, Benutzer, Passwort, Datenbank | `settings/override/site.ini.append.php` | die Datenbank des Ziels |
| Adressen der Website und Zuordnung der Siteaccesses | `settings/override/site.ini.append.php`, `settings/siteaccess/*/site.ini.append.php` | die Hostnamen des Ziels, wenn sie sich ändern |
| `[MailSettings]` Transport und Server | `settings/override/site.ini.append.php` | die Mail-Einrichtung des Ziels ([Mailzustellung](../betrieb/mailzustellung.md)) |
| Cluster-Einstellungen | `settings/override/file.ini.append.php` | den gemeinsamen Speicher des Ziels, wenn geclustert |
| Einstellungen von Velocity | `settings/override/velocity.ini.append.php`, falls vorhanden | die Ports und Zertifikate des Ziels |
| Absolute Pfade in eigenen Einstellungen | jede Überschreibung | die Pfade des Ziels |

> **Warnung**
> `settings/override` enthält das Datenbankpasswort und andere Geheimnisse. Legen Sie es nie in ein Ticket, ein
> öffentliches Repository oder einen geteilten Ordner; übertragen Sie es über einen verschlüsselten Kanal, und halten Sie
> das Archiv vertraulich.

## Prüfungen danach

- Startseite und Administration laden auf dem Ziel.
- Anmelden, ein Testobjekt veröffentlichen, ein Bild hochladen, suchen.
- Zeilenzahlen von `ezcontentobject`, `ezcontentobject_tree` und `ezcontentobject_attribute` mit der alten Datenbank
  vergleichen ([Datenbankunterstützung](../../../../en/us/help/database-support.md#moving-a-site-to-another-database)).
- Die Cronjobs laufen auf dem Ziel und nicht mehr auf der alten Maschine ([Cronjobs](../betrieb/cronjobs.md)).
- Mail kommt an.
- Der Log-Viewer zeigt keine neuen Fehler ([Log-Viewer und Log-Rotation](../betrieb/log-viewer-und-log-rotation.md)).

## Verwandte Seiten

- [Aus einem Backup wiederherstellen (Runbook)](../runbooks/aus-einem-backup-wiederherstellen.md)
- [Was gesichert werden muss](../betrieb/backups-was-gesichert-werden-muss.md)
- [Checkliste für den Start einer Kunden-Website](../checklisten/checkliste-fuer-den-start-einer-kunden-website.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
