---
title: Der technische Teil eines Förderantrags
audience: non-profit, businesses, project-manager, sales-marketing
category: non-profits
order: 30
summary: Wie Sie den Technikteil eines Förderantrags für eine Exponential-Website in einfacher Sprache schreiben, die ein Gutachter versteht - Plattform, Lizenz und Eigentum, Hosting, Barrierefreiheit, Datenschutz, Sprachen und Nachhaltigkeit - mit Formulierungen, die Sie anpassen können, und der Quellseite, die jede Aussage belegt.
keywords: Förderantrag, technischer Teil, Plattformbeschreibung, Open Source, GPL, Nachhaltigkeit, Hosting, Barrierefreiheit, Datenschutz, Sprachen, Gutachter, Geldgeber, grant proposal, technical section, platform description, open source, sustainability, hosting, accessibility, data protection, languages, reviewer, funder
type: help
---
# Der technische Teil eines Förderantrags

Diese Seite richtet sich an Antragsschreiber. Gutachter sind selten Technikfachleute; sie möchten wissen, dass die
Plattform solide ist, dass die Organisation besitzen wird, was sie aufbaut, und dass die Site auch nach der Förderung
noch funktioniert. Die folgenden Bausteine sind so geschrieben, dass Sie sie anpassen können. Jeder nennt seine Quelle,
damit Sie einem Gutachter zeigen können, woher er stammt.

> **Tipp**
> Behaupten Sie nur, was Ihre Site tatsächlich tun wird. Wenn Sie Velocity nicht nutzen werden, erwähnen Sie es nicht.
> Wenn Tests der Barrierefreiheit nicht im Budget sind, versprechen Sie keine Konformitätsstufe.

## Inhalt

1. [Plattform](#plattform)
2. [Lizenz und Eigentum](#lizenz-und-eigentum)
3. [Hosting](#hosting)
4. [Barrierefreiheit](#barrierefreiheit)
5. [Datenschutz](#datenschutz)
6. [Sprachen](#sprachen)
7. [Nachhaltigkeit](#nachhaltigkeit)
8. [Was Sie nicht schreiben sollten](#was-sie-nicht-schreiben-sollten)

## Plattform

> Die Site wird mit Exponential gebaut, einem freien, quelloffenen Content-Management-System in PHP mit Bearbeitung im
> Browser, rollenbasierten Berechtigungen, Freigabe von Inhalten und zeitgesteuerter Veröffentlichung sowie
> Unterstützung für mehrere Sprachen und mehrere Sites.

Quelle: `README.md`, „Main Exponential features“ („Fine grained role based permissions system“, „Content approval and
scheduled publication“, „Advanced multi-lingual support“, „Multi-site support“). Das Projekt wird von 7x geleitet und
von der gemeinnützigen Exponential Foundation getragen ([Wer ist 7x?](../../wer-ist-7x.md),
[Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md)).

## Lizenz und Eigentum

> Die Software steht unter der GNU General Public License, Version 2 oder später; es fällt keine Lizenzgebühr an. Die
> Organisation besitzt ihre Inhalte, ihr Design und ihre Daten und kann sie jederzeit zu einem anderen Anbieter
> mitnehmen.

Quelle: `README.md`, „License“; [Lizenzierung für Käufer](../../geschaeft/lizenzen-fuer-kaeufer.md);
[Was Ihnen gehört](../../kunden/was-ihnen-gehoert.md).

## Hosting

> Die Site wird auf [Shared Hosting / einem kleinen virtuellen Server / Managed Hosting] mit der Datenbank SQLite
> betrieben, die keinen separaten Datenbankserver benötigt. Sicherungen der Datenbank und des Dateispeichers werden
> [täglich] erstellt, und eine Wiederherstellung wird [jedes Quartal] getestet.

Quelle: [Unterstützung der SQLite-Datenbank](../../../funktionen/6.0/sqlite-datenbank.md),
[Hosting mit kleinem Budget](../betrieb/hosting-mit-kleinem-budget.md), [Sicherungen und Wiederherstellungen](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md).
Füllen Sie die Klammern mit dem aus, was Sie wirklich tun werden.

## Barrierefreiheit

> Die Templates werden nach den Barrierefreiheitsleitlinien des Projekts gebaut; Bilder erfordern einen Alternativtext;
> die Site wird vor dem Start mit automatischen Werkzeugen und per Tastatur getestet, und eine Erklärung zur
> Barrierefreiheit wird veröffentlicht.

Quelle: [Barrierefreiheit für Site-Ersteller](../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md),
[Barrierefreiheit testen](../../ersteller/website-ersteller/barrierefreiheit-testen.md),
[Eine Erklärung zur Barrierefreiheit schreiben](../../verwaltung/barrierefreiheit/eine-erklaerung-zur-barrierefreiheit-schreiben.md).
Nennen Sie ein Ziel wie WCAG 2.2 AA nur, wenn Tests dagegen im Budget vorgesehen sind.

## Datenschutz

> Formulare erheben nur die nötigen Daten; der Zugriff auf Einsendungen ist auf benannte Koordinatoren beschränkt;
> Einsendungen werden nach einem festen Zeitplan gelöscht. Der Newsletter verwendet Double-Opt-in.
> Administratorkonten verwenden eine Zwei-Faktor-Anmeldung. Ein manipulationssicheres Audit-Protokoll zeichnet
> administrative Aktionen auf.

Quelle: [Ehrenamtliche und Anmeldeformulare](../../unternehmen/gemeinnuetzige/ehrenamtliche-und-anmeldeformulare.md),
[cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md),
[Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md), [Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md).

## Sprachen

> Die Site wird in [Sprachen] veröffentlicht. Die Plattform unterstützt bis zu 62 Inhaltssprachen pro Installation
> sowie Schriften, die von rechts nach links laufen.

Quelle: [Die Grenze von 62 Sprachen](../../verwaltung/sprachen/die-grenze-von-62-sprachen.md),
[Sites mit Rechts-nach-links-Schrift](../../ersteller/website-ersteller/websites-von-rechts-nach-links.md).

## Nachhaltigkeit

> Nach dem Förderzeitraum wird die Site von [benannte Rolle] gepflegt; Hosting und Updates werden aus [Budgetposten]
> finanziert. Da keine Lizenzgebühr anfällt, bestehen die laufenden Kosten aus Hosting und der Zeit von Mitarbeitenden
> oder Auftragnehmern. Die Redakteure werden während des Projekts geschult, und Einrichtung, Konten und Routine werden
> dokumentiert.

Quelle: [Budgetposten für Hosting und Wartung](budgetposten-fuer-hosting-und-wartung.md),
[Übergabe an die Redakteure](../../ersteller/website-ersteller/die-website-an-die-redaktion-uebergeben.md).

## Was Sie nicht schreiben sollten

| Schreiben Sie nicht | Warum |
|---|---|
| „Vollständig barrierefrei“ oder „WCAG-zertifiziert“ | Konformität gehört nach den Tests zu Ihrer Site, nicht zur Software ([Was Exponential bereitstellt](../../verwaltung/barrierefreiheit/was-exponential-bietet.md)) |
| „Eingebautes Spendensystem“ | Es gibt keines ([Spenden](../spenden/README.md)) |
| „DSGVO-konform“ | Software kann nicht für sich allein konform sein; Ihre Verarbeitung ist es ([Grundlagen der DSGVO](../../unternehmen/sicherheit/dsgvo-grundlagen.md)) |
| Leistungszahlen für Ihre Site | Messen Sie selbst; zitieren Sie Benchmarks nicht als Versprechen |
| Einen Partnerrabatt für Non-Profit-Organisationen | Es ist keiner veröffentlicht ([Mit einem Partner arbeiten](../betrieb/mit-einem-partner-arbeiten.md)) |

## Verwandte Seiten

- [Fördermittel und die Website](README.md)
- [Projektplan für eine geförderte Website](projektplan-fuer-eine-gefoerderte-website.md)
- [Für wen Exponential gedacht ist](../../geschaeft/fuer-wen-exponential-ist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
