---
title: Defekte Links finden und reparieren
audience: government, editor
category: public-sector
order: 440
summary: Für Redakteure und Web-Verantwortliche - als ungültig markierte Links unter Setup > URL-Verwaltung finden, sehen, welche Seiten sie verwenden, die Adresse einmal für jede Seite korrigieren oder die Seiten bearbeiten, und wissen, wie und wann die Linkprüfung läuft.
keywords: defekte Links, Linkprüfung, URL-Verwaltung, url/list, ungültige Links, linkcheck.php, Verwendet von, Adresse bearbeiten, Ausgewählte als gültig markieren, externe Links, broken links, link check, Link management, invalid links, Used by, Edit address, Mark selected valid, external links
type: help
---
# Defekte Links finden und reparieren

Diese Seite ist für Redakteure und Web-Verantwortliche, die dafür sorgen, dass Links funktionieren. Links zu anderen
Sites brechen, wenn sich diese Sites ändern. Exponential prüft Links mit einem Cronjob und listet sie unter
**Setup > URL-Verwaltung** (`/url/list`)
([URLs und Aliase, 1 und 2](../../../../anleitungen/urls-und-aliase.md#1-die-linkliste)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Sie können **Setup > URL-Verwaltung** öffnen (bitten Sie das Web-Team um Zugang oder um eine monatliche Liste).
- Die Linkprüfung läuft: Es ist das Skript `linkcheck.php` im seltenen Teil der Cronjobs. Die Angabe
  **Last link check** der Seite sagt, wann sie zuletzt lief.

## Schritte

1. **Setup > URL-Verwaltung**. Klicken Sie auf die Zahl **Ungültig** (oder **Anzeigen** > Ungültig).
2. Für jede Karte: die Adresse, ihre Kennzeichen, die Zeit **Überprüft** und **Verwendet von** (bis zu drei Seiten
   und „und N weitere“, das zur eigenen Seite des Links mit jeder Version führt, die ihn verwendet).
3. Öffnen Sie die Adresse (**Öffnen**), um zu sehen, was nicht stimmt: verschwunden, umgezogen oder vorübergehend nicht
   erreichbar.
4. Reparieren Sie ihn:
   - die Adresse ist umgezogen und jede Seite soll die neue verwenden: **Bearbeiten** auf der Karte ändert die Adresse
     an einer Stelle für jedes Objekt, das sie verwendet;
   - der Link soll weg: Bearbeiten Sie jede Seite unter **Verwendet von** und entfernen oder ersetzen Sie den Link;
   - die Site verweigert automatische Prüfungen, funktioniert aber für Menschen: **Ausgewählte als gültig markieren**
     (die nächste Prüfung kann das wieder ändern).
5. Links innerhalb der Site zu Inhalten, die entfernt oder versteckt wurden, erscheinen ebenfalls als ungültig:
   Verlinken Sie auf die Ersatzseite.

## Das Ergebnis prüfen

- [ ] Der Zustand der Karte ändert sich nach der nächsten Linkprüfung (oder markieren Sie ihn von Hand).
- [ ] Die Seiten unter **Verwendet von** funktionieren, wenn Sie abgemeldet auf den Link klicken.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| „Nicht entschieden“ als defekt behandeln | die Antworten 401, 403, 429 und 503 sind nicht entschieden; prüfen Sie von Hand |
| Die Adresse bearbeiten, wenn sich nur eine Seite ändern soll | **Bearbeiten** ändert sie für jede Seite; bearbeiten Sie stattdessen die Seite |
| Seiten ohne Weiterleitungen entfernen | erzeugt anderswo defekte Links ([Alte Adressen nach einer Umstrukturierung weiterleiten](alte-adressen-nach-einer-umstrukturierung-weiterleiten.md)) |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Einen Link zu einem externen Dienst hinzufügen](einen-link-zu-einem-externen-dienst-hinzufuegen.md)
- [Link- und Inhaltsprüfungen](../../../ersteller/website-ersteller/link-und-inhaltspruefungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
