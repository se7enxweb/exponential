---
title: Eine Sendung umziehen, pausieren oder beenden
audience: podcaster
category: podcasting-streaming
order: 22
summary: Was Sie mit einem Podcast-Feed tun, wenn die Sendung pausiert, an eine neue Adresse oder auf eine neue Website umzieht, endgültig endet oder zurückgezogen werden muss - die Felder Umgezogen nach, Die Sendung ist abgeschlossen und Die Sendung heraushalten, der Schalter Aktiv und was Sie nie tun sollten.
keywords: Feed umziehen, neue Feed-URL, itunes:new-feed-url, Weiterleitung, Pause, Sendepause, Sendung beenden, itunes:complete, zurückziehen, itunes:block, inaktiver Export, Export entfernen, Archiv, move feed, new feed URL, redirect, pause, hiatus, end show, withdraw, inactive export, remove export, archive
type: help
---
# Eine Sendung umziehen, pausieren oder beenden

Diese Seite ist für Produzenten, deren Sendung ihren Zustand ändert: eine Staffelpause, eine neue Adresse, die letzte
Episode oder ein Rückzug. Für jeden Fall gibt es ein passendes Feld im Abschnitt **Apple Podcasts** des Exports oder
eine Einstellung des Exports. Die Felder werden nur in den Feed geschrieben, wenn sie gesetzt sind
(`generateITunes()` in `kernel/classes/ezrssexport.php`).

## Inhalt

1. [Überblick](#überblick)
2. [Eine Pause zwischen Staffeln](#eine-pause-zwischen-staffeln)
3. [Den Feed umziehen](#den-feed-umziehen)
4. [Die Sendung beenden](#die-sendung-beenden)
5. [Die Sendung zurückziehen](#die-sendung-zurückziehen)
6. [Tun Sie das nie](#tun-sie-das-nie)

## Überblick

| Situation | Was zu tun ist | Element im Feed |
|---|---|---|
| Eine Pause | nichts; halten Sie den Feed aktiv | keines |
| Neue Feed-Adresse | **Umgezogen nach** ausfüllen, die alte Adresse weiter antworten lassen | `itunes:new-feed-url` |
| Die Sendung ist beendet | **Die Sendung ist abgeschlossen - keine weiteren Folgen** ankreuzen | `itunes:complete` `Yes` |
| Aus dem Verzeichnis von Apple zurückziehen | **Die Sendung aus dem Apple-Podcasts-Verzeichnis heraushalten** ankreuzen | `itunes:block` `Yes` |
| Den Feed nicht mehr antworten lassen | **Aktiv** abwählen | die Adresse liefert einen Fehler |

## Eine Pause zwischen Staffeln

Nichts muss sich ändern. Der Feed listet weiter die vorhandenen Episoden. Wenn Hörer davon wissen sollen,
veröffentlichen Sie eine kurze Ankündigungsepisode oder sagen es auf der Seite der Sendung. Wenn die Sendung
zurückkehrt, erscheinen die neuen Episoden wie gewohnt oben.

## Den Feed umziehen

Sie ziehen einen Feed vielleicht um, weil die Website eine neue Domain bekommt, die Zugriffs-URL sich ändern muss oder
die Sendung auf eine andere Website wechselt.

1. Bringen Sie zuerst den neuen Feed zum Laufen und prüfen Sie ihn ([Ihren Feed prüfen](ihren-feed-pruefen.md)).
2. Tragen Sie im **alten** Export unter **Umgezogen nach** die vollständige neue Feed-Adresse ein. Sie wird geprüft wie
   die Adresse des Titelbilds: `http` oder `https`, höchstens 255 Zeichen.
3. Lassen Sie den alten Export **aktiv** und die Episoden auflisten, damit Apps, die ihn lesen, die neue Adresse sehen.
4. Informieren Sie die Verzeichnisse über deren eigene Werkzeuge, so wie sie es verlangen.
5. Entfernen Sie den alten Export erst, wenn Sie sicher sind, dass ihn niemand mehr liest. Ihn zu entfernen lässt die
   Adresse verstummen ([RSS-Feeds](../../anleitungen/rss-feeds.md#5-anlegen-und-entfernen)).

Wenn sich nur der Hostname der Website ändert und der alte Host weiter zum neuen weiterleitet, erledigt die
Weiterleitung des Webservers die Arbeit; füllen Sie trotzdem **Umgezogen nach** aus, damit Apps die neue Adresse
speichern.

## Die Sendung beenden

1. Veröffentlichen Sie die letzte Episode.
2. Kreuzen Sie im Export **Die Sendung ist abgeschlossen - keine weiteren Folgen** an und drücken Sie **OK**.
3. Lassen Sie den Export **aktiv** und die Episoden veröffentlicht. Eine abgeschlossene Sendung kann noch jahrelang
   gehört werden.

Achten Sie darauf, dass die **Anzahl der Objekte** alle Episoden abdeckt, damit das Archiv im Feed vollständig bleibt
([Wie eine Sendung zu Inhalten wird](wie-eine-sendung-zu-inhalten-wird.md)).

## Die Sendung zurückziehen

Um die Sendung aus dem Verzeichnis von Apple Podcasts zu nehmen, während der Feed für bestehende Abonnenten weiter
funktioniert, kreuzen Sie **Die Sendung aus dem Apple-Podcasts-Verzeichnis heraushalten** an. Um den Feed ganz zu
stoppen, wählen Sie **Aktiv** ab: Die Adresse antwortet nicht mehr. Die Episoden bleiben auf der Website, es sei denn,
Sie verbergen oder entfernen sie ([Episoden verbergen und zurückziehen](episoden-verbergen-und-zurueckziehen.md)).

Ein Rückzug aus rechtlichen Gründen (ein Rechtestreit, eine gerichtliche Anordnung) bedeutet meist auch, bestimmte
Episoden zu entfernen oder zu verbergen; siehe [Rechte und Lizenzen](rechte-und-lizenzen.md).

## Tun Sie das nie

- **Die Zugriffs-URL ohne Umgezogen nach ändern.** Jeder Abonnent verliert die Sendung.
- **Den Export neu anlegen, um "frisch anzufangen".** Der neue Export ist ein neuer Feed; Verzeichnisse kennen ihn
  nicht.
- **Episoden neu anlegen.** Neue Objekte erhalten neue Remote-IDs und damit neue `guid`-Werte; Apps listen sie als neu.
- **Das Audio alter Episoden löschen.** Apps, die sie auflisten, erhalten Fehler.

## Verwandte Seiten

- [Referenz der Kanalfelder](referenz-der-kanalfelder.md)
- [Weiterleitungen und verschobene Inhalte](../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)
- [Bei Podcast-Verzeichnissen einreichen](bei-podcast-verzeichnissen-einreichen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
