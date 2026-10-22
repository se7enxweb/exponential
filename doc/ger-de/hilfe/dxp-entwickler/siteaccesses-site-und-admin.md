---
title: "Siteaccesses: site und admin"
audience: dxp-developer
category: dxp-development
order: 15
summary: Was ein Siteaccess in der DXP ist, die beiden, die das Skeleton installiert (site und admin), wie die Konfiguration über default, Gruppen, den Siteaccess und global aufgelöst wird und wie Sie eine zweite Website oder Sprache hinzufügen.
keywords: Siteaccess, site, admin, admin_group, site_group, Siteaccess-Gruppen, Geltungsbereich, scope, default, global, Multisite, Sprachen, Config Resolver, ibexa.yaml, siteaccess groups, languages, config resolver
type: help
---
# Siteaccesses: site und admin

Diese Seite richtet sich an Entwickler, die konfigurieren, was eine Anfrage sieht. Ein Siteaccess ist eine benannte
Konfiguration, die die DXP für jede Anfrage auswählt: welches Design, welche Sprachen, welcher Teil des Inhaltsbaums,
welche Einstellungen. Die Idee ist dieselbe wie beim Siteaccess von Exponential 6, ausgedrückt in YAML.

## Inhalt

1. [Die zwei Siteaccesses zu Beginn](#die-zwei-siteaccesses-zu-beginn)
2. [Wie Einstellungen aufgelöst werden](#wie-einstellungen-aufgelöst-werden)
3. [Wo die Konfiguration liegt](#wo-die-konfiguration-liegt)
4. [Eine Website oder Sprache hinzufügen](#eine-website-oder-sprache-hinzufügen)
5. [Einen Wert prüfen](#einen-wert-prüfen)

## Die zwei Siteaccesses zu Beginn

| Siteaccess | Bedient | URL |
|---|---|---|
| `site` | das Twig-Frontend | `/` |
| `admin` | die Administrationsoberfläche | `/adminui/` |

Quelle: Anleitung des Skeletons, Abschnitt 2. Das Administrations-Bundle erwartet seine Siteaccesses in der Gruppe, die
`admin_group_name` nennt (standardmäßig `admin_group`).

## Wie Einstellungen aufgelöst werden

Einstellungen unter `ibexa.system` werden in dieser Reihenfolge gelesen, die spätere gewinnt: `default`, die
Siteaccess-Gruppe, der Siteaccess selbst und `global`. Der Kommentar in einer installierten
`config/packages/ibexa.yaml` sagt das und gibt den Tipp: "For multisite installations organize shared config into
SiteAccess groups, on single-site you can place shared config under `default`".

```yaml
ibexa:
    system:
        default:            # every siteaccess
            content:
                default_ttl: '%httpcache_default_ttl%'
        site_group:         # every siteaccess of the group
            image_variations: { }
        site:               # only this siteaccess
            languages: [eng-GB]
```

## Wo die Konfiguration liegt

| Was | Schlüssel |
|---|---|
| Die Liste der Siteaccesses, der Standard, die Gruppen, wie eine Anfrage zugeordnet wird | `ibexa.siteaccess` (`list`, `default_siteaccess`, `groups`, `match`) |
| Einstellungen je Geltungsbereich | `ibexa.system.<scope>` |
| Das Design eines Siteaccess | `ibexa.system.<scope>.design` ([Twig und die Design-Engine](twig-templates-und-die-design-engine.md)) |

Die Rezepte schreiben diese Dateien unter `config/packages/`; ein Projekt kann sie in anderen YAML-Dateien halten, die
der Kernel importiert. Suchen Sie sie mit `grep -rn "siteaccess:" config/`.

## Eine Website oder Sprache hinzufügen

1. Fügen Sie den neuen Siteaccess zu `ibexa.siteaccess.list` und zu einer Gruppe hinzu (einer neuen `site2_group` oder
   der vorhandenen Website-Gruppe).
2. Fügen Sie unter `ibexa.siteaccess.match` eine Zuordnung hinzu (nach Host, nach URI-Element oder mit einer anderen
   Zuordnung, die der Core anbietet).
3. Geben Sie ihm seinen Geltungsbereich unter `ibexa.system`: `languages`, die Wurzel im Baum, das `design`.
4. Leeren Sie den Cache und prüfen Sie den Wert (unten).

Fügen Sie Administrations-Siteaccesses nur der Admin-Gruppe hinzu, nie die öffentlichen.

## Einen Wert prüfen

```bash
php bin/console exponential:debug:config-resolver languages --siteaccess=site
php bin/console exponential:debug:config-resolver design --siteaccess=admin
php bin/console debug:config ibexa siteaccess
```

`exponential:debug:config-resolver` (Alias `ibexa:debug:config-resolver`) zeigt den Wert, den ein Siteaccess auflöst
([Konsolenbefehle](konsolenbefehle-und-umbenennungen.md)).

## Verwandte Seiten

- [Die Admin-Oberfläche](die-admin-oberflaeche.md)
- [Siteaccess-Einstellungen in Exponential 6](../erweiterungsentwicklung/teil-2-builder/siteaccess-einstellungen.md), zum Vergleich
- [Umstieg von Exponential 6](umstieg-von-exponential-6.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
