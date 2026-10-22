---
title: Episoden verbergen und zurückziehen
audience: podcaster
category: podcasting-streaming
order: 28
summary: Was mit der Website und dem Podcast-Feed geschieht, wenn eine Episode verborgen, in den Papierkorb verschoben oder so eingestellt wird, dass sie zu einem Datum verschwindet - die Cronjobs hide.php und unpublish.php, der Papierkorb, die Wirkung auf die Elemente und guids des Feeds - und wie Sie Episoden zurückziehen, ohne Abonnenten zu stören.
keywords: Episode verbergen, Episode entfernen, Veröffentlichung zurücknehmen, ablaufen lassen, herunternehmen, zurückziehen, Papierkorb, wiederherstellen, hide.php, unpublish.php, HideSettings, UnpublishSettings, Feed, guid, Ablauf der Rechte, hide episode, remove episode, unpublish, expire, take down, retire, trash, restore, rights expiry
type: help
---
# Episoden verbergen und zurückziehen

Diese Seite ist für Produzenten, die eine Episode von der Website nehmen müssen: Ein Rechtezeitraum endet, ein Gast
widerruft seine Einwilligung, ein Fehler lässt sich durch Bearbeiten nicht beheben, oder altes Material wird
zurückgezogen. Exponential bietet Verbergen, Verschieben in den Papierkorb und beides zu einem Datum. Jedes wirkt sich
anders auf den Feed aus.

## Inhalt

1. [Die Möglichkeiten](#die-möglichkeiten)
2. [Wirkung auf den Feed](#wirkung-auf-den-feed)
3. [Jetzt verbergen oder entfernen](#jetzt-verbergen-oder-entfernen)
4. [Zu einem Datum verbergen oder entfernen](#zu-einem-datum-verbergen-oder-entfernen)
5. [Eine Episode zurückholen](#eine-episode-zurückholen)
6. [Bewährte Vorgehensweise](#bewährte-vorgehensweise)

## Die Möglichkeiten

| Möglichkeit | Auf der Website | In der Administration | Umkehrbar |
|---|---|---|---|
| **Verstecken** | für Besucher verschwunden, mit allem darunter | weiterhin da, als versteckt markiert | ja: wieder zeigen |
| **Entfernen** | verschwunden | im Papierkorb | ja: **Wiederherstellen** aus dem Papierkorb, bis er geleert wird |
| Aus dem Papierkorb löschen | verschwunden | verschwunden | nein |
| **Zu einem Datum verstecken** (`hide.php`) | nach dem Datum verschwunden | als versteckt markiert | ja |
| **Zu einem Datum entfernen** (`unpublish.php`) | nach dem Datum verschwunden | im Papierkorb | ja, aus dem Papierkorb |

Einzelheiten: [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md#ein-objekt-verstecken).

## Wirkung auf den Feed

Der Podcast-Export liest die Objekte unterhalb seiner Quellen zu dem Zeitpunkt, an dem er den Feed schreibt
(`podcastItemList()` in `kernel/classes/ezrssexport.php`):

| Aktion | Feed |
|---|---|
| Versteckt | das Objekt wird übersprungen, es sei denn, die Website ist so eingestellt, dass sie versteckte Objekte zeigt |
| Im Papierkorb oder entfernt | das Objekt liegt nicht mehr unterhalb der Quelle und wird nicht aufgeführt |
| Wiederhergestellt oder wieder gezeigt | das Objekt kehrt mit **derselben guid** zurück (der Remote-ID des Objekts), sodass Apps es wiedererkennen |

Die Änderung erreicht den Feed, wenn der Feed-Cache abläuft (`CacheTime`, im Auslieferungszustand 1200 Sekunden). Apps,
die die Episode bereits heruntergeladen haben, behalten ihre Kopie; Exponential kann keine Dateien von den Geräten der
Hörer entfernen.

Verstecken betrifft, was die Website zeigt; es ist keine Berechtigung. Wenn die Datei auf keinen Fall mehr abgerufen
werden darf, zum Beispiel aus rechtlichen Gründen, entfernen Sie das Objekt oder verschieben es in eine Sektion, die
Besucher nicht lesen dürfen ([Episoden nur für Mitglieder](episoden-nur-fuer-mitglieder.md)), und testen Sie die alte
Download-Adresse abgemeldet.

## Jetzt verbergen oder entfernen

1. Öffnen Sie die Episode.
2. **Verstecken**: Öffnen Sie im Baum das Menü des Objekts und wählen Sie **Fortgeschritten** > **Verstecken /
   Zeigen** ([Ein Objekt verstecken](../veroeffentlichen-und-versionen.md#ein-objekt-verstecken)).
3. **Entfernen**: Drücken Sie auf der Seite des Objekts **Entfernen** und bestätigen Sie; das Objekt kommt in den
   Papierkorb ([Entfernen und wiederherstellen](../veroeffentlichen-und-versionen.md#entfernen-und-wiederherstellen)).

## Zu einem Datum verbergen oder entfernen

Beide Cronjobs laufen im Standardteil von `settings/cronjob.ini` und tun nichts, bis ein Administrator ihre
Einstellungen in `settings/content.ini` ausfüllt:

| Cronjob | Einstellungen | Was die Klasse braucht |
|---|---|---|
| `hide.php` | `[HideSettings] RootNodeList[]` und `HideDateAttributeList[<class>]=<date field>` | ein Datumsfeld, zum Beispiel `hide_date` |
| `unpublish.php` | `[UnpublishSettings] RootNodeList[]` und `ClassList[]` | ein Feld `unpublish_date` |

Für eine Sendung mit lizenziertem Material, das nur für einen Zeitraum online sein darf, fügen Sie der Episodenklasse
ein Datumsfeld hinzu und lassen `hide.php` die Episoden verstecken, wenn der Zeitraum endet. Siehe
[Zeitgesteuertes Veröffentlichen](../kunden/alltag/zeitgesteuertes-veroeffentlichen.md#zu-einem-datum-verbergen-oder-entfernen).

## Eine Episode zurückholen

- Versteckt: Zeigen Sie sie wieder (**Fortgeschritten** > **Verstecken / Zeigen**). Sie kehrt in Listen und den Feed
  zurück.
- Im Papierkorb: **Wiederherstellen** an ihren alten Ort oder einen neuen ([Der Papierkorb](../veroeffentlichen-und-versionen.md#der-papierkorb)).
- Aus dem Papierkorb gelöscht: Sie kann nicht zurückkommen. Sie neu anzulegen erzeugt ein neues Objekt mit einer neuen
  `guid`; Apps führen es als neue Episode.

## Bewährte Vorgehensweise

- Bevorzugen Sie **Verstecken**, wenn die Episode zurückkehren könnte; lassen Sie entfernte Episoden eine Weile im
  Papierkorb, bevor Sie sie endgültig löschen.
- Notieren Sie, warum und wann eine Episode zurückgezogen wurde, zum Beispiel in einem internen Notizfeld oder im
  Protokoll Ihres Teams.
- Verschwindet eine Episode wegen eines Rechte- oder Einwilligungsproblems, prüfen Sie, ob ihr Audio nicht von anderen
  Seiten, Einbettungen oder den Shownotes anderer Episoden verlinkt ist ([Rechte und Lizenzen](rechte-und-lizenzen.md)).
- Eine ganze Sendung zurückzuziehen ist etwas anderes: siehe [Eine Sendung umziehen, pausieren oder beenden](eine-sendung-umziehen-pausieren-oder-beenden.md).

## Verwandte Seiten

- [Archivieren und der Papierkorb](../kunden/alltag/archivieren-und-der-papierkorb.md)
- [Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md)
- [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
