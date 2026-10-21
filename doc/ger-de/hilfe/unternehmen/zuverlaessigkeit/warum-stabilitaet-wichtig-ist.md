---
title: Warum Stabilität wichtig ist
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 10
summary: Was eine instabile Website ein Unternehmen kostet und welche konkreten Dinge in der Geschichte, den Release-Regeln und den mitgelieferten Werkzeugen von Exponential eine Website über Jahre stabil halten.
keywords: Stabilität, Zuverlässigkeit, langlebig, Release-Linie, veröffentlichte Tags, Rendite, Ausfallzeit, Vertrauen, Wartungskosten, stability, reliability, long-lived, release line, published tags, return on investment, downtime, trust, maintenance cost
type: help
---
# Warum Stabilität wichtig ist

Diese Seite richtet sich an Inhaber und Führungskräfte, die mit einer Website Geld, Mitglieder oder Ansehen gewinnen und
wissen wollen, ob Exponential eine sichere Grundlage ist. Sie erklärt, was Instabilität kostet und welche Tatsachen aus
der Geschichte, den Release-Regeln und den mitgelieferten Werkzeugen von Exponential eine stabile Website zum Normalfall
machen statt zum Glücksfall. Jede Aussage verweist auf ihre Quelle.

## Inhalt

1. [Was Instabilität ein Unternehmen kostet](#was-instabilität-ein-unternehmen-kostet)
2. [Eine Codebasis mit langem Gedächtnis](#eine-codebasis-mit-langem-gedächtnis)
3. [Release-Regeln, die Sie schützen](#release-regeln-die-sie-schützen)
4. [Werkzeuge, die mit jeder Installation kommen](#werkzeuge-die-mit-jeder-installation-kommen)
5. [Was Stabilität für die Rendite bewirkt](#was-stabilität-für-die-rendite-bewirkt)
6. [Was Stabilität nicht bedeutet](#was-stabilität-nicht-bedeutet)
7. [Fragen, die Sie sich stellen sollten](#fragen-die-sie-sich-stellen-sollten)

## Was Instabilität ein Unternehmen kostet

Eine Website, die ausfällt, kostet mehr als die Minuten, in denen sie nicht erreichbar ist:

- **Entgangene Verkäufe und Anmeldungen**, solange der Shop, das Anmeldeformular oder die Spendenseite nicht antwortet.
- **Verlorenes Vertrauen**: Ein Kunde, der zweimal auf eine Fehlerseite stößt, kommt vielleicht nicht wieder.
- **Arbeitszeit**: Jede Störung zieht Menschen von geplanter Arbeit ab, oft außerhalb der Bürozeiten.
- **Nacharbeit**: Eine Änderung, die etwas kaputt macht, muss gefunden, rückgängig gemacht und erneut umgesetzt werden.
- **Angst vor Aktualisierungen**: Ein Team, das sich einmal die Finger verbrannt hat, hört auf zu aktualisieren, fällt
  bei Sicherheitskorrekturen zurück und macht die nächste Aktualisierung größer und riskanter.

Wie groß jeder Posten ist, ist für jede Organisation anders. Um Ihre eigenen Zahlen einzusetzen, verwenden Sie das
[Arbeitsblatt: Kosten eines Ausfalls](../kosten/arbeitsblatt-kosten-eines-ausfalls.md) im Bereich Kosten. Diese Seite nennt keine
Branchenzahlen, weil die Quellen dieser Hilfe keine angeben.

## Eine Codebasis mit langem Gedächtnis

Exponential ist nicht erst kürzlich entstanden. Sein Code war zuerst der Code von eZ Publish, entwickelt ab 1999; die
Historie des Repositorys reicht bis zum 2. September 2002 zurück und enthält jeden Commit seither
([Wer ist 7x?, die Jahre von eZ Publish](../../wer-ist-7x.md#die-jahre-von-ez-publish-1999-bis-2021)). Das Inhaltsmodell, das
Sie heute nutzen (Klassen, Objekte, Versionen, Siteaccesses und die Template-Sprache), wurde in dieser frühen Generation
eingeführt und ist immer noch das Modell von Exponential (dieselbe Quelle).

Die README nennt den Status des Projekts deutlich: „Exponential hat sein Lebensende im Jahr 2021 überschritten und
überlebt. Aktuelle Releases zielen vor allem darauf, die Anforderungen für die Unterstützung aktueller Versionen der
Sprache PHP zu erleichtern“
([README, „Exponential Project Status“](https://github.com/se7enxweb/exponential/blob/main/README.md#exponential-project-status)).

Für ein Unternehmen bedeutet das:

- Eine vor Jahren gebaute Website lässt sich weiterhin auf einem dokumentierten Weg aktualisieren
  ([Upgrade](../../../anleitungen/aktualisieren.md) deckt Versionen von 3.10 bis heute ab).
- Die Fähigkeiten, die Ihr Team oder Ihr Partner bereits hat, behalten ihren Wert.
- Inhalte, die Sie einmal eingegeben haben, bleiben, wo sie sind; Sie bauen sie nicht für jede neue Hauptversion neu auf.

## Release-Regeln, die Sie schützen

Die folgenden Regeln sind im Repository schriftlich festgehalten, nicht nur gelebte Praxis:

| Regel | Was sie für Sie bedeutet | Quelle |
|---|---|---|
| Eine veröffentlichte Version ändert sich nie | Die Version, die Sie getestet haben, ist genau die Version, die Sie ausrollen | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Sicherheitskorrekturen kommen mit dem nächsten 6.0.x-Release | Sie erhalten Korrekturen durch Aktualisierung innerhalb derselben Linie | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Exponential 6.0 ist die gepflegte Linie | Sie wissen, auf welcher Linie Sie sein sollten | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Jedes Paket liegt auf GitHub und Packagist | Sie können jederzeit den genauen Code Ihrer Version beziehen | [Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md#die-regeln-denen-jedes-release-folgt) |

Einzelheiten stehen unter [Release-Linie und Support-Richtlinie](release-linie-und-support-richtlinie.md).

## Werkzeuge, die mit jeder Installation kommen

Stabilität kommt auch daher, Probleme früh zu sehen und sich schnell zu erholen. Diese Werkzeuge sind Teil des
Produkts, keine kostenpflichtigen Zusätze:

| Werkzeug | Was es für ein Unternehmen tut | Lesen |
|---|---|---|
| Zustandsprüfungen unter **Setup > Systeminformationen** | Listen auf, was Aufmerksamkeit braucht, mit dem, was zu tun ist, bevor ein Ausfall daraus wird | [Systeminformationen](../../../anleitungen/systeminformationen.md) |
| Fehlerreferenzen | Die Fehlerseite eines Kunden zeigt eine Referenz, die direkt zu den Log-Zeilen führt | [Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md) |
| Prüfprotokoll | Zeichnet auf, wer was getan hat, und beweist, dass die Aufzeichnung nicht verändert wurde | [Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md) |
| Wartungsmodus | Nimmt die Website mit einer sauberen Seite und einem Status 503, den Suchmaschinen verstehen, vom Netz | [Wartungsmodus](../../../anleitungen/wartungsmodus.md) |
| Upgrade-Prüfung | Vergleicht Dateien und Datenbank vor der Aktualisierung mit dem Release | [Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md) |
| Caches und Velocity | Halten die Website antwortfähig, wenn viele Menschen gleichzeitig kommen | [Caching für Verfügbarkeit](caching-fuer-verfuegbarkeit.md) |
| Watchdog von Velocity | Startet den Velocity-Server nach einem Absturz wieder | [Zustandsprüfungen und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md#velocity-zustandsendpunkt-und-watchdog) |

## Was Stabilität für die Rendite bewirkt

Eine stabile Plattform senkt die Kosten auf eine Weise, die sich über die Jahre summiert:

- **Weniger Störungen** bedeuten weniger ungeplante Arbeitszeit.
- **Routinemäßige Aktualisierungen** statt Neubauten: Der Wechsel auf die neueste 6.0.x ist eine wiederkehrende Aufgabe,
  kein Projekt
  ([Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md#wozu-sich-das-projekt-verpflichtet)).
- **Längere Lebensdauer dessen, was Sie gebaut haben**: Designs, Erweiterungen und Inhaltsmodelle funktionieren über
  Releases hinweg weiter.
- **Planbare Budgets**: Wenn Aktualisierungen klein und regelmäßig sind, lassen sie sich als Betriebskosten einplanen.

Um daraus Ihre eigenen Zahlen zu machen, siehe [Rendite der Stabilität](../kosten/rendite-der-stabilitaet.md) und
[Gesamtbetriebskosten](../kosten/gesamtbetriebskosten.md).

## Was Stabilität nicht bedeutet

Seien Sie gegenüber Ihrem Team und Ihren Kunden klar, wo die Grenzen liegen:

- Stabilität der Software ersetzt keine Backups, kein Monitoring und keine getestete Wiederherstellung. Eine Festplatte
  kann auf jeder Plattform ausfallen.
- Das Projekt veröffentlicht keinen Verfügbarkeitswert und kein Service Level Agreement. Kommerzielle Bedingungen bieten
  7x und Hosting-Partner an, nicht das Open-Source-Projekt
  ([SLA-Optionen für Exponential-Hosting](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)).
- Auch eine stabile Linie muss aktualisiert werden. Eine Website, die auf einem alten Tag bleibt, erhält
  Sicherheitskorrekturen nur durch den Wechsel auf eine neuere 6.0.x
  ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).
- Ihre eigenen Erweiterungen und Templates liegen in Ihrer Verantwortung; die Upgrade-Prüfung zeigt, wo sie vom Release
  abweichen.

## Fragen, die Sie sich stellen sollten

- [ ] Wissen wir, welche Exponential-Version und welche PHP-Version unsere Website ausführt?
- [ ] Wissen wir, wer es bemerken würde, wenn die Website nachts nicht mehr antwortet?
- [ ] Wann wurde unser letztes Backup auf einem anderen Rechner wiederhergestellt?
- [ ] Ist das Aktualisieren eine Routine in unserem Kalender oder etwas, dem wir ausweichen?
- [ ] Könnten zwei verschiedene Personen in unserem Team eine Störung bewältigen?

Lautet eine Antwort „nein“ oder „nicht sicher“, macht der [Bewertungsbogen Zuverlässigkeit](bewertungsbogen-zuverlaessigkeit.md)
daraus einen Plan.

## Verwandte Seiten

- [Zuverlässigkeit für Unternehmen](README.md)
- [Warum Unternehmen sich auf Exponential verlassen](../warum-unternehmen-sich-auf-exponential-verlassen.md)
- [Release-Linie und Support-Richtlinie](release-linie-und-support-richtlinie.md)
- [Risikomanagement für Exponential-Projekte](../../fuehrung/risikomanagement.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
