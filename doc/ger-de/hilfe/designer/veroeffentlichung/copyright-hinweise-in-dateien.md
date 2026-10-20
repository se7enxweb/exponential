---
title: "Copyright-Hinweise in Dateien"
audience: designer
category: theme-distribution
order: 300
summary: Wo ein Theme sein Copyright und seine Lizenz angibt - LICENSE, README, Metadatendateien und optionale Dateiköpfe -, der Hinweis, den die Free Software Foundation für GPL-Code empfiehlt, und wie Sie mit Hinweisen anderer umgehen.
keywords: Copyright-Hinweis, Dateikopf, Lizenzhinweis, GPL-Hinweis, Namensnennung, Kopfzeilen, Template-Kommentar, copyright notice, file header, license notice, attribution
type: help
---
# Copyright-Hinweise in Dateien

Diese Seite ist für Designer, die möchten, dass Copyright und Lizenz ihres Themes klar sind, wo auch immer eine Datei
landet.

## Das Minimum

1. `LICENSE` mit dem vollständigen Lizenztext.
2. Das README mit einer Copyright-Zeile und dem Namen der Lizenz.
3. `ezinfo.php` und `extension.xml` mit derselben Zeile ([Copyright- und Autorenzeilen](../erweiterungen/copyright-und-autorenzeilen.md)).

## Dateiköpfe (optional, für PHP empfohlen)

Für GPL-Code empfiehlt die Free Software Foundation einen kurzen Hinweis am Anfang jeder Quelldatei. Für eine PHP-Datei
eines Themes:

```php
<?php
/**
 * My Theme - template operators.
 *
 * @copyright Copyright (C) 2026 Your Name
 * @license GNU General Public License v2.0 or later
 */
```

Der Design-Erweiterungs-Assistent schreibt in die Dateien, die er erzeugt, einen Lizenzhinweis für die gewählte Lizenz.

Für Templates genügt ein Kommentar am Anfang, der nicht an den Browser geschickt wird:

```
{* My Theme (C) 2026 Your Name, GNU General Public License v2.0 or later *}
```

Halten Sie Köpfe in CSS und JavaScript kurz: ein Kommentar wie `/* My Theme (C) 2026 Your Name, GPL-2.0-or-later */`.

## Hinweise anderer

- **Entfernen Sie nie** einen Copyright- oder Lizenzhinweis aus einer Datei, die Sie von jemand anderem übernommen haben.
- Wenn Sie eine solche Datei ändern, fügen Sie Ihre Zeile unter der ihren hinzu.
- Wenn Sie Dateien unter verschiedenen Lizenzen kombinieren, behalten Sie den Hinweis jeder Datei.

## Schritt für Schritt

1. Entscheiden Sie, ob Sie Dateiköpfe verwenden; wenn ja, fügen Sie sie jeder PHP-Datei hinzu.
2. Fügen Sie `pagelayout.tpl` und dem Haupt-Stylesheet einen einzeiligen Kommentar hinzu.
3. Suchen Sie nach Hinweisen, die Sie versehentlich entfernt haben könnten: `git log -p -S'Copyright' --oneline | head`.

## Verwandte Seiten

- [Danksagungen und Namensnennung](../lebenszyklus/danksagungen-und-namensnennung.md)
- [Die GPL für Theme-Code](gpl-fuer-theme-code.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
