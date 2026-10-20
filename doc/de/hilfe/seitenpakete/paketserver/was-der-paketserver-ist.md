---
title: Was der Paketserver ist
audience: admin, developer, hosting
category: install-upgrade
order: 31
summary: Die Aufgabe des Paketservers in einer Exponential-Installation, was er ist und was nicht, welche Teile von Exponential von ihm lesen und welche Anfragen sie genau stellen.
keywords: Paketserver, exponential.packages.exponential.earth, statische Website, index.xml, ezpkg, Setup-Assistent, Kickstarter, exp:install, ezupdate, HTTPS, GitHub Pages, package server, static site, setup wizard
type: help
---
# Was der Paketserver ist

Diese Seite ist für alle, die Exponential installieren und wissen wollen, womit der Installer im Internet spricht. Sie beantwortet: was der
Paketserver tut, was er nicht tut, welche Teile von Exponential ihn nutzen und welche Anfragen sie senden.

## Inhalt

1. [Seine Aufgabe](#seine-aufgabe)
2. [Was er nicht ist](#was-er-nicht-ist)
3. [Wer von ihm liest](#wer-von-ihm-liest)
4. [Die Anfragen, Schritt für Schritt](#die-anfragen-schritt-für-schritt)
5. [Wie er antwortet](#wie-er-antwortet)
6. [Warum eine statische Website](#warum-eine-statische-website)
7. [Verwandte Seiten](#verwandte-seiten)

## Seine Aufgabe

Der Paketserver veröffentlicht die **Seiteninstallationspakete** jedes Release von Exponential: die Archive `.ezpkg`, aus denen eine neue
Installation gebaut wird, und eine `index.xml` pro Release, die sie aufführt. Der Standardserver ist
<https://exponential.packages.exponential.earth/>.

Die Beschreibung seines Repositorys auf GitHub sagt seine Rolle deutlich: Der Server gehört zum Konfigurationsablauf der Installation mit dem
Setup-Assistenten von Exponential 6, der nur einmal genutzt wird, um die Software wie vorgesehen zu installieren; außerhalb des
Setup-Assistenten müssen Benutzer diesen Server nicht nutzen.

Eine Installation liest also vom Server, während sie installiert wird, und danach nur, wenn ein Administrator ein weiteres Paket anfordert
(zum Beispiel über die Paketserver von ezupdate).

## Was er nicht ist

| Er ist nicht ... | Weil ... |
|---|---|
| eine Anwendung | er eine statische HTTPS-Website ist, die GitHub Pages veröffentlicht; nichts darauf führt Code aus |
| ein Kontensystem | es keine Anmeldung, keinen API-Schlüssel und kein Formular zum Hochladen gibt; alle bekommen dieselben Dateien |
| ein Composer-Repository | Composer-Pakete (Erweiterungen, der Kernel) von Packagist kommen; der Paketserver enthält nur Dateien `.ezpkg` |
| ein Ort für Updates einer laufenden Website | eine installierte Website ihn nicht prüft; Code wird mit Git oder Composer aktualisiert ([Mit Git aktualisieren](../../../../en/us/help/update-with-git.md)) |
| die einzige Quelle für Pakete | der Installer auch Pakete nutzt, die bereits in `var/storage/packages/` liegen, und eine hochgeladene `.ezpkg` annimmt |

## Wer von ihm liest

| Teil von Exponential | Wann | Code |
|---|---|---|
| Der Setup-Assistent, Schritt **Site Paket** | wenn der Schritt gezeigt wird und wenn ein Paket gewählt wird | `kernel/setup/steps/ezstep_site_types.php` |
| Der Kickstarter (`exp:kickstarter run`) | im Schritt `SiteTypes`, für `[site_types] Site_package` aus `kickstart.ini` | dieselbe Schrittklasse, im Kickstart-Modus |
| Die Installation über die Konsole (`exp:install --package=<name>`) | derselbe Schritt, von der Kommandozeile gesteuert | dieselbe Schrittklasse |
| ezupdate, **Package servers** | wenn ein Administrator die Pakete eines Servers auflistet oder eines abruft | `extension/ezupdate/classes/ezupdatepackageservers.php` (in der Erweiterung ezupdate) |

Alle nutzen dieselbe Adresse, `settings/package.ini [RepositorySettings] RemotePackagesIndexURL`
([Einstellungen des Paketservers](einstellungen-des-paketservers.md)). ezupdate führt diesen Server als ersten auf, nur lesbar, neben Servern, die
Sie selbst hinzufügen.

## Die Anfragen, Schritt für Schritt

Für den Setup-Assistenten tut `eZStepSiteTypes` Folgendes:

1. Er baut die Adresse des Index: `RemotePackagesIndexURL` plus `/index.xml`. Für 6.0.15:

   ```text
   https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml
   ```

2. Er lädt diese Datei in das Cache-Verzeichnis herunter (`<cache>/packages/index.xml`), liest sie und behält jedes Element `<package>`. Für
   die Liste auf dem Bildschirm behält er nur die mit `type="site"`.
3. Wenn Sie ein Seitenpaket wählen, das noch nicht importiert ist, lädt er die `url` des Pakets herunter und importiert es nach
   `var/storage/packages/7x/<name>/`.
4. Er liest `<dependencies><requires>` des importierten Pakets und schlägt für jedes verlangte Paket, das fehlt oder älter als seine
   `min-version` ist, dessen `url` im selben Index nach und lädt es ebenfalls herunter.

Jeder Download ist ein einfaches `GET`. Mit der PHP-Erweiterung curl wird `site.ini [ProxySettings] ProxyServer` (und `User`, `Password`)
genutzt, wenn gesetzt; Umleitungen werden verfolgt (höchstens fünf). Siehe [das Format index.xml](das-format-index-xml.md) für die Datei und
[Wie der Setup-Assistent Pakete nutzt](../grundlagen/wie-der-setup-assistent-pakete-nutzt.md) für den ganzen Schritt.

## Wie er antwortet

Auf dem laufenden Server geprüft:

| Anfrage | Antwort |
|---|---|
| `GET /exponential/6.0/6.0.15/index.xml` | `200`, `Content-Type: application/xml` |
| `GET /exponential/6.0/6.0.15/sevenx_multisite.ezpkg` | `200`, `Content-Type: application/octet-stream`, mit `Content-Length`, `Last-Modified` und `ETag` |
| jede dieser Dateien | `Cache-Control: max-age=600` |
| ein Ordner, etwa `/exponential/6.0/6.0.15/` | `200`, eine erzeugte Seite der Verzeichnisauflistung ([Den Paketserver im Browser durchsuchen](den-paketserver-im-browser-durchsuchen.md)) |
| `http://` statt `https://` | `301` auf die HTTPS-Adresse |
| ein Release-Ordner, der nicht existiert | `404` |

> **Hinweis**
> `max-age=600` bedeutet, dass eine gerade neu veröffentlichte Datei bis zu zehn Minuten lang aus einem Cache ausgeliefert werden kann. Ein
> korrigiertes Paket kann also einige Minuten brauchen, bis es jeden Installer erreicht.

## Warum eine statische Website

- **Nichts, wo man einbrechen könnte.** Es gibt keinen Servercode, keine Datenbank und kein Hochladen; die Angriffsfläche sind die Dateien.
- **Jeder kann es prüfen.** Jede Datei auf dem Server liegt mit ihrer vollständigen Geschichte in einem öffentlichen Git-Repository
  ([Der Paketserver auf GitHub](der-paketserver-auf-github.md)).
- **Jeder kann es kopieren.** Ein Release-Ordner sind nur Dateien. Kopieren Sie ihn auf einen beliebigen Webserver oder auf die Festplatte
  und richten Sie `RemotePackagesIndexURL` auf die Kopie ([Offline-Installationen und Spiegel](offline-installationen-und-spiegel.md)).
- **Beiträge sind Pull Requests.** Ein neues Paket erreicht jede Installation, indem es in das Repository zusammengeführt wird
  ([Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)).

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Der Paketserver](README.md)
- [Was auf dem Paketserver liegt](was-auf-dem-paketserver-liegt.md)
- [Einstellungen des Paketservers](einstellungen-des-paketservers.md)
- [Was ein Seiteninstallationspaket ist](../grundlagen/was-ein-seiteninstallationspaket-ist.md)
- [Paketserver in ezupdate](../admin-oberflaeche/paketserver-in-ezupdate.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
