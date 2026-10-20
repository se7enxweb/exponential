---
title: "Demo-Inhalte für ein Theme"
audience: designer
category: theme-distribution
order: 340
summary: Demo-Inhalte, die ein Theme von seiner besten Seite und an seinen Grenzen zeigen - welche Seiten Sie anlegen, realistischer, aber erfundener Text, lizenzierte Bilder, lange Titel und leere Felder - und wie Sie sie als Paket oder Startdaten mitliefern.
keywords: Demo-Inhalte, Beispielinhalte, Lorem ipsum, Testinhalte, Grenzfälle, Inhaltspaket, Startdaten, Demoseiten, demo content, sample content, test content, edge cases, content package, seed data
type: help
---
# Demo-Inhalte für ein Theme

Diese Seite ist für Designer, die eine Demo-Website füllen oder mit einem Theme Beispielinhalte mitliefern. Gute
Demo-Inhalte verkaufen das Theme und finden seine Fehler.

## Was Sie anlegen

| Inhalt | Warum |
|---|---|
| Eine Startseite mit jedem Blocktyp, den das Theme gestaltet | Zeigt die Bandbreite |
| Artikel mit kurzen und sehr langen Titeln, mit und ohne Bilder | Findet Fehler bei Umbruch und Zuschnitt |
| Eine Liste mit mehr Einträgen, als auf eine Seite passen | Zeigt das Blättern |
| Ein Termin, ein Blogbeitrag, ein Formular (wenn das Theme sie gestaltet) | Zeigt die Klassen-Templates |
| Seiten in einer zweiten Sprache und, falls unterstützt, in einer Sprache, die von rechts nach links läuft | Belegt Übersetzungen und Schreibrichtung |
| Leere optionale Felder | Zeigt, dass das Template mit fehlenden Daten zurechtkommt |

## Text und Bilder

- Schreiben Sie realistischen, aber erfundenen Text; kopieren Sie nie die Website eines echten Kunden. Exponential kann
  Platzhaltertext in einer Testsprache erzeugen ([Testsprache und Lorem ipsum](../../../anleitungen/testsprache-lorem-ipsum.md)).
- Verwenden Sie nur Bilder, die Sie weitergeben dürfen ([Creative Commons für Bilder](creative-commons-fuer-bilder.md)).
- Keine echten Personen, Adressen oder E-Mail-Adressen.

## Mitliefern

| Weg | Seite |
|---|---|
| Ein Inhaltsobjekt-Paket (`.ezpkg`) des Demo-Teilbaums | [Der Paketerstellungs-Assistent](../../../../eng-us/help/designers/extensions/package-creator-wizard-step-by-step.md), [Exportieren mit ezpm](../../../../eng-us/help/designers/extensions/exporting-with-ezpm.md) |
| Bilder und Dateien in `share/` mit Installationsschritten | [Der share-Ordner](../../../../eng-us/help/designers/extensions/share-folder-assets.md) |
| Nur auf Ihrer Demo-Website | [Eine Demo-Website betreiben](demo-websites.md) |

## Schritt für Schritt

1. Bauen Sie die Inhalte auf einer Entwicklungsinstallation in einem eigenen Teilbaum auf.
2. Prüfen Sie sie anhand der Tabelle oben.
3. Exportieren Sie den Teilbaum: `php ezpm.php add mytheme_demo ezcontentobject <node id>` nach `create`, dann `export`.
4. Installieren Sie ihn auf einer frischen Testseite ([Ihr Paket auf einer Testseite installieren](../../../../eng-us/help/designers/extensions/installing-your-package-on-a-test-site.md)).

## Seitenpakete: tragen Sie sie bei

Wenn Ihr Theme mit einem vollständigen Seitenpaket kommt (der Art, aus der der Setup-Assistent eine neue Website
installiert), ermutigen 7x und die Exponential Foundation Sie ausdrücklich, es per Pull-Request bei
[se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth)
einzureichen, dem Repository des Paketservers, den der Setup-Assistent nutzt. Dann kann jede neue Installation damit
beginnen. Wie: [Ihr Seitenpaket beitragen](../../seitenpakete/ihr-seitenpaket-beitragen.md).

## Verwandte Seiten

- [Screenshots für ein Theme](screenshots-fuer-ein-theme.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
