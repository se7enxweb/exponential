---
title: Wenn der Paketserver nicht erreichbar ist
audience: admin, hosting, developer
category: install-upgrade
order: 42
summary: Was der Setup-Assistent, der Kickstarter, exp:install und ezupdate tun, wenn ein Download vom Paketserver scheitert - die Wiederholungen, jede Fehlermeldung und jeder Grund, wo sie protokolliert werden - und eine Checkliste, was Sie versuchen, vom Abwarten eines Ausrollens bis zur Offline-Installation.
keywords: Paketserver, Download gescheitert, Fehler, Wiederholung, DownloadAttempts, Timeout, curl-Fehler, HTTP 404, abgeschnitten, kein gzip-komprimiertes Paket, Proxy, Firewall, setup.log, Remote Site Paketliste, Paket manuell hochladen, Fehlerbehebung, package server, download failed, error, retry, timeout, truncated, proxy, firewall, troubleshooting
type: help
---
# Wenn der Paketserver nicht erreichbar ist

Diese Seite ist für Administratoren, die während einer Installation einen Fehler beim Herunterladen sehen. Sie erklärt, wie der Installer es
erneut versucht, was jede Meldung bedeutet, wo Sie die Details finden und was zu tun ist. Das Verhalten ist das von `eZStepSiteTypes` in
`kernel/setup/steps/ezstep_site_types.php` (6.0.15).

## Inhalt

1. [Wie der Installer es erneut versucht](#wie-der-installer-es-erneut-versucht)
2. [Die Meldungen](#die-meldungen)
3. [Die Gründe](#die-gründe)
4. [Wo Sie nachsehen](#wo-sie-nachsehen)
5. [Was zu tun ist](#was-zu-tun-ist)
6. [In ezupdate](#in-ezupdate)
7. [Verwandte Seiten](#verwandte-seiten)

## Wie der Installer es erneut versucht

Jeder Download des Setups, der Index ebenso wie jedes Paket, wird bis zu `DownloadAttempts` Mal versucht (Standard 3), mit
`DownloadRetryDelays[]` Sekunden Wartezeit zwischen den Versuchen (Standard 2, dann 5). Ein Versuch darf `DownloadTimeout` Sekunden (300)
dauern und `DownloadConnectTimeout` Sekunden (30) auf die Verbindung warten. Ein gescheiterter Versuch hinterlässt keine Teildatei
([Einstellungen des Paketservers](einstellungen-des-paketservers.md#die-einstellungen-für-das-herunterladen)).

Die Wiederholungen gibt es, weil ein Paketserver, der gerade neu veröffentlicht wird, einige Minuten lang mit Fehlern antwortet
([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md)): Während GitHub Pages eine neue Version der Website ausrollt, kann eine Anfrage
scheitern ([Der Paketserver auf GitHub](der-paketserver-auf-github.md#vom-push-zur-website)).

Nicht wiederholt wird ein Download, dessen Datei sich lokal nicht schreiben lässt („Kann nicht in Datei schreiben“), weil Warten nicht hilft.

## Die Meldungen

| Meldung im Schritt **Site Paket** oder auf der Kommandozeile | Bedeutung |
|---|---|
| „Das Empfangen der Remote Site Paketliste ist fehlgeschlangen. Sie können die Pakete manuell hochladen.“ | Der Index ließ sich nicht herunterladen, und es gibt keine `var/storage/packages/7x/index.xml`. Importierte Seitenpakete werden weiterhin aufgeführt |
| „Der Download des Pakets '*name*' ist nach *N* Versuchen fehlgeschlagen: *Grund*. Sie können das Paket manuell hochladen.“ | Jeder Versuch, ein Paket herunterzuladen, ist gescheitert; der Grund ist der des letzten Versuchs |
| Der Download des Pakets '*name*' ist gescheitert, mit Grund, und Sie können das Paket manuell hochladen | Der Download ist einmal gescheitert und wurde nicht wiederholt |
| „Der Download des Pakets '*name*' ist gescheitert. Sie können das Paket manuell uploaden.“ | Ein verlangtes Paket ist nicht importiert, und der Index hat keinen Eintrag dafür (oder es ließ sich kein Index lesen) |
| „Ungültiges Paket“ | Die Datei kam an, aber der Import scheiterte: kein Paket, ein ungültiger Name oder ein Archiv, das der Import abweist |

Bei einem Fehler zeigt der Schritt auch „URL des entfernten Repositorys:“ mit der genutzten Adresse, dem Wert von `RemotePackagesIndexURL`.
Prüfen Sie diese zuerst.

## Die Gründe

Der *Grund* in der Meldung sagt Ihnen, wo es gescheitert ist:

| Grund | Was er bedeutet | Wahrscheinliche Ursache |
|---|---|---|
| `curl error 6: Could not resolve host ...` | der Hostname wurde nicht gefunden | kein DNS auf dem Server oder ein Tippfehler in `RemotePackagesIndexURL` |
| `curl error 7: ...`, `curl error 28: ...` | keine Verbindung oder eine Zeitüberschreitung | ausgehendes HTTPS durch eine Firewall blockiert, ein Proxy nötig oder das Netzwerk langsam |
| `cannot resolve the host '...'` | dasselbe, ohne die Erweiterung curl | wie oben |
| `HTTP 404` | die Datei ist nicht da | ein falscher Release-Ordner, ein Paketname, der nicht im Index steht, oder ein laufendes Ausrollen |
| `HTTP 403`, `HTTP 5xx` | der Server hat abgelehnt oder ist gescheitert | ein Proxy, der ablehnt, oder der Server wird neu veröffentlicht |
| `the server sent an empty file` | `200` ohne Inhalt | ein abfangender Proxy oder ein laufendes Ausrollen |
| `the file is truncated, N bytes expected` | die Übertragung hielt zu früh an | eine instabile Verbindung oder ein für ein großes Paket zu kurzes `DownloadTimeout` |
| `the file is not a gzip-compressed package` | eine `.ezpkg`, die keine gzip-Daten ist | ein Proxy oder ein Captive Portal hat mit einer Webseite geantwortet |

Jeder Grund endet mit der Anzahl empfangener Bytes, zum Beispiel `HTTP 404, 9379 bytes received`.

## Wo Sie nachsehen

| Wo | Was Sie finden |
|---|---|
| `var/log/setup.log` | eine Warnung pro gescheitertem Versuch: `Download of <url>, attempt <n> of <N> failed: <reason>; trying again in <s> s` |
| die Debug-Ausgabe, `var/log/warning.log` | dieselben Warnungen und `Download of package '<name>' from '<url>' failed after <N> attempt(s): <reason>` |
| `var/log/kickstart.log` | der Lauf des Kickstarters ([Der Kickstarter, 6.9](../../../../en/us/install/06-kickstarter.md#69-logs)) |

Testen Sie vom Server selbst aus, was der Installer sehen würde:

```bash
# the index of the release this installation uses
curl -sS -o /dev/null -w '%{http_code} %{size_download} bytes\n' \
  https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml

# through the proxy of site.ini [ProxySettings], if you have one
curl -sS -x http://proxy.example.com:3128 -o /dev/null -w '%{http_code}\n' \
  https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml
```

Führen Sie es als der Benutzer aus, der PHP ausführt. `200` bedeutet, dass der Server von dort aus erreichbar ist; sehen Sie dann die
Adresse des Pakets an, die die Meldung nennt.

## Was zu tun ist

Arbeiten Sie die Liste von oben nach unten ab:

- [ ] **Lesen Sie die Adresse.** Ist „URL des entfernten Repositorys:“ der Release-Ordner, den Sie erwarten? Ein Tippfehler, ein Spiegel mit
      `http://`, den es nicht mehr gibt, oder eine geleerte `RemotePackagesIndexURL` (deren Rückfall einen Ordnernamen baut, den es nicht
      gibt, siehe [Einstellungen des Paketservers](einstellungen-des-paketservers.md#remotepackagesindexurlbase)) enden alle in Fehlern.
- [ ] **Öffnen Sie die Adresse in einem Browser** oder mit `curl` vom Server aus, wie oben.
- [ ] **Warten Sie einige Minuten und versuchen Sie es erneut**, wenn der Server für eine Datei, die in der
      [Auflistung](den-paketserver-im-browser-durchsuchen.md) existiert, mit `404` oder einem Fehler geantwortet hat: Ein Ausrollen läuft
      vielleicht. Seinen Fortschritt sehen Sie unter [Actions](https://github.com/se7enxweb/exponential.packages.exponential.earth/actions)
      des Repositorys.
- [ ] **Setzen Sie den Proxy** in `site.ini [ProxySettings]` (`ProxyServer` und, falls nötig, `User`, `Password`), wenn der Server das
      Internet nur über einen erreicht. Die Downloads des Setups über curl nutzen ihn.
- [ ] **Erlauben Sie ausgehendes HTTPS** zu `exponential.packages.exponential.earth` in der Firewall, wenn Ihr Hosting ausgehende
      Verbindungen beschränkt.
- [ ] **Erhöhen Sie die Grenzen** für eine langsame Verbindung: `DownloadTimeout`, `DownloadAttempts`
      ([Die Einstellungen überschreiben](einstellungen-des-paketservers.md#die-einstellungen-überschreiben)).
- [ ] **Laden Sie das Paket von Hand hoch**: Laden Sie es anderswo herunter und nutzen Sie dann **Paket hochladen** im Schritt
      **Site Paket** oder `php ezpm.php import` ([Ein Paket von Hand herunterladen](ein-paket-von-hand-herunterladen.md)).
- [ ] **Installieren Sie offline**: Importieren Sie zuerst jedes Paket ([Offline-Installationen und Spiegel](offline-installationen-und-spiegel.md)).

Für die Installation über die Konsole ist ein Halt beim Abrufen von Paketen in
[Die Installation über die Konsole, 7.11](../../../../en/us/install/07-console-install.md#711-what-can-go-wrong) und
[Fehlerbehebung, 12.8](../../../../en/us/install/12-troubleshooting.md#128-the-kickstarter-and-the-console-install) behandelt.

> **Tipp**
> Führen Sie auf einem Server, auf dem Sie oft installieren, zuerst `exp:install --dry-run` aus. Es lädt das Seitenpaket und seine
> Anforderungen in ein temporäres Repository herunter und hält an, bevor irgendetwas geschrieben wird, sodass ein Problem beim Herunterladen
> sichtbar wird, bevor die Installation beginnt
> ([Die Installation über die Konsole, 7.6](../../../../en/us/install/07-console-install.md#76-dry-run-and-print)).

## In ezupdate

Die Seite **Package servers** von ezupdate nutzt ihren eigenen Download (`extension/ezupdate/classes/ezupdatepackageservers.php`): nur HTTPS,
eine Grenze von 15 Sekunden für die Verbindung, 300 Sekunden pro Übertragung, den Proxy aus `site.ini [ProxySettings] ProxyServer` und keine
Wiederholungen. Ihre Meldungen:

| Meldung | Bedeutung |
|---|---|
| `Could not fetch <url>: <reason>` | die Übertragung ist gescheitert; der Grund kommt von curl |
| `The server did not answer with a package index.` | die Antwort war kein XML mit einer Wurzel `<packages>` |
| `The PHP curl extension is needed to reach a server.` | PHP hat kein curl |
| `The address must be an https:// URL.` / `The package address must be an https:// URL.` | eine Adresse eines Servers oder Pakets mit einem anderen Schema |

Siehe [Paketserver in ezupdate](../admin-oberflaeche/paketserver-in-ezupdate.md).

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Der Paketserver](README.md)
- [Einstellungen des Paketservers](einstellungen-des-paketservers.md)
- [Offline-Installationen und Spiegel](offline-installationen-und-spiegel.md)
- [Der Setup-Assistent, 5.3.8 Seitenpaket](../../../../en/us/install/05-setup-wizard.md#538-site-package)
- [Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
