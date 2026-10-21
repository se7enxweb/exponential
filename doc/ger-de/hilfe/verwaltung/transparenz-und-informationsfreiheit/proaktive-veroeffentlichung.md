---
title: Proaktive Veröffentlichung
audience: government, project-manager, editor
category: public-sector
order: 30
summary: Wie eine öffentliche Stelle mit Exponential Informationen veröffentlicht, bevor jemand danach fragt - entscheiden, was veröffentlicht wird, eine Struktur dafür, regelmäßiges Veröffentlichen mit Rollen und Freigabe, und alles aktuell halten.
keywords: proaktive Veröffentlichung, regelmäßig veröffentlichen, Veröffentlichungsplan, Transparenz, Informationsfreiheit, Transparenzportal, Informationen, die wir veröffentlichen, proactive disclosure, publish routinely, publication scheme, freedom of information, FOIA reading room, routine publication
type: help
---
# Proaktive Veröffentlichung

Diese Seite richtet sich an Führungskräfte und Redaktionen, die Informationen regelmäßig veröffentlichen, ohne auf
Anträge zu warten. Viele Informationsfreiheitsgesetze verlangen oder fördern das; in den Vereinigten Staaten zum
Beispiel erklärt [FOIA.gov](https://www.foia.gov/) das Bundesgesetz und seine Pflichten zur proaktiven
Veröffentlichung. Exponential stellt die Seite des Veröffentlichens bereit; was veröffentlicht werden muss, ist eine
Frage von Recht und Verwaltungspraxis.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was üblicherweise veröffentlicht wird](#was-üblicherweise-veröffentlicht-wird)
2. [Eine Struktur im Inhaltsbaum](#eine-struktur-im-inhaltsbaum)
3. [Ein fester Ablauf für jede Kategorie](#ein-fester-ablauf-für-jede-kategorie)
4. [Aktuell halten](#aktuell-halten)
5. [Checkliste](#checkliste)

## Was üblicherweise veröffentlicht wird

Kategorien, die Transparenzprogramme häufig proaktiv veröffentlichen:

| Kategorie | Typischer Inhaltstyp in Exponential |
|---|---|
| Wer wir sind und was wir tun | Seiten, Organigramm als Bild mit Textalternative |
| Was wir ausgeben | Ausgabenberichte als Dateien, ein Ausgabenregister (siehe „Ausgaben und Verträge veröffentlichen“) |
| Unsere Prioritäten und Leistungen | Pläne und Berichte als Dateien |
| Wie wir entscheiden | Tagesordnungen, Protokolle, Beschlussregister (siehe „Sitzungsunterlagen“ und „Beschlussregister“) |
| Unsere Richtlinien und Verfahren | Seiten oder Dateien |
| Listen und Register | als Inhalt geführte Register, als offene Daten exportiert |
| Leistungen, die wir anbieten | Leistungsseiten |
| Häufig nachgefragte Informationen | herausgegebene Auskünfte nach Informationsfreiheitsrecht (siehe „Offenlegungsprotokolle“) |

## Eine Struktur im Inhaltsbaum

Legen Sie unterhalb eines Ordners „Transparenz“ einen Ordner pro Kategorie an und legen Sie jede Kategorie in eine
eigene Sektion, wenn verschiedene Teams sie veröffentlichen ([Sektionen](../../../anleitungen/sektionen.md)). Die
Ordnernamen werden zu den Adressen (`/Transparency/What-we-spend`), die auch nach Umbenennungen gültig bleiben, weil
alte Aliasse weiterleiten ([Weiterleitungen und verschobene Inhalte](../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md)).

## Ein fester Ablauf für jede Kategorie

Halten Sie für jede Kategorie fest: die verantwortliche Person, die Häufigkeit, das Format, den Freigabeschritt und
die Aufbewahrung. Richten Sie es dann in Exponential ein:

1. Eine Rolle für das verantwortliche Team, beschränkt auf die Sektion der Kategorie
   ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
2. Einen Freigabe-Workflow für die Sektion, wenn eine zweite Person prüfen muss (siehe „Freigabe vor dem
   Veröffentlichen“).
3. Ein Workflow-Ereignis *Bis Datum warten*, wenn Veröffentlichungen zu einem festen Zeitpunkt erscheinen müssen (siehe
   „Zeitgesteuertes Veröffentlichen und Sperrfristen“).
4. Einen RSS-Export des Ordners, damit Abonnenten neue Einträge sehen ([RSS-Feeds](../../../anleitungen/rss-feeds.md)).

## Aktuell halten

- Fügen Sie den Klassen, die Sie für regelmäßige Veröffentlichungen verwenden, ein Datumsfeld „Zuletzt geprüft“ hinzu
  und geben Sie es aus.
- Verwenden Sie **Setup > URL-Verwaltung**, um defekte Links in veröffentlichten Seiten zu finden
  ([Links](../../../anleitungen/urls-und-aliase.md#1-die-linkliste)).
- Verwenden Sie Objektzustände (zum Beispiel eine Gruppe „Prüfung“ mit „Aktuell“ und „Prüfung nötig“), um Einträge zur
  Prüfung zu markieren, und eine Rollenbeschränkung, damit nur Prüfende sie setzen
  ([Objektzustände](../../../anleitungen/objektzustaende.md)).
- Entfernen Sie überholte Dokumente nicht stillschweigend; kennzeichnen Sie sie als überholt und verlinken Sie das neue.

## Checkliste

- [ ] Kategorien, verantwortliche Personen und Häufigkeiten mit der Rechtsberatung festgelegt.
- [ ] Ein Ordner und eine Sektion pro Kategorie; Rollen pro verantwortlichem Team.
- [ ] Freigabe und Zeitsteuerung, wo erforderlich; der Workflow-Cronjob läuft.
- [ ] Feeds neuer Veröffentlichungen.
- [ ] Eine jährliche Überprüfung jeder Kategorie.

## Verwandte Seiten

- [Veröffentlichungspläne](veroeffentlichungsplaene.md)
- [Offenlegungsprotokolle](offenlegungsprotokolle.md)
- [Den Inhaltsbaum strukturieren](../../../../eng-us/help/creators/website-creators/structuring-the-content-tree.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
