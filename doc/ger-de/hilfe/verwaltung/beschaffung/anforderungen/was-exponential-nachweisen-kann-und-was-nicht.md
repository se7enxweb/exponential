---
title: Was Exponential nachweisen kann und was nicht
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 160
summary: Eine ehrliche Liste, entnommen den Seiten zu den Standards, was das Repository von Exponential einer bewertenden Stelle im öffentlichen Sektor belegen kann und was nicht - kein VPAT, keine ASVS-Bewertung, kein externes Audit, kein Cookie-Banner, keine Vorlage für eine Erklärung zur Barrierefreiheit - und wie Sie jede Lücke in einer Ausschreibung behandeln.
keywords: Lücken, Nachweise, VPAT, ACR, ASVS, Penetrationstest, Zertifizierung, Konformität, Cookie-Banner, Erklärung zur Barrierefreiheit, security.txt, signierte Commits, Grenzen, Ehrlichkeit, Ausschreibung, gaps, evidence, penetration test, certification, conformance, cookie banner, accessibility statement, signed commits, limitations, honesty, tender
type: help
---
# Was Exponential nachweisen kann und was nicht

Diese Seite richtet sich an Bewertende, die wissen müssen, was sie finden werden und was nicht, und an Bieter, die
ehrlich antworten möchten. Jeder Punkt stammt aus den Abschnitten „Known gaps“ und den Statuszeilen der
[Seiten zu den Standards](../../../../../eng-us/standards/README.md). Eine Lücke allein ist kein Grund, ein Produkt auszuschließen:
Viele Lücken werden durch Konfiguration oder durch die Arbeit des Lieferanten geschlossen. Ein Angebot, das behauptet,
was das Repository nicht zeigen kann, sollte aber Punkte verlieren.

## Inhalt
1. [Was das Repository nachweisen kann](#was-das-repository-nachweisen-kann)
2. [Was es nicht nachweisen kann](#was-es-nicht-nachweisen-kann)
3. [Wie Sie jede Lücke in einer Ausschreibung behandeln](#wie-sie-jede-lücke-in-einer-ausschreibung-behandeln)
4. [Fragen, die Übertreibungen aufdecken](#fragen-die-übertreibungen-aufdecken)

## Was das Repository nachweisen kann

| Bereich | Nachweise, die Sie selbst prüfen können |
|---|---|
| Barrierefreiheit der Administration admin4 und admin4l | Design-Tests in `tests/tests/design/`, in der CI ausgeführt; dokumentierte Kriterien je WCAG-Bereich ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)) |
| Gegenmaßnahmen zur Sicherheit je Kategorie der OWASP Top 10 | Einstellungen, Klassen und Tests je Zeile benannt ([Sicherheitsstandards](../../../../../eng-us/standards/security.md)) |
| Ein manipulationssicheres Audit-Protokoll | `./console exp:audit verify` ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)) |
| Datenschutzfunktionen (Einwilligungsprotokoll, Löschung beim Entfernen eines Kontos, Maskierung in Protokollen, Aufbewahrung) | [Datenschutzstandards](../../../../../eng-us/standards/privacy.md) |
| Unicode, Locales, rechts nach links | [Standards zur Internationalisierung](../../../../../eng-us/standards/internationalization.md) |
| Open-Source-Lizenz | `LICENSE`, `COPYRIGHT.md`, `composer.json` ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)) |
| Release-Richtlinie und unterstützte Versionen | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md), [Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md) |
| Export aller Inhalte in offenen Formaten | [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md) |
| Automatisierte Tests und CI | [Tests und kontinuierliche Integration](../../../../../eng-us/standards/testing-and-ci.md) |

## Was es nicht nachweisen kann

| Lücke | Quelle |
|---|---|
| **Kein VPAT und kein Accessibility Conformance Report**; Status für Section 508 „Unverified“ | [Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md) |
| **Keine Bewertung nach EN 301 549**; Status „Unverified“; Klausel 11.8 für Autorenwerkzeuge nicht geprüft | ebenda |
| **Öffentliche Designs nicht** auf Barrierefreiheit **geprüft**; Online-Editor nicht geprüft | ebenda |
| **Keine Vorlage für eine Erklärung zur Barrierefreiheit** für Websites | ebenda |
| **Keine automatisierte Prüfung der Barrierefreiheit in der CI**; Läufe mit axe erfolgen manuell | ebenda |
| **Keine Bewertung nach OWASP ASVS**; Status „Unverified“; Standard-Mindestlänge des Passworts (10) unter ASVS Stufe 1 (12) | [Sicherheitsstandards](../../../../../eng-us/standards/security.md) |
| **Keine externe Sicherheitsbewertung** dokumentiert („Complies“ bräuchte eine) | ebenda |
| **Kontosperre standardmäßig aus**; zweistufige Anmeldung auf Wunsch | ebenda |
| **Keine Prüfung der Abhängigkeiten auf Schwachstellen in der CI** | ebenda |
| **Keine `security.txt`** mitgeliefert | ebenda |
| **CSP standardmäßig nicht durchgesetzt** (Report-only in der Administration, aus auf Websites) | ebenda |
| **Kein Cookie-Einwilligungsbanner** | [Datenschutzstandards](../../../../../eng-us/standards/privacy.md) |
| **Keine einzelne Funktion „alle meine Daten exportieren“ oder „mich löschen“** | ebenda |
| **Kernel-Protokolle ohne Altersgrenze** in der Standardeinstellung | ebenda |
| **Kein getaggtes PDF** (PDF/UA Does not comply) | [Standards zu Dokumentation und Veröffentlichung](../../../../../eng-us/standards/documentation-and-publishing.md) |
| **Keine Unterstützung von Pluralformen** in Übersetzungen der Oberfläche | [Standards zur Internationalisierung](../../../../../eng-us/standards/internationalization.md) |
| **Keine OpenAPI-Beschreibung** der REST API des Kernels | [Standards zu APIs und Protokollen](../../../../../eng-us/standards/apis-and-protocols.md) |
| **Unsignierte Commits und Tags**; kein Branch-Schutz auf `main` | [Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md) |
| **Keine veröffentlichte Codeabdeckung** | [Tests und kontinuierliche Integration](../../../../../eng-us/standards/testing-and-ci.md) |
| **Kein veröffentlichtes SLA** des Projekts | [SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md) |

## Wie Sie jede Lücke in einer Ausschreibung behandeln

| Art der Lücke | Behandlung |
|---|---|
| Durch Konfiguration geschlossen (Kontosperre, Passwortlänge, Durchsetzung der CSP, HSTS, `security.txt`) | Machen Sie die Konfiguration zur Lieferanforderung; prüfen Sie sie bei der Abnahme. |
| Durch die Arbeit des Lieferanten geschlossen (Barrierefreiheit des öffentlichen Designs, Erklärung zur Barrierefreiheit, Cookie-Einwilligung) | Nehmen Sie die Arbeit und ihre Nachweise (zum Beispiel eine Bewertung nach WCAG-EM) in den Leistungsumfang auf. |
| Durch eine externe Bewertung geschlossen (ASVS, Penetrationstest, Bewertung nach EN 301 549) | Budgetieren Sie sie gesondert; Sie wählen die prüfende Stelle. |
| Durch Ihre Organisation geschlossen (Aktenplan, Datenschutzhinweis) | Behalten Sie es in Ihrem eigenen Projektplan. |
| In der Vertragslaufzeit nicht zu schließen (signierte Commits im Projekt, Pluralformen) | Halten Sie das Risiko fest; fragen Sie den Bieter nach seiner Sicht; bewerten Sie entsprechend. |

## Fragen, die Übertreibungen aufdecken

- „Senden Sie das VPAT für die angebotene Version.“ Die ehrliche Antwort für Exponential ist, dass keines existiert.
- „Welche ASVS-Stufe wurde bewertet, von wem, wann?“ Keine.
- „Welche Einstellungen werden auf unserer Website von den mitgelieferten Standardwerten abweichen?“ Ein Bieter, der
  keine nennen kann, hat die Härtung nicht geplant.
- „Zeigen Sie die Bewertung der Barrierefreiheit des vorgeschlagenen öffentlichen Designs.“ Mitgelieferte öffentliche
  Designs haben keine.

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Anforderungen Nachweisen zuordnen](anforderungen-nachweisen-zuordnen.md)
- [Anforderungen an die Barrierefreiheit](anforderungen-an-die-barrierefreiheit.md)
- [Sicherheitsanforderungen](sicherheitsanforderungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
