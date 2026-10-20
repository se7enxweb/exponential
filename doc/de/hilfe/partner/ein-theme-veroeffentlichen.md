---
title: Ein Theme oder Design als Partner veröffentlichen
audience: partner
category: partnership
order: 430
summary: Wie Kreativ- und IT-Partner ein Theme oder Design für Exponential veröffentlichen - es als Erweiterung paketieren, Designregeln, Barrierefreiheit, Sprachen und Schreibrichtung von rechts nach links, Assets und ihre Lizenzen, Screenshots, Release und Pflege.
keywords: Theme, Design, veröffentlichen, Paket, Erweiterung, Templates, Stylesheets, Barrierefreiheit, von rechts nach links, Dunkelmodus, Assets, Schriften, Bilder, Lizenz, Screenshots, theme, design, publish, package, extension, templates, stylesheets, accessibility, right to left, dark mode, assets, fonts, images, license, screenshots
type: help
---
# Ein Theme oder Design als Partner veröffentlichen

Diese Seite ist für Designer und Frontend-Entwickler in Partnerunternehmen. In Exponential wird ein Theme oder Design
als Erweiterung ausgeliefert, die ein Design-Verzeichnis (Templates, Stylesheets, Bilder) und seine Einstellungen
enthält; daher gelten die Release-Schritte aus [Eine Erweiterung als Partner veröffentlichen](eine-erweiterung-veroeffentlichen.md).
Diese Seite ergänzt, was bei Designs besonders ist.

## Bevor Sie es paketieren

- [ ] Entfernen Sie die Marke des Kunden: Logo, Farben, Schriften, Bilder, Texte.
- [ ] Ersetzen Sie sie durch neutrale Standardwerte, die sich in Einstellungen oder Stylesheets leicht ändern lassen.
- [ ] Holen Sie eine schriftliche Erlaubnis ein, wenn das Design bezahlte Kundenarbeit war.

## Checkliste zur Designqualität

- [ ] Funktioniert mit dem erwarteten Inhaltsmodell und bleibt brauchbar, wenn Felder leer sind
- [ ] Funktioniert mit langen Titeln und anderen Sprachen; Schreibrichtung von rechts nach links getestet, wenn Sie sie
      unterstützen ([Sprachen](../../../en/us/help/languages.md))
- [ ] Tastaturbedienung und sichtbarer Fokus; Textkontrast mindestens 4,5:1
- [ ] Responsiv ab Telefonbreite; kein horizontales Scrollen
- [ ] Funktioniert mit den Blöcken des Layouts-Editors, wenn es Layouts unterstützt ([Der Layouts-Editor](../../../en/us/help/layouts-editor.md))
- [ ] Wenn es die Administration berührt: folgt `doc/en/us/bc/6.0/admin4-styling.md` (kein Inline-CSS, Tokens mit
      Werten für den Dunkelmodus)

## Assets und ihre Lizenzen

Jede Datei in einem Theme hat eine Lizenz. Führen Sie sie in der README auf:

| Asset | Prüfen |
|---|---|
| Schriften | Die Schriftlizenz erlaubt die Weitergabe |
| Icons | Die Lizenz des Icon-Sets erlaubt die Weitergabe |
| Bilder | Eigene oder mit einer Lizenz, die die Weitergabe erlaubt; keine Stockfotos, die Sie nur auf einer Website verwenden dürfen |
| CSS/JS von Dritten | Lizenz kompatibel mit der Lizenz Ihres Themes |

## Screenshots und Demo

- Zwei oder drei Screenshots mit neutralen Demo-Inhalten, hell und dunkel, falls unterstützt.
- Eine Demo-Installation nur, wenn Sie sie aktuell halten können.

## Release und Pflege

- Versionieren und veröffentlichen Sie wie bei jeder Erweiterung ([Erweiterungen, „Release it“](../../../en/us/guides/extensions.md#7-release-it)).
- Testen Sie nach jedem Exponential-Release erneut, besonders bei Änderungen an Templates und Layouts.
- Führen Sie ein Änderungsprotokoll, dem Designer folgen können.

## Im Programm

Ein veröffentlichtes Design ist ein Nachweis für die Profilzertifizierung Design und kann die Grundlage eines Tutorials
oder einer Fallstudie sein ([Zertifizierungen](zertifizierungen.md), [Punkte durch Community-Arbeit sammeln](punkte-durch-community-arbeit.md)).

## Verwandte Seiten

- [Kreativpartner](kreativpartner.md)
- [Partner für Erweiterungen und Themes](partner-fuer-erweiterungen-und-themes.md)
- [Eine Erweiterung als Partner veröffentlichen](eine-erweiterung-veroeffentlichen.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
