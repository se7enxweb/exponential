---
title: "Ausschreibungsvorlage: Klausel zu Support und Wartung"
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 110
summary: Klauseltext für Support und Wartung einer Exponential-Website - Umfang, Kanäle, Upgrades innerhalb der gepflegten Linie, Eskalation an das vorgelagerte Projekt, Gewährleistung für individuelle Entwicklungen, Wissenstransfer und Berichte - mit Lücken zum Ausfüllen.
keywords: Support-Klausel, Wartung, Upgrades, Eskalation, vorgelagertes Projekt, Partner, 7x, Gewährleistung, Mängel, Helpdesk, Wissenstransfer, Ausschreibungsvorlage, support clause, maintenance, upgrades, escalation, upstream, warranty, defects, help desk, knowledge transfer, tender template
type: help
---
# Ausschreibungsvorlage: Klausel zu Support und Wartung

Diese Seite richtet sich an Beschaffungsstellen, die laufenden Support und Wartung einer Exponential-Website beauftragen.
Sie ergänzt die [Klausel zu Service-Levels](klausel-service-level.md), die Zeiten festlegt; diese Klausel legt Umfang und
Pflichten fest. Hintergrund: [Anforderungen an Support und Wartung](../anforderungen/anforderungen-an-support-und-wartung.md).

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Die Klausel](#die-klausel)
2. [Tabelle zum Umfang](#tabelle-zum-umfang)
3. [Eskalation über den Lieferanten hinaus](#eskalation-über-den-lieferanten-hinaus)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Die Klausel

```text
M. SUPPORT AND MAINTENANCE

M.1 Scope. The Supplier supports and maintains the items in <scope table>: the
    Exponential software, the extensions, the design and templates, the
    integrations and the hosting environment [if hosted by the Supplier].

M.2 Channels. Support requests are made through <ticket system / e-mail / phone>
    by the Customer's named contacts. Severity 1 incidents may also be reported by
    <phone number role>.

M.3 Corrective maintenance. The Supplier corrects defects in the delivered system.
    Defects in Custom Work are corrected without charge for <period> after
    acceptance [and thereafter under <terms>].

M.4 Preventive maintenance. The Supplier applies releases of the maintained
    Exponential line, extension releases and platform updates as set out in
    <patching clause>, and reports their effect.

M.5 Upgrades. The Supplier proposes a yearly upgrade plan by <month>. Upgrades
    within the maintained line are included; changes to a new major line are
    quoted separately.

M.6 Upstream issues. Where a defect is in the software rather than the delivery,
    the Supplier reports it to the maintainers through the documented channel,
    provides a workaround where possible, and applies the fix when released.

M.7 Small changes. <hours / days> per <period> of change work are included, used
    on request and reported.

M.8 Knowledge. The Supplier keeps the system documentation current (see
    <documentation clause>) and records every change in the repository.

M.9 Staff. The Supplier names the support team and its qualifications, and informs
    the Customer of changes.

M.10 Reporting. Monthly report as set out in <service levels clause>.
```

## Tabelle zum Umfang

| Element | Im Umfang | Hinweise |
|---|---|---|
| Kernel von Exponential | ja | gepflegte Linie 6.0 ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |
| Erweiterungen in `ActiveExtensions[]` | <ja / die aufgeführten> | führen Sie sie auf; Versionen auf der About-Seite (`/ezinfo/about`) |
| Design, Templates, Stylesheets | ja | |
| Integrationen | <Liste> | |
| Inhalte | <nein / Stunden für redaktionellen Support> | eigene Arbeit der Redaktion |
| Hosting-Plattform | <ja, wenn gehostet> | siehe Hosting-Klausel |
| Fragen der Redaktion („Wie mache ich …“) | <ja / begrenzt> | die Hilfe im Produkt beantwortet viele ([Hilfesystem](../../../../../eng-us/features/6.0/help-system.md)) |

## Eskalation über den Lieferanten hinaus

Lassen Sie den Weg im Angebot nennen. Dokumentierte Möglichkeiten, aus [Support erhalten](../../../projekt/unterstuetzung-erhalten.md):

| Weg | Zusage |
|---|---|
| Öffentlicher Issue-Tracker | keine; offen für alle ([Wenn etwas schiefgeht](../../../wenn-etwas-schiefgeht.md)) |
| Foren und Chats der Community | keine |
| Partner-Support des Projekts | Reaktionszeiten nach Partnerstufe, wie veröffentlicht ([Support-Stufen und Reaktionszeiten](../../../partner/support-stufen-und-reaktionszeiten.md)) |
| Bezahlter Support von 7x | mit 7x vereinbart ([Wer ist 7x?](../../../wer-ist-7x.md)) |
| Sicherheitsmeldungen | vertrauliche E-Mail, erste Antwort innerhalb eines Werktags |

## Hinweise für die bewertende Person

- M.5 verhindert, dass das jährliche Upgrade zu einer überraschenden Rechnung wird; budgetieren Sie es (siehe die Seiten
  zu den Gesamtbetriebskosten dieses Abschnitts zur Beschaffung).
- M.6 sollte nennen, wer das Issue eröffnet und wer die Korrektur testet; ein Bieter, der „wir warten auf das Projekt“
  antwortet, hat keinen Plan für eine Umgehung.

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Klausel zu Service-Levels](klausel-service-level.md)
- [Klausel zu Offenlegung von Schwachstellen und Patches](klausel-offenlegung-von-schwachstellen-und-patches.md)
- [Kunden aktuell halten (für Partner)](../../../partner/kunden-websites-aktuell-halten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
