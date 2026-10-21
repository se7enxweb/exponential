---
title: "Ausschreibungsvorlage: Klausel zu Open-Source-Lizenz und Verfügbarkeit des Quellcodes"
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 70
summary: Klauseltext, der für das gelieferte System eine Open-Source-Lizenz verlangt, die Lieferung des vollständigen Quellcodes in einem Versionsverwaltungs-Repository, eine Liste der Komponenten und Lizenzen und die Freiheit, das System von jedem pflegen zu lassen - mit den Fakten zur Lizenz von Exponential.
keywords: Open-Source-Klausel, Lizenz, GPL, GPL-2.0-or-later, Lieferung des Quellcodes, Repository, Hinterlegung, Komponentenliste, SBOM, Lizenzen Dritter, Open Source Definition, Ausschreibungsvorlage, open source clause, license, source code delivery, repository, escrow, component list, third-party licenses, tender template
type: help
---
# Ausschreibungsvorlage: Klausel zu Open-Source-Lizenz und Verfügbarkeit des Quellcodes

Diese Seite richtet sich an Beschaffungsstellen und Rechtsberatung, die wollen, dass das gelieferte System tatsächlich
Open Source ist, nicht nur dem Namen nach: Die Lizenz erlaubt die Freiheiten, der Kunde hat den Quellcode, und niemand
anderes muss zustimmen, damit er gepflegt werden kann. Hintergrund: [Anforderungen an Open Source und Lizenzierung](../anforderungen/anforderungen-an-open-source-und-lizenzierung.md).

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Die Klausel](#die-klausel)
2. [Fakten zur Lizenz von Exponential für die Antwort des Bieters](#fakten-zur-lizenz-von-exponential-für-die-antwort-des-bieters)
3. [Die Komponentenliste](#die-komponentenliste)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Die Klausel

```text
L. LICENSE AND SOURCE CODE

L.1 License. The software on which the system is built shall be licensed under a
    license that meets the Open Source Definition of the Open Source Initiative.
    The Supplier shall name the license of every component.

L.2 No license fees. No license fee is payable for the software, unless listed in
    the pricing schedule with the component, its license and the reason.

L.3 Source delivery. The Supplier shall deliver the complete source code of the
    system as run in production - core, extensions, designs, templates,
    stylesheets, scripts in their unminified form, build files, configuration
    templates without secrets, and deployment scripts - in a version control
    repository with full history, controlled by the Customer, at <each release /
    every <interval>> and at the end of the contract.

L.4 Reproducibility. The delivery under L.3 shall allow a competent third party to
    build and install the system using only the repository and the documentation.
    The Supplier shall demonstrate this <once / at acceptance>.

L.5 Components. The Supplier shall maintain a list of every third-party component
    with its version, license and source location, and update it with each release.

L.6 Freedom to maintain. The Customer may have the system maintained, changed or
    hosted by any party, without the Supplier's consent and without fees to the
    Supplier.

L.7 Contributions. [The Supplier shall offer fixes of general interest to the
    maintainers of the software under their license.]
```

## Fakten zur Lizenz von Exponential für die Antwort des Bieters

| Punkt | Fakt | Quelle |
|---|---|---|
| L.1 | GNU GPL Version 2 oder jede spätere Version (`GPL-2.0-or-later`) | [Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md), [GPL v2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) |
| L.1 | Erfüllt als GPL-Lizenz die [Open Source Definition](https://opensource.org/osd) | |
| L.2 | Keine Lizenzgebühr; Entgelte gibt es für Kopien, Installation, Support und Updates | [Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md) |
| L.3 | Der Code ist öffentlich auf GitHub unter `se7enxweb` und wird mit Composer oder Git-Klonen mit vollständiger Historie installiert | [Den Code holen](../../../../installation/03-den-code-beschaffen.md), [Mit Git entwickeln](../../../mit-git-entwickeln.md) |
| L.3 | Bei JavaScript und CSS bedeutet „Quellcode“ die nicht minimierte Datei oder das Original, aus dem sie gebaut wurde | [Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md) |
| L.5 | Erweiterungen geben Version und Lizenz in `ezinfo.php` und `extension.xml` an; die About-Seite listet sie auf | [About- und Paketseiten](../../../../../eng-us/features/6.0/about-and-package-pages.md) |
| L.7 | Wie Sie eine Erweiterung zurückgeben | [Erweiterungen zurückgeben](../../../erweiterungen-zurueckgeben.md) |

## Die Komponentenliste

Eine praktische Liste für L.5 bei einer Exponential-Website hat diese Quellen:

- `composer.json` und `composer.lock` des Projekts (PHP-Pakete und ihre angegebenen Lizenzen);
- das Verzeichnis `extension/`, mit der `extension.xml` jeder Erweiterung;
- die eigenen Schriften, Icons und Bilder des Designs, deren Lizenzen der Designer angeben muss;
- vom Design mitgelieferte JavaScript-Bibliotheken.

**Setup > Updates and packages** und die About-Seite in der Administration (`/ezinfo/about`) zeigen installierte Pakete
und Versionen der Erweiterungen.

## Hinweise für die bewertende Person

- L.3 ist die Klausel, die in der Praxis zählt. Eine Lizenz gibt Rechte; das Repository gibt die Fähigkeit.
- Fragen Sie den Bieter, ob ein Teil des Angebots geschlossen ist; bewerten Sie ehrlich angegebene geschlossene Teile
  besser als nicht angegebene, die später gefunden werden.

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Klausel zu Rechten an individuellen Entwicklungen](klausel-rechte-an-individuellen-entwicklungen.md)
- [Klausel zur Zusicherung ohne Lock-in](klausel-zusicherung-ohne-lock-in.md)
- [Die Lizenz dieser Dokumentation](../../../../../eng-us/README-LICENSE.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
