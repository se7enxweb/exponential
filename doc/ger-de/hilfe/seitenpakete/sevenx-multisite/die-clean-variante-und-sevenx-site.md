---
title: Die Clean-Variante und sevenx_site
audience: admin, hosting, website-creator, developer
category: install-upgrade
order: 71
summary: Wie sich sevenx_multisite_clean von sevenx_multisite unterscheidet und was die einfacheren Seitenpakete sevenx_site und sevenx_site_clean stattdessen installieren, damit Sie das richtige wählen können.
keywords: sevenx_multisite_clean, sevenx_site, sevenx_site_clean, clean, ohne Demo-Inhalte, Website Interface, Design simple, SevenxeZWebinInstaller, sevenxezwebininstaller.php, ein Seitenpaket wählen, without demo content, simple design, choosing a site package
type: help
---
# Die Clean-Variante und sevenx_site

Diese Seite ist für Administratoren, Hoster und Website-Ersteller, die zwischen den Seitenpaketen von 7x wählen müssen, und für Entwickler,
die sie pflegen. Sie beantwortet: was „clean“ ändert und wie sich `sevenx_site` von `sevenx_multisite` unterscheidet.

## Inhalt

1. [Vier Pakete, zwei Installer](#vier-pakete-zwei-installer)
2. [sevenx_multisite_clean](#sevenx_multisite_clean)
3. [sevenx_site und sevenx_site_clean](#sevenx_site-und-sevenx_site_clean)
4. [Nebeneinander](#nebeneinander)
5. [Welches Sie wählen](#welches-sie-wählen)

## Vier Pakete, zwei Installer

| Paket | Zusammenfassung in seiner package.xml | Zusammenfassung im Index von 6.0.15 | Installer-Klasse |
|---|---|---|---|
| `sevenx_multisite` | `MultiSite Default Installation` | `7x MultiSite Default Installation` | `sevenxMultiSiteInstaller` |
| `sevenx_multisite_clean` | `MultiSite Default Installation (without demo content)` | `7x MultiSite Default Installation (without demo content)` | `sevenxMultiSiteInstaller` |
| `sevenx_site` | `Website Interface` | `7x Simple Website Interface` | `SevenxeZWebinInstaller` |
| `sevenx_site_clean` | `7x Simple Website Interface (without demo content)` | `7x Simple Website Interface (without demo content)` | `SevenxeZWebinInstaller` |

Innerhalb jedes Paars sind die PHP-Dateien in `settings/` identisch; nur `package.xml` (und bei `sevenx_site_clean` der Lizenztext und das
Vorschaubild) unterscheidet sich. Der Index führt alle vier mit `version="6.0-15-stable"`, derselben Version wie ihre eigene `package.xml`
(`<number>6.0</number>`, `<release>15-stable</release>`; bis zum 19.10.2026 als `1.0.0-0` aufgeführt).

## sevenx_multisite_clean

Die Beschreibung in seiner `package.xml` sagt, dass diese Variante die zwei Websites mit ihrer Struktur, Konfiguration, ihren Formularen und
rechtlichen Seiten installiert, ohne Demo-Inhalte. Der einzige funktionale Unterschied ist das Inhaltspaket, das sie verlangt:

```xml
<require type="ezpackage" name="sevenx_multisite_democontent_clean" min-version="5.1" />
```

Alles andere folgt aus den Inhalten, die dieses Paket installiert:

- Dieselben Siteaccesses, Erweiterungen, Einstellungen, Rollen und Layouts wie `sevenx_multisite`.
- 35 Inhaltsobjekte statt 291 (siehe [Die Demo-Inhaltspakete](die-demo-inhaltspakete.md#was-das-clean-paket-behält)).
- Verweise der Layouts auf nicht installierte Inhalte werden entfernt, Regeln ohne Ziele werden ausgeschaltet, und Links in Blöcken auf
  fehlende Seiten werden ausgeschaltet (siehe [Korrekturen nach der Installation](korrekturen-nach-der-installation-von-sevenx-multisite.md)).
- Menüeinträge für Seiten, die es nicht gibt, werden aus `menu.ini` weggelassen.
- Kein RSS-Feed: `setRSSExport()` überspringt ein Paket, dessen Name auf `_clean` endet.

`sevenxDemocontentPackageName()` in `post-install-fix.php` liest das verlangte Inhaltspaket aus der eigenen `package.xml` des Seitenpakets,
sodass die zwei Varianten jede Zeile Code teilen.

## sevenx_site und sevenx_site_clean

`sevenx_site` ist das ältere Paket mit einer Website: die öffentliche Website im Design simple auf den Demo-Inhalten der Website, mit den
Siteaccesses der Administration und der Redaktion. Sein Installer, `settings/sevenxezwebininstaller.php` (5847 Zeilen, Klasse
`SevenxeZWebinInstaller`), ist die Datei, aus der `sevenx-multi-site-installer.php` am 07.09.2026 kopiert wurde; beide stammen vom Installer
des Pakets `ezwebin_site` ab, der im Januar 2024 umbenannt wurde.

| | `sevenx_site` | `sevenx_site_clean` |
|---|---|---|
| Verlangt | `ezwt_extension`, `ezstarrating_extension`, `ezgmaplocation_extension`, `ezpaypal_extension`, `sevenx_extension`, `sevenx_classes`, `sevenx_banners`, `sevenx_democontent`, `sevenx_design_cleangray`, `sevenx_design_gray`, `sevenx_design_blue` | dieselben, mit `sevenx_democontent_clean` statt `sevenx_banners` und `sevenx_democontent` |
| Öffentliches Design | `simple` (`solutionName()`), aus `sevenx_themes_simple` | dasselbe |
| Titel der Website, wenn keiner eingegeben wird | „Exponential“ | dasselbe |

Verglichen mit `sevenx_multisite` tut sein Installer Folgendes:

- er führt 43 Erweiterungen statt 60 auf: Er fügt `bcwebshop` hinzu und lässt die Familie `explayouts`, `expquery_translator`, die
  Erweiterungen `expsite_*`, `sevenx_themes_media` und `ezurlmultisite` weg;
- er legt keine Siteaccesses für Bold Agency und keine Layouts an und spielt keine `share/*.dba` des Themes oder von `explayouts` ein;
- er hat 40 Schritte der Nachinstallation statt 50;
- er installiert die Administration weiterhin auf `admin4l` (das ohne `explayouts` jede Seite mit den eigenen Templates von admin4 zeichnet),
  die Siteaccesses der Redaktion und adminui, die zweistufige Anmeldung, die Suchstatistik und das geschlossene Modul `ezinfo`.

## Nebeneinander

| | `sevenx_multisite` | `sevenx_multisite_clean` | `sevenx_site` | `sevenx_site_clean` |
|---|---|---|---|---|
| Öffentliche Websites | Fit & Healthy, Bold Agency | dieselben, leer | eine | eine, leer |
| Öffentliches Design | `media` | `media` | `simple` | `simple` |
| Seitenlayouts (`explayouts`) | 21 | 21, Verweise beschnitten | keine | keine |
| Demo-Inhalte | 291 Objekte | 35 Objekte | `sevenx_democontent` | `sevenx_democontent_clean` |
| Veröffentlichtes Archiv (6.0.15) | 91296 Bytes | 91289 Bytes | 79291 Bytes | 69166 Bytes |

Die Archivgrößen sind die der Seitenpakete allein, ohne das, was sie verlangen; `sevenx_multisite_democontent` allein hat 62669990 Bytes.

## Welches Sie wählen

| Sie wollen | Wählen Sie |
|---|---|
| sehen, was Exponential kann, mit Layouts und zwei Websites | `sevenx_multisite` |
| eine echte Website auf der Struktur und den Layouts der Multisite bauen | `sevenx_multisite_clean` |
| eine einzelne Website im Design simple, mit Demo-Inhalten | `sevenx_site` |
| eine einzelne Website im Design simple, die Sie selbst füllen | `sevenx_site_clean` |

Installieren Sie mit `exp:install --package=<name>` oder `Site_package` in `kickstart.ini`
([6.4 Referenz zu kickstart.ini](../../../installation/06-kickstarter.md#64-referenz-zu-kickstartini)), oder wählen Sie es auf der Seite der
Seitenpakete im Setup-Assistenten. Die Sicht eines Hosters auf dieselbe Wahl steht in
[Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md).

> **Tipp**
> Keines passt? Gehen Sie von dem aus, das am nächsten liegt, und machen Sie es zu Ihrem eigenen:
> [Ein Seitenpaket forken](../eigene/ein-seitenpaket-forken.md). Wenn es anderen nützt, ermutigen 7x und die Exponential Foundation Sie,
> es einzureichen: [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Verwandte Seiten

- [sevenx_multisite](README.md)
- [Die Demo-Inhaltspakete](die-demo-inhaltspakete.md)
- [cjw_multisite im Vergleich](../cjw-multisite/sevenx-multisite-und-cjw-multisite-im-vergleich.md)
- [Seitenpakete und die mitgelieferten Themes](../../../../eng-us/help/creators/website-creators/site-packages-and-shipped-themes.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
