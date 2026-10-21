---
title: Schwerpunkte der Anforderungen für den öffentlichen Gesundheitsdienst
audience: government, project-manager, admin
category: public-sector
order: 50
summary: Die Anforderungen, die für die Website des öffentlichen Gesundheitsdienstes am meisten wiegen - Richtigkeit und medizinische Prüfung, Barrierefreiheit, Sprachen und einfache Sprache, Verfügbarkeit unter plötzlicher Last, Schutz von Gesundheitsdaten - und was Exponential bietet.
keywords: Anforderungen, öffentliche Gesundheit, medizinische Prüfung, Richtigkeit, Barrierefreiheit, einfache Sprache, mehrsprachig, Verfügbarkeit, Last, statischer Cache, Datenschutz, Gesundheitsdaten, requirements, public health, clinical review, accuracy, accessibility, plain language, multilingual, availability, load, static cache, privacy, health data
type: help
---
# Schwerpunkte der Anforderungen für den öffentlichen Gesundheitsdienst

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren der Website des
öffentlichen Gesundheitsdienstes.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Richtigkeit und Prüfung](#richtigkeit-und-prüfung)
2. [Barrierefreiheit und einfache Sprache](#barrierefreiheit-und-einfache-sprache)
3. [Sprachen](#sprachen)
4. [Verfügbarkeit unter Last](#verfügbarkeit-unter-last)
5. [Datenschutz](#datenschutz)

## Richtigkeit und Prüfung

| Anforderung | Exponential | Lesen |
|---|---|---|
| Freigabe vor dem Veröffentlichen | Ereignis *Freigeben* mit medizinischen Freigebenden | [Workflows](../../../../anleitungen/workflows.md) |
| Wer was wann freigegeben hat | Elemente der Zusammenarbeit und das Audit-Protokoll | [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md) |
| Was sich zwischen Versionen geändert hat | **Vergleichen** auf der Versionsseite | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md) |
| Hinweise nach Plan überprüft | ein Attribut für das Überprüfungsdatum; eine Template-Liste überfälliger Themen | [Inhaltsmodell für den öffentlichen Gesundheitsdienst](inhaltsmodell.md) |
| Veraltete Hinweise aus der Ansicht entfernt | verbergen; `hide.php` für datierte Inhalte | [Cronjobs](../../../../anleitungen/cronjobs.md) |

Es gibt keine eingebaute Erinnerungs-E-Mail für Überprüfungen; eine wöchentlich geprüfte Template-Liste ist das übliche
Muster.

## Barrierefreiheit und einfache Sprache

Gesundheitsinformationen müssen alle erreichen. In der EU gilt die
[Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj) für öffentliche Stellen; in den Vereinigten
Staaten gilt [Section 508](https://www.section508.gov/) für Bundesbehörden und die
[Leitlinie zum Plain Writing Act](https://www.plainlanguage.gov/) für Texte der Bundesverwaltung.

Status von Exponential: Administration admin4 **Partly complies** mit WCAG 2.2 AA; EN 301 549 und Section 508
**Unverified**; öffentliche Designs nicht geprüft; kein VPAT
([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)). Für Gesundheitsinhalte:

| Praxis | Warum |
|---|---|
| Kernbotschaften zuerst, in einem kurzen Feld für die Zusammenfassung | Menschen unter Stress lesen die ersten Zeilen |
| Überschriften als Fragen, die Menschen stellen | Überfliegen und Navigation mit dem Screenreader |
| Keine Hinweise nur in Bildern oder Video | Alternativtext, Untertitel, Transkripte |
| Daten als Tabellen mit einer Zusammenfassung im Text | Diagramme allein schließen viele Lesende aus |

## Sprachen

| Bedarf | Exponential |
|---|---|
| Warnungen in mehreren Sprachen innerhalb von Stunden | Übersetzungen je Objekt; Übersetzende mit einer Einschränkung nach Sprache |
| Eine Seite angezeigt, auch wenn sie noch nicht übersetzt ist | „Immer verfügbar“ entscheidet, ob die Hauptsprache in anderen Siteaccesses angezeigt wird |
| Sprachen von rechts nach links | unterstützt ([Sprachen von rechts nach links](../../../../../eng-us/features/6.0/right-to-left.md)) |
| Welche Seiten es in welcher Sprache gibt | der Reiter **Übersetzungen** eines Objekts; `/content/translations` listet die Sprachen |

Siehe [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) und
[Warnungen in vielen Sprachen und in einfacher Sprache](warnungen-mehrsprachig-und-in-einfacher-sprache.md).

## Verfügbarkeit unter Last

Ein Ausbruch kann den Verkehr plötzlich vervielfachen. Die Werkzeuge von Exponential:

| Werkzeug | Wirkung | Lesen |
|---|---|---|
| Ansichts-Cache | dargestellte Seiten wiederverwendet | [Caches](../../../../anleitungen/caches.md) |
| Statischer Cache | Seiten als einfache Dateien, vom Webserver ausgeliefert | [Generator für den statischen Cache](../../../../../eng-us/features/6.0/static-cache-generator.md) |
| Antwort-Cache von Velocity | Antworten aus dem Speicher ausgeliefert | [Antwort-Cache von Velocity](../../../../../eng-us/features/6.0/velocity-response-cache.md) |
| Ein vorgeschaltetes CDN | Verkehr anderswo aufgefangen | [HTTP-Caching und CDNs](../../../../../eng-us/help/creators/website-creators/http-caching-and-cdns.md) |

Statische Seiten und Caches müssen aufgefrischt werden, wenn sich eine Warnung ändert: Das Veröffentlichen leert den
Ansichts-Cache des Objekts und verwandter Seiten; ein statischer Cache oder ein CDN braucht seine eigene Auffrischung.
Proben Sie das.

## Datenschutz

Gesundheitsdaten sind in der EU eine besondere Kategorie im Sinne der
[DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj). Halten Sie sie aus Formularen der Website heraus; verwenden Sie
klinische und Buchungssysteme. Exponential maskiert IP-Adressen in Protokollen und pseudonymisiert Audit-Einträge
([Datenschutzstandards](../../../../../eng-us/standards/privacy.md)); es hat kein Cookie-Banner, und Analysedienste Dritter auf
Gesundheitsseiten verraten, was Menschen nachschlagen, wählen Sie Statistiken also mit Sorgfalt
([Statistik und Analyse](../../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md)).

## Verwandte Seiten

- [Öffentliche Gesundheit](README.md)
- [Die ersten 90 Tage für den öffentlichen Gesundheitsdienst](die-ersten-90-tage.md)
- [Leistung und Caching](../../../../../eng-us/help/creators/website-creators/performance-and-caching.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
