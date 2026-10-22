---
title: Übersetzungshilfe
audience: ai-agents, editor, admin
category: ai-agents
order: 27
summary: Wie Exponential Intelligence bei Übersetzungen hilft - die Feldaktion Translate, im Reiter Intelligence um einen übersetzten Entwurf bitten und der Übersetzungsabgleich, der jede Übersetzung nach einer Änderung der Quelle einordnet (unverändert, Patch oder vollständige Neuübersetzung), kleine Patches unter einer Kostenschwelle einreiht und den Rest einem Redakteur zur Freigabe überlässt.
keywords: Übersetzung, maschinelle Übersetzung, KI-Übersetzung, Feldaktion Translate, Übersetzungsabgleich, Patch, vollständige Neuübersetzung, Kostenschwelle, Ausgangssprache, expai/translation_sync, Workflow-Event, mehrsprachig, Prüfung, translation, machine translation, AI translation, translation sync, full retranslation, cost threshold, source language, multilingual, review
type: help
---
# Übersetzungshilfe

Diese Seite richtet sich an Redakteure und Administratoren mehrsprachiger Websites, die Hilfe beim Übersetzen von Inhalten möchten.
Exponential Intelligence bietet drei Ebenen: ein Feld, ein Objekt und das Gleichhalten jeder Übersetzung mit ihrer Quelle. In jedem
Fall ist das Ergebnis ein **Entwurf** für einen Menschen, der die Zielsprache liest und ihn prüft.

## Inhalt

1. [Ein Feld: die Aktion Translate](#ein-feld-die-aktion-translate)
2. [Ein Objekt: im Reiter Intelligence fragen](#ein-objekt-im-reiter-intelligence-fragen)
3. [Jede Übersetzung: der Übersetzungsabgleich](#jede-übersetzung-der-übersetzungsabgleich)
4. [Den Übersetzungsabgleich einrichten](#den-übersetzungsabgleich-einrichten)
5. [Maschinelle Übersetzungen prüfen](#maschinelle-übersetzungen-prüfen)

## Ein Feld: die Aktion Translate

Im Bearbeitungsformular übersetzt die Feldaktion **Translate** den Wert eines Feldes in eine andere Sprache der Website; sie fragt
nach der Sprache. Der neue Wert kommt mit einem Link **Undo** in das Feld; nichts wird gespeichert, bis Sie den Entwurf speichern
oder veröffentlichen ([Was Exponential Intelligence tut](was-exponential-intelligence-tut.md#das-bearbeitungsformular)).

## Ein Objekt: im Reiter Intelligence fragen

Im Reiter **Intelligence** können Sie zum Beispiel fragen "Translate this article into German as a new draft." Der Assistent
schreibt einen Entwurf der Übersetzung als Sie, mit Ihrer Berechtigung, in dieser Sprache zu bearbeiten. Sie prüfen ihn und
veröffentlichen ihn oder senden ihn zur Freigabe.

## Jede Übersetzung: der Übersetzungsabgleich

Ändert sich die Ausgangssprache eines Objekts, sind seine Übersetzungen veraltet. Der Übersetzungsabgleich findet, was sich
geändert hat, und entscheidet je Übersetzung, wie sie aktualisiert wird
([Dokumentation der Erweiterung: Übersetzungsabgleich](https://github.com/se7enxweb/expai/blob/main/doc/translation-sync.md)):

| Entscheidung | Wann | Was passiert |
|---|---|---|
| `noop` | Nichts Übersetzbares hat sich geändert | Die Übersetzung wird als aktuell markiert |
| `patch` | Ein kleiner Anteil hat sich geändert, wenige Blöcke, die Schätzung auf oder unter der Kostenschwelle | **Sofort eingereiht**; nur die geänderten Blöcke werden gesendet, mit ihrer alten und neuen Quelle und der aktuellen Übersetzung dieses Blocks |
| `patch`, wartet | Ein Patch über der Schwelle oder mit mehr Änderungen | Wartet auf die Freigabe durch einen Redakteur |
| `full` | Der größte Teil der Quelle hat sich geändert, die Übersetzung hat nicht mehr die Struktur ihrer Basis, oder es wurde keine Basis gefunden | Wartet auf die Freigabe durch einen Redakteur |

Die Regel in einem Satz: **Patches unter der Kostenschwelle werden automatisch eingereiht; vollständige Übersetzungen und alles über
der Schwelle warten auf einen Redakteur.** Markup reist als Platzhalter, sodass Links, Hervorhebungen und eingebettete Objekte
genau bleiben, wie sie sind; unveränderte Blöcke behalten die bestehende Übersetzung Byte für Byte.

Das Ergebnis ist immer ein **Entwurf** der Übersetzung, gekennzeichnet mit Exponential Intelligence. Nichts wird veröffentlicht.

Im Reiter Intelligence erhalten Objekte mit mehr als einer Sprache einen Abschnitt **Translations**, der jede Übersetzung mit ihrem
Status, ihrer Entscheidung, dem geänderten Anteil, der Schätzung und dem Entwurf auflistet. Benutzer mit `expai/translation_sync`
(und `expai/write`) haben die Schaltflächen Check translations, Approve, Translate in full und Skip; Approve und Skip brauchen
außerdem die Berechtigung, in dieser Sprache zu bearbeiten.

## Den Übersetzungsabgleich einrichten

1. Die Job-Warteschlange (Erweiterung `expqueue`) muss laufen, mit ihrem Cron-Teil oder einem Worker.
2. Fügen Sie das Workflow-Event **Exponential Intelligence: translation sync** einem Workflow hinzu, der mit dem Auslöser
   **content / publish / after** verbunden ist. Hat dieser Auslöser bereits einen Workflow, fügen Sie das Event diesem hinzu (ein
   Auslöser hat einen Workflow). Das Event hält nie eine Veröffentlichung an.
3. Geben Sie der Rolle der Übersetzer `expai/translation_sync`.
4. Prüfen Sie die Kostenschwelle und die Patch-Grenzen in `[TranslationSync]` Ihrer expai-Einstellungen.

Auf der Kommandozeile, für ein Objekt:

```
./console ext:expai:translation-sync --object=<id> --languages=<language> --approve --login=<user>
```

## Maschinelle Übersetzungen prüfen

Eine maschinelle Übersetzung ist ein erster Entwurf, kein fertiger Text. Bevor Sie eine veröffentlichen:

- Lassen Sie sie von jemandem lesen, der die Zielsprache und das Thema kennt.
- Prüfen Sie Namen, Zahlen, Daten, Einheiten und Rechtsbegriffe gegen die Quelle.
- Prüfen Sie, dass Links weiterhin auf die Seite in der richtigen Sprache verweisen.
- Achten Sie auf Text, der in der Quelle richtig war, in der Zielsprache aber seltsam klingt.

Der Leitfaden für Fürsprecher [Eine maschinengestützte Übersetzung prüfen](../../fuersprecher/maschinell-unterstuetzte-uebersetzung-gegenlesen.md)
und die [Prüfcheckliste](../checklisten/ki-entwuerfe-pruefen.md) gehen weiter.

## Verwandte Seiten

- [Sprachen](../../sprachen.md)
- [Mehrsprachige Unternehmen](../../unternehmen/mehrsprachig/README.md)
- [Ketten und eingereihte Arbeit](ketten-und-eingereihte-arbeit.md)
- [Kostenkontrolle](../steuerung/kostenkontrolle.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
