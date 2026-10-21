---
title: Das Skript des Multisite-Installers
audience: developer, admin, hosting
category: install-upgrade
order: 64
summary: Was sevenx-multi-site-installer.php während einer Installation tut, der Reihe nach - seine Einstellungen, die Vorinstallation, die Einstellungen und Rollen, nach denen das Setup fragt, und die 50 Schritte der Nachinstallation.
keywords: sevenx-multi-site-installer.php, sevenxMultiSiteInstaller, eZSiteInstaller, preInstall, postInstall, initSettings, initSteps, executeSteps, EZSW-080, CreateSites, Installationsskripte, Schritte der Nachinstallation, install scripts, post-install steps
type: help
---
# Das Skript des Multisite-Installers

Diese Seite ist für Entwickler, die die Installation von `sevenx_multisite` ändern oder untersuchen, und für Hoster, die wissen müssen, was
eine Standardinstallation mit einer Datenbank und mit `settings/` tut. Sie beantwortet: was `settings/sevenx-multi-site-installer.php`
ausführt, wann und in welcher Reihenfolge. Alle Fakten stammen aus der Datei selbst (7692 Zeilen in 6.0.15) und aus
`kernel/setup/steps/ezstep_create_sites.php` und `kernel/classes/ezsiteinstaller.php` des Kernels.

## Inhalt

1. [Die Klasse und wann sie läuft](#die-klasse-und-wann-sie-läuft)
2. [Phase 1: Einstellungen, die der Installer ermittelt](#phase-1-einstellungen-die-der-installer-ermittelt)
3. [Phase 2: die Vorinstallation](#phase-2-die-vorinstallation)
4. [Phase 3: Einstellungen, Rollen und Voreinstellungen](#phase-3-einstellungen-rollen-und-voreinstellungen)
5. [Phase 4: die Schritte der Nachinstallation](#phase-4-die-schritte-der-nachinstallation)
6. [Wenn ein Schritt scheitert](#wenn-ein-schritt-scheitert)
7. [Die Installationsmethode für die Kommandozeile](#die-installationsmethode-für-die-kommandozeile)

## Die Klasse und wann sie läuft

Die Datei definiert `class sevenxMultiSiteInstaller extends eZSiteInstaller`. Der Konstruktor von `eZSiteInstaller` des Kernels ruft
`initSettings( $parameters )` und dann `initSteps()` auf, sodass jedes `new sevenxMultiSiteInstaller(...)` zuerst seine Einstellungen und
seine Liste der Schritte der Nachinstallation ermittelt. Die kleinen Hüllendateien des Pakets (siehe [Woraus es besteht](woraus-sevenx-multisite-besteht.md))
erzeugen jeweils einen und rufen eine Methode auf:

```text
CreateSites (kernel/setup/steps/ezstep_create_sites.php)
  include every <settings-file> of the site package
  eZSitePreInstall()            -> preInstall()
  install the required packages (classes, then content)
  eZSiteINISettings()           -> siteINISettings()
  eZSiteAdminINISettings()      -> adminINISettings()
  eZSiteCommonINISettings()     -> commonINISettings()
  eZSiteRoles()                 -> siteRoles()
  eZSitePreferences()           -> sitePreferences()
  eZSitePostInstall()           -> postInstall() = the 50 steps of initSteps()
  then: exp_adminui in the admin siteaccess, the editor siteaccess, the adminui siteaccess
```

Die Setup-Frontends (Web-Assistent, Kickstarter, `exp:install`) führen alle `CreateSites` aus, sodass das für jede Installationsmethode
gleich ist. Siehe [Wie der Setup-Assistent Pakete nutzt](../grundlagen/wie-der-setup-assistent-pakete-nutzt.md) und
[Wie der Kickstarter und die Konsole Pakete nutzen](../grundlagen/wie-der-kickstarter-und-die-konsole-pakete-nutzen.md).

## Phase 1: Einstellungen, die der Installer ermittelt

`initSettings()` liest die Parameter, die das Setup übergibt, und speichert, was die späteren Schritte brauchen:

| Einstellung | Wert |
|---|---|
| `template_look_class_id`, `template_look_object_id` | die Klasse `template_look` und ihr erstes Objekt |
| `admin_account_id`, `guest_accounts_id`, `anonymous_accounts_id` | aus der Zuordnung der Remote-IDs von Objekten des Setups |
| `main_site_design` | `media` |
| `extension_list` | 60 Erweiterungen, siehe [Erweiterungen, die es aktiviert](erweiterungen-die-sevenx-multisite-aktiviert.md) |
| `locales`, `primary_language` | die im Setup gewählten Sprachen, die erste die Hauptsprache |
| `fallback_locales` | `eng-US`, wenn es nicht gewählt wurde, weil die mitgelieferten Inhalte in eng-US sind |
| `user_siteaccess` | der Wert des Setups, standardmäßig `site` (`sevenx_site_user` oder leer werden ebenfalls zu `site`) |
| `admin_siteaccess` | der Wert des Setups, standardmäßig `admin` |
| `editor_siteaccess` | `editor` |
| `adminui_siteaccess` | `adminui`, wenn der Kernel ihn anlegen kann und `exp_adminui` installiert ist, sonst leer |
| `site_title` | der im Setup eingegebene Titel; wenn er leer ist oder nur die Zusammenfassung des Pakets wiederholt: „Fit & Healthy“ (oder „Bold Agency“, wenn der Benutzer-Siteaccess `bold` ist) |
| `language_based_siteaccess_list`, `language_siteaccess_map` | ein Siteaccess-Name pro zusätzlicher Sprache, siehe [Siteaccesses und Sprachen](siteaccesses-und-sprachen-von-sevenx-multisite.md) |
| `host`, `siteaccess_urls` | die Adresse der Website und die Adressen der Siteaccesses für Administration, Redaktion, Benutzer und Übersetzungen |
| `var_dir` | `var/site` |

## Phase 2: die Vorinstallation

`eZSitePreInstall()` erzeugt den Installer mit `array( 'var_dir' => 'var/site' )` und ruft `preInstall()` auf, bevor irgendein Paket
installiert wird:

1. Merkt sich die höchste Knoten-ID und Objekt-ID der Basisdaten in `$GLOBALS['sevenxBaseMaxNodeID']` und `$GLOBALS['sevenxBaseMaxObjectID']`.
   Die Korrektur der Nachinstallation nutzt sie, um Basisdaten von Paketinhalten zu unterscheiden.
2. Fügt der Klasse `folder` `tags` (ezkeyword) und `publish_date` (ezdatetime) hinzu.
3. Setzt `[FileSettings] VarDir=var/site` für den laufenden Prozess, sodass Bilder und Dateien im var-Verzeichnis der Website landen.
4. `activateSiteExtensionSettings()`: fügt der laufenden Konfiguration den Ordner `settings/` jeder Erweiterung aus `extension_list` hinzu,
   sodass die Datentypen und INI-Dateien dieser Erweiterungen bekannt sind, während die Klassen und Objekte importiert werden.
5. Registriert die Verzeichnisse der Datentypen `ezstarrating`, `ezgmaplocation`, `exp_enhanced_link`, `eztags` und die Datentypen
   `ezsrrating`, `ezgmaplocation`, `ngenhancedlink`, `eztags` in `content.ini [DataTypeSettings]`.
6. Fügt der Klasse `user` das Feld **Zweistufige Anmeldung** hinzu, wenn `extension/sevenx_authentication_2fa` existiert.
7. `dropExtensionTables()`, dann `insertDBFile()` für jedes Schema einer Erweiterung: Hier kommen die Layouttabellen und ihre Zeilen hinein.
   Siehe [Layoutdaten und share-DBA-Dateien](layoutdaten-und-share-dba-dateien.md).
8. Korrigiert die Sprach-IDs der Zeilen `eng-GB` in `eztags_keyword` und `eztags`.

## Phase 3: Einstellungen, Rollen und Voreinstellungen

Während `CreateSites` die Einstellungen baut, fragt es das Paket nach seinen eigenen. Der Installer gibt Listen von INI-Gruppen zurück:

| Funktion | Geschrieben in | Dateien |
|---|---|---|
| `siteINISettings()` | den Benutzer-Siteaccess | `menu.ini`, `override.ini`, `toolbar.ini`, `site.ini`, `image.ini`, `content.ini`, `design.ini`, `browse.ini`, `template.ini`, `contentstructuremenu.ini`, `odf.ini` |
| `adminINISettings()` | den Siteaccess der Administration | `toolbar.ini`, `contentstructuremenu.ini`, `override.ini`, `site.ini`, `content.ini`, `icon.ini`, `viewcache.ini`, `odf.ini`, `ezoe.ini`, `menu.ini` |
| `commonINISettings()` | `settings/override/` | `site.ini`, `content.ini`, `menu.ini`, `viewcache.ini`, `staticcache.ini`, `httpcache.ini`, `override.ini`, `forum.ini`, `ezoe_attributes.ini`, `ezxml.ini` und `sevenxauthentication2fa.ini`, wenn 2FA installiert ist |

Die Gruppe `settings/override/site.ini` wird mit `discard_old_values` geschrieben: Was sie nicht setzt, ist nach der Installation weg.
`siteRoles()` und `sitePreferences()` geben die Rollen und die Voreinstellungen des Administrators zurück; siehe
[Rollen und Richtlinien](rollen-und-richtlinien-von-sevenx-multisite.md).

## Phase 4: die Schritte der Nachinstallation

`postInstall()` führt die Liste der Schritte von `initSteps()` über `executeSteps()` aus. In Reihenfolge:

| # | Schritt | Was er tut |
|---|---|---|
| 1-2 | `dbBegin`, `setVersion` | eine Transaktion öffnen, die Version der Lösung festhalten (`solutionVersion()`, aus den Klassenkonstanten `MAJOR_VERSION` und `MINOR_VERSION` gebaut) |
| 3 | `postInstallFixContentObjectNameLanguages` | Sprach-IDs von Namen, Attributen und Masken mit ihrem Locale in Einklang bringen |
| 4 | `postInstallResolveRelationListIds` | per Remote-ID gespeicherte Einträge von Relationslisten in Objekt-IDs umwandeln |
| 5 | `postInstallRekeyStarRatings` | die eingespielte Bewertung des „Test Product“ auf das installierte Objekt verschieben |
| 6 | `postInstallImportNewsletterClasses` | die Klassen von `cjw_newsletter` und den Newsletter-Baum importieren |
| 7-8 | `postInstallAdminSiteaccessINIUpdate`, `postInstallUserSiteaccessINIUpdate` | Designs, Startknoten, Wurzelknoten und URLs von `admin` und `site` |
| 9-10 | `createTranslationSiteAccesses`, `createSecondarySiteaccesses` | ein Siteaccess pro zusätzlicher Sprache; `bold` und `bold_ger` |
| 11-12 | `updateTemplateLookClassAttributes`, `updateTemplateLookObjectAttributes` | zwölf zusätzliche Felder für Aussehen und Bedienung und ihre Werte |
| 13-14 | `swapNodes`, `removeContentObject` | der alte Knoten namens `eZ Publish` in älteren sauberen Daten wird mit „Home“ getauscht und entfernt; übersprungen, wenn er fehlt |
| 15 | `removeClassAttribute` | entfernt `id` aus `template_look` |
| 16-31 | Sektionen, Rollen, Benutzergruppen | Sektion Restricted, Richtlinien für Anonymous/Editor/Partner/Member, Gruppe Partners, Gruppe Members, Rollenzuweisungen |
| 32 | `updateClassAttributes` | Attributnamen von folder „Summary“ und „Display sub items“ |
| 33 | `setRSSExport` | der Feed „My RSS Feed“ unter `/rss/feed/my_feed` (nicht für Pakete `_clean`) |
| 34 | `dbCommit` | bestätigen |
| 35 | `postInstallFixPackageNodesAndExplayouts` | Platzierung von Knoten, Verweise der Layouts, Menüs, Tags, Einbettungen; leert alle Caches |
| 36 | `postInstallImportAdminLayouts` | die drei Standardlayouts der Administration |
| 37-39 | `postInstallCleanUrlText`, `postInstallRegenerateURLAliases`, `postInstallCreateSitePrefixAliases` | URL-Aliase beider Websites, mit ihren Pfadpräfixen |
| 40 | `postInstallClearLinksToContentNotInstalled` | Links in Blöcken auf Seiten, die das Paket nicht installiert hat, werden ausgeschaltet |
| 41 | `postInstallPlaceOrphanedPackageObjects` | ohne Knoten installierte Objekte bekommen einen |
| 42 | `postInstallSetSiteHomeAndPrefix` | `PathPrefix`, `IndexPage`, `DefaultPage` von `site`, `bold`, `bold_ger` und den Übersetzungen |
| 43 | `postInstallCloseInformationPagesOnPublicSiteaccesses` | das Modul `ezinfo` auf öffentlichen Siteaccesses aus |
| 44-45 | `postInstallResyncContentClassNames`, `postInstallRepairClassNameLists` | Listen von Klassennamen |
| 46 | `postInstallCreateAdminUISiteaccess` | `settings/siteaccess/adminui` aus dem Siteaccess der Administration |
| 47 | `postInstallEnableSearchStatsOnEverySiteaccess` | `LogSearchStats=enabled` |
| 48 | `postInstallWriteMultisiteURLSettings` | `settings/override/ezurlmultisite.ini.append.php` |
| 49-50 | `updateObjectAttributeFromString` (zweimal) | das Logo der Website aus `files/default/image/logo.png` und der Seitenstil `ezwebin_design_blue` |

Die Details der Schritte 35 bis 41 stehen auf [Korrekturen nach der Installation](korrekturen-nach-der-installation-von-sevenx-multisite.md).

## Wenn ein Schritt scheitert

`executeSteps()` hält beim ersten Schritt an, der einen Fehler meldet, den der Installer nicht behandelt; das eigene `handleError()` des
Installers bestätigt die offene Transaktion bei einem Abbruch. Das Setup meldet dann `EZSW-080`: Die Nachinstallation des Seitenpakets hielt
bei Schritt N von M an, und die Schritte danach liefen nicht. Deshalb ordnet der Code die Schritte so, wie er es tut: Die zwei kosmetischen
Schritte für das Logo laufen zuletzt, weil `executeSteps` die ganze Kette beim ersten Schritt abbricht, der einen Fehler meldet, und mehrere
Kernel-Schritte sind überschrieben (`swapNodes`, `removeContentObject`, `setSection`, `setRSSExport`), um einen Hinweis zu protokollieren und
weiterzumachen, wenn das, was sie nennen, nicht da ist.

> **Tipp**
> Nach einer gescheiterten Installation sagt Ihnen die Schrittnummer in `EZSW-080`, wo Sie in dieser Tabelle nachsehen. Der Installer und das
> Setup schreiben ihre Hinweise in die Debug-Ausgabe und einige davon über `eZLog::write()` in die Logdatei `setup.log` (zum Beispiel
> `no adminui siteaccess`).

## Die Installationsmethode für die Kommandozeile

Die Klasse hat außerdem eine Methode `install()` für die Nutzung außerhalb von `CreateSites`. Sie schreibt die Gruppen für den
Benutzer-Siteaccess, den Siteaccess der Administration und `settings/override` selbst und ruft dann `updateRoles()`, `updatePreferences()` und
`postInstall()` auf. Die Setup-Frontends nutzen sie nicht; sie gehen über die Hooks oben.

## Verwandte Seiten

- [sevenx_multisite](README.md)
- [Installationsskripte in einem Seitenpaket](../grundlagen/installationsskripte-in-einem-seitenpaket.md)
- [Korrekturen nach der Installation](korrekturen-nach-der-installation-von-sevenx-multisite.md)
- [Das Installerskript ändern](../eigene/das-installerskript-aendern.md)
- [4.3 Seitenpakete: was installiert wird](../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
