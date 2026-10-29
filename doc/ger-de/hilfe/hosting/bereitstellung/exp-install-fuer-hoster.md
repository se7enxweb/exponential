---
title: "exp:install für Hoster"
audience: hosting, admin
category: hosting
order: 30
summary: Die Installation über die Konsole, wie ein Hoster sie aus Skripten und Panels nutzt - die Optionen, die zählen, das Datenbankpasswort aus der Umgebung, die Regeln für das Administratorpasswort, Probelauf und Ausgabe, Exit-Status und Beispiele für jede der sechs Datenbanken.
keywords: exp:install, install.php, Installation über die Konsole, unbeaufsichtigt, Optionen, --db, --db-action, EXP_INSTALL_DB_PASSWORD, --url, --access, --password, --random-password, --dry-run, --print, --force, Exit-Status, initial-admin-password, console install, unattended, options, exit status
type: help
---
# exp:install für Hoster

Diese Seite ist für Systemadministratoren und Entwickler, die die Bereitstellung eines Hosters auf der Installation über die Konsole
aufbauen, `exp:install` (`bin/php/install.php`). Sie beantwortet: welche Optionen ein Hoster setzt, wie Geheimnisse übergeben werden,
was der Befehl mit Passwörtern tut, wie Sie vor der Installation testen und wie ein Skript weiß, dass es funktioniert hat. Die Referenz
ist [Kapitel 7](../../../installation/07-installation-ueber-die-konsole.md).

## Inhalt

1. [Was es ist](#was-es-ist)
2. [Die Optionen, die ein Hoster setzt](#die-optionen-die-ein-hoster-setzt)
3. [Geheimnisse](#geheimnisse)
4. [Das Administratorpasswort](#das-administratorpasswort)
5. [Zuerst testen: Ausgabe und Probelauf](#zuerst-testen-ausgabe-und-probelauf)
6. [Exit-Status](#exit-status)
7. [Beispiele je Datenbank](#beispiele-je-datenbank)

## Was es ist

`exp:install` ist eine Oberfläche für den Kickstarter. Es verwandelt seine Optionen in eine temporäre `kickstart.ini`, führt
`expKickstarter` im selben Prozess aus, entfernt die temporäre Datei und legt eine `kickstart.ini`, die vorher da war, wieder zurück
([4.2](../../../installation/04-installationsweg-waehlen.md#die-konsoleninstallation)). Jede Option hat einen Standard, sodass
`php bin/php/console exp:install --apply` allein das Seitenpaket `sevenx_multisite` auf SQLite mit dem Administrator `admin` und einem
erzeugten Passwort installiert.

## Die Optionen, die ein Hoster setzt

| Option | Standard | Setzen Sie sie auf |
|---|---|---|
| `--db=` | `sqlite` | `sqlite`, `pgsql`, `mongodb`, `mysql` (auch `mariadb`) oder `oracle` |
| `--db-host=`, `--db-port=`, `--db-name=`, `--db-user=` | je Engine | die Datenbank, die Sie für diesen Kunden angelegt haben; nie die Standards `root` oder `postgres` auf einer echten Website |
| `--db-action=` | `remove` | `remove` leert die Datenbank zuerst; auf einer neuen, leeren Datenbank ergeben `remove` und `ignore` dasselbe |
| `--url=` | `http://localhost` | die Adresse des Kunden; die Adresse jedes Siteaccess wird daraus gebaut |
| `--access=` | `url` | `url` (`/admin`), `host` (mit `--host` und `--admin-host`) oder `port` |
| `--package=` | `sevenx_multisite` | das Seitenpaket des Tarifs ([Seitenpakete](seitenpakete-fuer-hoster.md)) |
| `--language=`, `--languages=` | `eng-US` | die Sprachen des Kunden |
| `--title=` | `Exponential` | der Name der Website |
| `--email=` | `nospam@exponential.earth` | eine echte Adresse des Kunden; sie wird zu `[MailSettings] AdminEmail` |
| `--password=` / `--random-password` | erzeugt | siehe unten |
| `--force` | aus | nur für eine bewusste Neuinstallation |

Vollständige Liste mit jeder Prüfung und Meldung: [7.3 Optionen](../../../installation/07-installation-ueber-die-konsole.md#73-optionen).

> **Hinweis**
> Wenn `extension/exp_adminui` in der Installation ist, wird auch ein Siteaccess `adminui` angelegt, erreichbar über `/adminui`, was
> auch immer `--access` sagt ([7.3](../../../installation/07-installation-ueber-die-konsole.md#site)).

## Geheimnisse

- **Datenbankpasswort**: Übergeben Sie es in `EXP_INSTALL_DB_PASSWORD`, nicht mit `--db-password`. Eine Option ist für jeden Benutzer
  der Maschine in der Prozessliste (`ps`) sichtbar, solange die Installation läuft, und bleibt in der Shell-Historie. Die Variable ist
  für beides nicht sichtbar.
- **Administratorpasswort**: aus Ihrem Geheimnisspeicher mit `--password=`, oder lassen Sie den Befehl eines erzeugen und lesen Sie es
  aus `var/log/initial-admin-password` (Modus `0600`).
- **Die gespeicherte Konfiguration** in `var/log/exp-install-<date>.ini` hat maskierte Passwörter.

## Das Administratorpasswort

Aus [7.5](../../../installation/07-installation-ueber-die-konsole.md#75-das-passwort-des-administrators): `exp:install` installiert nie ein Passwort, das leer,
kurz oder bekannt ist.

| Sie geben | Ergebnis |
|---|---|
| kein `--password` | ein erzeugtes Passwort |
| `--random-password` | ein erzeugtes Passwort, auch wenn `--password` angegeben ist |
| `--password=` mit mindestens 10 Zeichen (`[UserSettings] MinPasswordLength`), das nicht bekannt ist | Ihr Passwort |
| ein kürzeres oder bekanntes Passwort | ein erzeugtes Passwort; die Zusammenfassung sagt, warum |
| ein leeres `--password=` | abgelehnt, Exit-Status 1 |

Ein erzeugtes Passwort hat 24 Zeichen aus einer sicheren Zufallsquelle. Es wird einmal in der Zusammenfassung gezeigt und nach
`var/log/initial-admin-password` geschrieben. Übergeben Sie es dem Kunden über einen sicheren Kanal und löschen Sie die Datei.

## Zuerst testen: Ausgabe und Probelauf

| Modus | Tut |
|---|---|
| `--print` | prüft die Optionen und gibt die Konfiguration aus, Passwörter maskiert; keine Datenbank, kein Netzwerk |
| `--dry-run` | prüft die Optionen, verbindet sich mit der Datenbank, holt die Pakete in ein temporäres Repository; schreibt nur Logs |

Ein Probelauf läuft auch auf einem Verzeichnis, das bereits eine Installation enthält, ohne `--force`, und endet mit
`Dry-run completed: database and remote packages verified. Stopped after SiteDetails, nothing written.`

## Exit-Status

`0`, wenn installiert, oder für ein bestandenes `--dry-run`, `--print` oder `--help`; `1` für alles andere, einschließlich des Falls,
dass das Verzeichnis bereits eine Installation enthält und weder `--force` noch `--dry-run` angegeben wurde. Ein Skript kann sich
darauf verlassen:

```bash
php bin/php/console exp:install --dry-run --db=pgsql --db-user=<db user> --url=https://www.example.com \
  && php bin/php/console exp:install --apply --db=pgsql --db-user=<db user> --url=https://www.example.com \
  || echo "installation failed, see var/log/setup.log"
```

Wenn der Lauf scheitert, bleibt der Wartungsmodus an, damit Besucher keine halb installierte Website sehen.

## Beispiele je Datenbank

In der offiziellen Reihenfolge. Führen Sie jedes als Benutzer der Website aus, mit gesetztem `EXP_INSTALL_DB_PASSWORD`, wo ein Server
genutzt wird.

```bash
# 1. SQLite 3 (the default): no database server
php bin/php/console exp:install --apply --url=https://www.example.com --email=<contact>

# 2. PostgreSQL
php bin/php/console exp:install --apply --db=pgsql --db-host=<db host> --db-name=<db> --db-user=<db user> \
  --url=https://www.example.com --email=<contact>

# 3. MongoDB (a database user is required)
php bin/php/console exp:install --apply --db=mongodb --db-host=<db host> --db-name=<db> --db-user=<db user> \
  --url=https://www.example.com --email=<contact>

# 4. MariaDB and 6. MySQL (same driver)
php bin/php/console exp:install --apply --db=mysql --db-host=<db host> --db-name=<db> --db-user=<db user> \
  --url=https://www.example.com --email=<contact>

# 5. Oracle (service name becomes host:port/service)
php bin/php/console exp:install --apply --db=oracle --db-host=<db host> --db-name=<service> --db-user=<db user> \
  --url=https://www.example.com --email=<contact>
```

Hinweise je Engine stehen in [7.9](../../../installation/07-installation-ueber-die-konsole.md#79-beispiele-nach-datenbank) und auf den Datenbankseiten dieses
Bereichs, von [SQLite für Hoster](../grundlagen/sqlite-fuer-hoster.md) bis [MySQL für Hoster](../grundlagen/mysql-fuer-hoster.md).

**Eine öffentliche Demo bereitstellen.** `EXP_DEMO_MODE=1 ./console exp:install --apply --password=<the published password> ...` installiert das veröffentlichte Administratorpasswort unverändert, auch ein bekanntes oder kurzes (`publish`, `publishing`); ohne die Variable wird es durch ein erzeugtes in `var/log/initial-admin-password` ersetzt, was ein Aufbau einer Demo als Fehler behandeln sollte. Stellen Sie die Website eines Kunden nie mit der Variablen bereit. [Rezept: eine öffentliche Demo auf LiteSpeed und Plesk betreiben](../server/rezept-eine-oeffentliche-demo-auf-litespeed-und-plesk-betreiben.md).

## Verwandte Seiten

- [Ein-Klick-Installer](ein-klick-installer-fuer-exponential.md)
- [Bereitstellung automatisieren](bereitstellung-automatisieren.md)
- [Mit einem Befehl installieren](../../../funktionen/6.0/installation-mit-einem-befehl.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
