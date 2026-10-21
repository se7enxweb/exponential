---
title: Control Panels: Überblick
audience: hosting
category: hosting
order: 10
summary: Der Überblick über den Bereich Control Panels - welche Hosting-Panels die Quellen von Exponential erwähnen und in welcher Stufe (Plesk Stufe 1, cPanel und DirectAdmin Stufe 3, ISPConfig und Webmin nicht erwähnt), was jedes Panel für eine Exponential-Website bereitstellen muss, und eine Zeile zu jeder Seite.
keywords: Control Panel, Hosting-Panel, Plesk, cPanel, DirectAdmin, ISPConfig, Webmin, Stufe, PHP-Auswahl, PHP-FPM-Pool, Cron, Shell-Zugang, Datenbanken, TLS-Zertifikate, Überblick, control panel, hosting panel, tier, PHP selection, PHP-FPM pool, shell access, databases, TLS certificates, overview
type: help
---
# Control Panels: Überblick

Dieser Bereich ist für Hosting-Anbieter, die Exponential-Websites auf Servern betreiben, die von einem Hosting-Control-Panel
verwaltet werden, und für ihre Support-Mitarbeitenden. Er beantwortet, welche Panels die Quellen des Projekts kennen, wie weit
jedes geprüft ist und wie die alltäglichen Aufgaben im Panel (PHP wählen, Cron einrichten, Datenbanken anlegen, Zertifikate
installieren) für eine Exponential-Website gelten.

Zurück zu [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md).

> **Hinweis**
> Dieser Bereich nennt nur, was die Quellen von Exponential über jedes Panel sagen. Für alles innerhalb eines Panels, was die
> Quellen nicht beschreiben (Menünamen, Bildschirme, Optionen), lesen Sie in der Dokumentation Ihres Panels nach. Wo diese
> Seiten betriebliche Ratschläge geben, die die Quellen nicht abdecken, sagen sie das.

## Inhalt

1. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
2. [Panels in den Quellen](#panels-in-den-quellen)
3. [Was jedes Panel bereitstellen muss](#was-jedes-panel-bereitstellen-muss)
4. [Velocity auf einem Server mit Panel](#velocity-auf-einem-server-mit-panel)

## Die Seiten dieses Bereichs

| Seite | Was sie beantwortet |
|---|---|
| [Plesk](plesk.md) | Das Panel des eigenen Entwicklungsservers des Projekts: PHP-Pfade, Pools pro Domain, die Dienste `plesk-php<XY>-fpm`, Zertifikate, Logs |
| [cPanel](cpanel.md) | Was die Quellen sagen (Stufe 3, PHP aus EasyApache) und wie Sie es angehen |
| [DirectAdmin](directadmin.md) | Was die Quellen sagen (Stufe 3) und wie Sie es angehen |
| [ISPConfig](ispconfig.md) | In den Quellen nicht erwähnt; die allgemeinen Anforderungen |
| [Webmin](webmin.md) | In den Quellen nicht erwähnt; die allgemeinen Anforderungen |
| [PHP-Auswahl und FPM-Pools](php-auswahl-und-fpm-pools.md) | Die PHP-Version der Website für Web und Kommandozeile, ein Pool pro Website, den richtigen Dienst neu laden |
| [Cron und Shell-Zugang](cron-und-shell-zugang.md) | `runcronjobs.php` mit dem richtigen PHP planen, und was ohne Shell funktioniert |
| [Datenbanken und Benutzer](datenbanken-und-benutzer.md) | Die Datenbank und ihren Benutzer für jede Engine anlegen, in der offiziellen Reihenfolge |
| [TLS-Zertifikate in Panels](tls-zertifikate-in-panels.md) | Zertifikate aus dem Panel, und wann stattdessen Velocity oder FrankenPHP HTTPS ausliefert |

## Panels in den Quellen

| Panel | Stufe in den Quellen | Was die Quellen sagen |
|---|---|---|
| Plesk | **1** (hier getestet) auf AlmaLinux 9; Plesk-Hosts auf den Distributionen der Stufe 2 sind Stufe 2 | Der Entwicklungsserver des Projekts: Apache 2.4 mit PHP-FPM 8.5 aus dem Plesk-Panel und Exponential Velocity auf derselben Installation |
| cPanel | 3 (funktioniert erwartungsgemäß) | EasyApache (EA-PHP 8.x), PHP pro Konto; Shared Hosting kann lang laufende Prozesse verbieten, sodass Velocity womöglich nicht erlaubt ist; nutzen Sie Apache oder LiteSpeed mit `.htaccess` |
| DirectAdmin | 3 (funktioniert erwartungsgemäß) | dieselbe Zeile wie cPanel |
| ISPConfig | nicht eingestuft | in den Quellen nicht erwähnt |
| Webmin | nicht eingestuft | in den Quellen nicht erwähnt |

Quelle: [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md) (Stufen 1 bis 3 und „Container, Cloud und Hosting-Panels“).
Stufe 3 ist eine begründete Erwartung, kein Testergebnis; ein Panel, das nicht eingestuft ist, hat überhaupt keine Aussage. Planen
Sie eine Testinstallation, bevor Sie eines davon anbieten.

## Was jedes Panel bereitstellen muss

Welches Panel auch immer: Eine Exponential-Website braucht ([Kapitel 2](../../../installation/02-voraussetzungen.md)):

| Bedarf | In der Sprache eines Panels |
|---|---|
| PHP 8.0 oder später (auch für Velocity) mit den erforderlichen Erweiterungen | die PHP-Auswahl und die Schalter für Erweiterungen des Panels, für die Website und für ihre Kommandozeile |
| `php.ini`: `memory_limit` 64M oder mehr, `max_execution_time` 30 oder mehr, `date.timezone`, `file_uploads`, `allow_url_fopen` | die PHP-Einstellungen pro Website im Panel |
| Rewrite-Regeln: nur aufgeführte Asset-Pfade als Dateien ausgeliefert, alles andere an `index.php` | `.htaccess` aus `.htaccess_root` unter Apache oder LiteSpeed; für nginx der abgeleitete Server-Block ([8.6](../../../installation/08-die-website-ausliefern.md#86-nginx-mit-php-fpm)) |
| Der Document Root im Wurzelverzeichnis der Installation | die Einstellung des Panels für den Document Root der Domain |
| Eine Datenbank der sechs, in der offiziellen Reihenfolge: 1. SQLite 3 (der Standard, kein Server) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL; Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich | die Datenbankseite des Panels, oder nichts für SQLite |
| Beschreibbare `var/` und `settings/` für den Benutzer der Website | der eigene Benutzer des Kontos |
| Geplantes `runcronjobs.php` | die Cron-Seite des Panels |
| HTTPS | die Zertifikatsverwaltung des Panels |

## Velocity auf einem Server mit Panel

Panels verwalten Apache oder nginx und ihre PHP-FPM-Pools. Velocity ist ein getrennter, lang laufender Prozess; auf einem Server
mit Panel läuft es daher **neben** dem Webserver des Panels, wie auf dem Entwicklungsserver des Projekts, wo das Apache von Plesk
mit PHP-FPM und Velocity dieselbe Installation ausliefern ([Stufe 1](../../betriebssystemunterstuetzung.md#stufe-1-hier-getestet)). Dann:

- müssen beide `var/` als derselbe Benutzer schreiben ([8.7.2](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe));
- muss ein Ausrollen das richtige PHP-FPM neu laden und Velocity neu starten; `exp:velocity deploy` tut beides in Reihenfolge
  ([8.3.14](../../../installation/08-die-website-ausliefern.md#8314-eine-änderung-bereitstellen-expvelocity-deploy));
- muss die Firewall des Panels die Ports von Velocity erlauben, wenn Besucher ihn direkt erreichen.

Siehe [Velocity neben Apache oder nginx](../server/velocity-neben-apache-oder-nginx.md).

## Verwandte Seiten

- [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md)
- [Shared Hosting](../modelle/shared-hosting.md)
- [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
