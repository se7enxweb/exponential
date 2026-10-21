---
title: "Lektion 5.2: Suche und Sprachen"
audience: trainer, admin
category: training
order: 52
summary: Die Teilnehmenden erklären, wie ein übersetztes Objekt indiziert wird und welche Übersetzungen die Suche eines Siteaccess auflistet, testen auf ihrer Schulungsinstallation eine Suche in zwei Sprachen und nennen die Spracheinstellungen von ezfind. 25 Minuten.
keywords: suchkurs, sprachen, übersetzungen, mehrsprachige suche, SiteLanguageList, immer verfügbar, SearchMainLanguageOnly, MultiCore, LanguagesCoresMap, siteaccess, search course, languages, translations, multilingual search, always available
type: help
---
# Lektion 5.2: Suche und Sprachen

Auf einer mehrsprachigen Website gibt es dasselbe Element in mehreren Sprachen, und die Besucher jedes Siteaccess
erwarten Ergebnisse in ihrer Sprache. Diese Lektion zeigt den Teilnehmenden, wie die eingebaute Suchmaschine mit
Übersetzungen umgeht, was die Sprache eines Ergebnisses entscheidet und welche Einstellungen ezfind für Sprachen
hinzufügt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 5: Feinabstimmung und Fehlersuche](README.md) |
| Dauer | 25 Minuten (10 Präsentation, 15 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren mehrsprachiger Websites |
| Voraussetzungen | [Lektion 5.1](die-sucheinstellungen-abstimmen.md); eine Schulungsinstallation mit mindestens zwei Inhaltssprachen |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, dass die eingebaute Suchmaschine die Wörter jeder Übersetzung eines Objekts indiziert;
- erklären, dass ein Siteaccess nur Objekte auflistet, die in seinen Sprachen verfügbar sind
  (`[RegionalSettings] SiteLanguageList[]` und die Markierung *immer verfügbar*), unter ihrem Namen in der ersten
  vorhandenen Sprache;
- eine Suche nach einem Wort, das nur in einer Übersetzung vorkommt, in zwei Siteaccesses testen;
- die `[LanguageSearch]`-Einstellungen von ezfind nennen: `SearchMainLanguageOnly`, `MultiCore` und
  `LanguagesCoresMap[]`.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Sprachen](../../../sprachen.md), [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md), die eingebaute
Suchmaschine (`kernel/search/plugins/ezsearchengine/`) und `ezfind.ini [LanguageSearch]` der Erweiterung.

1. **Indizierung** (3 Minuten). Wenn ein Objekt veröffentlicht wird, indiziert die eingebaute Suchmaschine jedes
   durchsuchbare Attribut in allen seinen Übersetzungen. Ein Wort aus dem deutschen Text und ein Wort aus dem
   englischen Text verweisen beide auf dasselbe Objekt.
2. **Auflistung** (4 Minuten). Eine Suche läuft in einem Siteaccess. Die Ergebnisse werden nach den Sprachen dieses
   Siteaccess gefiltert, mit demselben Filter, den Inhaltslisten verwenden: Ein Objekt wird aufgelistet, wenn es in
   einer der Sprachen von `SiteLanguageList[]` existiert oder als immer verfügbar markiert ist, und sein Name wird aus
   der ersten dieser Sprachen genommen, die es hat. Ein deutsches Wort kann also im englischen Siteaccess ein Objekt
   finden, wenn dieses Objekt auch auf Englisch existiert; es wird dann auf Englisch angezeigt.
3. **ezfind** (3 Minuten). Mit Solr gibt `SearchMainLanguageOnly=enabled` (ausgeliefert) nur Ergebnisse in der ersten
   Sprache von `SiteLanguageList[]` zurück; `disabled` verwendet die Fallback-Reihenfolge und die Markierung immer
   verfügbar. `MultiCore=enabled` mit `LanguagesCoresMap[<locale>]=<core>` indiziert jede Sprache in einem eigenen
   Solr-Core für eine sprachspezifische Analyse; nicht zugeordnete Sprachen werden dann nicht indiziert.

### Häufige Fragen

**Warum bekommt ein Besucher auf der englischen Website ein Ergebnis für ein deutsches Wort?** Das Objekt hat eine
deutsche Übersetzung, die das Wort enthält, und existiert auch auf Englisch (oder ist immer verfügbar), deshalb listet
der englische Siteaccess es auf. So arbeitet die eingebaute Suchmaschine; testen Sie es mit Ihren eigenen Inhalten.

**Kann die eingebaute Suchmaschine nur eine Sprache durchsuchen?** Sie hat dafür keine Einstellung. Eine
sprachspezifische Suche ist einer der Gründe, ezfind mit `SearchMainLanguageOnly` oder mehreren Cores in Betracht zu
ziehen.

**Spielen Akzente eine Rolle?** Nein: Die Suchtransformation entfernt in jeder Sprache diakritische Zeichen und
Groß-/Kleinschreibung (Lektion 1.2).

### Stolperfallen

- **Eine Schulungs-Website mit nur einer Sprache.** Die Übung braucht zwei. Fügen Sie vor dem Kurs eine zweite
  Inhaltssprache hinzu, oder führen Sie die Übung als Vorführung durch.
- **Nur in der Administration testen.** Der Admin-Siteaccess zeigt meist jede Sprache. Testen Sie in den öffentlichen
  Siteaccesses.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation mit zwei Inhaltssprachen und einem öffentlichen Siteaccess für jede
(zum Beispiel einen englischen und einen deutschen; siehe **Setup > Sprachen** und **Setup > Systeminformationen**,
Abschnitt **Siteaccesses**).

1. Geben Sie die Sprachen jedes öffentlichen Siteaccess aus:

   ```bash
   php bin/php/console exp:ini get site.ini/RegionalSettings/SiteLanguageList siteaccess:<first siteaccess>
   php bin/php/console exp:ini get site.ini/RegionalSettings/SiteLanguageList siteaccess:<second siteaccess>
   ```

2. Legen Sie in der ersten Sprache einen Artikel mit dem Wort `blueberry` im Titel an und veröffentlichen Sie ihn.
3. Übersetzen Sie ihn in die zweite Sprache und setzen Sie das Wort `heidelbeere` (oder ein anderes Wort, das nur dort
   vorkommt) in den übersetzten Titel. Veröffentlichen Sie.
4. Suchen Sie im ersten öffentlichen Siteaccess als Besucher nach `blueberry`, dann nach `heidelbeere`.
5. Suchen Sie im zweiten öffentlichen Siteaccess nach beiden Wörtern.
6. Notieren Sie jeweils, in welcher Sprache der Name des Ergebnisses angezeigt wird.
7. Legen Sie einen zweiten Artikel nur in der zweiten Sprache an, mit dem Wort `preiselbeere`. Suchen Sie in beiden
   öffentlichen Siteaccesses danach.

## Erwartetes Ergebnis

- Die Schritte 4 und 5 finden den übersetzten Artikel für beide Wörter in beiden Siteaccesses, angezeigt in der
  Sprache des Siteaccess.
- Schritt 7 findet den zweiten Artikel nur in dem Siteaccess, dessen Sprachen die zweite Sprache enthalten (sofern das
  Objekt nicht immer verfügbar ist).
- Der Trainer bittet eine Person, Schritt 7 mit den Begriffen *SiteLanguageList* und *immer verfügbar* zu erklären.

## Quiz

1. Ein Objekt existiert auf Englisch und Deutsch. Die Wörter welcher Übersetzungen stehen im eingebauten Index?
2. Welche Einstellung entscheidet, welche Sprachen ein Siteaccess zeigt?
   A. `[SearchSettings] SearchLanguage` B. `[RegionalSettings] SiteLanguageList[]` C. `[SolrBase] Shards[]` D. keine
3. Ein Objekt existiert nur auf Deutsch und ist nicht immer verfügbar. Wird es in einem Siteaccess gefunden, dessen
   Sprachen nur Englisch sind?
4. Was bewirkt `SearchMainLanguageOnly=enabled` von ezfind?
5. Was geschieht mit `MultiCore=enabled` mit einer Sprache, die nicht in `LanguagesCoresMap[]` steht?

<details>
<summary>Antworten</summary>

1. Die Wörter aller seiner Übersetzungen.
2. **B.**
3. Nein; der Siteaccess listet nur Objekte auf, die in seinen Sprachen verfügbar sind.
4. Es gibt nur Ergebnisse in der ersten Sprache von `SiteLanguageList[]` zurück (die `ezfind.ini` der Erweiterung).
5. Sie wird nicht indiziert („Unmapped languages won't be indexed“).

</details>

## Weiterführende Informationen

- [Sprachen](../../../sprachen.md): Sprachen für Redakteure und Administratoren.
- [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md): Inhaltssprachen hinzufügen und verwalten.
- [Übersetzungen und Sprachen](../../../../../eng-us/features/6.0/translations-and-languages.md): die Referenz der Funktion.

## Verwandte Seiten

- [Modul 5: Feinabstimmung und Fehlersuche](README.md)
- [Suche: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
