---
title: "Siteaccesses: einen Request einer Site zuordnen"
audience: web-framework-developer, developer
category: web-frameworks
order: 50
summary: Der Siteaccess ist das Konzept, das Framework-Entwickler am häufigsten übersehen - eine benannte Konfiguration, die pro Request anhand von URI, Host, Port oder Servervariable vor dem Routing ausgewählt wird - wie die Zuordnung funktioniert, was ein Siteaccess verändert, wie er sich zu Mandanten, Domain-Routen und dem Sites-Framework von Django verhält und wie Code ihn ausliest.
keywords: Siteaccess, siteaccess, MatchOrder, URIMatchType, HostMatchType, HostMatchMapItems, HostUriMatchMapItems, AvailableSiteAccessList, Multisite, multisite, Mandant, Mandantenfähigkeit, tenant, Domain-Routing, domain routing, eZSiteAccess, Web-Framework-Entwickler, web framework developer
type: help
---
# Siteaccesses: einen Request einer Site zuordnen

Diese Seite richtet sich an Entwickler, die eine Anwendung mit genau einer Konfiguration erwarten. Exponential
beantwortet jeden Request im Kontext eines **Siteaccess**: einer benannten Konfiguration wie `site`, `admin`, `ger`
oder `eng`, die vor dem Routing ausgewählt wird. Die Administration ist ein Siteaccess, jede Sprachversion einer Site
ist in der Regel einer, und mehrere Sites auf einer Datenbank sind mehrere Siteaccesses.

## Inhalt

1. [Was ein Siteaccess verändert](#was-ein-siteaccess-verändert)
2. [Wie ein Request zugeordnet wird](#wie-ein-request-zugeordnet-wird)
3. [Die Zuordnungsarten](#die-zuordnungsarten)
4. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)
5. [Den Siteaccess im Code auslesen](#den-siteaccess-im-code-auslesen)
6. [Praktische Regeln](#praktische-regeln)

## Was ein Siteaccess verändert

Ein Siteaccess hat ein eigenes Einstellungsverzeichnis, `settings/siteaccess/<name>/`, und jede Einstellung darin
überschreibt für Requests dieses Siteaccess den globalen Standardwert ([Konfiguration](konfiguration-ini-dateien.md)).
Typische Unterschiede:

| Einstellung | Beispiel |
|---|---|
| Design und Template-Overrides | das öffentliche Design für `site`, ein Administrationsdesign für `admin` |
| Sprachen | `site.ini [RegionalSettings]` mit den Sprachen der Site, sodass `ger` deutsche Inhalte zeigt |
| Inhaltswurzel und Baummenü | welcher Knoten die Startseite ist |
| Erweiterungen | `site.ini [ExtensionSettings] ActiveAccessExtensions[]`: Erweiterungen, die nur für diesen Siteaccess aktiv sind |
| Anmeldung | `[SiteAccessSettings] RequireUserLogin`: die Administration verlangt eine Anmeldung, eine öffentliche Site nicht |
| Caches und Protokolle | Pfade pro Site ([Protokoll- und Cache-Pfade bei mehreren Sites](../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md)) |

## Wie ein Request zugeordnet wird

`site.ini [SiteAccessSettings] MatchOrder` listet die Prüfungen auf, die der Reihe nach versucht werden; der
ausgelieferte Wert ist `uri;host;port`. Die erste Prüfung, die einen in `AvailableSiteAccessList[]` aufgeführten
Siteaccess ergibt, gewinnt. `eZSiteAccess::match()` erledigt das im Konstruktor des Kernels, und
`eZSiteAccess::change()` wechselt zum Ergebnis ([Der Request-Lebenszyklus](der-request-lebenszyklus.md)).

```ini
# settings/override/site.ini.append.php (a sketch: hosts and names are examples)
<?php /*
[SiteAccessSettings]
AvailableSiteAccessList[]=site
AvailableSiteAccessList[]=admin
MatchOrder=host;uri
HostMatchType=map
HostMatchMapItems[]=www.example.com;site
HostMatchMapItems[]=admin.example.com;admin
*/ ?>
```

## Die Zuordnungsarten

| Prüfung | Einstellungen | Ordnet zu nach |
|---|---|---|
| `uri` | `URIMatchType` (ausgeliefert `element`, `map`, `text`, `regexp`, `disabled`), `URIMatchElement`, `URIMatchMapItems[]` | dem ersten Pfadsegment: `example.com/admin/...` |
| `host` | `HostMatchType` (ausgeliefert `map`, `element`, `text`, `regexp`), `HostMatchMapItems[]`, `HostMatchMethod` | dem Hostnamen: `admin.example.com` |
| `host_uri` | `HostUriMatchMapItems[]=host;uri;siteaccess[;method]` | Host und Pfad zusammen: `example.com/ger` |
| `port` | die Port-Einträge von `[SiteAccessSettings]` | einer Portnummer |
| `servervar` | eine vom Webserver gesetzte Servervariable | einem Wert, den der Webserver bestimmt |

Wenn keine Prüfung greift, kann `DefaultHostUriMatchMapItems[]` den Siteaccess anhand des Hosts und der vom Browser
akzeptierten Sprachen auswählen, mit einer einzigen Weiterleitung auf die Sprachadresse
([Host- und Sprachzuordnung von Siteaccesses](../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md)).

## Im Vergleich mit anderen Frameworks

| Framework | Nächstes Konzept | Unterschied |
|---|---|---|
| Symfony | Host-Anforderungen an Routen, ein Request-Listener, der die Mandantenkonfiguration lädt | ein Siteaccess ändert die gesamte Konfiguration einschließlich Design und aktiver Erweiterungen, nicht nur die Routen |
| Laravel | Domain-Routengruppen, Pakete für Mandantenfähigkeit | in den Kernel und das Einstellungssystem eingebaut |
| Django | das Sites-Framework (`SITE_ID`) | pro Request gewählt, nicht pro Prozess |
| Rails | Constraints an Routen, mehrere Engines | eine Datenbank kann mehrere Siteaccesses gleichzeitig bedienen |
| Spring | Profile | ein Profil gilt pro Prozess, ein Siteaccess pro Request |

Siteaccesses sind keine Umgebungen: Die Umgebung der Maschine (Entwicklung, Test, Produktion) ist `EXP_ENV`
([Umgebungen und Geheimnisse](umgebungen-und-geheimnisse.md)).

## Den Siteaccess im Code auslesen

```php
$access = eZSiteAccess::current();      // array with 'name' and the match details, or null
$name = $access ? $access['name'] : null;
```

Ermitteln Sie den aktuellen Siteaccess über `eZSiteAccess::current()`, statt die URL selbst zu zerlegen
([Siteaccess](../../standards/architektur/kernel-architektur.md#siteaccess)). In Templates enthält
`$access_type.name` den Namen. Links, die mit dem Operator `ezurl` erzeugt werden, erhalten bei URI-Zuordnung das
Siteaccess-Präfix; schreiben Sie in Templates daher niemals absolute Pfade von Hand.

## Praktische Regeln

- Geben Sie jedem Siteaccess nur die Einstellungen, die sich unterscheiden; gemeinsame Einstellungen gehören in eine
  Erweiterung oder nach `settings/override/`.
- Kommandozeilenskripte brauchen `-s <siteaccess>`, wenn sie von Siteaccess-Einstellungen oder Siteaccess-Erweiterungen
  abhängen.
- Eine Einstellung, die nur für einen Siteaccess gelten soll, darf nicht im globalen Verzeichnis `settings/` einer
  Erweiterung liegen: Einstellungen von Erweiterungen werden nach `settings/siteaccess/` gelesen und haben Vorrang
  ([Platzierung von INI-Overrides](../../spezifikationen/6.0/ini-override-platzierungen.md)).

## Verwandte Seiten

- [Multisite einrichten](../ersteller/website-ersteller/multisite-einrichten.md)
- [Einstellungen pro Site in Erweiterungen](../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md)
- [Siteaccesses und Site-Adressen](../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
