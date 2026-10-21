---
title: Wofür Sie nicht bezahlen
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 20
summary: Die Kostenposten, die Sie bei einem Budget für Exponential weglassen können, jeweils mit Quelle - keine Lizenzgebühr, keine Gebühr pro Benutzer oder Server, kein vorgeschriebener Datenbankserver oder separater Webserver und viele Funktionen, die mitgeliefert werden - und die Kosten, die bleiben.
keywords: keine Lizenzgebühr, kostenlos, GPL, pro Benutzer, pro Arbeitsplatz, pro Server, SQLite, Velocity, eingebaute Funktionen, Shop, Newsletter, Zwei-Faktor, Audit-Trail, Herstellerbindung, Ausstiegskosten, no license fee, free of charge, per user, per seat, per server, built-in features, shop, newsletter, two-factor, audit trail, vendor lock-in, exit cost
type: help
---
# Wofür Sie nicht bezahlen

Diese Seite ist für alle, die ein Budget vorbereiten und wissen möchten, welche Kostenposten es bei Exponential einfach
nicht gibt. Jeder Posten unten nennt seine Quelle im Repository, damit Einkauf oder Finanzabteilung ihn prüfen können.
Die Seite listet auch auf, wofür Sie weiterhin bezahlen, damit das Budget ehrlich bleibt.

> **Hinweis**
> Diese Seite beschreibt die Lizenz und den Inhalt der Software. Sie ist keine Rechtsberatung; für Fragen zu Ihren
> eigenen Vorhaben mit der Lizenz lesen Sie [Lizenzierung für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)
> und fragen Sie Ihre Rechtsberatung.

## Inhalt

1. [Keine Lizenzgebühr](#keine-lizenzgebühr)
2. [Keine Gebühr pro Benutzer, Server oder Website](#keine-gebühr-pro-benutzer-server-oder-website)
3. [Kein vorgeschriebener Datenbankserver oder Webserver](#kein-vorgeschriebener-datenbankserver-oder-webserver)
4. [Funktionen, die mitgeliefert werden](#funktionen-die-mitgeliefert-werden)
5. [Keine Ausstiegsgebühr](#keine-ausstiegsgebühr)
6. [Wofür Sie weiterhin bezahlen](#wofür-sie-weiterhin-bezahlen)
7. [Checkliste für das Budget](#checkliste-für-das-budget)

## Keine Lizenzgebühr

- „Using Exponential under the terms of the GNU GPL is free of charge.“ (Die Nutzung von Exponential unter den
  Bedingungen der GNU GPL ist kostenlos, `README.md`, „License“)
- „The Professional License is no longer available.“ Die alte bezahlte Lizenz gibt es nicht mehr; Exponential ist
  „GNU GPLv2-or-later ONLY“ (`README.md`, „License“).
- `composer.json` gibt `"license": "GPL-2.0-or-later"` an.

Es gibt also für die Software selbst kein Jahresabonnement, das verlängert werden muss, und keine Verlängerung, die
auslaufen und eine Website stoppen kann.

## Keine Gebühr pro Benutzer, Server oder Website

Abschnitt 0 der GPL (`LICENSE.md`) sagt: „The act of running the Program is not restricted“ (Das Ausführen des
Programms ist nicht eingeschränkt). In der Praxis:

| Sie dürfen ohne Gebühr | Warum |
|---|---|
| Redakteure, Administratoren und Mitglieder ohne Grenze hinzufügen | Keine Klausel zählt Benutzer |
| Mehrere Websites aus einer Installation betreiben | Multisite ist eine eingebaute Funktion (`README.md`, „Main Exponential features“); siehe [Multisite](../multisite/README.md) |
| Server für Last oder Verfügbarkeit hinzufügen | Das Ausführen ist nicht eingeschränkt; Cluster werden unterstützt (`README.md`, „Support for highly available and scalable configurations“) |
| Eine Staging- und eine Testkopie halten | Kopien für den eigenen Gebrauch sind Ausführen des Programms |

## Kein vorgeschriebener Datenbankserver oder Webserver

- **SQLite 3 ist die Standarddatenbank**, und „SQLite needs none“ (SQLite braucht keinen Datenbankserver,
  `README.md`, „Requirements“). [Datenbankunterstützung](../../datenbankunterstuetzung.md) nennt es „a full production
  database for a site served from one machine“ (eine vollwertige Produktionsdatenbank für eine Website, die von einer
  Maschine ausgeliefert wird). Ein Server weniger, den Sie kaufen, betreiben, patchen und sichern müssen.
- **Exponential Velocity** ist ein Webserver, „bundled with Exponential“ (mit Exponential mitgeliefert), der „needs
  nothing installed beyond PHP“ (außer PHP nichts installiert braucht, `README.md`, „Requirements“ und „How
  Exponential is served“). Er liefert HTTPS selbst aus und kann Zertifikate von Let's Encrypt beziehen und erneuern
  (`README.md`, „When a traditional server is still the right answer“).
- Wenn Sie später einen Datenbankserver brauchen (mehr als eine schreibende Maschine, viele Redakteure), ist PostgreSQL
  der empfohlene ([Hosting, Datenbank und Betriebssystem wählen](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md)).
  Wenn Sie einen kommerziellen Datenbankserver wählen, gelten dessen eigene Lizenzbedingungen; prüfen Sie sie mit dem
  Hersteller.

## Funktionen, die mitgeliefert werden

Aus `README.md`, „Main Exponential features“, und den Erweiterungen, die `composer.json` verlangt:

| Bedarf | Enthalten | Mehr lesen |
|---|---|---|
| Online-Shop | „Advanced e-Commerce Stores (Built in)“ mit USt, Währungen, Bestellungen; PayPal über `ezpaypal` | [Shops](../shops/README.md) |
| Newsletter | `cjw_newsletter` wird von `composer.json` verlangt | [Newsletter](../../../../eng-us/help/businesses/newsletters/README.md) |
| Zweistufige Anmeldung | `sevenx_authentication_2fa` wird verlangt, und die Installer aktivieren es | [Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md) |
| Audit-Trail | „A tamper-evident audit trail with a console, alerts, exports and extension channels“ (ein manipulationssicherer Audit-Trail mit Konsole, Warnungen, Exporten und Kanälen für Erweiterungen) | [Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) |
| Suche | „Built in search engine“ (eingebaute Suchmaschine); Solr über `ezfind` ist optional | [Suche auf der Website](../../../../eng-us/help/creators/website-creators/site-search.md) |
| Sprachen | „Advanced multi-lingual support“ (umfassende Mehrsprachigkeit) | [Mehrsprachig](../../../../eng-us/help/businesses/multilingual/README.md) |
| Headless und Apps | „A headless CRUD REST API“ | [Mobil und headless](../mobil-und-headless/README.md) |
| Seitenbaukasten | Exponential Layouts | [Landingpages mit Layouts](../../../../eng-us/help/creators/website-creators/landing-pages-with-layouts.md) |
| Formulare | „Contact forms“ (Kontaktformulare) mit dem Information Collector; Erweiterungen für reCAPTCHA und hCaptcha | [Formulare mit dem Information Collector](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md) |
| Aktualisierungen in der Administration | `ezupdate` wird verlangt | [Automatische Aktualisierungen mit ezupdate](../../hosting/betrieb/automatische-updates-mit-ezupdate.md) |

Bevor Sie einen Posten aus dem Budget streichen, prüfen Sie, ob die enthaltene Funktion tut, was Sie brauchen: Eine
Vorführung auf einer Testinstallation ist der Beweis.

## Keine Ausstiegsgebühr

Die Lizenz gibt Ihnen das Recht, den Code zu jedem Anbieter mitzunehmen
([Lizenzierung für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md), „What a buyer may do with the
software“). Der Code und seine Historie sind öffentlich auf GitHub ([Mit Git entwickeln](../../mit-git-entwickeln.md)).
Der Umzug einer Website zu einem anderen Hoster ist ein dokumentiertes Verfahren
([Eine Website zwischen Hostern umziehen](../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)). Sie bezahlen
weiterhin für die Arbeit des Umzugs, aber niemand verlangt Geld für die Erlaubnis.

## Wofür Sie weiterhin bezahlen

Seien Sie gegenüber der Geschäftsleitung klar darüber; es sind echte Kosten auf jeder Plattform:

- **Arbeit**: Design, Entwicklung, Migration, Schulung und Support, durch Ihre Mitarbeiter oder durch ein Unternehmen
  Ihrer Wahl. 7x bietet das an; „Prices and terms are on the 7x website, not here“ (Preise und Bedingungen stehen auf
  der Website von 7x, nicht hier, [Wer ist 7x?](../../wer-ist-7x.md#dienstleistungen-und-hosting)).
- **Hosting**: Server oder ein Tarif, Backups, Monitoring ([Kostentreiber beim Hosting](kostentreiber-beim-hosting.md)).
- **Dienste Dritter**: Zahlungsanbieter, ein gehosteter KI-Anbieter, wenn Sie einen für Exponential Intelligence
  verwenden, Mail-Zustelldienste, CAPTCHA-Dienste. Jeder legt seine eigenen Bedingungen fest.
- **Eine Gewährleistung oder garantierte Reaktionszeit**: Die GPL kommt ohne Gewährleistung (`LICENSE.md`, Abschnitte
  11 und 12); ein Vertrag mit einem Anbieter kann eine hinzufügen
  ([Grundlagen zu Verträgen und SLAs](../auswahl/grundlagen-zu-vertraegen-und-slas.md)).
- **Freiwillige Unterstützung des Projekts**: Sponsoring und Spenden sind freiwillig (`README.md`, „Donate and make a
  support subscription“).

## Checkliste für das Budget

- [ ] Lizenzposten auf 0 gesetzt, mit `README.md` „License“ als Quelle.
- [ ] Kein Posten pro Benutzer oder pro Server.
- [ ] Posten für einen Datenbankserver nur, wenn Ihr Vorhaben einen braucht.
- [ ] Posten für die Lizenz eines separaten Webservers entfernt, wenn Sie Velocity oder einen Open-Source-Webserver
      verwenden.
- [ ] Posten für Zusatzprodukte nur für Funktionen entfernt, die Sie funktionieren gesehen haben.
- [ ] Posten für Arbeit, Hosting, Dienste Dritter und Support beibehalten und schriftlich angeboten.

## Verwandte Seiten

- [Gesamtbetriebskosten](gesamtbetriebskosten.md)
- [Exponential sachlich vergleichen](../../geschaeft/exponential-sachlich-vergleichen.md)
- [Funktionshighlights für Kunden](../../geschaeft/funktionshighlights-fuer-kunden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
