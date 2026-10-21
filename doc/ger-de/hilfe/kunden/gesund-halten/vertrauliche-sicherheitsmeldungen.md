---
title: Vertrauliche Sicherheitsmeldungen
audience: customer, admin, partner
category: customer-success
order: 20
summary: Was ein Website-Betreiber tut, wenn jemand im Team, ein Kunde oder ein Sicherheitsforscher ein mögliches Sicherheitsproblem in Exponential findet - es vertraulich an security@se7enx.com melden, was hineingehört und was nicht, was danach geschieht und wie es sich von einem Einbruch auf Ihrer eigenen Website unterscheidet.
keywords: Sicherheitsmeldung, Sicherheitslücke, verantwortungsvolle Offenlegung, SECURITY.md, security@se7enx.com, vertraulich, private Meldung von Sicherheitslücken auf GitHub, security report, vulnerability, responsible disclosure, private, GitHub private vulnerability reporting
type: help
---
# Vertrauliche Sicherheitsmeldungen

Ein Sicherheitsproblem in der Software (ein Weg, etwas zu sehen oder zu ändern, was man nicht sollte) muss die
Menschen erreichen, die es beheben können, und darf nicht zuerst Angreifer erreichen. Diese Seite sagt einem Betreiber
und seinem Team, was mit einem solchen Fund zu tun ist. Die Regeln stammen aus `SECURITY.md` im Wurzelverzeichnis jeder
Installation.

## Zwei verschiedene Situationen

| Situation | Was es ist | Was zu tun ist |
|---|---|---|
| Eine Schwachstelle in Exponential selbst | Jeder, der dieselbe Version betreibt, könnte betroffen sein | Vertraulich an das Projekt melden (diese Seite) |
| Ein Einbruch oder Missbrauch Ihrer Website | Auf Ihrer Website ist etwas geschehen: unbekannte Konten, geänderte Dateien, Seiten, die Sie nicht veröffentlicht haben | Rufen Sie sofort Ihren Hoster an; es ist ein Vorfall der Schwere 1 ([Wenn die Website nicht erreichbar ist](wenn-die-website-nicht-erreichbar-ist.md), [Kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md)) |

Manchmal führt das Zweite zum Ersten: Die Untersuchung ergibt, dass die Ursache eine Schwachstelle in der Software ist.
Dann gilt beides.

## Vertraulich melden

„Bitte melden Sie Sicherheitsprobleme vertraulich per E-Mail an security@se7enx.com, nicht im öffentlichen
Issue-Tracker oder in den Diskussionen“ (`SECURITY.md`). Die Fehlermeldeseite der Hilfe bietet außerdem die private
Meldung von Sicherheitslücken des Repositorys auf GitHub an ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)).

Veröffentlichen Sie es **nicht** in einem öffentlichen Issue, einem Forum, einem Chat, einem Pull Request oder einem
sozialen Netzwerk.

## Was hineingehört

`SECURITY.md` verlangt:

- die Version (`composer show se7enxweb/exponential`, oder das Tag oder den Commit, den Sie betreiben) und die PHP-Version;
- die Engine, die die Website ausliefert (Exponential Velocity, FrankenPHP, Apache oder nginx mit PHP-FPM), und die Datenbank;
- was ein Angreifer tun kann, und die Schritte, um es nachzustellen.

**Lassen Sie Passwörter, Geheimnisse und personenbezogene Daten weg** (dieselbe Datei). Stellen Sie das Problem
möglichst auf einer Testkopie nach, nie auf der Website eines anderen.

Meist schreibt Ihr Administrator oder Partner die Meldung, weil er Versionen und Schritte genau beschreiben kann. Die
Aufgabe des Betreibers ist sicherzustellen, dass sie vertraulich abgeschickt wird, und zu wissen, dass sie abgeschickt wurde.

## Was danach geschieht

Aus `SECURITY.md`:

1. „Sie können innerhalb eines Werktags mit einer ersten Antwort rechnen.“
2. „Ihnen wird mitgeteilt, ob die Meldung angenommen oder abgelehnt wird, und Sie werden auf dem Laufenden gehalten,
   während eine Korrektur vorbereitet wird.“
3. „Ein angenommenes Problem wird auf `main` behoben und mit der nächsten Version 6.0.x veröffentlicht.“

Diese erste Antwort ist die einzige Zusage zur Reaktionszeit in den eigenen Dateien des Projekts
([Grundlagen zu Verträgen und SLAs](../../unternehmen/auswahl/grundlagen-zu-vertraegen-und-slas.md)).

## Während Sie auf die Korrektur warten

- Beschränken Sie den Fund auf die Personen, die davon wissen müssen.
- Fragen Sie Ihren Partner oder Hoster, ob eine vorübergehende Maßnahme (eine Konfiguration oder eine Regel im
  Webserver) Ihre Website in der Zwischenzeit schützt; die Leitfäden zur Härtung beschreiben viele ([Sicherheitshärtung](../../sicherheitshaertung.md)).
- Wenn das Release mit der Korrektur erscheint, lassen Sie es schnell einspielen ([Upgrades mit Ihrem Anbieter](upgrades-mit-ihrem-anbieter.md)).
- Partner informieren die Öffentlichkeit nicht, bevor eine Korrektur veröffentlicht ist
  ([Eskalationen und Sicherheitsprobleme](../../partner/eskalationen-und-sicherheitsprobleme.md)); halten Sie Ihre eigene
  Kommunikation an dieselbe Regel.

## Erweiterungen anderer Autoren

Eine Erweiterung, die nicht Teil des Projekts ist, hat ihren eigenen Autor und gegebenenfalls einen eigenen
Sicherheitskontakt. Prüfen Sie das Repository der Erweiterung auf eine Sicherheitsrichtlinie; hat es keine, wenden Sie
sich vertraulich an den Autor statt öffentlich. Die Seite **Info > Über** nennt die Website jeder Erweiterung
([Die Seiten Über und Copyright](../../../../eng-us/features/6.0/about-and-package-pages.md)).

## Personenbezogene Daten

Wenn personenbezogene Daten von Kunden offengelegt worden sein könnten, kann es rechtliche Meldepflichten geben. Fragen
Sie sofort Ihren Datenschutzbeauftragten oder Anwalt. Diese Seite ist keine Rechtsberatung; siehe
[DSGVO-Grundlagen](../../unternehmen/sicherheit/dsgvo-grundlagen.md).

## Verwandte Seiten

- [Sicherheitsgrundlagen](sicherheitsgrundlagen.md)
- [Sicherheitsupdates und Offenlegung (für Unternehmen)](../../unternehmen/sicherheit/sicherheitsupdates-und-offenlegung.md)
- [Probleme melden](probleme-melden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
