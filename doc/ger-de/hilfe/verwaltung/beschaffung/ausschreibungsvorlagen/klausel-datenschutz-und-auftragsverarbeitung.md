---
title: "Ausschreibungsvorlage: Klausel zu Datenschutz und Auftragsverarbeitung"
audience: government, project-manager, partner, hosting
category: public-sector
order: 60
summary: Klauseltext für die Datenschutzpflichten in einem Vertrag über ein CMS und die Punkte, die ein Auftragsverarbeitungsvertrag abdecken muss - Verarbeitung auf Weisung, Unterauftragsverarbeiter, Standorte, Einstellungen zur Datenminimierung, Anfragen betroffener Personen, Meldung von Datenschutzverletzungen und Löschung am Ende.
keywords: Datenschutzklausel, Auftragsverarbeitungsvertrag, AVV, Auftragsverarbeiter, Unterauftragsverarbeiter, DSGVO Artikel 28, Datenminimierung, Cookies, Einwilligung, Meldung von Datenschutzverletzungen, Löschung, Ausschreibungsvorlage, privacy clause, data processing agreement, DPA, processor, sub-processor, GDPR article 28, data minimization, consent, breach notification, deletion, tender template
type: help
---
# Ausschreibungsvorlage: Klausel zu Datenschutz und Auftragsverarbeitung

Diese Seite richtet sich an Datenschutzbeauftragte und Beschaffungsstellen. Wenn ein Lieferant eine Website baut, hostet
oder betreut, verarbeitet er in Ihrem Auftrag personenbezogene Daten. In der EU verlangt die
[DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj) einen Vertrag mit dem Auftragsverarbeiter (Artikel 28). Die
Klausel unten deckt die für ein CMS spezifischen Punkte ab; Ihr Standard-Auftragsverarbeitungsvertrag deckt den Rest ab.

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Die Klausel](#die-klausel)
2. [Personenbezogene Daten, die ein CMS enthält](#personenbezogene-daten-die-ein-cms-enthält)
3. [Was Exponential für die Klausel bietet](#was-exponential-für-die-klausel-bietet)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Die Klausel

```text
P. PRIVACY AND DATA PROCESSING

P.1 Roles. The Customer is the controller of personal data processed in the
    system. The Supplier processes it only on the Customer's documented
    instructions, under the data processing agreement in <annex>.

P.2 Inventory. Before go-live the Supplier shall deliver an inventory of the
    personal data the delivered system stores (tables, files, logs, backups),
    its purpose and its retention, and every cookie the public site sets.

P.3 Minimization by configuration. The Supplier shall configure log masking and
    retention as set out in <annex>, and shall not activate any extension or
    service that sends visitor data to a third party without the Customer's
    written approval and, where required, a consent mechanism.

P.4 Sub-processors and locations. The Supplier shall list every sub-processor and
    every location where personal data or backups are stored, and shall not
    change them without <period> prior notice and the Customer's right to object.

P.5 Requests. The Supplier shall assist the Customer in answering requests for
    access, correction, erasure and restriction within <time>, using the
    documented tools of the system, and shall document any data that cannot be
    reached by those tools.

P.6 Breaches. The Supplier shall notify the Customer of a personal data breach
    without undue delay and at the latest within <time> of becoming aware of it.

P.7 End of contract. At the end of the contract the Supplier shall return the data
    as set out in <exit clause> and then delete it, including from backups within
    <time>, and confirm the deletion in writing.
```

## Personenbezogene Daten, die ein CMS enthält

Bitten Sie den Bieter, für P.2 mindestens Folgendes abzudecken, aus den [Datenschutzstandards](../../../../../eng-us/standards/privacy.md):

| Wo | Was |
|---|---|
| Benutzerkonten | Namen, E-Mail-Adressen, Anmeldedaten von Beschäftigten und registrierten Besuchern |
| Information Collector | alles, was Besucher in Formulare eingeben ([Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)) |
| E-Mail-Einstellungen und Einwilligungsprotokoll | Kategorien, Einwilligungsverlauf, der angezeigte Text ([E-Mail-Einstellungen](../../../../../eng-us/features/6.0/mail-preferences.md)) |
| Newsletter-Daten | Abonnenten; Statistiken je Person nur mit Einwilligung |
| Suchbegriffe | gespeichert, können personenbezogene Daten enthalten |
| Audit-Protokoll | handelnde Personen, gekürzte Adressen, nach einer festgelegten Zeit pseudonymisiert ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)) |
| Protokolle | Anfrageprotokolle, Fehlerprotokolle, Protokolle des Webservers (außerhalb von Exponential) |
| Sicherungen | alles oben Genannte |

## Was Exponential für die Klausel bietet

| Klausel | Funktion |
|---|---|
| P.3 | Einstellungen der Audit-Felder `[AuditPrivacySettings] Field[]`; `MaskIP=enabled` für Anfrageprotokolle; Schwärzung im Log-Betrachter; `MaxAgeDays` je Protokoll ([Rotation der Logdateien](../../../../../eng-us/features/6.0/log-rotation.md)); Erweiterungen Dritter standardmäßig nicht aktiv |
| P.5 | Export der E-Mail-Einstellungen (JSON, CSV), `./console exp:audit export --subject-user=<id>`, Löschung der Maildaten beim Entfernen eines Kontos, „Deaktivieren und verschieben“ zur Einschränkung ([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md)) |
| Lücke bei P.5 | Keine einzelne Funktion „alle meine Daten exportieren“ oder „mich löschen“; Formulare, Bestellungen und Inhalte werden gesondert erreicht |
| Lücke bei P.2 | Kein Cookie-Einwilligungsbanner im Kernel oder in mitgelieferten Designs |

## Hinweise für die bewertende Person

- Ein Bieter, der Analyse, CAPTCHA oder Anmeldung über soziale Netzwerke vorschlägt, sollte den Dritten und das
  Vorgehen zur Einwilligung nennen (P.3).
- Fragen Sie, wie der Bieter auf einer anderen Exponential-Website eine Auskunftsanfrage beantwortet hat, Schritt für
  Schritt, ohne Kundendaten.
- Die Seite des Hostings zu P.4 und P.7: [Compliance und DSGVO für Hoster](../../../hosting/sicherheit/compliance-und-dsgvo.md).

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Anforderungen an den Datenschutz](../anforderungen/anforderungen-an-datenschutz.md)
- [Klausel zu Hosting und Datenstandort](klausel-hosting-und-datenstandort.md)
- [Klausel zu Ausstieg und Datenrückgabe](klausel-ausstieg-und-datenrueckgabe.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
