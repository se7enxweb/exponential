---
title: Dateiberechtigungen
audience: dxp-developer
category: dxp-development
order: 12
summary: Welche Verzeichnisse eines DXP-Projekts der Webserver schreiben muss, wie Sie das mit ACLs erlauben, damit Kommandozeilenbenutzer und Webserver beide schreiben können, der Ausweg ohne ACLs und was Sie bei "Permission denied" prüfen.
keywords: Berechtigungen, Dateiberechtigungen, setfacl, ACL, var, public/var, www-data, chown, chmod, Permission denied, Benutzer des Webservers, Kommandozeilenbenutzer, permissions, web server user, CLI user
type: help
---
# Dateiberechtigungen

Diese Seite richtet sich an Entwickler und Administratoren, die ein DXP-Projekt auf einem Server oder einem
Arbeitsplatzrechner einrichten. Zwei Benutzer schreiben in das Projekt: Sie auf der Kommandozeile (`php bin/console`)
und der Webserver. Beide müssen dieselben Verzeichnisse schreiben können.

## Inhalt

1. [Welche Verzeichnisse](#welche-verzeichnisse)
2. [Mit ACLs (empfohlen)](#mit-acls-empfohlen)
3. [Ohne ACLs](#ohne-acls)
4. [Prüfen](#prüfen)
5. [Häufige Fehler](#häufige-fehler)

## Welche Verzeichnisse

| Verzeichnis | Geschrieben von | Enthält |
|---|---|---|
| `var/` | beiden | Symfony-Cache, Logs, Sessions, die SQLite-Datei, falls Sie eine verwenden |
| `public/var/` | beiden | gespeicherte Dateien und erzeugte Bildvariationen |
| `config/jwt/` | der Kommandozeile, einmal | das JWT-Schlüsselpaar (für den Webserver lesbar) |

Alles andere (`src/`, `config/`, `templates/`, `vendor/`) wird nur bei einer Bereitstellung geschrieben.

## Mit ACLs (empfohlen)

Ersetzen Sie `www-data` durch den Benutzer Ihres Webservers (`apache`, `nginx`, `_www` unter macOS):

```bash
setfacl -R  -m u:www-data:rwX -m g:www-data:rwX var/ public/var/
setfacl -dR -m u:www-data:rwX -m g:www-data:rwX var/ public/var/
```

Die erste Zeile setzt die Rechte auf das Vorhandene, die zweite (`-d`) macht sie zum Standard für neue Dateien.
Installieren Sie das Paket `acl`, wenn `setfacl` fehlt (`apt install acl`, `dnf install acl`).

## Ohne ACLs

Auf Dateisystemen ohne ACLs (manche NFS- und BSD-Mounts, macOS APFS):

```bash
chown -R www-data:www-data var/ public/var/
chmod -R 775 var/ public/var/
```

Fügen Sie dann Ihren Kommandozeilenbenutzer der Gruppe des Webservers hinzu oder führen Sie Konsolenbefehle als dieser
Benutzer aus (`sudo -u www-data php bin/console cache:clear`).

## Prüfen

```bash
getfacl var/ | head                       # the ACL entries
sudo -u www-data touch var/log/.write-test && sudo -u www-data rm var/log/.write-test
ls -l var/data_*.db                       # SQLite: owner and group
```

## Häufige Fehler

- **`cache:clear` als root ausführen.** Die Cache-Dateien gehören dann root, und der Webserver kann sie nicht neu
  schreiben. Führen Sie Konsolenbefehle als Ihr normaler Benutzer oder als Benutzer des Webservers aus.
- **`public/var/` vergessen.** Uploads schlagen fehl, und Bildvariationen liefern Fehler.
- **Alles auf 777 setzen.** Nie nötig; verwenden Sie ACLs oder die Gruppe.

## Verwandte Seiten

- [Installation mit dem Skeleton](installation-mit-dem-skeleton.md)
- [SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md)
- [Fehlersuche](fehlersuche-in-der-dxp.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
