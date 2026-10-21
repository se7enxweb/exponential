---
title: Eine Seite in eine andere Sprache übersetzen
audience: government, editor
category: public-sector
order: 260
summary: Für Redakteure und Übersetzer - in Exponential mit Neue Übersetzung und Übersetzung basiert auf eine Übersetzung einer Seite hinzufügen, mit dem Original als Referenz übersetzen, veröffentlichen und die Übersetzungen nach Änderungen auf demselben Stand halten.
keywords: übersetzen, Übersetzung, Neue Übersetzung, Übersetzung basiert auf, Sprache, mehrsprachig, Reiter Übersetzungen, immer verfügbar, Hauptsprache, Übersetzerrolle, translate, translation, New translation, Translate based on, language, multilingual, Translations tab, always available, main language, translator role
type: help
---
# Eine Seite in eine andere Sprache übersetzen

Diese Seite ist für Redakteure und Übersetzer auf mehrsprachigen öffentlichen Sites. In Exponential enthält ein Objekt
alle seine Übersetzungen; jeder Sprach-Siteaccess zeigt die Sprache des Besuchers
([Inhaltssprachen, 7](../../../../anleitungen/inhaltssprachen.md#7-inhalte-im-editor-übersetzen)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Übersetzungen auf demselben Stand halten](#übersetzungen-auf-demselben-stand-halten)
4. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Die Sprache ist der Site hinzugefügt (die Seite der Inhaltssprachen, `/content/translations`).
- Ihre Rolle hat `content/edit` (oder `content/translate`) und `content/create` für diese Sprache; Übersetzerrollen
  sind oft durch **Language** eingeschränkt.
- Der Ausgangstext ist endgültig und veröffentlicht.

## Schritte

1. Öffnen Sie die Seite und klicken Sie auf **Bearbeiten**. Wenn das Objekt in mehr als einer Sprache bearbeitet werden
   kann, öffnet sich die Seite *<Name> bearbeiten*.
2. Wählen Sie unter **Neue Übersetzung** die Sprache. Wählen Sie unter **Übersetzung basiert auf** die Sprache, von der
   aus Sie beginnen (ihre Felder werden kopiert), oder **Keine**.
3. Klicken Sie auf **OK**. Das Formular zeigt die neue Sprache, mit dem Original als Referenz.
4. Übersetzen Sie jedes Textfeld; nicht übersetzbare Felder (Daten, Relationen, Schlagwörter) sind gemeinsam und werden
   nicht in jeder Sprache zur Bearbeitung angezeigt.
5. Übersetzen Sie die Alternativtexte der Bilder in deren eigenen Bild-Objekten, wenn diese übersetzt werden.
6. **Zur Veröffentlichung schicken**.

## Übersetzungen auf demselben Stand halten

| Wenn sich das Original ändert | Tun |
|---|---|
| Eine Tatsache ändert sich (Datum, Adresse, Betrag) | aktualisieren Sie jede Übersetzung am selben Tag |
| Der Text wird neu geschrieben | aktualisieren Sie die Übersetzungen oder kennzeichnen Sie sie in Ihrem Ablauf als älter |
| Eine Übersetzung wird nicht mehr gepflegt | markieren Sie sie auf dem Reiter **Übersetzungen** und wählen Sie **Ausgewählte entfernen** |

Der Reiter **Übersetzungen** listet die Sprachen des Objekts mit markierter Hauptsprache; **Als Haupt setzen** ändert
die Hauptsprache; **Aktualisieren** speichert die Einstellung „immer verfügbar“ („Die Hauptsprache verwenden, wenn keine
vorrangige Übersetzung vorhanden ist“).

## Das Ergebnis prüfen

- [ ] Öffnen Sie die Seite abgemeldet im Sprach-Siteaccess: Die Übersetzung erscheint.
- [ ] Der Sprachumschalter, falls Ihre Site einen hat, führt zwischen den Fassungen hin und her.
- [ ] Seitentitel und Beschreibung (Metadaten) sind übersetzt.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Maschinelle Übersetzung ohne Prüfung veröffentlicht | Prüfung durch jemanden, der Sprache und Thema kennt |
| Die falsche Sprache bearbeiten | prüfen Sie die Sprache in der Titelzeile des Bearbeitungsformulars |
| Seiten fehlen in einer Sprache, sind aber aus übersetzten Menüs verlinkt | entscheiden Sie, ob „immer verfügbar“ die Hauptsprache zeigt ([Inhaltssprachen, 6](../../../../anleitungen/inhaltssprachen.md#6-hauptsprache-und-immer-verfügbar)) |
| Text von rechts nach links in einer Seite von links nach rechts | der Siteaccess setzt die Schreibrichtung; kennzeichnen Sie gemischten Text im Editor ([Sprachen mit Schreibrichtung von rechts nach links](../../../../funktionen/6.0/rechts-nach-links.md)) |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Sprachen](../../../sprachen.md)
- [Mehrsprachige Sites](../../../ersteller/website-ersteller/mehrsprachige-websites.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
