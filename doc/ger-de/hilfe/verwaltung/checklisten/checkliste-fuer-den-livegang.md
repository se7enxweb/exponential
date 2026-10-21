---
title: Checkliste für den Livegang einer öffentlichen Website
audience: government, admin, project-manager, editor, hosting
category: public-sector
order: 20
summary: Alles, was eine öffentliche Stelle prüfen sollte, bevor sie eine Exponential-Website für die Öffentlichkeit freigibt - Inhalte, Barrierefreiheit, Sicherheit, Datenschutz, Betrieb, Überwachung und Abnahme - als umsetzbare Checkliste mit der Seite, die jeden Punkt erklärt.
keywords: Livegang, Start, Go-live, Checkliste, öffentliche Website, Produktion, Abnahme, HTTPS, Datensicherungen, Erklärung zur Barrierefreiheit, Datenschutzerklärung, Überwachung, öffentlicher Sektor, go-live, launch, checklist, public site, production, sign-off, backups, accessibility statement, privacy notice, monitoring, public sector
type: help
---
# Checkliste für den Livegang einer öffentlichen Website

Diese Checkliste richtet sich an die Projektleitung und die Website-Administration einer öffentlichen Stelle in den
letzten Wochen vor dem Start. Sie ergänzt die allgemeinen Startlisten in
[Checkliste für den Start](../../../../eng-us/help/creators/website-creators/launch-checklist.md) und, für Hoster,
[Livegang einer neuen Kundenwebsite](../../hosting/checklisten/checkliste-fuer-den-start-einer-kunden-website.md) um die Punkte des
öffentlichen Sektors (Erklärung zur Barrierefreiheit, Datenschutzerklärung, Schriftgut, Abnahme).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Inhalte](#inhalte)
2. [Barrierefreiheit](#barrierefreiheit)
3. [Sicherheit](#sicherheit)
4. [Datenschutz](#datenschutz)
5. [Betrieb](#betrieb)
6. [Am Tag des Starts](#am-tag-des-starts)
7. [Abnahme](#abnahme)

## Inhalte

- [ ] Jede Seite der Inhaltsbestandsaufnahme ist migriert, geprüft und veröffentlicht ([Sitemap und Inhaltsbestandsaufnahme](../../../../eng-us/help/creators/website-creators/site-map-and-content-inventory.md)).
- [ ] Weiterleitungen von alten Adressen sind eingerichtet ([Weiterleitungen und verschobene Inhalte](../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md)).
- [ ] Keine defekten Links oder Relationen: **Setup > URL-Verwaltung** und **Ungültige Relationen** ([Inhaltsprüfungen](../../../../eng-us/features/6.0/content-checks.md)).
- [ ] Kontaktdaten, Öffnungszeiten und rechtliche Seiten (Impressum, Nutzungsbedingungen) sind aktuell.
- [ ] Testinhalte und Testkonten sind entfernt.
- [ ] Jede Sprachfassung ist vollständig, oder die fehlenden sind verborgen ([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)).

## Barrierefreiheit

- [ ] Wichtige Templates und Seiten gegen WCAG 2.2 AA getestet ([Barrierefreiheit testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)); das öffentliche Design liegt in Ihrer Verantwortung, es wird vom Projekt nicht geprüft ([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#known-gaps)).
- [ ] Bilder haben Alternativtexte; Überschriften sind in der richtigen Reihenfolge; Linktexte ergeben auch allein Sinn.
- [ ] Eine Erklärung zur Barrierefreiheit ist veröffentlicht; Exponential liefert dafür keine Vorlage mit (Leitfaden des W3C: [w3.org/WAI/planning/statements](https://www.w3.org/WAI/planning/statements/)).
- [ ] Die vollständige Liste steht in der [Checkliste Barrierefreiheit](checkliste-barrierefreiheit.md).

## Sicherheit

- [ ] Überall HTTPS; HSTS gesetzt, sobald die ganze Website HTTPS nutzt ([TLS und HTTPS](../../hosting/sicherheit/tls-und-https.md), [Sicherheitsstandards](../../../../eng-us/standards/security.md#known-gaps)).
- [ ] Das anfängliche Administratorpasswort ist geändert und `var/log/initial-admin-password` entfernt ([Exponential mit einem Befehl installieren](../../../../eng-us/features/6.0/install-in-one-command.md#safer-first-login)).
- [ ] Debug-Ausgabe für Besucher ausgeschaltet; Härtung angewendet ([13. Sicherheitshärtung für die Produktion](../../../installation/13-sicherheitshaertung.md)).
- [ ] Administratorkonten verwenden die zweistufige Anmeldung, wo Ihre Website sie hat ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- [ ] Prüfprotokoll eingeschaltet und Empfänger von Alarmen gesetzt: `./console exp:audit status` und `./console exp:audit alerts recipients` ([Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)).
- [ ] Die vollständige Liste steht in der [Checkliste Sicherheit](checkliste-sicherheit.md).

## Datenschutz

- [ ] Datenschutzerklärung veröffentlicht, die jeden Drittanbieter nennt, den die Website nutzt ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#third-party-services-in-shipped-extensions)).
- [ ] Cookie-Einwilligung eingerichtet, wenn die Website Webanalyse, Einbettungen oder Werbung hinzufügt; Exponential hat kein eigenes Banner ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#status-1)).
- [ ] Formulare zeigen einen Einwilligungstext und haben eine Aufbewahrungsregel ([Formulare mit dem Informationssammler](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)).
- [ ] Die vollständige Liste steht in der [Checkliste Datenschutz](checkliste-datenschutz.md).

## Betrieb

- [ ] Datensicherungen laufen, und eine Wiederherstellung wurde getestet ([Datensicherungen und Wiederherstellungen](../../../../eng-us/help/creators/website-creators/backups-and-restores.md)).
- [ ] Cronjobs laufen: **Setup > Cronjobs** ([Cronjobs](../../../anleitungen/cronjobs.md)).
- [ ] **Setup > Systeminformationen** zeigt keine offene Zustandsprüfung ([Systeminformationen](../../../anleitungen/systeminformationen.md)).
- [ ] Eine externe Verfügbarkeitsprüfung überwacht eine Seite, die die Datenbank berührt ([Zustandsprüfungen und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)).
- [ ] Die Kontaktliste für Vorfälle und die Runbooks sind bekannt ([Bereitschaft für Vorfälle](bereitschaft-fuer-vorfaelle.md)).
- [ ] Die Caches sind vorgewärmt, damit die ersten Besucher nicht warten ([Caches vorwärmen](../../../anleitungen/caches-vorwaermen.md)).

## Am Tag des Starts

- [ ] DNS in einer ruhigen Zeit umstellen; die alte Website bis zur Abnahme schreibgeschützt behalten.
- [ ] Startseite, Suche, ein Formular, die Anmeldung und einen Download von außerhalb Ihres Netzes prüfen.
- [ ] In den ersten Stunden **Setup > Logdateien** und das Audit-Dashboard (`/audit/dashboard`) beobachten ([Protokollanzeige](../../../../eng-us/features/6.0/log-viewer.md)).
- [ ] Die Website über die Kanäle Ihrer Organisation ankündigen.

## Abnahme

- [ ] Abnahmetests bestanden und festgehalten.
- [ ] Dokumentation und Administratorzugang an benannte Personen in Ihrer Organisation übergeben.
- [ ] Redaktionen geschult ([Schulung in der Beschaffung](../../../../eng-us/help/government/procurement/training/README.md)).
- [ ] Der Ausstiegsplan liegt vor ([Bereitschaft für den Ausstieg](bereitschaft-fuer-den-ausstieg.md)).

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Checkliste für den Start](../../../../eng-us/help/creators/website-creators/launch-checklist.md)
- [Livegang: Hosts und HTTPS](../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
