---
title: Fehler gut melden
audience: partner
category: partnership
order: 660
summary: Wie Partner Fehlerberichte einreichen, die behoben werden - welcher Tracker, wie Sie auf einer sauberen Installation nachstellen, was hineingehört, wie Sie Kundendaten und Geheimnisse entfernen, wie Sie nachfassen und wann Sie die Korrektur selbst senden.
keywords: Fehler, Fehlerbericht, Issue, GitHub, nachstellen, Tracker, exponential-community, Umgebung, nachfassen, Korrektur, bug, bug report, reproduce, environment, follow up, fix
type: help
---
# Fehler gut melden

Diese Seite ist für alle in einem Partnerunternehmen, die einen Fehler finden. Ein Fehlerbericht bringt 50 Punkte
(<https://exponential.earth/partner/requirements>), aber ein guter ist weit mehr wert: Er ist der erste Schritt zu
einer Korrektur, die alle Ihre Kunden schützt.

## Welcher Tracker

| Problem | Tracker | Quelle |
|---|---|---|
| Ein Fehler in der Software Exponential | <https://github.com/se7enxweb/exponential/issues> | README, [Wenn etwas schiefgeht](../../../en/us/help/when-something-goes-wrong.md) |
| Ein Fehler in einer Erweiterung | Das Repository dieser Erweiterung | ihre `ezinfo.php` / `extension.xml` |
| Das Projekt, seine Websites oder die Community | <https://github.com/se7enxweb/exponential-community/issues> | README |
| Eine Sicherheitslücke | **Kein Tracker**: E-Mail an security@se7enx.com | SECURITY.md |

## Bevor Sie melden

- [ ] Stellen Sie den Fehler auf einer sauberen Installation eines aktuellen Release nach, möglichst ohne den Code Ihres Kunden.
- [ ] Durchsuchen Sie offene und geschlossene Issues.
- [ ] Notieren Sie die genauen Versionen: Exponential, PHP, Datenbank, Engine, Erweiterungen.

## Was hineingehört

Die Anleitung der Hilfe enthält die vollständige Checkliste: [Wenn etwas schiefgeht, „Eine gute Fehlerbeschreibung“](../../../en/us/help/when-something-goes-wrong.md#a-good-bug-description).
Kurz: Schritte, erwartetes Ergebnis, tatsächliches Ergebnis, Versionen, Logzeilen.

```text
Title: <what breaks> when <doing what> (Exponential <version>)

Steps:
1. ...
Expected: ...
Actual: ...
Environment: Exponential <version>, PHP <x.y>, <database>, <engine>, extensions: <...>
Log (secrets and customer data removed):
<lines>
```

## Vor dem Veröffentlichen entfernen

Kundennamen, E-Mail-Adressen, private URLs, Hostnamen von Datenbanken, Schlüssel, Passwörter, Session-IDs. Ersetzen Sie
sie durch Platzhalter ([Kundendaten schützen](kundendaten-schuetzen.md)).

## Nachfassen

- Beantworten Sie Fragen im Issue schnell; eine unbeantwortete Frage ist der häufigste Grund, warum ein Fehler stockt.
- Testen Sie die Korrektur, wenn sie auf `main` landet ([Mit Git entwickeln, „Eine Korrektur vor dem Release nutzen“](../../../en/us/help/develop-with-git.md#use-a-fix-before-it-is-released)).
- Schließen Sie den Kreis: Sagen Sie, dass es bei Ihnen behoben ist.

## Die Korrektur senden

Wenn Sie die Korrektur kennen, senden Sie sie: [Wenn etwas schiefgeht, „Noch besser: die Korrektur senden“](../../../en/us/help/when-something-goes-wrong.md#even-better-send-the-fix)
und [Code zurückgeben](code-zurueckgeben.md).

## Verwandte Seiten

- [Ein Support-Ticket als Partner eröffnen](support-ticket-eroeffnen.md)
- [Releases testen, bevor Ihre Kunden es tun](releases-testen.md)
- [Wenn etwas schiefgeht](../../../en/us/help/when-something-goes-wrong.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
