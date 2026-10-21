---
title: Quellen zitieren und Ihre Texte lizenzieren
audience: advocate
category: advocacy
order: 55
summary: Wie Fürsprecher die Dokumentation und den Code richtig zitieren, unter der GNU FDL und der GPL zitieren, die Arbeit anderer nennen, Material aus eZpedia und Share verwenden und eine Lizenz für ihre eigenen Beiträge, Vorträge und Videos wählen. Keine Rechtsberatung.
keywords: Zitat, Quellen, zitieren, Lizenz, GFDL, GNU Free Documentation License, GPL, Copyright, Namensnennung, Nennung, eZpedia, Share, Creative Commons, eigene Texte, citation, sources, quoting, license, copyright, attribution, credit, own writing
type: help
---
# Quellen zitieren und Ihre Texte lizenzieren

Diese Seite ist für Fürsprecher, die schreiben, präsentieren oder aufnehmen. Sie beantwortet: Wie zitiere ich die
Dokumentation, was darf ich daraus kopieren, wie nenne ich andere, und wie lizenziere ich, was ich erstelle?

> **Warnung**
> Diese Seite ist keine Rechtsberatung. Sie verweist auf die Lizenztexte und die Aussagen des Projekts. Für eine
> rechtliche Frage lesen Sie die Lizenz und fragen Sie eine Beratung.

## Inhalt

1. [Wie Sie zitieren](#wie-sie-zitieren)
2. [Zitieren oder kopieren](#zitieren-oder-kopieren)
3. [Aus der Hilfe kopieren (GNU FDL)](#aus-der-hilfe-kopieren-gnu-fdl)
4. [Codebeispiele (GPL)](#codebeispiele-gpl)
5. [Andere Quellen der Community](#andere-quellen-der-community)
6. [Menschen und Projekte nennen](#menschen-und-projekte-nennen)
7. [Ihre eigene Arbeit lizenzieren](#ihre-eigene-arbeit-lizenzieren)

## Wie Sie zitieren

Ein gutes Zitat lässt den Leser die genaue Stelle finden:

| Quelle | Zitieren Sie als |
|---|---|
| Eine Hilfeseite | Ihr Titel und ein Link auf die Seite im Repository, z. B. `doc/eng-us/help/security-hardening.md` auf GitHub |
| Ein Abschnitt | Die Seite und die Überschrift: „Sicherheitshärtung, Checkliste“ |
| Eine Datei im Repository | Der Dateiname und sein Link, z. B. SECURITY.md |
| Ein Commit | Der kurze Hash, den jeder mit `git show <hash>` öffnen kann |
| Eine öffentliche Website | Die Website und die Seite, mit dem Datum, an dem Sie sie gelesen haben |

Verlinken Sie auf die Datei auf GitHub (zum Beispiel `https://github.com/se7enxweb/exponential/blob/main/doc/eng-us/help/...`)
statt auf eine Administrations-URL einer einzelnen Installation, die Leser nicht öffnen können.

## Zitieren oder kopieren

- **Zitieren** Sie kurze Passagen in Anführungszeichen, mit der Quelle. So zitiert die Hilfe selbst ihre Quellen.
- **Kopieren** Sie ganze Seiten oder große Teile nur unter den Bedingungen der Lizenz (unten).
- **Umschreiben** Sie mit eigenen Worten und verlinken Sie trotzdem die Quelle.

## Aus der Hilfe kopieren (GNU FDL)

Die Dokumentation steht unter der GNU Free Documentation License 1.3 oder später
([Die Lizenz dieser Dokumentation](../../README-LIZENZ.md)). Sie dürfen sie unter den Bedingungen der Lizenz kopieren,
übersetzen, anpassen und teilen. [Freie Dokumentation](../freie-dokumentation.md#was-sie-tun-dürfen) erklärt in einfachen Worten:

- [Kopieren und teilen](../freie-dokumentation.md#kopieren-und-an-andere-benutzer-weitergeben)
- [Übersetzen](../freie-dokumentation.md#übersetzen)
- [Anpassen und verbessern und Ihre Fassung teilen](../freie-dokumentation.md#anpassen-verbessern-und-ihre-fassung-weitergeben)
- [Mit anderen Werken kombinieren](../freie-dokumentation.md#mit-anderen-werken-zusammenstellen)
- [Zitieren](../freie-dokumentation.md#zitieren)

Manche älteren Seiten haben eigene Bedingungen ([Ältere Seiten mit eigenen Bedingungen](../freie-dokumentation.md#ältere-seiten-mit-eigenen-bedingungen));
prüfen Sie die Seite, die Sie kopieren.

## Codebeispiele (GPL)

Code aus Exponential steht unter der GNU GPL Version 2 oder später
([LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE)). Kurze Ausschnitte in einem Tutorial, die zeigen,
wie man etwas aufruft, sind üblich; wer größere Teile des Codes in seine eigene Software kopiert, übernimmt damit die
Bedingungen der GPL. Verlinken Sie im Zweifel auf die Datei, statt sie zu kopieren.

## Andere Quellen der Community

| Quelle | Lizenz | Was Sie tun |
|---|---|---|
| [eZpedia](https://ezpedia.exponential.earth/) | GNU FDL (laut der Website) | Mit Link zitieren; nur unter der GFDL kopieren |
| Beiträge auf [Share](https://share.exponential.earth/) | Die eigenen Bedingungen jedes Autors | Kurz mit Link zitieren; vor dem Kopieren den Autor fragen |
| [Projects](https://projects.exponential.earth/) | Die eigene Lizenz jedes Projekts | Im Projekt nachlesen |
| Spiegel der Schulungsbücher, „Learning eZ Publish“ | Laut den Beschreibungen der Repositorys; „Learning eZ Publish“ unter der GFDL | [Orte der Community](../wer-ist-die-exponential-foundation.md#orte-der-community) |
| Andere Websites und Blogs | Meist alle Rechte vorbehalten | Verlinken, nicht kopieren |

Wenn Sie der Hilfe Text beisteuern, der aus einer anderen Quelle mit einer anderen Lizenz stammt, sagen Sie das in Ihrem
Pull Request ([Zur Dokumentation beitragen](../../README-LIZENZ.md#zur-dokumentation-beitragen)).

## Menschen und Projekte nennen

- Nennen Sie Arbeit so, wie es das Projekt tut: „bringt das Konzept von Netgen Layouts in den Kernel von Exponential“,
  „unter voller Nennung von Mugo Web“ ([Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst)).
- Nennen Sie Beitragende nur so, wie öffentliche Quellen sie nennen, und nur mit dem, was öffentlich ist.
- Übernehmen Sie Copyright-Hinweise genau so, wie sie geschrieben sind: „7x & Exponential Foundation“
  ([Marken- und Namensregeln](../geschaeft/marken-und-namensregeln.md#die-gemeinnützige-organisation-die-exponential-foundation)).

## Ihre eigene Arbeit lizenzieren

Ihre Beiträge, Folien und Videos gehören Ihnen, und Sie lizenzieren sie. Möglichkeiten:

| Wahl | Wirkung |
|---|---|
| GNU FDL 1.3 oder später | Dieselbe Lizenz wie die Hilfe; leicht in sie zurückzugeben |
| Eine Creative-Commons-Lizenz | Üblich für Beiträge und Folien; prüfen Sie die Kompatibilität, bevor Sie zur Hilfe beitragen |
| Alle Rechte vorbehalten | Andere dürfen nur verlinken und zitieren |

Was immer Sie wählen, **nennen Sie es** am Ende des Werks. Wenn Ihr Tutorial eines Tages eine Hilfeseite werden soll,
macht die GNU FDL das am einfachsten.

## Verwandte Seiten

- [Für freie Dokumentation eintreten](fuer-freie-dokumentation-eintreten.md)
- [Einen Blogbeitrag schreiben](einen-blogbeitrag-schreiben.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
