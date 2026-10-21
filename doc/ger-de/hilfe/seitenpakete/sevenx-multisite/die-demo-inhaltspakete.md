---
title: Die Demo-Inhaltspakete
audience: developer, admin, website-creator
category: install-upgrade
order: 63
summary: Was sevenx_multisite_democontent und sevenx_multisite_democontent_clean enthalten - die 31 Inhaltsklassen ng_*, die Inhaltsobjekte von Fit & Healthy und Bold Agency, die Binärdateien - und wie sich die beiden unterscheiden.
keywords: sevenx_multisite_democontent, sevenx_multisite_democontent_clean, Demo-Inhalte, Paket contentobject, ng_article, ng_recipe, ng_frontpage, Fit & Healthy, Bold Agency, simplefiles, Remote-ID, media-n-939, demo content, contentobject package, remote id
type: help
---
# Die Demo-Inhaltspakete

Diese Seite ist für Entwickler, Administratoren und Website-Ersteller, die wissen wollen, mit welchen Inhalten eine Installation von
`sevenx_multisite` beginnt oder warum eine Installation `_clean` so aussieht, wie sie aussieht. Sie beantwortet: was die zwei Inhaltspakete
enthalten, wie sie aufgebaut sind und wie sie sich unterscheiden.

## Inhalt

1. [Die zwei Pakete](#die-zwei-pakete)
2. [Die 31 Inhaltsklassen ng](#die-31-inhaltsklassen-ng)
3. [Die Inhaltsobjekte](#die-inhaltsobjekte)
4. [Was das Clean-Paket behält](#was-das-clean-paket-behält)
5. [Binärdateien und Remote-IDs](#binärdateien-und-remote-ids)
6. [Dateien, die mitkommen, aber nicht installiert werden](#dateien-die-mitkommen-aber-nicht-installiert-werden)

## Die zwei Pakete

| | `sevenx_multisite_democontent` | `sevenx_multisite_democontent_clean` |
|---|---|---|
| Zusammenfassung | `MultiSite demo content` | `MultiSite site structure without demo content` |
| Typ | `contentobject`, `install_type="install"` | dasselbe |
| Version | Nummer `6.0`, Release `15-stable` | dasselbe |
| Klassendateien in `ezcontentclass/` | 31 | dieselben 31, Byte für Byte gleich |
| In `contentobjects.xml` aufgeführte Objektdateien | 291 | 35 |
| Dateien in `simplefiles/` | 194 | 6 |
| Einträge `<simple-file>` in `package.xml` | 314 | 6 |
| Größe des veröffentlichten Archivs (6.0.15) | 62669990 Bytes | 221174 Bytes |
| Verlangt von | `sevenx_multisite` | `sevenx_multisite_clean` |

Beide Pakete haben denselben Abschnitt `<install>`: 31 Elemente `ezcontentclass`, gefolgt von einem Element `ezcontentobject`
(`filename="contentobjects"`). Ihr Abschnitt `<uninstall>` führt dieselben Elemente auf. Die `contentobjects.xml` beider nennt zwei
Wurzelknoten, `media-n-940` „Bold Agency“ und `media-n-939` „Fit & Healthy“, und die Siteaccesses `site` und `admin`.

## Die 31 Inhaltsklassen ng

Aus den Klassendateien gelesen. `Netgen` und `Netgen Components` sind die in den Dateien genannten Klassengruppen.

| Bezeichner | Name | Gruppe | Attribute |
|---|---|---|---|
| `ng_accordion_item` | Accordion item | Netgen | 3 |
| `ng_article` | NG Article | Netgen | 16 |
| `ng_audio` | Audio | Netgen | 7 |
| `ng_banner` | NG Banner | Netgen | 7 |
| `ng_blog_post` | NG Blog post | Netgen | 17 |
| `ng_category` | Category | Netgen | 18 |
| `ng_component_about` | About Component | Netgen Components | 10 |
| `ng_component_features` | Features Component | Netgen Components | 73 |
| `ng_component_hero` | Hero Component | Netgen Components | 11 |
| `ng_component_lead` | Lead Component | Netgen Components | 8 |
| `ng_component_logos` | Logos Component | Netgen Components | 10 |
| `ng_component_quote` | Quote Component | Netgen Components | 51 |
| `ng_contact_form` | Contact form | Netgen | 21 |
| `ng_container` | Container | Netgen | 2 |
| `ng_cookie_policy` | Cookie policy | Netgen | 26 |
| `ng_frontpage` | NG Frontpage | Netgen | 2 |
| `ng_gallery` | NG Gallery | Netgen | 10 |
| `ng_htmlbox` | HTML box | Netgen | 3 |
| `ng_job_application_form` | Job Application Form (in-place/modal) | Netgen | 20 |
| `ng_job_position` | Job position | Netgen | 14 |
| `ng_landing_page` | Landing page | Netgen | 9 |
| `ng_lead_form` | Lead form (in-place/modal) | Netgen | 18 |
| `ng_logo` | Logo | Netgen | 6 |
| `ng_menu_item` | Menu item | Netgen | 11 |
| `ng_news` | News | Netgen | 18 |
| `ng_person` | Person | Netgen | 8 |
| `ng_recipe` | Recipe | Netgen | 21 |
| `ng_shortcut` | Shortcut | Netgen | 6 |
| `ng_site_info` | Site info | Netgen | 20 |
| `ng_topic` | Topic | Netgen | 7 |
| `ng_video` | NG Video | Netgen | 15 |

Mehrere Datentypen, die diese Klassen nutzen, kommen aus Erweiterungen: `eztags` (eztags), `ngenhancedlink` (`exp_enhanced_link`),
`sckenhancedselection` (`enhancedselection2`), `ngclasslist` (`ngclasslist`) und `xrowmetadata`. Deshalb macht `preInstall()` des
Installers die Einstellungen jeder aufgeführten Erweiterung zum Teil des Laufs, bevor irgendein Paket installiert wird
(`activateSiteExtensionSettings()`); ohne das wurden, wie der Kommentar im Installer sagt, die Datentypen von enhancedselection2 und
ngclasslist nicht gefunden.

## Die Inhaltsobjekte

Die 291 Objektdateien des vollständigen Pakets, gezählt nach der `ezremote:class_identifier` jeder Datei:

| Klasse | Objekte | Klasse | Objekte |
|---|---|---|---|
| `image` | 67 | `ng_topic` | 4 |
| `ng_article` | 27 | `ng_component_lead` | 4 |
| `ng_video` | 25 | `folder` | 4 |
| `ng_recipe` | 19 | `ng_person` | 3 |
| `ng_container` | 19 | `ng_news` | 3 |
| `ng_shortcut` | 12 | `ng_job_position` | 3 |
| `ng_logo` | 12 | `ng_site_info`, `ng_lead_form`, `ng_gallery`, `ng_frontpage`, `ng_contact_form` | je 2 |
| `ng_component_features` | 11 | `ng_component_quote`, `ng_component_logos`, `ng_category` | je 2 |
| `ng_landing_page` | 10 | `review`, `product`, `ng_audio`, `file`, `comment` | je 1 |
| `ng_blog_post` | 9 | | |
| `ng_accordion_item` | 8 | | |
| `ng_component_hero`, `ng_component_about` | je 6 | | |
| `ng_menu_item` | 7 | | |
| `ng_banner` | 6 | | |
| `ng_htmlbox` | 5 | | |

Darunter sind eine geführte Tour, die `ng_landing_page` „Tour“ (Remote-ID `showcase-s1-tour`) mit zwölf Stationen
(`object-media-o-showcase-s1-stop-01.xml` bis `-12.xml`), und ein „Test Product“ (`product`) mit einer `review` und einem `comment`. Die
Übersetzungen sind überwiegend `eng-US`; 51 Objektdateien bringen außerdem eine Übersetzung `ger-DE` mit: überwiegend die Seiten von Bold
Agency, Stellenangebote, Komponenten und Formulare, dazu die Links im Footer, die der deutsche Siteaccess `bold_ger` zeigt.

## Was das Clean-Paket behält

Die 35 Objekte von `sevenx_multisite_democontent_clean` sind das Gerüst beider Websites ohne Artikel, Rezepte, Videos oder Bilder:

| Art | Objekte |
|---|---|
| Startseiten | `Fit & Healthy` und `Bold Agency` (`ng_frontpage`) |
| Einstellungen der Website | `Fit & Healthy site info - [SITEINFO]` und `Bold Agency - [SITEINFO]` (`ng_site_info`) |
| Formulare | `Contact` (`ng_contact_form`), `Lead form` und `Job application form` (`ng_lead_form`) |
| Rechtliche Seiten | `Privacy Policy` und `Cookie Policy` für jede Website (vier `ng_article`) |
| Ordner der Medienbibliothek | `Media`, `Images`, `Files`, `Footer links` (`folder`) |
| Container | Banners, Clients & Partners, Components und seine sechs Komponentenordner, Configuration, Forms, Menu items, Videos |
| Links im Footer | Cart, Edit, Forgot Password, Login, Signup, Sitemap, Tagcloud (`ng_menu_item`) |

Die Layouts in `sevenx_themes_media/share/db_data.dba` nennen noch die Demo-Inhalte, die das Clean-Paket weglässt. Die Korrektur der
Nachinstallation entfernt diese Verweise, statt sie auf irgendein Objekt zeigen zu lassen, das dieselbe ID bekommen hat; siehe
[Korrekturen nach der Installation](korrekturen-nach-der-installation-von-sevenx-multisite.md).

## Binärdateien und Remote-IDs

Bilder, Dateien, Audio und Video werden einmal in `simplefiles/` unter kurzen gehashten Namen gespeichert (`simplefiles/4d7492d6.jpg`) und
aus Einträgen `<simple-file>` mit ihrem ursprünglichen Pfad referenziert, sodass eine Binärdatei mehreren Verweisen dienen kann (Commit vom
13.09.2026: jede Binärdatei einmal statt einmal pro Verweis speichern, damit die gebauten Archive in die Größengrenze der Verteilung
passen). Im vollständigen Paket sind das 135 Dateien `.jpg`, 5 `.jpeg`, 29 `.png`, 22 `.svg`, eine `.mp4`, eine `.wav` und eine `.txt`,
entpackt etwa 61 MB.

Jedes Objekt und jeder Knoten trägt eine Remote-ID der Form `media-o-<name or number>` und `media-n-<number>`. Der Installer verlässt sich
darauf: Knoten-IDs unterscheiden sich zwischen Installationen, Remote-IDs nicht. Die Startknoten werden als `media-n-939` (Fit & Healthy) und
`media-n-940` (Bold Agency) gefunden, die Menüs und rechtlichen Seiten über die Knoten-IDs des Pakets, die die Korrektur der
Nachinstallation abbildet.

## Dateien, die mitkommen, aber nicht installiert werden

Keine mehr. Bis zum 19.10.2026 brachte das vollständige Paket außerdem 113 Dateien namens `object-media-o-<n>.xml.bak.eztagsdedupe` und
`object-media-o-<n>.xml.bak.tagorder` in `ezcontentobject/` mit, Sicherungskopien, die `packages/build.sh` mit allem anderen packte. Nichts
las sie: `contentobjects.xml` führt nur die Dateien `object-*.xml` auf, und der Code der Nachinstallation sucht nach Dateien, die
`object-media-o-*.xml` und `^object-.+\.xml$` entsprechen. Sie wurden aus dem Quell-Repository entfernt, `build.sh` und `bin/pack.sh` lassen
nun Dateien `*.bak` und `*.bak.*` aus jedem Paket weg, und das neu gebaute Archiv (62.314.497 Bytes) liegt auf dem Paketserver.

## Verwandte Seiten

- [sevenx_multisite](README.md)
- [sevenx_classes](sevenx-classes-das-paket-der-inhaltsklassen.md)
- [Die Clean-Variante und sevenx_site](die-clean-variante-und-sevenx-site.md)
- [Layoutdaten und share-DBA-Dateien](layoutdaten-und-share-dba-dateien.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
