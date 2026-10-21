---
title: "Lektion 7.3: Fehlersuche: welches Layout eine Seite bekommt"
audience: trainer, editor, website-creator
category: training
order: 73
summary: Die Teilnehmenden diagnostizieren die üblichen Zuordnungsprobleme, einen Treffer mit höherer Priorität, ein unveröffentlichtes Layout, eine zwischengespeicherte Seite, ein gebrochenes Pfadziel, eine falsch geschriebene Bedingung, und beheben jedes davon. 45 Minuten.
keywords: exponential layouts, fehlersuche, zuordnung, priorität, cache, resolver-cache, unveröffentlichtes layout, pfadziel, tippfehler in bedingung, altes layout, diagnose, troubleshooting, mapping, priority, resolver cache, unpublished layout, path target, condition typo, old layout, diagnose
type: help
---
# Lektion 7.3: Fehlersuche: welches Layout eine Seite bekommt

„Ich habe veröffentlicht, und die Seite sieht immer noch gleich aus.“ Das hört jedes Layouts-Team. Diese Lektion macht
aus der Fehlersuchtabelle der Dokumentation eine Übung: Der Trainer baut auf der Schulungs-Website einen Fehler ein,
die Teilnehmenden finden ihn.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 7: Zuordnungsregeln](README.md) |
| Dauer | 45 Minuten (10 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Ersteller und erfahrene Redakteure |
| Voraussetzungen | [Lektion 7.2](eine-zuordnung-erstellen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die fünf üblichen Ursachen für „die Seite zeigt das falsche Layout“ aufzählen;
- sie in fester Reihenfolge prüfen: Zuordnungen, Veröffentlichungsstatus, Caches, Ziele, Bedingungen;
- jede Ursache beheben;
- erklären, wann ein Leeren des Caches nötig ist und wer es durchführt.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Layout-Regeln: Fehlersuche](../../../ersteller/website-ersteller/layout-regeln.md#fehlersuche) und
[Exponential Layouts: Fehlersuche](../../../../bc/6.0/layouts.md#fehlerbehebung).

1. **Die Tabelle** (6 Minuten). Zeigen Sie sie am Beamer:

   | Symptom | Prüfen |
   |---|---|
   | Die Seite zeigt das alte Layout | eine Zuordnung mit höherer Priorität passt zuerst; das Layout ist nicht veröffentlicht; der Seiten-Cache enthält noch die alte Seite |
   | Eine Zuordnung gilt für zu viele Seiten | ein falsch geschriebener Bedingungstyp passt auf alles; das Ziel ist ein breiter Teilbaum |
   | Eine Zuordnung funktioniert nach einer Umbenennung nicht mehr | sie verwendet ein Pfadziel; wechseln Sie zu Knoten oder Teilbaum |
   | Nach dem Veröffentlichen ändert sich nichts | der Cache für Inhaltsansichten und, wo die Website einen verwendet, der Antwort-Cache des Webservers |

2. **Caches** (4 Minuten). Der Layouts-Resolver speichert zwischen, welches Layout eine Anfrage bekommt. Nach Änderungen
   im Editor und auf den Zuordnungsbildschirmen leert der Editor die Caches oder bietet es an; **More actions** einer
   Zuordnung enthält ein Cache-Leeren für diese eine Zuordnung. Nach Änderungen außerhalb des Editors (ein Import, ein
   Skript) leert ein Administrator die Caches, wie es die Dokumentation der Erweiterung beschreibt.

### Häufige Fragen

**Warum funktioniert es angemeldet, aber nicht abgemeldet?** Besucher bekommen möglicherweise eine zwischengespeicherte
Kopie; die eigene Ansicht des Editors ist frisch.

**Dürfen Redakteure Caches leeren?** Manche Rollen dürfen es (das Kästchen **Cache leeren** in der rechten Spalte oder
das Cache-Leeren der Zuordnung); sonst fragen Sie einen Administrator.

**Wie sehe ich, welche Zuordnung für eine Seite gewonnen hat?** Gehen Sie die Liste wie in Lektion 7.1 in der
Reihenfolge der Priorität durch; der erste aktivierte Treffer gewinnt.

### Stolperfallen

- **Zuerst die Caches leeren.** Das verdeckt die eigentliche Ursache, wenn es ein Prioritätsproblem ist. Prüfen Sie
  zuerst die Zuordnungen.
- **Die Zuordnung einer anderen Person reparieren.** Die Teilnehmenden beheben nur die Probleme auf ihren eigenen
  Testseiten.
- **Prioritäten pauschal ändern.** Ändern Sie eine Zuordnung nach der anderen und prüfen Sie jedes Mal.

## Praktische Übung

Verwenden Sie die Schulungsinstallation. Vor der Übung beschädigt der Trainer die Testseite jeder Person auf eine dieser
Arten (notiert auf einer Karte, die die Person nicht sieht):

- eine neue aktivierte Zuordnung mit höherer Priorität und einem anderen Layout, die auf die Testseite passt;
- das Kurs-Layout hat unveröffentlichte Änderungen, die die Person zu sehen erwartet;
- die Zuordnung der Person ist deaktiviert;
- die Zuordnung der Person verwendet ein Pfadziel, und die Testseite wurde umbenannt.

1. Öffnen Sie Ihre Testseite auf der Website in einem privaten Fenster. Beschreiben Sie, was nicht stimmt.
2. Öffnen Sie **Layout-Zuordnungen** und lesen Sie jede Zuordnung, die auf Ihre Seite passen könnte, in der
   Reihenfolge der Priorität.
3. Prüfen Sie, ob Ihre eigene Zuordnung aktiviert ist.
4. Prüfen Sie, ob Ihr Kurs-Layout einen Entwurf mit unveröffentlichten Änderungen hat (öffnen Sie es; der Editor bietet
   Ihren Entwurf an).
5. Prüfen Sie den Zieltyp Ihrer Zuordnung: Knoten, Teilbaum oder Pfad?
6. Beheben Sie die gefundene Ursache: die konkurrierende Zuordnung deaktivieren oder herabstufen (wenn es eine für Sie
   angelegte Schulungszuordnung ist), Ihr Layout veröffentlichen, Ihre Zuordnung aktivieren oder ein Pfadziel durch ein
   Knotenziel ersetzen.
7. Verwenden Sie das Cache-Leeren der Zuordnung (oder fragen Sie den Trainer) und laden Sie die Seite abgemeldet neu.
8. Vergleichen Sie mit der Karte des Trainers.

## Erwartetes Ergebnis

- Alle Teilnehmenden haben die Ursache auf ihrer Karte gefunden und behoben, und ihre Testseite zeigt wieder ihr
  Kurs-Layout.
- Die Teilnehmenden können die fünf üblichen Ursachen der Reihe nach nennen.
- Der Trainer prüft das, indem er die Karten vorliest und jede Person fragt, wie sie den Fehler gefunden hat.

![Der Reiter Layouts einer Seite: das Layout, zu dem sie aufgelöst wird, und ihre Zuordnungen](../../../../../eng-us/help/images/trainers/ly-tab.webp)

## Quiz

1. Nennen Sie drei Ursachen dafür, dass eine Seite das alte Layout zeigt.
2. In welcher Reihenfolge prüfen Sie: Caches, Zuordnungen, Veröffentlichungsstatus?
3. Eine Zuordnung gilt plötzlich für die ganze Website. Was vermuten Sie?
4. Warum sollten Sie nicht zuerst die Caches leeren?
5. Eine Zuordnung funktioniert nicht mehr, nachdem eine Seite umbenannt wurde. Was ist die Lösung?
   A. Den Cache leeren B. Das Pfadziel durch ein Knotenziel ersetzen C. Die Priorität erhöhen D. Das Layout erneut
   veröffentlichen

<details>
<summary>Antworten</summary>

1. Drei beliebige aus: Eine Zuordnung mit höherer Priorität passt zuerst; das Layout ist nicht veröffentlicht; die
   zwischengespeicherte Kopie der Seite; ein Pfadziel nach einer Umbenennung; eine deaktivierte Zuordnung.
2. Zuordnungen, dann Veröffentlichungsstatus, dann Caches.
3. Einen falsch geschriebenen Bedingungstyp (er passt auf alles) oder ein breites Teilbaumziel
   ([Layout-Regeln](../../../ersteller/website-ersteller/layout-regeln.md#fehlersuche)).
4. Es verdeckt ein Prioritäts- oder Veröffentlichungsproblem, das wiederkommen würde.
5. **B.**

</details>

## Weiterführende Informationen

- [Layout-Regeln: Fehlersuche](../../../ersteller/website-ersteller/layout-regeln.md#fehlersuche)
- [Exponential Layouts: Resolver-Caching](../../../../bc/6.0/layouts.md#caching-des-resolvers)

## Verwandte Seiten

- [Modul 7: Zuordnungsregeln](README.md)
- [Exponential Layouts: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
