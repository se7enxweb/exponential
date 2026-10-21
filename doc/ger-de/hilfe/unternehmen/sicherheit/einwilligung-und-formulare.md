---
title: Einwilligung und Formulare
audience: businesses, admin, sales-marketing, website-creator
category: business-solutions
order: 40
summary: Wie Exponential mit der Einwilligung für E-Mails umgeht (notwendige und optionale Mails, Double-Opt-in, Abmeldung mit einem Klick, das Einwilligungsprotokoll und die Sperrliste), was Newsletter hinzufügen und wie Sie Kontakt- und Lead-Formulare betreiben, die nur erheben, was Sie brauchen. Keine Rechtsberatung.
keywords: Einwilligung, Opt-in, Double-Opt-in, Abmeldung, E-Mail-Einstellungen, Mail-Schranke, Einwilligungsprotokoll, Sperrliste, Newsletter, Formulare, Informationssammler, Datenschutzhinweis, Formular-Token, Werbemails, consent, opt-in, double opt-in, unsubscribe, mail preferences, mail gate, consent log, suppression list, newsletter, forms, information collector, privacy notice, CAPTCHA, form token, marketing mail
type: help
---
# Einwilligung und Formulare

Diese Seite richtet sich an Inhaber, Mitarbeiter im Marketing und Administratoren, die Kunden Mails senden oder über
Formulare Daten erheben. Sie erklärt, was Exponential für die Einwilligung tut, was jeder Teil für Ihr Unternehmen
bedeutet und was Sie selbst einrichten oder schreiben müssen.

> **Warnung**
> Grundlagen, keine Rechtsberatung. Die Regeln zur Einwilligung unterscheiden sich nach Land und Art der Mail. Lesen Sie
> die Gesetze, die für Sie gelten (das Projekt ordnet mehrere davon in
> [E-Mail-Einstellungen: die Checkliste zu den Gesetzen](../../../spezifikationen/6.0/mail-einstellungen-compliance.md) ein),
> und fragen Sie einen Anwalt oder Ihren Datenschutzbeauftragten.

## Inhalt

1. [Die Mail-Schranke in einem Absatz](#die-mail-schranke-in-einem-absatz)
2. [Notwendige und optionale Mails](#notwendige-und-optionale-mails)
3. [Double-Opt-in, Abmeldung und das Einwilligungsprotokoll](#double-opt-in-abmeldung-und-das-einwilligungsprotokoll)
4. [Newsletter](#newsletter)
5. [Formulare, die Daten erheben](#formulare-die-daten-erheben)
6. [Was Sie selbst einrichten müssen](#was-sie-selbst-einrichten-müssen)
7. [Checkliste](#checkliste)

## Die Mail-Schranke in einem Absatz

Seit 6.0.15 durchläuft jede Mail, die die Installation sendet, eine Mail-Schranke, gleich welcher Transport verwendet
wird. Optionale Mails gehen nur an Menschen, die sie eingeschaltet haben, eine Mail pro Empfänger, mit einer Fußzeile und
One-Click-Headern zum Abmelden (RFC 8058). Notwendige Mails gehen immer hinaus. Jede Änderung einer Einstellung wird mit
dem genauen Text, den die Person gesehen hat, in ein Einwilligungsprotokoll geschrieben
([E-Mail-Einstellungen: Leitfaden für Administratoren](../../../anleitungen/mail-einstellungen-administrator.md#1-was-das-system-tut-in-einem-absatz)).

## Notwendige und optionale Mails

| Art | Beispiele | Geht an |
|---|---|---|
| Notwendig (`security`, `orders`, `legal`, `admin`) | Passwort geändert, Bestellbestätigung | immer |
| Optional | Newsletter, Angebote, Neuigkeiten | nur Menschen, die die Kategorie eingeschaltet haben; aus, bis sie es tun |

Quelle: [Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md#einwilligung-abmeldung-und-unterdrückung-bei-mails).
Optionale Kategorien können nicht standardmäßig eingeschaltet sein, es sei denn, ein Administrator erlaubt es
ausdrücklich (`[CategorySettings] AllowDefaultOn`), und das Anmeldeformular zeigt jedes Kästchen nicht angekreuzt
([Die Checkliste zu den Gesetzen](../../../spezifikationen/6.0/mail-einstellungen-compliance.md#dsgvo-und-eprivacy-richtlinie-europäische-union)).

## Double-Opt-in, Abmeldung und das Einwilligungsprotokoll

- **Double-Opt-in** für Newsletter, Angebote und Neuigkeiten sowie für eine neue E-Mail-Adresse: Aus der Kategorie wird
  nichts gesendet, bis die Person bestätigt.
- **Einen Link zu öffnen ist keine Einwilligung**: Die Seiten zum Bestätigen und Abmelden ändern nichts, bis ihre
  Schaltfläche gedrückt wird.
- **Der Widerruf ist so einfach wie die Einwilligung**: Dasselbe Kästchen schaltet eine Kategorie aus, „Alle optionalen
  E-Mails ausschalten“ ist eine Schaltfläche, jede optionale Mail hat einen Link für die Abmeldung mit einem Klick.
- **Einwilligungsprotokoll**: Zeit, Kategorie, alter und neuer Zustand, Quelle, der genau angezeigte Text, IP-Adresse,
  handelnder Benutzer.
- **Sperrliste**: Hard Bounces, Beschwerden, „alle stoppen“ und rechtliche Anfragen; von jeder Adresse wird nur ein
  gesalzener Hash gespeichert.
- **Die eigene Kopie der Person**: „Meine E-Mail-Daten herunterladen“ als JSON und CSV.

Alles aus [Die Checkliste zu den Gesetzen](../../../spezifikationen/6.0/mail-einstellungen-compliance.md). Prüfen Sie den
Zustand Ihrer Website mit `php bin/php/console exp:mail:status`.

## Newsletter

Die Newsletter-Erweiterung `cjw_newsletter`, die von `composer.json` verlangt wird, lässt Besucher Listen „mit einem
Double-Opt-in“ abonnieren und bietet Seiten zum Abonnieren, Bestätigen, Ändern und Abmelden
([cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md)). Öffnungen und Klicks werden nur gezählt, wenn die
Website das Tracking einschaltet (standardmäßig `[TrackingSettings] Tracking=disabled`), und pro Person nur mit
Einwilligung ([Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md#was-bei-hoster-und-kunde-bleibt)).
Die geschäftliche Seite steht in [Newsletter: Opt-in und Einwilligung](../newsletter/opt-in-und-einwilligung.md).

## Formulare, die Daten erheben

Kontakt-, Feedback- und Lead-Formulare verwenden den Informationssammler: Jedes Absenden wird als Sammlung gespeichert
([Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md)).

| Gute Praxis | Wie in Exponential |
|---|---|
| Nur erheben, was Sie brauchen | Markieren Sie nur die notwendigen Klassenattribute als Informationssammler |
| Sagen, was mit den Daten geschieht | Setzen Sie im Template einen kurzen Hinweis und einen Link zu Ihrem Datenschutzhinweis neben das Formular |
| Zustimmung festhalten, wo Sie sie brauchen | Ein Kontrollkästchen-Attribut (`ezboolean`) kann Informationen sammeln, sodass ein angekreuztes Kästchen mit dem Absenden gespeichert wird |
| Beschränken, wer Eingaben liest | Geben Sie `infocollector/read` nur den Menschen, die sie bearbeiten |
| Entfernen, was Sie nicht mehr brauchen | **Setup > Gesammelte Informationen** entfernt Sammlungen nach einer Bestätigung ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)) |
| Spam und gefälschte Absendungen stoppen | Formular-Tokens (`ezformtoken`) und reCAPTCHA oder hCaptcha ([Spam-Schutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md)) |

> **Hinweis**
> Ein Kontrollkästchen in einem Formular wird als ein Wert dieses Absendens gespeichert. Es wird nicht in das
> Einwilligungsprotokoll für Mails eingetragen und schaltet keine Mail-Kategorie ein. Soll ein Formular jemanden für
> Mails anmelden, verwenden Sie die E-Mail-Einstellung oder die Newsletter-Anmeldung, die ihr eigenes Double-Opt-in
> haben.

Ein CAPTCHA-Dienst ist ein Dritter, der Daten aus den Browsern Ihrer Besucher erhält; nennen Sie ihn in Ihrem
Datenschutzhinweis.

## Was Sie selbst einrichten müssen

Aus [Der Website überlassen](../../../spezifikationen/6.0/mail-einstellungen-compliance.md#der-site-überlassen):

1. Die Postanschrift in der Fußzeile der Mails (`mailpreferences.ini [FooterSettings] OrganisationAddress`, nach einer
   Installation leer) und den Namen des Absenders (`OrganisationName`).
2. Eine Kategorie für jede kommerzielle Mail, die eigener Code sendet.
3. `[GateSettings] SplitRecipients=enabled` beibehalten.
4. DKIM-Signierung der Abmelde-Header durch den Mail-Server, oft Aufgabe Ihres Hosters
   ([Mail-Einwilligung und Bounces](../../hosting/betrieb/einwilligung-und-bounces-bei-mails.md)).
5. Einen Link zum Datenschutzhinweis auf dem Registrierungsformular, wenn Ihr Design einen vorsieht.

## Checkliste

- [ ] Die Fußzeile der Mails ist ausgefüllt; `exp:mail:status` meldet keine Probleme.
- [ ] Jede Werbemail hat eine optionale Kategorie.
- [ ] Die Newsletter-Anmeldung verwendet Double-Opt-in; das Tracking bleibt aus, sofern Ihr Hinweis es nicht abdeckt.
- [ ] Jedes Formular fragt nur, was es braucht, und verlinkt den Datenschutzhinweis.
- [ ] `infocollector/read` ist wenigen Menschen gegeben; alte Sammlungen werden nach Plan entfernt.
- [ ] CAPTCHA und andere Dienste Dritter sind im Datenschutzhinweis genannt.
- [ ] Rechtliche Fragen sind mit Ihrem Berater geklärt.

## Verwandte Seiten

- [DSGVO-Grundlagen](dsgvo-grundlagen.md)
- [Personenbezogene Daten in Exponential](personenbezogene-daten-in-exponential.md)
- [Ein Kontaktformular bauen](../../ersteller/website-ersteller/ein-kontaktformular-bauen.md)
- [Listen und Abonnements](../newsletter/listen-und-abonnements.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
