---
title: Offline-Installationen und Spiegel
audience: admin, hosting, developer
category: install-upgrade
order: 41
summary: Wie Sie Exponential auf einem Server installieren, der den Paketserver nicht erreichen kann, indem Sie die Pakete zuerst importieren oder eine lokale index.xml führen, und wie Sie einen Release-Ordner auf einem eigenen Webserver spiegeln und Installationen darauf richten.
keywords: Offline-Installation, Spiegel, Air Gap, kein Internet, lokale index.xml, var/storage/packages/7x, importieren, Kickstarter, exp:install, Setup-Assistent, RemotePackagesIndexURL, git sparse-checkout, curl, Kopie des Paketservers, offline install, mirror, air gap, no internet, local index.xml, import, setup wizard, package server copy
type: help
---
# Offline-Installationen und Spiegel

Diese Seite ist für Administratoren und Hoster, deren Server keinen ausgehenden Internetzugang haben oder die wollen, dass jede Installation
eine getestete Kopie der Pakete nutzt. Sie erklärt, wie sich der Installer ohne den Paketserver verhält, wie Sie eine Offline-Installation
vorbereiten und wie Sie einen Spiegel betreiben.

## Inhalt

1. [Was der Installer ohne den Server tut](#was-der-installer-ohne-den-server-tut)
2. [Offline-Installation, Schritt für Schritt](#offline-installation-schritt-für-schritt)
3. [Eine lokale index.xml](#eine-lokale-indexxml)
4. [Einen Release-Ordner spiegeln](#einen-release-ordner-spiegeln)
5. [Installationen auf den Spiegel richten](#installationen-auf-den-spiegel-richten)
6. [Einen Spiegel aktuell halten](#einen-spiegel-aktuell-halten)
7. [Verwandte Seiten](#verwandte-seiten)

## Was der Installer ohne den Server tut

Das Verhalten kommt aus `kernel/setup/steps/ezstep_site_types.php`:

| Situation | Setup-Assistent | Kickstarter, `exp:install` |
|---|---|---|
| Der Index lässt sich nicht herunterladen, `var/storage/packages/7x/index.xml` existiert | diese Datei wird stattdessen genutzt, ohne Fehler | dasselbe |
| Der Index lässt sich nicht herunterladen, keine lokale `index.xml` | „Das Empfangen der Remote Site Paketliste ist fehlgeschlangen. Sie können die Pakete manuell hochladen.“; die importierten Seitenpakete werden weiterhin aufgeführt | das importierte Seitenpaket wird genutzt, wenn alle verlangten Pakete importiert sind; sonst hält der Schritt an |
| Ein verlangtes Paket ist nicht importiert und lässt sich nicht herunterladen | der Schritt hält mit einem Fehler an, der das Paket nennt | dasselbe |
| Alles ist importiert | für die Abhängigkeiten wird nichts heruntergeladen | nichts wird heruntergeladen |

Der verlässliche Weg, offline zu installieren, ist also, **zuerst jedes Paket zu importieren**. Die Dokumentation des Kickstarters sagt
dasselbe: Wenn sich der Index nicht lesen lässt, wird ein Paket im lokalen Repository genutzt, wenn alle seine Anforderungen importiert sind
(eine Offline-Installation) ([Der Kickstarter, 6.4](../../../installation/06-kickstarter.md#64-referenz-zu-kickstartini)).

> **Hinweis**
> Ein Probelauf (`exp:install --dry-run`, `exp:kickstarter run --dry-run`) lädt das Seitenpaket und seine Anforderungen immer in das
> temporäre Repository `var/storage/packages/dryrun/` herunter, um sie zu prüfen. Er braucht den Paketserver (oder einen Spiegel), selbst
> wenn die Pakete importiert sind ([Der Kickstarter, 6.7](../../../installation/06-kickstarter.md#67-probeläufe)).

## Offline-Installation, Schritt für Schritt

Auf einer Maschine mit Internetzugang:

1. Wählen Sie das Seitenpaket, zum Beispiel `sevenx_multisite_clean`.
2. Laden Sie es und jedes Paket herunter, das es verlangt, und wiederum deren Anforderungen
   ([Ein Paket von Hand herunterladen](ein-paket-von-hand-herunterladen.md#herausfinden-was-es-verlangt)). Für `sevenx_multisite` sind das
   `sevenx_classes` und `sevenx_multisite_democontent`.
3. Prüfen Sie jede Datei ([Prüfen](ein-paket-von-hand-herunterladen.md#prüfen)).

Auf dem Offline-Server, nachdem der Code an seinem Platz ist:

4. Kopieren Sie die Dateien `.ezpkg` in einen Ordner der Installation, zum Beispiel `var/tmp/packages/`.
5. Importieren Sie jede:

   ```bash
   for f in var/tmp/packages/*.ezpkg; do php ezpm.php import "$f"; done
   ```

6. Prüfen Sie, dass sie in `var/storage/packages/7x/` liegen:

   ```bash
   ls var/storage/packages/7x/
   ```

7. Installieren Sie wie üblich: `exp:install --package=sevenx_multisite_clean`, den Kickstarter mit
   `[site_types] Site_package=sevenx_multisite_clean` oder den Setup-Assistenten, in dem das Paket als importiert erscheint.
8. Entfernen Sie die Kopien in `var/tmp/packages/`, wenn die Installation fertig ist.

## Eine lokale index.xml

Wenn sich der Index nicht herunterladen lässt, sucht das Setup nach `index.xml` im System-Repository, dem Ordner von
`[RepositorySettings] Vendor` (standardmäßig `var/storage/packages/7x/index.xml`), und nutzt sie, als wäre sie heruntergeladen worden. Das ist
nützlich, wenn die Attribute `url` darin auf einen Server zeigen, den die Offline-Maschine erreichen **kann**, etwa einen Spiegel im eigenen
Netzwerk. Von sich aus lässt sie keine Pakete erscheinen: Ein Eintrag, dessen `url` nicht erreichbar ist, scheitert weiterhin, wenn er
gebraucht wird.

> **Warnung**
> Im Setup-Assistenten ersetzt ein Eintrag eines Seitenpakets aus einem Index die importierte Kopie in der Liste, wenn die Version im Index
> höher ist, und wenn Sie ihn wählen, wird das Paket erneut heruntergeladen. Ein lokaler Index, dessen `url` nicht erreichbar ist, kann also
> Ihr importiertes Seitenpaket verbergen. Halten Sie die Versionen in einem lokalen Index gleich oder niedriger als die der importierten
> Pakete, oder nutzen Sie keinen lokalen Index.

## Einen Release-Ordner spiegeln

Ein Spiegel ist eine Kopie eines Release-Ordners auf einem Webserver, den Sie kontrollieren. Nichts ist nötig außer dem Ausliefern
statischer Dateien.

**Aus Git**, nur mit dem Ordner, den Sie brauchen:

```bash
git clone --filter=blob:none --sparse https://github.com/se7enxweb/exponential.packages.exponential.earth.git
cd exponential.packages.exponential.earth
git sparse-checkout set exponential/6.0/6.0.15
```

**Vom Server**, anhand seines Index:

```bash
base=https://exponential.packages.exponential.earth/exponential/6.0/6.0.15
mkdir -p mirror/6.0.15/thumbnails && cd mirror/6.0.15
curl -fsSLO "$base/index.xml"
grep -o ' url="[^"]*"' index.xml | cut -d'"' -f2 | xargs -n1 curl -fsSLO
grep -o 'thumbnail_url="[^"]*"' index.xml | cut -d'"' -f2 | (cd thumbnails && xargs -n1 curl -fsSLO)
```

Schreiben Sie dann die Adressen in der kopierten `index.xml` auf Ihren Host um, weil jede `url` im Original den öffentlichen Server nennt:

```bash
sed -i 's#https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/#https://packages.example.com/6.0/6.0.15/#g' index.xml
```

Veröffentlichen Sie den Ordner so, dass `https://packages.example.com/6.0/6.0.15/index.xml` und jede Datei, die er nennt, mit `200`
antworten, und prüfen Sie sie ([Einen Index prüfen](das-format-index-xml.md#einen-index-prüfen)). Der Ordner 6.0.15 umfasst etwa 87 MiB;
das meiste davon ist `sevenx_multisite_democontent.ezpkg`.

## Installationen auf den Spiegel richten

```ini
# settings/override/package.ini.append.php
[RepositorySettings]
RemotePackagesIndexURL=https://packages.example.com/6.0/6.0.15
```

Der Setup-Assistent, der Kickstarter, `exp:install` und ezupdate nutzen dann den Spiegel. ezupdate braucht eine Adresse `https://`. Details,
einschließlich `kickstart.ini` und der Serverliste von ezupdate: [Einstellungen des Paketservers](einstellungen-des-paketservers.md) und
[Eine Website auf Ihren Paketserver richten](../eigene/eine-website-auf-ihren-paketserver-richten.md).

## Einen Spiegel aktuell halten

Pakete in einem Release-Ordner werden auf dem öffentlichen Server an Ort und Stelle korrigiert: Der Dateiname bleibt, der Inhalt ändert sich
([Wie ein Release den Paketserver erreicht](wie-ein-release-den-paketserver-erreicht.md)). Um diesen Korrekturen zu folgen:

- beim Git-Spiegel `git pull` im Klon, dann das Umschreiben der Adressen in `index.xml` erneut ausführen;
- beim Download-Spiegel den Download erneut in einen neuen Ordner ausführen, Prüfsummen vergleichen und die Ordner tauschen.

Ob ein Spiegel jeder Korrektur folgen oder auf einer von Ihnen getesteten Version bleiben soll, entscheiden Sie; ein Hoster, der viele
Kunden-Websites installiert, testet meist zuerst.

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Der Paketserver](README.md)
- [Wenn der Paketserver nicht erreichbar ist](wenn-der-paketserver-nicht-erreichbar-ist.md)
- [Einen eigenen Paketserver betreiben](../eigene/einen-eigenen-paketserver-betreiben.md)
- [Das lokale Paket-Repository](../grundlagen/das-lokale-paket-repository.md)
- [Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
