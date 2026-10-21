---
title: Wie versende ich einen Newsletter?
audience: editor, admin
category: manual
order: 405
summary: Legen Sie in der Liste des Newsletters eine Ausgabe an, füllen und veröffentlichen Sie sie, prüfen Sie die Vorschau und eine Testmail, klicken Sie dann auf Send Newsletter und bestätigen Sie; der Cronjob der Mail-Warteschlange verschickt sie.
keywords: Newsletter, Ausgabe, Newsletter versenden, cjw_newsletter, Vorschau, Testmail, Abonnenten, Mailing, newsletter, edition, send newsletter, preview, test mail, subscribers, mailing
type: help
---
# Wie versende ich einen Newsletter?

Eine Newsletter-Ausgabe ist ein Inhalt wie ein Artikel. Sie schreiben sie, prüfen sie und schicken sie dann an die
Abonnenten ihrer Liste.

## Wer das tun kann

Benutzer, die in der Newsletter-Liste Inhalte anlegen dürfen und deren Rolle die Newsletter-Richtlinien hat (in einer
neuen Installation die Erweiterung cjw_newsletter). Wo Ausgaben eine Freigabe brauchen, gibt jemand mit
`newsletter/approve` sie zuerst frei.

## Schritte

1. Klicken Sie im oberen Menü auf **Newsletter** (oder **Mehr** > **Newsletter**).
   Das **Newsletter dashboard** öffnet sich.
2. Öffnen Sie die Newsletter-Liste und klicken Sie auf **Create newsletter here**.
3. Füllen Sie die Ausgabe aus: Betreff, Texte, die Artikel, die sie zeigt. Klicken Sie auf **Zur Veröffentlichung
   schicken**.
4. Öffnen Sie die Ausgabe und prüfen Sie die **Newsletter edition preview** jedes Ausgabeformats.
5. Schicken Sie einen Test an Ihre eigene Adresse und lesen Sie ihn in Ihrem Mailprogramm. Prüfen Sie die Links und den
   Abmeldelink.
6. Klicken Sie auf **Send Newsletter**, wählen Sie den Zeitpunkt und bestätigen Sie „Do you really want to send out this
   newsletter?“.
   Der **Edition State** ändert sich, während die Warteschlange verschickt wird.

## Was schiefgehen kann

- **Achtung: Ein verschickter Newsletter lässt sich nicht zurückholen.** Prüfen Sie zuerst die Testmail. Um einen
  Versand anzuhalten, der noch läuft, verwenden Sie **Abort newsletter send out process**.
- **Die Testmail kommt nicht an oder landet im Spam.** Die Absenderdomain ist beim Mailanbieter nicht eingerichtet.
  Fragen Sie einen Administrator.
- **Es wird nichts verschickt.** Der Cronjob der Mail-Warteschlange läuft nicht. Das Dashboard zeigt wartende Mails.

## Verwandte Seiten

- [Newsletter sicher versenden](../../../eng-us/help/creators/website-creators/sending-newsletters.md)
- [Newsletter](../kunden/wachstum/newsletter.md)
- [Newsletter-Routine](../kunden/wachstum/newsletter-routine.md)
- [cjw_newsletter](../../../eng-us/features/6.0/extensions/cjw_newsletter.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
