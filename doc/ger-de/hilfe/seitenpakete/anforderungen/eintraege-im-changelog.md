---
title: Einträge im Changelog
audience: developer, designer, partner
category: install-upgrade
order: 54
summary: Wie Sie den Changelog eines Seitenpakets schreiben, das Element changelog von package.xml mit einem Eintrag pro Release, was der Assistent zum Erstellen und ezpm schreiben, was die Paketseite zeigt und wie sich der Changelog des Pakets zur Commit-Geschichte des Quell-Repositorys verhält.
keywords: Changelog, Changelog des Pakets, entry, change, timestamp, person, release, Creation of package, package.xml, Commit-Nachrichten, Added, Updated, Removed, Renamed, changelog, package changelog, commit messages
type: help
---
# Einträge im Changelog

Diese Seite ist für Entwickler, die eine neue Version eines Seitenpakets veröffentlichen. Ein Changelog sagt einem Administrator vor der
Installation, was ein Release ändert. Ein Seitenpaket hat zwei: den `<changelog>` in seiner `package.xml`, der mit dem Archiv reist und auf
der Paketseite erscheint, und die Commit-Geschichte seines Quell-Repositorys. Diese Seite behandelt beide. **Keiner ist erzwungen**: Ein
Paket ohne Changelog lässt sich importieren und installieren. Beide sind Konventionen, die Prüfende erwarten.

## Inhalt

1. [Das Element changelog](#das-element-changelog)
2. [Was einen Eintrag schreibt](#was-einen-eintrag-schreibt)
3. [Was die Paketseite zeigt](#was-die-paketseite-zeigt)
4. [Was die ausgelieferten Pakete mitbringen](#was-die-ausgelieferten-pakete-mitbringen)
5. [Einen guten Eintrag schreiben](#einen-guten-eintrag-schreiben)
6. [Die Geschichte des Quell-Repositorys](#die-geschichte-des-quell-repositorys)
7. [Verwandte Seiten](#verwandte-seiten)

## Das Element changelog

`eZPackage::parseDOMTree()` liest jeden `<entry>` des ersten `<changelog>` und jeden `<change>` darin:

```xml
<changelog>
  <entry timestamp="1791331200" person="Example Maintainer" email="maintainer@example.com" release="2">
    <change>Updated: the installer activates ezownerchange.</change>
    <change>Added: a German starter page.</change>
  </entry>
  <entry timestamp="1705211779" person="Example Maintainer" email="maintainer@example.com" release="1">
    <change>Creation of package.</change>
  </entry>
</changelog>
```

| Attribut oder Element | Bedeutung | Hinweise |
|---|---|---|
| `timestamp` | Wann der Eintrag geschrieben wurde, Unix-Sekunden | Ohne ihn hat der Eintrag keine eigene Zeit |
| `person` | Wer ihn geschrieben hat | Auf der Paketseite gezeigt |
| `email` | Wie die Person erreichbar ist | Auf der Paketseite als Link `mailto:` gezeigt; nutzen Sie eine Adresse, die öffentlich sein soll |
| `release` | Das `<version><release>`, zu dem er gehört | Gelesen und geschrieben, nicht gezeigt |
| `<change>` | Eine Änderung | Ein Element pro Änderung; die Seite listet jede auf |

## Was einen Eintrag schreibt

| Werkzeug | Was es schreibt |
|---|---|
| Der Assistent zum Erstellen von Paketen, Schritt **Paket-Changelog** (`kernel/classes/ezpackagecreationhandler.php`) | Verlangt einen Namen, eine E-Mail und etwas Text und weist den Schritt ohne sie ab. Der Text beginnt als `- Creation of package.`; jede Zeile, die mit `-` oder `*` beginnt, wird zu einem `<change>`, andere Zeilen setzen die vorherige fort. Das Release ist standardmäßig die Release-Nummer des Pakets oder `1` |
| `php ezpm.php create` | Ein Eintrag `Creation of package`, vom Benutzer, als der ezpm läuft |
| Eine von Hand bearbeitete `package.xml` | Was Sie schreiben; nichts füllt einen fehlenden Zeitstempel aus |

Die Regeln des Assistenten sind der Grund, warum ein `<change>` eine einzelne Textzeile ist: Schreiben Sie eine Änderung pro Element, keine
Liste in einem Element.

## Was die Paketseite zeigt

Die Paketseite in der Administration (`design/admin4/templates/package/view/full.tpl`) hat einen Abschnitt **Logbuch**, wenn das Paket
Einträge hat. Für jeden Eintrag zeigt sie den Zeitstempel als Datum und Uhrzeit, die Person als Link auf die E-Mail-Adresse und jeden
`<change>` als Listeneintrag. Jeder Wert wird maskiert, sodass Markup in einer Änderung als Text erscheint. Das Attribut release wird nicht
gezeigt; sagen Sie deshalb im Text, zu welcher Version eine Änderung gehört, wenn es darauf ankommt.

## Was die ausgelieferten Pakete mitbringen

In den zwei Quell-Repositorys von 7x am 18.10.2026 geprüft:

- 15 der 16 Pakete haben genau einen Eintrag, `release="1"`, mit der einzigen Änderung `Creation of package.`; `sevenx_extension` hat keinen
  Changelog.
- Zehn Einträge haben einen `timestamp` (`1705211779`, 14. Januar 2024, in den Paketen von 7x; `1791331200`, 7. Oktober 2026, in
  `cjw_multisite_democontent`); die fünf Seitenpakete haben keinen.
- Keiner hält die seitdem gemachten Änderungen fest. Diese stehen in der Commit-Geschichte der Quell-Repositorys (siehe
  [Die Geschichte des Quell-Repositorys](#die-geschichte-des-quell-repositorys)).

Das ist der Zustand, den es zu verbessern gilt, nicht zu kopieren. Ein zur Veröffentlichung eingereichtes Paket sollte für jedes Release,
das es veröffentlicht, einen Eintrag hinzufügen.

## Einen guten Eintrag schreiben

- [ ] Ein `<entry>` pro veröffentlichtem Release, der neueste zuerst, mit `timestamp`, `person` und `release`.
- [ ] Ein `<change>` pro Änderung, beginnend mit `Added:`, `Updated:`, `Removed:` oder `Renamed:`, den Präfixen, die die Repositorys von 7x
      für Betreffzeilen von Commits nutzen.
- [ ] Sagen Sie, was ein Administrator bemerken wird: einen neuen Siteaccess, eine geänderte Rolle, eine neue Erweiterung, die der Installer
      aktiviert, ein hinzugefügtes oder entferntes Klassenattribut.
- [ ] Nennen Sie alles, was nach der Installation eine Handlung braucht.
- [ ] Nutzen Sie einen Namen und eine E-Mail-Adresse, die veröffentlicht werden sollen. Setzen Sie nicht die Adresse einer Privatperson in
      ein öffentliches Paket.
- [ ] Behalten Sie die alten Einträge. Ein Changelog ist Geschichte; ein Eintrag wird nie neu geschrieben, nachdem sein Release
      veröffentlicht ist.

> **Hinweis**
> Das `<release>` des Eintrags und das `<version><release>` des Pakets sind dieselbe Zahl. Erhöhen Sie beide zusammen
> ([Versionsnummern](versionsnummern.md)).

## Die Geschichte des Quell-Repositorys

Die Commit-Geschichte des Quell-Repositorys ist die ausführliche Aufzeichnung. In
[se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage), das 1226 Commits bis zurück zum 12.10.2006 enthält, nutzen
die Betreffzeilen der Commits seit Januar 2026 die Präfixe `Updated:` (71), `Fixed:` (29), `Added:` (12) und `Removed:` (2). Eine
Betreffzeile nennt das Paket und die Wirkung, zum Beispiel (15.10.2026):

```text
Updated: The multisite installer of the sevenx_multisite package activates ezownerchange, so changing the owner of content is available in a new installation
```

Ein gutes Release hält beides im Gleichschritt: Die Zeilen `<change>` des neuen Eintrags fassen die Commits seit dem letzten Release
zusammen. Für eine Changelog-Datei im Repository selbst siehe [Eine Changelog-Datei führen](../../../../eng-us/help/designers/distribution/keeping-a-changelog-file.md)
und [Präfixe im Changelog](../../../../eng-us/help/designers/distribution/changelog-prefixes.md).

## Verwandte Seiten

- [Anforderungen an die Dokumentation von Seitenpaketen](README.md)
- [Versionsnummern](versionsnummern.md)
- [README und Dokumentation im Repository](readme-und-dokumentation-im-repository.md)
- [Release-Hinweise schreiben](../../../../eng-us/help/designers/distribution/writing-release-notes.md)
- [Referenz zu package.xml](../grundlagen/referenz-zu-package-xml.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
