---
title: Vorlage für einen Bewertungsbogen für CMS-Angebote
audience: government, project-manager, sales-marketing
category: public-sector
order: 30
summary: Eine Vorlage für einen Bewertungsbogen, mit dem Sie Angebote für ein Content-Management-System vergleichen, mit einer Punkteskala, Regeln für Nachweise, einem Schritt zur Konsensbildung und einem ausgearbeiteten Aufbau; jede Gewichtung und jede Punktzahl ist ein Platzhalter, den Ihr Gremium ausfüllt.
keywords: Bewertungsbogen, Bewertungsmatrix, Angebotswertung, Punktzahl, Konsensbewertung, gewichtete Punktzahl, Ausschreibung, Beschaffung, Vorlage, scoring sheet, scoring matrix, bid scoring, evaluation score, consensus scoring, weighted score, tender, procurement, template
type: help
---
# Vorlage für einen Bewertungsbogen für CMS-Angebote

Diese Seite richtet sich an die Beschaffungsstelle, die die Bewertung einer Ausschreibung für ein Content-Management-System
einer Website oder eines Intranets aufsetzt, und an die Bewertenden, die den Bogen ausfüllen. Sie gibt eine Punkteskala,
Regeln, die Punktzahlen vertretbar halten, und einen Aufbau des Bogens. Die Gewichtungen sind Platzhalter: Sie stammen
aus Ihren Vergabeunterlagen, nie von dieser Seite.

> **Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung.

## Inhalt
1. [Bevor Sie bewerten](#bevor-sie-bewerten)
2. [Eine Punkteskala mit Regeln für Nachweise](#eine-punkteskala-mit-regeln-für-nachweise)
3. [Der Aufbau des Bogens](#der-aufbau-des-bogens)
4. [Einzel- und Konsensbewertung](#einzel--und-konsensbewertung)
5. [Statusangaben aus der Herstellerdokumentation bewerten](#statusangaben-aus-der-herstellerdokumentation-bewerten)
6. [Fallstricke](#fallstricke)

## Bevor Sie bewerten

- Die Kriterien und ihre Gewichtungen müssen die in der Ausschreibung veröffentlichten sein. Die meisten Vergaberegeln
  verbieten, nach dem Öffnen der Angebote ein Kriterium hinzuzufügen.
- Mindestanforderungen (bestanden oder nicht bestanden) werden zuerst geprüft, mit der Vorlage für eine
  Bewertungscheckliste aus diesem Ordner. Nur Angebote, die bestehen, werden bewertet.
- Jede bewertende Person erklärt Interessenkonflikte, bevor sie die Angebote sieht.
- Legen Sie vorab fest, welche Nachweise zählen: das schriftliche Angebot, die Demo, der Proof of Concept, die
  öffentliche Dokumentation. Für Exponential ist die öffentliche Dokumentation das Verzeichnis `doc/` des Repositorys,
  zum Beispiel die [Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md) und die [Sicherheitsstandards](../../../../standards/sicherheit.md).

## Eine Punkteskala mit Regeln für Nachweise

| Punkte | Bedeutung | Erforderliche Nachweise |
|---|---|---|
| 0 | Nicht behandelt oder durch Nachweise widerlegt | keine, oder Nachweise, die der Behauptung widersprechen |
| 1 | Behauptet, ohne Nachweis | nur eine Aussage im Angebot |
| 2 | Teilweise erfüllt, mit Nachweis | Dokumentation oder Demo zeigen einen Teil davon; Lücken benannt |
| 3 | Erfüllt, mit Nachweis | Dokumentation und Demo oder Proof of Concept zeigen es |
| 4 | Erfüllt, mit unabhängigem oder wiederholbarem Nachweis | das Gremium hat es nachvollzogen (Proof of Concept, Testlauf) oder es gibt einen unabhängigen Bericht |

Passen Sie die Zahl der Stufen an Ihre Regeln an. Wichtig ist, dass jede Stufe die Nachweise nennt, die sie braucht,
damit zwei Bewertende bei denselben Fakten zur selben Punktzahl kommen.

## Der Aufbau des Bogens

```text
Tender <ref>                    Bid <bidder> / Exponential <version>          Evaluator <initials>

Criterion                       Weight   Score (0-4)   Weighted   Evidence (link, demo minute, PoC step)
------------------------------  -------  ------------  ---------  -------------------------------------
1 Editorial functions           <w1>     <s>           <w1*s>     ...
2 Accessibility                 <w2>     <s>           <w2*s>     doc/eng-us/standards/accessibility.md; demo ...
3 Security                      <w3>     <s>           <w3*s>     doc/eng-us/standards/security.md; PoC step ...
4 Privacy and data protection   <w4>     <s>           <w4*s>     doc/eng-us/standards/privacy.md
5 Languages                     <w5>     <s>           <w5*s>     ...
6 Hosting and operations        <w6>     <s>           <w6*s>     ...
7 Support and maintenance       <w7>     <s>           <w7*s>     the support offer
8 Training and documentation    <w8>     <s>           <w8*s>     ...
9 Exit and portability          <w9>     <s>           <w9*s>     doc/eng-us/install/migration/leaving-exponential.md
10 Price / total cost           <w10>    <s>           <w10*s>    the TCO worksheet
                                -------                ---------
Total                           <100>                  <sum>
```

Der Preis wird oft nach einer in der Ausschreibung festgelegten Formel bewertet statt auf der Skala von 0 bis 4; folgen
Sie Ihren Regeln. Das Arbeitsblatt zu den Gesamtbetriebskosten im benachbarten Ordner total-cost-of-ownership hilft
Ihnen, Preise auf derselben Grundlage zu vergleichen.

## Einzel- und Konsensbewertung

1. **Einzelbewertung.** Jede bewertende Person bewertet allein und schreibt zu jeder Punktzahl den Nachweis auf.
2. **Moderationssitzung.** Eine moderierende Person, die selbst nicht bewertet, geht die Kriterien einzeln durch.
   Weichen Punktzahlen um mehr als eine Stufe ab, erläutern die Bewertenden ihre Nachweise.
3. **Konsenspunktzahl.** Das Gremium einigt sich auf eine Punktzahl je Kriterium und hält die Begründung fest. Bilden
   Sie keinen Durchschnitt: Ein Durchschnitt verbirgt die Uneinigkeit, nach der ein Debriefing fragen wird.
4. **Abzeichnung.** Jede bewertende Person unterschreibt den Konsensbogen.

## Statusangaben aus der Herstellerdokumentation bewerten

Die Seiten zu den Standards von Exponential verwenden feste Statusangaben. Eine faire Übertragung auf die Skala oben:

| Status in `doc/eng-us/standards/` | Höchstens bewerten mit | Warum |
|---|---|---|
| **Erfüllt** | 3, oder 4, wenn Sie es nachvollzogen haben | die Seite nennt die Nachweise (Tests, Einstellungen, Dateien) |
| **Teilweise erfüllt** | 2 | die Seite nennt, was abgedeckt ist, und die Lücken |
| **Nicht geprüft** | 1 | es wurde keine Bewertung durchgeführt; zum Beispiel sind EN 301 549, Section 508 und OWASP ASVS „Nicht geprüft“ |
| **Nicht erfüllt** | 0 | klar ausgesprochen, zum Beispiel unsignierte Commits in [Quellcodeverwaltung und Releases](../../../../standards/versionsverwaltung-und-releases.md) |

Ein Bieter kann eine Punktzahl mit eigenen Nachweisen anheben, zum Beispiel mit einem Audit der Barrierefreiheit des
öffentlichen Designs, das er für Sie baut. Bewerten Sie diesen Nachweis, nicht nur die Produktdokumentation.

## Fallstricke

- Den Glanz der Demo bewerten statt der Anforderung. Notieren Sie, welche Anforderung jede Minute der Demo gezeigt hat.
- Behauptungen „vollständiger Konformität“ belohnen, die kein Dokument stützt. Die Dokumentation von Exponential selbst
  beansprucht keine WCAG-Konformität und kein VPAT ([Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md#section-508)).
- Die Person, die den Preis bewertet, zuerst die Qualitätspunkte sehen lassen oder umgekehrt, wenn Ihre Regeln das
  trennen.
- Die Links zu den Nachweisen verlieren: Bewahren Sie sie im Bogen auf.

## Verwandte Seiten
- [Bewertung: Übersicht](README.md)
- [Beschaffung: Übersicht](../README.md)
- [Standards: wie der Status zu lesen ist](../../../../standards/README.md#wie-sie-den-status-lesen)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
