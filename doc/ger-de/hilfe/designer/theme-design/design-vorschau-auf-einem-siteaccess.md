---
title: Ein Design auf einem Siteaccess in der Vorschau ansehen
audience: designer, admin
category: theme-design
order: 160
summary: Wie Sie ein neues oder geändertes Design mit echten Inhalten sehen, ohne die Live-Website zu berühren: ein Vorschau-Siteaccess mit denselben Inhalten und Ihrem Design, die Einstellungen, die er braucht, und wie Sie die Live-Website umstellen, wenn das Design fertig ist.
keywords: Design-Vorschau, Test-Siteaccess, Staging-Design, neuer Siteaccess, Vorschau eines Designs, Design umstellen, Design starten, AvailableSiteAccessList, RelatedSiteAccessList, SiteDesign, preview design, test siteaccess, staging design, new siteaccess, design preview, switch design, launch design
type: help
---
# Ein Design auf einem Siteaccess in der Vorschau ansehen

Diese Seite ist für Designer, die an einem Design mit den echten Inhalten arbeiten möchten, während Besucher weiterhin das alte sehen. Der
übliche Weg ist ein **Vorschau-Siteaccess**: ein zweiter Zugang zu denselben Inhalten, mit Ihrem Design. Die Seite beantwortet: welche
Einstellungen ein solcher Siteaccess braucht, wie Sie ihn erreichen und wie Sie umstellen.

## Inhalt

1. [Die Idee](#die-idee)
2. [Was ein Administrator einrichtet](#was-ein-administrator-einrichtet)
3. [Die Einstellungen des Siteaccess](#die-einstellungen-des-siteaccess)
4. [Ihn erreichen](#ihn-erreichen)
5. [Daran arbeiten](#daran-arbeiten)
6. [Live gehen](#live-gehen)
7. [Aufräumen](#aufräumen)

## Die Idee

Der Live-Siteaccess (etwa `site`) und der Vorschau-Siteaccess (etwa `site_preview`) lesen dieselbe Datenbank und dieselben Inhalte. Nur
ihre Designs unterscheiden sich. Sie entwickeln in `site_preview`; für `site` ändert sich nichts, bis Sie dessen `SiteDesign` ändern.

## Was ein Administrator einrichtet

Einen Siteaccess anzulegen, berührt Einstellungen der Installation (`settings/override/site.ini.append.php`), die nur ein Administrator
ändert. Bitten Sie um:

1. `site_preview` in `site.ini [SiteAccessSettings] AvailableSiteAccessList[]` aufgenommen;
2. `site_preview` in `RelatedSiteAccessList[]` der Siteaccesses aufgenommen, die die Datenbank teilen (damit Caches auch für ihn geleert
   werden);
3. einen Weg, ihn zu erreichen: Mit dem Standard `MatchOrder=uri;host;port` und `URIMatchType=element` funktioniert der Pfad
   `/site_preview/` bereits; ein Hostname wie `preview.example.com` braucht einen Eintrag in der Host-Zuordnung
   ([Designs und Siteaccesses](designs-und-siteaccesses.md)).
4. eine Pflicht zur Anmeldung für die Vorschau, damit Suchmaschinen und Besucher sie nie sehen:
   `[SiteAccessSettings] RequireUserLogin=true` in seiner `site.ini.append.php`.

## Die Einstellungen des Siteaccess

Beginnen Sie mit einer Kopie des Ordners des Live-Siteaccess: `settings/siteaccess/site/` nach `settings/siteaccess/site_preview/` (nur die
designbezogenen Dateien sind nötig, wenn die Erweiterungseinstellungen über `ExtensionSettingsSiteAccess` hereinkommen), und ändern Sie
dann die Design-Zeilen in `site.ini.append.php`:

```ini
[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base

[SiteAccessSettings]
RequireUserLogin=true
# the extension siteaccess settings of the live site, e.g. its overrides
ExtensionSettingsSiteAccess=site

[TemplateSettings]
# while you develop: show which template drew what
ShowTemplatePathComments=enabled
```

Kopieren Sie nie Datenbankpasswörter oder andere Geheimnisse in eine neue Datei; eine Siteaccess-Datei braucht sie normalerweise nicht,
weil die Datenbankeinstellungen aus den Override-Einstellungen der Installation kommen.

Soll Ihre Design-Erweiterung nur für die Vorschau aktiv sein, führen Sie sie nur in `ActiveAccessExtensions[]` des Vorschau-Siteaccess auf.

## Ihn erreichen

- `https://www.example.com/site_preview/` (Zuordnung über die URI) oder der Host, den der Administrator zugeordnet hat.
- Melden Sie sich mit Ihrem eigenen Konto an. Verwenden Sie ein Testkonto mit denselben Rollen wie ein Redakteur, um zu sehen, was
  Redakteure sehen.

## Daran arbeiten

- Template-Änderungen erscheinen sofort, wenn die Template-Kompilierung die Zeiten der Dateien prüft
  (`site.ini [TemplateSettings] DevelopmentMode=enabled` auf der Vorschau; nie auf der Live-Website).
- Neue Template-Dateien, neue Override-Regeln, neue INI-Zeilen: Leeren Sie den Template-Override- und den INI-Cache
  (`php bin/php/ezcache.php --clear-id=template-override` und `--clear-tag=ini`) oder bitten Sie um ein Deploy.
- Stylesheets: Mit `DevelopmentMode=enabled` steht der Packer auf Stufe 0 und liefert Ihre Dateien einzeln aus, was das Debuggen
  erleichtert ([Der Packer](der-packer.md)).

## Live gehen

1. Gehen Sie die [Checkliste zum Start eines Themes](checkliste-theme-start.md) durch.
2. Ändern Sie `SiteDesign` (und die Rückfallliste) des Live-Siteaccess auf die Werte der Vorschau.
3. Aktivieren Sie die Design-Erweiterung für den Live-Siteaccess, wenn sie nur für die Vorschau aktiv war.
4. Leeren Sie die Caches und laden Sie PHP neu (auf Exponential-Installationen mit Velocity: `./console exp:velocity deploy`), dann leeren
   Sie den View-Cache der Inhalte, damit keine mit dem alten Design gerenderte Seite ausgeliefert wird.
5. Öffnen Sie die wichtigsten Seiten als Besucher (abgemeldet) auf einem Telefon und einem Desktop.

## Aufräumen

Entfernen Sie `ShowTemplatePathComments` und `DevelopmentMode` aus jedem Siteaccess, den Besucher verwenden, und entfernen Sie den
Vorschau-Siteaccess oder lassen Sie ihn für die nächste Design-Änderung nur mit Anmeldung bestehen.

## Verwandte Seiten

- [Designs und Siteaccesses](designs-und-siteaccesses.md)
- [Ein Design debuggen](ein-design-debuggen.md)
- [Designs in einer Multisite-Installation](designs-in-einer-multisite-installation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
