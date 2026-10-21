---
title: Einen eigenen Paketserver betreiben
audience: developer, hosting, partner
category: install-upgrade
order: 93
summary: Ein Paketserver ist ein statischer HTTPS-Ordner mit index.xml, den Archiven .ezpkg und ihren Vorschaubildern. Diese Seite gibt den Ordneraufbau, eine vollständige index.xml, wie Sie ihn wie 7x auf GitHub Pages oder auf einem beliebigen Webserver hosten, die Grenzen, die ezupdate anwendet, und wie Sie Pakete von 7x als Abhängigkeiten wiederverwenden.
keywords: Paketserver, eigener Paketserver, index.xml, ezpkg, Vorschaubilder, GitHub Pages, statische Website, HTTPS, Spiegel, RemotePackagesIndexURL, RemotePackagesIndexURLBase, thumbnail_url, Abhängigkeiten, package server, own package server, thumbnails, static site, mirror, dependencies
type: help
---
# Einen eigenen Paketserver betreiben

Diese Seite ist für Entwickler, Agenturen und Hoster, die wollen, dass Installationen Seitenpakete von einem eigenen Server herunterladen: für
Pakete, die noch nicht öffentlich sind, für eine für einen Kunden getestete Sammlung oder als Spiegel. Nichts auf einem Paketserver führt
Code aus; er ist ein Ordner mit Dateien, die über HTTPS ausgeliefert werden.

7x und die Exponential Foundation ermutigen Sie ausdrücklich, Ihr Seitenpaket außerdem per Pull Request an
se7enxweb/exponential.packages.exponential.earth einzureichen, den Server, den jede Installation standardmäßig liest: siehe
[Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Inhalt

1. [Was ein Server beantworten muss](#was-ein-server-beantworten-muss)
2. [Der Ordneraufbau](#der-ordneraufbau)
3. [Eine vollständige index.xml](#eine-vollständige-indexxml)
4. [Auf GitHub Pages hosten](#auf-github-pages-hosten)
5. [Auf einem beliebigen Webserver hosten](#auf-einem-beliebigen-webserver-hosten)
6. [Grenzen und Prüfungen der Clients](#grenzen-und-prüfungen-der-clients)
7. [Checkliste](#checkliste)

## Was ein Server beantworten muss

Die Clients lesen eine Datei, `<index URL>/index.xml`, und dann jedes Archiv unter der `url`, die der Index angibt:

| Client | Liest | Code |
|---|---|---|
| Setup-Assistent, Kickstarter, `exp:install` | `package.ini [RepositorySettings] RemotePackagesIndexURL` + `/index.xml` | `kernel/setup/steps/ezstep_site_types.php` |
| ezupdate (**Package servers**, `fetch`) | die URL des Servers + `/index.xml` | `extension/ezupdate/classes/ezupdatepackageservers.php` |

Der Server von 7x antwortet unter `https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml`. Mehr auf
[Was der Paketserver ist](../paketserver/was-der-paketserver-ist.md).

## Der Ordneraufbau

Spiegeln Sie den Aufbau von 7x, damit `RemotePackagesIndexURLBase` auch für Ihren Server funktioniert (der Kernel hängt
`/<major.minor>/<version>` an, wenn `RemotePackagesIndexURL` leer ist):

```text
https://packages.example.com/exponential/
└── 6.0/
    └── 6.0.15/
        ├── index.xml
        ├── acme_site.ezpkg
        ├── acme_site_democontent.ezpkg
        └── thumbnails/
            └── acme_site.png
```

Ein Ordner pro Release von Exponential hält alte Installationen am Laufen: Eine Installation von 6.0.14 liest weiter ihren Ordner, während Sie
für das nächste Release veröffentlichen.

## Eine vollständige index.xml

```xml
<?xml version="1.0"?>
<packages vendor="acme">
  <package name="acme_site_democontent" type="contentobject"
           url="https://packages.example.com/exponential/6.0/6.0.15/acme_site_democontent.ezpkg"/>
  <package name="sevenx_classes" type="contentclass"
           url="https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/sevenx_classes.ezpkg"/>
  <package name="acme_site" version="1.0.0-1" type="site"
           summary="Acme starter site"
           description="What an editor gets after installing it."
           url="https://packages.example.com/exponential/6.0/6.0.15/acme_site.ezpkg"
           thumbnail_url="https://packages.example.com/exponential/6.0/6.0.15/thumbnails/acme_site.png"/>
</packages>
```

- Das Wurzelelement muss `<packages>` sein; sonst meldet der Assistent `Malformed index file.`
- Jedes `<package>` braucht `name` und `url`; Seitenpakete außerdem `type="site"` (der Assistent führt nur diese auf), eine `summary`, eine
  `version` und eine `thumbnail_url`. Der Index von 7x nutzt dieselben Attribute.
- **Jedes Paket, das ein Seitenpaket verlangt, muss im selben Index stehen.** Der Assistent schlägt ein verlangtes Paket in dem Index nach,
  den er gelesen hat, nicht auf anderen Servern. Seine `url` darf überallhin zeigen, auch auf den Server von 7x, wie `sevenx_classes` oben.

Das Format vollständig: [Das Format index.xml](../paketserver/das-format-index-xml.md).

## Auf GitHub Pages hosten

Der Server von 7x ist das Repository
[se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth), von GitHub Pages
veröffentlicht:

| Datei | Rolle |
|---|---|
| `exponential/6.0/<version>/` | die Release-Ordner |
| `CNAME` | die eigene Domain, auf die die Pages-Website antwortet |
| `.github/workflows/workflow.yml` | bei jedem Push: checkt den Branch `master` aus, erzeugt Verzeichnisauflistungen mit `jayanta525/github-pages-directory-listing`, lädt den Baum hoch und rollt ihn mit `actions/deploy-pages` aus; ein Ausrollen nach dem anderen (`concurrency: pages`) |

Um dasselbe zu tun: Legen Sie ein Repository an, fügen Sie die Ordner und eine Kopie des Workflows hinzu (ändern Sie `ref:` auf Ihren
Standard-Branch), schalten Sie GitHub Pages für das Repository ein und fügen Sie wahlweise eine `CNAME` mit Ihrer Domain hinzu. Details auf
[Der Paketserver auf GitHub](../paketserver/der-paketserver-auf-github.md).

> **Hinweis**
> Jedes Ausrollen veröffentlicht den ganzen Baum zum Stand seines Commits, und wie `settings/package.ini` es sagt, antwortet ein Paketserver,
> der neu veröffentlicht wird, einige Minuten lang mit Fehlern. Der Setup-Assistent wiederholt es deshalb (`DownloadAttempts`,
> `DownloadRetryDelays[]`); siehe [Wenn der Paketserver nicht erreichbar ist](../paketserver/wenn-der-paketserver-nicht-erreichbar-ist.md).

## Auf einem beliebigen Webserver hosten

Jeder Server, der statische Dateien über HTTPS ausliefert, funktioniert:

```bash
rsync -av exponential/ deploy@packages.example.com:/srv/packages/exponential/
curl -sI https://packages.example.com/exponential/6.0/6.0.15/index.xml | head -1
```

Liefern Sie den Ordner nur lesend aus, ohne Skripte, und halten Sie die Dateien in git, damit sich jeder veröffentlichte Stand nachvollziehen
lässt.

## Grenzen und Prüfungen der Clients

| Prüfung | Setup-Assistent | ezupdate |
|---|---|---|
| Schema | jede URL, die PHP abrufen kann | nur `https://`, auch für Umleitungen |
| HTTP-Status | eine andere Antwort als 200 lässt den Versuch scheitern | ein Fehlerstatus scheitert |
| Archiv | muss mit der gzip-Signatur beginnen, darf nicht leer oder kürzer als angekündigt sein | höchstens 256 MiB |
| Index | als XML gelesen, Wurzel `<packages>` | höchstens 1 MiB, Wurzel `<packages>` |
| Wiederholungen | `DownloadAttempts` (3) mit `DownloadRetryDelays[]` (2, 5, 10 s) | keine |
| Timeouts | `DownloadTimeout` 300 s, `DownloadConnectTimeout` 30 s | 300 s, Verbindung 15 s |

Nutzen Sie HTTPS für jede URL in Ihrem Index, damit beide Clients sie annehmen.

## Checkliste

- [ ] `curl -sI` auf `index.xml`, jedes Archiv und jedes Vorschaubild antwortet mit `200`.
- [ ] `xmllint --noout index.xml` besteht.
- [ ] Jedes Archiv besteht `php ezpm.php validate` ([Mit build.sh neu bauen](mit-build-sh-neu-bauen.md)).
- [ ] Jedes `<require>` jedes Seitenpakets hat einen Eintrag im Index.
- [ ] Die Werte `version` im Index sind gleich dem `<number>-<release>` der Archive.
- [ ] Ein Probelauf gegen den Server gelingt ([In einer Sandbox testen](in-einer-sandbox-testen.md)).

## Verwandte Seiten

- [Ein eigenes Seitenpaket erstellen](README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Eine Website auf Ihren Paketserver richten](eine-website-auf-ihren-paketserver-richten.md)
- [Offline-Installationen und Spiegel](../paketserver/offline-installationen-und-spiegel.md)
- [Einstellungen des Paketservers](../paketserver/einstellungen-des-paketservers.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
