---
title: Siteaccesses und Multisite
audience: cms-developer
category: cms-development
order: 21
summary: Für CMS-Entwickler, wie eine Installation mehrere Gesichter bedient, etwa öffentliche Websites, Sprachen, die Administration und eine Oberfläche für Redakteure. Die Seite behandelt, wie eine Anfrage ihren Siteaccess wählt, was ein Siteaccess festlegt, Teilbaum-Websites mit PathPrefix, Links zwischen Websites, Einstellungen, die in eine Erweiterung gehören, und wann getrennte Installationen besser sind.
keywords: Siteaccess, Multisite, mehrere Websites, MatchOrder, Zuordnung über URI, Zuordnung über Host, Zuordnung über Port, AvailableSiteAccessList, RelatedSiteAccessList, PathPrefix, RootNode, IndexPage, ezurlmultisite, sevenx_multisite, admin, Siteaccess editor, Einstellungen je Website, multisite, several sites, URI matching, host matching, port matching, settings per site
type: help
---
# Siteaccesses und Multisite

Diese Seite richtet sich an Entwickler, die mehr als eine Website, Sprache oder Oberfläche in einer Installation bauen.
Ein **Siteaccess** ist ein Zugang zur Installation: ein Name, ein Ordner mit Einstellungen und eine Regel, auf welche
Anfragen er antwortet. Alle Siteaccesses teilen eine Datenbank und einen Inhaltsbaum; Inhalte lassen sich also zwischen
Websites teilen, während jede Website ihr eigenes Design, ihre Sprachen und ihren Startpunkt behält.

## Inhalt

1. [Was ein Siteaccess festlegt](#was-ein-siteaccess-festlegt)
2. [Wie eine Anfrage ihren Siteaccess wählt](#wie-eine-anfrage-ihren-siteaccess-wählt)
3. [Die Siteaccesses einer neuen Installation](#die-siteaccesses-einer-neuen-installation)
4. [Mehrere Websites in einem Baum](#mehrere-websites-in-einem-baum)
5. [Links zwischen Websites](#links-zwischen-websites)
6. [Website-Einstellungen in einer Erweiterung halten](#website-einstellungen-in-einer-erweiterung-halten)
7. [Ablesen, was ein Siteaccess sieht](#ablesen-was-ein-siteaccess-sieht)
8. [Eine Installation oder mehrere](#eine-installation-oder-mehrere)

## Was ein Siteaccess festlegt

| Legt fest | Einstellungen |
|---|---|
| Das Design | `site.ini [DesignSettings] SiteDesign`, `AdditionalSiteDesignList[]` |
| Die Inhaltssprachen | `[RegionalSettings] SiteLanguageList[]`, `Locale` ([Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md)) |
| Wo die Website im Baum beginnt | `content.ini [NodeSettings] RootNode`, `site.ini [SiteSettings] IndexPage`, `DefaultPage`, `[SiteAccessSettings] PathPrefix` |
| Ob Besucher sich anmelden müssen | `[SiteAccessSettings] RequireUserLogin` |
| Erweiterungen, die nur für dieses Gesicht aktiv sind | `[ExtensionSettings] ActiveAccessExtensions[]` |
| Template-Regeln | seine eigene `override.ini.append.php` |

Ein kommentierter öffentlicher Siteaccess steht unter
[Siteaccess-Einstellungen](../erweiterungsentwicklung/teil-2-builder/siteaccess-einstellungen.md#ein-öffentlicher-siteaccess-kommentiert).

## Wie eine Anfrage ihren Siteaccess wählt

`site.ini [SiteAccessSettings]` listet jeden Siteaccess in `AvailableSiteAccessList[]` auf und versucht die
Zuordnungen aus `MatchOrder` der Reihe nach; die mitgelieferte Reihenfolge ist `uri;host;port`.

| Zuordnung | Beispiel | Typische Verwendung |
|---|---|---|
| URI (`URIMatchType=element`) | `example.com/admin/...`, `example.com/ger/...` | ein Hostname, Sprachen als Pfadelemente |
| Host (`HostMatchMapItems[]=www.example.com;site`) | `admin.example.com`, `www.example.de` | Produktion, ein Zertifikat je Host |
| Port | `:8081` | lokale Arbeit |

`RelatedSiteAccessList[]` nennt die Siteaccesses, die Inhalte und Links teilen. Jede Zuordnungsregel und wie Sie sie
testen: [Siteaccess-Zuordnung nach Host und Sprache](../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md) und
[Multisite in einer Installation](../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#zuordnung-nach-url-host-oder-port).

## Die Siteaccesses einer neuen Installation

Eine Installation hat mindestens einen öffentlichen Siteaccess und die Administration; `exp:install` legt außerdem
einen Siteaccess `editor` mit einer einfacheren Oberfläche für Redakteure an
([Einrichtungsassistent und Siteaccess editor](../../funktionen/6.0/einrichtungsassistent-und-editor-siteaccess.md)). Das
Seitenpaket `sevenx_multisite` legt mehr an: zwei Demo-Websites (Fit & Healthy und Bold Agency), einen Siteaccess je
weiterer Sprache, `admin`, `editor` und mit `exp_adminui` auch `adminui`
([Siteaccesses und Sprachen von sevenx_multisite](../seitenpakete/sevenx-multisite/siteaccesses-und-sprachen-von-sevenx-multisite.md)).
Nachzulesen, wie dieses Paket seine Websites verdrahtet, ist der schnellste Weg, eine Multisite-Einrichtung zu lernen.

## Mehrere Websites in einem Baum

Jede Website erhält ihren eigenen Teilbaum unter Knoten 2 und ihren eigenen Siteaccess (oder einen je Sprache). Im
Siteaccess:

- zeigen `content.ini [NodeSettings] RootNode` und `site.ini [SiteSettings] IndexPage` auf den obersten Knoten der
  Website, sodass Startseite und Menüs dort beginnen;
- blendet `[SiteAccessSettings] PathPrefix` den eigenen Pfad des Teilbaums in den URLs aus, sodass
  `/bold-agency/services` auf der Bold-Website als `/services` ausgeliefert wird (`PathPrefixExclude[]` listet Pfade,
  die die volle Adresse behalten);
- lässt eine Sektion je Website Rollen und Template-Regeln die Websites unterscheiden
  ([Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md)).

Gemeinsam genutzte Inhalte (eine Medienbibliothek, Personen, ein News-Pool) liegen außerhalb der Teilbäume der Websites
und werden von jeder Website verknüpft, eingebettet oder abgerufen.

## Links zwischen Websites

Verlinkt eine Website auf einen Knoten einer anderen Website, würde das Standard-`ezurl` den aktuellen Siteaccess vor
den Pfad der anderen Website setzen. Die Erweiterung `ezurlmultisite`, die die `composer.json` im Hauptverzeichnis
verlangt, lässt `ezurl` die Adresse auf dem Siteaccess erzeugen, der den Inhalt ausliefert; sie wird in
`ezurlmultisite.ini [MultisiteSettings]` konfiguriert (`SiteAccessList[]`, `RootNodeList[<siteaccess>]`)
([Links zwischen Websites](../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md)).

## Website-Einstellungen in einer Erweiterung halten

Die Siteaccess-Einstellungen einer Website gehören unter Versionsverwaltung, in Ihre Website-Erweiterung:
`extension/<ext>/settings/siteaccess/<siteaccess>/` für einen Siteaccess und `extension/<ext>/settings/override/` für
Einstellungen, die für jeden Siteaccess dieser Erweiterung gelten
([INI-Overrides je Website-Erweiterung](../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md)). Die Theme-Erweiterung
`sevenx_themes_media` liefert die Bold-Siteaccesses auf diese Weise mit. Wie sich die Geltungsbereiche gegeneinander
durchsetzen: [Einstellungen und INI für CMS-Entwickler](einstellungen-und-ini-fuer-cms-entwickler.md).

## Ablesen, was ein Siteaccess sieht

```bash
./console exp:ini scopes siteaccess
./console exp:ini get site.ini/DesignSettings/SiteDesign -s <siteaccess>
./console exp:ini where content.ini/NodeSettings/RootNode -s <siteaccess>
./console exp:ezrequestrules --help
```

In einem Template ist der aktuelle Siteaccess `$access_type.name`; in PHP `eZSiteAccess::current()`.

## Eine Installation oder mehrere

Eine Installation passt zu Websites, die Inhalte, Redakteure oder ein Designsystem teilen und gemeinsam aktualisiert
werden. Getrennte Installationen passen zu Websites, die getrennte Datenbanken, getrennte Aktualisierungspläne oder
strikte Trennung zwischen Kunden brauchen
([Eine Installation oder mehrere?](../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#eine-installation-oder-mehrere)).
Logs und Caches lassen sich auch in einer Installation je Website führen
([Log- und Cache-Pfade je Website](../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md)).

## Verwandte Seiten

- [URL-Aliase und Routing](url-aliase-und-routing.md)
- [Designs und Siteaccesses](../designer/theme-design/designs-und-siteaccesses.md)
- [Tutorial: zwei Websites, zwei Designs](../designer/theme-design/tutorial-zwei-websites-zwei-designs.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
