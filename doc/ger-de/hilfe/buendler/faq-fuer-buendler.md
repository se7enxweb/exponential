---
title: FAQ für Bündler
audience: bundler, partner, hosting
category: bundling
order: 520
summary: Kurze Antworten auf die häufigsten Fragen von Bündlern - dürfen wir es verkaufen, müssen wir unseren Code veröffentlichen, welche Version und Datenbank, wie vermeiden wir Standardpasswörter, Docker-Dateien, Offline-Installationen, Updates, Marke, Support und das Partnerprogramm - jeweils mit der Seite, die es erklärt.
keywords: FAQ, Fragen, Bundle, verkaufen, Quellcode, Version, Datenbank, Passwort, offline, Update, Marke, Support, Partner, questions, sell, GPL, source code, Docker, password, update, brand, support, OEM, partner
type: help
---
# FAQ für Bündler

Diese Seite richtet sich an Bündler, die zuerst eine schnelle Antwort möchten. Jede Antwort verlinkt die Seite mit den
Einzelheiten. Antworten zur Lizenzierung sind keine Rechtsberatung.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../hosting/control-panels/plesk.md), [cPanel](../hosting/control-panels/cpanel.md), [DirectAdmin](../hosting/control-panels/directadmin.md), [ISPConfig](../hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

## Inhalt

1. [Lizenzierung](#lizenzierung)
2. [Bauen](#bauen)
3. [Installieren](#installieren)
4. [Nach dem Release](#nach-dem-release)
5. [Marke und Partnerschaft](#marke-und-partnerschaft)

## Lizenzierung

| Frage | Antwort |
|---|---|
| Dürfen wir ein Bundle mit Exponential darin verkaufen? | Ja. Sie dürfen für das Bundle, die Weitergabe und Dienstleistungen Geld verlangen; für die Exponential-Teile gilt die GPL ([GPL-Pflichten](gpl-pflichten-beim-weitergeben.md)). |
| Dürfen wir "Exponential-Lizenzen" verkaufen? | Eine solche Lizenz gibt es nicht; "Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos" ([Lizenzierung für Partner](../partner/lizenzierung-fuer-partner.md)). |
| Müssen wir unsere eigenen Erweiterungen veröffentlichen? | Wenn Sie sie mit Exponential weitergeben, klären Sie mit einem Rechtsbeistand, ob sie GPL-kompatibel sein müssen; Code, den Sie nicht weitergeben, ist nicht betroffen ([Lizenzierung für Partner](../partner/lizenzierung-fuer-partner.md#häufige-fragen)). |
| Was müssen wir mitliefern? | Lizenztexte, Hinweise, das Quellcode-Angebot, die Liste der Komponenten Dritter ([Lizenztexte und Hinweise](mitzuliefernde-lizenztexte-und-hinweise.md)). |
| Dürfen wir die Dokumentation beilegen? | Ja, unter der GFDL 1.3: Behalten Sie die Hinweise bei und legen Sie die Lizenz bei ([Mitzuliefernde Dokumentation](mitzuliefernde-dokumentation.md)). |

## Bauen

| Frage | Antwort |
|---|---|
| Gibt es ein offizielles Dockerfile? | Nein; das Repository enthält keines. Velocity veröffentlicht Images unter `ghcr.io/se7enxweb/exponential-velocity` ([Container-Images](container-images.md)). |
| Gibt es ein offizielles deb- oder rpm-Paket des CMS? | Nein; Velocity hat deb- und rpm-Pakete ([Pakete für Linux-Distributionen](pakete-fuer-linux-distributionen.md)). |
| Auf welcher Version bauen wir auf? | Auf einem veröffentlichten Release-Tag, gefunden mit einer Versionssortierung, nicht einer Namenssortierung ([Versionen, Tags und Pinning](versionen-tags-und-pinning.md)). |
| Warum gibt es keine `composer.lock`? | Die Lock-Datei "ist nicht Teil des Repositorys"; Ihr Bundle führt seine eigene ([Lock-Dateien](lock-dateien-und-reproduzierbare-builds.md)). |
| Welches PHP? | 8.0 bis 8.5 für die Linie 6.0.15; die veröffentlichten Tags `v6.0.8` bis `v6.0.14` brauchen 8.1 ([Den Code beschaffen](den-code-fuer-ein-bundle-beschaffen.md)). |
| Welche Datenbank? | Standardmäßig SQLite 3, danach PostgreSQL, MongoDB, MariaDB, Oracle, MySQL ([Datenbankwahl](datenbankwahl-fuer-bundles.md)). |

## Installieren

| Frage | Antwort |
|---|---|
| Wie installieren wir ohne Browser? | `php bin/php/console exp:install --apply` oder der Kickstarter ([Die drei Installer](die-drei-installer.md)). |
| Können wir eine installierte Website ausliefern? | Nicht für echte Websites: Installieren Sie beim ersten Start ([Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md)). |
| Wie lautet das Administratorpasswort? | Der Installer erzeugt es, sofern Sie kein zulässiges übergeben; es steht in `var/log/initial-admin-password` ([Standardzugangsdaten](standardzugangsdaten-und-erste-anmeldung.md)). |
| Können wir `publish` wie in alten Anleitungen verwenden? | Nur in einer öffentlichen Demo mit `EXP_DEMO_MODE=1` ([Demo-Modus in Bundles](demomodus-in-bundles.md)). |
| Lässt es sich offline installieren? | Ja, wenn zuvor jedes Paket importiert wurde ([Offline-Bundles](offline-bundles.md)). |
| Die Website zeigt beim ersten Start 503. | Der Wartungsmodus ist aktiv, solange der Installer arbeitet, und bleibt nach einem Fehler aktiv ([Die drei Installer](die-drei-installer.md#wartungsmodus-während-der-installation)). |
| Wie legen wir `.htaccess` und `config.php` an? | `./console exp:setup:htaccess --apply` und `./console exp:setup:config --apply` kopieren sie aus `.htaccess_root` und `config.php-RECOMMENDED`, mit dem Modus der Vorlage und, als root mit `--allow-root-user`, mit ihrem Eigentümer (`--owner=www-data:www-data` nennt einen anderen); ein zweiter Lauf ändert nichts ([.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)). |
| Wie ändern wir eine Einstellung in `config.php` ohne Editor? | `./console exp:config:set <NAME> <value> --apply` (vorher Sicherung, `php -l`, bevor die Datei ersetzt wird), `exp:config:get` liest eine, `exp:config:list --only-set` zeigt alle gesetzten ([.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)). |

## Nach dem Release

| Frage | Antwort |
|---|---|
| Wie aktualisieren die Empfänger? | Code und Datenbank, auf dem Weg, den Ihr Bundle festlegt ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)). |
| Ein veröffentlichtes Bundle-Release ist fehlerhaft. | Veröffentlichen Sie das nächste und sagen Sie, was falsch war; verschieben Sie nie den Tag ([Versionen, Tags und Pinning](versionen-tags-und-pinning.md#ein-veröffentlichtes-release-ändert-sich-nie)). |
| Wer unterstützt die Empfänger? | Zuerst Sie; 7x über Partner- oder bezahlten Support; die Community ohne Verpflichtung ([Grenzen des Supports](grenzen-des-supports.md)). |
| Wir haben einen Fehler in Exponential gefunden. | Melden Sie ihn beim Projekt; Sicherheitsprobleme vertraulich an security@se7enx.com ([Korrekturen zurückgeben](bundle-korrekturen-zurueckgeben.md)). |

## Marke und Partnerschaft

| Frage | Antwort |
|---|---|
| Dürfen wir unser Bundle "Exponential" nennen? | Geben Sie ihm einen eigenen Namen und sagen Sie "basierend auf Exponential"; jede Verwendung des Namens in einem Produkt stimmen Sie mit 7x ab ([Benennen und Gestalten](ein-bundle-benennen-und-gestalten.md)). |
| Gibt es ein OEM-Programm? | "OEM Resale Opportunities" ist ein Vorteil auf jeder Stufe; Einzelheiten sind nicht veröffentlicht: Fragen Sie das Partnerteam ([OEM und das Partnerprogramm](oem-und-das-partnerprogramm-fuer-buendler.md)). |
| Was kostet eine Partnerschaft? | Je nach Stufe, von Student bis Platinum, siehe [Partnerpläne und Preise](../partner/partnertarife-und-preise.md). |
| Wie treten wir bei? | Bewerben, zuerst bezahlen, dann unterschreiben ([Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md)). |
| Wen fragen wir? | <partners@exponential.earth> |

## Verwandte Seiten

- [Glossar für Bündler](glossar-fuer-buendler.md)
- [FAQ für Partner](../partner/faq-fuer-partner.md)
- [Bündler: hier beginnen](buendler-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

## Urheberrecht und Copyleft

Seit dem 29. Oktober 2026 halten die Exponential Foundation und 7x das Copyright an Exponential gemeinsam, die
Foundation zuerst genannt: „Exponential Copyright © 1998-2026 Exponential Foundation and 7x“, in Dateien
„Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“ Wer es hält und warum, was die
GPL Ihnen erlaubt, welche Hinweise Dritter bleiben und wie der Hinweis in neue Dateien und Erweiterungen kommt:
[Urheberrecht und Copyleft](../urheberrecht-und-copyleft.md).
