---
title: Ein Paket von Hand herunterladen
audience: admin, developer, hosting
category: install-upgrade
order: 40
summary: Wie Sie eine .ezpkg selbst vom Paketserver holen, mit tar hineinsehen, prüfen, dass sie vollständig ist und dem entspricht, was Sie erwarten, und sie mit ezpm, der Seite zum Hochladen in der Administration oder dem Setup-Assistenten importieren.
keywords: herunterladen, curl, wget, tar -tzf, ezpkg, Prüfsumme, sha256sum, gzip, package.xml, importieren, ezpm import, hochladen, Setup-Assistent, Paketserver, requires, download, checksum, import, upload, setup wizard, package server
type: help
---
# Ein Paket von Hand herunterladen

Diese Seite ist für Administratoren, die ein Paket ansehen wollen, bevor ein Installer es nutzt, oder die ein Paket auf einen Server bringen
müssen, der den Paketserver nicht erreichen kann. Sie zeigt, wie Sie eine `.ezpkg` herunterladen, hineinsehen, sie prüfen und importieren.

## Inhalt

1. [Die Adresse finden](#die-adresse-finden)
2. [Herunterladen](#herunterladen)
3. [Hineinsehen](#hineinsehen)
4. [Prüfen](#prüfen)
5. [Herausfinden, was es verlangt](#herausfinden-was-es-verlangt)
6. [Importieren](#importieren)
7. [Verwandte Seiten](#verwandte-seiten)

## Die Adresse finden

Die Adresse jedes Pakets ist das Attribut `url` seines Eintrags in der `index.xml` des Release. Für 6.0.15 folgen alle einem Muster:

```text
https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/<name>.ezpkg
```

Um die Namen und Typen eines Release aufzulisten:

```bash
curl -s https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml \
  | grep -o '<package name="[^"]*"[^>]*type="[^"]*"' \
  | sed 's/<package name="\([^"]*\)".*type="\([^"]*\)"/\1 \2/'
```

Oder öffnen Sie den Release-Ordner in einem Browser ([Den Paketserver im Browser durchsuchen](den-paketserver-im-browser-durchsuchen.md)).

## Herunterladen

Arbeiten Sie in einem eigenen Ordner, zum Beispiel `var/tmp/` der Installation, nicht in einem geteilten temporären Ordner:

```bash
mkdir -p var/tmp/packages && cd var/tmp/packages
curl -fsSLO https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/sevenx_multisite.ezpkg
```

| Option | Warum |
|---|---|
| `-f` | bei einem HTTP-Fehler scheitern, statt die Fehlerseite als Paket zu speichern |
| `-s`, `-S` | leise, aber Fehler weiterhin zeigen |
| `-L` | einer Umleitung folgen (eine Adresse `http://` wird auf `https://` umgeleitet) |
| `-O` | den Dateinamen der URL behalten |

`wget https://...` tut dasselbe. Hinter einem Proxy setzen Sie ihn so, wie Ihre Werkzeuge es erwarten (für curl `-x` oder die Variable
`https_proxy`).

## Hineinsehen

Eine `.ezpkg` ist ein mit gzip komprimiertes tar-Archiv mit dem Inhalt des Paketordners oben:

```bash
tar -tzf sevenx_multisite.ezpkg
```

```text
documents/
documents/LICENCE
files/
files/default/
files/default/image/
files/default/image/logo.png
files/default/thumbnail/
files/default/thumbnail/thumbnail.png
package.xml
settings/
settings/ini-admin.php
settings/sevenx-multi-site-installer.php
...
```

Eine Datei lesen, ohne zu entpacken:

```bash
tar -xzOf sevenx_multisite.ezpkg package.xml | less
```

In einen eigenen Ordner entpacken, um den Rest zu lesen:

```bash
mkdir sevenx_multisite && tar -xzf sevenx_multisite.ezpkg -C sevenx_multisite
```

Was die Dateien sind: [Die Datei .ezpkg](../grundlagen/die-datei-ezpkg.md) und [Referenz zu package.xml](../grundlagen/referenz-zu-package-xml.md).

## Prüfen

| Prüfung | Befehl | Erwartet |
|---|---|---|
| Es sind gzip-Daten | `file sevenx_multisite.ezpkg` | `gzip compressed data` |
| Es ist vollständig | `ls -l` und die `Content-Length` von `curl -sI <url>` | dieselbe Anzahl Bytes |
| Das Archiv ist intakt | `tar -tzf sevenx_multisite.ezpkg > /dev/null && echo ok` | `ok` |
| Die Definition ist wohlgeformt | `tar -xzOf sevenx_multisite.ezpkg package.xml \| xmllint --noout -` | keine Ausgabe |
| Keine unsicheren Einträge | `tar -tzvf sevenx_multisite.ezpkg \| grep -E '^[lhcbp]\|\.\./'` | keine Ausgabe (keine Links, Geräte oder Pfade mit `..`) |
| Es ist die Datei in Git | `sha256sum` Ihrer Datei und derselben Datei im Repository des Servers | dieselbe Summe |

Für die letzte Prüfung hat das Repository des Servers jede Datei mit ihrer Geschichte:

```bash
git clone --filter=blob:none https://github.com/se7enxweb/exponential.packages.exponential.earth.git
git -C exponential.packages.exponential.earth show HEAD:exponential/6.0/6.0.15/sevenx_multisite.ezpkg | sha256sum
```

Der Import wendet auch eigene Prüfungen an: gzip-Signatur, Grenzen für Größe und Einträge, nur einfache Dateien und Ordner mit relativen
Pfaden, eine gültige `package.xml` mit gültigem Namen. Sie sind in der
[Anleitung zu Paketen, 6. Ein Paket hochladen](../../../anleitungen/pakete.md#6-ein-paket-hochladen) aufgeführt.

## Herausfinden, was es verlangt

Ein Seitenpaket kommt selten allein. Sein `<requires>` nennt die anderen Pakete:

```bash
tar -xzOf sevenx_multisite.ezpkg package.xml | xmllint --xpath '//requires/require/@name' -
# or, to see min-version too:
tar -xzOf sevenx_multisite.ezpkg package.xml | grep -A2 '<require '
```

Für `sevenx_multisite` ergibt das `sevenx_classes` und `sevenx_multisite_democontent`, jeweils mit `min-version="5.1"`. Laden Sie auch
diese herunter und wiederum deren Anforderungen, bevor Sie die Pakete auf einen Server ohne Netzwerkzugang bringen. Siehe
[Abhängigkeiten und die Installationsreihenfolge](../grundlagen/abhaengigkeiten-und-die-installationsreihenfolge.md).

## Importieren

Importieren legt das Paket ins lokale Repository (`var/storage/packages/<vendor>/<name>/`), ohne die Website zu ändern
([Importieren oder installieren](../grundlagen/importieren-oder-installieren.md)). Drei Wege:

**Auf der Kommandozeile**, aus dem Wurzelverzeichnis der Installation:

```bash
php ezpm.php import var/tmp/packages/sevenx_multisite.ezpkg
```

`ezpm.php import` nimmt den Pfad einer `.ezpkg` in jedem der beiden Paketformate und führt dieselben Prüfungen des Archivs aus wie die Seite
zum Hochladen. Es weist ein Paket ab, dessen Name bereits in einem Repository existiert (mit der Meldung, dass das Paket nicht importiert
werden konnte, weil es bereits existiert). Siehe [ezpm, der Paketmanager auf der Kommandozeile](../../../../en/us/features/6.0/ezpm-package-manager-cli.md).

**In der Administration**: **Setup > Pakete**, **Neues Paket importieren**, die Datei wählen, **Importiere Paket**
([Ein Paket importieren](../admin-oberflaeche/ein-paket-importieren.md)).

**Im Setup-Assistenten**: im Schritt **Site Paket** **Paket hochladen** mit der Datei, dann **Upload**. Die Seite wird erneut mit dem Paket
in der Liste gezeigt ([Der Setup-Assistent, 5.3.8](../../../installation/05-setup-assistent.md#538-site-paket)).

> **Tipp**
> Damit der Installer Ihre Kopien nutzt, statt herunterzuladen, importieren Sie das Seitenpaket und jedes Paket, das es verlangt, bevor Sie
> beginnen. Ein verlangtes Paket wird so genutzt, wie es ist, wenn seine Version mindestens die verlangte `min-version` ist. Der Kickstarter
> und `exp:install` nutzen ein importiertes Seitenpaket so, wie es ist; der Setup-Assistent zeigt nur den entfernten Eintrag, wenn der Index
> eine höhere Version führt als die importierte Kopie, und wenn Sie ihn wählen, wird das Paket neu heruntergeladen
> ([Der Setup-Assistent, 5.3.8](../../../installation/05-setup-assistent.md#538-site-paket)).

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Der Paketserver](README.md)
- [Offline-Installationen und Spiegel](offline-installationen-und-spiegel.md)
- [Das Format index.xml](das-format-index-xml.md)
- [Anleitung zu Paketen](../../../anleitungen/pakete.md)
- [Ein Paket vor der Veröffentlichung prüfen](../anforderungen/ein-paket-vor-der-veroeffentlichung-pruefen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
