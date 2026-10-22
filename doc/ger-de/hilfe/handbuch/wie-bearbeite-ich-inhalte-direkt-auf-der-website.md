---
title: Wie bearbeite ich Inhalte direkt auf der Website?
audience: editor
category: manual
order: 465
summary: Auf Websites, deren Design die Website-Werkzeugleiste zeigt, melden Sie sich auf der öffentlichen Website an; die Werkzeugleiste über jeder Seite erstellt, bearbeitet, verschiebt und entfernt Inhalte an Ort und Stelle, fügt Orte hinzu, setzt die Sortierung und veröffentlicht, speichert oder verwirft im Bearbeitungsmodus den Entwurf; Kopieren, der Papierkorb und die meisten Einstellungen bleiben in der Administration.
keywords: Website-Werkzeugleiste, Frontend-Bearbeitung, Bearbeiten auf der Website, hier erstellen, bearbeiten, verschieben, entfernen, Orte hinzufügen, Sortierung, Entwurf speichern, Versionen verwalten, übersetzen, importieren, exportieren, website toolbar, front-end editing, ezwt, create here, edit, move, remove, add locations, sorting, store draft, manage versions, translate, import, export, replace
type: help
---
# Wie bearbeite ich Inhalte direkt auf der Website?

Manche Website-Designs zeigen oben auf jeder Seite eine **Website-Werkzeugleiste**, wenn ein Redakteur auf der
öffentlichen Website angemeldet ist. Sie wirkt auf die Seite, die Sie gerade ansehen; so können Sie einen Tippfehler
korrigieren oder einen Artikel genau dort hinzufügen, wo Sie ihn sehen, ohne die Administration zu öffnen. Die
Werkzeugleiste stammt aus der Erweiterung *ezwt* und erscheint in den Designs, die sie einbinden (die klassischen
Website-Designs tun das); eine Website mit einem anderen Design zeigt sie unter Umständen nicht.

## Wer das tun kann

Redakteure, deren Rolle die Richtlinie `websitetoolbar/use` hat (sie lässt sich auf einige Klassen beschränken), dazu
die üblichen Rechte `content/create`, `content/edit` und `content/remove` für das, was sie ändern. Melden Sie sich mit
Ihrem gewohnten Konto auf der öffentlichen Website an.

## Schritte

1. Melden Sie sich auf der öffentlichen Website an und öffnen Sie die Seite, an der Sie arbeiten möchten.
   Die Werkzeugleiste erscheint über der Seite.
2. Verwenden Sie ihre Schaltflächen auf der aktuellen Seite:
   - **Hier erstellen**: Wählen Sie in der Liste daneben eine Klasse und klicken Sie dann auf die Schaltfläche; das
     neue Element wird unter der aktuellen Seite angelegt;
   - **Bearbeiten**: die aktuelle Seite bearbeiten;
   - **Verschieben** und **Entfernen**: die Seite an einen anderen Ort verschieben oder entfernen (sie kommt in den
     Papierkorb);
   - **Orte hinzufügen**: die Seite zusätzlich an einer anderen Stelle zeigen;
   - **Sortierung**: die Reihenfolge der Elemente unter der aktuellen Seite ändern.
3. Im Bearbeitungsformular bietet die Werkzeugleiste **Zur Veröffentlichung schicken**, **Entwurf speichern**,
   **Entwurf speichern und beenden**, **Entwurf verwerfen**, **Vorschau** und **Versionen verwalten** an, für eine
   Übersetzung außerdem **Übersetzen** mit **Übersetzen von**.
4. Wenn Sie fertig sind, klicken Sie auf **Zur Veröffentlichung schicken**, oder auf **Entwurf speichern und beenden**,
   um später weiterzumachen.

Bei Klassen, die dafür eingerichtet sind, hat die Werkzeugleiste außerdem **Importieren**, **Replace** und
**Exportieren** für Textverarbeitungsdokumente:
siehe [Wie importiere oder exportiere ich ein Textverarbeitungsdokument?](wie-importiere-oder-exportiere-ich-ein-textdokument.md)

## Was schiefgehen kann

- **Es erscheint keine Werkzeugleiste.** Sie sind nicht angemeldet, Ihrer Rolle fehlt `websitetoolbar/use` für diese
  Klasse, oder das Design der Website bindet die Werkzeugleiste nicht ein. Verwenden Sie stattdessen die
  Administration.
- **Eine Schaltfläche fehlt.** Die Werkzeugleiste bietet nur an, was Ihre Rolle auf dieser Seite erlaubt; **Hier
  erstellen** listet nur Klassen auf, die Sie dort anlegen dürfen.
- **Sie müssen kopieren, aus dem Papierkorb wiederherstellen oder eine Version entfernen.** Das geschieht in der
  Administration. Der Link der Werkzeugleiste zur Administration (falls Ihre Website einen hat) öffnet dort dieselbe
  Seite.
- **Ein Entwurf bleibt zurück.** Wenn Sie das Bearbeitungsformular mit der Zurück-Schaltfläche des Browsers verlassen,
  bleibt der Entwurf bestehen. Verwenden Sie die Schaltflächen der Werkzeugleiste und räumen Sie **Meine Entwürfe** ab
  und zu auf.

## Verwandte Seiten

- [Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md)
- [Was tue ich bei einem „Möglichen Bearbeitungs-Konflikt“?](was-tue-ich-bei-einem-moeglichen-bearbeitungskonflikt.md)
- [Wie ändere ich die Reihenfolge der Unterelemente?](wie-aendere-ich-die-reihenfolge-der-unterelemente.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
