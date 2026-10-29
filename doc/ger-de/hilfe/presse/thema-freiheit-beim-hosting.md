---
title: "Thema: Freiheit beim Hosting"
audience: press
category: press-media
order: 67
summary: Ein Themenbeitrag darüber, wo Exponential laufen kann - jeder Webserver, der URLs umschreibt, oder das eigene Velocity, sechs Datenbanken, fast jedes Betriebssystem mit PHP 8.0, Shared Hosting mit Grenzen und keine Bindung an einen Anbieter - mit druckbaren Fakten, Quellen und den ehrlichen Grenzen.
keywords: Thema, Hosting, Freiheit beim Hosting, keine Anbieterbindung, Webserver, Datenbanken, Betriebssysteme, Shared Hosting, Container, Cloud, Presse, story, hosting freedom, no lock-in, web servers, databases, operating systems, shared hosting, containers, cloud, press
type: help
---
# Thema: Freiheit beim Hosting

Diese Seite ist für Journalisten, die über Anbieterbindung, digitale Souveränität oder den Hosting-Markt schreiben.
Exponential dokumentiert eine breite Auswahl an Webservern, Datenbanken und Betriebssystemen und auch die Grenzen
dieser Auswahl. Diese Seite gibt beides, mit Quellen.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Die Auswahl auf einen Blick](#die-auswahl-auf-einen-blick)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Die ehrlichen Grenzen](#die-ehrlichen-grenzen)
5. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
7. [Wen Sie fragen](#wen-sie-fragen)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

„Exponential 6.0.x läuft auf fast jedem Betriebssystem, das PHP 7.4 oder neuer und einen Webserver hat. Es besteht aus
einfachen PHP-Dateien plus einer Datenbank; es hat keinen eigenen kompilierten Code“
([Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md)). Es läuft hinter „jedem Webserver, der eine URL umschreiben kann“,
oder unter seinem eigenen Server Velocity
([README](https://github.com/se7enxweb/exponential/blob/main/README.md#the-traditional-options)), auf sechs Datenbanken
von SQLite bis Oracle ([Datenbankunterstützung](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge)), und der Code
steht unter der GPL, sodass jeder es hosten darf. 7x verkauft Managed Hosting, aber „jeder“ darf es hosten; „Sie
wählen“ ([Gesprächspunkte: Hosting](../fuersprecher/gespraechspunkte-hosting-datenbanken-freiheit.md#fragen-die-sie-hören-werden)).

## Die Auswahl auf einen Blick

| Ebene | Auswahl in den Quellen | Quelle |
|---|---|---|
| Webserver | Apache, Nginx, LiteSpeed, OpenLiteSpeed, Caddy, IIS, lighttpd, H2O, Traefik davor, oder Velocity; FrankenPHP als alternative Engine | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#the-traditional-options) |
| Datenbank | SQLite 3 (Standard), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Datenbankunterstützung](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge) |
| Betriebssystem | Vier Stufen, von „hier getestet“ bis „heute nicht möglich“ | [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md#die-vier-stufen) |
| PHP | 8.0 bis 8.5, Velocity eingeschlossen (ab seinem Release 0.0.4.54) | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#what-version-of-php-is-required) |
| Wo | Container, Cloud-Maschinen und Hosting-Panels sind abgedeckt | [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels) |

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Nichts in den Anforderungen nennt ein Betriebssystem. | [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md#was-das-betriebssystem-bereitstellen-muss) |
| Stufe 1 („hier getestet“) ist AlmaLinux 9 auf dem Entwicklungsserver des Projekts und Ubuntu in der CI. | [Unterstützte Betriebssysteme, Stufe 1](../betriebssystemunterstuetzung.md#stufe-1-hier-getestet) |
| Velocity liefert `.deb`-Pakete für Debian 12 und 13 sowie Ubuntu 22.04 und 24.04. | [Unterstützte Betriebssysteme, Stufe 2](../betriebssystemunterstuetzung.md#linux-distributionen) |
| Eine Website kann mit dokumentierten Werkzeugen auf eine andere Datenbank umziehen. | [Eine Website auf eine andere Datenbank umziehen](../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen) |
| Backups sind die üblichen Dumps der jeweiligen Datenbank. | [Backup und Wiederherstellung](../datenbankunterstuetzung.md#backup-und-wiederherstellung) |
| „Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos.“ | [Wer ist 7x?, Open-Source-Lizenzen](../wer-ist-7x.md#open-source-lizenzen) |

## Die ehrlichen Grenzen

- **Shared Hosting**: Apache oder LiteSpeed mit `.htaccess` können es ausliefern, und der Einrichtungsassistent
  installiert es ohne Shell; Velocity kann dort meist nicht laufen.
- Systeme der **Stufe 3** sind „eine begründete Erwartung, kein Testergebnis“
  ([Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md#die-vier-stufen)).
- **MongoDB** hat dokumentierte Grenzen ([Grenzen von MongoDB](../datenbankunterstuetzung.md#grenzen-von-mongodb)).
- **Microsoft SQL Server** ist „mit zusätzlicher Arbeit möglich“, aber keine der sechs.

## Was Sie nicht behaupten sollten

- „Läuft überall.“ Nennen Sie die Stufen und die Grenzen.
- „Null Anbieterbindung.“ Jede Website hängt von ihrem Inhaltsmodell, ihren Templates und Erweiterungen ab; sagen Sie
  stattdessen, dass sich Code, Datenbanken und Hoster mit dokumentierten Werkzeugen wechseln lassen.
- „Zertifiziert auf“ einer Plattform: Die Quellen beschreiben Stufen, keine Zertifizierungen.

## Blickwinkel und Ideen für Überschriften

```text
„Ihr Server, Ihre Datenbank, Ihr Land: Freiheit beim Hosting in einem Open-Source-CMS“
„Vier Stufen Ehrlichkeit: wie Exponential dokumentiert, wo es läuft“
„Vom Laptop mit SQLite bis Oracle: ein CMS, sechs Datenbanken“
```

## Wen Sie fragen

| Frage | Fragen Sie | Wo |
|---|---|---|
| Plattformunterstützung, Managed Hosting | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Ein System, das einer Stufe hinzugefügt werden soll | den öffentlichen Tracker | <https://github.com/se7enxweb/exponential/issues> |

## Verwandte Seiten

- [Gesprächspunkte: Hosting, Datenbanken und Freiheit](../fuersprecher/gespraechspunkte-hosting-datenbanken-freiheit.md)
- [Thema: sechs Datenbanken](thema-sechs-datenbanken.md)
- [Thema: Betriebssysteme](thema-betriebssysteme.md)
- [Themenbeiträge: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
