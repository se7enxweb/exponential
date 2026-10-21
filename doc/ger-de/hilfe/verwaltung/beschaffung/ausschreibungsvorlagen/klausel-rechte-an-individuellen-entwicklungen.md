---
title: "Ausschreibungsvorlage: Klausel zu Rechten an individuellen Entwicklungen"
audience: government, project-manager, partner, developer
category: public-sector
order: 80
summary: Klauseltext für die Rechte an eigenen Erweiterungen, Designs, Integrationen und Dokumentation, die unter einem Vertrag über ein CMS bezahlt werden, passend zur GPL, die Erweiterungen von Exponential übernehmen, mit Optionen, die Arbeit zur Wiederverwendung durch andere öffentliche Stellen zu veröffentlichen.
keywords: geistiges Eigentum, individuelle Entwicklungen, Urheberrecht, GPL, abgeleitetes Werk, Erweiterung, Design, veröffentlichen, Wiederverwendung, Public Code, Lizenzeinräumung, Übertragung, Ausschreibungsvorlage, intellectual property, custom work, copyright, derivative work, extension, design, publish, reuse, public code, license grant, assignment, tender template
type: help
---
# Ausschreibungsvorlage: Klausel zu Rechten an individuellen Entwicklungen

Diese Seite richtet sich an Beschaffungsstellen und Rechtsberatung, die entscheiden, wer die individuelle Arbeit eines
Lieferanten nutzen, ändern und veröffentlichen darf: Erweiterungen, Designs, Templates, Integrationen, Migrationsskripte
und Dokumentation. Bei Exponential bestimmt die GPL die Möglichkeiten: Die Fragen zur Lizenzierung des Projekts erklären,
dass Erweiterungen und Designs abgeleitete Werke sind und, wenn sie weitergegeben werden, unter der GPL Version 2 oder
neuer weitergegeben werden ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)).

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Zwei Modelle](#zwei-modelle)
2. [Die Klausel](#die-klausel)
3. [Zur GPL passend](#zur-gpl-passend)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Zwei Modelle

| Modell | Der Kunde erhält | Passt, wenn |
|---|---|---|
| **Lizenzeinräumung** | die Arbeit unter der GPL Version 2 oder neuer (Code) und einer offenen Lizenz für Dokumentation; der Lieferant behält das Urheberrecht und darf die Arbeit wiederverwenden | Sie die Freiheiten wollen und damit einverstanden sind, dass der Lieferant wiederverwendet, was er gebaut hat |
| **Übertragung** | das Urheberrecht an der individuellen Arbeit, dann so weiterlizenziert, wie der Kunde entscheidet (innerhalb der GPL bei Code, der ein abgeleitetes Werk ist) | Ihre Regeln das Eigentum an bezahlter Arbeit verlangen |

In beiden Fällen kann der Kunde die Arbeit veröffentlichen, und andere Stellen können sie wiederverwenden, weil sich
GPL-Code weitergeben lässt.

## Die Klausel

```text
IP. CUSTOM WORK

IP.1 Definition. "Custom Work" means all software, templates, stylesheets, scripts,
     designs, content type definitions, configuration, migration scripts and
     documentation created by the Supplier for the Customer under this contract.

IP.2 [Option A - license] The Supplier licenses all Custom Work that is software
     to the Customer under the GNU General Public License version 2 or any later
     version, and all Custom Work that is documentation under <documentation
     license>. The Supplier delivers it in source form as set out in <source
     clause>.

IP.2 [Option B - assignment] The Supplier assigns to the Customer all copyright in
     the Custom Work on payment of <milestone>. Software in the Custom Work that is
     a derivative work of GPL software remains subject to the GPL.

IP.3 Third-party material. The Supplier shall not include material in the Custom
     Work that the Customer cannot use, change and publish under IP.2, and shall
     list every third-party asset (fonts, images, icons, libraries) with its
     license.

IP.4 Publication. The Customer may publish the Custom Work [in a public
     repository] [and offer it to the maintainers of the software]. The Supplier
     shall remove secrets and personal data from the repository before delivery.

IP.5 Pre-existing work. Work the Supplier created before this contract and uses in
     the delivery is licensed to the Customer under [the GPL version 2 or later /
     <license>]; the Supplier lists it in <annex>.

IP.6 Moral rights and credits. <as required by applicable law>.
```

## Zur GPL passend

- Code, der die PHP-Klassen von Exponential verwendet, und JavaScript, das mit dessen GPL-JavaScript arbeitet, muss bei
  der Weitergabe unter der GPL oder einer GPL-kompatiblen Lizenz stehen. Bilder und andere Dateien, die PHP nur an
  Browser sendet, sind Daten und nicht betroffen ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)).
- Eine Agentur, die eine Website für ihren Kunden baut, handelt als dessen Beauftragte, was laut den Fragen keine
  Weitergabe ist. Deshalb räumt IP.2 die Lizenz ausdrücklich ein, statt sich auf eine Weitergabe zu verlassen.
- Eine Bibliothek unter einer GPL-inkompatiblen Lizenz darf in weitergegebenem Code nicht in denselben PHP-Prozess
  geladen werden; IP.3 erkennt das.
- Die Dokumentation von Exponential selbst steht unter der GNU FDL 1.3 oder neuer
  ([Die Lizenz dieser Dokumentation](../../../../../eng-us/README-LICENSE.md)); eigene Dokumentation, die daraus kopiert, übernimmt
  diese Lizenz.
- Um die Arbeit mit der Community zu teilen, siehe [Erweiterungen zurückgeben](../../../erweiterungen-zurueckgeben.md) und
  [Eine Erweiterung veröffentlichen](../../../partner/eine-erweiterung-veroeffentlichen.md).

## Hinweise für die bewertende Person

- Option A ist bei Open-Source-Lieferanten üblich und kostet weniger; Option B kann von Ihren Regeln verlangt werden.
- Prüfen Sie, ob die Liste der Bestandteile nach IP.3 für das Design vollständig ist: Schriften sind die übliche
  Überraschung.

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Klausel zu Open-Source-Lizenz und Verfügbarkeit des Quellcodes](klausel-open-source-lizenz-und-quellcode.md)
- [Anforderungen an Open Source und Lizenzierung](../anforderungen/anforderungen-an-open-source-und-lizenzierung.md)
- [Lizenzen lesen, bevor Sie etwas wiederverwenden](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
