---
title: Namensnennung und Urheberrechtshinweise
audience: developer, designer, app-creator
category: extension-development
order: 70
summary: Wo die Namensnennung in einer Erweiterung steht - Dateiköpfe, Lizenzdateien, Includes in ezinfo.php, authors in composer.json, Credits in der README, Git-Historie - und wie Sie Ihren Namen hinzufügen, ohne den eines anderen zu entfernen.
keywords: Namensnennung, Urheberrecht, Copyright, Hinweis, Credits, Dateikopf, LICENSE, COPYRIGHT, Includes, authors, README, Git-Historie, ursprünglicher Autor, attribution, copyright notice, credit, file header, Git history, original author
type: help
---
# Namensnennung und Urheberrechtshinweise

Teil 5, Kapitel 7. Die Namensnennung ist keine Höflichkeit, die Sie weglassen dürfen: Die Lizenzen verlangen, dass
die Hinweise erhalten bleiben, und die Menschen, die den Code geschrieben haben, verdienen es, genannt zu werden.
Dieses Kapitel listet jede Stelle auf, an der in einer Erweiterung Namensnennung steht, und zeigt, wie Sie Ihre eigene
hinzufügen, ohne die eines anderen zu entfernen.

## Die Stellen, an denen Namensnennung steht

| Stelle | Was dort steht | Pflicht? |
|---|---|---|
| Dateiköpfe | die Copyright-Zeile(n) und der Lizenzhinweis dieser Datei | vorhandene behalten: ja |
| `LICENSE` (oder `LICENSE.md`) | der vollständige Text der Lizenz der Erweiterung | ja, für die Weitergabe |
| `COPYRIGHT.md` oder eine Credits-Datei | wer an was das Urheberrecht hält | empfohlen |
| Die eigenen `LICENSE`-Dateien mitgelieferter Bibliotheken | der Lizenztext der Bibliothek, direkt daneben | ja |
| `ezinfo.php` und `extension.xml` | Einträge `Copyright`, `License` und `Includes` für enthaltene Software | die Konvention dieses Projekts |
| `composer.json` | `authors`, `license` | dringend empfohlen |
| `README.md` | ein Abschnitt „Credits“: wer was gemacht hat, mit Links | empfohlen |
| Git-Historie | jeder Commit mit seinem Autor | behalten: forken, nicht kopieren |

## Dateiköpfe

Ein typischer GPL-Kopf in einer alten Erweiterung:

```php
<?php
//
// Copyright (C) 2009 Original Author. All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
```

Wenn Sie die Datei wesentlich ändern, fügen Sie Ihre Zeile unter der des Autors hinzu. Ersetzen Sie sie nicht, und
„aktualisieren“ Sie nicht seine Jahreszahl:

```php
<?php
//
// Copyright (C) 2009 Original Author. All rights reserved.
// Copyright (C) 2026 Your Name: PHP 8 port and Content Security Policy changes.
//
// This program is free software; ...
```

Die zweite Zeile erfüllt außerdem die Anforderung der GPL 2, geänderte Dateien zu kennzeichnen („stating that you
changed the files and the date“). Ein Vermerk im Changelog ist die andere Hälfte.

Eine neue Datei, die Sie von Grund auf schreiben, trägt nur Ihre Zeile (und den Lizenzhinweis der Erweiterung).

## ezinfo.php: Includes

Die Seite „Über“ der Administration zeigt unter jeder Erweiterung die Software, die sie nach eigener Angabe enthält. Der
Kernel liest jeden Schlüssel in `ezinfo.php`, der mit `Includes` beginnt, als einzelnen Eintrag oder als Liste.
se7enxweb/expchangeclass nennt die Erweiterung, von der sie geforkt wurde:

```php
'Includes' => array(
    array( 'Name' => "eZChangeclass",
           'Copyright' => "Copyright (C) 2007 (the original author)",
           'License' => "GNU General Public License v2.0" ) )
```

(In der echten Datei steht der Name des Autors ausgeschrieben; hier ist er gekürzt.) Jeder Eintrag kann außerdem
`Version` und `Info_url` enthalten.

## composer.json: authors

```json
"authors": [
    { "name": "Your Name", "homepage": "https://example.org", "role": "Maintainer" },
    { "name": "Original Author", "role": "Original author" }
]
```

expchangeclass nennt ihren Maintainer und an zweiter Stelle den ursprünglichen Autor „with the role Original author
of eZChangeclass“. Packagist zeigt die Liste auf der Seite des Pakets.

## README: Credits

Ein kurzer Abschnitt, wie ihn se7enxweb/sevenx_dse hat: eine Tabelle mit jedem Teil, wer ihn gemacht hat, und einem
Link. Die Credits von sevenx_dse nennen das eingebettete Datenbankwerkzeug, das Projekt, von dem es geforkt wurde, und
die eigene Integrationsarbeit der Erweiterung, jeweils mit ihrem Urheber, gefolgt von einem Satz des Dankes. Der Dank
ist keine Pflicht, aber er trifft den richtigen Ton.

## Einen Hinweis zu entfernen ist nie ein Aufräumen

Bei einem Aufräumen werden Sie versucht sein, „alte“ Köpfe, Autoren-Tags (`@author`) oder eine Copyright-Zeile zu
entfernen, die eine Firma nennt, die es nicht mehr gibt. Tun Sie es nicht. Ein Hinweis, der über die Gegenwart nicht
mehr zutrifft, trifft über die Vergangenheit immer noch zu, und die Lizenz verlangt ihn. Hinzufügen, nie wegnehmen.

Dasselbe gilt für `@author`-Tags in Docblocks: Behalten Sie sie an dem Code, den diese Person geschrieben hat.

## Ihre Arbeit im Projekt eines anderen

Wenn Sie per Pull Request zu einem anderen Projekt beitragen, ist Ihre Namensnennung Ihr Commit: Ihr Name und Ihre
E-Mail-Adresse in der Git-Historie. Halten Sie sich bei Dateiköpfen an die Regeln des Projekts; die meisten Projekte
möchten nicht, dass Beitragende Copyright-Zeilen in ihre Dateien schreiben, und das ist ihre Entscheidung. Setzen Sie
nie eine Nennung Ihrer selbst in die Oberflächentexte eines Projekts.

## Checkliste

- [ ] Jede vorhandene Copyright-Zeile und jeder Lizenzhinweis ist noch da.
- [ ] Geänderte Dateien tragen eine Zeile, die sagt, wer sie wann geändert hat.
- [ ] Jede mitgelieferte Bibliothek hat ihre eigene Lizenzdatei direkt daneben.
- [ ] `ezinfo.php` hat einen `Includes`-Eintrag je enthaltener Software.
- [ ] `authors` in `composer.json` nennt die ursprünglichen Autoren.
- [ ] Die README hat einen Abschnitt mit Credits.

## Verwandte Seiten

- Zurück: [Lizenzen und wie sie sich kombinieren lassen](lizenzen-und-kompatibilitaet.md)
- Weiter: [Mit Pull Requests zurückgeben](mit-pull-requests-zurueckgeben.md)
- [Ethisches Rebranding](ethisches-rebranding.md)
- [ezinfo.php und extension.xml modernisieren](../../../../eng-us/help/extension-development/part-4-upgrading/modernizing-ezinfo-and-extension-xml.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
