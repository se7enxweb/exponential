---
title: Durchsuchbare Felder und Indizierung
audience: government, website-creator, admin
category: public-sector
order: 30
summary: Welche Felder der Inhaltsklassen des öffentlichen Sektors als durchsuchbar zu markieren sind, wie Indizierung beim Veröffentlichen und verzögerte Indizierung funktionieren, wie man den Index neu aufbaut, und die Fallstricke (interne Codes, versteckte Inhalte, große Importe).
keywords: durchsuchbares Attribut, Indizierung, Suchindex, Inhaltsklassen, Relevanz, Suchqualität, searchable attribute, indexing, search index, updatesearchindex, DelayedIndexing, indexcontent, content classes, relevance, search quality
type: help
---
# Durchsuchbare Felder und Indizierung

Diese Seite richtet sich an Site-Ersteller und Administratoren. Was Besucher finden können, hängt davon ab, welche
Attribute im Klasseneditor als **Durchsuchbar** markiert sind und ob der Index aktuell ist
([Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md#attribute-settings)).

## Inhalt

1. [Was als durchsuchbar zu markieren ist](#was-als-durchsuchbar-zu-markieren-ist)
2. [Wann Inhalte indiziert werden](#wann-inhalte-indiziert-werden)
3. [Den Index neu aufbauen](#den-index-neu-aufbauen)
4. [Fallstricke](#fallstricke)

## Was als durchsuchbar zu markieren ist

| Klasse | Durchsuchbar | Nicht durchsuchbar |
|---|---|---|
| Leistungsseite | Titel, Zusammenfassung, Text, Schlüsselwörter | interner Leistungscode, verantwortliche Stelle |
| Nachricht | Titel, Einleitung, Text | Veröffentlichungskennzeichen |
| Dokument (Datei) | Name, Beschreibung; der Text der Datei, wenn die Extraktion eingerichtet ist | Dateigröße |
| Beschluss, Sitzung | Titel, Text, Aktenzeichen | interne Notizen |
| Datensatz | Titel, Beschreibung, Schlüsselwörter | Lizenzcode |

Weniger, aussagekräftige Felder zu markieren, verbessert die Ergebnisse; interne Codes erzeugen Rauschen.

## Wann Inhalte indiziert werden

- Voreingestellt beim Veröffentlichen.
- Mit `site.ini [SearchSettings] DelayedIndexing=enabled` (oder `classbased`) durch den Cronjob-Teil
  `indexcontent.php`, was das Veröffentlichen auf großen Sites beschleunigt; die Ergebnisse hinken hinterher, bis der
  Cronjob läuft.

## Den Index neu aufbauen

Nach einer Änderung, welche Felder durchsuchbar sind, nach Importen oder nach einem Umzug der Datenbank:

```bash
php bin/php/updatesearchindex.php --clean
```

Führen Sie es auf einer großen Site zu einer ruhigen Zeit aus; mit Solr füllt derselbe Befehl Solr.

## Fallstricke

- Ein Feld, das erst als durchsuchbar markiert wird, nachdem Inhalte existieren, wird für diese Inhalte erst nach einem
  Neuaufbau oder einer erneuten Veröffentlichung indiziert.
- Versteckte Inhalte: Die eingebaute Suchmaschine prüft die Sichtbarkeit beim Suchen; Solr hält die Sichtbarkeit in
  seinem Index, und der Cronjob `hide.php` benachrichtigt die Suchmaschine, wenn er Inhalte versteckt
  ([Cronjobs](../../../../anleitungen/cronjobs.md)).
- Wörter in mehr als `StopWordThresholdPercent` (60 %) der Objekte werden ignoriert; ein Wort in jedem Seitentitel
  (der Name der Behörde) hilft beim Suchen nicht.

## Verwandte Seiten

- [Eingebaute Suche für öffentliche Sites](eingebaute-suche-fuer-oeffentliche-sites.md)
- [Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
