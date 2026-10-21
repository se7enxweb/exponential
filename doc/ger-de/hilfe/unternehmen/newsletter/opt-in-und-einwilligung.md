---
title: Opt-in und Einwilligung
audience: businesses, sales-marketing, admin
category: business-solutions
order: 40
summary: Wie Exponential und cjw_newsletter mit der Einwilligung für Newsletter umgehen - Double-Opt-in, die zentralen E-Mail-Einstellungen, Abmeldung mit einem Klick, das Einwilligungsprotokoll, Importe mit einer Einwilligungsquelle, Statistiken und SMS nur mit Einwilligung. Grundlagen, keine Rechtsberatung.
keywords: Double-Opt-in, Einwilligung, DSGVO, Abmeldung, Abmeldung mit einem Klick, Einwilligungsprotokoll, E-Mail-Einstellungen, Sperrliste, Einwilligung für Newsletter-Statistik, Datenschutz, double opt-in, consent, GDPR, unsubscribe, one-click unsubscribe, consent log, e-mail preferences, suppression list, privacy
type: help
---
# Opt-in und Einwilligung

Diese Seite richtet sich an Inhaber, Führungskräfte und Ansprechpartner für den Datenschutz, die wissen müssen, wie ein
Newsletter auf Exponential die Einwilligung einholt, festhält und beachtet. Sie beschreibt, was die Software tut, mit
einer Quelle für jeden Punkt, damit Sie mit Ihren Beratern entscheiden können, ob das Ihren Pflichten genügt.

> **Hinweis**
> Dies sind Grundlagen, keine Rechtsberatung. Lesen Sie die Vorschriften, die für Sie gelten (zum Beispiel die
> Datenschutz-Grundverordnung der EU und Ihre nationalen Regeln zur elektronischen Werbung), und fragen Sie Ihren Anwalt
> oder Datenschutzbeauftragten.

## Inhalt

1. [Double-Opt-in für öffentliche Anmeldungen](#double-opt-in-für-öffentliche-anmeldungen)
2. [Die E-Mail-Einstellungen der Website](#die-e-mail-einstellungen-der-website)
3. [Abmelden mit einem Klick](#abmelden-mit-einem-klick)
4. [Das Einwilligungsprotokoll](#das-einwilligungsprotokoll)
5. [Importierte Abonnenten](#importierte-abonnenten)
6. [Statistiken nur mit Einwilligung](#statistiken-nur-mit-einwilligung)
7. [Einwilligung für SMS](#einwilligung-für-sms)
8. [Löschung und Anfragen zu Daten](#löschung-und-anfragen-zu-daten)
9. [Checkliste Einwilligung](#checkliste-einwilligung)

## Double-Opt-in für öffentliche Anmeldungen

Ein Besucher, der auf der Website abonniert, bekommt eine Bestätigungsmail und wird erst gezählt, wenn er auf ihren
Link klickt („der Abonnent bestätigt per Mail (Double-Opt-in), bevor das Abonnement zählt“,
[Newsletter mit cjw_newsletter](../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md#abonnementformulare-auf-der-website)).
Bis dahin ist das Abonnement **wartend** und erhält nichts.

Das Abonnieren per E-Mail (Release 4.2.0, standardmäßig aus mit `[MailInSettings] MailIn=disabled`) folgt derselben
Regel: „Eine Anmeldemail startet nur ein wartendes Abonnement: Die Bestätigungsmail geht an den Absender, der ihren Link
öffnen muss (Double-Opt-in)“ (Kommentar in der mitgelieferten `cjw_newsletter.ini`). Der From-Header allein ändert nie
etwas.

## Die E-Mail-Einstellungen der Website

Seit Exponential 6.0.15 durchläuft jede Mail, die die Installation sendet, eine **Mail-Schranke**. Eine Mail, die eine
Kategorie angibt, wird mit den Einstellungen des Empfängers, der Sperrliste und einem wartenden Double-Opt-in
abgeglichen; optionale Mails gehen nur an Menschen, die sie eingeschaltet haben, mit einer Fußzeile, die sagt, warum
die Person sie bekommt und wie sie sich abmeldet
([der Leitfaden für Administratoren](../../../anleitungen/mail-einstellungen-administrator.md#1-was-das-system-tut-in-einem-absatz)).
Release 4.2.0 des Newsletters „arbeitet mit den E-Mail-Einstellungen von 6.0.15, statt ein eigenes System zu führen“
([cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#die-funktionen-von-420)).

Was ein Abonnent sieht ([E-Mail-Einstellungen: der Leitfaden für Benutzer](../../../funktionen/6.0/e-mail-einstellungen.md)):

- optionale E-Mails sind „aus, bis Sie sie einschalten“;
- eine Seite listet jede Art von E-Mail mit einem eigenen Schalter auf, dazu einen Hauptschalter für alle optionalen
  Mails;
- das Registrierungsformular bietet pro optionaler Art ein nicht angekreuztes Kästchen: „nichts wird für Sie
  angekreuzt“;
- die Entscheidungen und jede Änderung daran lassen sich als JSON oder CSV herunterladen.

## Abmelden mit einem Klick

Jede optionale E-Mail endet mit dem Grund, warum die Person sie bekommen hat, einem Link **Abmelden** und einem Link zu
ihrer Einstellungsseite; für beides ist keine Anmeldung nötig. Das bloße Öffnen des Links ändert nichts, sodass ein
Virenscanner, der Links öffnet, niemanden abmelden kann; die Person drückt eine Schaltfläche. Mail-Programme, die ihre
eigene Schaltfläche zum Abmelden zeigen, verwenden denselben Link
([Von einer E-Mail abmelden](../../../funktionen/6.0/e-mail-einstellungen.md#eine-e-mail-abbestellen)). Der Leitfaden für
Administratoren nennt die One-Click-Header nach RFC 8058.

Auch die Skins der Erweiterung drucken einen Abmeldelink: Die Checkliste für Website-Ersteller verlangt „einen
Abmeldelink in jeder Ausgabe (die mitgelieferten Skins `news` und `company` drucken einen)“
([Newsletter-Listen und Abonnements](../../ersteller/website-ersteller/newsletter-listen-und-abonnements.md#einwilligung)).

## Das Einwilligungsprotokoll

Setup > E-Mail-Einstellungen > **Einwilligungsprotokoll** listet jede Änderung einer Einstellung auf, die neueste
zuerst, „mit der Person, der Kategorie, der Änderung, der Quelle, dem angezeigten Text und der IP-Adresse“. Es lässt sich
filtern und mit der Richtlinie `mailpreferences/export` als CSV exportieren
([Das Einwilligungsprotokoll](../../../anleitungen/mail-einstellungen-administrator.md#8-das-einwilligungsprotokoll)). Das ist der Nachweis,
den Sie vorlegen, wenn jemand fragt, wann und wie er zugestimmt hat.

Aufbewahrung: Der Cronjob-Teil `mailpreferences` entfernt die Protokollzeilen von Menschen, die nicht mehr da sind,
sobald sie älter als `[ConsentSettings] RetentionDays` sind (standardmäßig 1095 Tage), und entfernt Bestätigungen, die
länger als `[ConsentSettings] PendingDays` (7) gewartet haben
([Aufbewahrung und der Cronjob](../../../anleitungen/mail-einstellungen-administrator.md#9-aufbewahrung-und-der-cronjob)).
Gleichen Sie diese Werte mit Ihren eigenen Aufbewahrungsregeln ab.

## Importierte Abonnenten

Ein Import hat keinen Bestätigungsklick, daher verlangt die Erweiterung eine **Einwilligungsquelle**: wo und wie die
Menschen zugestimmt haben. Sie ist für einen echten Import Pflicht, wird mit der Quelle `import` in das
Einwilligungsprotokoll geschrieben, und „Sie sind dafür verantwortlich, diese Einwilligung nachweisen zu können“
([importexport.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/importexport.md)).

> **Warnung**
> Importieren Sie nur Menschen, die dem Empfang dieses Newsletters zugestimmt haben, und bewahren Sie die Nachweise (das
> Formular, das Datum, den Wortlaut) auch außerhalb der Website auf. „Importieren Sie nur Menschen, die dem Empfang des
> Newsletters zugestimmt haben; halten Sie fest, woher die Einwilligung stammt“
> ([Newsletter-Listen und Abonnements](../../ersteller/website-ersteller/newsletter-listen-und-abonnements.md#importieren-und-exportieren)).

## Statistiken nur mit Einwilligung

„Pro Person wird nichts ohne die Einwilligung dieser Person gezählt, und die Einwilligung ist die optionale Kategorie
‚Newsletter-Statistik‘ der E-Mail-Einstellungen von Exponential (ab 6.0.15), standardmäßig aus“
([statistics.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/statistics.md)). Der Schalter der Website
`[TrackingSettings] Tracking` ist in den mitgelieferten Einstellungen ausgeschaltet, sodass nichts gezählt wird, bis Sie
ihn einschalten. Die Einwilligung wird bei jeder Öffnung und jedem Klick erneut geprüft; wer sie widerruft, wird von da
an anonym gezählt. Testversände und SMS werden nie getrackt.

## Einwilligung für SMS

Newsletter per SMS brauchen die Einwilligungskategorie „Newsletter per SMS“, bestätigt mit einem per SMS gesendeten
Code, und das Schlüsselwort STOP beendet sie ([sms.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/sms.md)).
SMS sind ausgeschaltet, bis sie in den Einstellungen eingeschaltet werden.

## Löschung und Anfragen zu Daten

- Eine Person kann ihren Verlauf auf ihrer Einstellungsseite sehen und herunterladen.
- Zum Löschen wendet sich eine Person an den Administrator: Die Einstellungen werden entfernt und der Verlauf wird
  anonymisiert; ein Vermerk, dass die Person widerrufen hat, bleibt ohne ihre Adresse erhalten, damit die Website
  nachweisen kann, dass sie nicht mehr schreibt ([Ihre Rechte](../../../funktionen/6.0/e-mail-einstellungen.md#ihre-rechte)).
- Hatte eine gelöschte Person Newsletter-Daten, entfernen die Handler der Kategorien ihren Newsletter-Benutzer, ihre
  Interessen, SMS-Daten und personenbezogenen Statistiken
  ([cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#die-funktionen-von-420)).

Wie Sie solche Anfragen Schritt für Schritt bearbeiten:
[Anfragen nach dem Datenschutzrecht](../../../anleitungen/mail-einstellungen-administrator.md#6-anfragen-nach-dem-datenschutzrecht).

## Checkliste Einwilligung

- [ ] Das Anmeldeformular sagt, was Abonnenten bekommen und wie oft, und verlinkt auf Ihren Datenschutzhinweis.
- [ ] Öffentliche Abonnements verwenden das Double-Opt-in; niemand wird „standardmäßig“ hinzugefügt.
- [ ] Jede Ausgabe trägt einen Abmeldelink; aus einem echten Postfach getestet.
- [ ] Importe nur mit einer schriftlichen Einwilligungsquelle und aufbewahrten Nachweisen.
- [ ] Das Tracking bleibt aus oder steht auf anonymen Summen, sofern Sie sich nicht für Statistiken pro Person mit
      Einwilligung entschieden haben.
- [ ] Die Aufbewahrungswerte (`RetentionDays`, `PendingDays`) sind mit Ihren Richtlinien abgeglichen.
- [ ] Eine namentlich benannte Person bearbeitet Anfragen auf Auskunft, Berichtigung und Löschung.
- [ ] Ihr Anwalt oder Datenschutzbeauftragter hat die Formulierungen geprüft.

## Verwandte Seiten

- [Newsletter für Unternehmen](README.md)
- [Listen und Abonnements](listen-und-abonnements.md)
- [DSGVO-Grundlagen](../sicherheit/dsgvo-grundlagen.md)
- [Einwilligung und Formulare](../sicherheit/einwilligung-und-formulare.md)
- [E-Mail-Einstellungen: der Leitfaden für Administratoren](../../../anleitungen/mail-einstellungen-administrator.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
