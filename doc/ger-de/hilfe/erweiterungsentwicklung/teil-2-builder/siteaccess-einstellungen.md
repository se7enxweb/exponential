---
title: Siteaccess-Einstellungen
audience: developer, designer, app-creator
category: extension-development
order: 30
summary: Ein Siteaccess ist ein Gesicht einer Installation - eine öffentliche Website, eine Sprache, die Administration. Wie eine Anfrage ihren Siteaccess auswählt, welche Einstellungen einen festlegen und ein vollständiges, kommentiertes Beispiel eines öffentlichen Siteaccess.
keywords: Siteaccess, Site Access, Mehrsprachigkeit, Multisite, Admin-Siteaccess, Design, Sprache, site access, MatchOrder, URIMatchType, AvailableSiteAccessList, RelatedSiteAccessList, SiteDesign, AdditionalSiteDesignList, RegionalSettings, multisite, admin siteaccess
type: help
---
# Siteaccess-Einstellungen

Teil 2, Kapitel 3. Eine Exponential-Installation bedient aus derselben Datenbank mehrere „Gesichter“: die öffentliche
Website, dieselbe Website in einer anderen Sprache, eine zweite Website, die Administration, eine Oberfläche für
Redakteure. Jedes Gesicht ist ein **Siteaccess**. Dieses Kapitel erklärt, wie eine Anfrage ihren Siteaccess auswählt
und was in die Einstellungen eines Siteaccess gehört.

## Was ein Siteaccess entscheidet

Ein Siteaccess ist ein Name (zum Beispiel `site`, `ger`, `admin`) und ein Ordner mit Einstellungen,
`settings/siteaccess/<name>/`. Diese Einstellungen entscheiden für jede Anfrage, die ihn verwendet:

| Entscheidet | Typische Einstellungen |
|---|---|
| Welches Design die Seiten zeichnet | `site.ini [DesignSettings] SiteDesign`, `AdditionalSiteDesignList[]` |
| In welcher Sprache Inhalte angezeigt werden | `site.ini [RegionalSettings] Locale`, `ContentObjectLocale`, `SiteLanguageList[]` |
| Wo die Website im Inhaltsbaum beginnt | `content.ini [NodeSettings] RootNode`, `site.ini [SiteSettings] IndexPage`, `DefaultPage` |
| Ob Besucher sich anmelden müssen | `site.ini [SiteAccessSettings] RequireUserLogin` |
| Welche Erweiterungen nur für dieses Gesicht eingeschaltet sind | `site.ini [ExtensionSettings] ActiveAccessExtensions[]` |
| Der Name der Website und die Metadaten | `site.ini [SiteSettings] SiteName`, `MetaDataArray[]` |

## Wie eine Anfrage einen Siteaccess auswählt

Die Regeln stehen in `site.ini [SiteAccessSettings]`:

| Einstellung | Bedeutung |
|---|---|
| `AvailableSiteAccessList[]` | Jeder Siteaccess der Installation |
| `MatchOrder` | Welche Matcher in welcher Reihenfolge probiert werden; Standard ist `uri;host;port` |
| `URIMatchType` | Wie die Adresse geprüft wird; `element` bedeutet, dass das erste Pfadelement den Siteaccess nennt (`/admin/...`) |
| `HostMatchMapItems[]` und zugehörige Host-Einstellungen | Ordnen Hostnamen Siteaccesses zu (`www.example.com;site`, `admin.example.com;admin`) |
| `RelatedSiteAccessList[]` | Siteaccesses, die Inhalte und Links teilen, zum Beispiel die Sprachen einer Website |

Mit URI-Matching verwendet `https://example.com/ger/about` den Siteaccess `ger` und zeigt `/about`. Mit Host-Matching
hat jeder Siteaccess seinen eigenen Hostnamen. Jede Regel und ihr Werkzeug zur Fehlersuche sind in
[Siteaccess-Zuordnung nach Host und Sprache](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md) und beim
Befehl für die Anfrageregeln beschrieben:

```bash
./console exp:ezrequestrules
```

## Ein öffentlicher Siteaccess, kommentiert

Eine typische `settings/siteaccess/<name>/site.ini.append.php` einer öffentlichen Website (ohne Datenbankangaben):

```ini
[SiteSettings]
SiteName=My site
# The front page and where "home" links go
IndexPage=/content/view/full/2
DefaultPage=/content/view/full/2

[SiteAccessSettings]
# Visitors do not have to sign in
RequireUserLogin=false
# The siteaccesses that share this site's content
RelatedSiteAccessList[]
RelatedSiteAccessList[]=site
RelatedSiteAccessList[]=ger
RelatedSiteAccessList[]=admin

[DesignSettings]
# Your own design first, then the ones it falls back to
SiteDesign=mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=base

[RegionalSettings]
Locale=eng-US
ContentObjectLocale=eng-US
SiteLanguageList[]
SiteLanguageList[]=eng-US
```

Der Siteaccess der Administration sieht ähnlich aus, mit `SiteDesign=admin4` (oder einem anderen Admin-Design),
`RequireUserLogin=true` und einer eigenen `AdditionalSiteDesignList[]`.

## Siteaccesses und Erweiterungen

Zwei Einstellungen verbinden sie:

- `ActiveAccessExtensions[]` in der `site.ini` eines Siteaccess schaltet eine Erweiterung nur für diesen Siteaccess ein.
  Verwenden Sie es für Erweiterungen, die nur für ein Gesicht sinnvoll sind (ein Admin-Werkzeug, das Theme einer
  Website).
- Eine Erweiterung kann einen Siteaccess **besitzen**, indem sie `extension/<ext>/settings/siteaccess/<sa>/` enthält.
  Dann liegen alle Einstellungen des Siteaccess in der Erweiterung, unter Versionskontrolle. Siehe
  [Einstellungen in einer Erweiterung](erweiterungseinstellungen-fuer-builder.md).

## Siteaccess-Einstellungen lesen

```bash
./console exp:ini list site.ini/DesignSettings -s <siteaccess>
./console exp:ini get site.ini/DesignSettings/SiteDesign -s <siteaccess>
./console exp:ini scopes siteaccess
```

`-s` wertet die Einstellungen so aus, wie dieser Siteaccess sie sieht. `scopes siteaccess` listet die Siteaccesses auf,
die einen Einstellungsordner haben.

In einem Template steht der aktuelle Siteaccess im Pagelayout als `$access_type.name` zur Verfügung, in PHP über
`eZSiteAccess::current()`.

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| Ein neuer Siteaccess zeigt „module not found“ oder die falsche Website | er steht nicht in `AvailableSiteAccessList[]`, oder die Zuordnungsregel schickt die Anfrage woandershin |
| Das Design ändert sich nicht | die `extension/<ext>/settings/site.ini.append.php` einer Erweiterung setzt `SiteDesign` und gewinnt gegen den Siteaccess-Ordner (siehe die Reihenfolge in [Einstellungsebenen](einstellungsebenen-und-ihre-reihenfolge.md)) |
| Links springen zu einer anderen Website | `RelatedSiteAccessList[]` oder die Host-Zuordnung |
| Inhalte erscheinen in der falschen Sprache | `SiteLanguageList[]` (der erste Eintrag ist die Hauptsprache), `ShowUntranslatedObjects` |

## Verwandte Seiten

- Zurück: [Einstellungsebenen und die Reihenfolge, in der sie gewinnen](einstellungsebenen-und-ihre-reihenfolge.md)
- Weiter: [Einstellungen in einer Erweiterung](erweiterungseinstellungen-fuer-builder.md)
- [Siteaccess-Zuordnung nach Host und Sprache](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)
- [Anfrageregeln](../../../../eng-us/features/6.0/request-rules.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
