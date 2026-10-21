---
title: "Lektion 4.4: Relevanz, Elevation und Facetten"
audience: trainer, admin, developer
category: training
order: 44
summary: Die Teilnehmenden stimmen ab, was in Solr-Ergebnissen zuerst kommt, mit Gewichtungen beim Indizieren, Gewichtungen bei der Abfrage und der Elevation ausgewählter Objekte, schließen Klassen vom Index aus und sehen, was Hervorhebung, Rechtschreibprüfung und Facetten bieten. 35 Minuten.
keywords: suchkurs, ezfind, relevanz, gewichtung, IndexBoost, QueryBoost, elevation, Elevate, Synchronise, IndexExclude, hervorhebung, rechtschreibprüfung, facetten, SortBy, search course, relevance, boost, highlighting, spell check, facets
type: help
---
# Lektion 4.4: Relevanz, Elevation und Facetten

Der Hauptgrund, Solr zu betreiben, sind bessere Ergebnisse: der beste Treffer zuerst, die Seite, die eine Organisation
gefunden sehen will, ganz oben, und Filter, mit denen Besucher eine lange Liste eingrenzen. Diese Lektion zeigt die
drei Hebel, die ezfind Administratoren gibt, Gewichtung, Elevation und Ausschluss, und stellt die Funktionen vor, auf
denen Entwickler aufbauen: Hervorhebung, Rechtschreibprüfung und Facetten.

> **Hinweis**
> Jede Änderung in dieser Lektion wird auf Ihrer eigenen Schulungsinstallation vorgenommen. Änderungen an Gewichtungen
> brauchen eine Neuindizierung; planen Sie das auf einer Live-Website.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 4: Solr und ezfind](README.md) |
| Dauer | 35 Minuten (15 Präsentation, 20 Übung) |
| Niveau | Experten |
| Zielgruppe | Website-Administratoren, Entwickler, Inhaltsverantwortliche |
| Voraussetzungen | [Lektion 4.3](indizierung-mit-solr.md); ein aufgebauter Solr-Index; die ezfind-Tabelle angelegt (Lektion 4.1) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Gewichtung beim Indizieren für eine Klasse oder ein Attribut in `ezfind.ini [IndexBoost]` setzen und neu
  indizieren;
- in der Administration ein Objekt für eine Suchanfrage hervorheben (Elevation) und die Konfiguration mit Solr
  synchronisieren;
- eine Klasse mit `[IndexExclude] ClassIdentifierList[]` vom Index ausschließen;
- sagen, was `[HighLighting]`, `[SpellCheck]` und Facetten bieten und wo ein Entwickler mehr dazu liest.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: die `settings/ezfind.ini` der Erweiterung, ihr admin4-Template `ezfind/elevate.tpl`,
[Suche mit ezfind und Solr](../../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md#what-changes-for-templates)
und [Die Reihenfolge der Suchergebnisse in den Dialogen des Editors](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#die-reihenfolge-der-suchergebnisse-in-den-dialogen-des-editors).

1. **Gewichtung beim Indizieren** (4 Minuten). `[IndexBoost]`: `Class[<class identifier>]` gewichtet ganze Objekte
   einer Klasse, `Attribute[<attribute>]` oder `Attribute[<class>/<attribute>]` gewichtet ein Feld,
   `Datatype[<datatype>]` gewichtet jedes Feld eines Datentyps, `ReverseRelatedScale` erhöht die Gewichtung für
   Objekte, auf die viele andere verweisen. Die Einstellungen merken an, dass passende Gewichtungen addiert werden,
   während Klassen- und Attributgewichtungen sich in Solr multiplizieren. Änderungen gelten, wenn Objekte erneut
   indiziert werden.
2. **Gewichtung bei der Abfrage** (2 Minuten). `[QueryBoost] RawBoostQueries[]` nimmt rohe Solr-Gewichtungsabfragen
   an, zum Beispiel das auskommentierte `meta_class_identifier_ms:folder^10`. Eine Experteneinstellung: Testen Sie sie
   zuerst auf der Schulungs-Website.
3. **Elevation** (4 Minuten). Mit aktivem ezfind bekommt die Administration einen Reiter im oberen Menü, benannt durch
   die `menu.ini` der Erweiterung (*eZ Find*), mit dem Eintrag **Elevation** im linken Menü (`ezfind/elevate`,
   Richtlinie `ezfind/elevate`). „Ein Objekt hervorheben“ (Elevate) bedeutet für eine Suchanfrage, dass das Objekt
   unter den allerersten Ergebnissen zurückgegeben wird, wenn nach dieser Anfrage gesucht wird. Zeigen Sie **Elevate
   object** (das Objekt suchen), **Search query**, **Language**, **Fuzzy match**, **Elevate**, die Liste **Existing
   configurations** mit **Filter** und **Synchronise**, das die Konfiguration mit Solr synchronisiert.
4. **Ausschluss und Funktionen** (5 Minuten). `[IndexExclude] ClassIdentifierList[]` hält ganze Klassen aus dem Index
   heraus. `[HighLighting] Enabled=true` markiert die Suchwörter in den Ergebnissen (`SimplePre`, `SimplePost`,
   `FragmentSize`). `[SpellCheck] SpellCheck=enabled` bietet Schreibvorschläge an. Facetten lassen Besucher nach
   Klasse, Tag oder Datum filtern; sie sind Template-Arbeit mit den Fetch-Funktionen von ezfind, und die eigene
   Dokumentation der Erweiterung nennt sie für die installierte Version. Die Sortierung der Suchdialoge des Editors
   wird mit `ezfind.ini [SearchSettings] SortBy[]` festgelegt.

### Häufige Fragen

**Gewichtung oder Elevation?** Eine Gewichtung ändert das Gewicht vieler Objekte auf einmal und braucht eine
Neuindizierung. Die Elevation heftet ein Objekt für eine Anfrage an und wirkt nach **Elevate** (und bei Bedarf
**Synchronise**).

**Können Redakteure Elevationen anlegen?** Wer die Richtlinie `ezfind/elevate` hat. Geben Sie sie der
Inhaltsverantwortlichen, nicht jedem Redakteur.

**Warum ist meine Gewichtung nicht sichtbar?** Gewichtungen beim Indizieren gelten nur für Objekte, die nach der
Änderung indiziert werden. Indizieren Sie neu.

### Stolperfallen

- **Übergewichtung.** Eine Gewichtung von 10 auf eine Klasse schiebt jedes ihrer Objekte über alles andere. Beginnen
  Sie mit kleinen Werten wie den auskommentierten Beispielen (`2.0`, `3.0`) und vergleichen Sie.
- **In der falschen Sprache hervorheben.** Eine Elevation wird mit einer Sprache gespeichert; prüfen Sie
  **Language**, bevor Sie auf **Elevate** klicken.
- **Keine Tabelle.** Ohne `ezfind_elevate_configuration` lässt sich keine Elevation speichern. Führen Sie die
  Schemadatei aus Lektion 4.1 aus.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation aus Lektion 4.3, mit laufendem Solr. Fügen Sie `--allow-root-user` nur hinzu,
wenn Sie als root arbeiten.

1. Suchen Sie in der Administration nach einem Wort, das einen Ordner und mehrere Artikel findet. Notieren Sie die
   Reihenfolge der ersten fünf Ergebnisse.
2. Gewichten Sie Artikel beim Indizieren höher und indizieren Sie neu:

   ```bash
   php bin/php/console exp:ini set "ezfind.ini/IndexBoost/Class[article]" 2.0 global
   php bin/php/ezcache.php --clear-tag=ini
   php extension/ezfind/bin/php/updatesearchindexsolr.php -s <admin siteaccess>
   ```

3. Suchen Sie erneut und vergleichen Sie die Reihenfolge mit Schritt 1.
4. Öffnen Sie den Reiter der Erweiterung im oberen Menü und wählen Sie im linken Menü **Elevation**.
5. Klicken Sie auf **Elevate object**, suchen Sie den Ordner aus Schritt 1 und wählen Sie ihn aus. Geben Sie das
   Suchwort in **Search query** ein, wählen Sie die Sprache und klicken Sie auf **Elevate**.
6. Suchen Sie erneut nach dem Wort. Wo steht der Ordner jetzt?
7. Verwenden Sie in **Existing configurations** **Filter** mit dem Wort, um Ihre Elevation zu finden. Lesen Sie ihre
   **Details**.
8. Machen Sie Ihre Änderungen rückgängig: Verwenden Sie in den Details der Elevation die Entfernen-Aktion unter
   **Actions** und klicken Sie auf der Bestätigungsseite auf **Remove**; entfernen Sie dann die Gewichtung und
   indizieren Sie neu:

   ```bash
   php bin/php/console exp:ini rem "ezfind.ini/IndexBoost/Class[article]" global
   php bin/php/ezcache.php --clear-tag=ini
   php extension/ezfind/bin/php/updatesearchindexsolr.php -s <admin siteaccess>
   ```

## Erwartetes Ergebnis

- Schritt 3 zeigt Artikel weiter oben als in Schritt 1.
- Schritt 5 zeigt *Successful creation of the following Elevate configuration*.
- Schritt 6 listet den Ordner unter den allerersten Ergebnissen auf.
- Schritt 7 listet die Elevation unter *Objects elevated by* der Anfrage auf.
- Schritt 8 zeigt *The Elevate configuration was removed.*; nach der Neuindizierung ist die Reihenfolge wieder wie in
  Schritt 1.
- Der Trainer prüft die Schritte 3 und 6 am Beamer mit der Installation einer Person.

## Quiz

1. Sie setzen `Attribute[title]=1.5`. Wann ändert das die Ergebnisse?
   A. sofort B. nachdem die betroffenen Objekte erneut indiziert wurden C. nach dem Leeren des Template-Caches D. nie
2. Was bewirkt es, ein Objekt für eine Anfrage hervorzuheben?
3. Welche Einstellung hält eine ganze Klasse aus dem Solr-Index heraus?
4. Welche Richtlinie ist nötig, um Elevationen zu verwalten?
5. Nennen Sie zwei Funktionen von ezfind, die ändern, wie Ergebnisse angezeigt werden, und jeweils ihren
   Einstellungsblock.

<details>
<summary>Antworten</summary>

1. **B.** Gewichtungen beim Indizieren gelten, wenn Objekte indiziert werden (die `ezfind.ini` der Erweiterung).
2. Das Objekt wird unter den allerersten Ergebnissen zurückgegeben, wann immer nach dieser Anfrage gesucht wird.
3. `ezfind.ini [IndexExclude] ClassIdentifierList[]`.
4. `ezfind/elevate`.
5. Zwei beliebige aus: Hervorhebung (`[HighLighting]`), Rechtschreibprüfung (`[SpellCheck]`), Autovervollständigung
   (`[AutoCompleteSettings]`), Facetten (Template-Arbeit mit den Fetch-Funktionen der Erweiterung).

</details>

## Weiterführende Informationen

- [Suche mit ezfind und Solr](../../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md): was Solr hinzufügt
  und was sich für Templates ändert.
- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#die-reihenfolge-der-suchergebnisse-in-den-dialogen-des-editors):
  die Suchdialoge des Editors mit `SortBy[]` sortieren.
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md): einer Rolle die
  Richtlinie für die Elevation geben.

## Verwandte Seiten

- [Modul 4: Solr und ezfind](README.md)
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
