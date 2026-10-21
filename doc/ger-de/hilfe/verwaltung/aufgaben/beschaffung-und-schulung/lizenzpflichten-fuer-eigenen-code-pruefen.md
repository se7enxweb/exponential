---
title: Lizenzpflichten für eigenen Code prüfen
audience: government, project-manager, partner, admin
category: public-sector
order: 240
summary: Schritt für Schritt prüfen, dass eigener Code, der für die Exponential-Site einer Behörde geliefert wird, die GPL einhält und Ihnen die nötigen Rechte gibt - Quellcode erhalten, Lizenzen von Erweiterungen und Bibliotheken, Ressourcen Dritter und Weitergabe an andere Behörden - auf Grundlage der FAQ zur Lizenzierung.
keywords: Prüfung der Lizenzen, GPL, eigener Code, Erweiterungen, Bibliotheken, Kompatibilität, Quellcode, Ressourcen Dritter, Weitergabe, Aufgabe zur Beschaffung, license check, custom code, extensions, libraries, compatibility, source code, third-party assets, sharing, procurement task
type: help
---
# Lizenzpflichten für eigenen Code prüfen

**Wer:** der Projektleiter mit einem Entwickler, und die Rechtsberatung in Zweifelsfällen. **Wann:** bei der Lieferung
und vor der Abnahme. Die Regeln stammen aus
[Fragen zur Lizenzierung: die GPL und Exponential](../../../projekt/lizenz-faq.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie Ihren
> eigenen Fall mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Ergebnis](#ergebnis)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Die Liste der gelieferten Erweiterungen, Designs und Bibliotheken.
- Die Vertragsklauseln zu Eigentum und Quellcode
  ([Klausel zum geistigen Eigentum an eigener Arbeit](../../../../../eng-us/help/government/procurement/tender-templates/intellectual-property-of-custom-work-clause.md)).

## Schritte

1. **Prüfen Sie, ob Sie den Quellcode** von allem **erhalten haben**: PHP und Templates, wie geschrieben, JavaScript
   und CSS mit ihren nicht minifizierten Originalen, Bilder in ihrer bearbeitbaren Form
   ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md#was-bedeutet-quellcode-bei-einer-webanwendung)).
2. **Prüfen Sie die Lizenz jeder Erweiterung**: Ihre `ezinfo.php`, `extension.xml` oder `composer.json` nennt sie; die
   Seite Über listet Erweiterungen mit ihrer Lizenz
   ([Seiten Über und Pakete](../../../../../eng-us/features/6.0/about-and-package-pages.md)). Code, der Exponential erweitert und
   an Sie verbreitet wird, steht unter der GPL Version 2 oder später.
3. **Prüfen Sie Bibliotheken**, die in denselben PHP-Prozess geladen werden: Sie müssen GPL-kompatibel sein;
   Bibliotheken, die nur über das Netzwerk erreicht werden, sind getrennt
   ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md#meine-erweiterung-verwendet-eine-bibliothek-unter-einer-anderen-lizenz-ist-das-erlaubt)).
4. **Prüfen Sie Ressourcen**: Schriften, Icons und Bilder brauchen eine Lizenz, die Ihre Nutzung erlaubt.
5. **Prüfen Sie, dass nichts Sie einschränkt**: keine Klausel oder Lizenz, die verbietet, den Code zu ändern oder ihn
   einem anderen Lieferanten oder einer anderen Behörde zu geben.
6. **Entscheiden Sie über die Weitergabe**: Allgemein nützliche Erweiterungen können dem Projekt beigesteuert oder unter
   der GPL mit anderen Behörden geteilt werden ([Erweiterungen zurückgeben](../../../erweiterungen-zurueckgeben.md)).
7. **Halten Sie das Ergebnis** in der Abnahmeakte **fest**.

## Ergebnis

Eine Liste der gelieferten Komponenten mit ihren Lizenzen und die Bestätigung, dass Sie den Quellcode und die Rechte
haben, den Code zu pflegen, zu ändern und weiterzugeben.

## Fallstricke

- Nur minifizierten oder verschleierten Code erhalten.
- Eine „proprietäre“ Erweiterung, die im selben Prozess mit GPL-Code kombiniert ist; fragen Sie die Rechtsberatung.
- Annehmen, dass der Betrieb einer Website Sie verpflichtet, Ihren Code zu veröffentlichen: Das tut er nicht
  ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md#muss-ich-den-code-meiner-website-an-ihre-besucher-herausgeben)).

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Anforderungen zu Open Source und Lizenzierung](../../../../../eng-us/help/government/procurement/requirements/open-source-and-licensing-requirements.md)
- [FAQ: Lizenzierung und Open Source](../../../../../eng-us/help/government/faq/licensing-and-open-source-faq.md)
