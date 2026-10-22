---
title: Wirkung mit Website-Daten messen
audience: non-profit, businesses, project-manager, admin
category: non-profits
order: 60
summary: Was eine Exponential-Site selbst zählt (Formulareinsendungen, Newsletter-Abonnements, Aufrufzähler, Suchstatistiken, Shop-Zahlen, das Audit-Protokoll), was standardmäßig ausgeschaltet ist und vor dem Berichtszeitraum eingeschaltet werden muss, wofür ein Analysedienst wie bcwebsitestatistics nötig ist und welche Fragen zur Einwilligung damit verbunden sind.
keywords: Messen, Kennzahlen, Wirkung, KPIs, Aufrufzähler, Suchstatistiken, Webanalyse, Einwilligung, Audit-Protokoll, Non-Profit-Organisation, measuring, metrics, outcomes, view counts, search statistics, LogSearchStats, updateviewcount, analytics, bcwebsitestatistics, consent, audit trail, non-profit
type: help
---
# Wirkung mit Website-Daten messen

Diese Seite richtet sich an Programmverantwortliche und die technische Leitung. Sie sagt, was Exponential zählen kann,
was zuerst eingeschaltet werden muss und wofür ein externer Dienst nötig ist. Die wichtigste Regel: **Schalten Sie
Messungen vor dem Zeitraum ein, über den Sie berichten werden.** Zahlen, die nie aufgezeichnet wurden, lassen sich
nachträglich nicht erzeugen.

## Inhalt

1. [Was Exponential selbst zählt](#was-exponential-selbst-zählt)
2. [Einschalten, bevor Sie es brauchen](#einschalten-bevor-sie-es-brauchen)
3. [Ein Analysedienst](#ein-analysedienst)
4. [Einwilligung und Datenschutz](#einwilligung-und-datenschutz)
5. [Wenige gute Kennzahlen wählen](#wenige-gute-kennzahlen-wählen)
6. [Checkliste für die Einrichtung](#checkliste-für-die-einrichtung)

## Was Exponential selbst zählt

Aus [Besucherstatistiken und Analyse](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md) und
[Webanalyse und was Sie messen sollten](../../kunden/alltag/analyse-und-was-sie-messen-sollten.md):

| Quelle | Was sie zählt | Voreinstellung |
|---|---|---|
| Gesammelte Informationen | Jede Formulareinsendung, pro Formular | Eingeschaltet, sobald ein Formular existiert |
| `cjw_newsletter` | Abonnements pro Liste mit Status; versandte Ausgaben | Eingeschaltet, wenn der Newsletter eingerichtet ist |
| Aufrufzähler | Seitenaufrufe pro Knoten aus dem Zugriffslog, über den Cronjob-Teil `updateviewcount` | Der Cronjob-Teil muss laufen |
| Suchstatistiken | Gesuchte Begriffe, Häufigkeiten, durchschnittliche Trefferzahl | **Ausgeschaltet** (`LogSearchStats=disabled` in `settings/site.ini`) |
| Shop-Statistiken | Bestellungen und Umsatz | Eingeschaltet, wenn der Shop genutzt wird |
| Audit-Protokoll | Wer was in der Administrationsoberfläche geändert hat | Standardmäßig eingeschaltet |
| Request-Logs | Requests mit Zeitmessung, für den Betrieb | Siehe [Request-Logs](../../../funktionen/6.0/anfrageprotokolle.md) |

Diese Quellen beantworten betriebliche Fragen; sie sagen Ihnen nicht, woher Besucher kommen oder wie lange sie bleiben.

## Einschalten, bevor Sie es brauchen

| Wenn Sie berichten werden über | Stellen Sie vor Beginn des Zeitraums sicher |
|---|---|
| Wonach gesucht wurde | Suchstatistiken von einem Administrator eingeschaltet |
| Seitenaufrufe | Der Cronjob-Teil `updateviewcount` läuft ([Cronjobs](../../../anleitungen/cronjobs.md)) |
| Anmeldungen pro Kanal | Ein Formular pro Kanal oder ein verstecktes Feld, das die Herkunft angibt |
| Wachstum des Newsletters | Die Anzahl der bestätigten Abonnenten am ersten Tag, schriftlich festgehalten |
| Besucher und Herkunft | Ein Analysedienst installiert und die Einwilligung geregelt |

## Ein Analysedienst

- `bcwebsitestatistics` fügt den Seiten das Skript von Google Analytics hinzu und kann Shop-Bestellungen melden; Sie
  müssen Ihre eigene Tracking-ID (`Urchin`) in `settings/override/bcwebsitestatistics.ini.append.php` setzen
  ([bcwebsitestatistics](../../../funktionen/6.0/erweiterungen/bcwebsitestatistics.md)).
- Sie können stattdessen das Skript eines anderen Analysedienstes in das Seitenlayout einfügen
  ([Besucherstatistiken und Analyse](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md)).
- Eine Site, die eine Content Security Policy sendet, muss die Hosts des Dienstes erlauben
  ([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)).

## Einwilligung und Datenschutz

Analysedienste setzen meist Cookies und senden Besucherdaten an Dritte. In vielen Ländern erfordert das eine
Einwilligung, und Ihre Datenschutzerklärung muss den Dienst nennen. Lesen Sie [Cookies](../../verwaltung/datenschutz/cookies.md),
[Cookie-Banner und Einwilligung](../../verwaltung/datenschutz/cookie-banner-und-einwilligung.md) und
[Webanalyse](../../verwaltung/datenschutz/webanalyse.md), bevor Sie einen hinzufügen. Die Zahlen, die Exponential selbst
zählt, bleiben auf Ihrem Server.

> **Hinweis**
> Grundlagen, keine Rechtsberatung. Fragen Sie Ihre Datenschutzberatung, welche Regeln für Sie gelten.

## Wenige gute Kennzahlen wählen

Geldgeber ziehen drei ehrliche Zahlen dreißig vagen vor. Gute Kennzahlen für die Site einer Non-Profit-Organisation:

| Kennzahl | Warum sie gut ist |
|---|---|
| Bewerbungen von Ehrenamtlichen pro Monat | Direkt mit einem Ziel verbunden, von der Site gezählt |
| Hilfeanfragen über das Formular | Zeigt, dass Menschen das Angebot erreicht haben |
| Bestätigte Newsletter-Abonnenten | Beruht auf Einwilligung, schwer aufzublähen |
| Downloads eines zentralen Materials | Zeigt die Nutzung dessen, was die Förderung hervorgebracht hat |
| Suchen ohne Treffer | Zeigt, worüber Sie als Nächstes schreiben sollten |

Vermeiden Sie Kennzahlen, die Sie einem Vorstandsmitglied nicht in einem Satz erklären können.

## Checkliste für die Einrichtung

- [ ] Drei bis fünf Kennzahlen gewählt, jede an ein Ziel gebunden.
- [ ] Suchstatistiken eingeschaltet, falls nötig.
- [ ] Der Cronjob-Teil `updateviewcount` läuft, falls Seitenaufrufe wichtig sind.
- [ ] Über einen Analysedienst entschieden, Einwilligung geregelt, Datenschutzerklärung aktualisiert.
- [ ] Ausgangszahlen mit Datum festgehalten.
- [ ] Eine monatliche Erinnerung, die Zahlen zu erfassen ([Monatliche Checkliste](../checklisten/monatliche-checkliste.md)).

## Verwandte Seiten

- [Fördermittel und die Website](README.md)
- [Förderergebnisse für Geldgeber](foerderergebnisse-fuer-geldgeber.md)
- [Statistiken und Analyse](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
