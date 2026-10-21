---
title: Eine Seite verschieben
audience: government, editor
category: public-sector
order: 170
summary: Für Redakteure - eine Seite mit allem darunter mit Verschieben an eine andere Stelle im Inhaltsbaum verschieben, verstehen, was mit ihrer Adresse, ihrem Platz im Menü, ihrer Sektion und den Berechtigungen geschieht, und große Verschiebungen handhaben.
keywords: Seite verschieben, verschieben, Inhaltsbaum, neues übergeordnetes Element, URL-Alias, Weiterleitung, Sektion, Berechtigungen, Teilbaum, Inhaltsauftrag, Ausgewähltes verschieben, move page, move, content tree, new parent, URL alias, redirect, section, permissions, subtree, content job, Move selected
type: help
---
# Eine Seite verschieben

Diese Seite ist für Redakteure, die Inhalte umstrukturieren. **Verschieben** setzt ein Element mit allem darunter
unter ein neues übergeordnetes Element. Die alte Adresse funktioniert weiter: Sie leitet auf die neue weiter
([Weiterleitungen und verschobene Inhalte](../../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Was sich ändert](#was-sich-ändert)
4. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Sie dürfen an der neuen Stelle Inhalte anlegen und das Element verschieben (sonst ist die Schaltfläche
  **Verschieben** ausgegraut und sagt, warum).
- Sie wissen, ob die Seite umziehen oder zusätzlich an einer zweiten Stelle erscheinen soll
  ([Ein Element an mehreren Stellen veröffentlichen](ein-element-an-mehreren-stellen-veroeffentlichen.md)).

## Schritte

1. Öffnen Sie die Seite und klicken Sie auf **Verschieben**.
2. Gehen Sie im Browser, der sich öffnet, zum neuen übergeordneten Element und wählen Sie es aus; bestätigen Sie.
3. Für mehrere Seiten: Markieren Sie sie in der Liste **Unterelemente** des übergeordneten Elements,
   **Weitere Aktionen** > **Ausgewähltes verschieben**.

Das Verschieben eines großen Teilbaums kann als Hintergrundauftrag angeboten werden (ab 50 Knoten wird zuerst der
Hintergrund angeboten); sein Fortschritt erscheint unter `/content/job/<id>`, und alle Aufträge stehen unter
`/content/jobs` ([Inhaltsmodell und Bearbeitung, 8](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#8-große-operationen-als-inhaltsjobs)).

## Was sich ändert

| Was | Nach dem Verschieben |
|---|---|
| Adresse | ein neuer URL-Alias aus dem neuen Pfad; der alte leitet weiter |
| Menü | sie erscheint dort, wo das neue übergeordnete Element sie einordnet |
| Sektion | Objekte, die die Sektion des alten übergeordneten Elements hatten, erhalten die Sektion des neuen; Objekte mit eigener Sektion behalten sie ([Sektionen, 8.2](../../../../anleitungen/sektionen.md#82-verschieben-und-orte)) |
| Wer lesen und bearbeiten darf | folgt der neuen Sektion und Stelle |
| Relationen und Einbettungen | unverändert: Sie verweisen auf das Objekt, nicht auf die Adresse |

## Das Ergebnis prüfen

- [ ] Die Seite öffnet sich an ihrer neuen Stelle; die alte Adresse leitet weiter (abgemeldet öffnen).
- [ ] Auf dem Reiter **Details** ist die Sektion die erwartete; wenn Besucher jetzt „Zugriff verweigert“ erhalten,
  dürfen sie die neue Sektion nicht lesen.
- [ ] Die Menüs zeigen die Seite an der neuen Stelle.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| In eine Sektion verschieben, die Besucher nicht lesen dürfen | prüfen Sie zuerst die Sektion des Ziels |
| Eine Seite verschieben, die über einen zweiten Ort auch anderswo gelistet ist | **Verschieben** verschiebt den Ort, auf dem Sie sich befinden; prüfen Sie den Reiter **Orte** |
| Die Berechtigungen eines Redakteurs zerstören | nach dem Verschieben erreichen Redakteure, die auf einen Teilbaum beschränkt sind, die Seite vielleicht nicht mehr |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Alte Adressen nach einer Umstrukturierung weiterleiten](alte-adressen-nach-einer-umstrukturierung-weiterleiten.md)
- [Inhalte erstellen und bearbeiten](../../../inhalte-bearbeiten.md#verschieben-kopieren-und-entfernen)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
