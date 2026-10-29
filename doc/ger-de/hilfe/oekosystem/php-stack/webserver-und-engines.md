---
title: Webserver und Engines im Ökosystem
audience: ecosystem, admin, hosting, developer
category: ecosystem
order: 33
summary: Die Wege, auf denen eine Exponential-Website ausgeliefert wird - Velocity mit seinen Engines Qbix, FrankenPHP und dem eingebauten Server von PHP sowie die klassischen Server Apache, Nginx, LiteSpeed, Caddy, IIS, lighttpd, H2O und Traefik -, mit dem, wofür jeder da ist.
keywords: Webserver, Engines, Velocity, Qbix, FrankenPHP, eingebauter PHP-Server, Apache, Nginx, LiteSpeed, Caddy, IIS, lighttpd, H2O, Traefik, PHP-FPM, Rewrite, web servers, engines, PHP built-in server, rewrite
type: help
---
# Webserver und Engines im Ökosystem

"Exponential runs behind any web server that can rewrite a URL" und "does not require any particular one", Exponential
läuft also hinter jedem Webserver, der eine URL umschreiben kann, und verlangt keinen bestimmten (`README.md`, "How
Exponential is served").

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../../beispiele/webserver/nginx.md), [LiteSpeed](../../../beispiele/webserver/litespeed.md), [Caddy](../../../beispiele/webserver/caddy.md), [IIS](../../../beispiele/webserver/iis.md),
> [Velocity](../../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hosting/control-panels/plesk.md), [cPanel](../../hosting/control-panels/cpanel.md), [DirectAdmin](../../hosting/control-panels/directadmin.md), [ISPConfig](../../hosting/control-panels/ispconfig.md).

## Inhalt

1. [Velocity und seine Engines](#velocity-und-seine-engines)
2. [Klassische Server](#klassische-server)
3. [Wählen](#wählen)

## Velocity und seine Engines

| Engine | Rolle |
|---|---|
| Qbix | Der eigene Server von Velocity; "The recommended engine for every stage, from development to production, and the fastest", die empfohlene und schnellste Engine für jede Phase |
| FrankenPHP | "The production-ready alternative", die produktionsreife Alternative |
| Der eingebaute Server von PHP | Für die Entwicklung; "the shipped default because it always works", der mitgelieferte Standard, weil er immer funktioniert |

Mehr: [Exponential Velocity](../produkte/exponential-velocity.md), [Velocity-Engines](../../../bc/6.0/velocity-engines.md).

## Klassische Server

| Server | Wie er umschreibt | Hinweise aus der README |
|---|---|---|
| Apache HTTP Server | `mod_rewrite`, `.htaccess` | "The most widely used; `.htaccess` ships with Exponential", der am weitesten verbreitete; `.htaccess` wird mit Exponential geliefert |
| Nginx | `try_files` / `rewrite` | Regeln im Server-Block |
| LiteSpeed, OpenLiteSpeed | `.htaccess`-kompatibel | Liest Apache-Regeln in den meisten Fällen |
| Caddy | `rewrite` / `handle` / `try_files` | Automatisches HTTPS |
| Microsoft IIS | URL Rewrite Module (`web.config`) | Windows-Hosting |
| lighttpd | `mod_rewrite` | Geringer Ressourcenbedarf |
| H2O | `redirect` / `file.custom-handler` | Auf HTTP/2 ausgerichtet |
| Traefik | `ReplacePathRegex` | Meist vor einem anderen Server |

Der eingebaute Server von PHP mit `php -S` ist "Development only [...] Never expose it to the internet", nur für die
Entwicklung und nie ins Internet zu stellen (`README.md`).

## Wählen

| Situation | Wahl |
|---|---|
| Ihr eigener Server oder Ihre eigene VM | Velocity mit der Engine Qbix |
| Sie bevorzugen ein einzelnes Binary auf Basis von Caddy | Velocity mit FrankenPHP |
| Shared Hosting ohne langlebige Prozesse | Apache oder LiteSpeed mit `.htaccess` |
| Mehrere Kunden, die Isolation auf Betriebssystemebene brauchen | Eine Velocity-Instanz pro Website oder eigene PHP-FPM-Pools pro Website |
| Ein abgestimmtes Nginx oder Apache steht bereits davor | Behalten Sie es; Velocity kann dahinter laufen |

Quelle: `README.md`, "When a traditional server is still the right answer". Hosting-Modelle:
[Hosting-Unternehmen: hier beginnen](../../hosting/hosting-anbieter-hier-beginnen.md).

## Verwandte Seiten

- [Der PHP-Stack](README.md)
- [Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)
- [Der Webserver Velocity](../../../funktionen/6.0/velocity-webserver.md)
- [Befehle für Webserver und Solr](../../../funktionen/6.0/befehle-fuer-webserver-und-solr.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
