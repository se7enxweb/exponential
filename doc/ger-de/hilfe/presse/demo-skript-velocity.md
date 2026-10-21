---
title: "Demo-Skript: Velocity"
audience: press
category: press-media
order: 159
summary: Ein achtminütiges Videoskript für Exponential Velocity, den Anwendungsserver mit dauerhaften Workern - das Engine-Paket außerhalb der Kamera installieren, ihn starten und prüfen, die Cache-Header lesen, das eingebaute Dashboard ansehen und über Geschwindigkeit nur mit den dokumentierten Zahlen und ihrer Methode sprechen. Was Sie nie zeigen und was Sie nicht behaupten sollten.
keywords: Demo-Skript, Velocity, Exponential Velocity, Anwendungsserver, dauerhafte Worker, exp:velocity, qbix, FrankenPHP, Antwort-Cache, X-Cache, Dashboard, Benchmark, exp:benchmark, Geschwindigkeit, Video, Creator, demo script, application server, persistent workers, response cache, dashboard, benchmark, speed
type: help
---
# Demo-Skript: Velocity

Diese Seite ist für Creator, die **Exponential Velocity** zeigen, den Anwendungsserver, der "boots once and keeps a pool
of workers alive" ([Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)). Geschwindigkeit ist das Thema,
bei dem Videos am häufigsten mehr sagen als die Quellen. Dieses Skript zeigt, wie Velocity funktioniert, und zitiert nur
die dokumentierten Zahlen, mit ihrer Methode. Es dauert etwa acht Minuten, in einem Terminal und einem Browser.

Velocity kam mit der Linie 6.0.15 (`./console exp:velocity`), die in Entwicklung ist; das Engine-Paket hat eigene
Versionsnummern (Exponential Velocity 0.0.4.x). Nennen Sie beide vor der Kamera.

## Inhalt

1. [Außerhalb der Kamera: installieren und vorbereiten](#außerhalb-der-kamera-installieren-und-vorbereiten)
2. [Das Skript](#das-skript)
3. [Über Geschwindigkeit sprechen](#über-geschwindigkeit-sprechen)
4. [Was Sie nie zeigen](#was-sie-nie-zeigen)
5. [Sich vor der Kamera fangen](#sich-vor-der-kamera-fangen)

## Außerhalb der Kamera: installieren und vorbereiten

Die Engine wird von der `composer.json` von Exponential verlangt (in `~0.0.4.54`), daher bringt `composer install` sie
mit; sie braucht PHP 8.0 oder später und die Erweiterung `sockets` ([Die Website ausliefern, 8.3.1](../../installation/08-die-website-ausliefern.md#831-das-engine-paket-installieren)):

```bash
composer install                                                   # brings the engine the kernel's composer.json requires
php bin/php/console exp:velocity ext check                         # missing PHP extensions, with the install command
php bin/php/console exp:velocity config set ServerSettings Engine qbix
```

Lesen Sie die Version aus dem Block `suggest` der `composer.json` des Kernels ab: Am 19. Oktober 2026 war es
`~0.0.4.52`, während das Installationshandbuch noch `~0.0.4.44` zeigt. Die Tilde akzeptiert jedes spätere 0.0.4.x.

Die ausgelieferte Standard-Engine ist `php` (der eingebaute Server von PHP, nur für die Entwicklung); `qbix` ist die
empfohlene Engine und FrankenPHP die produktionsreife Alternative. Ausgelieferte Ports: `qbix` 8088 (HTTP) und 8080
(HTTPS), `frankenphp` 8089 und 8444, `php` 8087. Starten Sie ihn einmal außerhalb der Kamera und setzen Sie das Passwort
des Control Panels über die Befehlszeile, damit das Passwort nie auf dem Bildschirm erscheint
([Velocity Control Panel](../../funktionen/6.0/velocity-control-panel.md#erste-anmeldung)).

## Das Skript

| Zeit | Auf dem Bildschirm | Tun | Sagen Sie |
|---|---|---|---|
| 0:00 | Titelkarte | | Offenlegung. „Exponential Velocity, der Anwendungsserver für Exponential, auf meinem eigenen Testrechner.“ |
| 0:20 | Terminal | | „Klassisches PHP-Hosting startet die Anwendung für jede Anfrage und baut sie danach wieder ab. Velocity lädt sie einmal und hält Worker zwischen den Anfragen am Leben.“ |
| 1:00 | Terminal | `./console exp:velocity start` | „Ein Befehl startet den Server und zeigt seinen Status.“ |
| 1:30 | Terminal | `./console exp:velocity status` | „Er listet jede Adresse auf, die man öffnen kann, die HTTPS-Adresse neben der unverschlüsselten, und ob die Caches gemeinsamen Speicher bekommen.“ |
| 2:15 | Browser | Die HTTPS-Adresse der Website öffnen | „HTTPS vom ersten Start an: Für mich wird ein selbstsigniertes Zertifikat erzeugt; meine eigenen Zertifikate werden verwendet, wenn ich sie angebe.“ Akzeptieren Sie die Browserwarnung möglichst außerhalb der Kamera |
| 3:00 | Entwicklerwerkzeuge des Browsers, Netzwerk | Die Startseite zweimal neu laden | „Die erste Antwort wird gerendert; die zweite hat `X-Cache: HIT`: beantwortet aus dem Antwort-Cache des Servers.“ ([Antwort-Cache von Velocity](../../funktionen/6.0/velocity-antwort-cache.md#ablesen-header-und-zustand)) |
| 4:00 | Browser | `/Q/dashboard` öffnen (im Panel angemeldet) | „Das eigene Dashboard des Servers: laufende Anfragen, Worker, Speicher, Statuscodes, häufigste Pfade, ohne Neuladen.“ |
| 5:00 | Terminal | `./console exp:velocity graceful` | „Die Worker laden einer nach dem anderen neu; der Port antwortet weiter.“ |
| 5:45 | Terminal | `./console exp:velocity cache clear` | „Nach einer Änderung an einem Template oder Stylesheet leert dies jeden Cache.“ |
| 6:30 | Titelkarte | | Geschwindigkeit, wie unten. Dann: „Die Befehle und die Methode sind unten verlinkt.“ |

`./console` ist eine Verknüpfung, die Sie mit `ln -s bin/php/console console` anlegen; ohne sie tippen Sie
`php bin/php/console`.

## Über Geschwindigkeit sprechen

Sagen Sie nur, was die README dokumentiert, und sagen Sie, wie gemessen wurde:

```text
„Die README dokumentiert 0,36 Millisekunden Serverarbeit pro Seite aus dem Cache, gemessen als Median über
Hunderte von Anfragen, und dauerhaft 8.941 Anfragen pro Sekunde auf einer Maschine mit 12 Kernen, die eine
echte Seite ausliefert, ohne Fehler bis zu 384 Verbindungen. Sie sagt, dass sich das mit Skripten nachvollziehen
lässt, die mit dem Projekt ausgeliefert werden. Der größte Teil der Geschwindigkeit kam von Korrekturen an
Exponential und seinen Caches, was überall hilft, wo es läuft.“
```

- Um selbst zu messen: `./console exp:benchmark --allow-root-user`, oder zwei Server direkt nacheinander mit
  `--compare=<erste Adresse>,<zweite Adresse>` ([Benchmarking](../../anleitungen/benchmarking.md)).
- Der Leitfaden warnt: "One run on a busy machine proves little." Eine Messung während einer Aufnahme auf einem Laptop
  ist eine Vorführung, kein Ergebnis. Sagen Sie das vor der Kamera, wenn Sie eine zeigen.
- Machen Sie aus den Zahlen nie „N-mal schneller als <ein anderes Produkt>“: Kein veröffentlichter Vergleich stützt das
  ([Faire Vergleiche](faire-vergleiche.md) erklärt, warum).

## Was Sie nie zeigen

| Nie auf dem Bildschirm | Warum |
|---|---|
| Das Passwort des Control Panels, den dokumentierten Standardschlüssel, die Ausgabe von `qbixctl panel:password` | Damit meldet man sich am Server an |
| Eine Dashboard-Adresse mit `?token=...` | Das Token öffnet das Dashboard |
| Private Schlüssel von Zertifikaten, Inhalte von `var/vc/`, Dateien in `/etc/vc/` oder `/etc/qbix/` | Schlüssel und Konfiguration |
| `settings/override/` | Zugangsdaten zur Datenbank |
| Öffentliche IP-Adressen oder Hostnamen eines echten Servers | Verwenden Sie `localhost` auf einem Demo-Rechner |

## Sich vor der Kamera fangen

| Was passiert | Was zu tun ist |
|---|---|
| Der Start verweigert: keine Prozessisolation oder kein `openssl` | Führen Sie `exp:velocity ext check` außerhalb der Kamera aus; es gibt den Installationsbefehl aus |
| Eine Änderung erscheint nicht | Worker halten die Anwendung im Speicher: `exp:velocity restart`, oder `deploy` bei einer PHP-Änderung |
| `./console exp:vc` meldet, der Befehl sei nicht gefunden | Verwenden Sie `exp:velocity`; die Dokumentation hält fest, dass der Alias auf diesem Stand nicht gefunden wird |
| Das Dashboard antwortet mit 403 | Melden Sie sich zuerst im Panel an, außerhalb der Kamera |

## Verwandte Seiten

- [Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)
- [Gesprächspunkte: Geschwindigkeit und Velocity](../fuersprecher/gespraechspunkte-geschwindigkeit-und-velocity.md)
- [Benchmarking](../../anleitungen/benchmarking.md)
- [Demo-Skript: Installation von null](demo-skript-installation-von-null.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
