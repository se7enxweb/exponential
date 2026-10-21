---
title: "Ein verwaistes Theme übernehmen"
audience: designer, developer
category: theme-lifecycle
order: 300
summary: Ein Theme übernehmen, dessen Autor die Pflege eingestellt hat - zuerst fragen, dann im ursprünglichen Repository weitermachen, wenn Sie Zugang bekommen, oder es unter neuem Namen forken; und wie sich alte Erweiterungen aus dem Community-Archiv auf das aktuelle Exponential bringen lassen.
keywords: verwaistes Theme, übernehmen, nicht gepflegt, Fork, den Autor fragen, alte Erweiterungen, Projektarchiv, Upgrade, abandoned theme, adopt, take over, unmaintained, ask the author, old extensions, projects archive
type: help
---
# Ein verwaistes Theme übernehmen

Diese Seite ist für Designer, die ein Theme nutzen oder mögen, das niemand mehr pflegt. Viele ältere Erweiterungen und
Designs stehen in den Archivtypen von [projects.exponential.earth](https://projects.exponential.earth/types) (den Typen
„Via Projects.ez.no“); manche verdienen ein zweites Leben.

## Schritt für Schritt

1. **Prüfen Sie, ob es wirklich verwaist ist**: lange keine Releases oder Antworten, ein Hinweis auf das Lebensende oder
   ein archiviertes Repository.
2. **Fragen Sie den Autor** (ein Issue, die Adresse in seinen Dateien): Würde er es übergeben oder Sie als Betreuer
   aufnehmen?
3. **Wenn ja**, folgen Sie der Übergabe aus Sicht des neuen Betreuers ([Ein Theme übergeben](uebergabe-an-einen-neuen-betreuer.md)).
4. **Wenn keine Antwort oder nein**, forken Sie es unter neuem Namen ([Ein Theme forken](ein-theme-forken.md),
   [Einen Fork fair umbenennen](einen-fork-umbenennen.md)). Die GPL erlaubt es; behalten Sie jeden Hinweis.
5. **Bringen Sie es auf den aktuellen Stand**: Metadatendateien ([ezinfo.php](../erweiterungen/ezinfo-php.md),
   [extension.xml](../erweiterungen/extension-xml.md)), `composer.json`, PHP 8 ([PHP-Versionskompatibilität](php-versionskompatibilitaet.md)),
   jQuery 4 und kein YUI ([JavaScript-Bibliotheken aktualisieren](javascript-bibliotheken-aktualisieren.md)), Maskierung
   ([Sicherheitskorrekturen](sicherheitskorrekturen-in-themes.md)), Prüfungen der Barrierefreiheit und der CSP.
6. **Veröffentlichen** Sie es mit einem Changelog, das den ursprünglichen Autor nennt und aufführt, was Sie geändert haben.
7. **Sagen Sie der Community**, wo das Theme weitergeht.

## Meist kleine Änderungen

Eine alte Erweiterung auf das aktuelle Exponential zu bringen, ist meist eine Reihe kleiner Änderungen: Die APIs des
Kernels sind weitgehend kompatibel geblieben. Arbeiten Sie die Liste in Schritt 5 Punkt für Punkt ab, mit einem Test nach
jedem.

## Verwandte Seiten

- [Danksagungen und Namensnennung](danksagungen-und-namensnennung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
