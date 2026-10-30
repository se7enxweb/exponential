---
title: "Der Setup-Assistent, damals und heute"
audience: user, developer, admin, builder
category: books
order: 110
summary: Die zwölf Seiten des Setup-Assistenten im Buch Learning, den Seiten des Assistenten von Exponential 6 zugeordnet, mit dem, was sich geändert hat (Datenbankauswahl, Site-Pakete, Siteaccess-Methoden), und den Befehlen, die den Assistenten überspringen.
keywords: Setup-Assistent, Installationsprogramm, Systemtest, Datenbank, Sprache, Site-Paket, Siteaccess, URL, Port, Hostname, Kickstarter, Buch Learning, setup wizard, installer, system check, database, language, site package, siteaccess, hostname, kickstarter, exp:install, Learning book
type: help
---
# Der Setup-Assistent, damals und heute

Das Buch geht den Assistenten Seite für Seite durch. Den Assistenten gibt es noch, mit mehr Seiten und mehr
Auswahlmöglichkeiten, und zwei Befehle können ihn für Sie ausfüllen. Diese Seite stellt die Seiten des Buchs denen von
heute gegenüber. Die genauen Bildschirme, Felder und Meldungen stehen in [Der Setup-Assistent](../../installation/05-setup-assistent.md)
im Installationsbuch; lesen Sie das, wenn Sie installieren.

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

## Die Seiten

| Buch (3.x) | Exponential 6 | Was sie abfragt |
|---|---|---|
| 1 Welcome | Willkommen | Die Sprache des Assistenten. Eine Schaltfläche **Feineinstellungen** erscheint, wenn ein optionaler Test fehlgeschlagen ist. |
| 2 System check | Systemtest | Verzeichnisse, in die der Server schreiben muss (`var/`, `settings/` und weitere), PHP-Version und -Erweiterungen, `memory_limit`. |
| (keine) | Feineinstellung des Systems | Hinweise zu den optionalen Tests, die nicht bestanden wurden. |
| 3 E-mail settings | Postausgang | Wie E-Mails die Website verlassen: sendmail, SMTP oder der Datei-Transport, der E-Mails nur in Dateien schreibt. |
| 4 Choose a database | Datenbanksystem auswählen | MySQL oder MariaDB, PostgreSQL, SQLite, MongoDB, Oracle. |
| 5 Database initialization | Initialisierung der Datenbank | Host, Benutzer, Passwort, Datenbank; was geschehen soll, wenn sie bereits Daten enthält. |
| 6 Language support | Sprachunterstützung | Die Standardsprache und weitere; weitere fügen Sie später in der Administration hinzu. |
| 7 Site packages | Site Paket, Sprachoptionen | Eine fertige Website zum Installieren. |
| 8 Site access configuration | Konfiguration der Siteaccesses | URL, Port oder Hostname. |
| 9 Site details | Seitendetails, Administrator der Seite | Titel, URL, Datenbankname, Administratorkonto. |
| 10 Site security | Seitensicherheit | Die Datei `.htaccess`, wenn Sie keinen virtuellen Host verwenden. |
| 11 Site registration | Registrierung | Optional. |
| 12 Finished | Erstellung der Seiten, Abgeschlossen | Die Adressen der neuen Website und ihrer Administration. |

## Was die Ratschläge des Buchs heute bedeuten

**Der Systemtest.** Das Buch sagt, man solle dem Webserver Schreibrechte auf `var`, `settings` und `design` geben, mit
`chmod 777` als schnellem (und auf geteiltem Hosting unsicherem) Weg und `chown` auf den Benutzer des Webservers als
besserem Weg. Bleiben Sie beim besseren Weg: Geben Sie dem Benutzer des Webservers (oder dem Benutzer des PHP-FPM-Pools)
den Besitz der Verzeichnisse, in die er schreiben muss, und nicht mehr. Niemals `777` auf einem Server mit anderen
Benutzern. Siehe [Berechtigungen und Besitz](../../installation/12-fehlerbehebung.md#123-berechtigungen-und-eigentümerschaft).

**Eine Datenbank wählen.** Der Rat des Buchs „Wenn Sie keinen Grund für PostgreSQL haben, brauchen Sie es
wahrscheinlich nicht“ lässt sich verallgemeinern: Wählen Sie, was Sie betreiben und sichern können. SQLite braucht
keinen Server und ist richtig zum Ausprobieren einer Website und für kleine Websites; MySQL oder MariaDB ist die übliche
Wahl für den Produktivbetrieb ([Datenbanken](../../installation/09-datenbanken.md)).

**Sprachen.** Unverändert: Fügen Sie keine Sprachen hinzu, bei denen Sie nicht sicher sind; Sie können [später eine hinzufügen](../handbuch/wie-fuege-ich-eine-inhaltssprache-hinzu.md).

**Site-Pakete.** Das Buch nennt acht Pakete (Blog, Corporate, Forum, Gallery, Intranet, News, Plain, Webshop) und
empfiehlt *Plain*, um von Grund auf zu bauen. Die Pakete einer Exponential-Installation sind andere: Standard ist
`sevenx_multisite`, und das Installationsprogramm bietet die Pakete an, die es in den erreichbaren Paket-Repositories
findet ([Pakete](../../anleitungen/pakete.md)). Für eine Website, die aus dem Nichts gebaut wird, wählen Sie das kleinste
angebotene Paket und lesen [Eine Website ohne Code bauen](../erweiterungsentwicklung/teil-2-builder/eine-website-ohne-code-bauen.md).

**Siteaccess.** Die drei Zugriffsarten des Buchs sind unverändert. **URL** (`/admin`) ist die einfachste; **Port** und
**Hostname** brauchen eine Konfiguration des Webservers (und, für Hostnamen, DNS). Jede Installation erhält heute drei
Siteaccesses: `site` für Besucher, `admin` für die Administration und `editor` für Redakteure. Siehe
[Siteaccess-Einstellungen](../erweiterungsentwicklung/teil-2-builder/siteaccess-einstellungen.md).

**Vorhandene Daten.** Wenn die Datenbank bereits Daten enthält, fragt der Assistent wie im Buch, was geschehen soll:
ergänzen, entfernen, unverändert lassen. Erstellen Sie eine Sicherung, bevor Sie wählen.

## Den Assistenten überspringen

- `./console exp:install --apply` installiert von der Kommandozeile, mit der Datenbank als Optionen
  ([Installation über die Konsole](../../installation/07-installation-ueber-die-konsole.md)).
- Der Kickstarter liest `kickstart.ini` und beantwortet die Seiten des Assistenten für Sie
  ([Kickstarter](../../installation/06-kickstarter.md)).

## Weiter

[Installationsprobleme, damals und heute](installationsprobleme-damals-und-heute.md).


## Quelle und Lizenz

Bearbeitet nach Kapitel 1, „Installing eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: die Seiten Exponential 6 zugeordnet; Ratschläge zu Berechtigungen, Datenbanken und Site-Paketen aktualisiert.
