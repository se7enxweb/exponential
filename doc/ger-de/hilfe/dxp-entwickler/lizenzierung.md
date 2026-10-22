---
title: Lizenzierung
audience: dxp-developer
category: dxp-development
order: 45
summary: Die Lizenz von Exponential Platform DXP, wie ihre Pakete sie deklarieren - GPL-2.0-or-later oder proprietär -, was die GPL für ein Projekt bedeutet, das Sie bauen und ausliefern, die Lizenzen der mitgelieferten Pakete von Drittanbietern und wie Sie einen Lizenzbericht erstellen.
keywords: Lizenz, Lizenzierung, GPL, GPL-2.0-or-later, proprietär, Doppellizenz, Open Source, composer licenses, Copyleft, Auslieferung an Kunden, Pakete von Drittanbietern, license, proprietary, dual license, open source, copyleft, delivering to customers, third-party packages
type: help
---
# Lizenzierung

Diese Seite richtet sich an Entwickler und Projektleiter, die die Lizenz eines DXP-Projekts angeben oder eines an einen
Kunden ausliefern müssen. Sie gibt wieder, was die Pakete deklarieren; sie ist keine Rechtsberatung.

## Inhalt

1. [Was die Pakete deklarieren](#was-die-pakete-deklarieren)
2. [Was die GPL für Ihr Projekt bedeutet](#was-die-gpl-für-ihr-projekt-bedeutet)
3. [Pakete von Drittanbietern](#pakete-von-drittanbietern)
4. [Ein Lizenzbericht](#ein-lizenzbericht)
5. [Dokumentation](#dokumentation)

## Was die Pakete deklarieren

| Paket | `license` in `composer.json` |
|---|---|
| `se7enxweb/exponential-platform-dxp` | `(GPL-2.0-or-later or proprietary)` |
| `se7enxweb/exponential-platform-dxp-skeleton` | `(GPL-2.0-or-later or proprietary)` |
| `se7enxweb/admin-ui` und die anderen Forks | GPL 2.0 oder später (die Forks haben die Formulierung von "GPL 2.0 only" geändert, [Schritt 6](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md#schritt-6-lizenzangaben)) |

Das README des Skeletons trägt das Abzeichen "GPL v2 (or any later version)". Die Copyright-Zeilen nennen 7x und, für den
Upstream-Code, Ibexa AS (früher eZ Systems AS).

## Was die GPL für Ihr Projekt bedeutet

- Sie dürfen die DXP für jeden Zweck verwenden, untersuchen, ändern und betreiben, auch in kommerziellen Projekten.
- Wenn Sie die Software **verbreiten** (einem Kunden eine Kopie übergeben, sie veröffentlichen), gehen der GPL-Code und
  Ihre Änderungen daran mit ihrem Quellcode unter der GPL mit.
- Eine Website für Besucher zu betreiben ist unter GPL Version 2 keine Verbreitung des Codes an sie.
- Ihr eigener Code in `src/`, der mit der DXP arbeitet, ist bei einer Verbreitung Teil eines kombinierten Werks;
  entscheiden Sie seine Lizenz mit diesem Wissen und fragen Sie für einen Vertrag, der davon abhängt, einen Juristen.

Die Seiten der Zielgruppe Designer erklären dieselben Regeln für Themes in einfachen Worten:
[GPL für Theme-Code](../designer/veroeffentlichung/gpl-fuer-theme-code.md),
[GPL und Verkauf](../designer/verkauf/gpl-und-verkauf.md).

## Pakete von Drittanbietern

Das Metapaket bringt Symfony (MIT), Doctrine (MIT), Twig (BSD-3-Clause), das JWT-Bundle von Lexik (MIT) und viele mehr
mit. Jedes behält seine eigene Lizenz. Ein ausgeliefertes Projekt listet sie auf.

## Ein Lizenzbericht

```bash
composer licenses --no-dev                          # every PHP package with its license
composer licenses --format=json > var/licenses.json
yarn licenses list                                  # Node packages used by the builds
```

Lesen Sie den Bericht auf Pakete ohne Lizenz oder mit einer Lizenz, die Ihr Kunde nicht akzeptiert, vor dem Vertrag,
nicht danach.

## Dokumentation

Diese Hilfeseiten sind freie Dokumentation unter der GNU FDL 1.3 ([Freie Dokumentation](../freie-dokumentation.md)): Sie
dürfen sie unter derselben Lizenz in Projektdokumentation für einen Kunden übernehmen.

## Verwandte Seiten

- [Das Metapaket lesen](das-metapaket-lesen.md)
- [Überblick über Lizenzentscheidungen (für Themes)](../designer/veroeffentlichung/lizenzwahl-ueberblick.md)
- [Zu den Forks beitragen](zu-den-forks-beitragen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
