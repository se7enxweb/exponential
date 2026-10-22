---
title: Wie tausche ich zwei Elemente im Baum?
audience: editor
category: manual
order: 430
summary: Fortgeschritten > Gegen anderen Knoten austauschen vertauscht den Inhalt zweier Orte, während ihre Plätze, Adressen und Unterelemente bleiben, wo sie sind; damit setzen Sie einen vorbereiteten Ersatz an die Spitze eines Zweigs, ohne alles darunter zu verschieben.
keywords: tauschen, austauschen, gegen anderen Knoten austauschen, Container ersetzen, Klasse ändern, Sitemap für Teilbaum, zweistufiges Verzeichnis, Knoten-ID, Objekt-ID, swap, swap with another node, exchange, replace a container, change class, sitemap for subtree, two-level index, node ID, object ID
type: help
---
# Wie tausche ich zwei Elemente im Baum?

Ein Tausch vertauscht, was zwei Orte zeigen. Jeder Ort behält seinen Platz im Baum, seine Unterelemente und sein
übergeordnetes Element; nur die Objekte dahinter tauschen die Plätze. Das ist der schnelle Weg, ein neues Objekt an
die Spitze eines bestehenden Zweigs zu setzen, zum Beispiel eine überarbeitete Startseite einer Sektion, die Sie
im Verborgenen vorbereitet haben und nun vor alles darunter stellen möchten.

## Wer das tun kann

Redakteure, die beide Elemente bearbeiten und die Klasse jedes Elements unter dem übergeordneten Element des anderen
anlegen dürfen. Ohne diese Rechte wird der Tausch abgelehnt.

## Schritte

1. Bereiten Sie den Ersatz vor: Legen Sie ihn an einer anderen Stelle an, zum Beispiel in einem versteckten Ordner,
   und veröffentlichen Sie ihn.
2. Prüfen Sie den Zweig, den Sie ändern wollen: Öffnen Sie das Baummenü des Elements und wählen Sie
   **Fortgeschritten** > **Sitemap für Teilbaum**. Ein zweistufiges Verzeichnis von allem darunter öffnet sich.
3. Öffnen Sie das Baummenü des zu ersetzenden Elements und wählen Sie **Fortgeschritten** > **Gegen anderen Knoten
   austauschen**.
   Ein Browser öffnet sich: *Wählen Sie den Austausch-Knoten für* das Element.
4. Suchen Sie den Ersatz, wählen Sie ihn mit seinem Optionsfeld aus und klicken Sie auf **Auswählen**.
   Die beiden Objekte tauschen die Plätze. Die Unterelemente bleiben, wo sie waren, und hängen jetzt unter dem Ersatz.
5. Öffnen Sie das alte Objekt an seinem neuen Platz (wo der Ersatz war) und entfernen oder archivieren Sie es.

## Was schiefgehen kann

- **Der Tausch wird wegen Unterelementen abgelehnt.** Ein Objekt, das keine Unterelemente aufnehmen kann (seine Klasse
  ist kein Container), kann keinen Platz einnehmen, der Unterelemente hat. Wählen Sie für den Ersatz eine
  Container-Klasse.
- **Die Sektion ändert sich.** Wenn ein Hauptort den Besitzer wechselt, übernimmt das Objekt die Sektion seines neuen
  übergeordneten Elements. Prüfen Sie danach den Tab **Details** beider Elemente.
- **Das alte Objekt ist jetzt sichtbar.** Es hat den Platz des Ersatzes eingenommen; war dieser Platz sichtbar, ist es
  auch das alte Objekt. Entfernen oder verstecken Sie es sofort.
- **Knoten- und Objektnummern.** Die Orte behalten ihre Knoten-IDs; die Objekt-IDs wandern. Links auf einen Knoten
  führen jetzt zum anderen Objekt. Der Tab **Details** zeigt beide Nummern.

## Verwandte Seiten

- [Wie verschiebe oder kopiere ich ein Element?](wie-verschiebe-oder-kopiere-ich-ein-element.md)
- [Wie ändere ich die Inhaltsklasse eines Objekts?](wie-aendere-ich-die-inhaltsklasse-eines-objekts.md), die
  Alternative, wenn sich die Inhalte Feld für Feld umwandeln lassen
- [Wie verstecke ich ein Element, ohne es zu entfernen?](wie-verstecke-ich-ein-element.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
