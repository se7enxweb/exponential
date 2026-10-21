---
title: "Eine Lizenz im Paket-Assistenten wählen"
audience: designer, admin
category: theme-extensions
order: 390
summary: Die Lizenzauswahl der Paket- und RAD-Assistenten - die eingerichtete Liste in package.ini [LicenseSettings] (GPL, LGPL, AGPL, MIT, GFDL, Creative Commons, gemeinfrei, proprietär), der Standard, wie Sie eine hinzufügen und wie Sie für ein Theme wählen.
keywords: Lizenz, package.ini, LicenseSettings, LicenseList, DefaultLicense, SPDX, GPL-2.0-or-later, CC-BY-SA-4.0, LicenseRef-Proprietary, AliasList, license, licence
type: help
---
# Eine Lizenz im Paket-Assistenten wählen

Diese Seite ist für Designer, die den Schritt **Paket Informationen** des Paket-Assistenten oder das Feld **Lizenz** der
RAD-Assistenten ausfüllen. Beide bieten eine strikte Auswahl aus einer eingerichteten Liste und verweigern jeden anderen
Wert. Diese Seite ist keine Rechtsberatung.

## Die eingerichtete Liste

`settings/package.ini [LicenseSettings]` legt fest:

- `DefaultLicense=GPL-2.0-or-later`, gewählt, wenn sich der Schritt öffnet;
- `LicenseList[]`, in der Auswahlliste gruppiert:

| Gruppe | Bezeichner |
|---|---|
| Software | `GPL-2.0-or-later`, `GPL-2.0-only`, `GPL-3.0-or-later`, `GPL-3.0-only`, `LGPL-2.1-or-later`, `LGPL-2.1-only`, `LGPL-3.0-or-later`, `LGPL-3.0-only`, `AGPL-3.0-or-later`, `AGPL-3.0-only`, `MIT` |
| Dokumentation | `GFDL-1.3-or-later`, `GFDL-1.3-only` |
| Creative Commons | `CC-BY`, `CC-BY-SA`, `CC-BY-ND`, `CC-BY-NC`, `CC-BY-NC-SA`, `CC-BY-NC-ND` in den Versionen 4.0, 3.0, 2.5, 2.0 und 1.0 |
| Gemeinfrei | `CC0-1.0`, `LicenseRef-PDM-1.0` |
| Proprietär | `LicenseRef-Proprietary` |

Der in `package.xml` gespeicherte Wert ist der Bezeichner: der SPDX-Bezeichner, wo es einen gibt, sonst ein
`LicenseRef-<name>`. `AliasList[GPL]=GPL-2.0-or-later` lässt ein altes Paket, das `GPL` angibt, richtig lesen.

## Wie Sie für ein Theme wählen

| Was das Paket enthält | Eine übliche Wahl | Warum |
|---|---|---|
| Templates, CSS, JavaScript, PHP | `GPL-2.0-or-later` | Die Lizenz von Exponential und der veröffentlichten Themes |
| Nur Dokumentation | `GFDL-1.3-or-later` | Die Lizenz der Dokumentation von Exponential |
| Nur Bilder und Grafiken | eine Creative-Commons-Lizenz | Für Werke gemacht, die keine Software sind |
| Code, den Sie nicht veröffentlichen | `LicenseRef-Proprietary` | Lesen Sie zuerst [Kommerzielle Bedingungen neben der GPL](../veroeffentlichung/kommerzielle-bedingungen-neben-der-gpl.md) |

Templates und PHP, die innerhalb von Exponential laufen, werden meist als GPL-Werk behandelt; lesen Sie
[Die GPL für Theme-Code](../veroeffentlichung/gpl-fuer-theme-code.md), bevor Sie für Code etwas anderes wählen.

## Eine Lizenz zur Liste hinzufügen

Ein Administrator fügt eine in `settings/override/package.ini.append.php` hinzu:

```ini
[LicenseSettings]
LicenseList[]=Apache-2.0

[License_Apache-2.0]
Name=Apache License 2.0
URL=https://www.apache.org/licenses/LICENSE-2.0
Group=software
```

Leeren Sie dann den INI-Cache. Der neue Eintrag erscheint in der Auswahlliste jedes Assistenten.

## Verwandte Seiten

- [Lizenzwahl: ein Überblick](../veroeffentlichung/lizenzwahl-ueberblick.md)
- [Die LICENSE-Datei eines Themes](die-license-datei.md)
- [Paketlizenzen und -versionen](../../../funktionen/6.0/paketlizenzen-und-versionen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
