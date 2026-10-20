---
title: Den Paketserver im Browser durchsuchen
audience: admin, developer, hosting, website-creator
category: install-upgrade
order: 35
summary: Wie Sie den Paketserver in einem Webbrowser ansehen, was die erzeugten Seiten der Verzeichnisauflistung zeigen und was nicht, wie Sie dort einen Paketindex lesen und wie Sie stattdessen die Geschichte einer Datei auf GitHub sehen.
keywords: Paketserver, Browser, Verzeichnisauflistung, Index of, Release-Ordner, thumbnails, index.xml, ezpkg, Last Modified, GitHub, durchsuchen, package server, directory listing, release folder, browse
type: help
---
# Den Paketserver im Browser durchsuchen

Diese Seite ist für alle, die sehen wollen, was auf dem Paketserver liegt, ohne eine Kommandozeile. Sie erklärt die Seiten, die Sie
bekommen, wenn Sie den Server in einem Browser öffnen, was jede Spalte bedeutet und wo Sie nachsehen, wenn eine Seite nicht genug sagt.

## Inhalt

1. [Wo Sie beginnen](#wo-sie-beginnen)
2. [Die Seiten der Auflistung](#die-seiten-der-auflistung)
3. [Was die Spalten bedeuten](#was-die-spalten-bedeuten)
4. [Dateien öffnen](#dateien-öffnen)
5. [Was die Auflistung nicht zeigt](#was-die-auflistung-nicht-zeigt)
6. [Dieselben Dateien auf GitHub](#dieselben-dateien-auf-github)
7. [Aus dem Setup-Assistenten](#aus-dem-setup-assistenten)
8. [Verwandte Seiten](#verwandte-seiten)

## Wo Sie beginnen

| Öffnen Sie | Sie sehen |
|---|---|
| <https://exponential.packages.exponential.earth/> | die oberste Ebene: `exponential/`, `CNAME`, `README.md`, `crossdomain.xml` |
| <https://exponential.packages.exponential.earth/exponential/6.0/> | einen Ordner pro Release, `6.0.0` bis `6.0.15` |
| <https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/> | die Pakete von 6.0.15, `index.xml` und `thumbnails/` |
| <https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/thumbnails/> | die Bilder der Seitenpakete |

Jede Adresse endet auf `/`. Ohne ihn antwortet der Server mit einer Umleitung auf die Adresse mit `/`.

## Die Seiten der Auflistung

Die Seiten mit dem Titel „Index of /...“ werden nicht von Hand geschrieben. Ein Workflow von GitHub Actions im Repository des Servers führt
bei jedem Push die Action [github-pages-directory-listing](https://github.com/jayanta525/github-pages-directory-listing) aus, die in jeden
Ordner eine `index.html` schreibt, bevor GitHub Pages ihn veröffentlicht ([Der Paketserver auf GitHub](der-paketserver-auf-github.md)).

Eine Seite der Auflistung hat:

- die Überschrift „Index of /exponential/6.0/6.0.15“ (der Pfad des Ordners);
- eine Zeile `../`, die einen Ordner nach oben führt;
- zuerst eine Zeile pro Ordner, dann eine Zeile pro Datei, jede mit einem Symbol für ihre Art;
- eine Tabelle mit den Spalten **Name**, **Size** und **Last Modified**.

Die Seite lädt ihre Styles aus einem öffentlichen CDN und sieht deshalb schlicht aus, wenn der Browser externe Skripte blockiert. Die Links
funktionieren so oder so.

## Was die Spalten bedeuten

| Spalte | Bedeutung | Beispiel |
|---|---|---|
| Name | die Datei oder der Ordner; ein Ordner endet auf `/` | `sevenx_multisite.ezpkg` |
| Size | die Dateigröße, in Einheiten von 1024 Bytes mit den Bezeichnungen `KB` und `MB`; `-` für einen Ordner | `89.16 KB` |
| Last Modified | der Zeitpunkt, zu dem der Lauf des Ausrollens die Datei geschrieben hat, in UTC | `2026-10-16 20:42:40 UTC` |

> **Hinweis**
> **Last Modified ist der Zeitpunkt des letzten Ausrollens, nicht der letzten Änderung der Datei.** Jeder Push baut die ganze Website neu,
> sodass alle Dateien der Website dieselbe Zeit zeigen. Um zu sehen, wann sich ein Paket wirklich geändert hat, sehen Sie seine Geschichte
> auf GitHub an (unten).

## Dateien öffnen

| Datei | Was der Browser tut |
|---|---|
| `index.xml` | zeigt das XML (ausgeliefert als `application/xml`); die Seitenpakete stehen am Ende ([Das Format index.xml](das-format-index-xml.md)) |
| `*.ezpkg` | lädt das Archiv herunter (ausgeliefert als `application/octet-stream`) |
| `thumbnails/*.png` | zeigt das Bild |
| `README.md` | zeigt den Markdown-Text oder lädt ihn herunter, je nach Browser |

Eine heruntergeladene `.ezpkg` ist ein mit gzip komprimiertes tar-Archiv. Die meisten Archivwerkzeuge öffnen sie, nachdem Sie sie in
`.tar.gz` umbenannt haben; auf der Kommandozeile listet `tar -tzf` sie so auf, wie sie ist
([Ein Paket von Hand herunterladen](ein-paket-von-hand-herunterladen.md)).

## Was die Auflistung nicht zeigt

- **Verborgene Ordner.** Die oberste Seite führt `.git/` und `.github/` auf, aber GitHub Pages liefert keine Ordner aus, die mit einem Punkt
  beginnen: Diese Links antworten mit `404`. Lesen Sie `.github/` stattdessen auf GitHub.
- **Versionen und Beschreibungen.** Eine Auflistung kennt nur Dateinamen und Größen. Zusammenfassung, Version und Typ jedes Pakets stehen in
  `index.xml`.
- **Welche Pakete eine Installation nutzt.** Eine Installation liest einen Release-Ordner, den, den ihre `RemotePackagesIndexURL` nennt
  ([Einstellungen des Paketservers](einstellungen-des-paketservers.md)).
- **Geschichte.** Frühere Versionen eines Pakets werden auf der Website nicht aufbewahrt; eine Korrektur ersetzt die Datei in ihrem
  Release-Ordner. Git bewahrt jede frühere Version.

## Dieselben Dateien auf GitHub

Das Repository zeigt denselben Baum mit Geschichte:

| Sie wollen | Öffnen Sie |
|---|---|
| den Baum | <https://github.com/se7enxweb/exponential.packages.exponential.earth> |
| die Commits, die ein Paket geändert haben | die Datei im Baum, dann **History** |
| die Ausrollvorgänge | den Reiter [Actions](https://github.com/se7enxweb/exponential.packages.exponential.earth/actions) des Repositorys |

Große Dateien werden auf GitHub nicht als Text gezeigt; nutzen Sie **Download raw file** oder die Adresse auf dem Paketserver, um sie zu
holen. Jeder Commit nennt das Paket, das er geändert hat, zum Beispiel einen Commit, der den Installer des Pakets cjw_multisite ezownerchange
aktivieren lässt.

## Aus dem Setup-Assistenten

Der Schritt **Site Paket** des Setup-Assistenten verlinkt den Server an zwei Stellen
(`design/standard/templates/setup/init/site_types.tpl`):

- bei einem Fehler „URL des entfernten Repositorys:“, gefolgt vom Release-Ordner, den er versucht hat (`RemotePackagesIndexURL`);
- unter **Weitere Pakete** einen Link auf das vollständige Archiv aller Pakete, der `RemotePackagesIndexURLBase` öffnet, standardmäßig
  <https://exponential.packages.exponential.earth/exponential>.

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Der Paketserver](README.md)
- [Was auf dem Paketserver liegt](was-auf-dem-paketserver-liegt.md)
- [Der Paketserver auf GitHub](der-paketserver-auf-github.md)
- [Ein Paket von Hand herunterladen](ein-paket-von-hand-herunterladen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
