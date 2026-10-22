---
title: Fördermittel und die Website
audience: non-profit, businesses, government, project-manager
category: non-profits
order: 10
summary: Wie eine Non-Profit-Organisation ihre Exponential-Website in die Arbeit mit Fördermitteln einbindet - ein Website-Projekt, das aus Fördermitteln bezahlt wird, der technische Teil eines Antrags, ehrliche Budgetposten für Hosting und Wartung und Wirkungsberichte, die sich auf Belege stützen, die die Site selbst aufzeichnet. Ein Modul zur Förderverwaltung gibt es nicht; dieser Ordner sagt, was die Site liefert und was nicht.
keywords: Fördermittel, Förderung, gefördertes Projekt, Förderantrag, Antrag, Budget, Hosting-Budget, Wartungsbudget, Wirkungsbericht, Belege, Geldgeber, Stiftung, Non-Profit-Organisation, grants, grant-funded project, proposal, budget, hosting budget, maintenance budget, outcome report, evidence, funder, foundation, non-profit
type: help
---
# Fördermittel und die Website

Dieser Ordner richtet sich an Antragsschreiber, Geschäftsführungen und Projektleitungen. Eine Website kann selbst
Gegenstand einer Förderung sein (eine neue Site, ein Relaunch, eine Übersetzung in eine neue Sprache) oder ein
Werkzeug, mit dem ein gefördertes Programm umgesetzt und dokumentiert wird. Beides braucht einen Plan, ein Budget, das
nicht am Tag des Starts endet, und Belege für den Abschlussbericht.

> **Hinweis**
> Exponential hat **kein Modul zur Förderverwaltung**: keine Antragsverfolgung, keine Datenbank der Geldgeber, keinen
> Berichtsgenerator. Eine Suche im Code und in der Dokumentation findet nichts dergleichen. Was die Site liefert, sind
> Belege: die Seiten, die Sie veröffentlicht haben, die Dateien, die heruntergeladen wurden, die Formulare, die
> abgeschickt wurden, die Zahlen, die Exponential aufzeichnet, und das Audit-Protokoll. Dieser Ordner zeigt, wie Sie
> sie nutzen.

## Inhalt

1. [Die Seiten dieses Ordners](#die-seiten-dieses-ordners)
2. [Drei Arten von Förderung](#drei-arten-von-förderung)
3. [Was die Site als Beleg liefern kann](#was-die-site-als-beleg-liefern-kann)
4. [Wo Sie beginnen](#wo-sie-beginnen)

## Die Seiten dieses Ordners

| Seite | Was sie beantwortet |
|---|---|
| [Projektplan für eine geförderte Website](projektplan-fuer-eine-gefoerderte-website.md) | Phasen, Ergebnisse, Rollen und ein Zeitplan, dem ein Geldgeber folgen kann |
| [Der technische Teil eines Förderantrags](technischer-teil-des-foerderantrags.md) | Wie Sie die Plattform, ihre Lizenz, das Hosting, die Barrierefreiheit und die Nachhaltigkeit beschreiben |
| [Budgetposten für Hosting und Wartung](budgetposten-fuer-hosting-und-wartung.md) | Welche Kosten Sie während und nach der Förderung einplanen |
| [Förderergebnisse für Geldgeber](foerderergebnisse-fuer-geldgeber.md) | Wie aus dem, was die Site aufzeichnet, ein Wirkungsbericht wird |
| [Wirkung mit Website-Daten messen](wirkung-mit-website-daten-messen.md) | Was Exponential selbst zählt und wofür ein zusätzlicher Dienst nötig ist |

Arbeitsblätter: [Arbeitsblatt Förderbudget](../vorlagen/arbeitsblatt-foerderbudget.md).

## Drei Arten von Förderung

| Art | Beispiel | Worauf es am meisten ankommt |
|---|---|---|
| Die Site ist das Projekt | Ein Relaunch, ein barrierefreies Redesign, eine neue Sprache | Ergebnisse, Starttermin, wer sie danach pflegt |
| Die Site setzt das Projekt um | Kursmaterialien, eine Materialsammlung, ein Informationsangebot | Inhaltsplan, Nutzungsbelege, Dauerhaftigkeit des Materials |
| Die Site dokumentiert das Projekt | Ein Projektblog, Veranstaltungen, Veröffentlichungen | Regelmäßiges Veröffentlichen, Archiv, Downloadzahlen |

Geldgeber fragen oft, wie die Arbeit nach dem Ende der Förderung weitergeht. Für eine Site bedeutet das Hosting,
Updates und eine verantwortliche Person. Bei Exponential gibt es keine Lizenzgebühr zu erneuern
([Lizenzierung für Käufer](../../geschaeft/lizenzen-fuer-kaeufer.md)), daher geht es bei der Frage der Nachhaltigkeit
vor allem um Menschen und Hosting; siehe [Budgetposten für Hosting und Wartung](budgetposten-fuer-hosting-und-wartung.md).

## Was die Site als Beleg liefern kann

| Beleg | Woher er stammt | Quelle |
|---|---|---|
| Erstellte Seiten und Veröffentlichungen | Der Inhaltsbaum, Versionen mit Datum | [Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md) |
| Wer was wann veröffentlicht hat | Das Audit-Protokoll (Kanal `content`) | [Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md) |
| Eingegangene Anmeldungen und Anfragen | Gesammelte Informationen, mit CSV-Export | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |
| Reichweite des Newsletters | Listen von `cjw_newsletter` und das Versandarchiv | [cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md) |
| Seitenaufrufe | Aufrufzähler (Cronjob-Teil `updateviewcount`) oder ein Analysedienst | [Besucherstatistiken und Analyse](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md) |
| Wonach gesucht wurde | Suchstatistiken, standardmäßig ausgeschaltet | dieselbe Seite |

## Wo Sie beginnen

1. Lesen Sie den [Projektplan für eine geförderte Website](projektplan-fuer-eine-gefoerderte-website.md), bevor Sie den
   Antrag schreiben.
2. Füllen Sie das [Arbeitsblatt Förderbudget](../vorlagen/arbeitsblatt-foerderbudget.md) mit Ihren eigenen Angeboten aus.
3. Schalten Sie vor Projektbeginn die Messungen ein, die Sie für den Bericht brauchen werden
   ([Wirkung mit Website-Daten messen](wirkung-mit-website-daten-messen.md)): Zahlen, die nie aufgezeichnet wurden,
   lassen sich später nicht berichten.

## Verwandte Seiten

- [Non-Profit-Organisationen: hier beginnen](../non-profit-organisationen-hier-beginnen.md)
- [Planung einer Non-Profit-Site](../../unternehmen/gemeinnuetzige/website-planung-fuer-gemeinnuetzige.md)
- [Durchgerechnetes Beispiel: eine Non-Profit-Organisation](../../unternehmen/kosten/durchgerechnetes-beispiel-gemeinnuetzige-organisation.md)
- [Projektmanager: hier beginnen](../../fuehrung/projektleiter-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
