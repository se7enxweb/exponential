---
title: Vom Mitwirkenden zum Maintainer
audience: community, developer, designer, supporter
category: community
order: 62
summary: Wie eine regelmäßig mitwirkende Person bei Exponential die Pflege übernimmt - einer eigenen Erweiterung, einer verwaisten oder eines Bereichs des Projekts -, was Maintainer tun, welcher Release-Disziplin sie folgen und wie sie übergeben, wenn sie aufhören.
keywords: Maintainer, Pflege, Wartung, Erweiterung, verwaiste Erweiterung, übernehmen, Releases, Tags, Versionierung, Übergabe, Verantwortung, maintenance, extension, abandoned extension, adopt, versioning, handover, responsibility
type: help
---
# Vom Mitwirkenden zum Maintainer

Ein Maintainer ist jemand, der dauerhaft Verantwortung dafür übernimmt, dass ein Teil der Exponential-Welt
funktioniert: Issues beantworten, Änderungen prüfen und Releases erstellen.

## Inhalt

1. [Arten der Pflege](#arten-der-pflege)
2. [Was ein Maintainer tut](#was-ein-maintainer-tut)
3. [Release-Disziplin](#release-disziplin)
4. [Übergeben](#übergeben)

## Arten der Pflege

| Art | Wie sie beginnt | Seite |
|---|---|---|
| Ihre eigene Erweiterung, Ihr Theme oder Seitenpaket | Sie veröffentlichen es | [Erweiterungen und Themes teilen](../mitwirken/erweiterungen-und-themes-teilen.md) |
| Eine verwaiste Erweiterung | Ihr Autor hat aufgehört; Sie forken oder übernehmen sie | [Eine verwaiste Erweiterung übernehmen](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/eine-verwaiste-erweiterung-uebernehmen.md) |
| Ein Bereich des Projekts (eine Übersetzung, eine Gruppe von Hilfeseiten) | Sie haben dort lange beigetragen | [Vom Nutzer zum Mitwirkenden](vom-nutzer-zum-mitwirkenden.md) |

Die Quellen beschreiben keine formale Ernennung von Maintainern für Bereiche des Kernels; Merges in den Kernel nehmen
die Maintainer des Projekts vor ([Wer entscheidet](../so-funktioniert-es/wer-entscheidet.md)).

## Was ein Maintainer tut

- Beantwortet Issues, auch mit „nicht geplant“.
- Prüft Pull Requests und hilft Mitwirkenden durch die Runden
  ([Reviews geben und erhalten](../mitwirken/reviews-geben-und-erhalten.md)).
- Testet mit jedem neuen 6.0.x-Release und jeder neuen PHP-Version.
- Hält die README, das Changelog und die Versionsfelder aktuell.
- Folgt dem Verhaltenskodex als einer der „Verantwortlichen der Community“ in seinem Raum (`CODE_OF_CONDUCT.md`).

Die vollständige Beschreibung: [Der Weg zum Maintainer](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/der-weg-zum-maintainer.md)
und [Der Lebenszyklus der Pflege](../../erweiterungsentwicklung/teil-7-veroeffentlichen/der-wartungslebenszyklus.md).

## Release-Disziplin

| Regel | Seite |
|---|---|
| Ein gepushter Tag ist dauerhaft; ein Fehler wird mit der nächsten Version behoben | [Tags sind dauerhaft](../../erweiterungsentwicklung/teil-7-veroeffentlichen/tags-sind-dauerhaft.md) |
| Wählen Sie die nächste Nummer aus dem Veröffentlichten, nach Version sortiert | [Die nächste Version wählen](../../erweiterungsentwicklung/teil-7-veroeffentlichen/die-naechste-version-waehlen.md) |
| `ezinfo.php` und `extension.xml` tragen die Version des Release | [ezinfo.php und extension.xml hochzählen](../../erweiterungsentwicklung/teil-7-veroeffentlichen/ezinfo-und-extension-xml-anheben.md) |
| Packagist muss von neuen Tags erfahren | [Der Packagist-Webhook](../../erweiterungsentwicklung/teil-7-veroeffentlichen/der-packagist-webhook.md) |
| Vor jedem Release eine Checkliste durchgehen | [Die Release-Checkliste](../../erweiterungsentwicklung/teil-7-veroeffentlichen/die-release-checkliste.md) |

## Übergeben

Wenn Sie etwas nicht mehr pflegen können, sagen Sie das in der README und in den Foren, und nennen Sie eine
Nachfolge, falls es eine gibt. Ein ehrlicher Hinweis „nicht mehr gepflegt“ ist für Nutzer besser als Schweigen.

## Verwandte Seiten

- [Wege durch die Community](README.md)
- [Mentoring](../anderen-helfen/mentoring.md)
- [Einen Fork synchron halten](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/einen-fork-synchron-halten.md)
- [Die Community durch Code wachsen lassen](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/die-community-durch-code-wachsen-lassen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
