---
title: Erweiterungen und Themes mit der Community teilen
audience: community, developer, designer, partner
category: community
order: 29
summary: Wie Community-Mitglieder ihre eigenen Exponential-Erweiterungen, Themes und Seitenpakete teilen - Veröffentlichung im eigenen Repository unter der GPL, Composer und Packagist, Projects, Versionierung und dauerhafte Tags, und wann die Übergabe von Code an das Projekt das CLA braucht.
keywords: Erweiterung, Theme, Seitenpaket, teilen, veröffentlichen, GitHub, Packagist, Composer, Projects, GPL, Version, Tag, ezinfo.php, extension.xml, CLA, extension, site package, share, publish
type: help
---
# Erweiterungen und Themes mit der Community teilen

Vieles, was Exponential nützlich macht, steckt in Erweiterungen, und viele davon begannen als die Lösung einer Person
für ihr eigenes Problem. Wenn Sie Ihre teilen, können andere darauf aufbauen.

## Inhalt

1. [Zwei Wege zu teilen](#zwei-wege-zu-teilen)
2. [Die eigene Arbeit veröffentlichen](#die-eigene-arbeit-veröffentlichen)
3. [Anderen davon erzählen](#anderen-davon-erzählen)
4. [An das Projekt übergeben](#an-das-projekt-übergeben)
5. [Am Leben erhalten](#am-leben-erhalten)

## Zwei Wege zu teilen

| Weg | Sie behalten | CLA |
|---|---|---|
| **Selbst veröffentlichen**: Ihr eigenes Repository, Ihr eigenes Packagist-Paket | Das Urheberrecht und die Pflege | Nicht nötig: „Wenn Sie einen Beitrag geschrieben haben und ihn auf GitHub verfügbar machen möchten, müssen Sie das CLA nicht unterschreiben“ (`CONTRIBUTING.md`) |
| **An 7x zur Aufnahme** in das Projekt übergeben, zum Beispiel zur Pflege in der Organisation se7enxweb | Die Nennung; das Urheberrecht wird übertragen | Nötig, wie bei Kernel-Code ([Das CLA erklärt](das-cla-erklaert.md)) |

## Die eigene Arbeit veröffentlichen

Die vollständige Anleitung finden Sie unter [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md) und in
Teil 7 des Buchs zur Erweiterungsentwicklung, [Veröffentlichen](../../erweiterungsentwicklung/teil-7-veroeffentlichen/README.md).
Kurz gesagt:

1. **Aufräumen**: keine Geheimnisse, keine Kundendaten, keine websitespezifischen Einstellungen.
2. **Eine Lizenz wählen.** Wenn Sie eine Erweiterung verbreiten, gelten die Bedingungen der GPL; das Projekt empfiehlt
   GPL 2 oder neuer ([Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md)).
3. **Beschreiben** Sie sie in `ezinfo.php` und `extension.xml` mit derselben Versionsnummer
   ([ezinfo.php und extension.xml hochzählen](../../erweiterungsentwicklung/teil-7-veroeffentlichen/ezinfo-und-extension-xml-anheben.md)).
4. **Veröffentlichen** Sie ein öffentliches Repository und ein Composer-Paket auf Packagist
   ([Composer und Packagist](../../erweiterungsentwicklung/teil-7-veroeffentlichen/composer-und-packagist.md)).
5. **Taggen Sie Releases** und verschieben Sie nie einen Tag, der gepusht wurde
   ([Tags sind dauerhaft](../../erweiterungsentwicklung/teil-7-veroeffentlichen/tags-sind-dauerhaft.md)).

Themes folgen den Bereichen für Designer: [Theme-Erweiterungen](../../designer/erweiterungen/theme-erweiterungen-hier-beginnen.md)
und [Veröffentlichung](../../designer/veroeffentlichung/ein-theme-veroeffentlichen-hier-beginnen.md). Seitenpakete:
[Ihr Seitenpaket beitragen](../../seitenpakete/ihr-seitenpaket-beitragen.md).

## Anderen davon erzählen

- Tragen Sie sie auf [Projects](https://projects.exponential.earth/) ein ([Auf Share und Projects veröffentlichen](../../fuersprecher/auf-share-und-projects-veroeffentlichen.md)).
- Kündigen Sie sie in den Foren an, mit dem, was sie tut, welche Exponential-Versionen sie unterstützt und wie man sie
  installiert ([Ein Release ankündigen](../../erweiterungsentwicklung/teil-7-veroeffentlichen/ein-release-ankuendigen.md)).
- Benennen Sie neuen Code nach der Präfixregel, damit er nicht mit anderem kollidiert
  ([Namenspräfixe](../../../standards/namenspraefixe-sevenx-und-exp.md)).

## An das Projekt übergeben

Wenn das Projekt Ihre Erweiterung pflegen soll, eröffnen Sie zuerst ein Issue im Community-Tracker oder sprechen Sie
mit den Maintainern. Code, der 7x „zur Aufnahme in das Projekt“ übergeben wird, fällt „auf dieselbe Weise“ wie
Kernel-Code unter das CLA (`CONTRIBUTING.md`, „What it does not cover“).

## Am Leben erhalten

- Beantworten Sie Issues, auch mit „nicht jetzt“.
- Testen Sie sie mit neuen 6.0.x-Releases.
- Wenn Sie sie nicht mehr pflegen können, sagen Sie das in der README, damit jemand sie übernehmen kann
  ([Eine verwaiste Erweiterung übernehmen](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/eine-verwaiste-erweiterung-uebernehmen.md)).

## Verwandte Seiten

- [Als Community-Mitglied mitwirken](README.md)
- [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md)
- [Eine Erweiterung veröffentlichen (Partner)](../../partner/eine-erweiterung-veroeffentlichen.md)
- [Ökosystem: hier beginnen](../../../../eng-us/help/ecosystem/ecosystem-start-here.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
