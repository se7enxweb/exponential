---
title: Was Sie nach der Installation von cjw_multisite haben
audience: admin, hosting, website-creator
category: install-upgrade
order: 81
summary: Ein Rundgang durch eine frische Installation von cjw_multisite. Die Adressen und Siteaccesses, der Inhaltsbaum mit der Wurzel der Startseite und der Startseite, Tags, Layouts, Sektionen, Benutzergruppen und Rollen, die geschriebenen Einstellungsdateien, die eingeschalteten Erweiterungen und eine Checkliste für den ersten Blick.
keywords: cjw_multisite, nach der Installation, Startseite, CJW JAC Exponential, Starter, Layouts, Tags, Sektion Restricted, Partners, Members, my_feed, ezurlmultisite.ini, settings/siteaccess, Checkliste, after install, starter site, Restricted section, check list
type: help
---
# Was Sie nach der Installation von cjw_multisite haben

Diese Seite beschreibt eine neue Installation mit `cjw_multisite` und den Sprachen `eng-US` und `ger-DE`: was auf dem Bildschirm zu sehen
ist, was im Inhaltsbaum und in der Datenbank steht und welche Dateien der Installer geschrieben hat. Sie ist für den Administrator oder
Hoster, der gerade die Installation ausgeführt hat und sie prüfen will, und für Website-Ersteller, die auf der Startseite aufbauen. Jeder
Punkt nennt den Code, der ihn angelegt hat, sodass Sie ihn in [Die Installerskripte](die-installerskripte-von-cjw-multisite.md) nachschlagen können.

## Inhalt

1. [Die Adressen](#die-adressen)
2. [Der Inhaltsbaum](#der-inhaltsbaum)
3. [Tags, Layouts und Pfade](#tags-layouts-und-pfade)
4. [Sektionen, Benutzergruppen und Rollen](#sektionen-benutzergruppen-und-rollen)
5. [Die geschriebenen Einstellungsdateien](#die-geschriebenen-einstellungsdateien)
6. [Erweiterungen und Tabellen](#erweiterungen-und-tabellen)
7. [Was nicht da ist](#was-nicht-da-ist)
8. [Ein erster Blick: Checkliste](#ein-erster-blick-checkliste)
9. [Verwandte Seiten](#verwandte-seiten)

## Die Adressen

Mit Zugriff über die URL auf `https://www.example.com`:

| Adresse | Siteaccess | Zeigt |
|---|---|---|
| `https://www.example.com/` | `site` (`DefaultAccess`) | die Startseite der Startseite, in der Hauptsprache |
| `https://www.example.com/ger/` | `ger` | dieselbe Website auf Deutsch |
| `https://www.example.com/admin/` | `admin` | die Administration, Design `admin4l` |
| `https://www.example.com/editor/` | `editor` | die Administration für Redakteure |
| `https://www.example.com/adminui/` | `adminui` | das Design AdminUI, wenn `exp_adminui` installiert ist |

Der Name der Website (`[SiteSettings] SiteName`) ist, was Sie eingegeben haben, oder **CJW JAC Exponential**, wenn Sie ihn leer gelassen
oder einen Standard behalten haben. Der Commit vom 11.10.2026 ließ die Startinhalte durchgehend denselben Namen nutzen: den Namen der Website
in den Seiteninformationen auf Englisch und Deutsch, den Titel und Meta-Titel der Startseite, den Footer, das Impressum und den Titel der
Cookie-Richtlinie.

Ein laufendes Beispiel für das Ergebnis ist auf [Demo-Websites](../../../../eng-us/help/designers/distribution/demo-sites.md) aufgeführt.

## Der Inhaltsbaum

| Knoten | Remote-ID | Hinweise |
|---|---|---|
| Starter | `cjw-starter-n-root` | direkt unter der Inhaltswurzel; die Inhaltswurzel (`RootNode`) und `PathPrefix` der öffentlichen Siteaccesses |
| Startseite („Startseite“, `ng_frontpage`) | `cjw-starter-n-779` | `IndexPage` und `DefaultPage` der öffentlichen Siteaccesses |
| Der Konfigurationsordner | `cjw-starter-n-807` | verborgen; enthält die Seiteninformationen, die Cookie-Richtlinie und die Links im Kopf |
| Die Datenschutzseite („Datenschutzerklärung“) | `cjw-starter-n-809` | `menu.ini [SiteInfo] PrivacyPolicyID` und `CookiePolicyID` |

Unter der Wurzel: 137 Objekte aus `cjw_multisite_democontent`, darunter 61 Bilder, 23 Artikel, 9 Nachrichten, 6 Banner und je 5 Galerien,
Landingpages, Container und Verknüpfungen (siehe [Das Demo-Inhaltspaket](das-demo-inhaltspaket-cjw-multisite-democontent.md#die-inhaltsobjekte)). Ihre
Veröffentlichungsdaten sind die der Quellseite. Die Administration beginnt an der Inhaltswurzel (Knoten 2) und zeigt also den Baum der
Startseite und den Rest der Basisdaten.

## Tags, Layouts und Pfade

- **Tags**: ein Tag „Starter“ (`cjw-starter-t-root`) mit den fünf Tags aus `cjw-starter-import.json` darunter, mit den eztags-Feldern der
  Startobjekte verknüpft.
- **Layouts der Startseite**: 23 Layouts (6 davon geteilt), 113 Zonen, 269 Blöcke und 11 Regeln mit IDs ab 900000, aus
  `cjw-explayouts.json`. Ihre Siteaccess-Regeln nennen `site` und `ger`.
- **Layouts der Administration**: `admin_3col`, `admin_2col` und `admin_full` mit ihren Zonen, Blöcken und Regeln, IDs ab 100001, aus
  `extension/explayouts/share/fragments/admin_layouts_db_data.dba`, nur geladen, wenn noch keines existiert.
- **URL-Pfade**: die Pfade der Quellseite pro Sprache (`cjw-paths.json`); umbenannte Elemente hinterlassen einen Eintrag in der Historie, der
  (mit 301) auf den neuen Pfad umleitet.

## Sektionen, Benutzergruppen und Rollen

| Element | Was der Installer getan hat |
|---|---|
| Sektion **Restricted** | angelegt; der Benutzergruppe Partners und dem Archivordner der Basisdaten zugewiesen |
| Benutzergruppe **Partners** | unter Users angelegt; die Rollen Partner, Member und Anonymous zugewiesen |
| Benutzergruppe **Members** | die Gruppe „Guest accounts“ der Basisdaten, umbenannt; Member zugewiesen |
| Editors | ebenfalls Member zugewiesen |
| Anonymous | `rss/feed`, `user/login` auf jedem ausgelieferten Siteaccess außer `admin` und `adminui`, `content/read` in Sektion 3 (Media), `tags/view`, `info-collection/read`, `osm_proxy/tiles`, `shop/buy` und die Inhaltsrichtlinien des Installers von 7x |
| Member | `content/read` in Sektion 3, `tags/view`, `info-collection/read`, `osm_proxy/tiles` und seine eigenen Inhaltsrichtlinien |
| Editor | die Richtlinien für Redakteure aus dem Installer von 7x; die Geschichte der Commits fügt durch eine Richtlinie geschützte Aufrufe von Knotendaten, `uiswitch/language` und `uiswitch/design` hinzu |
| Zweistufige Anmeldung | `user2fa/setup` und `user2fa/verify` für Member, Editor und Partner, wenn `sevenx_authentication_2fa` installiert ist |

Es gibt außerdem einen RSS-Export, **My RSS Feed** unter `my_feed`, mit bis zu zehn Objekten `ng_article` unterhalb der Wurzel der
Startseite.

Die Rollen des Installers von 7x sind ausführlich in
[Rollen und Richtlinien von sevenx_multisite](../sevenx-multisite/rollen-und-richtlinien-von-sevenx-multisite.md) beschrieben.

## Die geschriebenen Einstellungsdateien

| Datei | Enthält |
|---|---|
| `settings/override/site.ini.append.php` | Listen der Siteaccesses, `MatchOrder`, `ForceVirtualHost=true`, `ActiveExtensions[]`, die Datenbankeinstellungen, Cache-Header für anonyme Besucher, Sitzungs-Handler |
| `settings/override/ezurlmultisite.ini.append.php` | die öffentlichen Siteaccesses und ihre Wurzelknoten, für Links zwischen Websites |
| `settings/siteaccess/site/` | `site.ini`, `content.ini` (`RootNode`), `menu.ini`, `override.ini` und die anderen Dateien von `siteINISettings()` |
| `settings/siteaccess/ger/` | die Kopie für Deutsch |
| `settings/siteaccess/admin/`, `editor/`, `adminui/` | die Administration |

Die Siteaccesses, die der Installer schreibt, bekommen `[SearchSettings] LogSearchStats=enabled`; die öffentlichen bekommen
`[SiteAccessRules]`, die das Modul `ezinfo` ausschalten.

## Erweiterungen und Tabellen

`ActiveExtensions[]` ist die Liste von `sevenx_multisite` plus `cjw_themes_jumper` und `cjw_tmv`, endend mit `ezurlmultisite`. Die
Administration bekommt `expai` und `exp_adminui` als Zugriffserweiterungen, wo vorhanden; `expai` ist ausgeschaltet und hat keine
Anbieterschlüssel. Die Tabellen jeder aufgeführten Erweiterung mit einem Schema wurden allein aus diesem Schema angelegt: Keine
`db_data.dba` einer Erweiterung wurde geladen. Die Zeilen der Layouts und Tags der Startseite kommen aus den Schritten für die Startinhalte,
die Layouts der Administration aus ihrem eigenen Fragment.

## Was nicht da ist

- Keine zweite Website: kein `bold` oder anderer zweiter Siteaccess.
- Keine Zeilen aus `extension/sevenx_themes_media/share/db_data.dba` oder `extension/explayouts/share/db_data.dba`: Die Layouts der
  Startseite kommen stattdessen aus dem Inhaltspaket.
- Keine Bilder im Paket-Repository: Nach der Installation werden die Links in `extension/cjw_themes_jumper/share/` wieder entfernt; die
  Bilder liegen im Speicher der Website.

## Ein erster Blick: Checkliste

- [ ] `/` zeigt die Startseite im Design `starter`, mit dem Namen der Website im Titel.
- [ ] `/ger/` zeigt die deutsche Website; der Sprachumschalter bietet beide Sprachen an.
- [ ] Bilder erscheinen auf der Startseite und in den Galerien (wenn nicht, siehe den Hinweis unten).
- [ ] `/admin/` zeigt die Administration; die **Inhaltsstruktur** hat den Knoten „Starter“.
- [ ] **Layouts** in der Administration führt die Layouts „Starter - ...“ auf.
- [ ] Der Link zur Datenschutzseite im Cookie-Banner öffnet „Datenschutzerklärung“.
- [ ] Die Debug-Ausgabe des Setups hat keine Zeile `Starter content steps that failed`.

> **Hinweis**
> Fehlende Bilder bedeuten fast immer, dass `extension/cjw_themes_jumper` (mit seinen Ordnern `share/`) während der Installation nicht an
> seinem Platz war. Das Log enthält dann `Files of the starter content not found in extension/cjw_themes_jumper/share`.

> **Tipp**
> Sie haben auf dieser Startseite eine eigene Website gebaut? Paketieren und tragen Sie sie bei: 7x und die Exponential Foundation ermutigen
> Sie ausdrücklich dazu. Siehe [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Verwandte Seiten

- [cjw_multisite](README.md)
- [Siteaccesses und Sprachen](siteaccesses-und-sprachen-von-cjw-multisite.md)
- [Die Installerskripte](die-installerskripte-von-cjw-multisite.md)
- [Was Sie nach der Installation von sevenx_multisite haben](../sevenx-multisite/was-sie-nach-der-installation-von-sevenx-multisite-haben.md)
- [Links auf Inhalte einer anderen Website](../../../../eng-us/features/6.0/multisite-cross-site-links.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
