---
title: Die Installerskripte von cjw_multisite
audience: developer, admin
category: install-upgrade
order: 78
summary: Was cjw-multi-site-installer.php und cjw-starter-install.php während einer Installation tun, in der Reihenfolge, in der sie es tun. Die Einstellungen, von denen der Installer ausgeht, die Vorinstallation, die INI-Einstellungen, Rollen und Voreinstellungen, die Schritte der Nachinstallation und die Schritte für die Startinhalte rund um die Pakethandler des Kernels.
keywords: cjw-multi-site-installer.php, cjwMultiSiteInstaller, cjw-starter-install.php, cjwInstallStarterContent, cjwSeedStarterLayouts, cjwApplyStarterPaths, cjwLinkStarterFilesFromExtension, preInstall, postInstall, initSteps, eZSiteInstaller, executeSteps, post-install-fix.php, Installer, Nachinstallation, installer, post-install
type: help
---
# Die Installerskripte von cjw_multisite

`cjw_multisite` baut seine Website mit zwei PHP-Dateien in `settings/` des Pakets: `cjw-multi-site-installer.php` (die Klasse
`cjwMultiSiteInstaller`, 5.939 Zeilen) und `cjw-starter-install.php` (die Funktionen für die Startinhalte, 827 Zeilen), mit
`post-install-fix.php` (593 Zeilen) als Hilfe. Diese Seite folgt einer Installation der Reihe nach durch sie hindurch. Sie ist für
Entwickler, die den Installer ändern oder einen ähnlichen schreiben; Administratoren, die ein Setup-Log lesen, finden hier, was die
Meldung jedes Schritts bedeutet.

Der Kopf der Datei sagt, woher sie stammt: Sie wurde aus dem MultiSite-Installer von 7x (sevenx_multisite,
`sevenx-multi-site-installer.php`) für die Startseite der Erweiterung cjw_themes_jumper erzeugt. Die meisten Schritte sind daher dieselben
wie in [Das Skript des Multisite-Installers](../sevenx-multisite/das-skript-des-multisite-installers.md); diese Seite verweilt bei dem, was anders
ist.

## Inhalt

1. [Wann das Setup was aufruft](#wann-das-setup-was-aufruft)
2. [Die Einstellungen, von denen der Installer ausgeht](#die-einstellungen-von-denen-der-installer-ausgeht)
3. [Vor den Paketen: preInstall()](#vor-den-paketen-preinstall)
4. [INI-Einstellungen, Rollen und Voreinstellungen](#ini-einstellungen-rollen-und-voreinstellungen)
5. [Nach den Paketen: die Schritte der Nachinstallation](#nach-den-paketen-die-schritte-der-nachinstallation)
6. [Die Schritte für die Startinhalte](#die-schritte-für-die-startinhalte)
7. [Das Log lesen](#das-log-lesen)
8. [Verwandte Seiten](#verwandte-seiten)

## Wann das Setup was aufruft

Der Schritt zum Anlegen der Websites im Setup (`kernel/setup/steps/ezstep_create_sites.php`) lädt die Einstellungsdateien des Seitenpakets
und ruft die Funktionen auf, die sie definieren, in dieser Reihenfolge:

| Reihenfolge | Funktion (Einstellungsdatei) | Methode des Installers |
|---|---|---|
| 1 | `eZSitePreInstall()` (`install-scripts.php`) | `preInstall()` |
| 2 | der Kernel installiert die verlangten Pakete (`$package->install()`) | keine |
| 3 | `eZSiteINISettings()`, `eZSiteAdminINISettings()`, `eZSiteCommonINISettings()` | `siteINISettings()`, `adminINISettings()`, `commonINISettings()` |
| 4 | `eZSiteRoles()` (`roles.php`) | `siteRoles()` |
| 5 | `eZSitePreferences()` (`preferences.php`) | `sitePreferences()` |
| 6 | `eZSitePostInstall( &$parameters )` (`install-scripts.php`) | `postInstall()`, das die Schritte von `initSteps()` ausführt |

`eZSiteInstaller::executeSteps()` des Kernels führt die Schritte der Nachinstallation nacheinander aus und stoppt die Kette beim ersten
Schritt, der einen Fehler meldet; deshalb setzt der Installer robuste Reparaturen früh und kosmetische Schritte zuletzt.

## Die Einstellungen, von denen der Installer ausgeht

`initSettings()` liest die Parameter des Setups und legt diese Werte fest:

| Einstellung | Wert |
|---|---|
| `main_site_design` | `starter` |
| `user_siteaccess` | die Wahl im Setup, `site`, wenn leer |
| `admin_siteaccess`, `editor_siteaccess` | die Wahl im Setup (`admin`, wenn leer), `editor` |
| `adminui_siteaccess` | `adminui`, wenn die Erweiterung `exp_adminui` vorhanden ist und der Kernel ihn anlegen kann, sonst leer |
| `site_title` | der eingegebene Titel; `CJW JAC Exponential`, wenn er leer ist, die Zusammenfassung des Pakets, `CJW Multisite` oder `Exponential` ist |
| `extension_list` | die Liste `ActiveExtensions[]`: dieselbe Liste wie bei `sevenx_multisite` plus `cjw_themes_jumper` und `cjw_tmv`; `cjw_tmv` fällt weg, wenn `extension/cjw_tmv` fehlt |
| `language_siteaccess_map` | ein Übersetzungs-Siteaccess pro weiterer Sprache (siehe [Siteaccesses und Sprachen](siteaccesses-und-sprachen-von-cjw-multisite.md)) |
| `fallback_locales` | `eng-US`, wenn es in den gewählten Sprachen fehlt, es sei denn, das Setup übergibt eigene `fallback_language_codes` |
| `var_dir` | `var/site` |

## Vor den Paketen: preInstall()

1. Merkt sich die höchste Knoten-ID und Objekt-ID der Basisdaten (`cjwBaseMaxNodeID`, `cjwBaseMaxObjectID`), damit die spätere Korrektur
   der Knoten Basisdaten von Paketinhalten unterscheiden kann.
2. Fügt der Klasse `folder` die Attribute `tags` (`ezkeyword`) und `publish_date` (`ezdatetime`) hinzu, benannt in der Hauptsprache der
   Website.
3. Setzt für den Lauf `[FileSettings] VarDir=var/site` und stellt den Ordner `settings/` jeder Erweiterung der Liste voran
   (`activateSiteExtensionSettings()`), sodass Datentypen wie die von enhancedselection2 und eztags bekannt sind, während Klassen und
   Objekte importiert werden.
4. Verlinkt die Bilder und Dateien der Startinhalte aus `extension/cjw_themes_jumper/share/` in das Inhaltspaket
   (`cjwLinkStarterFilesFromExtension()`).
5. Registriert für den Lauf die Datentypen `ezsrrating`, `ezgmaplocation`, `ngenhancedlink` und `eztags`.
6. Fügt der Benutzerklasse das Feld für die zweistufige Anmeldung hinzu, wenn `sevenx_authentication_2fa` verfügbar ist.
7. Löscht die Tabellen jedes Schemas einer Erweiterung und legt sie neu an (`dropExtensionTables()`, `insertDBFile()`): die genannten
   (`ezstarrating`, `ezgmaplocation`, `eztags`, `explayouts`, `enhancedselection2`, `cjw_newsletter`, `syndication`) plus jede andere
   aufgeführte Erweiterung mit einer `share/db_schema.dba` oder `sql/<engine>/schema.sql` und die Zugriffserweiterungen der Administration.
   **Nur Tabellen**: Keine `db_data.dba` wird geladen (`declaredExtensionSchemas()` übergibt für jeden Eintrag `false`).
8. Repariert die Sprachspalten der eztags-Zeilen aus den Basisdaten, wenn `eng-GB` existiert.

## INI-Einstellungen, Rollen und Voreinstellungen

| Methode | Schreibt |
|---|---|
| `siteINISettings()` | `settings/siteaccess/<site>/`: Menü, Override, Toolbar, `site.ini` (Locale, `SiteLanguageList`, `LoginPage=embedded`, eine Sitzung über die öffentlichen Siteaccesses), Bild, Inhalt, Template, Menü der Inhaltsstruktur, ODF |
| `adminINISettings()` | `settings/siteaccess/<admin>/`: Anmeldung erforderlich, `ShowHiddenNodes=true`, `TranslationSA`, `ActiveAccessExtensions[]` (`expai`, `exp_adminui`, wo vorhanden), Menüs, Toolbar, Overrides, Symbole |
| `commonINISettings()` | `settings/override/`: `AvailableSiteAccessList`, `SiteList`, `DefaultAccess`, `MatchOrder` (`uri;host` oder `uri;port` bei Zugriff über Ports), `ForceVirtualHost=true`, `ActiveExtensions[]`, die Datenbankeinstellungen des gewählten Treibers, Cache-Header für anonyme Besucher (`public, max-age=300`) und mehr |
| `siteRoles()` | Richtlinien für Anonymous und Member (RSS-Feed, `user/login` auf jedem ausgelieferten Siteaccess außer `admin` und `adminui`, `content/read` in Sektion 3, `tags/view`, `info-collection/read`, `osm_proxy/tiles`) und die Richtlinien `user2fa`, wenn die zweistufige Anmeldung verfügbar ist |
| `sitePreferences()` | die Voreinstellungen des Administrators für die Panels der Administration |

Die Richtlinie `osm_proxy/tiles` ist diesem Paket eigen: Die Karten des Designs starter laden ihre Kacheln über das Modul `osm_proxy` von
`cjw_themes_jumper`.

## Nach den Paketen: die Schritte der Nachinstallation

Die Schritte von `initSteps()`, in Reihenfolge, gruppiert:

| Schritte | Was sie tun |
|---|---|
| `postInstallFixContentObjectNameLanguages`, `postInstallResolveRelationListIds`, `postInstallImportNewsletterClasses` | Reparaturen der importierten Daten; die Newsletter-Klassen von `cjw_newsletter` |
| `postInstallAdminSiteaccessINIUpdate` | Design der Administration `admin4l` mit `admin4`, `admin3`, `admin2`, `admin` danach; `RootNode=2` |
| `postInstallUserSiteaccessINIUpdate` | Der Benutzer-Siteaccess: `SiteDesign=starter`, `AdditionalSiteDesignList[]=standard`, `ExtensionSettingsSiteAccess=cjw_starter`, Startseite, Wurzel der Startseite, `PathPrefix`, `TranslationSA`; die Override-Blöcke anderer Erweiterungen für einen Siteaccess dieses Namens werden außer Gebrauch gesetzt |
| `createTranslationSiteAccesses`, `createSecondarySiteaccesses` | Ein Siteaccess pro weiterer Sprache; keine zweiten Siteaccesses |
| Schritte für Template-Look, Austausch und Entfernen, `createContentSection` (Restricted), Rollenrichtlinien, Benutzergruppen (Partners, Members), Rollenzuweisungen, Attributnamen von Ordnern | Wie im Installer von 7x |
| `setRSSExport` | Der Feed `my_feed` („My RSS Feed“) der Objekte `ng_article` unterhalb der Wurzel der Startseite |
| `postInstallFixPackageNodesAndExplayouts` | Bildet Knoten- und Objekt-IDs des Pakets auf die installierten ab (`cjwFixPackageNodesAndExplayouts()`) |
| `postInstallStarterContent` | Die Schritte für die Startinhalte unten |
| `postInstallImportAdminLayouts` | Die Layouts der Administration aus `explayouts/share/fragments/admin_layouts_db_data.dba`, IDs ab 100001, einmal |
| `postInstallRegenerateURLAliases`, `postInstallCreateSitePrefixAliases`, `postInstallStarterPaths` | URL-Aliase, dann die eigenen Pfade der Startseite aus `cjw-paths.json` |
| `postInstallClearLinksToContentNotInstalled` | Links in Titelblöcken auf Bereiche, die nicht installiert sind, werden ausgeschaltet |
| `postInstallPlaceOrphanedPackageObjects` | Platziert Objekte, die die Paketinstallation ohne Knoten hinterlassen hat, unter dem Elternelement, das ihre Objektdatei nennt |
| `postInstallSetSiteHomeAndPrefix` | `IndexPage`, `RootNode`, `RootNodeDepth` und `PathPrefix` für den Benutzer-Siteaccess und seine Übersetzungen; Menüs in die Übersetzungen kopiert |
| `postInstallStarterPolicyPages` | `menu.ini [SiteInfo] PrivacyPolicyID` und `CookiePolicyID` auf die Datenschutzseite (`cjw-starter-n-809`) |
| `postInstallCloseInformationPagesOnPublicSiteaccesses` | Das Modul `ezinfo` wird auf den öffentlichen Siteaccesses ausgeschaltet |
| `postInstallResyncContentClassNames`, `postInstallRepairClassNameLists` | Klassennamen, nach einer Sprache geschlüsselt |
| `postInstallCreateAdminUISiteaccess` | `settings/siteaccess/adminui` aus den Dateien der Administration |
| `postInstallEnableSearchStatsOnEverySiteaccess` | `LogSearchStats=enabled` |
| `postInstallWriteMultisiteURLSettings` | `settings/override/ezurlmultisite.ini.append.php` |
| zwei Schritte `updateObjectAttributeFromString` | Das Logo und der Seitenstil auf dem Objekt des Template-Looks, bewusst zuletzt |

## Die Schritte für die Startinhalte

`cjwInstallStarterContent( $siteaccesses )` läuft als Administrator (Benutzer 14) und ruft der Reihe nach auf:

| Teil | Funktion | Wirkung |
|---|---|---|
| Ergänzungen der Klassen | `cjwEnsureStarterClassAdditions()` | Fehlende Attribute aus `cjw-class-additions.xml` |
| Verweise im Rich Text | `cjwPostUnserializeStarterObjects()` | `eZContentObject::postUnserialize()` für jedes Startobjekt, was der Handler bei Remote-IDs überspringt, die nicht 32 Zeichen lang sind |
| Links | `cjwResolveStarterLinks()` | Werte `ngenhancedlink` bekommen die Objekt-ID des Ziels |
| Tags | `cjwFixStarterTags()` | Das Tag „Starter“ und seine Tags, mit den eztags-Feldern verknüpft |
| Daten | `cjwRestoreStarterDates()` | Zeitpunkte der Veröffentlichung und Änderung aus dem Paket |
| verborgener Ordner | `cjwHideStarterNodes()` | Verbirgt `cjw-starter-n-807` |
| Layouts | `cjwSeedStarterLayouts()` | Die Zeilen von `cjw-explayouts.json`, Band 900000 |
| Dateilinks entfernt | `cjwUnlinkStarterFilesFromExtension()` | Entfernt die vor der Installation angelegten Links |

Jeder Teil protokolliert sein Ergebnis; ein gescheiterter Teil wird in einer Fehlerzeile genannt, und die Installation läuft weiter. Am Ende
werden alle Inhalts-Caches geleert.

## Das Log lesen

Meldungen, nach denen Sie in der Debug-Ausgabe der Installation suchen:

```text
Base data before the content package: nodes up to ..., objects up to ...; content package cjw_multisite_democontent
Starter content files from the extension: <n> linked, <n> copied, <n> missing
Class additions of the starter content: <n> added, <n> present
Starter layouts: <n> rows in 10 tables, rules for <the user siteaccesses>
Starter paths: <n> renamed, <n> differences left
```

> **Warnung**
> Die Meldung `Files of the starter content not found in extension/cjw_themes_jumper/share` bedeutet, dass die Erweiterung fehlt oder
> unvollständig ist: Auch die Bilder der Website werden fehlen. Legen Sie die Erweiterung an ihren Platz und installieren Sie erneut.

## Verwandte Seiten

- [cjw_multisite](README.md)
- [Das Demo-Inhaltspaket](das-demo-inhaltspaket-cjw-multisite-democontent.md)
- [Siteaccesses und Sprachen](siteaccesses-und-sprachen-von-cjw-multisite.md)
- [Das Skript des Multisite-Installers von sevenx_multisite](../sevenx-multisite/das-skript-des-multisite-installers.md)
- [Installationsskripte in einem Seitenpaket](../grundlagen/installationsskripte-in-einem-seitenpaket.md)
- [Das Installerskript ändern](../eigene/das-installerskript-aendern.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
