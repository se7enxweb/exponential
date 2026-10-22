---
title: Installationen mit mehreren Websites
audience: software-engineer, admin
category: software-engineering
order: 430
summary: Wie eine Exponential-Installation mehrere Websites bedient - Siteaccesses je Website und Sprache, gemeinsame oder getrennte Inhaltsbäume, Einstellungen je Website innerhalb von Erweiterungen, Logs, Caches und INI-Caches je Website, Links zwischen Websites und die technischen Abwägungen gegenüber getrennten Installationen.
keywords: Multisite, mehrere Websites, Siteaccess, VarDir, Einstellungen je Website, Erweiterung je Website, Logs je Website, Cache je Website, Links zwischen Websites, Hosting, Mandantenfähigkeit, Softwareingenieur, multi-site, several sites, per-site settings, extension per site, logs per site, cache per site, cross-site links, tenancy
type: help
---
# Installationen mit mehreren Websites

Diese Seite richtet sich an Ingenieure, die eine Installation entwerfen, die mehr als eine Website bedient, und an jene,
die zwischen einer Installation und mehreren entscheiden. Exponential bedient seit Langem mehrere Websites aus einer
Codebasis; 6.0.15 hat Einstellungen je Website innerhalb von Erweiterungen sowie Logs und Caches je Website ergänzt.

## Inhalt

1. [Die Bausteine](#die-bausteine)
2. [Einstellungen je Website innerhalb von Erweiterungen](#einstellungen-je-website-innerhalb-von-erweiterungen)
3. [Logs und Caches je Website](#logs-und-caches-je-website)
4. [Links zwischen Websites](#links-zwischen-websites)
5. [Eine Installation oder mehrere](#eine-installation-oder-mehrere)
6. [Checkliste](#checkliste)

## Die Bausteine

| Baustein | Was er bietet |
|---|---|
| Siteaccess je Website (und je Sprache) | eigene Einstellungen, eigenes Design, Zuordnung nach Host oder Pfad ([Siteaccesses und Konfiguration](siteaccesses-und-konfiguration.md)) |
| Inhaltsbaum | Websites können einen Baum teilen oder jede einen Teilbaum verwenden (`content.ini [NodeSettings] RootNode`, `site.ini [SiteSettings] IndexPage` je Siteaccess) |
| `VarDir` je Website | Speicher, Cache und Logs jeder Website in `var/<site>/` |
| Eine Website in ihrer eigenen Erweiterung | ihre Einstellungen, ihr Design und ihr Code gemeinsam versioniert |
| Rollen mit Einschränkungen auf Teilbaum oder Sektion | Redakteure einer Website können keine andere ändern |

## Einstellungen je Website innerhalb von Erweiterungen

Seit Januar 2026 kann eine Erweiterung, die eine Website enthält, Override-Einstellungen mitbringen, die nur aktiv sind,
während einer ihrer Siteaccesses läuft: `extension/<site>/settings/override/` gilt für alle ihre Siteaccesses und ihr
`settings/siteaccess/<name>/` für einen. Das Leeren des INI-Cache leert ihn für jedes Projekt der Installation
([Einstellungen je Website innerhalb von Erweiterungen](../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md)).

## Logs und Caches je Website

Alles in `site.ini [FileSettings]`; jeder Standard behält das alte Verhalten:

| Einstellung | Wirkung |
|---|---|
| `UseGlobalLogDir=disabled` | Debug-Logs gehen in das Log-Verzeichnis der Website statt nach `var/log` |
| `LogDir`, `LogVarDir` | Logs auf einem anderen Dateisystem oder die Logs jeder Website unter einem Verzeichnis |
| `CacheVarDir` | die Caches jeder Website auf einem Dateisystem im Speicher |
| `ExpiryDir` | Ablaufzeitstempel dort, wo der Cache-Speicher liegt |
| `INICacheDir=site` | der INI-Cache je Website, für sich geleert |

Unter Velocity und auf der Kommandozeile wird jeder Wert neu aus `site.ini` abgeleitet, wenn der Siteaccess wechselt;
nichts wird von einer Anfrage oder Website zur nächsten behalten
([Logs, INI-Cache und Ablaufzeitstempel je Website](../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md)).

## Links zwischen Websites

Teilen sich mehrere Websites einen Inhaltsbaum und listet eine Seite einer Website Inhalte einer anderen (eine Tag-Seite,
ein Suchergebnis, ein verwandtes Element), zeigen die Links in die falsche Website und antworten mit 404, sofern die
Erweiterung `ezurlmultisite` sie nicht zur Adresse der anderen Website auflöst
([Links auf Inhalte einer anderen Website](../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md)).

## Eine Installation oder mehrere

| Eine Installation | Mehrere Installationen |
|---|---|
| eine Bereitstellung, eine Aktualisierung, ein Satz Erweiterungen | jede Website aktualisiert nach eigenem Plan |
| gemeinsame Inhalte und Benutzer sind möglich | strikte Trennung von Daten und Code |
| eine Datenbank und ein Fehlerbereich | Ausfälle bleiben in einer Website |
| Einstellungen, Logs und Caches je Website wie oben | alles je Website durch den Aufbau |

Wählen Sie eine Installation, wenn die Websites Personen, Inhalte oder einen Release-Plan teilen; wählen Sie mehrere,
wenn sie voneinander isoliert sein müssen.

## Checkliste

- [ ] Jede Website hat ihre Siteaccesses in `AvailableSiteAccessList[]`, mit Zuordnung nach Host oder Pfad.
- [ ] Die Einstellungen jeder Website liegen in ihrer Erweiterung, nicht in einem gemeinsamen `settings/override/`.
- [ ] Logs und Caches gelten je Website, wo Betreiber sie getrennt brauchen.
- [ ] Rollen halten die Redakteure jeder Website in ihrem Teilbaum oder ihrer Sektion.
- [ ] Die Liste vertrauenswürdiger Proxys und die Zertifikate decken jeden Hostnamen ab.
- [ ] Cronjobs laufen einmal je Installation, nicht einmal je Website, sofern ein Teil nicht websitespezifisch ist.

## Verwandte Seiten

- [Planung von Multisite und Mehrsprachigkeit](../fuehrung/multisite-und-mehrsprachigkeit-planen.md)
- [Siteaccess-Einstellungen](../erweiterungsentwicklung/teil-2-builder/siteaccess-einstellungen.md)
- [Internationalisierung und Rechts-nach-links](i18n-und-rechts-nach-links.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
