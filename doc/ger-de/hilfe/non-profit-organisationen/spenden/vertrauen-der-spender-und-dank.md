---
title: Vertrauen der Spender und Dank
audience: non-profit, businesses, editor, admin
category: non-profits
order: 60
summary: Was Spender der Exponential-Website einer Non-Profit-Organisation vertrauen und wiederkommen lässt - HTTPS überall, eine Datenschutzerklärung, die jeden Auftragsverarbeiter nennt, geschützte Administratorkonten, eine Dankesseite, ehrliche Quittungen (die Bestellquittung des Shops ist keine Steuerbescheinigung), Nachbereitung per Newsletter nur mit Einwilligung und ein sorgfältiger Umgang mit Spenderdaten.
keywords: Vertrauen der Spender, Datenschutzerklärung, Zwei-Faktor-Anmeldung, Dankesseite, Quittungen, Spendenbescheinigung, Steuerbescheinigung, Nachbereitung, Einwilligung in den Newsletter, Spenderdaten, Spenderpflege, donor trust, HTTPS, privacy notice, two-factor login, thank-you page, receipts, tax receipt, follow-up, newsletter consent, donor data, stewardship
type: help
---
# Vertrauen der Spender und Dank

Diese Seite richtet sich an Fundraiser und an alle, die die Website betreuen. Ein Spender entscheidet in Sekunden, ob
eine Website sicher wirkt, und über Monate, ob er erneut spendet. Beides hängt von Dingen ab, die Sie in der Hand haben.

## Inhalt

1. [Vor der Spende: Zeichen einer sicheren Website](#vor-der-spende-zeichen-einer-sicheren-website)
2. [Hinter den Kulissen: die Website schützen](#hinter-den-kulissen-die-website-schützen)
3. [Direkt nach der Spende: die Dankesseite](#direkt-nach-der-spende-die-dankesseite)
4. [Quittungen](#quittungen)
5. [Danach: Nachbereitung mit Einwilligung](#danach-nachbereitung-mit-einwilligung)
6. [Spenderdaten](#spenderdaten)
7. [Checkliste für Vertrauen](#checkliste-für-vertrauen)

## Vor der Spende: Zeichen einer sicheren Website

| Zeichen | Wie | Quelle |
|---|---|---|
| HTTPS auf jeder Seite | Zertifikat auf dem Host; Weiterleitung von HTTP | [Live gehen: Hosts und HTTPS](../../ersteller/website-ersteller/livegang-hosts-und-https.md) |
| Ein echter Kontakt | Adresse, Telefon oder Formular in der Fußzeile jeder Seite | [Gemeinsame Kopf- und Fußzeile](../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md) |
| Eine aktuelle Website | Nachrichten und Veranstaltungen aus den letzten Monaten, keine defekten Links | [Link- und Inhaltsprüfungen](../../ersteller/website-ersteller/link-und-inhaltspruefungen.md) |
| Offenheit beim Geld | Jahresveröffentlichungen und Finanzdokumente als Downloads | [Transparenz](../transparenz/README.md) |
| Eine Datenschutzerklärung | Nennt jeden Auftragsverarbeiter (Zahlungsanbieter, Newsletter, CAPTCHA, Analyse) | [Gliederung einer Datenschutzerklärung](../../verwaltung/datenschutz/gliederung-einer-datenschutzerklaerung.md) |

## Hinter den Kulissen: die Website schützen

Eine verunstaltete Spendenseite oder ein gekaperter Spenden-Link kostet mehr Vertrauen, als eine Kampagne zurückgewinnen
kann.

- Wenige Administratorkonten, jedes mit Zwei-Faktor-Anmeldung ([Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- Updates rechtzeitig eingespielt ([Routine für Sicherungen und Updates](../betrieb/routine-fuer-sicherungen-und-updates.md)).
- Nur vertrauenswürdige Personen dürfen die Spendenseite bearbeiten; schränken Sie Rollen nach Bereich ein
  ([Ehrenamtliche als Redakteure](../ehrenamtliche/ehrenamtliche-als-redakteure.md)).
- Das Audit-Protokoll zeigt, wer die Seite wann geändert hat ([Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md)).
  Ändert sich der Spenden-Link einmal unerwartet, sehen Sie zuerst dort nach.
- Prüfen Sie den Spenden-Link monatlich ([Monatliche Checkliste](../checklisten/monatliche-checkliste.md)).

## Direkt nach der Spende: die Dankesseite

Eine Seite auf Ihrer eigenen Website, nicht im Menü, auf die die Zahlungsplattform die Spender zurückschickt:

- [ ] Dank in einem Satz, herzlich und konkret.
- [ ] Was als Nächstes geschieht: eine Bestätigungs-E-Mail des Anbieters, eine Quittung, wann.
- [ ] Ein nächster Schritt: Ihre Arbeit verfolgen, die Kampagne teilen oder ehrenamtlich mitmachen. Nur einer.
- [ ] Ein Kontakt, falls etwas schiefgegangen ist.

Beim Weg über den Shop übernehmen die Bestellansicht und ihre Quittung diese Rolle
([Bestellquittungen](../../../funktionen/6.0/bestellbelege.md)).

## Quittungen

| Weg | Was der Spender von der Website oder dem Anbieter erhält |
|---|---|
| Externe Plattform | Was die Plattform ausstellt |
| Shop (`ezpaypal`, `ezauthorize`) | Eine Bestellquittung zum Drucken und Herunterladen; „kein Rechnungssystem“ ([Rechnungen und Quittungen](../../kunden/handel/rechnungen-und-belege.md)) |

Bei keinem der beiden Wege stellt Exponential eine **Steuer- oder Spendenbescheinigung** aus; eine solche Funktion
gibt es nicht. Wenn Ihr Recht eine verlangt, stellen Sie sie aus Ihrer Buchhaltung heraus aus. Fragen Sie Ihre
Steuerberatung.

## Danach: Nachbereitung mit Einwilligung

- Tragen Sie Spender **nur** dann in den Newsletter ein, wenn sie sich selbst angemeldet haben; `cjw_newsletter`
  verwendet Double-Opt-in ([Unterstützer-Newsletter](../kommunikation/unterstuetzer-newsletter-mit-cjw-newsletter.md),
  [Opt-in und Einwilligung](../../unternehmen/newsletters/opt-in-und-einwilligung.md)).
- Eine Spende ist keine Einwilligung in Werbe-E-Mails. Bieten Sie den Newsletter stattdessen auf der Dankesseite an.
- Berichten Sie zurück: was das Geld bewirkt hat, in einer Nachricht oder in der Jahresveröffentlichung.

## Spenderdaten

Spenderdaten liegen beim Zahlungsanbieter und, beim Weg über den Shop, in Bestellungen auf Ihrer Website. Lesen Sie
für Anfragen von Spendern [Datenschutz für Spender und Ehrenamtliche](../betrieb/datenschutz-fuer-spender-und-ehrenamtliche.md)
und [Datenexport und Löschung](../../kunden/handel/datenexport-und-loeschung.md).

> **Hinweis**
> Grundlagen, keine Rechtsberatung.

## Checkliste für Vertrauen

- [ ] HTTPS überall, getestet.
- [ ] Kontaktangaben und Datenschutzerklärung auf jeder Seite.
- [ ] Zwei-Faktor-Anmeldung für jeden Administrator.
- [ ] Spendenseite nur von benannten Personen bearbeitbar.
- [ ] Dankesseite live und als Rücksprungadresse eingetragen.
- [ ] Quittungen mit der Steuerberatung abgestimmt.
- [ ] Newsletter angeboten, nie aufgedrängt.

## Verwandte Seiten

- [Spenden](README.md)
- [Die Spendenseite](die-spendenseite.md)
- [Sicherheitsgrundlagen für Unternehmen](../../unternehmen/sicherheit/sicherheitsgrundlagen-fuer-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
