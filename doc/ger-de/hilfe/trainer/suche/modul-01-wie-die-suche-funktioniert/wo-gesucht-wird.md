---
title: "Lektion 1.1: Wo in Exponential gesucht wird"
audience: trainer, admin
category: training
order: 11
summary: Die Teilnehmenden nennen jeden Ort, an dem eine Exponential-Installation eine Suche anbietet, wer jeden davon nutzt und welche Regeln entscheiden, was eine Suche zurückgeben darf. 25 Minuten.
keywords: suchkurs, suche, schnellsuche, erweiterte suche, inhaltsfinder, website-suche, hilfesuche, berechtigungen, überblick, search course, search, quick search, advanced search, content finder, site search, help search, permissions, overview
type: help
---
# Lektion 1.1: Wo in Exponential gesucht wird

Bevor jemand eine Suchmaschine abstimmt, braucht er eine Landkarte, wer wo sucht. Eine Exponential-Installation hat
mehrere Suchen, die sich ähneln, aber unterschiedliche Fragen beantworten. Diese Lektion zeichnet diese Landkarte,
damit spätere Lektionen ohne Verwirrung sagen können: „Diese Einstellung ändert jene Suche.“

> **Hinweis**
> Verwenden Sie für die Übung eine Schulungsinstallation, niemals eine Live-Website. Suchen ändert nichts, aber es ist
> eine gute Gewohnheit von der ersten Lektion an.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 1: Wie die Suche funktioniert](README.md) |
| Dauer | 25 Minuten (10 Präsentation, 15 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Support-Mitarbeitende, Entwickler, die sich um die Suche kümmern werden |
| Voraussetzungen | Ein Administratorkonto auf einer Schulungsinstallation; Redakteure 101 oder gleichwertige Erfahrung |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die fünf Orte aufzählen, an denen gesucht wird: die Schnellsuche der Administration, die erweiterte Suche, den
  Inhaltsfinder, die Website-Suche für Besucher und die eigene Suche der Hilfe;
- für jeden sagen, ob er den Suchindex verwendet oder direkt in der Datenbank nachschlägt;
- erklären, warum zwei Benutzer für dieselben Wörter unterschiedliche Ergebnisse erhalten können;
- die zwei Suchmaschinen nennen, die eine Installation verwenden kann: die eingebaute Suchmaschine und Solr über die
  Erweiterung ezfind.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Suchen](../../../suchen.md), [Website-Suche](../../../../../eng-us/help/creators/website-creators/site-search.md) und
[Der Inhaltsfinder](../../../../../eng-us/features/6.0/content-finder.md).

1. **Die Landkarte** (4 Minuten). Zeichnen Sie fünf Kästen an die Tafel:
   - **Schnellsuche**: das Feld **Suchen...** in der Kopfzeile der Administration (admin4). Durchsucht alle Inhalte,
     die der Benutzer lesen darf, über die Suchmaschine.
   - **Erweiterte Suche**: dieselbe Suchmaschine mit mehr Feldern (genauer Ausdruck, Klasse, Klassenattribut, Sektion,
     Veröffentlichungsdatum). Erreichbar über **Suchbereich** oder den Link **Erweiterte Suche** auf der Ergebnisseite.
   - **Inhaltsfinder**: **Setup > Inhaltsfinder**. Keine Volltextsuche: Er schlägt eine ID, eine Remote-ID, einen
     URL-Alias oder einen Dateipfad direkt in der Datenbank nach.
   - **Website-Suche**: das Suchfeld der öffentlichen Website, die View `content/search` des öffentlichen Siteaccess.
   - **Hilfesuche**: das Suchfeld des Hilfe-Centers. Es durchsucht die Hilfeseiten, nicht die Inhalte.
2. **Eine Suchmaschine hinter den meisten** (3 Minuten). Die Schnellsuche, die erweiterte Suche und die Website-Suche
   fragen alle die konfigurierte Suchmaschine (`site.ini [SearchSettings] SearchEngine`). Der mitgelieferte Wert ist
   `eZSearchEngine`, die eingebaute Suchmaschine, die ihren Index in der Datenbank hält. Mit aktiver Erweiterung
   ezfind lautet der Wert `ezsolr`, und stattdessen antwortet ein Solr-Server. Modul 4 behandelt das.
3. **Berechtigungen** (3 Minuten). Die Suchmaschine gibt nur zurück, was der suchende Benutzer lesen darf. Ein
   Redakteur mit einer auf einen Teilbaum beschränkten Rolle findet weniger als ein Administrator; ein anonymer
   Besucher findet nur veröffentlichte, sichtbare Elemente in Sektionen, die anonyme Benutzer lesen dürfen. Das ist
   die häufigste Ursache für Meldungen wie „Die Suche ist kaputt“.

### Häufige Fragen

**Ist der Inhaltsfinder eine bessere Suche?** Er ist ein anderes Werkzeug. Er findet genau ein Ding über seine Nummer,
seinen Pfad oder seine Adresse und zeigt dessen Versionen, Orte und Papierkorbeinträge. Wörter in Texten sucht er
nicht.

**Verwendet die Hilfesuche denselben Index wie die Inhalte?** Nein. Sie durchsucht nur die Hilfeseiten.

**Warum kann ein Besucher nicht im Admin-Siteaccess suchen?** Besucher verwenden den öffentlichen Siteaccess. Die
Admin-Suche braucht ein Konto mit Zugang zur Administration.

### Stolperfallen

- **Nur als Administrator testen.** Ein Administrator sieht alles. Wiederholen Sie eine Suche immer als Benutzer mit
  weniger Rechten, bevor Sie entscheiden, dass Inhalte im Index fehlen.
- **Inhaltsfinder und Suche verwechseln.** Die Teilnehmenden geben ein Wort in den **Inhaltsfinder** ein und erhalten
  keinen Treffer. Zeigen Sie, dass er eine ID, einen Pfad oder eine Adresse erwartet.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation (installiert mit `php bin/php/console exp:install`, siehe
[Installation über die Kommandozeile](../../../../installation/07-installation-ueber-die-konsole.md)), als Administrator in der
Administration angemeldet.

1. Klicken Sie in der Kopfzeile in das Feld **Suchen...**, geben Sie ein Wort ein, das in einem der Beispielartikel
   vorkommt, und drücken Sie die Eingabetaste.
2. Notieren Sie die Überschrift der Ergebnisseite, zum Beispiel `Die Suche nach <Ihr Wort> ergab 3 Treffer`, und die
   Spalten **Name** und **Typ**.
3. Klicken Sie auf der Ergebnisseite auf **Erweiterte Suche**. Notieren Sie die Felder **Nach allen folgenden Worten
   suchen**, **Genau nach einem Ausdruck suchen**, **Klasse**, **Klassen Attribut**, **In** und **Veröffentlicht**.
4. Öffnen Sie im oberen Menü **Setup** und klicken Sie auf **Inhaltsfinder**. Geben Sie die Knotennummer der
   Startseite (auf ihrer Seite als *Knoten* angezeigt; auf einer Standardinstallation ist es `2`) in **ID, Pfad oder
   URL** ein und klicken Sie auf **Finden**.
5. Geben Sie dasselbe Wort wie in Schritt 1 in den **Inhaltsfinder** ein und klicken Sie auf **Finden**. Vergleichen
   Sie: Was antwortet der Finder?
6. Öffnen Sie die öffentliche Website in einem zweiten Browserfenster, abgemeldet. Verwenden Sie ihr Suchfeld, falls
   das Design eines hat, mit demselben Wort. Zählen Sie die Ergebnisse.
7. Öffnen Sie das Hilfe-Center (**Hilfemenü**) und geben Sie `search` in sein Suchfeld ein.
8. Schreiben Sie in Ihre Notizen eine Zeile pro Suche: wo sie ist, wer sie nutzt, was sie durchsucht.

## Erwartetes Ergebnis

- Schritt 2 listet Treffer mit **Name** und **Typ** auf.
- Schritt 4 findet die Startseite und zeigt ihre Versionen und Orte; Schritt 5 findet nichts oder nur einen Alias,
  weil der Finder keinen Text durchsucht.
- Schritt 6 zeigt gleich viele oder weniger Ergebnisse als Schritt 1: Der Besucher darf weniger lesen als der
  Administrator.
- Schritt 7 listet Hilfeseiten auf, keine Inhalte.
- Der Trainer prüft die Notizen von zwei Teilnehmenden anhand der Landkarte an der Tafel.

![Das Suchfeld in der Kopfzeile der Administration](../../../../../eng-us/help/images/trainers/se-where.webp)

## Quiz

1. Welche Einstellung benennt die Suchmaschine, die eine Installation verwendet?
   A. `site.ini [SiteSettings] SearchEngine` B. `site.ini [SearchSettings] SearchEngine` C. `solr.ini [SolrSettings] Url`
   D. `search.ini [Engine] Name`
2. Ein Redakteur und ein Administrator suchen nach demselben Wort und erhalten unterschiedlich viele Ergebnisse.
   Nennen Sie den wahrscheinlichsten Grund.
3. Richtig oder falsch: Der Inhaltsfinder durchsucht Wörter im Text von Artikeln.
4. Welche Suche verwendet ein Besucher der öffentlichen Website?
5. Was ist der mitgelieferte Wert der Einstellung für die Suchmaschine?

<details>
<summary>Antworten</summary>

1. **B.** `site.ini [SearchSettings] SearchEngine` ([Such-Backends](../../../hosting/grundlagen/such-backends.md)).
2. Ihre Rollen unterscheiden sich: Die Suchmaschine gibt nur Inhalte zurück, die der Benutzer lesen darf.
3. **Falsch.** Er schlägt IDs, Remote-IDs, URL-Aliase und Dateipfade nach
   ([Der Inhaltsfinder](../../../../../eng-us/features/6.0/content-finder.md)).
4. Die Website-Suche: die View `content/search` des öffentlichen Siteaccess, über das Suchfeld des Designs.
5. `eZSearchEngine`, die eingebaute Suchmaschine.

</details>

## Weiterführende Informationen

- [Suchen](../../../suchen.md): die Sicht der Redakteure auf die Schnellsuche, den Suchbereich und die erweiterte
  Suche.
- [Website-Suche](../../../../../eng-us/help/creators/website-creators/site-search.md): die Suche der Besucher, für Website-Ersteller.
- [Such-Backends](../../../hosting/grundlagen/such-backends.md): die beiden Suchmaschinen aus Sicht eines Hosters.

## Verwandte Seiten

- [Modul 1: Wie die Suche funktioniert](README.md)
- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
