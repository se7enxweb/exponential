---
title: Das Offenlegungsprotokoll veröffentlichen
audience: government, website-creator, editor
category: public-sector
order: 260
summary: Aufgabe für Site-Ersteller und Redakteure - das öffentliche Offenlegungsprotokoll einrichten (eine Klasse für Einträge ohne Daten der Antragsteller, ein Ordner je Jahr, eine Listenansicht, ein Feed) und den ersten Eintrag veröffentlichen.
keywords: Offenlegungsprotokoll, Informationsfreiheit, freigegebene Informationen, Klasse für Einträge, Jahresordner, RSS, Transparenz, disclosure log, FOI, released information, log entry class, year folders, transparency
type: help
---
# Das Offenlegungsprotokoll veröffentlichen

Diese Aufgabe richtet sich an Site-Ersteller und Redakteure. Hintergrund:
[Offenlegungsprotokolle](../../transparenz-und-informationsfreiheit/offenlegungsprotokolle.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Schritte

1. **Setup > Klassen**: Legen Sie die Klasse `disclosure_entry` an, mit Aktenzeichen, Titel, Datum der Antwort, Thema,
   Ergebnis, Zusammenfassung des Antrags, Antwort und Gründen; machen Sie sie zu einem Container; Muster für den
   Objektnamen `<reference> <title>`.
2. Legen Sie den Ordner „Offenlegungsprotokoll“ und darunter einen Ordner für das laufende Jahr an.
3. Geben Sie der Klasse eine Zeilenansicht mit Aktenzeichen, Titel, Datum und Ergebnis (Template-Override in Ihrem
   Design).
4. **Setup > RSS**: ein Export des Protokollordners mit Unterknoten (die Schritte stehen in „Einen Feed neuer
   Datensätze veröffentlichen“).
5. Veröffentlichen Sie den ersten Eintrag (siehe „Eine Auskunft mit geprüften Schwärzungen veröffentlichen“).

## Prüfen

Die Protokollseite listet die Einträge, die neuesten zuerst; kein Eintrag zeigt den Namen oder die Kontaktdaten eines
Antragstellers; der Feed listet den Eintrag.

## Probleme

| Problem | Lösung |
|---|---|
| Einträge sind nicht nach Datum sortiert | setzen Sie die Sortierung des Ordners, oder sortieren Sie im Template nach dem Datumsfeld |
| Besucher können das Protokoll nicht öffnen | die Sektion des Ordners ist für Anonymous nicht lesbar |

## Verwandte Seiten

- [Proaktive Veröffentlichung](../../transparenz-und-informationsfreiheit/proaktive-veroeffentlichung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
