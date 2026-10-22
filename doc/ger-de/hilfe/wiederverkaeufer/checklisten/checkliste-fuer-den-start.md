---
title: Checkliste für den Start für Wiederverkäufer
audience: reseller, partner, hosting
category: reselling
order: 470
summary: Die Checkliste, die ein Wiederverkäufer durcharbeitet, bevor die Exponential-Website, der Tarif oder das Produkt eines Kunden online geht - Vertragliches und Kaufmännisches, technische Bereitschaft, Sicherheit, Datensicherungen, Überwachung, Prüfungen von Inhalt und Barrierefreiheit, Schulung, Dokumentation und der erste Tag nach dem Start.
keywords: Start, Go-live, Checkliste, Checkliste für den Start, Kunden-Website, Bereitschaft, Sicherheit, Datensicherungen, Überwachung, DNS, TLS, Schulung, Dokumentation, Powered by Exponential, launch, go-live, checklist, launch checklist, customer site, readiness, security, backups, monitoring, training, documentation
type: help
---
# Checkliste für den Start für Wiederverkäufer

Diese Seite ist für Projektleiter bei Wiederverkäufern. Arbeiten Sie sie vor jedem Start eines Kunden durch. Die
technischen Punkte folgen der Go-live-Liste des Hosting-Bereichs ([Go-live einer neuen Kunden-Website](../../hosting/checklisten/checkliste-fuer-den-start-einer-kunden-website.md));
diese Liste ergänzt die kaufmännischen und kundenbezogenen Punkte des Wiederverkäufers.

## Inhalt

1. [Kaufmännisches](#kaufmännisches)
2. [Technik](#technik)
3. [Sicherheit](#sicherheit)
4. [Inhalt und Qualität](#inhalt-und-qualität)
5. [Menschen und Dokumente](#menschen-und-dokumente)
6. [Der Starttag und der Tag danach](#der-starttag-und-der-tag-danach)

## Kaufmännisches

- [ ] Vertrag unterschrieben, einschließlich der Bedingungen zur Auftragsverarbeitung und der Ausstiegsklausel ([Verträge mit Kunden](../kunden/vertraege-mit-kunden.md)).
- [ ] Stufe und Service-Levels schriftlich bestätigt ([Service-Levels gestalten](../angebote/service-levels-gestalten.md)).
- [ ] Erste Rechnung eingeplant ([Rechnungsstellung und Steuern](../verantwortlichkeiten/rechnungsstellung-und-steuern.md)).
- [ ] Subunternehmer (Hosting-Lieferant, White-Label-Dienstleistungen) für diesen Kunden bestätigt.
- [ ] Nennung „Powered by Exponential“ vereinbart oder abgelehnt ([Powered by Exponential zeigen](../verkauf/powered-by-exponential.md)).

## Technik

- [ ] Der Server erfüllt die Anforderungen; Web-PHP und Kommandozeilen-PHP stimmen überein ([Hosting-Anforderungen](../angebote/hosting-anforderungen-fuer-angebote.md)).
- [ ] Neuestes Release 6.0.x und aktuelle Versionen der Erweiterungen installiert ([Kunden aktuell halten](../kunden/kunden-aktuell-halten.md)).
- [ ] **Setup > Upgrade-Prüfung** ohne Befund ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
- [ ] Jeder Hostname in DNS, im Webserver oder in Velocity und im Zertifikat; jeder Siteaccess erreicht die richtige Website
      ([Multisite-Angebote](../angebote/multisite-angebote.md)).
- [ ] Cronjobs laufen ([Cronjobs](../../../anleitungen/cronjobs.md)).
- [ ] Mailversand getestet ([Mailzustellung](../../hosting/betrieb/mailzustellung.md)).
- [ ] Caches und Leistung unter der erwarteten Last geprüft ([Benchmarks einer gehosteten Website](../../hosting/leistung/benchmark-einer-gehosteten-website.md)).

## Sicherheit

- [ ] Härtung angewendet; Debug-Ausgabe aus ([Sicherheitspflichten von Wiederverkäufern](../verantwortlichkeiten/sicherheitspflichten.md)).
- [ ] Standardpasswörter geändert; nur persönliche Konten; der Kunde hat ein eigenes Administratorkonto.
- [ ] Anmeldung in zwei Schritten für Administratoren angeboten.
- [ ] Der Demomodus ist **nicht** gesetzt: kein `EXP_DEMO_MODE` im Pool der Website, im virtuellen Host, in
      Service-Units oder in der Crontab ([Demomodus für Hoster](../../hosting/sicherheit/demomodus-fuer-hoster.md#sie-von-der-produktion-fernhalten)).
- [ ] Datensicherungen laufen; eine Wiederherstellung getestet ([Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)).
- [ ] Überwachung von Startseite und Administration ([Health-Checks und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)).

## Inhalt und Qualität

- [ ] Barrierefreiheit des öffentlichen Designs geprüft ([Standards für Barrierefreiheit](../../../standards/barrierefreiheit.md)).
- [ ] Weiterleitungen von alten Adressen eingerichtet ([URLs, Weiterleitungen und verschobene Seiten](../../kunden/alltag/urls-weiterleitungen-und-verschobene-seiten.md)).
- [ ] Datenschutzhinweis und Einwilligungstexte vom Kunden verfasst.
- [ ] Shop-Websites: Zahlung, Mehrwertsteuer und Bestätigungsmail getestet ([Checkliste für den Start eines Shops](../../unternehmen/shops/checkliste-fuer-den-shop-start.md)).
- [ ] Sprachen: jede Sprache erreichbar, Schreibrichtung von rechts nach links bei Bedarf getestet ([Mehrsprachige Angebote](../angebote/mehrsprachige-angebote.md)).

## Menschen und Dokumente

- [ ] Redakteure geschult ([Schulungs-Wiederverkäufer](../modelle/schulungs-wiederverkaeufer.md)).
- [ ] Kundenhandbuch übergeben, mit erhaltenen Hinweisen ([Dokumentation, die Sie Ihren Kunden geben können](../kunden/dokumentation-fuer-ihre-kunden.md)).
- [ ] Willkommensschreiben mit Kontakten und Supportzeiten versendet ([Kunden-Onboarding](../kunden/kunden-onboarding.md#vorlage-für-das-willkommensschreiben)).
- [ ] Website in Ihr Inventar und Ihre monatliche Routine aufgenommen.

## Der Starttag und der Tag danach

- [ ] DNS umstellen oder in einem vereinbarten Zeitfenster live gehen; Personal ist verfügbar.
- [ ] Startseite, Administration, eine Testveröffentlichung, Formulare und Suche prüfen.
- [ ] Nach den ersten Stunden **Setup > Logdateien** prüfen.
- [ ] Dem Kunden eine kurze Nachricht „online“ senden, mit dem, worauf er achten sollte.
- [ ] Eine Woche später: Logs, Tickets und Fragen der Redakteure überprüfen.

## Verwandte Seiten

- [Go-live einer neuen Kunden-Website](../../hosting/checklisten/checkliste-fuer-den-start-einer-kunden-website.md)
- [Kunden-Onboarding](../kunden/kunden-onboarding.md)
- [Monatliche Checkliste für Wiederverkäufer](monatliche-checkliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
