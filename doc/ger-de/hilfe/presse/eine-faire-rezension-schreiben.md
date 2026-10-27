---
title: Eine faire Rezension schreiben
audience: press
category: press-media
order: 167
summary: Wie Creator und Rezensenten Exponential fair beurteilen - auf der eigenen Installation testen, Version und Aufbau nennen, Gesehenes von dem trennen, was die Dokumentation sagt, Grenzen und Lücken ehrlich berichten, nur Gleiches mit Gleichem vergleichen und 7x die Gelegenheit geben, Fakten zu korrigieren - mit einem Aufbau für die Rezension und einer Checkliste zur Fairness.
keywords: Rezension, faire Rezension, Bewertung, Test, Kriterien, Vor- und Nachteile, Grenzen, Vergleich, Methodik, Fazit, Recht auf Stellungnahme, Creator, Journalist, review, fair review, evaluation, test, criteria, pros and cons, limitations, comparison, methodology, verdict, right of reply, creator, journalist
type: help
---
# Eine faire Rezension schreiben

Diese Seite ist für Creator und Journalisten, die Exponential rezensieren: in einem Video, einem Beitrag, einem
Newsletter oder einer Folge. Eine faire Rezension darf kritisch sein; sie muss genau und getestet sein und offen sagen,
wie Sie getestet haben. Niemand bei 7x oder der Exponential Foundation muss Ihre Rezension freigeben, und nichts auf
dieser Seite verlangt, dass Sie positiv urteilen.

## Inhalt

1. [Bevor Sie urteilen: selbst testen](#bevor-sie-urteilen-selbst-testen)
2. [Nennen Sie Ihren Aufbau](#nennen-sie-ihren-aufbau)
3. [Trennen Sie Gesehenes von Gelesenem](#trennen-sie-gesehenes-von-gelesenem)
4. [Kriterien und wo Sie sie prüfen](#kriterien-und-wo-sie-sie-prüfen)
5. [Grenzen und Lücken, die einen Bericht wert sind](#grenzen-und-lücken-die-einen-bericht-wert-sind)
6. [Vergleiche](#vergleiche)
7. [Ein Aufbau für die Rezension und eine Checkliste](#ein-aufbau-für-die-rezension-und-eine-checkliste)

## Bevor Sie urteilen: selbst testen

- Installieren Sie es selbst, auf einem Rechner, den Sie kontrollieren ([Eine Demo-Installation einrichten](eine-demo-installation-einrichten.md)).
- Erledigen Sie die Aufgaben, die Ihr Publikum erledigen würde: schreiben und veröffentlichen, eine Seite bauen,
  übersetzen, aktualisieren, sichern.
- Notieren Sie, was fehlgeschlagen ist, mit der genauen Meldung und dem Schritt. Wenn es wie ein Fehler aussieht,
  melden Sie ihn ([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)); wenn es wie ein Sicherheitsproblem
  aussieht, melden Sie es vertraulich an security@se7enx.com und lassen Sie die Einzelheiten aus der Rezension heraus,
  bis es behoben ist.
- Nehmen Sie sich genug Zeit; ein erster Eindruck ist ein erster Blick, also nennen Sie ihn so.

## Nennen Sie Ihren Aufbau

```text
Tested: Exponential <6.0.14 | 6.0.15 development line, commit or date>
Installed with: <exp:install | setup wizard | kickstarter>, database <SQLite | ...>
Served by: <Apache + PHP-FPM | nginx | Velocity, engine qbix | FrankenPHP>, PHP <version>
Design: <admin4 | ...>; extensions switched on: <list>
Hardware or hosting: <description, without private addresses>
Time spent: <hours or days>
Disclosure: <line>
```

## Trennen Sie Gesehenes von Gelesenem

| Art der Aussage | Wie Sie sie schreiben |
|---|---|
| Sie haben es getestet | „In meinem Test blieb beim Veröffentlichen eines Ordners die frühere Version erhalten.“ |
| Die Dokumentation sagt es | „Laut Dokumentation trägt jeder Eintrag des Prüfprotokolls den Hash des vorherigen.“ (Link) |
| Jemand hat es Ihnen gesagt | „<Wer> hat mir gesagt ...“ - und prüfen Sie es ([Aussagen, die Sie nicht machen dürfen](../fuersprecher/aussagen-die-sie-nicht-machen-duerfen.md)) |
| Niemand sagt es | „Die Quellen sagen es nicht.“ |

Zahlen gehören zur zweiten Art, sofern Sie sie nicht selbst gemessen haben. Die Geschwindigkeitswerte im README zum
Beispiel kommen mit ihrer Methode; zitieren Sie sie als die des README, und wenn Sie messen, veröffentlichen Sie auch
Ihre Methode ([Benchmarking](../../anleitungen/benchmarking.md)).

## Kriterien und wo Sie sie prüfen

| Kriterium | Test | Dokumentation |
|---|---|---|
| Bearbeiten und Veröffentlichen | Entwürfe, Vorschau, Versionen, Freigabe | [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md) |
| Seitenbau | Layouts, Blöcke, geteilte Layouts, Zuordnungen | [Der Layout-Editor](../layout-editor.md) |
| Sprachen | Ein Element übersetzen; die Oberfläche umschalten | [Sprachen in Exponential](../../sprachen.md) |
| Barrierefreiheit | Nur Tastatur; Kontrast hell und dunkel | [Tastaturkürzel](../tastenkuerzel.md) |
| Installation | Von einem leeren Ordner aus | [Exponential installieren](../../installation/README.md) |
| Freiheit beim Hosting | Datenbanken, Betriebssysteme | [Unterstützte Datenbanken](../datenbankunterstuetzung.md), [Betriebssysteme](../betriebssystemunterstuetzung.md) |
| Sicherheit | Zweistufige Anmeldung, Prüfprotokoll, Härtung | [Sicherheitshärtung](../sicherheitshaertung.md) |
| Dokumentation | Antworten in der eingebauten Hilfe finden | [Das Hilfesystem](../../funktionen/6.0/hilfesystem.md) |
| Lizenz | GPL für den Code, GNU FDL für die Dokumentation; was die Weitergabe verlangt | [Wer ist 7x?, Open-Source-Lizenzen](../wer-ist-7x.md#open-source-lizenzen); [Lizenzverstöße](../projekt/lizenz-faq.md#lizenzverstöße) |
| Support und Kontinuität | Was zugesagt ist, und von wem | [Die Zusage zur Unterstützung](../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung) |

## Grenzen und Lücken, die einen Bericht wert sind

Eine faire Rezension berichtet, was die Quellen selbst einräumen, mit der Quelle:

- 6.0.15 ist in Entwicklung und hat kein Veröffentlichungsdatum; vieles von dem, was die Hilfe beschreibt, gehört dazu
  ([Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)).
- Alle Übersetzungen der Administration (die aktuellen Gesamtzahlen stehen am Anfang der Seite [Sprachen in Exponential](../../sprachen.md)) sind vollständig (26. Oktober 2026); später hinzukommende Texte zeigen
  sie Englisch, bis sie übersetzt sind ([Sprachen in Exponential](../../sprachen.md)).
- Die mitgelieferte Standard-Engine von Velocity ist der eingebaute Server von PHP, nur für die Entwicklung; die
  empfohlene Engine muss ausgewählt werden ([Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)).
- Exponential Platform DXP liefert keinen Layout-Editor
  ([Die Produktfamilie kennen](../fuersprecher/die-produktfamilie-kennen.md#häufige-verwechslungen)).
- Die Quellen zählen weder Installationen noch Benutzer noch Entwickler.

## Vergleiche

- Vergleichen Sie Gleiches mit Gleichem: dieselbe Aufgabe, dasselbe Hosting, dieselbe Sorgfalt bei der Einrichtung.
- Beschreiben Sie andere Produkte anhand ihrer eigenen Dokumentation, und datieren Sie sie.
- Keine Faktoren („10-mal schneller“) ohne eigene veröffentlichte Messung.
- Seien Sie fair zu anderen Projekten und ihren Menschen ([Andere Projekte und Unternehmen respektieren](../fuersprecher/andere-projekte-respektieren.md),
  [Exponential sachlich vergleichen](../geschaeft/exponential-sachlich-vergleichen.md)).

## Ein Aufbau für die Rezension und eine Checkliste

```text
1. Disclosure and setup (version, how installed, how served, time spent)
2. What it is, in two sentences, with the source
3. What I tested, task by task: what worked, what did not
4. Strengths, each with evidence
5. Weaknesses and gaps, each with evidence
6. Who it suits, and who it does not
7. Verdict, labeled as my opinion
8. Sources and corrections policy

[ ] Every fact has a source or is labeled as my own test
[ ] Names right: Exponential, 7x, the Exponential Foundation; eZ Publish only as history
[ ] No unsupported numbers, endorsements, release dates or compliance claims
[ ] Facts sent to 7x for checking, if there is time (not the opinion)
[ ] I know how I will correct mistakes
```

Um einen Faktencheck zu bitten ist freiwillig. Die Kontaktadressen stehen auf den Presseseiten
([Presse, Medien und Influencer: hier beginnen](presse-hier-beginnen.md)); fragen Sie nach Fakten, und Ihre Meinung bleibt
Ihre.

## Verwandte Seiten

- [Offenlegung und Ethik beim Sponsoring](offenlegung-und-ethik-beim-sponsoring.md)
- [Aussagen, die Sie nicht machen dürfen](../fuersprecher/aussagen-die-sie-nicht-machen-duerfen.md)
- [Ein Video oder einen Beitrag korrigieren](ein-video-oder-einen-beitrag-korrigieren.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
