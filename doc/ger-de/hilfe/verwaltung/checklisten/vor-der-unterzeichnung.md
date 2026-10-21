---
title: Vor der Unterzeichnung eines Vertrags
audience: government, project-manager, partner
category: public-sector
order: 110
summary: Was eine öffentliche Stelle prüft, bevor sie einen Vertrag für eine Exponential-Website, ihr Hosting oder ihren Support unterzeichnet - Umfang, Versionen, Eigentum am Code, Service-Level, Sicherheit, Datenschutz, Barrierefreiheit, Schulung, Ausstieg -, damit nichts Wichtiges dem guten Willen überlassen bleibt.
keywords: vor der Unterzeichnung, Vertrag, Vertragsprüfung, Eigentum, Quellcode, Service-Level, Auftragsverarbeitungsvertrag, Ausstieg, Gewährleistung, Abnahme, öffentlicher Sektor, before signing, contract, contract review, ownership, source code, service levels, data processing agreement, exit, warranty, acceptance, public sector
type: help
---
# Vor der Unterzeichnung eines Vertrags

Diese Checkliste richtet sich an das Vertragsmanagement und die Projektleitung einer öffentlichen Stelle nach dem
Zuschlag und vor der Unterschrift. Jeder Punkt ist etwas, das sich später nur schwer ergänzen lässt.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Lassen Sie den
> Vertrag von Ihrer Rechtsberatung prüfen.

## Inhalt

1. [Umfang und Versionen](#umfang-und-versionen)
2. [Eigentum und Quellcode](#eigentum-und-quellcode)
3. [Service und Support](#service-und-support)
4. [Sicherheit, Datenschutz und Barrierefreiheit](#sicherheit-datenschutz-und-barrierefreiheit)
5. [Menschen und Wissen](#menschen-und-wissen)
6. [Ausstieg](#ausstieg)

## Umfang und Versionen

- [ ] Die Lieferungen entsprechen den Anforderungen der Ausschreibung, jede mit einem Abnahmetest.
- [ ] Die zu liefernde Version von Exponential ist benannt (ein Release-Tag 6.0.x), mit den Erweiterungen und ihren Versionen ([Release-Richtlinie und Versionsnummern](../beschaffung/lebenszyklus/release-richtlinie-und-versionsnummern.md)).
- [ ] Der Dienstleister hält die Website während der Laufzeit auf einer unterstützten Version ([Unterstützte Versionen](../beschaffung/lebenszyklus/unterstuetzte-versionen.md)).
- [ ] Zuständigkeiten für die Plattform (PHP, Datenbank, Betriebssystem) zugewiesen ([Unterstützung von PHP und Plattform](../beschaffung/lebenszyklus/php-und-plattformunterstuetzung.md)).

## Eigentum und Quellcode

- [ ] Eigene Erweiterungen, Designs und Einstellungen werden als Quellcode in ein Repository geliefert, das Sie kontrollieren ([Mit Git entwickeln](../../mit-git-entwickeln.md)).
- [ ] Eigener Code, der Exponential erweitert, steht unter der GPL Version 2 oder später, wenn er an Sie verbreitet wird, und Sie erhalten seinen Quellcode ([Fragen zur Lizenzierung](../../projekt/lizenz-faq.md)).
- [ ] Keine Änderungen an Kernel-Dateien; Anpassungen in Erweiterungen und Overrides, damit Aktualisierungen möglich bleiben ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
- [ ] Komponenten von Drittanbietern und ihre Lizenzen aufgelistet.

## Service und Support

- [ ] Service-Level mit Ihren eigenen Zahlen: Verfügbarkeit, Reaktion pro Schweregrad, RPO und RTO, Wartungsfenster ([SLA-Optionen für das Hosting von Exponential](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)). Das Projekt veröffentlicht kein SLA.
- [ ] Wie die Service-Level gemessen und berichtet werden.
- [ ] Frist zum Einspielen veröffentlichter Sicherheitskorrekturen ([Sicherheitsupdates und Patch-Rhythmus](../beschaffung/lebenszyklus/sicherheitsupdates-und-patch-rhythmus.md)).
- [ ] Routinemäßige Updates enthalten oder mit Preis versehen ([Aktualisierungen im Haushalt planen](../beschaffung/lebenszyklus/upgrades-im-haushalt-planen.md)).
- [ ] Eskalationsweg und benannte Ansprechpersonen.

## Sicherheit, Datenschutz und Barrierefreiheit

- [ ] Auftragsverarbeitungsvertrag mit jedem Auftragsverarbeiter unterzeichnet; Datenstandort angegeben.
- [ ] Grundniveau der Härtung vereinbart ([Checkliste Sicherheit](checkliste-sicherheit.md)).
- [ ] Pflicht und Frist zur Meldung von Vorfällen vereinbart ([Bereitschaft für Vorfälle](bereitschaft-fuer-vorfaelle.md)).
- [ ] Ziel für die Barrierefreiheit der öffentlichen Website, mit Angabe, wer testet und wer behebt ([Checkliste Barrierefreiheit](checkliste-barrierefreiheit.md)).
- [ ] Keine personenbezogenen Daten aus der Produktion in Entwicklungs- oder Schulungskopien.

## Menschen und Wissen

- [ ] Schulungstermine, Teilnehmende und Rechte am Material festgelegt ([Schulungen bei einem Partner einkaufen](../beschaffung/schulung/schulung-bei-einem-partner-einkaufen.md)).
- [ ] Zu liefernde Dokumentation: Inhaltsmodell, Rollen, Einstellungen, Bereitstellung, Runbooks.
- [ ] Administratorkonten für benannte Mitarbeitende Ihrer Organisation vom ersten Tag an.

## Ausstieg

- [ ] Ausstiegsplan als Anlage: Exportformate, Übergabezeitraum, Zusammenarbeit mit einem Nachfolger ([Ausstiegspläne](../beschaffung/ausstiegsplaene/README.md)).
- [ ] Beim Ausstieg übergebene Schlüssel und Geheimnisse: Signaturschlüssel des Audits, Geheimnis der E-Mail-Einstellungen, TLS-Schlüssel ([Datensicherungen: Schlüssel und Geheimnisse](../../hosting/betrieb/backups-was-gesichert-werden-muss.md#schlüssel-und-geheimnisse-die-sich-nicht-neu-erzeugen-lassen)).
- [ ] Rückgabe und Löschung der Daten durch den Dienstleister am Ende, mit Bestätigung.
- [ ] Die Checkliste [Bereitschaft für den Ausstieg](bereitschaft-fuer-den-ausstieg.md) zur Vertragsakte genommen.

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Vor einer Ausschreibung](vor-einer-ausschreibung.md)
- [Ausschreibungsvorlagen](../beschaffung/ausschreibungsvorlagen/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
