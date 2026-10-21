---
title: Webmin
audience: hosting
category: hosting
order: 60
summary: Webmin wird in den Quellen von Exponential nicht erwähnt und ist in keine Stufe eingeordnet. Diese Seite sagt das, erklärt, warum die eigenen Anleitungen des Installationsbuchs für Apache, nginx, PHP-FPM, Cron und Zertifikate auf einem solchen Server die Referenz sind, und nennt die Reihenfolge der Arbeit und einen Testplan.
keywords: Webmin, nicht getestet, keine Stufe, Serververwaltung, Apache, nginx, PHP-FPM, systemd, Cron, Zertifikate, Konfigurationsdateien, Installationsbuch, Testplan, not tested, no tier, server administration, certificates, configuration files, installation book, test plan
type: help
---
# Webmin

Diese Seite ist für Hosting-Anbieter und Administratoren, deren Server über Webmin verwaltet werden und die Exponential darauf betreiben
möchten. **Die Quellen von Exponential erwähnen Webmin nicht.** Es ist in keine Stufe eingeordnet, und nichts hier stammt aus einem
Test. Die Seite erklärt, wie Sie trotzdem vorgehen: Auf einem Server, auf dem Sie die Konfiguration von Webserver, PHP und Cron selbst
bearbeiten (über welche Oberfläche auch immer), sind die eigenen Anleitungen des Installationsbuchs die Referenz, und diese Seite
listet sie in der Reihenfolge auf, in der Sie sie brauchen.

> **Hinweis**
> Nichts auf dieser Seite beschreibt die Module oder Bildschirme von Webmin. Wie Webmin eine bestimmte Konfigurationsdatei oder einen
> Dienst bearbeitet, lesen Sie in der Dokumentation von Webmin nach. Welche Oberfläche Sie auch nutzen: Die Dateien und Befehle unten
> sind das, was die Quellen von Exponential beschreiben.

## Inhalt

1. [Stand in den Quellen](#stand-in-den-quellen)
2. [Das Installationsbuch ist die Referenz](#das-installationsbuch-ist-die-referenz)
3. [Reihenfolge der Arbeit](#reihenfolge-der-arbeit)
4. [Was eine Verwaltungsoberfläche nicht rückgängig machen darf](#was-eine-verwaltungsoberfläche-nicht-rückgängig-machen-darf)
5. [Ein Testplan](#ein-testplan)
6. [Checkliste](#checkliste)

## Stand in den Quellen

| Frage | Antwort |
|---|---|
| Wird Webmin in der Dokumentation oder im Code genannt? | nein |
| Welche Stufe? | keine: es ist nicht eingeordnet |
| Was entscheidet stattdessen über die Unterstützung? | das Betriebssystem und der Webserver darunter, die in Stufen eingeordnet sind ([Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md)) |

Wenn die Distribution darunter Stufe 1 oder 2 ist (zum Beispiel AlmaLinux 9, Red Hat Enterprise Linux 9 und 10, Debian 12 und 13,
Ubuntu 22.04 und 24.04) und der Webserver Apache mit PHP-FPM (Stufe 1) oder nginx mit PHP-FPM (Stufe 2), ist der Stack, den
Exponential sieht, ein eingeordneter; nur die Verwaltungsoberfläche obendrauf ist es nicht.

## Das Installationsbuch ist die Referenz

| Aufgabe | Wo das Buch sie beschreibt |
|---|---|
| PHP-Pakete je Distribution | [2.9 Hinweise zu Betriebssystemen](../../../installation/02-voraussetzungen.md#29-hinweise-zu-betriebssystemen) |
| `php.ini`-Werte | [2.5](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini) |
| Virtueller Host für Apache mit PHP-FPM | [8.5.1 Schritt für Schritt](../../../installation/08-die-website-ausliefern.md#851-schritt-für-schritt) |
| Server-Block für nginx (abgeleitet, nicht mitgeliefert) | [8.6](../../../installation/08-die-website-ausliefern.md#86-nginx-mit-php-fpm) |
| Einstellungen und Neuladen von PHP-FPM | [8.5.2](../../../installation/08-die-website-ausliefern.md#852-php-fpm) |
| Velocity, mit einer systemd-Unit | [8.3](../../../installation/08-die-website-ausliefern.md#83-exponential-velocity-der-empfohlene-weg), [8.3.11](../../../installation/08-die-website-ausliefern.md#8311-velocity-als-dienst-betreiben) |
| Dateiberechtigungen und Eigentum | [8.7](../../../installation/08-die-website-ausliefern.md#87-dateiberechtigungen-und-eigentümer) |
| Datenbanken: Datenbank und Benutzer anlegen | [Kapitel 9](../../../installation/09-datenbanken.md) |
| Crontab | [10.3 Cronjobs](../../../installation/10-nach-der-installation.md#103-cronjobs) |
| HTTPS und Header | [13.9](../../../installation/13-sicherheitshaertung.md#139-sicherheits-header-und-https) |

Auf einem solchen Server haben Sie außerdem die volle Wahl der Engines: Exponential Velocity, für jede Phase empfohlen, braucht keinen
getrennten Webserver und liefert HTTPS selbst aus ([8.1](../../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle)).

## Reihenfolge der Arbeit

1. **PHP** 8.0 oder später (auch für Velocity) mit den erforderlichen Erweiterungen, für Web und Kommandozeile gleich.
2. **Datenbank**, in der offiziellen Reihenfolge: 1. SQLite 3 (der Standard, kein Server) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB ·
   5. Oracle · 6. MySQL; Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich. Legen Sie die Datenbank und
   ihren Benutzer vor der Installation an ([Datenbanken und Benutzer](datenbanken-und-benutzer.md)).
3. **Code**: `git clone` und `composer install`, als Benutzer der Website ([Kapitel 3](../../../installation/03-den-code-beschaffen.md)).
4. **Installieren**: `exp:install`, der Kickstarter oder der Setup-Assistent ([Kapitel 4](../../../installation/04-installationsweg-waehlen.md)).
5. **Ausliefern**: Velocity mit einem Startdienst, oder Apache/nginx mit einem PHP-FPM-Pool, der als Benutzer der Website läuft.
6. **Cron**: Zeilen für `runcronjobs.php` in der Crontab des Benutzers der Website.
7. **HTTPS**: ein Zertifikat für jeden Namen; HTTP umleiten; HSTS, sobald alles funktioniert.
8. **Backups**: Datenbank und `var/storage`, `settings/override`, `settings/siteaccess`, einmal anderswo wiederhergestellt.

```bash
php -v
php bin/php/console exp:velocity ext check
php bin/php/console exp:install --dry-run
```

`exp:install --dry-run` prüft Konfiguration und Pakete und schreibt nichts
([4.6](../../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)).

## Was eine Verwaltungsoberfläche nicht rückgängig machen darf

Jedes Werkzeug, das Konfigurationsdateien neu schreibt, kann eine von Hand vorgenommene Änderung stillschweigend zurücknehmen. Für
Exponential schützen Sie diese:

| Einstellung | Warum |
|---|---|
| Die Rewrite-Regeln (`.htaccess` oder der nginx-Block) | sie entscheiden, welche Dateien ausgeliefert werden; ohne sie liegen `settings/*.ini` und die SQLite-Datenbank offen ([13.2](../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf)) |
| Der `user` des PHP-FPM-Pools | ein Benutzer schreibt `var/` ([8.7.2](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)) |
| `AllowOverride` für den Document Root (Apache) | `.htaccess` muss gelesen werden |
| Die Crontab-Zeilen des Benutzers der Website | geplantes Veröffentlichen, Benachrichtigungen, Aufräumen |
| Firewall-Regeln für die Ports von Velocity, wenn Besucher Velocity direkt erreichen | fügen Sie den Port mit dem Werkzeug hinzu, dem der Regelsatz auf der Maschine gehört ([FrankenPHP](../../../../eng-us/bc/6.0/frankenphp.md)) |

> **Tipp**
> Führen Sie nach jeder Konfigurationsänderung über eine Oberfläche erneut die Prüfung auf offengelegte Dateien aus
> [13.2](../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf) aus. Sie dauert Sekunden und fängt
> den gefährlichsten Rückschritt ab.

## Ein Testplan

| Schritt | Besteht, wenn |
|---|---|
| Installieren | jeder kritische Test besteht; Website und `/admin` antworten |
| Prüfung auf offengelegte Dateien von außen | 404 oder eine Exponential-Seite für jeden Pfad, nie die Datei |
| Den Server neu starten | die Website und, falls genutzt, Velocity kommen von selbst wieder |
| Eine PHP-Einstellung über die Oberfläche ändern | die Änderung ist nach dem Neuladen in **Setup > Systeminformationen** sichtbar |
| Cron | Läufe erscheinen auf **Setup > Cronjobs** |
| Sichern und wiederherstellen | die wiederhergestellte Website funktioniert auf einer anderen Maschine |

## Checkliste

- [ ] Distribution und Webserver einer eingeordneten Stufe unter der Oberfläche.
- [ ] Die Schritte des Installationsbuchs für PHP, Server, Berechtigungen, Cron und HTTPS befolgt.
- [ ] Rewrite-Regeln, Benutzer des Pools und Crontab vor dem Überschreiben geschützt.
- [ ] Prüfung auf offengelegte Dateien nach jeder Konfigurationsänderung wiederholt.
- [ ] Der Testplan hat bestanden.

## Verwandte Seiten

- [Control Panels: Überblick](README.md), [ISPConfig](ispconfig.md)
- [VPS-Hosting](../modelle/vps-hosting.md), [Dedizierte Server](../modelle/dedizierte-server.md)
- [Checkliste: neuer Server](../checklisten/checkliste-fuer-einen-neuen-server.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
