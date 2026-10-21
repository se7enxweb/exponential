---
title: Wie wechsle ich zwischen Admin 4 und der klassischen Administration?
audience: editor, admin
category: manual
order: 270
summary: Wählen Sie im Kasten Oberfläche der rechten Werkzeugleiste unter Administrationsdesign ein anderes Design; die Seite lädt darin neu, nur für Ihre Sitzung, und das erste Design der Liste bringt Sie zurück.
keywords: Administrationsdesign, admin4, klassisch, adminui, admin3, UI-Umschalter, uiswitch, Oberfläche, Design wechseln, uiswitch/design, administration design, classic, ui switch, interface, switch design
type: help
---
# Wie wechsle ich zwischen Admin 4 und der klassischen Administration?

Sie können in einem anderen Administrationsdesign arbeiten, zum Beispiel im klassischen Design, ohne für andere
Benutzer etwas zu ändern. Die Wahl gilt, bis Sie sich abmelden.

## Wer das tun kann

Benutzer, deren Rolle die Richtlinie `uiswitch/design` hat. Die Auswahl erscheint nur, wenn mindestens zwei Designs
angeboten werden.

## Schritte

1. Suchen Sie den Kasten **Oberfläche** in der rechten Werkzeugleiste.
   Er zeigt **Sprache der Oberfläche** und darunter **Administrationsdesign**.
2. Wählen Sie unter **Administrationsdesign** das Design, zum Beispiel **Klassisch**.
   Die Seite lädt in diesem Design neu, auf derselben Seite.
3. Um zurückzukehren, wählen Sie das erste Design der Liste: das Design, das der Siteaccess normalerweise verwendet.

![Der Kasten Oberfläche der rechten Werkzeugleiste mit Sprache der Oberfläche und Administrationsdesign](../../../eng-us/help/manual/images/ui-switch.webp)

## Was schiefgehen kann

- **Der Kasten Oberfläche oder die Designliste fehlt.** Ihrer Rolle fehlt `uiswitch/design`, oder es wird nur ein
  Design angeboten. Fragen Sie einen Administrator.
- **Die rechte Werkzeugleiste ist geschlossen.** Öffnen Sie sie mit dem Pfeil am rechten Rand des Fensters.
- **Eine Seite sieht nach dem Zurückwechseln anders aus.** Laden Sie die Seite neu. Jedes Design hat seine eigenen
  Caches; für andere Benutzer wurde nichts geändert.
- **Die Sprache der Oberfläche hat sich auch geändert.** Das hat sie nicht: Sprache und Design sind getrennte
  Einstellungen im selben Kasten. Wählen Sie die Sprache erneut und klicken Sie auf **Anwenden**.

## Verwandte Seiten

- [Das Administrationsdesign für Ihre Sitzung wählen](../../funktionen/6.0/ui-umschalter-design.md)
- [Sprache der Oberfläche und der UI-Umschalter](../../funktionen/6.0/ui-umschalter.md)
- [Der Umschalter für das Administrationsdesign: Einstellungen, Designs und Rollen](../../anleitungen/ui-umschalter-design.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
