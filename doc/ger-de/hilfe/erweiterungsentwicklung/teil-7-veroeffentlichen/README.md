---
title: "Teil 7: Ihre Erweiterung veröffentlichen"
audience: developer, designer, app-creator
category: extension-development
order: 70
summary: Teil 7 des Buches - eine Erweiterung so veröffentlichen, dass andere sich darauf verlassen können - Versionsnummern, dauerhafte Tags, Anheben der Metadaten, Composer und Packagist, Changelogs, GitHub-Releases, Pakete, Tests und eine Checkliste.
keywords: Buch, Teil 7, Release, veröffentlichen, Version, Tag, Packagist, Composer, Changelog, GitHub-Release, ezinfo, extension.xml, filelist.md5, ezpkg, Checkliste, book, part 7, release, version, tag, changelog, GitHub release, checklist
type: help
---
# Teil 7: Ihre Erweiterung veröffentlichen

Dies ist der siebte Teil von [Erweiterungsentwicklung: das Buch](../README.md).
Ein Release macht aus „dem Code in meinem Repository“ die „Version 1.2.3, die Sie installieren können und auf die Sie
sich verlassen können“. Es ist ein Versprechen: dass diese Version immer genau diese Dateien bedeutet. Dieser Teil
zeigt, wie Sie dieses Versprechen sorgfältig geben, denn sobald eine Version veröffentlicht ist, lässt sie sich nicht
zurücknehmen.

## Die Kapitel

| Kapitel | Was es beantwortet |
|---|---|
| [Regeln für Versionsnummern](regeln-fuer-versionsnummern.md) | was die Zahlen bedeuten und wie die letzte Stelle weiterzählt |
| [Die nächste Versionsnummer wählen](die-naechste-version-waehlen.md) | nachsehen, was schon veröffentlicht ist, richtig sortiert |
| [Tags und Releases sind dauerhaft](tags-sind-dauerhaft.md) | warum ein gepushter Tag nie verschoben wird und was Sie stattdessen tun |
| [ezinfo.php und extension.xml anheben](ezinfo-und-extension-xml-anheben.md) | die Version in der Erweiterung, vor dem Tag |
| [Composer und Packagist](composer-und-packagist.md) | das Paket registrieren, damit `composer require` es findet |
| [Der Packagist-Webhook](der-packagist-webhook.md) | warum ein neuer Tag nicht erscheint und wie Sie das beheben |
| [Changelogs](changelogs.md) | aufschreiben, was sich geändert hat, aus den Commit-Nachrichten |
| [GitHub-Releases und Release Notes](github-releases-und-release-notes.md) | die Release-Seite, die Benutzer lesen |
| [filelist.md5 und die Upgrade-Prüfung](filelist-md5-und-die-upgrade-pruefung.md) | Prüfsummen für Erweiterungen, die welche mitliefern |
| [Pakete (.ezpkg) und der Paket-Ersteller](pakete-ezpkg.md) | der andere Weg der Verbreitung |
| [Vor einem Release testen](vor-einem-release-testen.md) | was Sie ausführen, bevor Sie taggen |
| [Ein Release ankündigen](ein-release-ankuendigen.md) | den Menschen Bescheid sagen, die die Erweiterung verwenden |
| [Der Wartungslebenszyklus](der-wartungslebenszyklus.md) | vom ersten Release bis zum letzten |
| [Die Release-Checkliste](die-release-checkliste.md) | das ganze Vorgehen auf einer Seite |

## Die eine Regel

**Verschieben, löschen oder erstellen Sie nie einen Tag neu, der gepusht wurde, und ersetzen Sie nie die Dateien eines
veröffentlichten Releases.** Wenn ein Release fehlerhaft ist, veröffentlichen Sie die nächste Version mit der
Korrektur und sagen Sie in ihren Notes, was falsch war. Die Gründe stehen in
[Tags und Releases sind dauerhaft](tags-sind-dauerhaft.md); alles andere in diesem Teil dreht sich darum, es beim
ersten Mal richtig zu machen.

## Verwandte Seiten

- Zurück: [Wo die Hilfe für Designer weitergeht](../teil-6-theme-entwickler/wo-die-hilfe-fuer-designer-weitergeht.md)
- Weiter: [Regeln für Versionsnummern](regeln-fuer-versionsnummern.md)
- [Spezifikation: Metadaten von Extensions](../../../spezifikationen/6.0/extension-metadaten.md)
- [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md)
- [Erweiterungsentwicklung: das Buch](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
