---
title: migration.ini [RemoteSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RemoteSettings] in migration.ini: RemoteList, Parallel, Timeout, Retries, StoreReports"
keywords: Einstellung, migration.ini, RemoteSettings, INI, setting, RemoteList, Parallel, Timeout, Retries, StoreReports
type: generated
generated: exp:help:examples
---
# migration.ini [RemoteSettings]

Der Block `[RemoteSettings]` von `settings/migration.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RemoteList

> Gegenstellen: andere Installationen, auf die diese ihre Migrationen überträgt (./console exp:migration:deploy --remotes=...),  
> alle gleichzeitig und im Gleichstand. Ein Abschnitt \[Remote\_<id>\] je ID von RemoteList\[\]; setzen Sie sie in settings/override.  
> Ein Gegenstellen-Abschnitt enthält nie ein Geheimnis: Credential nennt eines des Geheimnisspeichers (Setup > Secrets) oder eine  
> Umgebungsvariable (env:VARIABLE). Anleitung: doc/ger-de/anleitungen/migrationen-remote-deploy.md  
>
> \[Remote\_production1\]  
> Environment=production           alpha, dev, stage, beta, production, post, arch ... (die Bezeichnung in den Berichten)  
> Transport=rsync                  ssh (scp), sftp, rsync (über ssh), ftp, ftps (nur Dateien), local (dieser Host)  
> Host=node1.example.com  
> Port=22  
> User=deploy  
> Path=/var/www/site               das Wurzelverzeichnis der Installation auf der Gegenstelle  
> Php=php                          das PHP-CLI dort  
> Credential=sshkey:deploy\_production1  
> Groups\[\]=production              --remotes=production wählt jede Gegenstelle der Gruppe  
> HostKeyFingerprint=SHA256:...    optional: der Host-Schlüssel muss ihn haben (sonst wird er beim ersten Kontakt gelernt)  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
# ein leeres "RemoteList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RemoteList[]
*/ ?>
```

## Parallel

> An wie vielen Gegenstellen gleichzeitig gearbeitet wird. Das Umschalten (jede Gegenstelle führt aus) läuft immer auf allen gleichzeitig.  

Standard: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
Parallel=4
*/ ?>
```

## Timeout

> Sekunden, die eine Gegenstelle für eine Phase brauchen darf (Preflight, Übertragung, Prüfung, Umschalten, Rollback).  

Standard: `900`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
Timeout=900
*/ ?>
```

## Retries

> Wie oft ein Verbindungsfehler (ssh-Exit 255, eine Zeitüberschreitung, eine abgebrochene FTP-Übertragung) im Preflight,  
> bei der Übertragung und der Prüfung erneut versucht wird. Ein Lauf auf einer Gegenstelle wird nie zweimal gestartet.  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
Retries=2
*/ ?>
```

## StoreReports

> enabled: Jeder Deploy, der etwas sendet oder ausführt, behält seinen Bericht in var/<site>/migration/deploy/reports/.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
StoreReports=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
