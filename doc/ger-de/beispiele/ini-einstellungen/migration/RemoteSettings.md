---
title: migration.ini [RemoteSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RemoteSettings] in migration.ini: RemoteList, Parallel, Timeout, Retries, StoreReports, StageOrder, Notify, NotifyEmail ..."
keywords: Einstellung, migration.ini, RemoteSettings, INI, setting, RemoteList, Parallel, Timeout, Retries, StoreReports, StageOrder, Notify, NotifyEmail, SelfHeal, StaleLockAge, DiskMargin, PhpCheck, ExtensionCheck
type: generated
generated: exp:help:examples
---
# migration.ini [RemoteSettings]

Der Block `[RemoteSettings]` von `settings/migration.ini`, mit 13 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RemoteList

> Gegenstellen: andere Installationen, auf die diese ihre Migrationen überträgt (./console exp:migration:deploy  
> --remotes=...), alle gleichzeitig und im Gleichstand. Ein Abschnitt \[Remote\_<id>\] für jede ID aus RemoteList\[\]; setzen  
> Sie sie in settings/override. Ein Abschnitt einer Gegenstelle enthält nie ein Geheimnis: Credential nennt eines aus dem  
> Geheimnisspeicher (Einrichtung > Geheimnisse) oder eine Umgebungsvariable (env:VARIABLE). Anleitung:  
> doc/ger-de/anleitungen/migrationen-remote-deploy.md  
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
> Trigger=https://node1.example.com/migration/trigger    nur ftp und ftps: der signierte HTTP-Trigger der Gegenstelle  
>                                  führt die Migrationen dort aus (\[TriggerSettings\] dort); ohne ihn sendet FTP nur  
> TriggerSecret=trigger\_node1      das Geheimnis, mit dem die Anfragen des Triggers signiert werden (ein Geheimnisname oder env:VARIABLE)  

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

## StageOrder

> Ein Deploy in Stufen (--staged, „In Stufen“ in der Administration) teilt die Gegenstellen nach ihrer Environment in  
> Stufen ein, in dieser Reihenfolge (eine Umgebung, die nicht aufgeführt ist, kommt zuletzt); jede Stufe wird ganz oder gar  
> nicht umgeschaltet, und ein Tor wartet, bis jemand die nächste freigibt (--continue=<Deploy-ID> oder Freigeben auf der  
> Seite des Deploys).  

Standard: eine Liste mit 8: `alpha`, `dev`, `test`, `stage`, `beta`, `production`, `post`, `arch`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
# ein leeres "StageOrder[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
StageOrder[]
StageOrder[]=alpha
StageOrder[]=dev
StageOrder[]=test
StageOrder[]=stage
StageOrder[]=beta
StageOrder[]=production
StageOrder[]=post
StageOrder[]=arch
*/ ?>
```

## Notify

> Hinweise per E-Mail zu einem Deploy: failure (abgelehnt, fehlgeschlagen, zurückgenommen, nicht im Gleichstand), gate (ein Deploy  
> in Stufen wartet auf Freigabe), success (ausgeliefert oder übertragen). Reiner Text, Mail-Kategorie admin.  

Standard: eine Liste mit 2: `failure`, `gate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
# ein leeres "Notify[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Notify[]
Notify[]=failure
Notify[]=gate
*/ ?>
```

## NotifyEmail

> Wohin die Hinweise gehen; leer: site.ini \[MailSettings\] AdminEmail.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
NotifyEmail=
*/ ?>
```

## SelfHeal

> Selbstheilung: die Korrekturen, die ein Deploy auf einer Gegenstelle vornehmen darf (./console exp:migration:heal  
> dort), jede nur, wenn sie aufgeführt ist, und jede in den fixes der Gegenstelle im Bericht vermerkt. Vor dem Preflight:  
> stale-lock (eine Laufsperre, deren Prozess beendet ist, älter als StaleLockAge, auf jenem Host vom selben Benutzer  
> gehalten), paths (fehlende Migrationsordner), modes (die Rechte aus \[FileSettings\] für die Migrationsordner und  
> var/<site>/migration, nur ergänzt); nach dem Umschalten: caches (ini und template-override). Nie korrigiert, nur  
> gemeldet: PHP- oder Erweiterungsversionen, ein Fehler der Migration selbst.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
# ein leeres "SelfHeal[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SelfHeal[]
*/ ?>
```

## StaleLockAge

> Sekunden, ab denen eine gehaltene Laufsperre, deren Prozess beendet ist, als verwaist gilt (SelfHeal\[\]=stale-lock).  

Standard: `7200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
StaleLockAge=7200
*/ ?>
```

## DiskMargin

> Der freie Plattenplatz, den eine Gegenstelle über die Größe der Dateien hinaus haben muss, die ein Deploy ihr sendet (Bytes, oder mit K, M, G).  

Standard: `50M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
DiskMargin=50M
*/ ?>
```

## PhpCheck

> Die PHP-Version einer Gegenstelle im Vergleich zu der dieser Installation: major (dieselbe Hauptversion, sonst hält  
> der Deploy an), minor (dieselbe Haupt- und Nebenversion), warn (ein Unterschied ist nur eine Warnung), off.  

Standard: `major`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
PhpCheck=major
*/ ?>
```

## ExtensionCheck

> Die Erweiterungen einer Gegenstelle im Vergleich zu denen dieser Installation: migrations (jede Erweiterung, deren  
> Migrationen gesendet werden, ist dort mit derselben Version aktiv, sonst hält der Deploy an; andere Unterschiede sind  
> Warnungen), all (jede aktive Erweiterung), warn (Unterschiede sind nur Warnungen), off.  

Standard: `migrations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
ExtensionCheck=migrations
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
