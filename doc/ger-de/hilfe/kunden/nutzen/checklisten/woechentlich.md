---
title: Wöchentliche Checkliste
audience: customer, editor, admin
category: customer-success
order: 20
summary: Dreißig Minuten pro Woche für die Website-Leitung einer Exponential-Website - Inhalte, die Aufmerksamkeit brauchen, kaputte Links und nicht gefundene Seiten, Konten, Suchen ohne Treffer, der Shop und die Fragen an den Hoster - mit den eigenen wöchentlichen Prüfungen des Administrators.
keywords: wöchentliche Checkliste, wöchentliche Routine, kaputte Links, 404, nicht gefunden, Suchbegriffe, Konten, Backups, Fragen an den Hoster, Website-Leitung, weekly checklist, weekly routine, broken links, not found, search phrases, accounts, backups, host questions, site manager
type: help
---
# Wöchentliche Checkliste

Diese Seite ist die wöchentliche Prüfung für die Website-Leitung, idealerweise zu einem festen Termin. Sie schaut etwas
weiter als die tägliche Liste: was sich während der Woche verschoben hat. Die technischen wöchentlichen Prüfungen gehören
Ihrem Administrator oder Hoster; der letzte Abschnitt sagt, was Sie sie fragen.

## Die Checkliste

```text
WEEKLY CHECKLIST - week of ________     Done by: ________

CONTENT
[ ] Old drafts reviewed: finish, hand over, or remove (My drafts)
[ ] Pages with dates (events, offers, opening hours) still right
[ ] New content of the week has images with alternative text

LINKS AND ADDRESSES
[ ] Broken links list looked at (Setup > Link management), the worst fixed
[ ] Not-found pages of the week: any address visitors use that needs a redirect?

VISITORS
[ ] Search terms without results noted; content or synonyms to add
[ ] Questions from visitors this week: any the site should answer?

ACCOUNTS
[ ] New staff accounts intended; leavers of the week disabled
[ ] Nobody shares an account

STORE (if you sell)
[ ] Store weekly checklist done

ASK THE ADMINISTRATOR OR HOST (answer "yes" expected)
[ ] Did every backup of the week run?
[ ] Do the cron jobs run?
[ ] Is the audit chain intact?
[ ] Anything new in the error log?
```

## Wo jeder Punkt erledigt wird

| Punkt | Wo | Lesen Sie |
|---|---|---|
| Entwürfe | **Meine Entwürfe** | [Veröffentlichen und Versionen](../../../veroeffentlichen-und-versionen.md) |
| Kaputte Links | **Setup > URL-Verwaltung** (`/url/list`); die Linkprüfung läuft im Cron-Teil `infrequent` | [Kaputte Links und Inhaltsprüfungen](../../../ersteller/website-ersteller/link-und-inhaltspruefungen.md) |
| Nicht gefundene Seiten | `404.log`, gelesen unter **Setup > Logdateien** | [Request-Logs](../../../../funktionen/6.0/anfrageprotokolle.md) |
| Weiterleitungen für verschobene Seiten | URL-Aliase und Wildcards | [URLs und Aliase](../../../../anleitungen/urls-und-aliase.md) |
| Suchbegriffe | Die Suchstatistiken der Website | [Website-Suche](../../../ersteller/website-ersteller/suche-auf-der-website.md) |
| Konten | **Benutzer > Benutzerkonten prüfen** | [Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md) |
| Shop | Shop-Dashboard | [Wöchentliche Shop-Checkliste](../../handel/woechentliche-shop-checkliste.md) |

## Die wöchentlichen Prüfungen des Administrators

Der Leitfaden für Betreiber listet eine wöchentliche Checkliste, die Ihr Administrator oder Hoster in wenigen Minuten
durchgehen kann ([Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md), Abschnitt 10):

1. Der Cache-Status endet mit PASS.
2. Der Status des Prüfprotokolls zeigt jede Kette unversehrt.
3. Das Fehlerlog zeigt nichts Neues, das nicht verstanden ist.
4. Die Cron-Einträge sind noch installiert.
5. Ein Backup der letzten sieben Tage existiert und wurde mindestens einmal auf einem anderen Rechner wiederhergestellt.

Sie müssen diese Prüfungen nicht selbst ausführen. Bitten Sie einmal pro Woche um die fünf Antworten, per E-Mail oder in
einem kurzen Gespräch.

> **Hinweis**
> Ein „Ja“ auf „Ist das Backup gelaufen?“ ist nicht dasselbe wie ein Backup, das sich wiederherstellen lässt. Fragen Sie,
> wann der letzte Wiederherstellungstest war, und planen Sie einen pro Quartal ([Vierteljährliche Checkliste](vierteljaehrlich.md)).

## Verwandte Seiten

- [Tägliche Checkliste](taeglich.md)
- [Monatliche Checkliste](monatlich.md)
- [Wöchentliche Shop-Checkliste](../../handel/woechentliche-shop-checkliste.md)
- [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
