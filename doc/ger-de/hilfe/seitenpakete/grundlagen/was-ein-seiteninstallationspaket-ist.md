---
title: "Was ein Seiteninstallationspaket ist"
audience: admin, developer, hosting, website-creator
category: install-upgrade
order: 11
summary: Was ein Seiteninstallationspaket (ein Paket vom Typ site) ist, was es mitbringt und was es den Paketen überlässt, die es verlangt, mit sevenx_multisite als durchgearbeitetem Beispiel und den zwölf Seitenpaketen des Index von 6.0.15.
keywords: Seitenpaket, Seiteninstallationspaket, Typ site, install_type import, requires, settings-file, sevenx_multisite, cjw_multisite, Demo-Inhalte, clean, sevenx_classes, site package, site installer package, type site, demo content
type: help
---
# Was ein Seiteninstallationspaket ist

Diese Seite ist für alle, die im Setup-Assistenten ein Seitenpaket wählen, eines in `kickstart.ini` setzen oder eines an
`exp:install --package` übergeben und wissen wollen, was diese Wahl wirklich ist. Sie beantwortet: was ein Seitenpaket ist, was es enthält,
was es anderen Paketen überlässt und welche es gibt.

## Inhalt

1. [In einem Absatz](#in-einem-absatz)
2. [Die drei Teile eines Seitenpakets](#die-drei-teile-eines-seitenpakets)
3. [Ein durchgearbeitetes Beispiel: sevenx_multisite](#ein-durchgearbeitetes-beispiel-sevenx_multisite)
4. [Die Pakete, die ein Seitenpaket verlangt](#die-pakete-die-ein-seitenpaket-verlangt)
5. [Die Seitenpakete von 6.0.15](#die-seitenpakete-von-6015)
6. [Was ein Seitenpaket nicht ist](#was-ein-seitenpaket-nicht-ist)
7. [Verwandte Seiten](#verwandte-seiten)

## In einem Absatz

Ein **Seiteninstallationspaket** oder **Seitenpaket** ist ein Exponential-Paket, dessen `package.xml` `<type value="site"/>` sagt. Es
beschreibt eine ganze Startseite, bringt aber sehr wenig von dieser Website selbst mit: Es nennt die Pakete, die die Inhaltsklassen, die
Inhalte, die Erweiterungen und die Designs enthalten (`<dependencies><requires>`), und es bringt PHP-Installationsskripte mit
(`<settings><settings-file>`), die der Installer aufruft, um die Einstellungen der Siteaccesses, Rollen und Voreinstellungen zu schreiben.
Der Setup-Assistent, der Kickstarter und `exp:install` bieten nur Pakete dieses Typs als zu installierende Website an
(`eZStepSiteTypes::fetchAvailableSitePackages()` filtert nach `'type' => 'site'`, und die entfernte Liste behält nur Einträge des Index mit
`type="site"`).

## Die drei Teile eines Seitenpakets

| Teil | In `package.xml` | Was er tut |
|---|---|---|
| Beschreibung | `<name>`, `<summary>`, `<description>`, `<vendor>`, `<version>`, `<licence>`, Vorschaubild in `<files>` | Was der Assistent auf der Seite zur Wahl der Website zeigt |
| Anforderungen | `<dependencies><requires><require type="ezpackage" .../>` | Die Pakete, die mit ihm abgerufen und installiert werden |
| Installationsskripte | `<settings><settings-file filename="..."/>`, Dateien in `settings/` | PHP-Funktionen, die der Schritt zum Anlegen der Websites an festen Punkten aufruft |

Jedes Seitenpaket des Index von 6.0.15 hat `install_type="import"` und ein leeres `<install/>`: Das Seitenpaket selbst installiert nichts.
Was auf der Website landet, kommt aus den Paketen, die es verlangt, und aus seinen Installationsskripten. Siehe
[Importieren oder installieren](importieren-oder-installieren.md).

## Ein durchgearbeitetes Beispiel: sevenx_multisite

`sevenx_multisite` ist das Standard-Seitenpaket von `exp:install` und von `exp:kickstarter ini --yes`. Seine Quelle ist der Ordner
`packages/sevenx_multisite/` von [se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage). Sein Archiv auf dem
Paketserver enthält:

```text
documents/LICENCE
files/default/image/logo.png
files/default/thumbnail/thumbnail.png
package.xml
settings/ini-admin.php
settings/ini-common.php
settings/ini-site.php
settings/install-scripts.php
settings/post-install-fix.php
settings/preferences.php
settings/roles.php
settings/sevenx-multi-site-installer.php
```

Die Teile seiner `package.xml`, die hier zählen (gekürzt; die E-Mail des Betreuers ist weggelassen):

```xml
<package version="6.0.15-0" development="false" is_active="true" install_type="import">
  <name>sevenx_multisite</name>
  <vendor>7x</vendor>
  <type value="site" />
  <version>
    <number>6.0</number>
    <release>15-stable</release>
  </version>
  <licence>GPL</licence>
  <dependencies>
    <requires>
      <require type="ezpackage" name="sevenx_classes" min-version="5.1" />
      <require type="ezpackage" name="sevenx_multisite_democontent" min-version="5.1" />
    </requires>
  </dependencies>
  <settings>
    <settings-file filename='ini-site.php'/>
    <settings-file filename='ini-admin.php'/>
    <settings-file filename='ini-common.php'/>
    <settings-file filename='sevenx-multi-site-installer.php'/>
    <settings-file filename='install-scripts.php'/>
    <settings-file filename='roles.php'/>
    <settings-file filename='preferences.php'/>
  </settings>
  <install />
  <uninstall />
</package>
```

„sevenx_multisite“ zu installieren heißt also: es importieren, `sevenx_classes` (die Inhaltsklassen) und `sevenx_multisite_democontent`
(Inhaltsklassen und die Demo-Inhalte) importieren und installieren und die Installer-Klasse in `sevenx-multi-site-installer.php` über die
Hooks in `install-scripts.php`, `ini-*.php`, `roles.php` und `preferences.php` ausführen. Die Details dieses Installers stehen in
[Das Skript des Multisite-Installers](../sevenx-multisite/das-skript-des-multisite-installers.md).

> **Hinweis**
> Die Zeilen der Layouts (Tabellen `explayouts_*`) einer Website mit sevenx_multisite stehen in keinem Paket. Der Installer befüllt sie aus
> den Ordnern `share/` von Erweiterungen; siehe [Layoutdaten und share-DBA-Dateien](../sevenx-multisite/layoutdaten-und-share-dba-dateien.md).

## Die Pakete, die ein Seitenpaket verlangt

Die verlangten Pakete sind gewöhnliche Pakete mit `install_type="install"` (oder ohne, was dasselbe bedeutet) und echten
Installationselementen. Im Index von 6.0.15 sind das:

| Typ | Pakete | Installationselemente, die sie mitbringen |
|---|---|---|
| `contentclass` | `sevenx_classes`, `ezwebin_classes`, `ezflow_classes`, `ezdemo_classes` | `ezcontentclass`, eines pro Klasse |
| `contentobject` | `sevenx_multisite_democontent(_clean)`, `cjw_multisite_democontent`, `sevenx_democontent(_clean)`, `sevenx_banners`, die Inhaltspakete `ezwebin_`, `ezflow_` und `ezdemo_` | `ezcontentobject` (ein Element für alle Objekte), einige auch `ezcontentclass` |
| `extension` | `sevenx_extension`, `ezwebin_extension`, `ezflow_extension`, `ezdemo_extension`, `ezpaypal_extension`, `ezwt_extension`, `ezstarrating_extension`, `ezgmaplocation_extension` | `ezextension` |
| `sitestyle` | `sevenx_design_blue`, `_cleangray`, `_gray`, die drei `ezwebin_design_`, `ezflow_design` | `ezfile` (mit `install_type="import"`) |

Jeder Typ und Handler ist in [Pakettypen](pakettypen.md) und [Installationselemente und Handler](installationselemente-und-handler.md) erklärt.

## Die Seitenpakete von 6.0.15

Der Index unter `https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml` führt zwölf Pakete mit `type="site"` auf:

| Paket | Zusammenfassung im Index | Verlangt (Anzahl) |
|---|---|---|
| `sevenx_multisite` | `7x MultiSite Default Installation` | 2 |
| `sevenx_multisite_clean` | `7x MultiSite Default Installation (without demo content)` | 2 |
| `cjw_multisite` | `CJW Multisite` | 2 |
| `sevenx_site` | `7x Simple Website Interface` | 11 |
| `sevenx_site_clean` | `7x Simple Website Interface (without demo content)` | 10 |
| `ezflow_site`, `ezflow_site_clean` | `eZ Flow`, `eZ Flow (without demo content)` | je 9 |
| `ezwebin_site` | `Website Interface` | 11 |
| `ezwebin_site_clean` | `Website Interface (without demo content)` | 10 |
| `ezdemo_site`, `ezdemo_site_clean` | `Exponential Demo Site`, ... `(without demo content)` | je 8 |
| `plain_site` | `Plain site` | 0 |

Eine Variante `_clean` verlangt das Inhaltspaket `_clean` statt des Pakets mit Demo-Inhalten. Die Seiten pro Paket sind
[sevenx_multisite](../sevenx-multisite/README.md) und [cjw_multisite](../cjw-multisite/README.md); die vollständige Liste der Dateien auf dem
Server steht in [Was auf dem Paketserver liegt](../paketserver/was-auf-dem-paketserver-liegt.md).

## Was ein Seitenpaket nicht ist

- **Keine Erweiterung.** Erweiterungen werden über Composer oder Git installiert oder aus Paketen vom Typ `extension`; ein Seitenpaket nennt
  sie nur.
- **Nicht die ganze Website.** Klassen und Inhalte kommen aus den Paketen, die es verlangt; Layouts von sevenx_multisite kommen aus
  share-Dateien von Erweiterungen.
- **Kein Backup.** Ein Seitenpaket baut eine neue Website. Um eine vorhandene Website umzuziehen, siehe
  [ezpm, der Paketmanager auf der Kommandozeile](../../../../eng-us/features/6.0/ezpm-package-manager-cli.md).
- **Nicht für sich allein unter Setup > Pakete installiert.** Seine Paketseite sagt, dass es nichts zu installieren gibt, weil sein
  Installationstyp `import` ist.

## Verwandte Seiten

- [Grundlagen der Seitenpakete](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md): Sie haben ein eigenes gebaut? 7x und die Exponential Foundation ermutigen
  Sie ausdrücklich, es per Pull Request einzureichen.
- [Eine Installationsmethode wählen, 4.3 Seitenpakete](../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)
- [Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md)
- [Seitenpakete und die mitgelieferten Themes](../../../../eng-us/help/creators/website-creators/site-packages-and-shipped-themes.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
