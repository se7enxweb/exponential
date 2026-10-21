---
title: Eine archivierte Seite wiederherstellen
audience: government, editor, admin
category: public-sector
order: 470
summary: Schritt für Schritt - eine Seite aus dem Archiv in die aktuelle Site von Exponential zurückholen, gleich auf welche Weise sie archiviert wurde (in einen Archivordner verschoben, versteckt, auf einen archivierten Zustand gesetzt oder in eine Archivsektion gelegt), und prüfen, dass Besucher sie sehen.
keywords: wiederherstellen, archivierte Seite, zurückverschieben, aufdecken, versteckt, Objektzustand, Sektion, Archivordner, aus dem Archiv holen, Sichtbarkeit, öffentlicher Sektor, restore, archived page, move back, reveal, hidden, object state, section, archive folder, unarchive, visibility, public sector
type: help
---
# Eine archivierte Seite wiederherstellen

Für Redakteure und Administratoren, wenn eine archivierte Bekanntmachung oder Seite wieder aktuell sein muss (ein wieder
eröffnetes Beteiligungsverfahren, eine noch gültige Bekanntmachung). Wie das rückgängig gemacht wird, hängt davon ab, wie
sie archiviert wurde ([Alte Bekanntmachungen archivieren](../../schriftgut-und-archivierung/alte-bekanntmachungen-archivieren.md)).

## Inhalt

1. [Herausfinden, wie sie archiviert wurde](#herausfinden-wie-sie-archiviert-wurde)
2. [Schritte je Methode](#schritte-je-methode)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Herausfinden, wie sie archiviert wurde

Öffnen Sie das Element in der Administration:

- sein Ort im Baum: unter einem Archivordner?
- das Kennzeichen der Sichtbarkeit im Kopf des Knotens: **Versteckt** oder
  **Versteckt durch übergeordneten Eintrag**?
- der Reiter **Details**: seine Sektion und seine Inhaltszustände.
- das Audit für den Knoten: `/audit/console/(object)/node:<node id>` zeigt Verschiebungen, Verstecken sowie Änderungen
  an Zuständen und Sektionen.

## Schritte je Methode

| Archiviert durch | Wiederherstellen |
|---|---|
| Verschieben in einen Archivordner | **Verschieben** Sie es zurück zu seinem früheren übergeordneten Element (der Eintrag des Audits zur Verschiebung nennt es) |
| Verstecken | **Aufdecken** (wenn durch ein übergeordnetes Element versteckt, decken Sie dieses auf oder verschieben Sie das Element heraus) |
| einen Archivzustand | Reiter Details, **Zustand des Objekts**, den aktuellen Zustand setzen |
| eine Archivsektion | die frühere Sektion zuweisen (**Inhalt zuweisen** der Sektion oder der Reiter Details) |
| das geplante Verstecken (`hide.php` und ein Versteckdatum) | das Versteckdatum leeren oder ändern, dann Aufdecken; sonst versteckt der nächste Lauf des Cronjobs es wieder |

Wenn es im **Papierkorb** liegt, wurde es entfernt, nicht archiviert: siehe „Ein Element aus dem Papierkorb
wiederherstellen“.

## Prüfen, ob es funktioniert hat

- Öffnen Sie die Seite in einem privaten Fenster auf dem öffentlichen Siteaccess: Sie erscheint.
- Sie erscheint in der aktuellen Liste (wenn die Liste sie enthalten soll) und nach der Neuindizierung in der Suche.

## Verwandte Seiten

- [Die Bekanntmachungen des Vorjahres archivieren](die-bekanntmachungen-des-vorjahres-archivieren.md)
- [Inhalte verstecken](../../schriftgut-und-archivierung/inhalte-verbergen.md)
- [Objektzustände für Schriftgut](../../schriftgut-und-archivierung/objektzustaende-fuer-schriftgut.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
