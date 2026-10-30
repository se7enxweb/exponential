---
title: Mitzuliefernde Lizenztexte und Hinweise
audience: bundler, partner
category: bundling
order: 120
summary: Die genauen Dateien und Hinweise, die ein Bundle von Exponential mitbringt - LICENSE, LICENSE.md, COPYRIGHT.md, die Lizenz der Dokumentation, die Lizenzdateien der Erweiterungen und Ihre eigenen Hinweise - wo die Empfänger sie in einer laufenden Installation sehen (Info > Über, Info > Copyright) und eine Vorlage für die Hinweisdatei Ihres Bundles.
keywords: Lizenztext, Hinweise, Hinweisdatei, Namensnennung, Info Über, license text, notices, LICENSE, LICENSE.md, COPYRIGHT.md, LICENSE-GFDL-1.3.txt, README-LICENSE.md, Info About, Info Copyright, ezinfo, notice file, attribution
type: help
---
# Mitzuliefernde Lizenztexte und Hinweise

Diese Seite richtet sich an alle, die die Dateien eines Bundle-Releases zusammenstellen. Sie zählt die Lizenz- und
Hinweisdateien auf, die mit Exponential mitreisen müssen, zeigt, wo eine installierte Website sie anzeigt, und gibt
eine Vorlage für die Hinweisdatei Ihres eigenen Bundles. Keine Rechtsberatung; maßgeblich sind die Lizenztexte.

## Inhalt

1. [Dateien aus dem Kernel](#dateien-aus-dem-kernel)
2. [Dateien aus der Dokumentation](#dateien-aus-der-dokumentation)
3. [Dateien aus den Erweiterungen](#dateien-aus-den-erweiterungen)
4. [Wo eine laufende Website sie anzeigt](#wo-eine-laufende-website-sie-anzeigt)
5. [Eine Hinweisdatei für Ihr Bundle](#eine-hinweisdatei-für-ihr-bundle)
6. [Checkliste](#checkliste)

## Dateien aus dem Kernel

| Datei | Was sie enthält |
|---|---|
| `LICENSE` | die Urheberrechtszeilen von der Exponential Foundation und 7x sowie des ursprünglichen Entwicklers |
| `LICENSE.md` | den Text der GNU General Public License, Version 2 |
| `COPYRIGHT.md` | das Urheberrecht und die Lizenzbedingungen ("either version 2 of the License, or (at your option) any later version") |
| Köpfe in den Quelldateien | Zeilen `@copyright` und `@license`, zum Beispiel "GNU General Public License v2.0 (or any later version)" |

Liefern Sie diese Dateien unverändert aus und behalten Sie die Köpfe bei. Wenn Ihr Bundle die Dateien im
Wurzelverzeichnis aus dem Web-Root entfernt (zum Beispiel in einem Distributionspaket, das die Dokumentation nach
`/usr/share/doc` verschiebt), legen Sie sie dort ab, wo Ihre Plattform Lizenzdateien aufbewahrt, und sagen Sie das in
Ihrem Hinweis.

## Dateien aus der Dokumentation

| Datei | Was sie enthält |
|---|---|
| `doc/eng-us/LICENSE-GFDL-1.3.txt` | die GNU Free Documentation License 1.3 |
| `doc/eng-us/README-LICENSE.md` | wer das Urheberrecht an der Dokumentation hält und welche älteren Seiten andere Bedingungen behalten |

Einzelheiten: [Mitzuliefernde Dokumentation](mitzuliefernde-dokumentation.md) und
[Freie Dokumentation](../freie-dokumentation.md).

## Dateien aus den Erweiterungen

Jede Erweiterung bringt ihre eigenen Metadaten und meist eine eigene Lizenzdatei mit:

- `ezinfo.php`: `Name`, `Version`, `Copyright`, `License`, `Info_url`;
- `extension.xml`: `<license>`, `<copyright>`, `<info_url>`;
- eine Datei `LICENSE` in vielen Repositorys.

Quelle: [Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md). Reduzieren Sie
Erweiterungsverzeichnisse in einem Bundle nicht auf "nur den Code": Die Metadaten und Lizenzdateien gehören dazu.

## Wo eine laufende Website sie anzeigt

| Seite | Zeigt | Quelle |
|---|---|---|
| **Info > Über** (`/ezinfo/about`) | die Version, die Lizenz, den Urheberrechtshinweis, eine Tabelle **Third-party software** und jede Erweiterung mit ihrer Lizenz und Website | [Die Seiten Über und Copyright](../../funktionen/6.0/info-und-paketseiten.md) |
| **Info > Copyright** (`/ezinfo/copyright`) | das Urheberrecht, die GPL Version 2 oder später, den Gewährleistungsausschluss und die Nennung der ursprünglichen Entwickler, "die die Lizenz verlangt" | dieselbe Seite |

Entfernen oder verstecken Sie diese Seiten in einem Bundle nicht; über sie erfährt ein Empfänger seine Rechte. Wenn
Ihr Bundle ein eigenes Admin-Design hat, behalten Sie einen Weg dorthin bei.

## Eine Hinweisdatei für Ihr Bundle

Legen Sie einen kurzen, schlichten Hinweis auf die oberste Ebene des Bundles (zum Beispiel `NOTICE.txt` oder einen
Abschnitt Ihrer README):

```text
<Bundle name> <version>
Copyright (C) <year> <your company>.

This bundle contains Exponential <version>, free software under the
GNU General Public License, version 2 or (at your option) any later version.
Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
Copyright (C) 1999 - 2014 eZ Systems AS.
See LICENSE, LICENSE.md and COPYRIGHT.md.

It also contains the third-party components listed in THIRD-PARTY.txt,
each under its own license.

The corresponding source code of the GPL components of this bundle is
available at <URL>  (or: see the written offer in SOURCE-OFFER.txt).

The documentation in doc/ is under the GNU Free Documentation License 1.3
or any later version; see doc/eng-us/LICENSE-GFDL-1.3.txt.
```

Übernehmen Sie die Urheberrechtszeilen aus `LICENSE` und `COPYRIGHT.md` genau der Version, die Sie ausliefern; sie
ändern sich mit den Jahren. Für Empfänger im deutschen Sprachraum können Sie eine deutsche Fassung des Hinweises
daneben stellen.

## Checkliste

- [ ] `LICENSE`, `LICENSE.md`, `COPYRIGHT.md` sind im Bundle, aus der Version, die Sie ausliefern.
- [ ] `doc/eng-us/LICENSE-GFDL-1.3.txt` und `doc/eng-us/README-LICENSE.md` sind enthalten, wenn die Dokumentation
      enthalten ist.
- [ ] Jedes Erweiterungsverzeichnis behält seine `ezinfo.php`, `extension.xml` und Lizenzdatei.
- [ ] Ihre Hinweisdatei nennt das Bundle, die Lizenz von Exponential, die Liste der Komponenten Dritter und das
      Quellcode-Angebot.
- [ ] **Info > Über** und **Info > Copyright** funktionieren im installierten Bundle.

## Verwandte Seiten

- [GPL-Pflichten beim Weitergeben](gpl-pflichten-beim-weitergeben.md)
- [Komponenten Dritter und ihre Lizenzen](komponenten-dritter-und-ihre-lizenzen.md)
- [Lizenzierung für Partner](../partner/lizenzierung-fuer-partner.md)
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
