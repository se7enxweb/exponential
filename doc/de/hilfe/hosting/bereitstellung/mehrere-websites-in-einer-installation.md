---
title: Mehrere Websites in einer Installation
audience: hosting, admin
category: hosting
order: 80
summary: Wie eine Exponential-Installation über Siteaccesses mehrere Websites ausliefert, zugeordnet nach URL, Hostname oder Port; was das für Abschottung, Logs, Caches, Abrechnung und Backups eines Hosters bedeutet; und wann getrennte Installationen die bessere Wahl sind.
keywords: mehrere Websites, Multisite, Siteaccess, Siteaccesses, Zuordnung nach Host, HostMatchMapItems, Zuordnung nach URI, Zuordnung nach Port, SiteURL, VarDir, Logs pro Website, UseGlobalLogDir, LogVarDir, CacheVarDir, INICacheDir, Abschottung, Abrechnung, multisite, multi-site, host matching, URI matching, port matching, logs per site, isolation, billing
type: help
---
# Mehrere Websites in einer Installation

Diese Seite ist für Hoster und Administratoren, die mehrere Websites aus einer Exponential-Installation betreiben oder entscheiden
müssen, ob sie das tun. Sie beantwortet: wie Siteaccesses funktionieren, was der Hoster konfigurieren muss, wie Logs und Caches pro
Website getrennt gehalten werden können und wann getrennte Installationen besser sind.

## Inhalt

1. [Siteaccesses kurz gesagt](#siteaccesses-kurz-gesagt)
2. [Zuordnung nach URL, Host oder Port](#zuordnung-nach-url-host-oder-port)
3. [Logs und Caches pro Website](#logs-und-caches-pro-website)
4. [Eine Installation oder mehrere?](#eine-installation-oder-mehrere)

## Siteaccesses kurz gesagt

Ein **Siteaccess** ist ein Weg in die Installation: ein Name, ein Satz von Einstellungen in `settings/siteaccess/<name>/` und eine Regel,
welche Anfragen er beantwortet ([10.2](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)). Der Installer legt
mindestens den öffentlichen Siteaccess und die Administration an (und mit `exp:install` einen Siteaccess für Redakteure). Weitere
öffentliche Siteaccesses können weitere Websites, Sprachen oder Designs aus demselben Inhalts-Repository ausliefern.

Das Seitenpaket `sevenx_multisite` ist dafür gebaut („7x MultiSite Default Installation“; [Seitenpakete](seitenpakete-fuer-hoster.md)).

## Zuordnung nach URL, Host oder Port

Die Regeln liegen in `site.ini [SiteAccessSettings]`; `MatchOrder` ist in der Auslieferung `uri;host;port`.

| Aufbau | Beispiel | Für einen Hoster |
|---|---|---|
| URL | `www.example.com/admin` | ein Hostname, am einfachsten hinter jedem Webserver |
| Host | `admin.example.com` | am besten für die Produktion und für HTTPS-Zertifikate pro Host |
| Port | `:8081` | lokale Arbeit |

Zuordnung nach Host, wie in 10.2:

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
MatchOrder=host;uri
HostMatchType=map
HostMatchMapItems[]
HostMatchMapItems[]=www.example.com;site
HostMatchMapItems[]=www.example.org;site_org
HostMatchMapItems[]=admin.example.com;admin
```

Jeder Siteaccess setzt seine eigene `[SiteSettings] SiteURL`. Fügen Sie jeden Hostnamen dem Webserver oder Velocity, dem DNS und dem
Zertifikat hinzu, leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) und testen Sie, dass jeder
Name den richtigen Siteaccess erreicht. Der Hostname, den der Browser sendet, muss genau der in `HostMatchMapItems[]` sein, ohne Port
und in Kleinbuchstaben.

Hinter einem Proxy oder Load Balancer führen Sie ihn in `site.ini [HTTPHeaderSettings] TrustedProxies[]` auf, sonst sieht die Website
Schema und Adresse des Proxys ([10.2](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).

## Logs und Caches pro Website

Wenn jede Website ein eigenes `VarDir` hat, halten diese Einstellungen in `site.ini [FileSettings]` ihre Dateien getrennt
([Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../en/us/features/6.0/multi-site-log-and-cache-paths.md)):

| Einstellung | Standard | Wirkung |
|---|---|---|
| `UseGlobalLogDir` | `enabled` | `disabled` schreibt die Debug-Logs in das Log-Verzeichnis der Website statt nach `var/log` |
| `LogVarDir` | leer | ein Baum für die Logs aller Websites, `var_log/<site>/log` |
| `CacheVarDir` | leer | ein Baum für die Caches aller Websites, `var_cache/<site>/cache` (zum Beispiel auf einem Dateisystem im Arbeitsspeicher) |
| `INICacheDir` | `global` | `site` speichert die INI-Dateien jeder Website in ihrem eigenen Cache-Verzeichnis, sodass das Leeren einer die anderen in Ruhe lässt |

Jede Einstellung behält das bisherige Verhalten, sofern Sie sie nicht setzen. Einstellungen pro Website innerhalb von Erweiterungen sind
in [Einstellungen pro Website innerhalb von Erweiterungen](../../../../en/us/features/6.0/multi-site-ini-overrides.md) beschrieben.

## Eine Installation oder mehrere?

| | Eine Installation, mehrere Siteaccesses | Eine Installation pro Website |
|---|---|---|
| Gemeinsame Inhalte und Benutzer | ja | nein |
| Ein Update für alle Websites | ja | eines pro Website |
| Abschottung zwischen Websites auf Ebene des Betriebssystems | nein: ein Systembenutzer, eine Codebasis | ja ([Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md)) |
| Die Last eines Kunden betrifft die anderen | ja | nein, mit getrennten Pools oder Instanzen |
| Backups und Wiederherstellungen | gemeinsam | pro Website |
| Abrechnung „pro Website“ | legen Sie fest, was eine Website ist ([Abrechnungs- und Messoptionen](../geschaeft/abrechnungs-und-messoptionen.md)) | einfach |

Faustregel für einen Hoster: mehrere Siteaccesses in einer Installation für **einen Kunden** mit mehreren Websites; getrennte
Installationen für **verschiedene Kunden**.

## Verwandte Seiten

- [Seitenpakete](seitenpakete-fuer-hoster.md)
- [Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md)
- [Kapitel 10: Nach der Installation](../../../installation/10-nach-der-installation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
