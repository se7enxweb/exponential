---
title: "FAQ: Formulare und Newsletter"
audience: government, editor, admin, project-manager
category: public-sector
order: 170
summary: Antworten auf die Fragen öffentlicher Stellen zu Formularen und Newslettern in Exponential - Kontakt- und Feedbackformulare, wohin Einsendungen gehen, Spamschutz, Einwilligung, Newsletter-Abonnements mit Double-Opt-in, Abmelden, Statistiken und Aufbewahrung.
keywords: FAQ Formulare, Kontaktformular, Feedback, Informationssammlung, Einsendungen, Spam, CAPTCHA, Newsletter, cjw_newsletter, Double-Opt-in, Abmelden, Einwilligung, Aufbewahrung, öffentlicher Sektor, forms FAQ, contact form, information collection, submissions, double opt-in, unsubscribe, consent, retention, public sector
type: help
---
# FAQ: Formulare und Newsletter

Diese Seite beantwortet die Fragen öffentlicher Stellen dazu, wie sie mit Exponential Informationen von Bürgerinnen und
Bürgern erheben und Newsletter versenden. Die Antworten stammen aus
[Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md),
[Newsletter mit cjw_newsletter](../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md) und
[E-Mail-Einstellungen](../../../funktionen/6.0/e-mail-einstellungen.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Können wir Formulare auf die Website stellen?](#können-wir-formulare-auf-die-website-stellen)
2. [Wohin gehen die Einsendungen?](#wohin-gehen-die-einsendungen)
3. [Wie sind Formulare gegen Spam geschützt?](#wie-sind-formulare-gegen-spam-geschützt)
4. [Wie lange werden Einsendungen aufbewahrt?](#wie-lange-werden-einsendungen-aufbewahrt)
5. [Können wir Newsletter versenden?](#können-wir-newsletter-versenden)
6. [Wie melden sich Menschen ab?](#wie-melden-sich-menschen-ab)
7. [Werden Öffnungen und Klicks erfasst?](#werden-öffnungen-und-klicks-erfasst)

## Können wir Formulare auf die Website stellen?

Ja. Die Informationssammlung macht aus Inhaltsklassen Formulare, Umfragen und Abstimmungen; die Redaktion platziert sie
als Inhalt ([Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md),
[Ein Kontaktformular bauen](../../ersteller/website-ersteller/ein-kontaktformular-bauen.md)).

## Wohin gehen die Einsendungen?

Sie werden unter **Setup > Gesammelte Informationen** gespeichert und aufgelistet
([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)). Sie lassen sich mit
`./console ext:bccie:export` als CSV exportieren
([Von Exponential wegmigrieren](../../../installation/migration/exponential-verlassen.md#1-was-sie-exportieren-sollten)).

## Wie sind Formulare gegen Spam geschützt?

Jedes Formular trägt ein CSRF-Token. Gegen Bots gibt es die Erweiterungen recaptcha und hcaptcha; sie sind Dienste von
Drittanbietern, die in Ihrer Datenschutzerklärung erwähnt werden müssen und eine barrierefreie Alternative brauchen
([Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md)).

## Wie lange werden Einsendungen aufbewahrt?

Bis jemand sie entfernt. Die Website muss einen Einwilligungstext und eine Aufbewahrungsregel selbst hinzufügen
([Datenschutzstandards](../../../standards/datenschutz.md#bekannte-lücken),
[Formulareinsendungen](../../ersteller/website-ersteller/formulareinsendungen.md)).

## Können wir Newsletter versenden?

Ja, mit `cjw_newsletter`: Ausgaben als Inhalt geschrieben, Listen mit Double-Opt-in, Versand per Cron im Hintergrund,
Bearbeitung von Unzustellbarkeiten und eine Sperrliste sowie ein Archiv jedes Versands
([cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md)). Für Redaktionen und Administratoren gibt es einen
Kurs ([Kurs Newsletter](../../trainer/newsletter/README.md)).

## Wie melden sich Menschen ab?

Optionale E-Mails, die das E-Mail-Gate von Exponential passieren, tragen einen Header `List-Unsubscribe` mit Abmeldung
per Klick und einen Link in der Fußzeile, die ohne Anmeldung funktionieren
([Datenschutzstandards](../../../standards/datenschutz.md#can-spam-act-und-abmeldung-mit-einem-klick)). Menschen verwalten ihre
Kategorien auf der Seite der E-Mail-Einstellungen ([E-Mail-Einstellungen](../../../funktionen/6.0/e-mail-einstellungen.md)).

## Werden Öffnungen und Klicks erfasst?

Die Newsletter-Erweiterung kann Öffnungen und Klicks pro Person auf Ihrem eigenen Server zählen; das erfordert die
vorherige Einwilligung der Person in die Kategorie Newsletter-Statistik
([Datenschutzstandards](../../../standards/datenschutz.md#drittanbieterdienste-in-mitgelieferten-extensions)).

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Checkliste Datenschutz](../checklisten/checkliste-datenschutz.md)
- [E-Mail-Einwilligung und Unzustellbarkeiten (für Hoster)](../../hosting/betrieb/einwilligung-und-bounces-bei-mails.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
