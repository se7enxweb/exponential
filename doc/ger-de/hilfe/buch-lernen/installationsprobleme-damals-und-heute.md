---
title: "Installationsprobleme, damals und heute"
audience: user, developer, admin, builder
category: books
order: 120
summary: Die beiden Probleme, die das erste Kapitel des Buchs Learning behandelt (PHP-Speichergrenze, PHP als CGI), wie sie in Exponential 6 aussehen und wo Sie zuerst nachsehen.
keywords: Fehlerbehebung, Speichergrenze, memory_limit, CGI, FPM, Startseite für jeden Link, php.ini, Fehlerprotokoll, Buch Learning, troubleshooting, memory limit, front page for every link, error log, Learning book
type: help
---
# Installationsprobleme, damals und heute

Das Buch beendet sein erstes Kapitel mit zwei häufigen Problemen. Beide treten in irgendeiner Form noch auf.

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

## PHP-Speichergrenze

**Damals.** Die Standardgrenze von PHP lag bei 8 MB, zu wenig. Das Symptom war eine fatale Meldung „Allowed memory
size ... exhausted“. Das Buch erhöhte sie mit `php_value memory_limit "16M"` in einer `.htaccess`-Datei auf 16 MB und
meinte, 32 MB seien reichlich.

**Heute.** Exponential braucht weit mehr, und der Systemtest des Setup-Assistenten prüft das: Unter 128 MB ist ein
Fehler, unter 256 MB eine Warnung ([Systeminformationen](../../anleitungen/systeminformationen.md)). Setzen Sie
`memory_limit` dort, wo PHP seine Einstellungen liest; das hängt davon ab, wie PHP läuft:

| PHP läuft als | Wo Sie es setzen |
|---|---|
| PHP-FPM oder CLI | `php.ini` dieses PHP oder eine Pool-Datei; laden Sie FPM danach neu |
| Apache-Modul | `php.ini` oder `php_value` in `.htaccess` (nur, wenn der Host es erlaubt) |
| Exponential Velocity | das PHP, das es startet; seine eigene `php.ini` |

`php -i | grep memory_limit` zeigt den Wert der Kommandozeile; die Seite **Setup > Systeminformationen** zeigt den Wert,
den das PHP des Webservers verwendet, und auf diesen kommt es für die Website an (die beiden unterscheiden sich oft).
Ein Kommandozeilenskript wie ein Import kann mehr brauchen als eine Seite; führen Sie es mit
`php -d memory_limit=1G bin/php/<script>.php` aus.

## PHP läuft als CGI

**Damals.** Welchen Link Sie auch anklickten, Sie bekamen die Startseite. Die Ursache war, wie alte Versionen den
Anfragepfad lasen, wenn PHP als CGI statt als Apache-Modul lief, und die Abhilfe bestand darin, eine Kernel-Datei zu
bearbeiten.

**Heute.** Mit PHP-FPM oder einem Apache-Modul ist dieses Symptom selten. Wenn Sie für jede Adresse die Startseite
sehen, prüfen Sie zuerst die Rewrite-Regeln: Alle Anfragen, die keine echte Datei sind, müssen `index.php` erreichen
(siehe die mitgelieferte `.htaccess` und die Zeile „404 für jede Seite unter Apache“ in
[Fehlerbehebung](../../installation/12-fehlerbehebung.md#129-seiten-und-fehler-zur-laufzeit)). Die Seite **Setup >
Systeminformationen** zeigt, wie der Webserver PHP ausführt (die SAPI); so unterscheiden Sie ein Modul von FPM oder CGI.


## Wo Sie zuerst nachsehen

1. `var/log/error.log` ([Wo die Protokolle liegen](../../installation/12-fehlerbehebung.md#122-wo-die-logs-liegen)).
2. Die Debug-Ausgabe, für Sie selbst, nicht für Besucher.
3. [Fehlerbehebung bei der Installation](../../installation/12-fehlerbehebung.md), die vollständige Liste nach Symptom.

Zurück zu [den Kapiteln des Buchs Learning](README.md).


## Quelle und Lizenz

Bearbeitet nach Kapitel 1, „Installing eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: beide Probleme für Exponential 6 neu beschrieben, mit aktuellen Grenzen und Werkzeugen.
