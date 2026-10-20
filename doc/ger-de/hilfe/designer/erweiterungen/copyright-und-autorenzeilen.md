---
title: "Copyright- und Autorenzeilen"
audience: designer
category: theme-extensions
order: 80
summary: Wie Sie die Copyright-Zeile eines Themes in ezinfo.php, extension.xml, Dateiköpfen und composer.json schreiben - Jahre, Rechteinhaber, frühere Autoren eines portierten oder geforkten Designs - mit echten Beispielen.
keywords: Copyright, Autor, Autoren, Rechteinhaber, Jahre, Namensnennung, Anerkennung, portiertes Design, Fork, Composer authors, copyright, author, authors, holders, years, attribution, credit, ported design, fork
type: help
---
# Copyright- und Autorenzeilen

Diese Seite ist für Designer, die ein Theme allein oder mit anderen veröffentlichen. Eine Copyright-Zeile sagt, wer die
Rechte am Werk hält; eine Autorenzeile sagt, wer es geschrieben hat. Beide müssen wahr sein.

## Wohin sie gehören

| Datei | Feld | Beispiel |
|---|---|---|
| `ezinfo.php` | `'Copyright'`, `'Author'` | `"2007 - 2026 CJW Network, JAC Systeme GmbH and 7x"`, `'7x'` |
| `extension.xml` | `<copyright>` | `Copyright (C) 2007 - 2026 CJW Network, JAC Systeme GmbH and 7x` |
| `composer.json` | `authors[]` mit `name`, `homepage`, `email`, `role` | `{ "name": "7x", "homepage": "https://se7enx.com", "role": "Author" }` |
| `LICENSE` | der Copyright-Hinweis über dem Lizenztext | `Copyright (C) 2026 Your Name` |
| Quelldateien (optional) | ein Kopfkommentar | `@copyright Copyright (C) 2026 Your Name` |

Die Beispiele stammen aus `cjw_themes_jumper`, einer Portierung eines früheren Designs des CJW Network: Seine
Copyright-Zeile behält die ursprünglichen Rechteinhaber und fügt den hinzu, der es portiert hat.

## Schritt für Schritt

1. Listen Sie jeden Rechteinhaber auf, dessen Arbeit im Theme steckt: Sie selbst, Ihren Arbeitgeber, wenn die Arbeit für
   ihn erledigt wurde, frühere Autoren eines Designs, das Sie portiert oder geforkt haben, Mitwirkende, deren Änderungen
   Sie übernommen haben (mit deren Zustimmung oder einfach unter der Lizenz, unter der sie beigetragen haben).
2. Schreiben Sie die Jahre: vom ersten Jahr der Arbeit bis zum aktuellen Jahr (`2019 - 2026`).
3. Setzen Sie dieselbe Zeile in `ezinfo.php` und `extension.xml`.
4. Nennen Sie die Autoren in `authors[]` von `composer.json`. Verwenden Sie die öffentliche Kontaktadresse einer
   Organisation, nie die Adresse einer Privatperson ohne deren Einwilligung.
5. Bewahren Sie Hinweise Dritter (Schriften, Icon-Sätze, Bibliotheken) in eigenen Dateien neben diesen Assets auf
   ([Schriften, Icons und Bibliotheken Dritter](../veroeffentlichung/schriften-icons-und-bibliotheken-dritter.md)).

## Was Sie nicht tun

- Entfernen Sie frühere Rechteinhaber nicht aus einem Design, das Sie geforkt haben. Die GPL verlangt, dass die Hinweise
  bleiben ([Danksagungen und Namensnennung](../../../../eng-us/help/designers/lifecycle/credits-and-attribution.md)).
- Nennen Sie keine Firma und keine Person als Autor, die das Werk nicht geschrieben hat.
- Setzen Sie keine Kundennamen in die Dateien eines öffentlichen Themes, außer der Kunde hat zugestimmt, genannt zu werden.

## Verwandte Seiten

- [Die LICENSE-Datei eines Themes](die-license-datei.md)
- [Copyright-Hinweise in Dateien](../veroeffentlichung/copyright-hinweise-in-dateien.md)
- [Einen Fork fair umbenennen](../../../../eng-us/help/designers/lifecycle/rebranding-a-fork.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
