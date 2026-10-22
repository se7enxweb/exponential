---
title: Wie schalte ich eine Erweiterung ein oder aus?
audience: admin
category: manual
order: 645
summary: Klicken Sie unter Setup > Extensions auf der Karte der Erweiterung auf Aktivieren oder Deaktivieren, lesen Sie die Risiken unter Änderungen prüfen und klicken Sie auf Änderungen anwenden; die Seite schreibt ActiveExtensions, leert die Caches und erzeugt die Autoloads neu.
keywords: Erweiterung, aktivieren, deaktivieren, einschalten, ausschalten, Plugin, Add-on, Setup Extensions, Änderungen anwenden, Ladereihenfolge, ActiveExtensions, autoload, extension, activate, deactivate, turn on, turn off, plugin, add-on, apply changes, loading order
context: setup/extensions
type: help
---
# Wie schalte ich eine Erweiterung ein oder aus?

Erweiterungen fügen Exponential Funktionen hinzu, ohne seine eigenen Dateien zu ändern. Eine Erweiterung, die auf dem
Server liegt, tut nichts, bis sie aktiv ist. **Setup** > **Extensions** schaltet Erweiterungen für die gesamte
Installation ein und aus.

## Wer das tun kann

Benutzer, deren Rolle die Richtlinie `setup/setup` hat, meist nur Administratoren. Die Dateien der Erweiterung auf den
Server zu bringen ist eine eigene Aufgabe für einen Entwickler oder den Hoster.

## Schritte

1. Klicken Sie im oberen Menü auf **Setup** und wählen Sie **Extensions**.
   Die Liste zeigt zuerst die aktiven Erweiterungen in ihrer Ladereihenfolge und danach die inaktiven.
2. Suchen Sie die Erweiterung mit der Suche, oder wählen Sie im Filter **Inaktiv** oder **Aktiv**.
3. Klicken Sie auf ihrer Karte auf **Aktivieren**, um sie einzuschalten, oder auf **Deaktivieren**, um sie
   auszuschalten.
   Noch wird nichts geschrieben; eine Leiste zeigt, wie viele Änderungen warten.
4. Klicken Sie auf **Änderungen prüfen**.
   Die Seite listet die Änderungen und die Risiken auf, zum Beispiel eine Erweiterung, die eine andere aktive
   Erweiterung braucht.
5. Ist ein Risiko rot markiert und möchten Sie die Änderung trotzdem, haken Sie das Bestätigungsfeld darunter an.
6. Klicken Sie auf **Änderungen anwenden**.
   Die Seite schreibt die Liste, leert die Caches und meldet, was sich geändert hat.
7. Öffnen Sie die Seiten der Erweiterung und prüfen Sie, ob sie funktioniert.

## Was schiefgehen kann

- **Die Erweiterung ist nicht in der Liste.** Ihr Ordner liegt auf dem Server nicht unter `extension/`. Fragen Sie die
  Person, die auf dem Server Software installiert.
- **Die Website verhält sich wie vorher.** Wenn die Website auch von Exponential Velocity ausgeliefert wird, behalten
  dessen Worker die alte Liste, bis es neu gestartet wird (`./console exp:velocity restart`); die Seite zeigt, ob
  Velocity läuft.
- **Nach einer Deaktivierung funktioniert etwas anderes nicht mehr.** Eine andere Erweiterung brauchte sie, oder sie
  lieferte das Design eines Siteaccess. Aktivieren Sie sie wieder; der Prüfschritt nennt diese Fälle, bevor Sie
  anwenden.
- **Die Erweiterung braucht mehr als die Aktivierung.** Viele Erweiterungen brauchen eigene Datenbanktabellen,
  Einstellungen oder Richtlinien. Lesen Sie zuerst die eigene Dokumentation der Erweiterung im Hilfecenter.
- **Jemand anderes hat gleichzeitig eine Änderung vorgenommen.** Die Seite bemerkt das und beginnt mit der
  gespeicherten Liste neu, statt sie zu überschreiben.
- **Achtung:** Wenn Sie `ezformtoken` deaktivieren, verliert jedes Formular seinen Schutz gegen gefälschte Anfragen,
  und wenn Sie `ezjscore` deaktivieren, funktionieren die Skripte der Administration nicht mehr. Lassen Sie beide
  aktiv.

## Verwandte Seiten

- [Die Seite Extensions](../../anleitungen/extensions-seite.md): die Karten, die Ladereihenfolge und die Prüfung im Detail
- [Wie sehe ich die aktiven Erweiterungen und die Systeminformationen?](wie-sehe-ich-aktive-erweiterungen-und-systeminformationen.md)
- [Eine Erweiterung aktivieren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/eine-erweiterung-aktivieren.md)
- [Erweiterungen](../../anleitungen/extensions.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
