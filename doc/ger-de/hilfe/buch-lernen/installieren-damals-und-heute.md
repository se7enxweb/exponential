---
title: "Installieren, damals und heute: die Dateien, die Datenbank, der virtuelle Host"
audience: user, developer, admin, builder
category: books
order: 100
summary: Was Sie im ersten Kapitel des Buchs Learning von Hand erledigen sollten (entpacken, eine Datenbank anlegen, einen virtuellen Host schreiben, Bilder und Cron einrichten, die INI-Dateien lesen) und was jeder Schritt in Exponential 6 ist.
keywords: Installation, installieren, Tarball, Datenbank, virtueller Host, mod_rewrite, INI-Dateien, Einstellungen, Override, Siteaccess, Buch Learning, install, installation, database, virtual host, ini files, settings, override, siteaccess, Learning book
type: help
---
# Installieren, damals und heute: die Dateien, die Datenbank, der virtuelle Host

Kapitel 1 des Buchs *Learning eZ Publish* beginnt mit der Arbeit, die jede Installation braucht: die Dateien besorgen,
eine Datenbank anlegen, den Webserver auf die Dateien zeigen lassen, die Bildkonvertierung und eine regelmäßige
Aufgabe einrichten und lernen, wo die Einstellungen liegen. Die Schritte gibt es noch. Die meisten sind heute ein
einziger Befehl. Diese Seite folgt der Reihenfolge des Buchs und sagt, was Sie heute tun.

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
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

## Die Installation entpacken

Das Buch lud ein Archiv von etwa 4 MB herunter, entpackte es auf etwa 20 MB und übertrug die Ordner auf den Webserver.
Exponential bietet drei Wege zu denselben Dateien (siehe [Erste Schritte](../../anleitungen/erste-schritte.md)):

- einen Git-Klon des Kernels, mit den Erweiterungen als Klone (der Weg, eine Website zu bauen, die Sie pflegen),
- `composer create-project se7enxweb/exponential`,
- und, für einzelne Zusätze später, Pakete, die mit `ezpm` installiert werden.

Nach jedem der ersten beiden Wege sind alle Ordner da, die das Buch aufgezählt hat:

| Buch | Exponential 6 |
|---|---|
| `design` | `design/`: die Standard-Templates und -Stile. Ihre eigenen gehören in den Ordner `design/` einer Erweiterung, nicht hierher. |
| `index.php` | `index.php`: Jede Seitenanfrage kommt weiterhin hier herein. |
| `kernel`, `lib` | `kernel/`, `lib/`: der Code. Bearbeiten Sie ihn nicht; siehe [Kernel-Overrides, das letzte Mittel](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/kernel-overrides-der-letzte-ausweg.md). |
| `runcronjobs.php` | `runcronjobs.php`: weiterhin das Skript, das eine regelmäßige Aufgabe ausführt ([Cronjobs](../../anleitungen/cronjobs.md)). |
| `settings` | `settings/`: Standardwerte, dazu `override/` und `siteaccess/` für Ihre eigenen. |
| `var` | `var/`: hochgeladene Dateien, Caches und Protokolle. Der Benutzer des Webservers muss hier schreiben können. |

Neu in Exponential: `extension/` (Zusätze), `console` (die Kommandozeilenoberfläche, `./console list`) und `vendor/`
(Composer-Pakete).

## Die Datenbank initialisieren

Das Buch legte eine MySQL-Datenbank und einen Benutzer an, der nur diese Datenbank verwenden darf, mit `CREATE DATABASE`
und `GRANT ALL PRIVILEGES`, und empfahl diesen Benutzer statt eines mächtigen, weil das Passwort im Klartext in einer
Einstellungsdatei steht. Dieser Rat gilt unverändert. Neu sind zwei Dinge:

- Für den Anfang brauchen Sie keinen Datenbankserver: Standard ist SQLite in einer Datei. MySQL oder MariaDB,
  PostgreSQL, MongoDB und Oracle sind Optionen von `exp:install --db=` und des Setup-Assistenten ([Datenbanken](../../installation/09-datenbanken.md)).
- In `GRANT` akzeptieren MySQL 8 und MariaDB kein `IDENTIFIED BY` mehr; legen Sie den Benutzer zuerst mit
  `CREATE USER` an, dann `GRANT`. Das [Installationsbuch](../../installation/09-datenbanken.md) zeigt die Anweisungen.

## Der virtuelle Host und das Umschreiben

Das Buch erklärt, warum ein virtueller Host mit `mod_rewrite` die richtige Einrichtung ist: Alle Anfragen gehen an
`index.php`, URLs wie `/node/view/253` werden zu lesbaren Adressen wie `/contact`, und nur dieses eine Skript ist
erreichbar. Die beiden Rewrite-Zeilen des Buchs (alles, was kein Skript, Bild oder Stil ist, geht an `index.php`) sind
das, was die mitgelieferte `.htaccess` heute tut, in längerer und sichererer Form. Verwenden Sie unter Apache die
mitgelieferte `.htaccess` oder übernehmen Sie ihre Regeln in die Konfiguration des Hosts. Um eine Website ganz ohne
Webserver auszuprobieren, bringt Exponential einen eigenen mit, Velocity
([Die Website ausliefern](../../installation/08-die-website-ausliefern.md)).

Die Website in einem Unterordner zu betreiben, was das Buch erlaubt, wird nicht mehr empfohlen: Die Logik der
Siteaccesses und URL-Aliase ist für eine Website im Wurzelverzeichnis eines Hostnamens gebaut.

## Bildeinstellungen: ImageMagick und GD

Bilder werden auf dem Server konvertiert: Aus dem Original, das Sie hochladen, werden die Größen, die Ihre Templates
anfordern (die *Bild-Aliase*). Das Buch nennt zwei Konverter, ImageMagick (ein eigenes Programm, das leistungsfähigere)
und GD (eine Bibliothek in PHP, eine Ausweichlösung). Beide gibt es noch, in `settings/image.ini`. Der Unterschied
heute: GD steht an erster Stelle der Liste (es arbeitet innerhalb von PHP und startet kein Programm), und ImageMagick
ist die Ausweichlösung für Formate, die GD nicht lesen kann, etwa TIFF oder PDF, und für Filter, die nur es hat. Um
wieder ImageMagick zu bevorzugen, überschreiben Sie die Liste in `settings/override/image.ini.append.php`:

```ini
[ImageConverterSettings]
ImageConverters[]
ImageConverters[]=ImageMagick
ImageConverters[]=GD
```

Der alte Hinweis des Buchs zu GD vor Version 2 (schlechte Skalierung) ist Geschichte: Jedes GD in einem PHP, auf dem
Exponential läuft, hat Version 2 oder höher. Der Setup-Assistent schaltet ImageMagick von selbst ab, wenn er kein
Programm `convert` findet.

## Cronjobs

Das Buch verlangt eine Aufgabe alle 15 Minuten, die `runcronjobs.php` ausführt, für Workflows, Benachrichtigungen und
andere regelmäßige Arbeit. Das gilt auch heute. Die Zeile des Buchs übergab PHP die Option `-C`; sie wird nicht
gebraucht, und die Optionen, auf die es ankommt, sind `-q` (still) und `-s <siteaccess>`. Die Cron-Tabelle in
[Nach der Installation, 10.3](../../installation/10-nach-der-installation.md#103-cronjobs) ist die aktuelle, mit einer eigenen
Zeile für jeden Teil (`frequent`, der Standard, `infrequent`, `cache_cleanup`). Ohne die Aufgabe funktioniert die
Website, aber Workflows, die auf die Aufgabe warten, und Benachrichtigungen per E-Mail nicht.

## Konfigurationsdateien

Die Erklärung des Buchs stimmt noch genau: Einstellungen liegen in `.ini`-Dateien; die Standardwerte stehen in
`settings/`; die Ordner `settings/siteaccess/<name>/` und `settings/override/` ändern sie; die Reihenfolge ist
**Override, dann Siteaccess, dann die Standardwerte** (der erste gefundene Wert einer Einstellung gewinnt, von oben).
Die Dateien, die ein Builder am häufigsten anfasst, sind wie im Buch: `site.ini`, `override.ini`, `image.ini` und
`i18n.ini` für Sprachen. Der Dateiname sollte auf `.ini.append.php` enden. Der Grund, den das Buch nennt, ist immer
noch der Grund: Würde ein Webserver die Datei jemals ausliefern, liefe sie als PHP, und die Einstellungen stehen als
PHP-Kommentare darin, sodass nichts angezeigt wird.

Was sich geändert hat: Sie müssen diese Dateien selten von Hand bearbeiten. `./console exp:ini` ändert eine Einstellung
mit einer Sicherung und zeigt den Unterschied ([Einstellungen mit exp:ini ändern](../erweiterungsentwicklung/teil-2-builder/der-befehl-exp-ini.md)),
und die Administration hat eine Einstellungsseite ([Einstellungen in der Administration](../erweiterungsentwicklung/teil-2-builder/einstellungen-in-der-administration.md)).
Die Einzelheiten der Reihenfolge stehen in [Geltungsbereiche von Einstellungen und ihre Reihenfolge](../erweiterungsentwicklung/teil-2-builder/einstellungsebenen-und-ihre-reihenfolge.md).

## Weiter

[Der Setup-Assistent, damals und heute](der-setup-assistent-damals-und-heute.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 1, „Installing eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: gekürzt und umgestellt; jeder Schritt, jeder Pfad und jede Einstellung gegen Exponential 6 geprüft und aktualisiert; das Produkt heißt Exponential.
