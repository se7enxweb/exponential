---
title: "Mehrere Websites: Siteaccesses"
audience: admin, developer
category: examples
order: 100
summary: Zwei öffentliche Websites und eine Administration aus einer Installation bedienen, unterschieden nach Hostname, jede mit eigenem Design, Startknoten, eigener Sprache und eigenem var-Verzeichnis, mit den Einstellungen jedes Siteaccess und den Prüfungen, die die Zuordnung belegen.
keywords: Siteaccess, Multisite, mehrere Websites, Hostname, Zuordnung, Sprache, Startknoten, siteaccess, multisite, multi-site, AvailableSiteAccessList, RelatedSiteAccessList, MatchOrder, HostMatchType, HostMatchMapItems, HostMatchMethod, URIMatchType, HostUriMatchMapItems, settings/siteaccess, VarDir, CacheVarDir, LogVarDir, RootNode, PathPrefix, SiteDesign, DefaultAccess
type: example
---
# Mehrere Websites: Siteaccesses

Ein **Siteaccess** ist ein benannter Satz von Einstellungen, der für eine Anfrage gilt: welches Design, welcher Teil
des Inhaltsbaums, welche Sprache, ob Besucher sich anmelden müssen. Eine Installation, eine Datenbank, viele
Siteaccesses. Sie richten ein:

| Siteaccess | Host | Was er ist |
|---|---|---|
| `site_a` | `www.example.com` | die erste öffentliche Website, der Teilbaum *Site A* |
| `site_b` | `www.example.org` | die zweite öffentliche Website, der Teilbaum *Site B*, auf Deutsch |
| `admin` | `admin.example.com` | die Administration beider Websites |

Voraussetzungen: eine funktionierende Installation mit dem Siteaccess `admin`; die drei Hostnamen zeigen auf den
Server, und der Webserver antwortet für alle mit demselben Document Root ([Webserver-Beispiele](../README.md)); zwei
Ordner *Site A* und *Site B* unterhalb der Wurzel des Inhaltsbaums (notieren Sie ihre Knoten-IDs, hier 60 und 61).

## 1. Die Liste und die Zuordnung

Die Auswahl des Siteaccess gilt für die ganze Installation und gehört deshalb in das globale Override,
`settings/override/site.ini.append.php` (wird niemals committet: es enthält auch das Datenbankpasswort):

```ini
<?php /* #?ini charset="utf-8"?

[SiteSettings]
DefaultAccess=site_a

[SiteAccessSettings]
AvailableSiteAccessList[]
AvailableSiteAccessList[]=site_a
AvailableSiteAccessList[]=site_b
AvailableSiteAccessList[]=admin
# Siteaccesses that share this database (the administration shows and clears their caches)
RelatedSiteAccessList[]
RelatedSiteAccessList[]=site_a
RelatedSiteAccessList[]=site_b
RelatedSiteAccessList[]=admin
# Choose the siteaccess by the host name only
MatchOrder=host
HostMatchType=map
HostMatchMapItems[]
HostMatchMapItems[]=www.example.com;site_a
HostMatchMapItems[]=www.example.org;site_b
HostMatchMapItems[]=admin.example.com;admin

*/ ?>
```

- `MatchOrder` listet die Prüfungen in der Reihenfolge, in der sie versucht werden: `uri` (das erste Segment des
  Pfads, `/site_b/...`), `host`, `host_uri` (Host und Segment zusammen,
  `HostUriMatchMapItems[]=example.com;de;site_de`), `port`, `servervar`. Der ausgelieferte Standardwert ist
  `uri;host;port`.
- Passt nichts, antwortet `DefaultAccess`.
- `HostMatchMethod=start` (oder `;start` als viertes Feld eines Eintrags) passt auch auf Testhosts, die mit dem Namen
  beginnen, etwa `www.example.com.test.local`; siehe
  [Siteaccess nach Host-Präfix](../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md).

## 2. Die Einstellungen jedes Siteaccess

Jeder Siteaccess hat ein Verzeichnis `settings/siteaccess/<name>/`. Was sich je Website unterscheidet, gehört dorthin.

`settings/siteaccess/site_a/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[SiteSettings]
SiteName=Example A
SiteURL=www.example.com
DefaultPage=/content/view/full/60
IndexPage=/content/view/full/60

[SiteAccessSettings]
RequireUserLogin=false
# The URL alias of Site A's folder is not repeated in every address
PathPrefix=Site-A

[DesignSettings]
SiteDesign=mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=base

[RegionalSettings]
Locale=eng-US
ContentObjectLocale=eng-US
SiteLanguageList[]
SiteLanguageList[]=eng-US

[FileSettings]
VarDir=var/site_a

*/ ?>
```

`settings/siteaccess/site_a/content.ini.append.php` (die Wurzel der Menüs und von `fetch` ohne Elternknoten):

```ini
<?php /* #?ini charset="utf-8"?

[NodeSettings]
RootNode=60

*/ ?>
```

`settings/siteaccess/site_b/` ist genauso aufgebaut, mit `SiteName=Example B`, `SiteURL=www.example.org`, Knoten 61,
`PathPrefix=Site-B`, `VarDir=var/site_b` und Deutsch an erster Stelle:

```ini
[RegionalSettings]
Locale=ger-DE
ContentObjectLocale=ger-DE
SiteLanguageList[]
SiteLanguageList[]=ger-DE
SiteLanguageList[]=eng-US
```

`SiteLanguageList[]` ist die Reihenfolge, in der Inhaltssprachen angezeigt werden; die Sprachen müssen vorhanden sein
(**Setup > Sprachen**). Der Siteaccess `admin` behält seine eigenen Einstellungen; tragen Sie dort
`SiteLanguageList[]` für beide Sprachen ein.

**VarDir je Website** hält die Caches und den Speicher jeder Website getrennt. Um alle ihre Logs und Caches mit je
einer Zeile in eigene Verzeichnisbäume zu legen, verwenden Sie `LogVarDir`, `CacheVarDir` und `INICacheDir=site` im
globalen Override: [Logs, INI-Cache und Ablaufzeitstempel je Website](../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md).
Mit `CacheVarDir` muss der Webserver auch die öffentlichen Caches darunter ausliefern: Aktivieren Sie die
auskommentierte Regel `var_cache` in `.htaccess_root` (oder die entsprechende Regel im Beispiel für Ihren Server).

**Einstellungen in einer Erweiterung.** Eine Website-Erweiterung kann stattdessen
`extension/<ext>/settings/siteaccess/<name>/` mitbringen, sodass die Einstellungen unter Versionskontrolle stehen;
siehe [Einstellungen je Website in Erweiterungen](../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md).

## 3. Leeren und prüfen

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
./console exp:ini get site.ini/SiteSettings/SiteName -s site_b --allow-root-user
curl -sI https://www.example.org/ | head -1
```

Erwartet: `Example B` und `HTTP/... 200` (oder eine Weiterleitung auf die Startseite). Jede Website zeigt ihren
eigenen Teilbaum in ihrer eigenen Sprache; `https://admin.example.com/` verlangt eine Anmeldung. Unter Velocity
starten Sie Velocity nach einer Änderung an einem Siteaccess neu (`./console exp:velocity restart --allow-root-user`):
Seine Worker halten die Einstellungen im Speicher.

Auf der Kommandozeile nimmt jedes Skript den Siteaccess mit `-s` (`--siteaccess`) entgegen:
`php runcronjobs.php -s site_b frequent --allow-root-user`.

Für diese Seite geprüft (2026-10-18): Jede genannte Einstellung existiert in der ausgelieferten `settings/site.ini`
oder `settings/content.ini` mit der beschriebenen Bedeutung; die Host-Zuordnung und ihre Methoden sind die aus
`doc/eng-us/features/6.0/siteaccess-host-and-language-matching.md`. Die Konfiguration wurde für diese Seite nicht auf
eine Website angewendet.

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| Jeder Host zeigt dieselbe Website | `MatchOrder` enthält `host` nicht, oder der INI-Cache; der Webserver schreibt den Host um |
| Eine Weiterleitungsschleife auf dem Host der Administration | der Host steht nicht in `HostMatchMapItems[]`; `DefaultAccess` antwortet und verlangt die Anmeldung woanders |
| Ein Siteaccess wird ignoriert | er steht nicht in `AvailableSiteAccessList[]`, oder sein Verzeichnisname weicht ab |
| Links zeigen auf den anderen Host | `SiteURL` des Siteaccess; für Links zwischen Websites `ezurl` mit vollständiger Adresse oder der Sprachumschalter |
| `exp:ini get ... -s` zeigt den globalen Wert | die Einstellung steht im falschen Verzeichnis oder in der Override-Datei, die Vorrang vor dem Siteaccess hat |

## Abgeleitet aus

- `settings/site.ini` (`[SiteAccessSettings]`, `[SiteSettings]`, `[DesignSettings]`, `[RegionalSettings]`, `[FileSettings]`)
- `settings/content.ini` (`[NodeSettings] RootNode`), `settings/siteaccess/mysite/site.ini.append`
- `doc/eng-us/features/6.0/siteaccess-host-and-language-matching.md`, `doc/eng-us/features/6.0/multi-site-log-and-cache-paths.md`

## Verwandte Seiten

- [Kochbuch](README.md)
- [Platzierungen von Einstellungs-Overrides](../../spezifikationen/6.0/ini-override-platzierungen.md),
  [Einstellungen auf der Kommandozeile ändern](../../funktionen/6.0/exp-ini-befehl.md)
- [INI-Einstellungen](../ini-einstellungen/README.md): `site.ini` Block für Block, mit Standardwerten
- [Übersetzungen](uebersetzungen.md), [Caching](caching.md)
- Die Programmierregeln und der Musterkatalog: [die Standards](../../standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
