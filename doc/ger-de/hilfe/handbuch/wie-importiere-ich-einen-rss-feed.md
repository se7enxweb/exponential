---
title: Wie importiere ich einen RSS-Feed von einer anderen Website?
audience: admin
category: manual
order: 555
summary: Klicken Sie unter Setup > RSS auf Neuer Import, geben Sie die Quell-URL ein und klicken Sie auf Aktualisieren, wählen Sie den Zielordner, die Klasse und welches Feed-Feld welches Klassenfeld füllt, markieren Sie Aktiv und klicken Sie auf OK; der Cronjob für RSS-Importe legt dann die Objekte an.
keywords: RSS-Import, Feed, Import, Quell-URL, Zielpfad, Cronjob, rssimport, Syndication, RSS import, feed, import, source URL, destination path, cronjob, syndication
type: help
---
# Wie importiere ich einen RSS-Feed von einer anderen Website?

Ein RSS-Import liest regelmäßig den Feed einer anderen Website und legt für jeden neuen Eintrag ein Objekt an, zum
Beispiel Neuigkeiten eines Partners.

## Wer das tun kann

Benutzer, deren Rolle `rss/edit` hat.

## Schritte

1. Öffnen Sie **Setup** > **RSS** und klicken Sie unter **RSS Importe** auf **Neuer Import**.
2. Geben Sie einen **Namen** und die **Quell-URL** des Feeds ein und klicken Sie dann auf **Aktualisieren**.
   Die Seite liest den Feed und zeigt seine Felder.
3. Klicken Sie unter **Zielpfad** auf **Durchsuchen** und wählen Sie den Ordner für die neuen Objekte.
4. Wählen Sie die **Klasse** der neuen Objekte und für jedes Klassenfeld, welches Feed-Feld es füllt (oder
   **Ignorieren**).
5. Wählen Sie unter **Eigentümer der importierten Objekte** den Eigentümer (**Benutzer ändern**).
6. Markieren Sie **Aktiv** und klicken Sie auf **OK**.
7. Warten Sie auf den Cronjob für RSS-Importe und prüfen Sie dann den Ordner. **Cronjob öffnen** in der Liste zeigt,
   wann er läuft.

## Was schiefgehen kann

- **Es kommen keine Objekte an.** Der Cronjob läuft nicht, oder die Adresse des Feeds ist falsch. Öffnen Sie die
  Quelladresse in einem Browser.
- **Doppelte oder alte Einträge.** Der Import legt nur Einträge an, die er noch nicht gesehen hat. Wenn die andere
  Website die IDs ihrer Einträge ändert, kommen Einträge erneut.
- **Rechte.** Importieren Sie nur Feeds, die Sie weiterveröffentlichen dürfen.

## Verwandte Seiten

- [Wie veröffentliche ich einen RSS-Feed meiner Inhalte?](wie-veroeffentliche-ich-einen-rss-feed.md)
- [RSS-Feeds](../../anleitungen/rss-feeds.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
