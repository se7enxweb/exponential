---
title: Hosting-Angebote vergleichen
audience: government, project-manager, hosting
category: public-sector
order: 120
summary: Schritt für Schritt Hosting-Angebote für die Exponential-Site einer Behörde vergleichen - erfüllte Anforderungen, Engine, Datenbank, Sicherungen und Wiederherstellung, Sicherheit, Datenstandort, Service-Level, Abgrenzung des Supports und Ausstieg - auf einem Vergleichsblatt.
keywords: Hosting-Angebote, Hosting vergleichen, Ausschreibung für Hosting, Datenstandort, Sicherungen, Wiederherstellung, SLA, Engine, Datenbank, Sicherheit, Ausstieg, Aufgabe zur Beschaffung, hosting offers, compare hosting, hosting tender, data location, backups, restore, engine, database, security, exit, procurement task
type: help
---
# Hosting-Angebote vergleichen

**Wer:** die IT-Fachkraft und der Projektleiter. **Wann:** bei der Wahl eines Hosters. Der Bereich Hosting dieser Hilfe
beschreibt, was Hoster anbieten können
([Hosting-Unternehmen: hier beginnen](../../../hosting/hosting-anbieter-hier-beginnen.md),
[Hosting-Tarife gestalten](../../../hosting/geschaeft/hosting-tarife-gestalten.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Vergleichsblatt](#das-vergleichsblatt)
4. [Ergebnis](#ergebnis)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Ihre Anforderungen an Hosting und Datenstandort
  ([Anforderungen an Hosting und Datenstandort](../../../../../eng-us/help/government/procurement/requirements/hosting-and-data-location-requirements.md)).
- Die technischen Anforderungen von Exponential
  ([Anforderungen auf einen Blick](../../../hosting/grundlagen/anforderungen-auf-einen-blick.md)).

## Schritte

1. **Prüfen Sie die Grundlagen**: unterstützte PHP-Version, die gewählte Datenbank, Bildwerkzeuge, Composer,
   Shell-Zugang für Cronjobs ([Anforderungen auf einen Blick](../../../hosting/grundlagen/anforderungen-auf-einen-blick.md)).
2. **Prüfen Sie die Engine**: Velocity, Apache oder nginx mit PHP-FPM oder FrankenPHP; Velocity läuft auf PHP 8.0 oder
   neuer ([Einen Webserver wählen](../../../hosting/server/einen-webserver-waehlen.md)).
3. **Prüfen Sie die Sicherungen**: Datenbank und Dateien zusammen, Schlüssel eingeschlossen, extern, Aufbewahrung und
   eine getestete Wiederherstellungszeit
   ([Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)).
4. **Prüfen Sie die Sicherheit**: TLS, Beschränkung der Administration, ein Anfragefilter, Isolation je Kunde
   ([Härtung für Hoster](../../../hosting/sicherheit/haertung-fuer-hoster.md)).
5. **Prüfen Sie den Datenstandort** von Produktion, Sicherungen und Supportzugang.
6. **Prüfen Sie die Service-Level**, die der Hoster anbietet, und wie er sie misst
   ([SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)).
7. **Prüfen Sie die Abgrenzung des Supports**: wo der Support für das Hosting endet und der für die Anwendung beginnt
   ([Support-Stufen](../../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)).
8. **Prüfen Sie den Ausstieg**: wie Sie Ihre Daten und Sicherungen erhalten, wenn Sie gehen
   ([Offboarding von Kunden (für Hoster)](../../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)).
9. **Vergleichen Sie die Preise über die Laufzeit** mit Ihren eigenen Zahlen
   ([Hostingkosten mit Ihren eigenen Zahlen schätzen](hostingkosten-mit-ihren-eigenen-zahlen-schaetzen.md)).

## Das Vergleichsblatt

```text
                                  Offer A        Offer B        Offer C
PHP / database / engine
Backups: frequency, off-site, retention, last restore test, RPO / RTO
Security: TLS, admin restriction, request filter, isolation
Data location: production / backups / support access
Service levels and measurement
Support line (hosting vs application)
Staging environment included?
Exit: data and backup return
Price per year x term (your model)
```

## Ergebnis

Ein Blatt, das das Gremium bewerten kann, auf dem jede Lücke sichtbar ist.

## Fallstricke

- Monatspreise von Tarifen mit unterschiedlichen Stufen bei Sicherung und Support vergleichen.
- Ein Hoster, der noch nie eine Exponential-Site wiederhergestellt hat.
- Keine Staging-Umgebung, was jedes Upgrade zu einem Test in der Produktion macht.

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Hostingkosten](../../../../../eng-us/help/government/procurement/total-cost-of-ownership/hosting-costs.md)
- [Klausel zu Hosting und Datenstandort](../../../../../eng-us/help/government/procurement/tender-templates/hosting-and-data-location-clause.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
