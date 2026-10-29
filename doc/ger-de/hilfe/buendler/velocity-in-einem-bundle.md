---
title: Velocity in einem Bundle
audience: bundler, hosting
category: bundling
order: 270
summary: Wie ein Bundle Exponential mit Velocity ausliefert, dem Web- und Anwendungsserver, der seit 6.0.15 Teil von Exponential ist - woher er kommt (Composer, deb und rpm, Docker-Images, Binärdateien), die Engine-Einstellung, Benutzer und root, Start beim Booten, das Ausrollen einer Änderung und was Sie prüfen.
keywords: Velocity, Engine, Benutzer, root, Start beim Booten, Ausrollen, exponential-velocity, qbix, velocity.ini, ServerSettings, User, Group, AllowRootWorkers, systemd, exp:velocity start, status, deploy, ext check, deb, rpm, ghcr.io, bundle
type: help
---
# Velocity in einem Bundle

Diese Seite richtet sich an Bündler, die die gebündelte Website mit Exponential Velocity ausliefern. "Seit 6.0.15
**verlangt** die `composer.json` im Wurzelverzeichnis `se7enxweb/exponential-velocity ~0.0.4.57`", daher hat jede mit
Composer gebaute Installation es bereits
([Velocity-Engines](../../bc/6.0/velocity-engines.md#velocity-ist-seit-6015-teil-von-exponential)).

## Inhalt

1. [Woher Velocity kommt](#woher-velocity-kommt)
2. [Einstellungen, die ein Bundle setzt](#einstellungen-die-ein-bundle-setzt)
3. [Benutzer und root](#benutzer-und-root)
4. [Starten](#starten)
5. [Eine Änderung ausrollen](#eine-änderung-ausrollen)
6. [Prüfungen](#prüfungen)

## Woher Velocity kommt

| Form | Für | Quelle |
|---|---|---|
| Composer-Paket `se7enxweb/exponential-velocity` | jede Exponential-Installation | `composer.json` |
| deb- und rpm-Pakete | einen Systemdienst auf Debian 12 und 13, Ubuntu 22.04 und 24.04, Enterprise Linux 9 und 10 | [Velocity-Pakete](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md#betriebssystempakete) |
| Docker-Images unter `ghcr.io/se7enxweb/exponential-velocity` | Container, amd64 und arm64 | [Velocity-Pakete](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md#docker) |
| statische Binärdateien, das Phar, ein Quell-Kit | einzelne Maschinen, Laptops, Appliances, ungewöhnliche Plattformen | [Velocity-Pakete](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md#eine-form-wählen) |

"Eine Exponential-Installation hat die Engine bereits über Composer und braucht nichts davon"; die anderen Formen sind
für Velocity außerhalb einer Installation. "Alle Dateien eines Releases tragen Prüfsummen in `SHA256SUMS`."

## Einstellungen, die ein Bundle setzt

Die Einstellungen stehen in `settings/velocity.ini`; ein Bundle überschreibt sie in
`settings/override/velocity.ini.append.php` (ausgehend von
`doc/eng-us/examples/web-servers/velocity.ini.append.php.example`) oder mit
`exp:velocity config set <Block> <Setting> <value>`, das dieselbe Datei schreibt
([Velocity-Beispiel](../../beispiele/webserver/velocity.md)).

```ini
[ServerSettings]
Engine=qbix
```

"`Engine` ist die **Standard**-Engine ... Sie wird als `php` ausgeliefert ... jede Website, von der Entwicklung bis
zur Produktion, setzt `qbix`." Der ausgelieferte Port von `qbix` ist `8088` auf `127.0.0.1`, "nur lokal oder über
einen Reverse Proxy erreichbar"; ein Bundle, das auf 80 und 443 antwortet, ändert die Adressen wie im Beispiel
gezeigt.

## Benutzer und root

- `[ServerSettings] User` und `Group` nennen den Benutzer, unter dem die Worker laufen; "Velocity als root zu starten
  ist in Ordnung: Nur sein Elternprozess bleibt root, und root-Worker werden abgelehnt, sofern nicht
  `AllowRootWorkers=enabled` gesetzt ist (ausgeliefert `disabled`)"
  ([Beschreibbare Verzeichnisse und Rechte](../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md#einrichtung-1-ein-benutzer)).
- `exp:velocity` selbst lehnt `root` ab, "sofern nicht `--allow-root-user` angegeben ist"
  ([4.6](../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)).
- Wenn Velocity und Apache oder nginx mit PHP-FPM dieselbe Installation ausliefern, "teilen sie `var/`, die Caches und
  die Sitzungen: Geben Sie ihnen denselben Benutzer oder diese Gruppeneinrichtung".

## Starten

- **Auf einem Server:** eine systemd-Unit, die `php bin/php/console exp:velocity start --allow-root-user` ausführt.
  Das Beispiel `doc/eng-us/examples/web-servers/exponential-velocity.service` wird "nicht als Unit ausgeliefert";
  kopieren Sie es, ersetzen Sie die Pfade und die Datenbank-Unit und "kombinieren Sie es nicht mit der Unit des
  Betriebssystempakets für dieselben Ports".
- **In einem Container:** `exp:velocity start` "löst den Server ab und kehrt zurück, sobald er antwortet", daher
  wiederholt der Einstiegspunkt `exp:velocity status` in einer Schleife
  ([Container-Images](container-images.md#ein-einstiegspunkt)).

## Eine Änderung ausrollen

"Eine geänderte PHP-Klasse erreicht die Worker von Velocity erst nach einem Neustart; `deploy` erledigt das in der
richtigen Reihenfolge":

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user
php bin/php/console exp:velocity deploy --allow-root-user
php bin/php/console exp:velocity deploy --kernel --allow-root-user   # a kernel class was added or renamed
```

Quelle: [Velocity-Engines](../../bc/6.0/velocity-engines.md#eine-php-änderung-bereitstellen-expvelocity-deploy). In Containern
"ist die übliche Entsprechung ein neues Image und ein neuer Container".

## Prüfungen

```bash
php bin/php/console exp:velocity status
php bin/php/console exp:velocity ext check
```

`status` "meldet das Engine-Paket und seine Version"; `ext check` listet die PHP-Erweiterungen auf, die die
Installation braucht, und was fehlt. Velocity braucht `ext-sockets` (`pcntl` und `openssl` empfohlen).

## Verwandte Seiten

- [Webserver- und Engine-Wahl](webserver-und-engine-wahl.md)
- [Velocity für Hoster](../hosting/server/velocity-fuer-hoster.md), [Velocity startet nicht](../hosting/runbooks/velocity-startet-nicht.md)
- [Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
