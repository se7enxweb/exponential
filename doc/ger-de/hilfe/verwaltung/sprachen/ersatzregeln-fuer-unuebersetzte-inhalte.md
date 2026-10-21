---
title: Ersatzregeln für unübersetzte Inhalte
audience: government, admin, project-manager
category: public-sector
order: 90
summary: Was ein Besucher einer Sprachfassung sieht, wenn eine Seite noch nicht übersetzt ist, wie die Reihenfolge von SiteLanguageList, ShowUntranslatedObjects und „Immer verfügbar“ darüber entscheiden, und welche Grundsatzentscheidungen eine öffentliche Stelle für jede Amtssprache trifft.
keywords: Ersatzsprache, Fallback, unübersetzt, SiteLanguageList, ShowUntranslatedObjects, immer verfügbar, fehlende Übersetzung, Sprachpolitik, Amtssprachen, teilweise Übersetzung, fallback, untranslated, always available, missing translation, language policy, official languages, partial translation
type: help
---
# Ersatzregeln für unübersetzte Inhalte

Diese Seite richtet sich an Administratoren und Führungskräfte, die entscheiden, was geschieht, wenn Inhalte in der
Sprache eines Besuchers nicht vorliegen. In Exponential zeigt jeder Siteaccess ein Objekt in der ersten Sprache seiner
`SiteLanguageList[]`, die das Objekt hat; Objekte, die in keiner davon vorliegen, werden ausgeblendet, es sei denn, sie
sind immer verfügbar oder der Siteaccess zeigt unübersetzte Objekte an
([Inhaltssprachen, Abschnitte 5 und 6](../../../anleitungen/inhaltssprachen.md#5-eine-sprache-anzeigen-sitelanguagelist-und-siteaccesses)).

## Inhalt

1. [Die drei Einstellungen](#die-drei-einstellungen)
2. [Grundsatzentscheidungen](#grundsatzentscheidungen)
3. [Besucher informieren](#besucher-informieren)
4. [Suche und Listen](#suche-und-listen)
5. [Prüfen, was ein Siteaccess zeigt](#prüfen-was-ein-siteaccess-zeigt)

## Die drei Einstellungen

| Gewünscht | Einstellung |
|---|---|
| Die Hauptsprache zeigen, wo die Übersetzung fehlt | `SiteLanguageList[]=fre-FR`, dann `eng-US` |
| Ausblenden, was nicht übersetzt ist | `ShowUntranslatedObjects=disabled` und keine zweite Sprache |
| Alles in seiner Hauptsprache zeigen, wenn nichts anderes passt | `ShowUntranslatedObjects=enabled` |
| Ein Objekt erscheint in jeder Sprache so, wie es ist (Logo, ein PDF) | markieren Sie es als **immer verfügbar** |

## Grundsatzentscheidungen

| Grundsatz | Konfiguration | Folge |
|---|---|---|
| Volle Gleichwertigkeit: Jede Seite gibt es in jeder Amtssprache | eine Sprache pro Liste, kein Ersatz | unübersetzte Seiten sind in dieser Sprache unsichtbar; die Veröffentlichung wartet auf die Übersetzung |
| Ersatz durch die Hauptsprache | zwei Sprachen in jeder Liste | Besucher sehen manche Seiten in einer anderen Sprache; kennzeichnen Sie sie |
| Kerninhalte in allen Sprachen, der Rest in einer | Ersatz, dazu eine Regel, welche Bereiche übersetzt sein müssen | braucht einen Übersetzungsrückstand und eine Durchsicht |

Manche Rechtsordnungen verlangen bestimmte Informationen in jeder Amtssprache; legen Sie den Grundsatz mit Ihren
Rechts- und Sprachdiensten fest und halten Sie ihn pro Bereich schriftlich fest.

## Besucher informieren

Wenn eine Seite auf eine Ersatzsprache zurückfällt, teilen Sie den Besuchern in ihrer Sprache mit, dass sie darin noch
nicht verfügbar ist. Ein Template kann die angezeigte Sprache mit der ersten Sprache des Siteaccess vergleichen;
kennzeichnen Sie den Inhalt mit einem eigenen `lang`-Attribut (für die Schreibrichtung hilft
`parts/exp_content_direction.tpl` des Standarddesigns; der Wert von `lang` kommt aus der Sprache des Objekts), damit
Screenreader die Aussprache umstellen ([HTML-Sprachangaben und Barrierefreiheit](html-sprachangaben-und-barrierefreiheit.md)).

## Suche und Listen

Listen und Suchergebnisse folgen denselben Regeln: Ein Objekt, das in einem Siteaccess ausgeblendet ist, wird dort
nicht aufgeführt. Mit Solr entscheidet `ezfind.ini [LanguageSearch] SearchMainLanguageOnly`, ob die Ergebnisse dem
Ersatz folgen (siehe „Mehrsprachige Suche“).

## Prüfen, was ein Siteaccess zeigt

- **Setup > Sprachen** zeigt jeden Siteaccess mit seiner `SiteLanguageList` in Reihenfolge.
- Der Reiter **Übersetzungen** eines Objekts listet seine Sprachen, seine Hauptsprache und ob es immer verfügbar ist.
- Öffnen Sie dieselbe Seite abgemeldet unter jeder Sprachadresse.

## Verwandte Seiten

- [Immer verfügbare Inhalte](immer-verfuegbare-inhalte.md)
- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Eine mehrsprachige Website bauen](../../ersteller/website-ersteller/mehrsprachige-websites.md#fallbacks-und-nicht-übersetzte-inhalte)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
