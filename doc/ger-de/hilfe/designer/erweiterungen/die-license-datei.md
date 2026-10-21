---
title: "Die LICENSE-Datei eines Themes"
audience: designer
category: theme-extensions
order: 90
summary: Die LICENSE-Datei hinzufügen, die ein Theme braucht, bevor jemand es nutzen darf - welchen Text Sie kopieren, wohin er gehört, wie die Lizenz in ezinfo.php, extension.xml und composer.json genannt wird und was der Design-Assistent für Sie schreibt.
keywords: LICENSE, Lizenzdatei, GPL-2.0-or-later, SPDX, Lizenztext, gnu.org, Composer license, Lizenz der Erweiterung, license file, license text, composer license, extension license
type: help
---
# Die LICENSE-Datei eines Themes

Diese Seite ist für Designer, die ein Theme veröffentlichen. Ohne Lizenzdatei gibt ein veröffentlichtes Theme niemandem
das Recht, es zu nutzen, zu ändern oder weiterzugeben, egal was das README sagt. Die Seiten zur
[Lizenzwahl](../veroeffentlichung/lizenzwahl-ueberblick.md) helfen Ihnen bei der Wahl; diese Seite handelt davon, die
Wahl in Dateien umzusetzen. Sie ist keine Rechtsberatung.

## Was die veröffentlichten Themes verwenden

`sevenx_themes_simple`, `cjw_themes_jumper` und `sevenx_themes_media` stehen unter **GPL-2.0-or-later**, wie Exponential
selbst (`"license": "GPL-2.0-or-later"` in der `composer.json` des Wurzelverzeichnisses). GitHub zeigt für sie „GPL-2.0“.

## Schritt für Schritt

1. Holen Sie den offiziellen Text der gewählten Lizenz von ihrem Herausgeber. Für die GPL Version 2:
   `https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt`. Tippen Sie ihn nie ab und kürzen Sie ihn nie.
2. Speichern Sie ihn als `LICENSE` im Wurzelverzeichnis des Themes (manche Repositories führen zusätzlich `LICENSE.md`;
   eine Datei genügt).
3. Setzen Sie darüber oder ins README Ihren Copyright-Hinweis ([Copyright- und Autorenzeilen](copyright-und-autorenzeilen.md)).
4. Nennen Sie die Lizenz einheitlich an drei weiteren Stellen:

   | Datei | Form | Beispiel |
   |---|---|---|
   | `composer.json` `license` | SPDX-Bezeichner | `GPL-2.0-or-later` |
   | `ezinfo.php` `'License'` | Voller Name | `GNU General Public License v2.0 (or any later version)` |
   | `extension.xml` `<license>` | Voller Name | derselbe |

5. Stehen Teile des Themes unter anderen Bedingungen (eine Schrift, ein Icon-Satz, Fotos), bewahren Sie ihre
   Lizenzdateien daneben auf und führen Sie sie im README auf ([Schriften, Icons und Bibliotheken Dritter](../veroeffentlichung/schriften-icons-und-bibliotheken-dritter.md)).

## Was die Assistenten schreiben

- Der [Design-Erweiterungs-Assistent](der-design-erweiterungs-assistent.md) schreibt standardmäßig einen Teil `LICENSE` („eine
  Erweiterung ohne Lizenzdatei sagt nichts darüber, wie sie genutzt werden darf“) für die gewählte Lizenz. Er bietet nur
  die in `package.ini [LicenseSettings] LicenseList[]` eingerichteten Lizenzen an und verweigert jede andere.
- Der [Paketerstellungs-Assistent](paketerstellungs-assistent-schritt-fuer-schritt.md) speichert den Lizenzbezeichner in der
  `package.xml` des Pakets ([Eine Lizenz im Paket-Assistenten wählen](paketlizenzen-im-assistenten.md)).

## Prüfen

```bash
head -3 extension/mytheme/LICENSE
grep -n "'License'" extension/mytheme/ezinfo.php
grep -n '<license>' extension/mytheme/extension.xml
grep -n '"license"' extension/mytheme/composer.json
```

Erwartet: der Titel der Lizenz in den ersten Zeilen und dieselbe Lizenz in den anderen drei Dateien genannt.

## Verwandte Seiten

- [Lizenzwahl: ein Überblick](../veroeffentlichung/lizenzwahl-ueberblick.md)
- [Die GPL für Theme-Code](../veroeffentlichung/gpl-fuer-theme-code.md)
- [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
