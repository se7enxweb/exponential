---
title: Lizenzfelder in Ihren eigenen Paketen
audience: bundler, developer
category: bundling
order: 140
summary: Wo die Lizenz der Teile steht, die Sie einem Bundle hinzufügen - composer.json, ezinfo.php, extension.xml, package.xml eines Seitenpakets und Ihre LICENSE-Datei - mit den SPDX-Bezeichnern, die Exponential verwendet, der festen Lizenzliste in package.ini, wie Sie Ihre eigene Lizenz ergänzen, und Versionen nach Semantic Versioning.
keywords: Lizenzfeld, SPDX, Lizenzliste, Version, Semantic Versioning, license field, composer.json license, ezinfo.php License, extension.xml license, package.xml, package.ini LicenseSettings, DefaultLicense, LicenseList, GPL-2.0-or-later, version
type: help
---
# Lizenzfelder in Ihren eigenen Paketen

Diese Seite richtet sich an Bündler, die eigene Erweiterungen, Themes und Seitenpakete hinzufügen. Jeder Teil, den Sie
hinzufügen, sollte seine Lizenz dort angeben, wo Werkzeuge und Menschen sie lesen, und zwar überall übereinstimmend.

## Inhalt

1. [Wo eine Lizenz angegeben wird](#wo-eine-lizenz-angegeben-wird)
2. [SPDX-Bezeichner](#spdx-bezeichner)
3. [Die Lizenzliste in package.ini](#die-lizenzliste-in-packageini)
4. [Ihre eigene Lizenz zur Liste hinzufügen](#ihre-eigene-lizenz-zur-liste-hinzufügen)
5. [Versionen](#versionen)
6. [Checkliste](#checkliste)

## Wo eine Lizenz angegeben wird

| Datei | Feld | Gelesen von |
|---|---|---|
| `composer.json` | `"license": "GPL-2.0-or-later"` | Composer, Packagist, `composer licenses` |
| `ezinfo.php` | `'License' => "GNU General Public License v2.0 (or any later version)"` | **Info > Über** |
| `extension.xml` | `<license>GNU General Public License v2.0 (or any later version)</license>` | **Info > Über** (zuerst gelesen, wenn sie `<metadata>` hat), Setup > Extensions |
| `package.xml` eines Seitenpakets | der Lizenzbezeichner des Pakets | Setup > Pakete, `package/view/full` |
| `LICENSE` | der Lizenztext | Menschen |

Die eigene `composer.json` des Kernels sagt `"license": "GPL-2.0-or-later"`. Die Release-Regel für Erweiterungen:
"Setzen Sie `license` auf den vollen Text `GNU General Public License v2.0 (or any later version)` oder genau auf das,
was die Lizenzdatei der Erweiterung sagt"
([Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md#release-regel)).

## SPDX-Bezeichner

Die Paketwerkzeuge von Exponential verwenden SPDX-Bezeichner, "wo es einen gibt, sonst ein `LicenseRef-<name>` im
SPDX-Stil" ([Paketlizenzen und Semantic Versioning](../../funktionen/6.0/paketlizenzen-und-versionen.md)). Verwenden
Sie denselben Bezeichner in der `composer.json` und in der `package.xml` und die Langform in `ezinfo.php` und
`extension.xml`.

## Die Lizenzliste in package.ini

`settings/package.ini [LicenseSettings]` enthält die Liste, die der Paketassistent und die RAD-Assistenten anbieten:

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `DefaultLicense` | `GPL-2.0-or-later` | vorausgewählt |
| `LicenseList[]` | GPL, LGPL, AGPL (jeweils `-only` und `-or-later`), `MIT`, `GFDL-1.3-or-later`, `GFDL-1.3-only`, Creative Commons 1.0 bis 4.0, `CC0-1.0`, `LicenseRef-PDM-1.0`, `LicenseRef-Proprietary` | was gewählt werden darf |
| `AliasList[GPL]` | `GPL-2.0-or-later` | alte Namen, auf Bezeichner abgebildet |

"Der Server akzeptiert nur einen konfigurierten Bezeichner (oder einen Alias wie `GPL`) und lehnt alles andere ab."
Die von einem Assistenten erzeugten Dateien `ezinfo.php`, `extension.xml` und `composer.json` "tragen den gewählten
SPDX-Bezeichner".

## Ihre eigene Lizenz zur Liste hinzufügen

Wenn die Pakete Ihres Bundles eine Lizenz verwenden, die nicht auf der Liste steht, ergänzen Sie sie in
`settings/override/package.ini.append.php` Ihrer Build-Installation, wie die Funktionsseite zeigt:

```ini
<?php /* #?ini charset="utf-8"?
[LicenseSettings]
LicenseList[]=Apache-2.0

[License_Apache-2.0]
Name=Apache License 2.0
URL=https://www.apache.org/licenses/LICENSE-2.0
Group=software
*/ ?>
```

Leeren Sie danach den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`). Ob die ergänzte Lizenz neben GPL-Code
weitergegeben werden darf, ist eine Frage für einen Rechtsbeistand.

## Versionen

- Neue Pakete beginnen bei `1.0.0`; die Assistenten akzeptieren "eine strikte Version nach Semantic Versioning 2.0.0:
  `MAJOR.MINOR.PATCH` mit optionalem `-prerelease` und `+build`, höchstens 24 Zeichen"
  ([Paketlizenzen und Semantic Versioning](../../funktionen/6.0/paketlizenzen-und-versionen.md)).
- Erweiterungen schreiben die Release-Nummer in `ezinfo.php` (`'Version'`, ohne Präfix `v`) und `extension.xml`
  (`<version>`), und zwar in dem Commit, der getaggt wird
  ([Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md#release-regel)).
- Seitenpakete: siehe [Versionsnummern](../seitenpakete/anforderungen/versionsnummern.md).

## Checkliste

- [ ] `composer.json`, `ezinfo.php`, `extension.xml`, `package.xml` und `LICENSE` nennen dieselbe Lizenz.
- [ ] Der Bezeichner ist ein SPDX-Bezeichner oder ein `LicenseRef-`.
- [ ] Die Urheberrechtszeilen nennen Ihre Firma und alle früheren Autoren.
- [ ] Die Versionen stimmen in allen Dateien überein, und der Tag zeigt auf den Commit, der sie trägt.
- [ ] `php -l ezinfo.php` und `xmllint --noout extension.xml` laufen fehlerfrei
      ([Eine Erweiterung veröffentlichen](../partner/eine-erweiterung-veroeffentlichen.md)).

## Verwandte Seiten

- [Komponenten Dritter und ihre Lizenzen](komponenten-dritter-und-ihre-lizenzen.md)
- [Anforderungen an die Dokumentation von Seitenpaketen](../seitenpakete/anforderungen/README.md)
- [Erweiterungen aus einem Bundle veröffentlichen](erweiterungen-aus-einem-bundle-veroeffentlichen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
