---
title: Inhalts-Audits
audience: customer, editor, project-manager
category: customer-success
order: 23
summary: Wie ein Website-Betreiber auf einer Exponential-Website ein jährliches Inhalts-Audit durchführt - den Umfang wählen, ein Inventar aus der Liste der Unterelemente exportieren, die Inhaltsprüfungen und Suchstatistiken nutzen, pro Seite über Behalten, Aktualisieren, Zusammenführen, Archivieren oder Entfernen entscheiden und nachfassen - mit einem Arbeitsblatt.
keywords: Inhalts-Audit, Content-Audit, Inhaltsinventar, veraltete Inhalte, CSV-Export der Unterelemente, Inhaltsprüfungen, kaputte Beziehungen, Archiv, Überprüfung, jährlich, content audit, content inventory, ROT, outdated content, sub-items CSV export, content checks, broken relations, archive, review, yearly
type: help
---
# Inhalts-Audits

Diese Seite ist für den Betreiber und die Chefredaktion, die wissen wollen, was auf der Website steht und ob es noch
stimmt. Websites wachsen; eine jährliche Prüfung hält sie nützlich. Exponential liefert Ihnen das Inventar (die Liste
der Unterelemente mit ihrem CSV-Export), die Problemlisten (die Inhaltsprüfungen) und die Sicht der Besucher
(Suchstatistiken). Die Entscheidungen treffen Sie.

## Inhalt

1. [Umfang](#umfang)
2. [Das Inventar erstellen](#das-inventar-erstellen)
3. [Die Probleme finden](#die-probleme-finden)
4. [Pro Seite entscheiden](#pro-seite-entscheiden)
5. [Nachfassen](#nachfassen)
6. [Arbeitsblatt für die Prüfung](#arbeitsblatt-für-die-prüfung)

## Umfang

Eine vollständige Prüfung einer großen Website dauert Wochen. Prüfen Sie jeweils einen Bereich: einen Ordner, eine
Sektion, eine Marke einer Multisite-Installation oder eine Klasse (alle Veranstaltungen, alle Produkte). Wählen Sie
die Bereiche, die Besucher am meisten nutzen, oder die, die sich am längsten niemand angesehen hat.

## Das Inventar erstellen

In der **Liste der Unterelemente** eines Ordners kann jeder Benutzer Spalten aus einem Verzeichnis von mehr als hundert
wählen, einschließlich jedes Attributs der Klassen in der Liste, und die sichtbaren Spalten als **CSV** exportieren
([Die Liste der Unterelemente](../../../../en/us/features/6.0/subitems-table-options.md)). Der Export ist standardmäßig
eingeschaltet, bis zu 5000 Zeilen (`settings/subitems.ini [SubitemsSettings] CSVExport`, `CSVLimit`).

Nützliche Spalten für eine Prüfung: Name, Klasse, Änderungsdatum, Eigentümer oder letzte bearbeitende Person, Sektion,
Sichtbarkeit, Übersetzungen, Adresse. Öffnen Sie die CSV-Datei in einer Tabellenkalkulation und fügen Sie die Spalten
des [Arbeitsblatts für die Prüfung](#arbeitsblatt-für-die-prüfung) hinzu.

> **Tipp**
> Wenn Sie die Website mit einem Inhaltsinventar geplant haben, beginnen Sie damit
> ([Sitemap und Inhaltsinventar](../../../../en/us/help/creators/website-creators/site-map-and-content-inventory.md)); die Prüfung
> vergleicht dann den Plan mit dem, was gewachsen ist.

## Die Probleme finden

| Werkzeug | Findet | Quelle |
|---|---|---|
| Kaputte Beziehungen | sichtbare Inhalte, die auf etwas Entferntes, Verborgenes, im Papierkorb Liegendes oder nie Veröffentlichtes verweisen | [Die Inhaltsprüfungen](../../../../en/us/features/6.0/content-checks.md) |
| Nicht referenzierte Objekte | Bilder und Dateien, die nichts verwendet | ebenda |
| URL-Verwaltung, ungültig | kaputte ausgehende Links | [URL-Aliase, Wildcards und Suchstatistiken](../../../anleitungen/urls-und-aliase.md) |
| Suchstatistiken | wonach Besucher suchen und was sie nicht finden | [Website-Suche und wonach Besucher suchen](website-suche-und-wonach-besucher-suchen.md) |
| Setup > Sprachen | wie viele Elemente eine Übersetzung in jeder Sprache haben | [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md) |
| *Meine Entwürfe* jedes Redakteurs | vergessene Arbeit | [Entwürfe, Versionen und Rückgängigmachen](entwuerfe-versionen-und-rueckgaengig.md) |

Die Inhaltsprüfungen finden Sie im Menü Setup und im Kontextmenü des Baums (**Fortgeschritten**); auf einer neuen
Installation hat nur Administrator Zugriff darauf, planen Sie die Prüfung daher mit Ihrem Administrator.

## Pro Seite entscheiden

| Entscheidung | Wann | Wie in Exponential |
|---|---|---|
| Behalten | richtig, genutzt, aktuell | nichts |
| Aktualisieren | richtiges Thema, veraltete Fakten | bearbeiten; eine neue Version wird aufbewahrt ([Entwürfe, Versionen und Rückgängigmachen](entwuerfe-versionen-und-rueckgaengig.md)) |
| Zusammenführen | zwei Seiten sagen dasselbe | die bessere behalten, die Adresse der anderen als Alias hinzufügen, die andere entfernen ([URLs, Weiterleitungen und verschobene Seiten](urls-weiterleitungen-und-verschobene-seiten.md)) |
| Archivieren | nicht mehr aktuell, aber aufbewahrenswert | verbergen oder in einen Archivbereich verschieben ([Archivieren und der Papierkorb](archivieren-und-der-papierkorb.md)) |
| Entfernen | falsch, doppelt oder wertlos | in den Papierkorb entfernen; entscheiden, wohin ihre Besucher geleitet werden |

Fragen Sie vor der Entscheidung die zuständige Person der Seite, und halten Sie fest, wer entschieden hat.

## Nachfassen

1. Tragen Sie die Aktualisierungsarbeit mit Verantwortlichen und Terminen in den [Redaktionskalender](redaktionskalender.md) ein.
2. Führen Sie große Verschiebungen oder Entfernungen als Inhaltsaufträge aus, wenn der Teilbaum groß ist: Ab 50 Knoten
   bietet die Bestätigung an, sie im Hintergrund auszuführen ([Inhaltsaufträge](../../../../en/us/features/6.0/content-jobs.md)).
3. Lassen Sie die Prüfung auf kaputte Beziehungen nach den Entfernungen erneut laufen.
4. Lassen Sie entfernte Seiten eine Weile im Papierkorb, bevor Sie ihn leeren.
5. Notieren Sie, was Sie nächstes Jahr anders machen würden.

## Arbeitsblatt für die Prüfung

**Vorlage.** Spalten, die neben dem CSV-Export hinzuzufügen sind:

| Adresse | Name | Klasse | Zuletzt geändert | Zuständig | Noch korrekt? | Genutzt? (Aufrufe, Suche, Links) | Entscheidung | Weiterleiten auf | Erledigt |
|---|---|---|---|---|---|---|---|---|---|
| | | | | | ja / nein | | behalten / aktualisieren / zusammenführen / archivieren / entfernen | | |
| | | | | | | | | | |

```text
AUDIT: area ______________________   Dates: __________ to __________
Pages reviewed: ____   keep ____ update ____ merge ____ archive ____ remove ____
Broken relations fixed: ____   Unused files trashed: ____
Signed off by: ______________
```

## Verwandte Seiten

- [Archivieren und der Papierkorb](archivieren-und-der-papierkorb.md)
- [Pflege der Medienbibliothek](pflege-der-medienbibliothek.md)
- [Kaputte Links und Inhaltsprüfungen](../../../../en/us/help/creators/website-creators/link-and-content-checks.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
