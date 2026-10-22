---
title: Unterstützer-Newsletter mit cjw_newsletter
audience: non-profit, businesses, editor, admin
category: non-profits
order: 20
summary: Die Newsletter einer Non-Profit-Organisation für Unterstützer und Ehrenamtliche mit cjw_newsletter in Exponential betreiben - Systeme, Listen und Ausgaben als Inhalte, Double-Opt-in, ein Probelauf, der E-Mails in Dateien schreibt, die Umstellung auf echten Versand, die Cronjob-Teile, Bounces und die Sperrliste, Einwilligung und Abmeldung mit einem Klick sowie eine monatliche Routine - mit Links zu den ausführlichen Newsletter-Seiten.
keywords: Newsletter, Unterstützer-Newsletter, Listen, Ausgaben, Double-Opt-in, Anmeldung, Abmeldung, Cronjob, Mail-Warteschlange, Dateitransport, Bounces, Sperrliste, Einwilligung, Liste der Ehrenamtlichen, newsletter, supporter newsletter, cjw_newsletter, lists, editions, double opt-in, subscribe, unsubscribe, cron, mail queue, file transport, SMTP, bounces, blacklist, consent, volunteers list
type: help
---
# Unterstützer-Newsletter mit cjw_newsletter

Diese Seite richtet sich an die Verantwortlichen für Kommunikation und an den Administrator. `cjw_newsletter` ist das
Newsletter-System, das mit Exponential geliefert wird: „Sie schreiben Newsletter-Ausgaben als Inhalte; Besucher
abonnieren Listen mit Double-Opt-in; Ausgaben werden im Hintergrund per Cronjob versendet; Bounces und eine Sperrliste
halten die Listen sauber; jeder Versand wird archiviert“ ([cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md)).
Sie öffnen es über **Newsletter** im oberen Menü der Administrationsoberfläche.

## Inhalt

1. [Listen, die eine Non-Profit-Organisation üblicherweise braucht](#listen-die-eine-non-profit-organisation-üblicherweise-braucht)
2. [Einrichten](#einrichten)
3. [Proben, bevor Sie wirklich versenden](#proben-bevor-sie-wirklich-versenden)
4. [Eine Ausgabe schreiben und versenden](#eine-ausgabe-schreiben-und-versenden)
5. [Einwilligung und Abmeldung](#einwilligung-und-abmeldung)
6. [Listen sauber halten](#listen-sauber-halten)
7. [Monatliche Routine](#monatliche-routine)

## Listen, die eine Non-Profit-Organisation üblicherweise braucht

| Liste | Wer | Übliche Häufigkeit |
|---|---|---|
| Unterstützer | Spender und Freunde, die sich angemeldet haben | Monatlich oder vierteljährlich |
| Ehrenamtliche | Aktive Ehrenamtliche | Nach Bedarf: Dienstpläne, Veranstaltungen |
| Presse | Journalisten | Für Ankündigungen |
| Eine Sprachliste | Unterstützer in einer zweiten Sprache | Mit jeder übersetzten Ausgabe |

Listen sind Inhaltsobjekte (Klassen `cjw_newsletter_system`, `cjw_newsletter_list`, `cjw_newsletter_edition`) unterhalb
des Knotens, der in `[NewsletterSettings] RootFolderNodeId` gesetzt ist (dieselbe Quelle). Halten Sie die Zahl der
Listen klein; jede Liste macht Arbeit.

## Einrichten

Der Administrator oder Partner:

1. Legt im Inhaltsbaum ein Newsletter-System und seine Listen an.
2. Trägt die Einstellungen in `settings/override/cjw_newsletter.ini.append.php` ein.
3. Verlinkt die öffentliche Anmeldeseite (`newsletter/subscribe`) in der Fußzeile der Website, auf der Dankesseite der
   Spendenseite und auf den Seiten für Ehrenamtliche.
4. Fügt die Cronjob-Teile hinzu, sobald ein Probelauf funktioniert:

```bash
php runcronjobs.php cjw_newsletter
php runcronjobs.php -s <siteaccess> cjw_newsletter_mailqueue_create
php runcronjobs.php -s <siteaccess> cjw_newsletter_mailqueue_process
```

Quelle: [cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md),
[Newsletter mit cjw_newsletter](../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md).

## Proben, bevor Sie wirklich versenden

`cjw_newsletter.ini` kann E-Mails in Dateien schreiben, statt sie zu versenden (`TransportMethodCronjob=file` und die
beiden anderen Transporte, `FileTransportMailDir=var/log/mail`); so ist ein vollständiger Probelauf von Anmeldung,
Bestätigung, Versand und Abmeldung ohne echten Empfänger möglich. Stellen Sie danach auf `smtp` oder `sendmail` um
([Zustellung der Newsletter-E-Mails](../../unternehmen/newsletters/zustellung-der-newsletter-mails.md)).

Bei größeren Listen zählen ein Zustelldienst für E-Mails und korrekte DNS-Einträge der Absenderdomain mehr als alles auf
der Website; das kann ein eigener Budgetposten sein ([Budgetposten](../foerdermittel/budgetposten-fuer-hosting-und-wartung.md)).

## Eine Ausgabe schreiben und versenden

1. Legen Sie in der Liste eine Ausgabe als Inhalt an: einen Betreff, eine Einleitung, einige Beiträge mit Links zur
   Website.
2. Senden Sie einen Test an sich selbst und an eine Kollegin oder einen Kollegen (Vorschau).
3. Planen Sie den Versand oder versenden Sie sofort; der Cronjob stellt die E-Mails in die Warteschlange und
   verarbeitet sie.
4. Prüfen Sie anschließend das Archiv.

Tipps zum Schreiben: eine Hauptbotschaft pro Ausgabe, echter Text statt Bilder, Alternativtext für Bilder, eindeutige
Linktexte ([Barrierefreie Kampagnen und Dokumente](../barrierefreiheit/barrierefreie-kampagnen-und-dokumente.md#newsletter)).
Einzelheiten: [Ausgaben schreiben und versenden](../../unternehmen/newsletters/ausgaben-schreiben-und-versenden.md).

## Einwilligung und Abmeldung

- Double-Opt-in: Abonnenten bestätigen per E-Mail, bevor sie Ausgaben erhalten.
- Eine Spende ist keine Einwilligung in den Newsletter; bieten Sie ihn an, tragen Sie Spender nie selbst ein.
- Für den Import einer alten Liste brauchen Sie einen Nachweis, woher die Einwilligung stammt
  ([Opt-in und Einwilligung](../../unternehmen/newsletters/opt-in-und-einwilligung.md)).
- Der Widerruf muss so einfach sein wie die Einwilligung: ein Abmeldelink in jeder Ausgabe
  ([Abmeldung mit einem Klick](../../verwaltung/datenschutz/abmelden-mit-einem-klick.md)).

> **Hinweis**
> Grundlagen, keine Rechtsberatung.

## Listen sauber halten

Nach `BounceThresholdValue` Bounces (Voreinstellung 3) erhält ein Abonnent keine E-Mails mehr; Adressen auf der
Sperrliste erhalten nie E-Mails (dieselbe Quelle). Siehe [Bounces und Listenpflege](../../unternehmen/newsletters/bounces-und-listenpflege.md).

## Monatliche Routine

- [ ] Ausgabe geschrieben, getestet und planmäßig versendet.
- [ ] Zahl der bestätigten Abonnenten notiert (nützlich für Geldgeber, [Wirkung messen](../foerdermittel/wirkung-mit-website-daten-messen.md)).
- [ ] Bounces angesehen; Anfragen zur Sperrliste erledigt.
- [ ] Cronjob gelaufen (das Archiv zeigt den Versand).
- [ ] Etwaige Exporte von Abonnenten nach Gebrauch gelöscht.

Die allgemeine Routine: [Newsletter-Routine](../../kunden/wachstum/newsletter-routine.md).

## Verwandte Seiten

- [Kommunikation für Non-Profit-Organisationen](README.md)
- [Newsletter (Unternehmen)](../../unternehmen/newsletters/README.md)
- [Newsletter-Checkliste](../../unternehmen/newsletters/checkliste-newsletter.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
