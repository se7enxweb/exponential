---
title: Dateirechte und Eigentümer
audience: government, admin, hosting
category: public-sector
order: 380
summary: Für Administratorinnen, Administratoren und Hosting-Teams - das Modell für Dateieigentümer und Dateirechte, das verhindert, dass ein Fehler in einer PHP-Datei zu einer dauerhaften Kompromittierung wird: ein schreibender Benutzer, nur var/ und settings/ beschreibbar, keine für alle beschreibbaren Dateien, Geheimnisse mit 0640, keine Worker als root, und wie Sie das prüfen.
keywords: Dateirechte, Berechtigungen, Eigentümer, Rechtetrennung, schreibgeschützter Code, für alle beschreibbar, öffentlicher Sektor, file permissions, ownership, privilege separation, var, settings, read-only code, world-writable, StorageDirPermissions, StorageFilePermissions, EZP_DIR_MODE_MAX, EZP_FILE_MODE_MAX, root, AllowRootWorkers, TemporaryDir, public sector
type: help
---
# Dateirechte und Eigentümer

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams. Der häufigste Weg von einem
Fehler zu einer dauerhaften Kompromittierung ist eine Datei, die der Angreifer schreiben kann und die der Webserver
später ausführt oder ausliefert. Kann der PHP-Prozess nur `var/` und `settings/` ändern und wird unterhalb des
Wurzelverzeichnisses nichts ausgeführt, kann eine Schwachstelle in einer Erweiterung keinen Code einschleusen
([13.11 Dateirechte und Eigentümer](../../../installation/13-sicherheitshaertung.md#1311-dateiberechtigungen-und-eigentümer)).

## Inhalt

1. [Das Modell](#das-modell)
2. [Gehärtete Modi](#gehärtete-modi)
3. [Root](#root)
4. [Prüfen](#prüfen)
5. [Verwandte Seiten](#verwandte-seiten)

## Das Modell

- **Ein Benutzer schreibt**: PHP-FPM, Velocity-Worker, Cronjobs und Konsolenskripte laufen als Benutzer der Website
  (oder teilen sich eine Gruppe).
- **Nur was geschrieben werden muss, ist beschreibbar**: `var/` und `settings/` (und, solange Pakete aus der
  Administration installiert werden, `design/` und `extension/`). Der Code (`kernel/`, `lib/`, `vendor/`, `index.php`)
  kann einem anderen Benutzer gehören und für den Benutzer der Website schreibgeschützt sein.
- **Geheimnisse** in `settings/override`: `0640` oder strenger.
- **Temporäre Dateien**: `TemporaryDir` (ab Werk `/tmp/`) wird von allen Benutzern des Rechners geteilt; verwenden Sie
  besser ein eigenes Verzeichnis der Website unterhalb von `var/`.
- **Symbolische Links** aus dem Document-Root heraus: von Velocity abgewiesen (`FollowSymlinks=disabled`); mit Apache
  besser `SymLinksIfOwnerMatch`.

## Gehärtete Modi

Die mitgelieferten Werte von `[FileSettings]` sind `0777` und `0666`. Setzen Sie:

```ini
# settings/override/site.ini.append.php
[FileSettings]
StorageDirPermissions=0770
StorageFilePermissions=0660
TemporaryPermissions=0770
LogFilePermissions=0660
```

und korrigieren Sie vorhandene Dateien einmal (mit der Gruppe der Website auf allem):

```bash
find var -type d -exec chmod 2770 {} +
find var -type f -exec chmod 0660 {} +
```

Grenzen für neue Dateien lassen sich auch in `config.php` mit `EZP_DIR_MODE_MAX` und `EZP_FILE_MODE_MAX` setzen
([Grenzen für die Modi neuer Dateien](../../../bc/6.0/dateimodi.md)).

## Root

- Die Kommandozeilenskripte von Exponential verweigern die Ausführung als root, sofern nicht `--allow-root-user`
  angegeben wird.
- Die Worker von Velocity laufen nie als root (`[ServerSettings] AllowRootWorkers=disabled`).
- Führen Sie Cronjobs und die Konsole als Benutzer der Website aus dessen Crontab aus. Prüfen Sie nach einer
  dokumentierten Aufgabe als root, dass die Dateien in `var/` weiterhin dem Benutzer und der Gruppe der Website gehören.

## Prüfen

```bash
find settings extension/*/settings -perm -o+w          # must print nothing
find var -perm -o+w | head                             # should print nothing after hardening
ls -l settings/override
```

## Verwandte Seiten

- [13.11 Dateirechte und Eigentümer](../../../installation/13-sicherheitshaertung.md#1311-dateiberechtigungen-und-eigentümer)
- [Kapitel 8: Dateirechte und Eigentümer](../../../installation/08-die-website-ausliefern.md#87-dateiberechtigungen-und-eigentümer)
- [Benutzer und Pools je Kunde (Hoster)](../../hosting/sicherheit/benutzer-und-pools-pro-kunde.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
