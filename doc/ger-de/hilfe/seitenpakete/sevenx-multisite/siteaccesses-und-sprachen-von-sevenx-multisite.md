---
title: Siteaccesses und Sprachen von sevenx_multisite
audience: admin, developer, hosting
category: install-upgrade
order: 65
summary: Jeder Siteaccess, den eine Installation von sevenx_multisite anlegt, die Einstellungen, die der Installer für jeden schreibt, wie die Websites zugeordnet werden und wie aus den im Setup gewählten Sprachen Siteaccesses werden.
keywords: Siteaccess, site, bold, bold_ger, admin, editor, adminui, Übersetzungs-Siteaccess, PathPrefix, MatchOrder, SiteLanguageList, TranslationSA, languageNameFromLocale, eng-US, ger-DE, siteaccess, translation siteaccess
type: help
---
# Siteaccesses und Sprachen von sevenx_multisite

Diese Seite ist für Administratoren und Hoster, die eine Installation von `sevenx_multisite` betreiben und wissen müssen, welche Siteaccesses
sie hat, was in jedem gesetzt ist und was mit den im Setup gewählten Sprachen geschieht. Alles hier ist aus
`settings/sevenx-multi-site-installer.php` und dem Kernel-Schritt `kernel/setup/steps/ezstep_create_sites.php` gelesen.

![Die Anmeldeseite des Siteaccess der Administration](../../../../eng-us/help/images/help/sp-sevenx-login.webp)

## Inhalt

1. [Die Siteaccesses auf einen Blick](#die-siteaccesses-auf-einen-blick)
2. [Der Benutzer-Siteaccess](#der-benutzer-siteaccess)
3. [Bold Agency: bold und bold_ger](#bold-agency-bold-und-bold_ger)
4. [admin, editor und adminui](#admin-editor-und-adminui)
5. [Einstellungen, die alle Siteaccesses teilen](#einstellungen-die-alle-siteaccesses-teilen)
6. [Sprachen und Übersetzungs-Siteaccesses](#sprachen-und-übersetzungs-siteaccesses)

## Die Siteaccesses auf einen Blick

| Siteaccess | Angelegt von | Liefert aus | Design |
|---|---|---|---|
| `site` (der Benutzer-Siteaccess des Setups) | `CreateSites`, dann dem Installer | Fit & Healthy | `media` |
| einer pro zusätzlicher Sprache (zum Beispiel `ger`) | `createTranslationSiteAccesses()` | Fit & Healthy in dieser Sprache | von `site` kopiert |
| `bold` | `createSecondarySiteaccesses()` mit Einstellungen, die `sevenx_themes_media` mitbringt | Bold Agency auf Englisch | `media` |
| `bold_ger` | dasselbe | Bold Agency auf Deutsch | `media` |
| `admin` (der Siteaccess der Administration des Setups) | `CreateSites`, dann dem Installer | die Administration | `admin4l` |
| `editor` | `createEditorSiteAccess()` des Kernels nach der Nachinstallation | Bearbeitung von Inhalten | `editor` |
| `adminui` | `postInstallCreateAdminUISiteaccess()` (oder dem Kernel), wenn `exp_adminui` installiert ist | die Administration im Design Admin UI | aus `exp_adminui` |

`bold` und `bold_ger` werden nur hinzugefügt, wenn ihre Einstellungen existieren, entweder bereits in `settings/siteaccess/<name>/` oder von
einer Erweiterung in `extension/*/settings/siteaccess/<name>/` mitgebracht (`secondarySiteaccessIsAvailable()`). Die Theme-Erweiterung
`sevenx_themes_media` bringt beide mit.

## Der Benutzer-Siteaccess

`postInstallUserSiteaccessINIUpdate()` und `siteINISettings()` schreiben nach `settings/siteaccess/site/`:

| Datei und Gruppe | Einstellungen |
|---|---|
| `site.ini [DesignSettings]` | `SiteDesign=media`, `AdditionalSiteDesignList` `ezwebin`, `standard`, `base` |
| `site.ini [SiteSettings]` | `SiteName` (der Titel der Website), `SiteDescription` „An Exponential multisite installation“, `IndexPage` und `DefaultPage` `/content/view/full/<home node>`, `RootNodeDepth`, `SiteURL`, `MetaDataArray`, `LoginPage=embedded`, `AdditionalLoginFormActionURL`, das auf `/user/login` der Administration zeigt |
| `site.ini [SiteAccessSettings]` | `RequireUserLogin=false`, `ShowHiddenNodes=false`, `ForceVirtualHost=true`, `PathPrefix`, `RelatedSiteAccessList` |
| `site.ini [RegionalSettings]` | `Locale` und `ContentObjectLocale` (die Hauptsprache, sonst `eng-US`), `SiteLanguageList`, `ShowUntranslatedObjects=disabled` |
| `site.ini [Session]` | `SessionNamePerSiteAccess=disabled` |
| `site.ini [FileSettings]` | `VarDir=var/site` |
| `content.ini [NodeSettings]` | `RootNode` = der Startknoten |

Der Startknoten wird über seine Remote-ID `media-n-939` mit einfachem SQL gefunden (`homeNodeID()`), weil sich Knoten-IDs zwischen
Installationen unterscheiden und nach Sprache gefilterte Abfragen während der Installation nichts zurückgeben. `PathPrefix` ist die Form des
URL-Alias des Namens des Startknotens („Fit & Healthy“), geschrieben von `postInstallSetSiteHomeAndPrefix()`, sodass die Seiten der Website
ohne das eigene Pfadsegment des Startknotens ausgeliefert werden. `RequireUserLogin=false` wird ausdrücklich geschrieben, weil die
ausgelieferte `settings/site.ini` `RequireUserLogin=true` setzt.

## Bold Agency: bold und bold_ger

Bold Agency ist eine zweite Website in derselben Installation, unter dem Startknoten `media-n-940`. Ihre Siteaccesses werden über den Pfad
auf der Adresse der Hauptwebsite erreicht (`/bold/`, `/bold_ger/`); sie haben keinen eigenen Host oder Port.

`sevenx_themes_media` bringt `settings/siteaccess/bold` und `bold_ger` mit, mit dem Design `media`, `SiteName=Bold Agency`, den Sprachen
(`bold`: `eng-US`; `bold_ger`: `ger-DE`, dann `eng-US`, `TranslationList=ger-DE`) und einer Zuordnung für den Umschalter
`TranslationSA[bold]=English`, `TranslationSA[bold_ger]=Deutsch`. Der Installer schreibt dann `settings/siteaccess/bold*/site.ini.append.php`
selbst (`createSecondarySiteaccesses()`):

- absichtlich kein `[DatabaseSettings]`: Die Datenbank wird einmal in `settings/override` angegeben und vererbt;
- `RequireUserLogin=false`, `ShowHiddenNodes=false`, `RelatedSiteAccessList`, `PathPrefix` aus dem Namen des Startknotens;
- `SiteDesign=media` mit `simple`, `ezwebin`, `standard`, `base` danach;
- `IndexPage`, `DefaultPage`, `SiteURL` (die Adresse des Benutzer-Siteaccess, gefolgt von `/bold` oder `/bold_ger`), `LoginPage=embedded`
  und die Adresse des Anmeldeformulars der Administration.

`IndexPage` und `PathPrefix` lassen sich nicht dem Theme überlassen. Wie der Kommentar des Installers erklärt, hat `settings/siteaccess`
Vorrang vor `extension/<ext>/settings/siteaccess`, und die Erweiterung kann nur Knoten-IDs der Installation mitbringen, auf der sie
exportiert wurde.

## admin, editor und adminui

`adminINISettings()` und `postInstallAdminSiteaccessINIUpdate()` schreiben nach `settings/siteaccess/admin/`:

| Einstellung | Wert |
|---|---|
| `SiteDesign` | `admin4l`, mit `admin4`, `admin3`, `admin2`, `admin` in `AdditionalSiteDesignList` |
| `RequireUserLogin`, `ShowHiddenNodes`, `ForceVirtualHost` | `true` |
| `LoginPage` | `custom` |
| `SiteName` | Admin |
| `RegionalSettings` | die Hauptsprache, `SiteLanguageList` und `TranslationSA` (der Benutzer-Siteaccess und seine Übersetzungen) |
| `ExtensionSettings ActiveAccessExtensions` | `expai` und `exp_adminui`, jeweils nur, wenn die Erweiterung installiert ist |
| `content.ini RootNode` | das Elternelement des Startknotens |
| andere Dateien | `toolbar.ini`, `menu.ini` (Reiter im Kopf), `contentstructuremenu.ini`, `override.ini`, `icon.ini` (`crystal-admin`), `viewcache.ini`, `ezoe.ini`, `odf.ini` |

Den Siteaccess der Redaktion legt der Kernel nach der Nachinstallation an: `createEditorSiteAccess()` kopiert jede `*.ini.append.php` des
Siteaccess der Administration und setzt `SiteDesign=editor`, `AdditionalSiteDesignList` `admin4l`, `admin4`, `admin3`, `admin2`, `admin`,
`ExtensionSettingsSiteAccess=admin` und `SiteName=Editor`. Der Siteaccess `adminui` wird aus den Dateien des Siteaccess der Administration
mit der Designkette von Admin UI angelegt; er ist nur über die URI erreichbar (`/adminui`). Siehe
[Der Setup-Assistent und der Siteaccess der Redaktion](../../../../eng-us/features/6.0/setup-wizard-and-editor-siteaccess.md) und
[Admin UI](../../../../eng-us/features/6.0/exp-adminui.md).

## Einstellungen, die alle Siteaccesses teilen

`commonSiteINISettings()` schreibt `settings/override/site.ini.append.php`, das jeder Siteaccess erbt:

| Gruppe | Einstellungen |
|---|---|
| `[SiteAccessSettings]` | `AvailableSiteAccessList` und `RelatedSiteAccessList` (jeder ausgelieferte Siteaccess), `MatchOrder` `uri;host` (`uri;port` bei Zugriff über Ports), `PathPrefixExclude` `Media` und `Users`, `ForceVirtualHost=true` und `HostMatchMapItems` bei Zugriff über Hosts |
| `[PortAccessSettings]` | ein Eintrag pro Siteaccess bei Zugriff über Ports |
| `[SiteSettings]` | `SiteList`, `DefaultAccess` (der Benutzer-Siteaccess), `RootNodeDepth=1` |
| `[ExtensionSettings]` | `ActiveExtensions` |
| `[DatabaseSettings]` | der Treiber und die Verbindung, mit denen die Installation lief |
| `[Session]` | `SessionNameHandler=custom`, sodass die Administration eine eigene Sitzung behält |
| `[UserSettings]` | `LogoutRedirect=/`, plus der Login-Handler für 2FA, wenn installiert |

`MatchOrder` beginnt mit `uri`, weil die Siteaccesses von Bold über den Pfad erreicht werden. In `settings/override` wird kein
`[RegionalSettings]` geschrieben: Es hat Vorrang vor jedem Siteaccess, und eine dort gesetzte Sprache hielt den deutschen Siteaccess von Bold
auf eng-US fest.

## Sprachen und Übersetzungs-Siteaccesses

Die erste im Setup gewählte Sprache ist die Hauptsprache und wird vom Benutzer-Siteaccess ausgeliefert. Für jede andere gewählte Sprache
kopiert `createTranslationSiteAccesses()` den Benutzer-Siteaccess in einen neuen:

1. Der Name kommt aus `languageNameFromLocale()` in `kernel/classes/ezsiteinstaller.php`: der Teil vor dem Bindestrich (`ger-DE` wird zu
   `ger`). Wenn zwei gewählte Sprachen diesen Teil teilen, bekommt die spätere ihr Land (`eng_gb`).
2. Wenn der Benutzer-Siteaccess `bold` ist, bekommt der Name das Präfix `bold_`.
3. Ein bereits vergebener Name (`site`, `admin`, `editor`, `adminui`, `bold`, `bold_ger`, die Siteaccesses für Benutzer und Administration)
   bekommt das Land, dann eine Zahl.
4. Die Kopie bekommt `Locale` und `ContentObjectLocale` ihrer Sprache, `TextTranslation=enabled` (ausgeschaltet für `eng-GB`), eine
   `SiteLanguageList` mit ihrer Sprache zuerst, den Titel der Website und die Startseite des Benutzer-Siteaccess und
   `ExtensionSettingsSiteAccess`, auf den Benutzer-Siteaccess gesetzt, sodass sie dieselben Einstellungen des Themes nutzt.
5. `postInstallSetSiteHomeAndPrefix()` gibt ihr dasselbe `PathPrefix` und kopiert die `menu.ini.append.php` des Benutzer-Siteaccess zu ihr.

Die mitgelieferten Inhalte sind in `eng-US`. Wenn `eng-US` nicht unter den gewählten Sprachen ist, wird es danach als Rückfallsprache in
jede `SiteLanguageList` aufgenommen (`fallback_locales`), sodass Seiten ihre Inhalte weiter zeigen.

> **Hinweis**
> Bei Zugriff über Ports zählen die Übersetzungs-Siteaccesses vom Port des Benutzer-Siteaccess hoch und lassen die Ports der Administration
> und der Redaktion aus. Bei Zugriff über Hosts nutzen sie den Hostnamen der Website.

## Verwandte Seiten

- [sevenx_multisite](README.md)
- [Das Skript des Multisite-Installers](das-skript-des-multisite-installers.md)
- [Was Sie nach der Installation haben](was-sie-nach-der-installation-von-sevenx-multisite-haben.md)
- [Zuordnung von Siteaccesses nach Host und Sprache](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)
- [Sprachen](../../sprachen.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
