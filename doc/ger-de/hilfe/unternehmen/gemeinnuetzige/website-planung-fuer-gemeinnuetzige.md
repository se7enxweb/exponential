---
title: Website-Planung für gemeinnützige Organisationen
audience: businesses, project-manager, editor
category: business-solutions
order: 10
summary: Die Entscheidungen, die eine Hilfsorganisation, Stiftung oder Organisation vor Ort trifft, bevor sie ihre Website auf Exponential baut - Ziele, Zielgruppen, die Seiten, die eine gemeinnützige Organisation braucht, wer bearbeitet, wer freigibt, welche Funktionen genutzt werden, und ein Arbeitsblatt zur Planung auf einer Seite.
keywords: Website-Plan gemeinnützige Organisation, Website Hilfsorganisation, Website-Struktur, Zielgruppen, Spender, Ehrenamtliche, Hilfesuchende, Redakteure, Freigabe, Arbeitsblatt zur Planung, nonprofit website plan, charity website, site structure, audiences, donors, volunteers, beneficiaries, editors, approval, planning worksheet
type: help
---
# Website-Planung für gemeinnützige Organisationen

Diese Seite richtet sich an die Geschäftsführung, das Vorstandsmitglied oder den Koordinator, der für die Website einer
gemeinnützigen Organisation verantwortlich ist. Sie macht aus den allgemeinen Planungshinweisen für Exponential-Websites
die Entscheidungen, die eine gemeinnützige Organisation trifft, und nennt die Funktion hinter jeder. Lesen Sie sie,
bevor Sie mit einem Entwickler oder Partner sprechen; das macht dessen Schätzung schneller und genauer.

## Inhalt

1. [Mit Zielen beginnen](#mit-zielen-beginnen)
2. [Zielgruppen](#zielgruppen)
3. [Die Seiten, die die meisten gemeinnützigen Organisationen brauchen](#die-seiten-die-die-meisten-gemeinnützigen-organisationen-brauchen)
4. [Wer bearbeitet und wer freigibt](#wer-bearbeitet-und-wer-freigibt)
5. [Die Funktionen wählen](#die-funktionen-wählen)
6. [Sprachen und Barrierefreiheit](#sprachen-und-barrierefreiheit)
7. [Hosting und Budget](#hosting-und-budget)
8. [Ein Arbeitsblatt zur Planung](#ein-arbeitsblatt-zur-planung)

## Mit Zielen beginnen

Schreiben Sie drei bis fünf Ziele auf, denen die Website dient, jedes mit etwas, das Sie beobachten können:

| Ziel | Beobachtbares Zeichen |
|---|---|
| Den Auftrag erklären | Besucher erreichen die Seiten „Über uns“ und „Unsere Arbeit“ |
| Geld sammeln | Klicks auf die Spendenschaltfläche oder Bestellungen im Shop, wenn Sie Spendenartikel verkaufen |
| Ehrenamtliche gewinnen | Eingegangene Formulare von Ehrenamtlichen ([Ehrenamtliche und Anmeldeformulare](ehrenamtliche-und-anmeldeformulare.md)) |
| Unterstützer informiert halten | Abonnenten des Newsletters ([Newsletter](../newsletter/README.md)) |
| Kampagnen durchführen | Besuche der Kampagnenseiten während der Kampagne ([Kampagnen und Landingpages](kampagnen-und-landingpages.md)) |

Exponential setzt diese Ziele nicht für Sie und berichtet nicht darüber; die Werkzeuge zum Messen sind unter
[Statistiken und Webanalyse](../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md) aufgeführt.

## Zielgruppen

Listen Sie jede Zielgruppe auf und das eine, wofür sie kommt: Spender (geben, Wirkung sehen), Ehrenamtliche (sich
anmelden), Menschen, für die Sie da sind (Hilfe finden, Kontakt aufnehmen), Partner und Geldgeber (Berichte, Kontakte),
Presse (Fakten, Bilder). Die wichtigsten Aufgaben werden zum Hauptmenü; alles andere steht darunter. Zur Methode siehe
[Ihre Website planen](../../../../eng-us/help/creators/website-creators/planning-your-website.md) und
[Sitemap und Inhaltsinventar](../../../../eng-us/help/creators/website-creators/site-map-and-content-inventory.md).

## Die Seiten, die die meisten gemeinnützigen Organisationen brauchen

- [ ] Startseite: der Auftrag in einem Satz, die aktuelle Kampagne, die Schaltflächen für Spenden und Mitmachen.
- [ ] Über uns: Auftrag, Geschichte, Vorstand und Mitarbeiter, rechtliche Angaben, die Ihr Recht verlangt.
- [ ] Unsere Arbeit: Programme oder Projekte, jedes eine Seite mit eigener Geschichte und eigenen Ergebnissen.
- [ ] Hilfe finden: für die Menschen, für die Sie da sind, in einfacher Sprache.
- [ ] Spenden ([Spenden und Spendenaufrufe](spenden-und-spendenaufrufe.md)).
- [ ] Mitmachen ([Ehrenamtliche und Anmeldeformulare](ehrenamtliche-und-anmeldeformulare.md)).
- [ ] Neuigkeiten und Veranstaltungen.
- [ ] Berichte und Dokumente: Jahresberichte und Abschlüsse als Downloads
      ([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md)).
- [ ] Kontakt, Datenschutzhinweis, Erklärung zur Barrierefreiheit.

Die Website-Oberfläche `ezwebin`, die von `composer.json` verlangt wird, liefert Klassenpakete wie `article`, `event`,
`event_calendar`, `feedback_form`, `file`, `folder`, `frontpage` und `blog` (`extension/ezwebin/packages/`), die den
größten Teil dieser Liste ohne neue Klassen abdecken.

## Wer bearbeitet und wer freigibt

Kleine Organisationen haben oft eine hauptamtliche Person und mehrere Ehrenamtliche. Entscheiden Sie:

| Rolle | Wer | Idee für die Berechtigungen |
|---|---|---|
| Verantwortlicher der Website | Geschäftsführung oder Koordinator | Gibt Struktur und neue Bereiche frei |
| Redakteure | Mitarbeiter, vertrauenswürdige Ehrenamtliche | Bearbeiten nur in ihren eigenen Bereichen |
| Leser der Formulare | Wer Ehrenamtlichen und Kontaktanfragen antwortet | `infocollector/read`, sonst nichts |
| Administrator | Ein Partner oder ein fähiger Ehrenamtlicher | Einstellungen, Updates, Backups |

Rollen und Richtlinien werden in [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md)
erklärt, die Freigabe vor dem Veröffentlichen in [Workflows](../../../anleitungen/workflows.md). Die zweistufige Anmeldung
schützt Konten, die die Website ändern können ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)). Der
Bereich [Vereine und kleine Gruppen](../vereine/README.md) behandelt ehrenamtliche Redakteure ausführlicher.

## Die Funktionen wählen

| Funktion | Nutzen Sie sie, wenn |
|---|---|
| Exponential Layouts | Redakteure die Startseite und Kampagnenseiten oft ändern |
| Formulare mit dem Informationssammler | Sie Kontakt-, Ehrenamts- oder Feedback-Formulare brauchen |
| `cjw_newsletter` | Sie regelmäßig Neuigkeiten per Mail versenden |
| Der Shop mit `ezpaypal` | Sie Artikel verkaufen (einschließlich fester Spendenartikel) und PayPal annehmen |
| `eztags` | Inhalte Themen brauchen, die quer durch die Bereiche gehen |
| Mehrsprachige Inhalte | Sie Menschen in mehreren Sprachen erreichen |

Jede davon ist Teil einer Standardinstallation (`composer.json`). Beginnen Sie mit den wenigsten Funktionen, die Ihre
Ziele erfüllen; jede bringt Schulung und Pflege mit sich.

## Sprachen und Barrierefreiheit

Wenn Sie Menschen in mehreren Sprachen erreichen, planen Sie die Sprachen von Anfang an
([Mehrsprachige Websites](../../../../eng-us/help/creators/website-creators/multilingual-sites.md), [Mehrsprachigkeit](../mehrsprachig/README.md)).
Planen Sie auch die Barrierefreiheit von Anfang an: Das ist günstiger, als eine fertige Website zu reparieren
([Barrierefreiheit für gemeinnützige Organisationen](barrierefreiheit-fuer-gemeinnuetzige.md)).

## Hosting und Budget

Siehe [Betrieb mit kleinem Budget](betrieb-mit-kleinem-budget.md) für die Entscheidungen, die die laufenden Kosten niedrig
halten, und [Kosten und Rendite](../kosten/README.md) für ein Kostenmodell, das Sie mit Ihren eigenen Zahlen füllen.

## Ein Arbeitsblatt zur Planung

```text
Mission in one sentence:       ...
Goals (with observable sign):  1. ...  2. ...  3. ...
Audiences and top tasks:       ...
Main menu:                     ...
Donations:                     external page / shop items / none
Volunteer forms:               ... (who reads them: ...)
Newsletter:                    yes / no (owner: ...)
Campaign pages:                expected per year: ...
Languages:                     ...
Editors and their sections:    ...
Approval before publishing:    yes / no
Administrator:                 ...
Hosting:                       ... (database: SQLite / other)
Launch date:                   ...
```

## Verwandte Seiten

- [Gemeinnützige Organisationen](README.md)
- [Ihre Website planen](../../../../eng-us/help/creators/website-creators/planning-your-website.md)
- [Ein Exponential-Projekt planen](../../fuehrung/ein-exponential-projekt-planen.md)
- [Vorlage für Projektbeschreibungen](../vorlagen/vorlage-fuer-projektbeschreibungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
