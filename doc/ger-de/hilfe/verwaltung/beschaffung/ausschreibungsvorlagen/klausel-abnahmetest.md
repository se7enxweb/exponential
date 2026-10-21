---
title: "Ausschreibungsvorlage: Klausel zum Abnahmetest"
audience: government, project-manager, partner, admin
category: public-sector
order: 150
summary: Klauseltext für die Abnahme einer Exponential-Website - Testplan, Umgebungen, Tests zu Funktionen, Barrierefreiheit, Sicherheit, Leistung, Sprachen, Sicherung und Ausstieg, Mängelklassen und die Entscheidung über die Abnahme - mit den Prüfungen des Produkts, die Testende nutzen können.
keywords: Abnahmetest, Abnahmeklausel, Testplan, UAT, Mängel, Livegang, Test der Barrierefreiheit, Sicherheitstest, Wiederherstellungstest, Ausstiegstest, Leistung, Ausschreibungsvorlage, acceptance testing, acceptance clause, test plan, defects, go-live, accessibility test, security test, restore test, exit test, performance, tender template
type: help
---
# Ausschreibungsvorlage: Klausel zum Abnahmetest

Diese Seite richtet sich an Projektleitungen und Beschaffungsstellen, die entscheiden, wann eine gelieferte Website
abgenommen ist und Zahlungsmeilensteine fällig werden. Die Klausel legt fest, was getestet wird, von wem, und wie Mängel
die Abnahme verhindern. Sie verlinkt die eigenen Prüfungen des Produkts, damit Testende wissen, worauf sie achten
sollen.

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

## Inhalt
1. [Die Klausel](#die-klausel)
2. [Die Testbereiche](#die-testbereiche)
3. [Mängelklassen](#mängelklassen)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Die Klausel

```text
A. ACCEPTANCE

A.1 Test plan. The Supplier delivers a test plan <period> before acceptance
    testing, covering every requirement in <annex> and the test areas below. The
    Customer approves the plan.

A.2 Environment. Acceptance testing takes place on <staging environment>
    identical in software versions and configuration to production.

A.3 Execution. The Supplier runs the tests and records results; the Customer
    witnesses and may run any test itself. Results and evidence are delivered in
    <format>.

A.4 Defects. Defects are classified as in <defect classes>. Acceptance requires
    zero open defects of class 1 and 2, and an agreed plan for class 3.

A.5 Decision. The Customer accepts or rejects in writing within <period> after
    the test report. A rejection names the defects. Retesting follows the same
    rules.

A.6 Go-live check. Before go-live the Supplier completes the go-live checklist
    in <annex>.

A.7 Warranty period. Acceptance does not end the Supplier's obligation to
    correct defects found within <period>.
```

## Die Testbereiche

| Bereich | Was zu testen ist | Prüfungen und Seiten des Produkts |
|---|---|---|
| Funktionen | jede funktionale Anforderung, je Rolle | [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md), [Workflows](../../../../anleitungen/workflows.md) |
| Barrierefreiheit | Bewertung der öffentlichen Seiten im Umfang nach WCAG-EM; Nutzung des Editors mit Tastatur und Screenreader | [Klausel zur Barrierefreiheit](klausel-barrierefreiheit.md), [Die Barrierefreiheit testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md) |
| Sicherheitskonfiguration | jede Einstellung in der Anlage zur sicheren Konfiguration; Header; HTTPS | [Klausel zur Sicherheit](klausel-sicherheit.md), [13.16 Checkliste zum Livegang](../../../../installation/13-sicherheitshaertung.md#1316-checkliste-für-den-livegang) |
| Systemzustand | Zustandsprüfungen unter **Setup > Systeminformationen** ohne Fehler | [Systeminformationen](../../../../anleitungen/systeminformationen.md) |
| Integrität der Dateien | **Setup > Upgrade-Prüfung** listet Abweichungen vom Release | [Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md) |
| Integrität der Inhalte | defekte Beziehungen, fehlende Dateien | [Inhaltsprüfungen](../../../../../eng-us/features/6.0/content-checks.md), [Fehlende Dateien](../../../../anleitungen/fehlende-dateien.md) |
| Sprachen | jede Sprache und Schreibrichtung | [Klausel zur Mehrsprachigkeit](klausel-mehrsprachigkeit.md) |
| Leistung | Seitenzeiten unter der erwarteten Last | [Benchmarking](../../../../anleitungen/benchmarking.md) |
| Audit | Anmeldung, Veröffentlichen und Entfernen erscheinen im Audit-Protokoll; `./console exp:audit verify` besteht | [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md) |
| Sicherung und Wiederherstellung | eine vollständige Wiederherstellung in einer separaten Umgebung | [Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md) |
| Ausstieg | ein vollständiger Export, gegen die Zahlen geprüft | [Klausel zu Ausstieg und Datenrückgabe](klausel-ausstieg-und-datenrueckgabe.md) |
| Dokumentation | ein Dritter folgt der Installationsdokumentation | [Klausel zur Dokumentation](klausel-dokumentation.md) |

Eine Liste zum Start für Website-Ersteller steht in [Checkliste zum Start](../../../../../eng-us/help/creators/website-creators/launch-checklist.md);
die Version für Hoster in [Livegang einer neuen Kundenwebsite](../../../hosting/checklisten/checkliste-fuer-den-start-einer-kunden-website.md).

## Mängelklassen

| Klasse | Bedeutung (anpassen) | Verhindert die Abnahme |
|---|---|---|
| 1 | Sicherheitsschwäche, Datenverlust, Rechtsverstoß, Website unbenutzbar | ja |
| 2 | Eine „muss“-Anforderung nicht erfüllt; ein Fehler der Barrierefreiheit auf einer Seite im Umfang | ja |
| 3 | Ein „soll“ nicht erfüllt; ein Fehler mit Umgehung | nein, mit einem Plan |
| 4 | Kosmetisch | nein |

## Hinweise für die bewertende Person

- Bitten Sie Bieter um einen Beispiel-Testplan; bewerten Sie, wie er Barrierefreiheit, Wiederherstellung und Ausstieg
  abdeckt, die oft fehlen.
- Tests mit echten Inhalten (oder einer realistischen Stichprobe) finden Probleme, die Demo-Inhalte verbergen.

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Prüfbare Anforderungen schreiben](../anforderungen/pruefbare-anforderungen-schreiben.md)
- [Klausel zu Service-Levels](klausel-service-level.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
