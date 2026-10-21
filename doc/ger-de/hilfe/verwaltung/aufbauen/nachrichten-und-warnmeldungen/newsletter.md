---
title: Newsletter
audience: government, website-creator, admin, project-manager
category: public-sector
order: 60
summary: Wie eine Behörde mit der Erweiterung cjw_newsletter in Exponential eine regelmäßige Nachrichtenzusammenfassung versendet - Listen, aus Nachrichtenartikeln gebaute Ausgaben, Double-Opt-in, Abmeldung mit einem Klick, Probe mit dem Datei-Transport - und wofür ein Newsletter nicht gedacht ist.
keywords: Newsletter, Nachrichtenzusammenfassung, Ausgabe, Liste, Double-Opt-in, anmelden, abmelden, Sperrliste, Rückläufer, Datei-Transport, Einwilligung, newsletter, news digest, cjw_newsletter, edition, list, double opt-in, subscribe, unsubscribe, List-Unsubscribe, blacklist, bounces, file transport, consent
type: help
---
# Newsletter

Diese Seite richtet sich an Site-Ersteller, Administratoren und Projektleiter, die eine regelmäßige
Nachrichtenzusammenfassung per E-Mail versenden. Die Erweiterung `cjw_newsletter`, die die Standardinstallation
aktiviert, bietet Listen, als Inhalte geschriebene Ausgaben, Double-Opt-in, zeitgesteuerten Versand, Behandlung von
Rückläufern und ein Archiv. Diese Seite behandelt ihre Verwendung für Nachrichten; die vollständigen Anleitungen zu
Newslettern sind [Newsletter mit cjw_newsletter](../../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md),
[Newsletter-Listen und Abonnements](../../../ersteller/website-ersteller/newsletter-listen-und-abonnements.md) und
[Newsletter versenden](../../../ersteller/website-ersteller/newsletter-versenden.md). Die Hilfe für Behörden hat im
Bereich Aufbauen eine eigene Anleitung zu Newslettern ([Newsletter](../newsletter/README.md)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Eine Nachrichtenzusammenfassung](#eine-nachrichtenzusammenfassung)
2. [Einwilligung und Abmeldung](#einwilligung-und-abmeldung)
3. [Proben vor echten Mails](#proben-vor-echten-mails)
4. [Wofür ein Newsletter nicht gedacht ist](#wofür-ein-newsletter-nicht-gedacht-ist)

## Eine Nachrichtenzusammenfassung

| Teil | In cjw_newsletter |
|---|---|
| Das Newsletter-System und seine Liste | Inhalte unter `[NewsletterSettings] RootFolderNodeId`; Besucher abonnieren die Liste |
| Eine Ausgabe | Inhalt (`cjw_newsletter_edition`) mit Artikeln (`cjw_newsletter_article`); Entwürfe und Vorschau wie bei allen Inhalten |
| Nachrichten wiederverwenden | der Artikelpool (**Newsletter**, Liste des Artikelpools), um vorhandene Inhalte für eine Ausgabe auszuwählen |
| Versand | `newsletter/send` plant; die Cron-Teile `cjw_newsletter` und `cjw_newsletter_mailbox` bauen, senden und behandeln Rückläufer |
| Die Administration | **Newsletter** (`/newsletter/index`) |

Eine monatliche Zusammenfassung von Nachrichten, Beteiligungsverfahren und Veranstaltungen funktioniert für Behörden
gut; sie ergänzt RSS für Menschen, die E-Mail bevorzugen.

## Einwilligung und Abmeldung

| Anforderung | cjw_newsletter und der Kernel |
|---|---|
| Opt-in | Double-Opt-in: das Abonnement zählt erst nach dem Link in der Bestätigungsmail |
| Einfaches Abmelden | `newsletter/unsubscribe`; Exponential fügt optionalen Mails, die über sein Mail-Gateway gehen, die Header `List-Unsubscribe` und `List-Unsubscribe-Post` hinzu ([Standards zum Datenschutz](../../../../standards/datenschutz.md)) |
| Gesperrte Adressen | eine Sperrliste; Rückläufer-Adressen werden markiert |
| Nachweise | Status der Abonnements: ausstehend, bestätigt, freigegeben, entfernt, zurückgewiesen |

Erheben Sie nur eine E-Mail-Adresse (und einen Namen, wenn Sie ihn verwenden). In der EU gelten die
[ePrivacy-Richtlinie](https://eur-lex.europa.eu/eli/dir/2002/58/oj) und die
[DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj) für Mails werblicher Art; öffentliche Informationsnewsletter
werden in der Praxis oft gleich behandelt.

## Proben vor echten Mails

Der Newsletter hat eigene Transporte; stellen Sie sie zuerst auf Datei, senden Sie eine Testausgabe an eine Testliste
mit Adressen auf einer reservierten Domain und lesen Sie die auf die Festplatte geschriebenen Mails
([Newsletter versenden](../../../ersteller/website-ersteller/newsletter-versenden.md)). Erst dann schalten Sie auf echten
Versand um.

## Wofür ein Newsletter nicht gedacht ist

| Bedarf | Verwenden Sie stattdessen |
|---|---|
| Dringende Warnungen | die Website, Warnsysteme, Benachrichtigungen ([Das Muster für dringende Bekanntmachungen](das-muster-fuer-dringende-bekanntmachungen.md)) |
| Persönliche Fallinformationen | die eigenen Nachrichten des Fallsystems |
| Mail an alle, die sich je registriert haben | nur Abonnenten, die eingewilligt haben |

## Verwandte Seiten

- [Nachrichten und Warnmeldungen](README.md)
- [cjw_newsletter](../../../../funktionen/6.0/erweiterungen/cjw_newsletter.md)
- [Benachrichtigungen und Abonnements](benachrichtigungen-und-abonnements.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
