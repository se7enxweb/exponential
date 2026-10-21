---
title: Inhalte in Prüfung mit Objektzuständen markieren
audience: government, editor
category: public-sector
order: 560
summary: Für Redakteure - eine Seite mit einem Objektzustand auf dem Reiter Details (Zustand des Objekts, Zustände einstellen) als in Prüfung, freigegeben, ersetzt oder zurückgezogen kennzeichnen, für eine Seite oder einen ganzen Teilbaum, ohne eine neue Version anzulegen; und was Zustände leisten und was nicht.
keywords: Objektzustände, Zustand des Objekts, Zustände einstellen, Reiter Details, in Prüfung, ersetzt, zurückgezogen, Zustandsgruppe, Teilbaum, NewState, Inhalte kennzeichnen, object states, Content state, Set states, Details tab, under review, superseded, withdrawn, state group, subtree, label content
type: help
---
# Inhalte in Prüfung mit Objektzuständen markieren

Diese Seite ist für Redakteure, die Inhalte mit einem Status kennzeichnen: *In Prüfung*, *Freigegeben*, *Ersetzt*,
*Zurückgezogen*. Objektzustände sind Kennzeichen aus Zustandsgruppen, die Ihre Administratoren anlegen; das Setzen
eines Zustands erzeugt keine neue Version und ändert den Text nicht
([Objektzustände, 7](../../../../anleitungen/objektzustaende.md#7-zustände-an-inhalten-setzen)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Was Zustände leisten](#was-zustände-leisten)
4. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Eine Zustandsgruppe mit den Zuständen, die Sie brauchen, existiert (**Setup > Zustände**, von einem Administrator
  angelegt).
- Ihre Rolle hat `state/assign`; die Einschränkung `NewState` erlaubt Ihnen vielleicht nur einige Zustände (zum
  Beispiel nicht *Zurückgezogen*).

## Schritte

**Eine Seite:**

1. Öffnen Sie die Seite, Reiter **Details**, Kasten **Zustand des Objekts**.
2. Wählen Sie in der Auswahlliste der Gruppe den Zustand; klicken Sie auf **Zustände einstellen**.

**Eine Seite und alles darunter:**

1. Derselbe Kasten; markieren Sie vor **Zustände einstellen** die Option **Auch für alles unterhalb dieses Knotens**.
2. Große Teilbäume laufen nach einer Bestätigung als Hintergrundauftrag; verfolgen Sie ihn unter `/content/jobs`.

**Beim Bearbeiten eines Entwurfs** einer neuen Seite hat die Bearbeitungsansicht dieselben Auswahllisten mit
**Setzen**.

## Was Zustände leisten

| Zustände leisten | Zustände leisten nicht |
|---|---|
| Inhalte kennzeichnen; Templates können das Kennzeichen anzeigen („Ersetzt“) | Inhalte von sich aus verstecken |
| Rollen das Lesen oder Bearbeiten nach Zustand einschränken lassen (zum Beispiel lesen nur Prüfer Inhalte *In Prüfung*) | eine neue Version anlegen oder Text ändern |
| Im Audit erscheinen | Inhalte verschieben |

Ob Besucher einen Zustand sehen und ob ein Zustand Inhalte vor ihnen versteckt, hängt von Ihren Templates und Rollen
ab. Fragen Sie das Web-Team, was jeder Zustand auf Ihrer Site bewirkt.

## Das Ergebnis prüfen

- [ ] Der Kasten **Zustand des Objekts** zeigt den neuen Zustand.
- [ ] Die Seite zeigt das Kennzeichen dort, wo Ihre Templates es anzeigen (abgemeldet, wenn es ein öffentliches
  Kennzeichen ist).

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Erwarten, dass *Zurückgezogen* die Seite versteckt | verstecken Sie sie zusätzlich, wenn sie verschwinden muss ([Eine Seite vorübergehend verstecken](eine-seite-voruebergehend-verstecken.md)) |
| Die Auswahlliste ist deaktiviert | Sie dürfen in dieser Gruppe nur den aktuellen Zustand setzen |
| Gruppen, deren Bezeichner mit `ez` beginnt | gehören zum System (`ez_lock`) und sind nicht für redaktionelle Zustände gedacht |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Leitfäden für regulierte Akteure](../../../../../eng-us/help/government/levels/agencies/guidance-for-regulated-parties.md)
- [Sektionen und Objektzustände](../../../../../eng-us/help/creators/website-creators/sections-and-object-states.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
