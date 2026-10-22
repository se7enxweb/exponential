---
title: Ketten und eingereihte Arbeit
audience: ai-agents, editor, admin, developer
category: ai-agents
order: 30
summary: Mehrstufige KI-Arbeit in Exponential Intelligence über die Job-Warteschlange - KI-Ketten (schreiben, prüfen, überarbeiten, SEO und QA, ein menschlicher Kontrollpunkt, dann übersetzen), was im Hintergrund läuft, als wer es läuft, Lanes und Stapel für günstigere Massenarbeit und die Cron-Teile, die es braucht.
keywords: KI-Ketten, mehrstufig, agentischer Ablauf, Job-Warteschlange, expqueue, Kontrollpunkt, Mensch in der Schleife, Approve and continue, Ask for changes, Hintergrundarbeit, Lanes, Batch, Cron-Teile, expai/chain, AI chains, multi-step, agentic workflow, job queue, checkpoint, human in the loop, background work, lanes, batch, cron parts
type: help
---
# Ketten und eingereihte Arbeit

Diese Seite richtet sich an Redakteure und Administratoren, die Exponential Intelligence für Arbeit verwenden, die länger dauert als
eine Anfrage: mehrstufiges Schreiben, viele Elemente auf einmal, Arbeit, die läuft, während niemand zusieht. Diese Arbeit läuft über
die Job-Warteschlange der Erweiterung `expqueue`, als der Benutzer, der sie gestartet hat.

## Inhalt

1. [Was im Hintergrund läuft](#was-im-hintergrund-läuft)
2. [KI-Ketten](#ki-ketten)
3. [Der menschliche Kontrollpunkt](#der-menschliche-kontrollpunkt)
4. [Lanes und Stapel](#lanes-und-stapel)
5. [Was die Warteschlange braucht](#was-die-warteschlange-braucht)

## Was im Hintergrund läuft

| Arbeit | Gestartet aus | Endet in |
|---|---|---|
| KI-Ketten | Dem Reiter Intelligence ("Write with a chain") oder dem Dashboard ("Start writing") | Einem Entwurf je Element, nach einem menschlichen Kontrollpunkt |
| Übersetzungsabgleich | Einem Workflow-Event nach dem Veröffentlichen oder dem Abschnitt Translations | Entwürfen von Übersetzungen ([Übersetzungshilfe](uebersetzungshilfe.md)) |
| Bericht Missing alt text | `expai/alttext` | Entwürfen mit vorgeschlagenem Alternativtext ([Alternativtext für Bilder](alternativtext-fuer-bilder.md)) |
| Hintergrundläufe von Vector und freigegebene Pläne | Einem Vector-Thread | Vorschlägen oder Entwürfen ([Vector und gestufte Prüfung](vektorsuche-und-gestufte-pruefung.md)) |
| Modelltest und Laden | Setup > EI: Models | Einem Ergebnis auf dieser Seite |

Eingereihte Arbeit läuft **als der Benutzer, der sie eingereiht hat**, mit dessen Berechtigungen; es gibt kein Dienstkonto.

## KI-Ketten

Eine Kette schreibt Inhalte in Schritten statt in einer Antwort
([Dokumentation der Erweiterung: Ketten](https://github.com/se7enxweb/expai/blob/main/doc/chains.md)). Die mitgelieferte Kette
`article`:

```
write -> review --fail--> revise -> review ... (at most 3 reviews)
             |pass
             v
       SEO and QA -> checkpoint (editor) -> translate (hand-off) -> done
```

1. **write**: Die Vorgabe, der Ort und die Felder der Klasse gehen an ein starkes Modell; die Antwort sind die Felder.
2. **review**: Ein zweites Modell bewertet den Text und prüft ihn gegen eine Checkliste.
3. **revise**: Bei einer gescheiterten Prüfung erhält das erste Modell die Probleme und schreibt erneut. Nach drei gescheiterten
   Prüfungen läuft der Lauf weiter, und der Redakteur sieht die Gründe am Kontrollpunkt.
4. **SEO and QA**: Ein kleines Modell bereitet einen kurzen Titel, Meta-Titel, Meta-Beschreibung und Schlüsselwörter vor; jeder Link
   wird offline geprüft.
5. **checkpoint**: Der Entwurf wird jetzt geschrieben, gekennzeichnet mit Exponential Intelligence, und der Lauf wartet auf einen
   Redakteur.
6. **translate**: Nach der Freigabe wird die Übersetzung an den Übersetzungsabgleich übergeben.

Jeder Schritt ist ein eingereihter Job mit eigenem Modell, eigener Lane, eigenen Tokens und Kosten. Die Modelle der mitgelieferten
Kette sind eine Konfigurationsentscheidung der Erweiterung; Administratoren definieren in den Einstellungen eigene Ketten. Ketten
brauchen die Richtlinie `expai/chain` mit `expai/write`.

## Der menschliche Kontrollpunkt

Am Kontrollpunkt zeigt die Laufseite (`expai/chain/(run)/<id>`) jeden Schritt mit seinem Modell, Urteil, Wert, seinen Problemen und
Kosten, die Metadaten, die Linkprüfung und den Entwurf. Ein Redakteur, der am Lauf handeln darf, hat drei Schaltflächen:

- **Approve and continue**: Der Entwurf wird zurückgelesen, sodass Ihre Änderungen im Bearbeitungsformular erhalten bleiben, und der
  Lauf geht weiter.
- **Ask for changes** (mit einer Notiz): Die Notiz geht als Problem hoher Schwere an den Schritt revise; die Prüfung läuft erneut,
  und der Lauf kehrt zum Kontrollpunkt zurück. Derselbe Entwurf wird aktualisiert.
- **Stop the run**: Eingereihte Jobs werden abgebrochen; der Entwurf bleibt ein Entwurf.

Nichts, was eine Kette schreibt, wird von der Kette veröffentlicht.

## Lanes und Stapel

Eine **Lane** ist eine Art, eine Anfrage zu senden: standard, flex (langsamer, günstiger), batch (innerhalb von Stunden beantwortet,
typischerweise zum halben Preis) oder fast (ein Aufschlag). Das Panel und die Feldaktionen verwenden immer die Standard-Lane.
Eingereihte Anfragen nehmen die Lane, die die Lane-Richtlinie wählt. Die Erweiterung verwendet nie eine Lane, deren Preis sie raten
müsste: Solche Lanes werden ausgeschaltet ausgeliefert ([Kostenkontrolle](../steuerung/kostenkontrolle.md#lanes-und-stapel)).

## Was die Warteschlange braucht

- Die Erweiterung `expqueue` ist aktiv, und ihr Cron-Teil läuft jede Minute
  (`php runcronjobs.php -q -s <admin siteaccess> expqueue`), oder ein Worker läuft.
- Der Cron-Teil `expai` läuft alle fünf Minuten (`php runcronjobs.php -q -s <admin siteaccess> expai`): Er setzt angehaltene Jobs
  fort, die wieder ins Budget passen, und räumt alte Daten auf.
- Ein Budget für eingereihte Arbeit, damit nichts unbeaufsichtigt eine Rechnung in die Höhe treibt ([Kostenkontrolle](../steuerung/kostenkontrolle.md)).

## Verwandte Seiten

- [Warteschlangen mit expqueue](../../ersteller/anwendungsentwickler/warteschlangen-mit-expqueue.md)
- [Muster für Hintergrundarbeit](../../ersteller/anwendungsentwickler/muster-fuer-hintergrundarbeit.md)
- [Vector und gestufte Prüfung](vektorsuche-und-gestufte-pruefung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
