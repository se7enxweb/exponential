---
title: Fragen und Antworten im Pre-Sales
audience: reseller, partner, sales-marketing
category: reselling
order: 270
summary: Die Fragen, die ein Wiederverkäufer einem Interessenten in der Bedarfsanalyse stellt, und die Fragen, die Interessenten einem Wiederverkäufer stellen - Lizenz und Kosten, Hosting, Datenbanken, Sprachen, Multisite, Shop, Sicherheit, Datenschutz, Updates, Support, Ausstieg -, mit belegten Antworten und der Seite, die jede beweist.
keywords: Pre-Sales, Bedarfsanalyse, Fragen, Antworten, Interessent, Anforderungen, Lizenz, Kosten, Hosting, Datenbanken, Sprachen, Multisite, Shop, Sicherheit, Datenschutz, Updates, Support, Ausstieg, Ausschreibung, pre-sales, discovery, questions, answers, prospect, requirements, license, cost, databases, languages, multisite, shop, security, privacy, updates, support, exit, tender
type: help
---
# Fragen und Antworten im Pre-Sales

Diese Seite ist für Vertriebspersonal und Pre-Sales-Berater. Die erste Hälfte listet auf, was Sie einen
Interessenten fragen. Die zweite Hälfte beantwortet, was Interessenten Sie fragen, jede Antwort mit der Seite, die
sie beweist. Wenn eine Frage nicht abgedeckt ist, sagen Sie „Ich finde es heraus und schicke Ihnen die Quelle“,
statt zu raten.

## Inhalt

1. [Fragen an den Interessenten](#fragen-an-den-interessenten)
2. [Fragen, die Interessenten stellen](#fragen-die-interessenten-stellen)
3. [Fragen, die Sie weitergeben](#fragen-die-sie-weitergeben)

## Fragen an den Interessenten

```text
Organization and goal
  What should the site achieve in one year? Who are its visitors?
Content
  Which kinds of content (pages, news, events, products, documents)? Roughly how many items?
  Who edits, how many editors, do they need approval steps?
Sites and languages
  How many sites, brands or countries? Which languages for visitors? Which for editors?
Commerce
  Do you sell online? Which countries, currencies, payment providers?
Integration
  Which systems must the site talk to (single sign-on, CRM, accounting, search)?
Hosting and operations
  Hosted by us, by you, or by another provider? Any data location requirements?
Compliance
  Accessibility, privacy, records, security requirements? Any audit coming?
Today
  Which system do you use now? What must move? What do you dislike about it?
Budget and timeline
  Budget range for the project and per month? Launch date? Who decides?
```

## Fragen, die Interessenten stellen

| Frage | Antwort | Beleg |
|---|---|---|
| Was kostet die Lizenz? | Nichts: „Using Exponential under the terms of the GNU GPL is free of charge“; Sie bezahlen für Dienstleistungen | `README.md`; [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md) |
| Können wir Sie später verlassen? | Ja: Der Code steht unter der GPL, Ihre Inhalte liegen in einer Standarddatenbank, und wir übergeben alles | [Übergabe](../kunden/uebergabe.md), [Exponential sachlich vergleichen](../../geschaeft/exponential-sachlich-vergleichen.md#keine-herstellerbindung-punkt-für-punkt) |
| Welche Datenbanken? | SQLite 3 (die Voreinstellung), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, in dieser Reihenfolge der Präferenz | [Datenbankunterstützung](../../datenbankunterstuetzung.md) |
| Welche PHP-Versionen? | 8.0 bis 8.5 | [Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md) |
| Können wir mehrere Websites haben? | Ja, als Siteaccesses auf einer Installation, die sich Inhalte und Benutzer teilen, wo gewünscht | [Multisite-Angebote](../angebote/multisite-angebote.md) |
| Welche Sprachen? | 355 Locale-Dateien für Inhalte; Übersetzungen der Administration in drei Unterstützungsstufen; Schreibrichtung von rechts nach links unterstützt | [Mehrsprachige Angebote](../angebote/mehrsprachige-angebote.md) |
| Können wir online verkaufen? | Ja, ein Shop ist eingebaut; einiges (Lagerbestand, Gutscheine, Versandetiketten) ist nicht enthalten | [E-Commerce-Angebote](../angebote/e-commerce-angebote.md) |
| Wie wird mit Sicherheit umgegangen? | Vertrauliche Meldungen an security@se7enx.com mit einer ersten Antwort innerhalb eines Werktags; Fehlerbehebungen im nächsten Release 6.0.x; Leitfaden zur Härtung | `SECURITY.md`, [Sicherheitshärtung](../../sicherheitshaertung.md) |
| Können sich Redakteure in zwei Schritten anmelden? | Ja, die Anmeldung in zwei Schritten gibt es | [Anmeldung in zwei Schritten](../../../anleitungen/zwei-faktor-anmeldung.md) |
| Wie steht es um Datenschutz und DSGVO? | Funktionen wie Einwilligungsnachweise, maskierte Logs und Löschung beim Entfernen eines Kontos; der Betreiber entscheidet weiterhin über Zwecke und beantwortet Anfragen | [Datenschutzstandards](../../../standards/datenschutz.md) |
| Ist es barrierefrei? | Die Administration wird gegen WCAG 2.2 AA geprüft, mit dem Status „Teilweise erfüllt“; Ihr öffentliches Design muss geprüft werden | [Standards für Barrierefreiheit](../../../standards/barrierefreiheit.md) |
| Wer hält es aktuell? | Wir, im Rahmen des Wartungsvertrags; das Projekt veröffentlicht Versionen 6.0.x und keinen Release-Kalender | [Kunden aktuell halten](../kunden/kunden-aktuell-halten.md) |
| Gibt es ein SLA des Projekts? | Nein; das Projekt veröffentlicht kein SLA. Unser Vertrag gibt Ihnen unseres | [Service-Levels gestalten](../angebote/service-levels-gestalten.md) |
| Wird es noch gepflegt? | Ja: fünfzehn Releases 6.0.x vom 1. Januar 2024 bis zum 7. Juni 2026, 6.0.15 in Entwicklung | [Einwandbehandlung](../../geschaeft/einwandbehandlung.md) |
| Ist das das alte Produkt unter neuem Namen? | Exponential führt die alte Codebasis fort, ein Produkt, das sein Hersteller nach fast 18 Jahren eingestellt hat; es wird heute von 7x und der Exponential Foundation gepflegt | [Wer ist 7x?](../../wer-ist-7x.md) |

Antworten Sie nie mit einer Funktion, die Sie nicht in der Version geprüft haben, die Sie liefern werden. Die
Funktionsseiten der Hilfe (`doc/eng-us/features/6.0/`) und die Seiten für Unternehmen sind die Quellen.

## Fragen, die Sie weitergeben

| Frage | An wen |
|---|---|
| Partnerstatus, OEM-Bedingungen, White-Label-Preise | Das Partnerteam, <partners@exponential.earth> |
| Tiefe technische Fragen bei einer großen Ausschreibung | Pre-Sales-Unterstützung von 7x, ab Professional ([Pre-Sales-Unterstützung](../../partner/pre-sales-unterstuetzung.md)) |
| Rechtliche Auslegung der GPL für sein Produkt | Seinen Rechtsberater |
| Steuerliche Behandlung Ihrer Rechnungen | Seinen und Ihren Steuerberater |

## Verwandte Seiten

- [Funktionshighlights für Kunden](../../geschaeft/funktionshighlights-fuer-kunden.md)
- [Für wen Exponential ist](../../geschaeft/fuer-wen-exponential-ist.md)
- [Mit Einwänden umgehen als Wiederverkäufer](mit-einwaenden-umgehen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
