---
title: Korrekturen aus Bundles zurückgeben
audience: bundler, partner, developer
category: bundling
order: 470
summary: Warum und wie ein Bündler Korrekturen und Verbesserungen an das Projekt schickt, statt sie mitzuschleppen - der Beitragsablauf des Kernels, die GPL und die Contributor Licensing Agreement, Erweiterungen und Seitenpakete, Dokumentation und was privat bleibt.
keywords: beitragen, Upstream, Pull Request, Fork, Commit-Nachricht, Erweiterung, Seitenpaket, Dokumentation, contribute, upstream, pull request, fork, CONTRIBUTING.md, CLA, GPL, commit message, Added, Updated, Fixed, filelist.md5, extension, site package, documentation, bundle
type: help
---
# Korrekturen aus Bundles zurückgeben

Diese Seite richtet sich an Bündler, die beim Bauen ihres Bundles Probleme finden und beheben. Eine Korrektur, die Sie
behalten, müssen Sie bei jedem Release erneut anwenden; eine Korrektur, die im Projekt übernommen wird, kommt mit dem
nächsten Release zu allen, auch zu Ihnen.

## Inhalt

1. [Was Sie zurückschicken](#was-sie-zurückschicken)
2. [Der Ablauf im Kernel](#der-ablauf-im-kernel)
3. [Lizenzierung von Beiträgen](#lizenzierung-von-beiträgen)
4. [Erweiterungen und Seitenpakete](#erweiterungen-und-seitenpakete)
5. [Dokumentation](#dokumentation)
6. [Was privat bleibt](#was-privat-bleibt)

## Was Sie zurückschicken

| Beim Bündeln gefunden | Senden an |
|---|---|
| Ein Fehler im Kernel oder in einem Installer | <https://github.com/se7enxweb/exponential>, Issue oder Pull Request |
| Eine fehlende Option, die das Bündeln erleichtern würde | zuerst ein Issue, das den Bedarf beschreibt |
| Ein bewusster Standard, der für Ihr Bundle falsch ist | ein Issue mit klarer Begründung; es kann ein [negatives Feature](../projekt/unterstuetzung-erhalten.md#negative-funktionen) sein, auf das andere Websites sich verlassen, schlagen Sie also eine Option vor und rechnen Sie mit mehreren Prüfrunden |
| Ein Fehler in einer Erweiterung | das Repository der Erweiterung |
| Ein verbessertes oder neues Seitenpaket | das Repository des Paketservers ([Ihr Seitenpaket beitragen](../seitenpakete/ihr-seitenpaket-beitragen.md)) |
| Ein Fehler oder eine Lücke in der Dokumentation | ein Pull Request an `doc/` |
| Ein Sicherheitsproblem | security@se7enx.com, vertraulich, nie als öffentliches Issue |

## Der Ablauf im Kernel

Aus [Code zurückgeben](../partner/code-zurueckgeben.md#der-ablauf) und `CONTRIBUTING.md`:

1. Forken Sie das Repository und legen Sie einen Branch für eine Änderung an.
2. Nehmen Sie die Änderung vor und folgen Sie dabei dem Programmierstil.
3. Committen Sie mit einem Betreff, der damit beginnt, was die Änderung tut: `Added: `, `Updated: `, `Fixed: `,
   `Removed: ` oder `Renamed: `.
4. Halten Sie das Dateimanifest aktuell: Jede hinzugefügte, geänderte oder entfernte Datei aktualisiert ihre Zeile in
   `share/filelist.md5` (`make manifest-fix`; `make hooks` installiert die Prüfung).
5. Sorgen Sie dafür, dass die Tests bestehen, aktualisieren Sie die Dokumentation, pushen Sie und eröffnen Sie einen
   Pull Request.
6. Hinterlegen Sie bei 7x eine unterschriebene Contributor Licensing Agreement.

## Lizenzierung von Beiträgen

"Mit Ihrem Beitrag stimmen Sie zu, dass Ihre Beiträge unter der GNU General Public License, Version 2.0 oder später,
lizenziert werden", und "ein Beitrag zum Kernel braucht Ihre unterschriebene Contributor Licensing Agreement"
(`CONTRIBUTING.md`). Lesen Sie die Vereinbarung, bevor Sie unterschreiben; wenn Ihre Firma die Rechte an der Arbeit
ihrer Entwickler hält, unterschreibt die Firma oder gibt ihre Erlaubnis
([Code zurückgeben](../partner/code-zurueckgeben.md)).

## Erweiterungen und Seitenpakete

- Finden Sie das Upstream-Projekt einer Erweiterung, bevor Sie sie forken
  ([Erweiterungen an das Upstream-Projekt zurückgeben](../partner/erweiterungen-zurueckgeben.md)).
- Allgemeine Erweiterungen, die Sie für Ihr Bundle geschrieben haben, können eine Veröffentlichung wert sein
  ([Erweiterungen aus einem Bundle veröffentlichen](erweiterungen-aus-einem-bundle-veroeffentlichen.md)).
- "Ein Seitenpaket gebaut? 7x und die Exponential Foundation ermutigen Sie ausdrücklich, es per Pull Request beim
  Repository hinter dem Paketserver einzureichen, damit jede Installation es anbieten kann"
  ([Seitenpakete: hier beginnen](../seitenpakete/README.md)).

## Dokumentation

Die Hilfe ist freie Dokumentation unter der GNU FDL 1.3; Bündler, die darauf aufbauend Anleitungen schreiben, können
Verbesserungen an die Originalseiten schicken
([Dokumentation und Übersetzungen beitragen](../partner/dokumentation-und-uebersetzungen-beitragen.md)). Eine Seite über ein
Problem beim Bündeln hilft dem nächsten Bündler.

## Was privat bleibt

- Namen, Zugangsdaten, Inhalte und Geschäftsregeln von Kunden.
- Die kommerziellen Bedingungen Ihres Bundles.
- Alles, was Sie nicht veröffentlichen dürfen; prüfen Sie vor einem Beitrag, dass Sie die Rechte daran halten.

Partner sammeln Punkte für Community-Arbeit, einschließlich Beiträgen
([Punkte durch Community-Arbeit sammeln](../partner/punkte-durch-community-arbeit.md)).

## Verwandte Seiten

- [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Integritätsprüfungen](integritaetspruefungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
