---
title: Gemeinsam genutzte Elemente immer verfügbar machen
audience: government, editor, admin
category: public-sector
order: 70
summary: Aufgabe für Redakteure und Administratoren - Bilder, Medienordner, Benutzerkonten und einsprachige Dokumente als immer verfügbar markieren, damit jede Sprach-Site sie zeigt.
keywords: immer verfügbar, Reiter Übersetzungen, Medien, Benutzer, gemeinsam genutzte Elemente, mehrsprachig, Klassenvorgabe, always available, translations tab, media, users, shared items, multilingual, class default
type: help
---
# Gemeinsam genutzte Elemente immer verfügbar machen

Diese Aufgabe richtet sich an Redakteure und Administratoren einer mehrsprachigen Site. Hintergrund:
[Immer verfügbare Inhalte](../../sprachen/immer-verfuegbare-inhalte.md).

## Bevor Sie beginnen

Sie können die Elemente bearbeiten. Entscheiden Sie, welche Elemente sprachneutral sind (Bilder, Logos, Medienordner,
Benutzer) oder absichtlich nur in einer Sprache existieren (ein Rechtstext).

## Schritte

Für ein Element:

1. Öffnen Sie das Element; öffnen Sie den Reiter **Übersetzungen**.
2. Kreuzen Sie **Die Hauptsprache verwenden, wenn keine vorrangige Übersetzung vorhanden ist** an.
3. Drücken Sie **Aktualisieren**.

Für künftige Elemente einer Klasse (Administratoren):

1. **Setup > Klassen**, die Klasse (zum Beispiel Image), **Bearbeiten**.
2. Setzen Sie die voreingestellte Verfügbarkeit der Objekte auf immer verfügbar; **OK**. Bestehende Objekte behalten
   ihre Einstellung.

## Prüfen

Auf einem Sprach-Siteaccess, dessen `SiteLanguageList[]` die Sprache des Elements nicht enthält, erscheint das Element
jetzt (in seiner Hauptsprache). Auf der Seite einer Sprache unter **Setup > Sprachen** sind immer verfügbare Objekte
markiert.

## Probleme

| Problem | Ursache |
|---|---|
| Ein ganzer Ordner mit Bildern fehlt weiterhin | der Ordner selbst oder die Bilder darunter sind nicht immer verfügbar |
| Eine Textseite erscheint auf einer anderen Sprach-Site unübersetzt | sie wurde versehentlich immer verfügbar gemacht; entfernen Sie das Kreuz |

## Verwandte Seiten

- [Inhaltssprachen, Abschnitt 6](../../../../anleitungen/inhaltssprachen.md#6-hauptsprache-und-immer-verfügbar)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
