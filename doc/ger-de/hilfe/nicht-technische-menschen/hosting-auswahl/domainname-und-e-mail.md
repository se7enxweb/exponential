---
title: Domainname und E-Mail
audience: non-technical, customer
category: plain-language
order: 45
summary: Wer Ihren Domainnamen und Ihre E-Mail bereitstellt - nicht Exponential, sondern eine Registrierungsstelle und ein Mailanbieter -, warum die Domain auf Ihren eigenen Namen laufen muss, was sich am DNS bei einem Umzug ändert und warum die eigenen E-Mails der Website (Passwort zurücksetzen, Formularbenachrichtigungen) funktionierende E-Mail brauchen.
keywords: Domainname, Registrierungsstelle, Registrar, DNS, E-Mail, Postfach, Mailanbieter, Eigentum, Verlängerung, ausgehende E-Mail, Passwort zurücksetzen, Formularbenachrichtigung, nicht technisch, domain name, registrar, e-mail, mailbox, mail provider, ownership, renewal, outgoing mail, password reset, form notification, non-technical
type: help
---
# Domainname und E-Mail

Exponential betreibt Ihre Website. Es registriert **nicht** Ihren Domainnamen und betreibt **nicht** Ihre Postfächer.
Diese kommen von anderen Anbietern und verdienen ebenso viel Sorgfalt wie die Website selbst.

## Inhalt

1. [Der Domainname](#der-domainname)
2. [Ihre E-Mail](#ihre-e-mail)
3. [Die E-Mails, die die Website sendet](#die-e-mails-die-die-website-sendet)
4. [Eine kleine Checkliste](#eine-kleine-checkliste)

## Der Domainname

Ihr Domainname (`example.org`) wird jährlich bei einer **Registrierungsstelle** gemietet. Eine Einstellung dort, das
**DNS**, lässt den Namen auf den Server Ihres Hosters zeigen
([Hosting, Domains und HTTPS erklärt](../begriffe/hosting-domains-und-https-erklaert.md)).

Drei Regeln:

1. **Registrieren Sie ihn auf den Namen Ihrer Organisation**, mit einer E-Mail-Adresse, die Sie auch in fünf Jahren
   noch lesen (nicht die persönliche Adresse von jemandem, der vielleicht geht).
2. **Wissen Sie, wer die Zugangsdaten** zur Registrierungsstelle hat, und tragen Sie es in
   [Wen Sie anrufen](../wenn-es-hakt/wen-sie-anrufen.md) ein.
3. **Achten Sie auf das Verlängerungsdatum.** Läuft die Domain aus, funktionieren Website und E-Mail nicht mehr, und
   jemand anderes kann den Namen kaufen. Schalten Sie die automatische Verlängerung ein, wo es möglich ist.

Wenn Sie zu einem neuen Hoster wechseln, ändert sich nur die DNS-Einstellung; die Domain bleibt Ihre.

## Ihre E-Mail

Postfächer wie `info@example.org` betreibt Ihr Hoster oder ein eigener Mailanbieter. Die DNS-Einstellungen bei der
Registrierungsstelle entscheiden, wohin die Mail geht. Mit dem Lesen oder Senden Ihrer alltäglichen E-Mail hat
Exponential nichts zu tun.

Wenn Sie den Hoster wechseln, sorgen Sie dafür, dass auch die Mail-Einstellungen mitziehen, sonst kann tagelang Mail
verloren gehen. Nennen Sie das ausdrücklich, wenn Sie einen Umzug planen ([Zu einem neuen Hoster wechseln](zu-einem-neuen-hoster-wechseln.md)).

## Die E-Mails, die die Website sendet

Exponential selbst sendet E-Mails: Links zum Zurücksetzen des Passworts, Codes der zweistufigen Anmeldung per E-Mail,
Benachrichtigungen an Menschen, die einem Teil der Website folgen, Formularbenachrichtigungen, Bestellbestätigungen
im Shop und Newsletter, wo diese im Einsatz sind. Dafür braucht der Server funktionierende **ausgehende E-Mail**, die
der Hoster einrichtet ([Einen Hoster wählen](../../kunden/partner-und-hoster/einen-hoster-auswaehlen.md#was-die-website-braucht)).

Sagen Menschen "Die Mail zum Zurücksetzen des Passworts kommt nie an", sehen Sie zuerst im Spamordner nach und fragen
dann Ihren Hoster, ob die Website Mail senden kann ([Passwort vergessen?](../../anmelden-im-editor.md#passwort-vergessen)).

## Eine kleine Checkliste

- [ ] Die Domain ist auf den Namen unserer Organisation registriert.
- [ ] Wir kennen die Registrierungsstelle, die Person mit den Zugangsdaten und das Verlängerungsdatum.
- [ ] Die automatische Verlängerung ist eingeschaltet, mit einem Zahlungsmittel, das nicht bald abläuft.
- [ ] Wir wissen, wer unsere Postfächer betreibt.
- [ ] Eine Test-Mail zum Zurücksetzen des Passworts von der Website kommt an.

## Verwandte Seiten

- [Hosting-Auswahl](README.md)
- [Hosting, Domains und HTTPS erklärt](../begriffe/hosting-domains-und-https-erklaert.md)
- [Online gehen: Hosts und HTTPS](../../ersteller/website-ersteller/livegang-hosts-und-https.md), für Website-Ersteller
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
