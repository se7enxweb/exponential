---
title: "Fallstudie: ein Paket aus einem anderen Projekt mit Namensnennung portieren"
audience: developer, app-creator
category: extension-development
order: 160
summary: Wie zwei Erweiterungen fremden Code übernommen und genannt haben - sevenx_dse bettet ein Datenbankwerkzeug aus einer Kette von Forks ein, expchangeclass führt zwei Forks zusammen - und was Sie bei Ihrer eigenen Portierung prüfen sollten.
keywords: Fallstudie, Portierung, portieren, Namensnennung, Credits, sevenx_dse, AdminNeo, Adminer, expchangeclass, Fork, Lizenz, README-Credits, Includes, case study, port, credit, attribution, fork, license, README credits
type: help
---
# Fallstudie: ein Paket aus einem anderen Projekt mit Namensnennung portieren

Teil 5, Kapitel 16. Zwei Erweiterungen zeigen zwei übliche Wege, fremden Code in Exponential zu bringen, und wie jede
ihre Quellen nennt. Nehmen Sie sie als Vorbild, wenn Sie selbst etwas portieren.

## 1. Ein Werkzeug einbetten: sevenx_dse

se7enxweb/sevenx_dse bringt eine vollständige Oberfläche zur Datenbankverwaltung in die Exponential-Administration,
hinter deren Rollen und Richtlinien. Diese Oberfläche schreibt sie nicht selbst: Sie bettet ein vorhandenes
Open-Source-Werkzeug ein. Ihre README beschreibt die Kette in den ersten Zeilen: Die Erweiterung „is a downstream fork
of AdminNeo embedded as a native ... extension“, und AdminNeo ist „a downstream fork of the original Adminer database
tool“.

Wie sie diese Kette nennt:

| Wo | Was dort steht |
|---|---|
| README, erster Absatz | was eingebettet ist, mit einem Link zum Projekt |
| README, „Credits“ | eine Tabelle: der eingebettete Kern und sein Projekt, der ursprüngliche Autor beider Werkzeuge und die eigene Integrationsarbeit der Erweiterung, jeweils mit Link; danach ein Satz des Dankes an den ursprünglichen Autor |
| README, „Copyright“ | ihr eigenes Copyright und „AdminNeo and Adminer are copyright their respective authors“, mit einem Link zu deren vollständigen Credits |
| Der eingebettete Code | in einem eigenen Ordner, `adminneo/`, getrennt von den Klassen der Erweiterung |

Auch was sie geändert hat, sagt sie: „CSS isolation to prevent AdminNeo global styles from damaging eZ admin chrome“,
die rollenbasierte Sperre, bevor sich irgendetwas verbindet, die Sitzungsbehandlung. Ein Leser erkennt, welche Arbeit
von der Erweiterung stammt und welche vom Werkzeug.

Änderungen am eingebetteten Code sind eigene Commits in der Historie der Erweiterung, mit Begründung. Commit b02ff09
zum Beispiel ersetzte für PHP 8.2 `strlen( utf8_decode() )` durch `mb_strlen()` in zwei Dateien von `adminneo/`
([Veraltete Funktionen von PHP 8.1 bis 8.5](../../../../eng-us/help/extension-development/part-4-upgrading/php-8-1-to-8-5-deprecations.md)).
Eine solche Korrektur ist auch ein Kandidat, um sie an das Projekt des Werkzeugs zurückzuschicken.

## 2. Forks zusammenführen: expchangeclass

se7enxweb/expchangeclass ging einen anderen Weg: Die ursprüngliche Erweiterung war verwaist, und es gab zwei Forks.
Ihre README sagt, welche Teile woher kamen: Sie „takes the OpenContent code as its base, takes the attribute
pre-selection and packaging from the Netgen fork, and fixes what neither had“, und verlinkt das ursprüngliche
Repository.

Die Namensnennung steht an jeder Stelle, die dieses Buch empfiehlt
([Namensnennung und Urheberrechtshinweise](namensnennung-und-urheberrechtshinweise.md)):

- `ezinfo.php`: ein `Includes`-Eintrag mit Name, Copyright und Lizenz des Originals, angezeigt auf der Seite „Über“;
- `composer.json`: der ursprüngliche Autor in `authors`, mit der Rolle „Original author of eZChangeclass“;
- die Beschreibung in `composer.json`: „A hardened fork of eZChangeclass“;
- `doc/changelog.txt`: das Changelog des Originals bleibt erhalten, mit dem Eintrag 1.0.0 des Forks obenauf, der
  beginnt mit „Fork of eZChangeclass for Exponential Legacy / Exponential 6 on PHP 8, taking the OpenContent fork as
  its base and the mapping pre-selection from the Netgen fork“;
- die Lizenz: GPL, unverändert.

## Was Sie bei Ihrer eigenen Portierung prüfen sollten

| Frage | Warum |
|---|---|
| Welche Lizenz hat jeder Teil, und lassen sie sich kombinieren? | [Lizenzen und wie sie sich kombinieren lassen](lizenzen-und-kompatibilitaet.md) |
| Reist der Lizenztext jedes Teils mit ihm? | MIT, BSD und Apache verlangen den Hinweis; die GPL verlangt die Lizenz |
| Wird jeder Autor genannt, in der README und in den Metadaten? | Fairness, und die Seite „Über“ |
| Ist klar, welche Arbeit Ihre ist? | Benutzer und die ursprünglichen Autoren müssen wissen, wen sie fragen sollen |
| Wird der fremde Code getrennt gehalten (in einem eigenen Ordner)? | Updates des Originals lassen sich einspielen; die Lizenzen bleiben klar |
| Sind Ihre Änderungen am fremden Code eigene Commits mit Begründung? | Sie können sie zurückschicken und nach einem Update erneut anwenden |
| Haben Sie Ihre Korrekturen zurück angeboten? | [Mit Pull Requests zurückgeben](mit-pull-requests-zurueckgeben.md) |

## Eine Anmerkung zu „inspiriert von“

Manche Erweiterungen sind keine Portierungen, sondern Neuimplementierungen: Sie übernehmen die Ideen eines anderen
Projekts und schreiben neuen Code für Exponential. Ideen sind nicht so geschützt wie Code
([Lizenzen lesen, bevor Sie etwas wiederverwenden](../teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md)),
aber es ist trotzdem richtig zu sagen, woher sie stammen. Die Exponential-Layouts-Erweiterungen tun das in ihren
READMEs („an Exponential Legacy port inspired by“ die entsprechenden Pakete eines anderen Projekts). Sobald echter
Code, Templates oder JavaScript aus dem anderen Projekt enthalten sind, ist es eine Portierung im Sinne dieses
Kapitels, und dessen Lizenz und Hinweise gelten.

## Verwandte Seiten

- Zurück: [Die Community durch Code wachsen lassen](die-community-durch-code-wachsen-lassen.md)
- Weiter: [Fallstudie: ein Pull Request an einen Upstream-Autor](fallstudie-ein-pull-request-an-einen-upstream-autor.md)
- [Fallstudie: von eZChangeclass zu expchangeclass](../../../../eng-us/help/extension-development/part-4-upgrading/case-study-expchangeclass.md)
- [Ethisches Rebranding](ethisches-rebranding.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
