---
title: "Livegang: Hosts und HTTPS"
audience: website-creator, admin
category: website-creation
order: 670
summary: Eine Website auf ihre öffentlichen Adressen umziehen - Host-Zuordnung für jeden Siteaccess, SiteURL, DNS, HTTPS-Zertifikate mit Velocity oder Ihrem Webserver, Weiterleitungen auf einen Host, Proxys und die Prüfungen, die belegen, dass jede Adresse den richtigen Siteaccess erreicht.
keywords: Livegang, Hosts, HTTPS, TLS, Zertifikat, DNS, Siteaccess, Host-Zuordnung, Weiterleitung, Website-Ersteller, go live, certificate, ACME, Let's Encrypt, host matching, SiteURL, redirect, www, TrustedProxies, website creator
type: help
---
# Livegang: Hosts und HTTPS

Diese Seite richtet sich an Website-Ersteller, die eine Website auf ihre echten Hostnamen bringen. Drei Dinge müssen
übereinstimmen: Das DNS leitet den Host zu Ihrem Server, der Server (Velocity oder ein Webserver) beantwortet ihn mit
einem Zertifikat, und Exponential ordnet den Host dem richtigen Siteaccess zu.

## Inhalt

1. [Hosts Siteaccesses zuordnen](#hosts-siteaccesses-zuordnen)
2. [SiteURL](#siteurl)
3. [HTTPS](#https)
4. [Ein Host, ein Schema](#ein-host-ein-schema)
5. [Hinter einem Proxy](#hinter-einem-proxy)
6. [Prüfungen](#prüfungen)

## Hosts Siteaccesses zuordnen

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
MatchOrder=host;uri
HostMatchType=map
HostMatchMapItems[]
HostMatchMapItems[]=www.example.com;site
HostMatchMapItems[]=admin.example.com;admin
```

Die leere Zeile löscht geerbte Einträge; führen Sie jeden Siteaccess auf, den Sie brauchen
([10.2 Siteaccesses und Website-Adressen](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
`HostMatchMethod=start` lässt einen Testhost wie `www.example.com.test.local` denselben Siteaccess erreichen
([Siteaccess nach Host-Präfix](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)).

## SiteURL

Setzen Sie `[SiteSettings] SiteURL` in jedem Siteaccess auf seinen öffentlichen Host ohne Schema (`www.example.com`).
Links in Mails, Feeds und Sitemaps werden daraus gebildet. Leeren Sie danach den INI-Cache.

## HTTPS

| Ausgeliefert von | Zertifikate |
|---|---|
| Velocity | eigene Zertifikate, ein selbstsignierter Fallback oder ACME (Let's Encrypt) als Hintergrund-Jobs ([Velocity: HTTPS und Zertifikate](../../../../eng-us/features/6.0/velocity-https-certificates.md)) |
| Apache, nginx | `mod_ssl` oder nginx-TLS mit einem Zertifikatswerkzeug oder Ihrem Hosting-Panel ([Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)) |

Jeder Hostname jedes Siteaccess muss im Zertifikat stehen. Fügen Sie `Strict-Transport-Security` hinzu, sobald HTTPS
überall funktioniert ([8.3.9](../../../installation/08-die-website-ausliefern.md#839-https-direkt-von-velocity)).

## Ein Host, ein Schema

Leiten Sie im Server `http://` auf `https://` und die anderen Schreibweisen (`example.com`) auf die gewählte
(`www.example.com`) weiter, mit einem 301. Eine Adresse pro Seite ist besser für Besucher und Suchmaschinen.

## Hinter einem Proxy

Wenn ein Load Balancer oder CDN HTTPS terminiert, tragen Sie seine Adresse in `[HTTPHeaderSettings] TrustedProxies[]`
ein; sonst glaubt die Website, jede Anfrage sei reines HTTP, und speichert die Adresse des Proxys als die jedes
Besuchers ([8.8 Hinter einem Reverse Proxy](../../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy)).

## Prüfungen

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/
curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' https://admin.example.com/
curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' http://example.com/
```

Erwarten Sie `200` für die Website, eine Weiterleitung zur Anmeldung für die Administration und ein `301` auf
`https://www.example.com/` für die andere Schreibweise. Wenn zwei Hosts gleich antworten, greift die Host-Zuordnung
nicht: Der Host muss genau so angegeben sein, wie der Browser ihn sendet, in Kleinbuchstaben, ohne Port.

## Verwandte Seiten

- [Checkliste für den Start](checkliste-fuer-den-start.md)
- [Multisite einrichten](multisite-einrichten.md)
- [Hosting für eine Website](hosting-fuer-eine-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
