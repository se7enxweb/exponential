---
title: Warum Unternehmen sich auf Exponential verlassen
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 10
summary: Die Gründe, jeder mit seiner Quelle, warum eine Organisation, die online Geld verdient, sich auf Exponential verlassen kann - eine langlebige Codebasis, veröffentlichte Versionen, die sich nie ändern, wenige bewegliche Teile, Geschwindigkeit unter Last, Belege, wenn etwas schiefgeht, und keine Lizenzgebühr. Und wie jeder von ihnen Umsatz schützt und das Geschäft für Kunden erreichbar hält.
keywords: Stabilität, Zuverlässigkeit, warum Exponential, langlebig, Release-Richtlinie, Sicherheitskorrekturen, PHP-Versionen, SQLite, Velocity, Cache, Audit-Trail, keine Lizenzgebühr, GPL, Rendite, Verfügbarkeit, geringer Wartungsaufwand, stability, reliability, why Exponential, long-lived, release policy, security fixes, PHP versions, cache, audit trail, no license fee, return on investment, availability, uptime, low maintenance
type: help
---
# Warum Unternehmen sich auf Exponential verlassen

Diese Seite ist für Inhaber und Führungskräfte, die entscheiden müssen, ob eine Webplattform über Jahre einen Teil
ihres Umsatzes tragen kann. Sie listet die Eigenschaften von Exponential auf, die für diese Entscheidung zählen, jede
mit der Stelle, an der sie festgehalten ist, und erklärt, was jede für Geld und für die Erreichbarkeit für Kunden
bedeutet.

> **Hinweis**
> Diese Seite gibt wieder, was die Quellen darüber sagen, was die Software ist und tut. Sie verspricht keinen
> Verfügbarkeitswert: Die Verfügbarkeit hängt ebenso vom Hosting, von den Menschen, die die Website betreiben, und von
> der Art ab, wie sie gebaut ist, wie von der Software. Der Bereich [Zuverlässigkeit](../../../eng-us/help/businesses/reliability/README.md) erklärt,
> wie Sie dorthin kommen.

## Inhalt

1. [Die Frage, die ein Unternehmen stellt](#die-frage-die-ein-unternehmen-stellt)
2. [Eine langlebige Codebasis](#eine-langlebige-codebasis)
3. [Releases, mit denen Sie planen können](#releases-mit-denen-sie-planen-können)
4. [Wenige bewegliche Teile](#wenige-bewegliche-teile)
5. [Schnell unter Last](#schnell-unter-last)
6. [Belege, wenn etwas schiefgeht](#belege-wenn-etwas-schiefgeht)
7. [Keine Lizenzgebühr und keine Anbieterbindung](#keine-lizenzgebühr-und-keine-anbieterbindung)
8. [Wie Stabilität zu Rendite wird](#wie-stabilität-zu-rendite-wird)
9. [Was Stabilität nicht für Sie erledigt](#was-stabilität-nicht-für-sie-erledigt)

## Die Frage, die ein Unternehmen stellt

Eine Website, die verkauft oder auf die Mitarbeiter und Partner angewiesen sind, verdient ihr Geld nur, solange sie
erreichbar ist und tut, was Kunden erwarten. Jeder Ausfall kostet doppelt: den verlorenen Verkauf oder die verlorene
Arbeitsstunde und die Zeit der Menschen, die ihn beheben. Eine Plattform ist für ein Unternehmen umso mehr wert, je
seltener sie ausfällt, je schneller sie sich erholt und je weniger Überraschungen ihre Upgrades bringen. Die folgenden
Abschnitte gehen diese Punkte einzeln durch.

## Eine langlebige Codebasis

| Tatsache | Quelle |
|---|---|
| Der Copyright-Vermerk lautet „Copyright (C) 1998 - 2026 7x & Exponential Foundation“ | `COPYRIGHT.md`, zitiert in [Wer ist 7x?](../wer-ist-7x.md#kurz-gesagt) |
| „Exponential ist über sein Lebensende 2021 hinausgekommen und hat überlebt“ | `README.md`, „Exponential Project Status“ |
| 7x veröffentlicht die 6.0.x-Releases seit Dezember 2023, von 6.0.0 am 1. Januar 2024 bis 6.0.14 am 7. Juni 2026, mit 6.0.15 in Entwicklung | [Wer ist 7x?](../wer-ist-7x.md#kurz-gesagt) |
| Aktuelle Releases zielen darauf, „die Anforderungen für die Unterstützung aktueller Versionen der Sprache PHP wie PHP 8.2, 8.3, 8.4, 8.5, 8.6 und über php9 hinaus zu erleichtern“ | `README.md` |
| Websites, die in den Jahren gebaut wurden, als die Software noch eZ Publish hieß, lassen sich migrieren | [14. Migration von der 4.x-Linie](../../installation/14-migration-von-4x.md), [15. Migration vom 5.x-Legacy-Stack](../../installation/15-migration-von-5x-legacy.md) |

**Was das für ein Unternehmen bedeutet:** Inhalte, Templates und Erweiterungen, die vor Jahren geschrieben wurden,
laufen weiter. Ein Unternehmen muss seine Website nicht alle paar Jahre neu aufbauen, weil die Plattform darunter
aufgegeben oder neu geschrieben wurde. Das Inhaltsmodell, die URLs und die Gewohnheiten der Redakteure überstehen
Upgrades.

## Releases, mit denen Sie planen können

| Tatsache | Quelle |
|---|---|
| „Exponential 6.0 ist die gepflegte Linie. Sicherheitskorrekturen werden auf dem Branch `main` vorgenommen ... und mit dem nächsten 6.0.x-Release veröffentlicht“ | `SECURITY.md` |
| Vertrauliche Meldungen von Schwachstellen gehen an security@se7enx.com, mit „einer ersten Antwort innerhalb eines Werktags“ | `SECURITY.md` |
| „Ein veröffentlichter Tag wird nie geändert“: Eine Korrektur erreicht Sie als nächstes Release, nicht als berichtigte Kopie | `SECURITY.md` |
| Die Continuous Integration führt die Testsuite bei jedem Push und Pull Request auf PHP 8.1 bis 8.5 aus und prüft, dass PHP 8.0 noch startet | [Die Support-Zusage](../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung) |
| Vor einem Upgrade prüft die Administration die Datenbank und die Dateien gegen das Release (**Setup > Upgrade-Prüfung**) | [Die Upgrade-Prüfung: Dateien und Datenbank vor einem Upgrade](../../anleitungen/upgrade-pruefung.md), [Prüfung der Dateikonsistenz](../../../eng-us/features/6.0/file-consistency-check.md) |

**Was das für ein Unternehmen bedeutet:** Sie wissen, welche Version Sie betreiben, Sie wissen, dass sich eine Version
nie unter Ihnen ändert, und Sie können die nächste testen, bevor sie Kunden erreicht. Siehe
[Aktualisierungen ohne Überraschungen](../../../eng-us/help/businesses/reliability/upgrades-without-surprises.md) und
[Release-Linie und Support-Richtlinie](../../../eng-us/help/businesses/reliability/release-line-and-support-policy.md).

## Wenige bewegliche Teile

Jede Komponente, die eine Website braucht, ist eine weitere Sache, die ausfallen, falsch konfiguriert werden oder
eigene Updates brauchen kann.

| Tatsache | Quelle |
|---|---|
| SQLite 3 ist die Standarddatenbank; „SQLite braucht keinen“ (keinen Datenbankserver) | `README.md`, „Requirements“; [Datenbankunterstützung](../datenbankunterstuetzung.md) |
| Unterstützte Datenbanken, in der Reihenfolge der Präferenz: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | `README.md`, „Main Exponential features“ |
| Läuft auf fast jedem Betriebssystem mit PHP 8.0 oder neuer | [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md) |
| Läuft hinter Apache, Nginx, LiteSpeed, Caddy, IIS, lighttpd und anderen, oder auf seinem eigenen Server, Velocity | `README.md`, „How Exponential is served“ |
| Shop, Suchmaschine, Workflows, Multisite und viele Sprachen sind eingebaut | `README.md`, „Main Exponential features“ |

**Was das für ein Unternehmen bedeutet:** Eine kleine Organisation kann eine vollständige Website, einschließlich
Shop, auf einem Server mit PHP und ohne separaten Datenbankserver betreiben und Komponenten (PostgreSQL, Solr-Suche,
einen Cluster) erst hinzufügen, wenn sie sie braucht. Weniger Komponenten bedeuten weniger Updates, weniger
Fehlerquellen und geringere Hosting-Kosten. Siehe
[Datenbankwahl für Zuverlässigkeit](../../../eng-us/help/businesses/reliability/database-choice-for-reliability.md).

## Schnell unter Last

Die für den Stack dokumentierten Werte sind „0,36 ms pro Seite aus dem Cache, 8.941 Anfragen pro Sekunde im
Dauerbetrieb, und wiederholte Besuche berühren das Netzwerk nie“, mit ihrem Kontext: ein Median über Hunderte von
Anfragen, gemessen auf einer Maschine mit 12 Kernen, die eine echte Seite ohne einen einzigen Fehler bei jeder
getesteten Parallelität bis zu 384 Verbindungen ausliefert, reproduzierbar mit Skripten, die mit dem Projekt
ausgeliefert werden (`README.md`, „What that measures as“).

Die README sagt auch, woher die Geschwindigkeit kommt: „Der größte Teil der Geschwindigkeit kam davon, Exponential und
den Cache zu korrigieren, nicht vom Prozessmodell des Servers“, zum Beispiel eine Anfrage, die eine abgelaufene Seite
neu aufbaut, während alle anderen die vorhandene Kopie bekommen, und das Neuladen eines wiederkehrenden Besuchers, das
mit 202 Bytes statt der ganzen Seite beantwortet wird. „Exponential 6.0.15+ ist auch unter Apache oder Nginx schnell
unter Last“ (`README.md`, „The part that matters even if you never run Velocity“).

**Was das für ein Unternehmen bedeutet:** Eine Kampagne, ein Newsletter oder ein Ausverkauf, der viele Besucher
gleichzeitig bringt, ist der Moment, in dem eine langsame Website am meisten Geld verliert. Ein Cache, der die
vorhandene Kopie ausliefert, während eine Anfrage sie neu aufbaut, hält die Website während dieser Spitze am Antworten.
Siehe [Lastspitzen planen](../../../eng-us/help/businesses/reliability/peak-traffic-planning.md) und
[Velocity für Unternehmen](../../../eng-us/help/businesses/reliability/velocity-for-businesses.md). Ihre eigenen Werte hängen von Ihrer Hardware und
Ihren Seiten ab; messen Sie sie mit den Benchmark-Werkzeugen
([Benchmarks für Exponential: Geschwindigkeit messen, vergleichen und absichern](../../anleitungen/benchmarking.md)).

## Belege, wenn etwas schiefgeht

| Werkzeug | Was es Ihnen gibt | Quelle |
|---|---|---|
| Manipulationssicherer Audit-Trail | Wer was wann geändert hat, mit Konsole, Alarmen und Exporten | [Der Audit-Trail](../../../eng-us/features/6.0/audit-trail.md) |
| Log-Viewer | Die Logs in der Administration, ohne Shell | [Log-Viewer](../../../eng-us/features/6.0/log-viewer.md) |
| Fehlerreferenzen | Eine dem Besucher angezeigte Referenz, die zum protokollierten Fehler führt | [Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md) |
| Systeminformationen | Versionen, PHP, Datenbank und Einstellungen auf einen Blick | [Systeminformationen: worauf die Site läuft und was Aufmerksamkeit braucht](../../anleitungen/systeminformationen.md) |
| Wartungsmodus | Eine saubere Seite für Besucher, während gearbeitet wird | [Wartungsmodus: die Site vom Netz nehmen und zurückholen](../../anleitungen/wartungsmodus.md) |

**Was das für ein Unternehmen bedeutet:** kürzere Störungen. Die Zeit zwischen „Ein Kunde sagt, die Kasse funktioniert
nicht“ und „Wir wissen, warum“ ist der Ort, an dem die meiste Ausfallzeit verbracht wird. Siehe
[Störungsbehandlung für kleine Teams](../../../eng-us/help/businesses/reliability/incident-response-for-small-teams.md).

## Keine Lizenzgebühr und keine Anbieterbindung

Exponential ist freie Software unter der GNU General Public License, Version 2 oder später, und seine Dokumentation
steht unter der GNU Free Documentation License
([Lizenzen für Käufer erklärt: GPL-Code, GFDL-Dokumentation](../geschaeft/lizenzen-fuer-kaeufer.md),
[Freie Dokumentation: Nutzen Sie sie, um sich selbst und anderen zu helfen](../freie-dokumentation.md)). Es gibt keine
Lizenzgebühr pro Website, pro Benutzer oder pro Server, und Code, Inhalte und Datenbank gehören Ihnen. Sie können den
Hoster oder den Partner wechseln, ohne die Software zu wechseln. Siehe
[Wofür Sie nicht bezahlen](kosten/wofuer-sie-nicht-bezahlen.md).

## Wie Stabilität zu Rendite wird

| Eigenschaft | Spart oder bringt ein | Lesen |
|---|---|---|
| Langlebige Codebasis | Kein erzwungener Neuaufbau der Website alle paar Jahre | [Gesamtbetriebskosten](kosten/gesamtbetriebskosten.md) |
| Releases, die sich nie ändern | Vorhersehbarer Aufwand für Upgrades, vorab testbar | [Budget für Aktualisierungen und Wartung](kosten/budget-fuer-aktualisierungen-und-wartung.md) |
| Wenige bewegliche Teile | Geringere Kosten für Hosting und Administration | [Kostentreiber beim Hosting](kosten/kostentreiber-beim-hosting.md) |
| Schnell unter Last | Keine verlorenen Verkäufe zu Spitzenzeiten | [Lastspitzen planen](../../../eng-us/help/businesses/reliability/peak-traffic-planning.md) |
| Belege, wenn etwas schiefgeht | Kürzere Störungen | [Arbeitsblatt: Kosten eines Ausfalls](kosten/arbeitsblatt-kosten-eines-ausfalls.md) |
| Keine Lizenzgebühr | Das Budget fließt in Inhalte und Funktionen | [Rendite der Stabilität](kosten/rendite-der-stabilitaet.md) |

Die Seite [Rendite der Stabilität](kosten/rendite-der-stabilitaet.md) macht daraus eine Rechnung mit Beispielannahmen, die Sie
durch Ihre eigenen ersetzen.

## Was Stabilität nicht für Sie erledigt

Ehrliche Grenzen, damit später niemand überrascht ist:

- **Die Software sichert sich nicht selbst.** Jemand muss Backups erstellen und testen, dass sie sich wiederherstellen
  lassen ([Backups, die sich wiederherstellen lassen](../../../eng-us/help/businesses/reliability/backups-that-restore.md)).
- **Updates installieren sich nicht von selbst.** Jemand muss Sicherheits-Releases einspielen
  ([Sicherheitsupdates und Offenlegung](../../../eng-us/help/businesses/security/security-updates-and-disclosure.md)).
- **Eigener Code liegt in Ihrer Verantwortung.** Erweiterungen und Templates, die für Sie geschrieben wurden, müssen
  von Ihnen oder Ihrem Partner gepflegt werden ([Einen Partner auswählen](auswahl/einen-partner-auswaehlen.md)).
- **Das Hosting zählt.** Ein billiger Hoster ohne Monitoring kann nicht liefern, was die Software ermöglicht
  ([Hosting auswählen](auswahl/hosting-auswaehlen.md)).

## Verwandte Seiten

- [Unternehmen: hier beginnen](unternehmen-hier-beginnen.md)
- [Warum Stabilität wichtig ist](../../../eng-us/help/businesses/reliability/why-stability-matters.md)
- [Positionierung und Nutzenversprechen](../geschaeft/positionierung-und-nutzenversprechen.md)
- [Exponential sachlich vergleichen](../geschaeft/exponential-sachlich-vergleichen.md)
- [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
