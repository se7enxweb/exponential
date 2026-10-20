---
title: Siteaccesses und Sprachen von cjw_multisite
audience: admin, developer, hosting
category: install-upgrade
order: 79
summary: Welche Siteaccesses eine Installation von cjw_multisite bekommt und wie die gewählten Sprachen sie formen. Der Benutzer-Siteaccess, ein Übersetzungs-Siteaccess pro weiterer Sprache und wie er benannt wird, die Siteaccesses admin, editor und adminui, der Sprachumschalter und welche Sprache Sie für die überwiegend deutschen Startinhalte wählen.
keywords: cjw_multisite, Siteaccess, site, ger, Übersetzungs-Siteaccess, TranslationSA, SiteLanguageList, PathPrefix, starter, admin, editor, adminui, MatchOrder, ger-DE, eng-US, Rückfallsprache, languageNameFromLocale, siteaccess, translation siteaccess, fallback language
type: help
---
# Siteaccesses und Sprachen von cjw_multisite

Eine Installation von `cjw_multisite` hat eine öffentliche Website, ausgeliefert in jeder Sprache, die Sie wählen, plus die Administration.
Diese Seite führt die Siteaccesses auf, die sie bekommt, wie sich ihre Namen und Einstellungen aus den bei der Installation gewählten
Sprachen ergeben und was Sie für die Startinhalte wählen. Sie ist für Administratoren, die eine Installation planen, und Hoster, die
Adressen einrichten; die genannten Einstellungen schreibt `cjw-multi-site-installer.php` (siehe
[Die Installerskripte](die-installerskripte-von-cjw-multisite.md)).

## Inhalt

1. [Die Siteaccesses auf einen Blick](#die-siteaccesses-auf-einen-blick)
2. [Die Sprachen wählen](#die-sprachen-wählen)
3. [Wie ein Übersetzungs-Siteaccess benannt wird](#wie-ein-übersetzungs-siteaccess-benannt-wird)
4. [Worauf jeder öffentliche Siteaccess gesetzt ist](#worauf-jeder-öffentliche-siteaccess-gesetzt-ist)
5. [Adressen und Zuordnung](#adressen-und-zuordnung)
6. [Der Sprachumschalter](#der-sprachumschalter)
7. [Die Siteaccesses der Administration](#die-siteaccesses-der-administration)
8. [Verwandte Seiten](#verwandte-seiten)

## Die Siteaccesses auf einen Blick

Mit dem Befehl aus der Beschreibung des Pakets, `--languages=eng-US,ger-DE`, und der Standard-Hauptsprache `eng-US`:

| Siteaccess | Liefert aus | Design | Angelegt von |
|---|---|---|---|
| `site` | die Startseite in der Hauptsprache (`eng-US`) | `starter`, dann `standard` | dem Setup, dann `postInstallUserSiteaccessINIUpdate()` |
| `ger` | die Startseite in `ger-DE` | dasselbe (eine Kopie von `site`) | `createTranslationSiteAccesses()` |
| `admin` | die Administration | `admin4l`, dann `admin4`, `admin3`, `admin2`, `admin` | dem Setup, dann `postInstallAdminSiteaccessINIUpdate()` |
| `editor` | die Administration für die Bearbeitung von Inhalten | wie aus `admin` angelegt | dem Setup (`eZStepCreateSites::createEditorSiteAccess()`) |
| `adminui` | die Administration im Design AdminUI | `adminui` | `postInstallCreateAdminUISiteaccess()`, nur wenn `extension/exp_adminui` vorhanden ist |

Anders als bei `sevenx_multisite` gibt es keine zweiten Websites: `secondarySiteaccessList()` gibt eine leere Liste zurück, und der Schritt
`createSecondarySiteaccesses` protokolliert nur, dass diese Installation keine zweiten Siteaccesses anbietet.

## Die Sprachen wählen

Die Startinhalte sind überwiegend deutsch. Von den 137 Objekten in `cjw-starter-import.json` gibt es 111 nur in `ger-DE`, 22 nur in `eng-US`
und 4 in beiden. Also:

| Sie wählen | Sie bekommen | Startinhalte |
|---|---|---|
| `eng-US` und `ger-DE` (von der Beschreibung des Pakets empfohlen) | `site` auf Englisch, `ger` auf Deutsch | vollständig in `ger`; `site` zeigt die englischen Objekte und, mit `ger-DE` in seiner `SiteLanguageList`, die deutschen |
| `ger-DE` als Hauptsprache, `eng-US` als weitere Sprache | `site` auf Deutsch, `eng` auf Englisch | vollständig in `site` |
| nur `eng-US` | nur `site` | der Kernel fügt `ger-DE` als Rückfallsprache für Inhalte hinzu (kein Siteaccess), damit die deutschen Objekte installiert werden |

Die letzte Zeile ist, was der Changelog von 6.0.15 für `eZStepCreateSites::contentFallbackLanguages()` beschreibt: Davor protokollierte eine
solche Installation 125 Fehler, und die Website konnte diese Inhalte nicht zeigen. Der Installer selbst fügt außerdem `eng-US` als
Rückfallsprache hinzu, wenn sie in den gewählten Sprachen fehlt (`fallback_locales`).

```bash
# English site plus German translation siteaccess
./console exp:install --package=cjw_multisite --languages=eng-US,ger-DE

# German as the main site, English as the translation
./console exp:install --package=cjw_multisite --language=ger-DE --languages=eng-US
```

`--language` und `--languages` sind in [Die Installation über die Konsole](../../../installation/07-installation-ueber-die-konsole.md) beschrieben; der
Kickstarter nutzt `[language_options] Primary` und `Languages[]` (siehe [Der Kickstarter](../../../installation/06-kickstarter.md)).

## Wie ein Übersetzungs-Siteaccess benannt wird

Jede gewählte Sprache außer der Hauptsprache bekommt einen Siteaccess. Sein Name kommt aus `eZSiteInstaller::languageNameFromLocale()`
(`kernel/classes/ezsiteinstaller.php`): der Teil des Locale vor dem Bindestrich, also ergibt `ger-DE` `ger` und `eng-US` `eng`. Dann:

- Zwei gewählte Locales einer Sprache (`eng-US` und `eng-GB`): Das erste behält den kurzen Namen, spätere fügen ihr Land hinzu (`eng_gb`).
- Die Namen `site`, `admin`, `editor`, `adminui` und die gewählten Namen der Siteaccesses für Benutzer, Administration und Redaktion sind
  vergeben; eine Übersetzung, die mit einem davon kollidieren würde, bekommt ihr Land und dann eine Zahl angehängt.

## Worauf jeder öffentliche Siteaccess gesetzt ist

Der Benutzer-Siteaccess (`settings/siteaccess/site/`):

| Einstellung | Wert |
|---|---|
| `[DesignSettings] SiteDesign`, `AdditionalSiteDesignList[]` | `starter`; `standard` |
| `[SiteAccessSettings] ExtensionSettingsSiteAccess` | `cjw_starter` (die Siteaccess-Einstellungen von `cjw_themes_jumper`) |
| `[SiteAccessSettings] PathPrefix` | der URL-Alias der Wurzel der Startseite (das Objekt „Starter“; `starter` in den Kommentaren des Installers) |
| `[SiteSettings] IndexPage`, `DefaultPage` | `/content/view/full/<node of cjw-starter-n-779>`, die Startseite |
| `[SiteSettings] RootNodeDepth`, content.ini `[NodeSettings] RootNode` | die Tiefe und die Knoten-ID der Wurzel der Startseite |
| `[SiteSettings] SiteName` | der Titel der Website, standardmäßig `CJW JAC Exponential` |
| `[RegionalSettings] Locale`, `ContentObjectLocale`, `SiteLanguageList` | die Hauptsprache; sie, die anderen gewählten Sprachen, dann die Rückfallsprachen |
| `[RegionalSettings] ShowUntranslatedObjects` | `disabled` |
| `[SiteAccessSettings] RequireUserLogin`, `ShowHiddenNodes` | `false`, `false` |

Ein Übersetzungs-Siteaccess ist eine Kopie davon mit eigenem `Locale` und `ContentObjectLocale`, `TextTranslation=enabled` (`disabled` für
`eng-GB`), einer `SiteLanguageList`, die mit seinem eigenen Locale beginnt, `ShowUntranslatedObjects=enabled`,
`[ContentSettings] TranslationList` auf sein Locale gesetzt und einer eigenen `SiteURL`. Seine Menüs werden nach der Korrektur der
Paketknoten aus dem Benutzer-Siteaccess kopiert (`copySiteaccessMenus()`).

Die Siteaccess-Regeln der Layouts der Startseite nennen den Benutzer-Siteaccess und alle Übersetzungs-Siteaccesses
(`cjwSeedStarterLayouts()`), ebenso die Einstellungen der Datenschutz- und Cookie-Richtlinie (`postInstallStarterPolicyPages`).

## Adressen und Zuordnung

`settings/override/site.ini.append.php` bekommt:

```ini
[SiteAccessSettings]
AvailableSiteAccessList[]=site
AvailableSiteAccessList[]=ger
# ... admin, editor, adminui
MatchOrder=uri;host
PathPrefixExclude[]=Media
PathPrefixExclude[]=Users
ForceVirtualHost=true

[SiteSettings]
DefaultAccess=site
RootNodeDepth=1
```

`MatchOrder` ist `uri;port`, wenn die Installation Zugriff über Ports nutzt. Bei Zugriff über die URL wird die Übersetzung über den Pfad
erreicht (`https://www.example.com/ger/`). Bei Zugriff über Ports bekommt eine Übersetzung den nächsten Port nach dem der Website; bei
Zugriff über Hosts nennt der Kommentar des Installers die Form `ger.example.com`. Siehe
[Zuordnung von Siteaccesses nach Host und Sprache](../../../../en/us/features/6.0/siteaccess-host-and-language-matching.md).

Eine Sitzung deckt alle öffentlichen Siteaccesses ab (`[Session] SessionNamePerSiteAccess=disabled` auf dem Benutzer-Siteaccess), während
die Administration eine eigene Sitzung behält (`SessionNameHandler=custom` in der Überschreibung).

## Der Sprachumschalter

`[RegionalSettings] TranslationSA` ordnet jedem öffentlichen Siteaccess den Namen seiner Sprache in dieser Sprache zu, ohne das Land:
`Deutsch (Deutschland)` wird zu `Deutsch` (`translationSALabel()`). Es wird in den Benutzer-Siteaccess, die Übersetzungen (als Kopien) und
die Administration geschrieben, nie in `settings/override`, weil eine Einstellung für das ganze Array dort die eigenen Werte der
Siteaccesses löschen würde. Der Umschalter des Designs starter liest es.

## Die Siteaccesses der Administration

- `admin`: Anmeldung erforderlich, `ShowHiddenNodes=true`, Wurzelknoten der Inhalte 2, `Locale` und `SiteLanguageList` aus den gewählten
  Sprachen, `ActiveAccessExtensions[]` mit `expai` und `exp_adminui`, wo vorhanden. Siehe [admin4l](../../../../en/us/features/6.0/admin4l.md).
- `editor`: vom Setup aus den Dateien der Administration angelegt; siehe
  [Der Setup-Assistent und der Siteaccess der Redaktion](../../../../en/us/features/6.0/setup-wizard-and-editor-siteaccess.md).
- `adminui`: nur über die URI erreichbar (`/adminui`), ohne Eintrag für Host, Port oder Host-Zuordnung. Siehe
  [exp_adminui](../../../../en/us/features/6.0/exp-adminui.md).

> **Hinweis**
> Anonymous darf sich auf jedem ausgelieferten Siteaccess außer `admin` und `adminui` anmelden (`siteRoles()`), sodass der Siteaccess der
> Redaktion und die öffentlichen das Anmeldeformular annehmen.

## Verwandte Seiten

- [cjw_multisite](README.md)
- [Die Installerskripte](die-installerskripte-von-cjw-multisite.md)
- [Was Sie nach der Installation haben](was-sie-nach-der-installation-von-cjw-multisite-haben.md)
- [Siteaccesses und Sprachen von sevenx_multisite](../sevenx-multisite/siteaccesses-und-sprachen-von-sevenx-multisite.md)
- [Übersetzungen und Sprachen](../../../../en/us/features/6.0/translations-and-languages.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
