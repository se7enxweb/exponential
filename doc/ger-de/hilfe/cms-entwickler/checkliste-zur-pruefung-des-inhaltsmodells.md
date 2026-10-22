---
title: Checkliste zur Prüfung des Inhaltsmodells
audience: cms-developer
category: cms-development
order: 47
summary: Für CMS-Entwickler und ihre Prüfer eine Checkliste, um ein Inhaltsmodell vor dem Bau und noch einmal vor dem Start zu prüfen. Sie behandelt Klassen, Bezeichner, Datentypen, Relationen, den Baum, den Zugriff, Sprachen, den redaktionellen Ablauf, Templates und Übertragbarkeit, mit der Frage hinter jedem Punkt.
keywords: Prüfung des Inhaltsmodells, Prüfcheckliste, Klassen, Bezeichner, Datentypen, Relationen, Inhaltsbaum, Sektionen, Rollen, Übersetzungen, Workflows, Ansichtsmodi, Pakete, Remote-ID, Modellqualität, content model review, review checklist, identifiers, datatypes, content tree, view modes, packages, remote id, model quality
type: help
---
# Checkliste zur Prüfung des Inhaltsmodells

Diese Seite richtet sich an Entwickler, die ein Inhaltsmodell prüfen, ihr eigenes oder das eines Kollegen. Ein Modell
lässt sich auf dem Papier billig ändern und teuer, sobald Inhalte existieren; prüfen Sie es also zweimal: vor dem Bau,
mit einem Redakteur und einem Designer im Raum, und vor dem Start. Jeder Punkt hat die Frage, die Sie stellen; die
verlinkten Seiten erklären die Antworten.

## Inhalt

1. [Wie Sie die Prüfung durchführen](#wie-sie-die-prüfung-durchführen)
2. [Klassen](#klassen)
3. [Bezeichner und Namen](#bezeichner-und-namen)
4. [Datentypen](#datentypen)
5. [Relationen und Wiederverwendung](#relationen-und-wiederverwendung)
6. [Der Baum](#der-baum)
7. [Zugriff](#zugriff)
8. [Sprachen](#sprachen)
9. [Redaktioneller Ablauf](#redaktioneller-ablauf)
10. [Darstellung](#darstellung)
11. [Übertragbarkeit](#übertragbarkeit)
12. [Das Ergebnis festhalten](#das-ergebnis-festhalten)

## Wie Sie die Prüfung durchführen

1. Bringen Sie das Modell so mit, wie es aufgeschrieben ist
   ([Ein Inhaltsmodell entwerfen, Schritt 8](ein-inhaltsmodell-entwerfen.md#schritt-8-aufschreiben-und-prüfen)): eine
   Tabelle je Klasse, eine Skizze des Baums, die Rollen.
2. Bringen Sie echte Inhalte mit: drei echte Elemente je Klasse, kein Lorem ipsum.
3. Bitten Sie einen Redakteur, jedes Element mit den vorgegebenen Feldern auf dem Papier "anzulegen", und einen
   Designer, zu zeigen, wo jedes Feld auf der Seite erscheint.
4. Gehen Sie die Listen unten durch; jedes "Nein" wird entweder behoben oder als akzeptiertes Risiko notiert.

## Klassen

- [ ] Steht jede Klasse für eine echte Art von Inhalt? (Keine Klasse, deren Feld "Typ" alles ändert.)
- [ ] Sind zwei Klassen, die alle Felder teilen, wirklich verschieden? (Unterscheidet sich nur ihr Ort, legen Sie sie
      zusammen.)
- [ ] Ist jeder Behälter ein Behälter und jedes Blatt keiner?
- [ ] Ist das Muster für den Objektnamen aussagekräftig, sodass Namen in Listen und URLs erkennbar sind?
  ([Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md))

## Bezeichner und Namen

- [ ] Sind Bezeichner kleingeschrieben, englisch und für dieselbe Bedeutung in jeder Klasse gleich (`title`, `intro`,
      `image`)?
- [ ] Haben Projektklassen ein Präfix, wo sie mit den Klassen eines Seitenpakets kollidieren könnten?
- [ ] Sind die Anzeigenamen und Beschreibungen für Redakteure geschrieben und übersetzt, wo Redakteure es brauchen?

## Datentypen

- [ ] Verwendet jedes Feld den Datentyp, der zu seiner Bedeutung passt (Datumsangaben als Datum, Auswahlen als
      Auswahllisten oder Tags)?
- [ ] Ist langer Text Rich Text und kurzer Text reiner Text?
- [ ] Sind erforderlich, durchsuchbar und übersetzbar bei jedem Feld bewusst gesetzt?
- [ ] Stammt jeder Erweiterungs-Datentyp aus einer Erweiterung, die auf allen Siteaccesses aktiv ist, die ihn brauchen?
  ([Datentypen wählen](datentypen-waehlen.md))

## Relationen und Wiederverwendung

- [ ] Ist alles, was an mehr als einer Stelle verwendet wird, ein eigenes Objekt, auf das eine Relation verweist?
- [ ] Ist jeder Verweis nur auf einer Seite gespeichert, wobei die Gegenrichtung über einen Fetch gefunden wird?
- [ ] Sind Relationsattribute auf die Klassen eingeschränkt, auf die sie zeigen dürfen, mit einem sinnvollen
      Standardort?
- [ ] Liegen gemeinsam genutzte Bilder in der Medienbibliothek statt kopiert in vielen Feldern?
  ([Relationen und Einbettungen](relationen-und-einbettungen.md), [Bilder und Bildvarianten](bilder-und-bildvarianten.md))

## Der Baum

- [ ] Spiegelt der Baum unter der Wurzel die Navigation wider, die Redakteure und Besucher erwarten?
- [ ] Ist die Sortierung jedes Behälters festgelegt (Priorität für eine Reihenfolge von Hand, Datum für News)?
- [ ] Bleiben große Sammlungen bei der in drei Jahren erwarteten Menge benutzbar (Blättern, Datumsangaben)?
- [ ] Werden zweite Platzierungen nur dort verwendet, wo Inhalte wirklich an zwei Stellen hingehören?
  ([Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md))

## Zugriff

- [ ] Ist jede Regel "nur X darf Y sehen oder bearbeiten" als Sektion oder Teilbaum plus Rolle ausgedrückt?
- [ ] Ist die Rolle Anonymous noch minimal?
- [ ] Sind Stufen jenseits von Entwurf und veröffentlicht als Objektzustände modelliert, nicht als Ordner?
  ([Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md))

## Sprachen

- [ ] Sind die Sprachen und die Reihenfolge der Ausweichsprachen je Siteaccess entschieden?
- [ ] Sind Felder, die sich je Sprache nicht unterscheiden dürfen, nicht übersetzbar?
- [ ] Sind gemeinsam genutzte Objekte (Ordner, Medien, Personen) immer verfügbar?
  ([Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md))

## Redaktioneller Ablauf

- [ ] Ist klar, wer jede Klasse anlegt, freigibt und veröffentlicht, und ist das ein Workflow oder eine Gewohnheit?
- [ ] Sind datiertes Verbergen und Entfernen dort konfiguriert, wo Inhalte ablaufen?
- [ ] Sind die Versionsgrenzen für sehr häufig bearbeitete Klassen richtig?
  ([Workflows und Auslöser](workflows-und-ausloeser.md), [Versionen, Entwürfe und Veröffentlichen](versionen-entwuerfe-und-veroeffentlichen.md))

## Darstellung

- [ ] Sind für jede Klasse ihre Ansichtsmodi aufgeführt (full, line, eine Karte, embed)?
- [ ] Sind Seiten, die Redakteure aus Blöcken zusammenstellen, benannt, und verwenden sie Layouts statt vieler
      optionaler Felder?
- [ ] Sind Bildvarianten für jede Größe festgelegt, die das Design zeigt?
  ([Ansichtsmodi](ansichtsmodi.md), [Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md))

## Übertragbarkeit

- [ ] Liegen die Klassen in Paketen im Repository?
- [ ] Verweisen Code und Einstellungen auf Bezeichner und Remote-IDs, nicht auf Knoten- oder Objektnummern?
- [ ] Tragen importierte Objekte Remote-IDs aus ihrer Quelle?
  ([Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md#klassen-unter-versionsverwaltung))

## Das Ergebnis festhalten

Schreiben Sie Datum, Prüfer, die vereinbarten Änderungen und die akzeptierten Risiken in das Modelldokument in Ihrem
Repository. Prüfen Sie vor dem Start erneut mit der [Checkliste für den Projektstart](checkliste-fuer-den-projektstart.md) und
immer dann, wenn nach dem Start eine neue Klasse hinzukommt.

## Verwandte Seiten

- [Das Inhaltsmodell](das-inhaltsmodell.md)
- [Inhalte auf Exponential abbilden](../../installation/migration/inhaltsmodell-zuordnen.md)
- [Inhaltsklassen für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/inhaltsklassen-fuer-builder.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
