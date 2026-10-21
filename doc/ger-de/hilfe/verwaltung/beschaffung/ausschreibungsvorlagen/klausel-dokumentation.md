---
title: "Ausschreibungsvorlage: Klausel zur Dokumentation"
audience: government, project-manager, partner, admin
category: public-sector
order: 130
summary: Klauseltext für die Dokumentation, die ein Lieferant mit einer Exponential-Website liefert - Systembeschreibung, Inhaltsmodell, Rollen, Abweichungen der Konfiguration, Betriebshandbücher, Leitfäden für die Redaktion - in bearbeitbarer Form unter einer offenen Lizenz, aufbauend auf der freien Produktdokumentation.
keywords: Klausel zur Dokumentation, Systemdokumentation, Übergabe, Runbooks, Betriebshandbücher, Inhaltsmodell, Konfiguration, Leitfaden für die Redaktion, Ist-Dokumentation, GFDL, offene Lizenz, Ausschreibungsvorlage, documentation clause, system documentation, handover, runbooks, content model, configuration, editor guide, as-built, open license, tender template
type: help
---
# Ausschreibungsvorlage: Klausel zur Dokumentation

Diese Seite richtet sich an Beschaffungsstellen und IT-Leitungen, die eine Dokumentation wollen, mit der ein anderes
Team übernehmen kann. Das Produkt selbst ist im Repository dokumentiert, in `doc/`, wird in der Hilfe der Administration
angezeigt und ist freie Dokumentation unter der GNU FDL 1.3 oder neuer ([Die Lizenz dieser Dokumentation](../../../../../eng-us/README-LICENSE.md)).
Der Lieferant muss das Produkt also nicht neu dokumentieren: Die Klausel verlangt, was für Ihre Website spezifisch ist.

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

## Inhalt
1. [Die Klausel](#die-klausel)
2. [Der Dokumentationssatz](#der-dokumentationssatz)
3. [Die Produktdokumentation nutzen](#die-produktdokumentation-nutzen)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Die Klausel

```text
D. DOCUMENTATION

D.1 Deliverables. The Supplier delivers the documentation set in <annex> at
    acceptance and keeps it current with every release to production.

D.2 Form. Documentation is delivered in an editable, open format (for example
    Markdown in the system's repository) and is licensed to the Customer under
    <license> so that the Customer and its future suppliers may change it.

D.3 Reference, do not copy. Where the published product documentation covers a
    topic, the Supplier links the exact page and documents only what is specific
    to the Customer's system.

D.4 Language. Documentation for editors is in <languages>; technical
    documentation in <languages>.

D.5 Accessibility. Documentation meets <accessibility standard for documents>.

D.6 Verification. The Customer may ask a third party to install or restore the
    system using only the documentation; gaps found are defects.
```

## Der Dokumentationssatz

| Dokument | Inhalt | Seiten des Produkts, die statt einer Kopie zu verlinken sind |
|---|---|---|
| Systemüberblick | Siteaccesses, Designs, Erweiterungen, Integrationen, Umgebungen | [Einführung](../../../../installation/01-einfuehrung.md), [Erweiterungen](../../../../anleitungen/extensions.md) |
| Inhaltsmodell | Inhaltstypen, Felder, Sektionen, Objektzustände, Workflows | [Inhaltsmodell](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md), [Sektionen](../../../../anleitungen/sektionen.md), [Objektzustände](../../../../anleitungen/objektzustaende.md), [Workflows](../../../../anleitungen/workflows.md) |
| Rollen und Berechtigungen | jede Rolle, ihre Richtlinien, wer sie innehat | [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) |
| Konfigurationsnachweis | jede Einstellung, die vom mitgelieferten Standardwert abweicht, mit Begründung | [Die Einstellungsseite](../../../../anleitungen/einstellungsseite.md) |
| Installation und Deployment | wie aus dem Repository gebaut und ausgerollt wird | [Ausrollen](../../../../anleitungen/bereitstellung.md), [Exponential installieren: das Buch](../../../../installation/README.md) |
| Betriebshandbücher | Caches, Cronjobs, Sicherungen, Wiederherstellung, Störungen | [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md), [Hosting-Runbooks](../../../hosting/runbooks/README.md) |
| Sicherheitsnachweis | durchgeführte Härtung, Verzeichnis der Geheimnisse (nicht die Geheimnisse) | [Sicherheitshärtung für den Produktivbetrieb](../../../../installation/13-sicherheitshaertung.md) |
| Leitfaden für die Redaktion | auf die Website zugeschnittene Anleitungen für Ihre Inhaltstypen | [Inhalte bearbeiten](../../../inhalte-bearbeiten.md), [Veröffentlichen und Versionen](../../../veroeffentlichen-und-versionen.md) |
| Entwurf der Erklärung zur Barrierefreiheit | nach der Klausel zur Barrierefreiheit | [Klausel zur Barrierefreiheit](klausel-barrierefreiheit.md) |

## Die Produktdokumentation nutzen

- Redaktion und Administratoren lesen die Hilfe in der Administration (**Hilfe-Center**, `/exphelp/dashboard`), und die
  Schaltfläche „?“ öffnet die Hilfe zur aktuellen Seite ([Hilfesystem](../../../../../eng-us/features/6.0/help-system.md)).
- Die Hilfe lässt sich exportieren, zum Beispiel als EPUB ([Standards zu Dokumentation und Veröffentlichung](../../../../../eng-us/standards/documentation-and-publishing.md)).
- Weil die Produktdokumentation unter der GFDL steht, darf Ihre eigene Website-Dokumentation sie zitieren und anpassen
  und muss diese Lizenz für die angepassten Teile beibehalten ([Freie Dokumentation](../../../freie-dokumentation.md)).

## Hinweise für die bewertende Person

- Bitten Sie um einen Beispiel-Konfigurationsnachweis aus einem früheren Projekt, ohne Kundendaten.
- D.6 ist der stärkste Test; planen Sie ihn vor dem Ende der Gewährleistung ein.

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Klausel zur Schulung](klausel-schulung.md)
- [Klausel zu Ausstieg und Datenrückgabe](klausel-ausstieg-und-datenrueckgabe.md)
- [Übergabe an die Redaktion](../../../../../eng-us/help/creators/website-creators/handing-over-to-editors.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
