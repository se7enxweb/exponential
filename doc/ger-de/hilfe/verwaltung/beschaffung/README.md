---
title: Exponential im öffentlichen Sektor beschaffen
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 10
summary: Der Bereich Beschaffung für öffentliche Stellen, die ein Content-Management-System, Leistungen rund um Exponential oder beides einkaufen - Anforderungen, Ausschreibungsvorlagen, Bewertung, Gesamtbetriebskosten, Ausstiegspläne, Schulung und der Lebenszyklus des Produkts, mit dem, was das Repository belegen kann und was nicht.
keywords: Beschaffung, Vergabe, Ausschreibung, Leistungsbeschreibung, Anforderungen, Bewertung, Wertung, Gesamtbetriebskosten, TCO, Ausstiegsplan, Anbieterabhängigkeit, Lock-in, Schulung, Lebenszyklus, öffentlicher Sektor, Verwaltung, ein CMS einkaufen, procurement, tender, RFP, request for proposal, requirements, evaluation, scoring, total cost of ownership, exit plan, lock-in, training, lifecycle, public sector, government, buying a CMS
type: help
---
# Exponential im öffentlichen Sektor beschaffen

Dieser Bereich richtet sich an Beschaffungsstellen, Projektverantwortliche und die Fachleute, die in einem
Bewertungsgremium einer öffentlichen Stelle sitzen: einem Ministerium, einer Behörde, einer Kommune, einem Schulträger.
Er beantwortet die Fragen, die eine öffentliche Beschaffung eines Web-Content-Management-Systems aufwirft, von der ersten
Anforderung bis zum Ende des Vertrags, und sagt für jede, was die eigene Dokumentation von Exponential belegen kann. Die
Fakten stammen aus den Seiten zu den Standards in `doc/eng-us/standards/`, dem Installationsbuch in `doc/eng-us/install/`,
den Bereichen zu Partnern und Hosting dieser Hilfe und `SECURITY.md`.

> **Hinweis** Dieser Bereich beschreibt Funktionen und übliche Praxis; er ist keine Rechtsberatung. Die öffentliche
> Beschaffung unterliegt dem Recht und den Regeln Ihrer Organisation. Prüfen Sie sie mit Ihrer Vergabe- und
> Rechtsberatung.

## Inhalt

1. [Was Sie einkaufen](#was-sie-einkaufen)
2. [Die Teile dieses Bereichs](#die-teile-dieses-bereichs)
3. [Lesereihenfolge](#lesereihenfolge)
4. [Was das Repository belegen kann und was nicht](#was-das-repository-belegen-kann-und-was-nicht)
5. [Verwandte Seiten](#verwandte-seiten)

## Was Sie einkaufen

Exponential selbst ist freie Software unter der GNU General Public License, Version 2 oder neuer
([Fragen zur Lizenzierung](../../projekt/lizenz-faq.md)). Es gibt keine Lizenzgebühr zu vergleichen. Was eine
öffentliche Stelle beschafft, ist alles rundherum:

| Sie können beschaffen | Bei wem | Wo diese Hilfe es beschreibt |
|---|---|---|
| Implementierung: Inhaltsmodell, Design, Templates, Migration | ein Implementierungspartner oder eine Agentur | [Implementierungspartner](../../partner/implementierungspartner.md) |
| Hosting und Betrieb | ein Hosting-Unternehmen oder Ihr eigenes Rechenzentrum | [Hosting-Unternehmen: hier beginnen](../../hosting/hosting-anbieter-hier-beginnen.md) |
| Support mit vereinbarten Reaktionszeiten | 7x oder ein Partner | [Support erhalten](../../projekt/unterstuetzung-erhalten.md), [Support-Stufen und Reaktionszeiten](../../partner/support-stufen-und-reaktionszeiten.md) |
| Schulung für Redaktion und Administration | ein Schulungspartner oder Ihre eigenen Trainerinnen und Trainer | [Kurskatalog](../../../../eng-us/help/trainers/course-catalogue.md) |
| Prüfungen der Barrierefreiheit oder der Sicherheit | eine unabhängige prüfende Stelle | [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md), [Sicherheitsstandards](../../../../eng-us/standards/security.md) |

Weil die Software gleich bleibt, wer auch immer die Leistungen erbringt, kann eine öffentliche Stelle ihren Lieferanten
wechseln, ohne ihr System zu wechseln. Das ist das stärkste Argument gegen eine Anbieterabhängigkeit, und der Teil zu den
Ausstiegsplänen in diesem Bereich zeigt, wie Sie es im Vertrag aufrechterhalten.

## Die Teile dieses Bereichs

| Teil | Er beantwortet |
|---|---|
| [Anforderungen](anforderungen/README.md) | Wie Sie prüfbare Anforderungen an ein öffentliches CMS schreiben und welche Nachweise das Repository für jede liefert |
| [Ausschreibungsvorlagen](ausschreibungsvorlagen/README.md) | Klauseltexte für eine Ausschreibung oder Angebotsaufforderung, mit anzupassenden Platzhaltern |
| [Bewertung](bewertung/README.md) | Checklisten, Bewertungsbögen, Demo-Skripte, ein Plan für einen Proof of Concept und Referenzprüfungen |
| [Gesamtbetriebskosten](../../../../eng-us/help/government/procurement/total-cost-of-ownership/README.md) | Ein Kostenmodell mit Kategorien und durchgerechneten Beispielen, deren Zahlen alle als Annahmen gekennzeichnet sind |
| [Ausstiegspläne](ausstiegsplaene/README.md) | Wie Sie das Ende eines Vertrags planen: Exportformate, Übergabe, Übergang und Klauseln gegen Lock-in |
| [Schulung](../../../../eng-us/help/government/procurement/training/README.md) | Ein Schulungsplan für eine öffentliche Stelle, aufbauend auf den Kursen für Trainerinnen und Trainer dieser Hilfe |
| [Lebenszyklus](lebenszyklus/README.md) | Release-Richtlinie, unterstützte Versionen, Unterstützung von PHP und wie Sie Upgrades im Haushalt planen |

## Lesereihenfolge

1. Beginnen Sie mit den [Anforderungen](anforderungen/README.md): Eine Ausschreibung ist nur so gut wie ihre
   Anforderungen.
2. Machen Sie daraus mit den [Ausschreibungsvorlagen](ausschreibungsvorlagen/README.md) Klauseln.
3. Bereiten Sie das Gremium mit der [Bewertung](bewertung/README.md) vor, bevor die Angebote eingehen.
4. Vergleichen Sie Angebote nach den [Gesamtbetriebskosten](../../../../eng-us/help/government/procurement/total-cost-of-ownership/README.md), nicht nach dem Preis
   des ersten Jahres.
5. Schreiben Sie den [Ausstiegsplan](ausstiegsplaene/README.md) vor der Unterzeichnung, nicht erst, wenn der Vertrag endet.
6. Planen Sie [Schulung](../../../../eng-us/help/government/procurement/training/README.md) und den [Lebenszyklus](lebenszyklus/README.md) des Produkts für die gesamte
   Laufzeit ein.

Die Checklisten „Vor einer Ausschreibung“ und „Vor der Unterzeichnung“ im Bereich der Checklisten für Verwaltungen
fassen jede Stufe auf einer Seite zusammen.

## Was das Repository belegen kann und was nicht

Bewertungsgremien verlangen oft Dokumente, die ein kommerzieller Hersteller erstellt. Das Exponential-Projekt
veröffentlicht seine Nachweise stattdessen im Repository und sagt deutlich, wo etwas fehlt:

| Frage | Was es gibt | Was es nicht gibt |
|---|---|---|
| Barrierefreiheit | [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md): WCAG 2.2 AA **Partly complies** für die Administration admin4 und admin4l; EN 301 549 und Section 508 **Unverified** | Kein VPAT und kein Accessibility Conformance Report; öffentliche Website-Designs nicht geprüft; keine Vorlage für eine Erklärung zur Barrierefreiheit |
| Sicherheit | [Sicherheitsstandards](../../../../eng-us/standards/security.md): Zuordnung zu den OWASP Top 10, drei dokumentierte Härtungsdurchgänge, manipulationssicheres Audit-Protokoll | Keine Bewertung nach OWASP ASVS; kein Bericht eines externen Penetrationstests im Repository; keine mitgelieferte `security.txt` |
| Datenschutz | [Datenschutzstandards](../../../../eng-us/standards/privacy.md): Einwilligungsnachweise, Löschung beim Entfernen eines Kontos, Maskierung und Aufbewahrung von Protokollen | Keine einzelne Funktion „alle meine Daten exportieren“; kein Cookie-Banner |
| Umgang mit Schwachstellen | [`SECURITY.md`](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md): vertrauliche Meldung, erste Antwort innerhalb eines Werktags | Keine veröffentlichte Zusage zur Behebungszeit; Korrekturen erscheinen als nächstes Release |
| Release-Richtlinie | [Quellcodeverwaltung und Releases](../../../../eng-us/standards/source-control-and-releases.md): dauerhafte Tags, Changelogs | Commits und Tags sind nicht signiert |
| Service-Level | keine vom Projekt; Partner und Hoster veröffentlichen ihre eigenen | Das Projekt veröffentlicht kein SLA ([SLA-Optionen für Hosting](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)) |

Verlangen Sie von den Bietern, die fehlenden Teile als Teil ihres Angebots zu liefern (zum Beispiel eine Prüfung der
Barrierefreiheit der Website, die sie bauen), statt sie vom Open-Source-Projekt zu erwarten.

## Verwandte Seiten

- Verwaltungen: hier beginnen (die Einstiegsseite dieses Bereichs der Hilfe)
- [Fragen zur Lizenzierung: die GPL und Exponential](../../projekt/lizenz-faq.md)
- [Wer ist 7x?](../../wer-ist-7x.md) und [Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md)
- [Partner: hier beginnen](../../partner/partner-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
