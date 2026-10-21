---
title: cjw_newsletter für Unternehmen
audience: businesses, sales-marketing, project-manager
category: business-solutions
order: 20
summary: Was die mit Exponential gelieferte Erweiterung cjw_newsletter in geschäftlichen Begriffen leistet - ihre Teile, ihre Geschichte, was 4.2.0 hinzugefügt hat, was sie kostet, was sie nicht tut und wo die Einzelheiten stehen.
keywords: Newsletter-Software, E-Mail-Marketing, Newsletter-Funktionen, Zustellbarkeit, Redaktion, Statistiken, SMS-Newsletter, CSV-Import, Unternehmen, cjw_newsletter, newsletter software, email marketing, newsletter features, deliverability, editorial, statistics, SMS newsletter, CSV import, business
type: help
---
# cjw_newsletter für Unternehmen

Diese Seite richtet sich an Entscheider, die wissen wollen, ob das Newsletter-System, das mit Exponential geliefert
wird, für ihre Organisation genügt. Sie beschreibt die Teile und Funktionen der Erweiterung in geschäftlichen Begriffen,
sagt klar, was sie nicht tut, und verweist für jede Aussage auf die technische Dokumentation.

## Inhalt

1. [Kurz gefasst](#kurz-gefasst)
2. [Woher sie kommt](#woher-sie-kommt)
3. [Die Teile in geschäftlichen Begriffen](#die-teile-in-geschäftlichen-begriffen)
4. [Was 4.2.0 hinzugefügt hat](#was-420-hinzugefügt-hat)
5. [Was sie kostet](#was-sie-kostet)
6. [Was sie nicht tut](#was-sie-nicht-tut)
7. [Fragen vor Ihrer Entscheidung](#fragen-vor-ihrer-entscheidung)

## Kurz gefasst

- Sie ist Teil der Installation: `se7enxweb/cjw_newsletter` wird vom `composer.json` von Exponential verlangt.
- Ausgaben sind Inhalte im Inhaltsbaum, sodass Redakteure die Werkzeuge nutzen, die sie schon kennen.
- Abonnenten bestätigen per Mail (Double-Opt-in); jede optionale Mail trägt über die E-Mail-Einstellungen der Website
  einen Abmeldelink ([E-Mail-Einstellungen](../../../../eng-us/features/6.0/mail-preferences.md)).
- Der Versand läuft im Hintergrund per Cron, mit einem Archiv jedes Versands.
- Alles lässt sich proben, ohne eine einzige Mail zu senden, indem Mails in Dateien geschrieben werden.

Quelle: [cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md).

## Woher sie kommt

Die Erweiterung wurde zwischen 2007 und 2015 vom CJW Network, coolscreen.de, JAC Systeme und Webmanufaktur geschrieben.
Das Projekt se7enxweb hat sie im August 2026 für PHP 8.5 und Exponential 6 übernommen (4.0.0.0 am 13. August). Die
Releases 4.1.0 bis 4.1.17 brachten sie auf PHP 8, auf persistente Worker (Velocity) und auf jede Datenbank und
schlossen Fehler, die eine systematische Prüfung gefunden hatte; 4.2.0 folgte am 5. Oktober 2026
([cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md)). Für ein Unternehmen bedeutet das ein
langlebiges Werkzeug mit einer gepflegten aktuellen Linie, kein neues Experiment.

## Die Teile in geschäftlichen Begriffen

| Geschäftlicher Bedarf | Teil der Erweiterung | Wo er liegt |
|---|---|---|
| Mehrere Newsletter für verschiedene Zielgruppen | Newsletter-Systeme und Listen (Inhaltsklassen `cjw_newsletter_system`, `cjw_newsletter_list`) | Inhaltsbaum unterhalb von `RootFolderNodeId` |
| Eine Ausgabe mit ihren Artikeln | Ausgaben (`cjw_newsletter_edition`, Artikel `cjw_newsletter_article`) | Unterhalb einer Liste |
| Menschen können sich selbst an- und abmelden | Öffentliche Ansichten `newsletter/subscribe`, `configure`, `unsubscribe` | Seiten Ihrer Website |
| Wissen, wer was bekommt | Abonnement- und Benutzerlisten mit einem Status pro Abonnement | Newsletter-Menü der Administration |
| Adressen nicht anschreiben, die bouncen oder widersprechen | Bounce-Postfächer, Blacklist, die Sperrliste der Website | Newsletter-Menü, Setup > E-Mail-Einstellungen |
| Abonnenten übernehmen und herausgeben | CSV-Import und -Export | Newsletter-Menü |
| Versenden, ohne die Website zu blockieren | Cron-Teile `cjw_newsletter_mailqueue_create` und `cjw_newsletter_mailqueue_process` | Cron des Servers |
| Ein Überblick für die verantwortliche Person | Dashboard unter `/newsletter/index` (4.1.17) mit Problemen und dem, was dagegen zu tun ist | Newsletter-Menü |

Quellen: [cjw_newsletter, Die Teile](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-parts) und
[Newsletter mit cjw_newsletter](../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md#the-parts).

## Was 4.2.0 hinzugefügt hat

Die eigenen Leitfäden der Erweiterung beschreiben jeden Bereich (Ordner `doc/` der Erweiterung, erschlossen über ihr
`doc/README.md`):

| Bereich | Was er einem Unternehmen bringt |
|---|---|
| Zustellbarkeit | Hard Bounces und Beschwerden kommen auf die Sperrliste der Website; Soft Bounces werden wiederholt; Ratenbegrenzungen und Stapel; Testgruppen; An- und Abmelden per E-Mail; Sperrlisten-Import |
| Redaktion | Wiederkehrende Versände, Artikel-Pools und eine Artikelauswahl, Freigabe von Ausgaben im Posteingang der Zusammenarbeit |
| Darstellung | Platzhalter, Teile, die nur manche Abonnenten bekommen, Interessen, eine Ausgabe in mehreren Sprachen, drei weitere Skins, Nur-Text-Ansichten, Vorschau als Abonnent |
| Statistiken | Öffnungen und Klicks pro Person nur mit Einwilligung, sonst anonyme Summen, Berichte, A/B-Tests von Betreffzeilen |
| SMS | Newsletter per SMS über einen anbieterneutralen Transport, ein Double-Opt-in per Code, STOP |
| Import, Export, Migration | CSV-Import mit Spaltenzuordnung, Export der Abonnenten, Migration von der alten Erweiterung eznewsletter |

Quelle: [cjw_newsletter, Die Funktionen von 4.2.0](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-features-of-420).
Tracking und SMS sind ausgeschaltet, bis sie in `settings/override/cjw_newsletter.ini.append.php` eingeschaltet werden
(dieselbe Seite).

## Was sie kostet

Es gibt keine Lizenzgebühr: „Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos“ (README,
„License“), und die eigene Lizenz der Erweiterung ist die GNU GPL v2 oder später (`doc/README.md` der Erweiterung). Sie
bezahlen für Arbeit und Dienstleistungen, die Sie bewusst einkaufen, sowie für den Mail-Dienst, der die Nachrichten
zustellt ([Lizenzierung für Käufer](../../geschaeft/lizenzen-fuer-kaeufer.md#keine-lizenzgebühren-und-wofür-käufer-tatsächlich-bezahlen)).
Für ein Kostenmodell mit Ihren eigenen Zahlen siehe [Gesamtbetriebskosten](../kosten/gesamtbetriebskosten.md).

## Was sie nicht tut

Seien Sie sich über diese Punkte im Klaren, bevor Sie einem Marketingteam etwas versprechen:

- **Sie stellt Mails nicht selbst zu.** Sie übergibt Mails an `sendmail` oder einen SMTP-Server. Ob Mails in den
  Posteingängen ankommen, hängt von diesem Server, den DNS-Einträgen Ihrer Absenderdomain (SPF, DKIM) und Ihren Inhalten
  ab ([Newsletter sicher versenden](../../../../eng-us/help/creators/website-creators/sending-newsletters.md)).
- **Sie ist kein gehosteter Mailing-Dienst.** Sie oder Ihr Hosting-Partner betreiben Cron, das Bounce-Postfach und den
  Mail-Transport ([Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md)).
- **Sie zählt Öffnungen und Klicks nicht standardmäßig.** Das Tracking ist in den mitgelieferten Einstellungen
  ausgeschaltet, und Zahlen pro Person brauchen die Einwilligung jeder Person
  ([statistics.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/statistics.md)).
- **Sie entscheidet nicht, was rechtmäßig ist.** Sie zeichnet Einwilligungen auf und bietet die Abmeldung an; ob Ihre
  Liste und Ihre Formulierungen Ihrem Recht entsprechen, klären Ihr Anwalt oder Ihr Datenschutzbeauftragter.
- **SMS braucht einen Anbieter.** Die Erweiterung liefert einen Datei-Transport für Tests, einen generischen
  HTTP/JSON-Transport und eine Voreinstellung für APIs im Stil von Twilio; für echte Nachrichten brauchen Sie ein Konto
  bei einem Anbieter ([sms.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/sms.md)).

## Fragen vor Ihrer Entscheidung

| Frage | Wo die Antwort steht |
|---|---|
| Können wir von unserer eigenen Domain und über unseren Mail-Anbieter senden? | [Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md) |
| Können wir unsere bestehenden Abonnenten mitbringen? | [Listen und Abonnements](listen-und-abonnements.md#importieren-und-exportieren) |
| Kann eine Führungskraft jede Ausgabe freigeben? | [Ausgaben schreiben und versenden](ausgaben-schreiben-und-versenden.md#freigabe-vor-dem-versand) |
| Wie weisen wir die Einwilligung nach? | [Opt-in und Einwilligung](opt-in-und-einwilligung.md) |
| Was geschieht mit Adressen, die bouncen? | [Bounces und Listenpflege](bounces-und-listenpflege.md) |
| Können wir alles testen, ohne Kunden anzuschreiben? | [Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md#erst-die-probe) |

## Verwandte Seiten

- [Newsletter für Unternehmen](README.md)
- [Einen Newsletter planen](einen-newsletter-planen.md)
- [Funktionsseite cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md)
- [Funktionshöhepunkte für Kunden](../../geschaeft/funktionshighlights-fuer-kunden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
