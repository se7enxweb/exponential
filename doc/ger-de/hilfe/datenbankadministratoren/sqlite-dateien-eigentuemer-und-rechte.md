---
title: SQLite-Dateien, Eigentümer und Rechte
audience: dba
category: database-administration
order: 11
summary: Die vier Dateien einer SQLite-Datenbank von Exponential, welcher Prozess welche Datei schreiben können muss, wie Sie eine gemeinsame Gruppe mit dem Setgid-Bit und einer Umask von 0002 einrichten, damit sich PHP-FPM, Velocity, Cronjobs und Konsolenbefehle nie gegenseitig aussperren, und wie Sie sicherstellen, dass die Datenbank nie über HTTP ausgeliefert wird.
keywords: SQLite, Rechte, Eigentümer, Gruppe, Setgid, 2775, umask 0002, attempt to write a readonly database, -wal, -shm, writer-lock, var/storage/sqlite3, modfix, HTTP, Download, Sicherheit, permissions, ownership, group, download, security
type: help
---
# SQLite-Dateien, Eigentümer und Rechte

Diese Seite richtet sich an Datenbank- und Systemadministratoren, die die Dateien einer SQLite-Datenbank von Exponential
einrichten oder reparieren. Bei SQLite ist das Dateisystem die Zugriffskontrolle: Der richtige Eigentümer, die richtige
Gruppe und der richtige Modus sind das, was auf einer Serverdatenbank Rechte und Rollen sind. Diese Seite beantwortet:
welche Dateien es gibt, wer sie schreiben muss, wie Sie sie einmal so einrichten, dass jeder Prozess weiter funktioniert,
und wie Sie prüfen, dass sich die Datenbank nicht herunterladen lässt. Quelle:
[Dateien, Eigentümer und gemischte Benutzer](../../installation/09-datenbanken.md#dateien-eigentümer-und-gemischte-benutzer).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../hosting/control-panels/plesk.md), [cPanel](../hosting/control-panels/cpanel.md), [DirectAdmin](../hosting/control-panels/directadmin.md), [ISPConfig](../hosting/control-panels/ispconfig.md).

## Inhalt

1. [Die vier Dateien](#die-vier-dateien)
2. [Wer die Datenbank verwendet](#wer-die-datenbank-verwendet)
3. [Einmal einrichten](#einmal-einrichten)
4. [Nach einem Tag prüfen](#nach-einem-tag-prüfen)
5. [Was Sie nicht tun sollten](#was-sie-nicht-tun-sollten)
6. [Die Datenbank nie über HTTP ausliefern](#die-datenbank-nie-über-http-ausliefern)
7. [Checkliste](#checkliste)

## Die vier Dateien

| Datei | Was sie ist | Wer sie schreiben können muss |
|---|---|---|
| `exponential.db` | die Datenbank | jeder Prozess, der schreibt |
| `exponential.db-wal` | das Write-Ahead-Log | jeder Prozess, der schreibt |
| `exponential.db-shm` | der Index des WAL im gemeinsamen Speicher | **jeder Prozess, auch Leser** |
| `exponential.db.writer-lock` | die Schreibwarteschlange des Treibers | jeder Prozess (nur lesend genügt für `flock()`) |

SQLite legt `-wal` und `-shm` selbst an und entfernt sie, deshalb muss auch das **Verzeichnis** von jedem Prozess
schreibbar sein. Der Treiber legt `var/storage/sqlite3/` mit Modus `0775` an, setzt aber weder Modus noch Eigentümer der
Dateien: Sie erhalten den Eigentümer und die Umask des Prozesses, der sie zuerst angelegt hat.

## Wer die Datenbank verwendet

Auf einem typischen Server öffnen sie drei Arten von Prozessen:

- das PHP des Webservers (PHP-FPM oder mod_php), als Benutzer der Website;
- Velocity-Worker;
- Cronjobs und Konsolenbefehle, oft ein anderer Benutzer, manchmal `root`.

Legt einer von ihnen `-shm` mit Modus `0644` an, scheitern die anderen mit "attempt to write a readonly database".

## Einmal einrichten

```bash
# one group that every process runs with (the web server's group is usual)
chgrp -R <web group> var/storage/sqlite3
# the setgid bit: files created in the directory inherit its group
chmod 2775 var/storage/sqlite3
chmod 664 var/storage/sqlite3/*
```

Geben Sie dann jedem Prozess eine Umask von `0002`, damit neue Dateien für die Gruppe schreibbar sind:

- in einer systemd-Unit: `UMask=0002`;
- am Anfang einer Cron-Zeile: `umask 002; cd /path/to/installation && php runcronjobs.php ...`;
- im PHP-FPM-Pool und im Dienst von Velocity über ihren Dienstmanager.

[Kapitel 8](../../installation/08-die-website-ausliefern.md#87-dateiberechtigungen-und-eigentümer) behandelt die Benutzer, unter denen
die Server laufen; [Schreibbare Verzeichnisse und Rechte](../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)
dasselbe für Hosting-Unternehmen.

## Nach einem Tag prüfen

```bash
ls -l var/storage/sqlite3/
```

Jede Datei sollte für die Gruppe schreibbar sein und die gemeinsame Gruppe tragen (Eigentümer, Gruppe und Größen sind Ihre):

```text
-rw-rw-r-- 1 example www-data 187678720 Oct  5 10:14 exponential.db
-rw-rw-r-- 1 example www-data     32768 Oct  5 10:20 exponential.db-shm
-rw-rw-r-- 1 example www-data   4124152 Oct  5 10:20 exponential.db-wal
-rw-rw-r-- 1 example www-data         0 Oct  5 09:58 exponential.db.writer-lock
```

## Was Sie nicht tun sollten

- **Führen Sie nicht `bin/modfix.sh`** (`bin:modfix`) aus, um das zu beheben. Es macht `var/storage` und andere
  Verzeichnisse für alle schreibbar (Modus `777`, Dateien `666`), und das Skript selbst warnt, dass das nicht sicher ist.
  Korrigieren Sie Gruppe und Modus wie oben.
- **Löschen Sie nicht `-wal` oder `-shm`**, solange ein Prozess die Datenbank geöffnet hat.
- **Legen Sie die Datenbank nicht auf ein Netzwerkdateisystem**, das mehrere Maschinen teilen: WAL braucht gemeinsamen
  Speicher auf einem Host.
- **Kopieren Sie keine beschäftigte Datenbankdatei** als Sicherung; verwenden Sie `.backup` oder `VACUUM INTO`
  ([Sicherungen je Engine](sicherungen-je-engine.md)).

## Die Datenbank nie über HTTP ausliefern

Die Datenbank liegt unter `var/storage`, das die mitgelieferte `.htaccess` an `index.php` schickt und das die Liste
statischer Dateien von Velocity auslässt. Ist der Webserver von Hand konfiguriert, prüfen Sie es auf Ihrem eigenen Host;
alles außer `200` ist richtig:

```bash
curl -sI https://www.example.com/var/storage/sqlite3/exponential.db | head -1
```

Eine Datenbank, die unter einem absoluten Pfad außerhalb des Document Root liegt (`Database=/srv/exponential/data/site.db`),
kann überhaupt nicht ausgeliefert werden; für dieses Verzeichnis gelten dieselben Regeln zu den Eigentümern.

## Checkliste

- [ ] Eine gemeinsame Gruppe für jeden Prozess; Verzeichnis `2775`; Dateien `664`.
- [ ] Jeder Prozess (PHP-FPM, Velocity, Cron, Konsole) läuft mit Umask `0002`.
- [ ] Keine Datei in `var/storage/sqlite3/` gehört einem Benutzer, für den die anderen nicht schreiben können.
- [ ] `curl -sI` auf die Adresse der Datenbank gibt nicht `200` zurück.
- [ ] Sicherungen der Datei sind nur für Administratoren lesbar.

## Verwandte Seiten

- [SQLite 3 für Datenbankadministratoren](sqlite-fuer-datenbankadministratoren.md)
- [Die Datenbank absichern](die-datenbank-absichern.md)
- [Datenbankfehler beheben](datenbankfehler-beheben.md)
- [SQLite für Hoster](../hosting/grundlagen/sqlite-fuer-hoster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
