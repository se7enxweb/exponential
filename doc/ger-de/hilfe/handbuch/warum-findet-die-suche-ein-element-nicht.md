---
title: Warum findet die Suche ein Element nicht, von dem ich weiß, dass es existiert?
audience: editor, admin
category: manual
order: 496
summary: Die Suche findet nur veröffentlichte Versionen von Elementen, die Sie lesen dürfen, nur ganze Wörter und nur Felder, die als durchsuchbar markiert sind; Entwürfe, der Papierkorb, nicht durchsuchbare Felder, ein zu enger Suchbereich und eine später laufende Indizierung sind die üblichen Gründe, warum ein Element in den Ergebnissen fehlt.
keywords: Suche, nicht gefunden, keine Treffer, durchsuchbar, Suchindex, nur veröffentlicht, Entwürfe, Platzhalter, aktueller Ort, verzögerte Indizierung, neu indizieren, Rechte, search, not found, no results, searchable, search index, published only, drafts, wildcard, current location, delayed indexing, reindex, permissions
type: help
---
# Warum findet die Suche ein Element nicht, von dem ich weiß, dass es existiert?

Die Suche liest den Inhaltsbaum nicht direkt. Sie schlägt in einem Index nach, der gefüllt wird, wenn ein Element
veröffentlicht wird. Was nicht im Index steht oder für Sie nicht sichtbar ist, wird nicht gefunden. Gehen Sie die
Prüfungen unten der Reihe nach durch.

## Wer das tun kann

Jeder Redakteur kann die Prüfungen vornehmen. Für die letzten braucht es einen Administrator.

## Schritte

1. **Prüfen Sie die Wörter.** Die eingebaute Suche findet ganze Wörter, ohne Unterschied zwischen Groß- und
   Kleinschreibung: *Garten* findet nicht *Gartenarbeit*. Versuchen Sie eine andere Form des Wortes oder weniger Wörter.
   (Websites mit Solr-Suche sind nachsichtiger.)
2. **Prüfen Sie den Suchbereich.** Ist der Suchbereich auf **Aktueller Ort** oder auf eine Sektion gesetzt, bleiben
   Elemente an anderer Stelle außen vor. Öffnen Sie die Trichter-Schaltfläche des Suchfelds und wählen Sie die Suche
   überall.
3. **Prüfen Sie, ob das Element veröffentlicht ist.** Entwürfe, wartende und archivierte Versionen werden nicht
   durchsucht. Öffnen Sie das Element oder sehen Sie in **Meine Entwürfe** nach: Ein Element, das nie veröffentlicht
   wurde, lässt sich nicht finden.
4. **Prüfen Sie den Papierkorb.** Entfernte Elemente werden nicht gefunden. Öffnen Sie **Papierkorb** unten in der
   linken Spalte.
5. **Prüfen Sie das Feld.** Nur Felder, die in der Klasse als **durchsuchbar** markiert sind, werden indiziert; Zahlen,
   Preise und Daten meist nicht. Suchen Sie stattdessen nach einem Wort aus dem Titel, oder bitten Sie einen
   Administrator, sich die Klasse anzusehen (**Setup** > **Klassen**, das Kästchen **Durchsuchbar** des Feldes).
6. **Warten Sie ein wenig.** Manche Websites indizieren neue Inhalte im Hintergrund, einige Minuten nach dem
   Veröffentlichen.
7. **Fragen Sie einen Administrator**, wenn das Element immer noch fehlt. Ihre Rolle erlaubt Ihnen vielleicht nicht,
   es zu lesen, oder der Index muss neu aufgebaut werden.

## Was schiefgehen kann

- **Ein Sternchen bewirkt nichts.** Die Suche mit `*` am Wortende funktioniert nur auf Websites, die die
  Platzhaltersuche eingeschaltet haben, und dann erst nach einigen Buchstaben.
- **Ein geändertes Klassenfeld wird immer noch nicht gefunden.** Das Anhaken von **Durchsuchbar** wirkt sich auf
  Elemente aus, die danach veröffentlicht werden. Vorhandene Elemente werden gefunden, sobald sie erneut veröffentlicht
  werden oder ein Administrator den Index neu aufbaut.
- **Besucher finden weniger als Sie.** Die öffentliche Website zeigt keine versteckten Elemente, und Besucher dürfen
  womöglich weniger lesen als Redakteure.

## Verwandte Seiten

- [Suchen](../suchen.md)
- [Wie finde ich Inhalte in der Administration?](wie-finde-ich-inhalte.md)
- [Durchsuchbare Felder und Indizierung](../verwaltung/aufbauen/suche/durchsuchbare-felder-und-indizierung.md)
- [Suche mit ezfind und Solr](../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
