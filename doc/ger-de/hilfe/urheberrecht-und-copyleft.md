---
title: "Urheberrecht und Copyleft: wem Exponential gehört und was die GPL Ihnen erlaubt"
audience: editor, admin, developer, user, integrator, partner, government, press, trainer, community
category: support
order: 94
summary: Der eine Copyright-Hinweis von Exponential, „Exponential Copyright © 1998-2026 Exponential Foundation and 7x“, und seine Form für Dateiköpfe; der historische Wechsel vom 29.10.2026 von 7x allein zur Exponential Foundation und 7x gemeinsam und warum die Foundation zuerst steht; was die GNU GPL Version 2 oder später für Anwender, Integratoren und Autoren von Erweiterungen bedeutet; das Support-Fenster; Beitragende und das CLA; die Hinweise Dritter, die bleiben; und wie der Hinweis in neuen Dateien und Erweiterungen geschrieben wird, mit Beispielen.
keywords: Urheberrecht, Copyright, Copyleft, Hinweis, Exponential Foundation, 7x, GPL, GNU General Public License, GPL-2.0-or-later, Lizenz, Dateikopf, @copyright, ezinfo.php, extension.xml, CLA, Beitragende, Hinweise Dritter, eZ Systems, Namensnennung, LICENSE, COPYRIGHT.md, 2026-10-29
type: help
---
# Urheberrecht und Copyleft: wem Exponential gehört und was die GPL Ihnen erlaubt

Exponential hat **einen Copyright-Hinweis**. Wo immer ein Mensch ihn sieht (in der Fußzeile einer Seite, auf der
Anmeldeseite, auf der Seite „Über“, in der Hilfe, in der Konsole), lautet er Wort für Wort:

> **Exponential Copyright © 1998-2026 Exponential Foundation and 7x**

Der Hinweis wird nicht übersetzt: Er ist eine rechtliche Angabe mit Namen und steht in jeder Sprache gleich da.

In den Quelldateien, in den Metadaten von Erweiterungen und in den Lizenzdateien steht derselbe Hinweis in der Form,
die für Dateiköpfe üblich ist:

> **Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.**

Die Software ist freie Software unter der **GNU General Public License, Version 2 oder (nach Ihrer Wahl) jeder
späteren Version** (GPL-2.0-or-later). Das Copyright sagt, *wem* das Werk gehört; die Lizenz sagt, *was alle anderen*
damit tun dürfen. Diese Seite erklärt beides in einfachen Worten, dazu die Regeln, denen jede Datei, jede
Erweiterung und jede Seite des Projekts folgt.

Diese Seite ist eine Zusammenfassung, keine Rechtsberatung. Maßgeblich sind der Lizenztext in der Datei `LICENSE`
und die Hinweise in `COPYRIGHT.md`.

## Inhalt

1. [Der Hinweis in einer Minute](#der-hinweis-in-einer-minute)
2. [Ein historischer Wechsel: 29. Oktober 2026](#ein-historischer-wechsel-29-oktober-2026)
3. [Warum die Exponential Foundation zuerst genannt wird](#warum-die-exponential-foundation-zuerst-genannt-wird)
4. [Die zwei Formen und wo sie stehen](#die-zwei-formen-und-wo-sie-stehen)
5. [Was „Copyleft“ bedeutet](#was-copyleft-bedeutet)
6. [Was die GPL für Sie bedeutet](#was-die-gpl-fuer-sie-bedeutet)
7. [Das Support-Fenster und warum der Hinweis dafür zählt](#das-support-fenster-und-warum-der-hinweis-dafuer-zaehlt)
8. [Beitragende und das CLA](#beitragende-und-das-cla)
9. [Hinweise, die bleiben: eZ Systems AS und Software Dritter](#hinweise-die-bleiben-ez-systems-as-und-software-dritter)
10. [Den Hinweis in neue Dateien schreiben](#den-hinweis-in-neue-dateien-schreiben)
11. [Den Hinweis in eine Erweiterung schreiben](#den-hinweis-in-eine-erweiterung-schreiben)
12. [Den Hinweis in einem Design oder Theme zeigen](#den-hinweis-in-einem-design-oder-theme-zeigen)
13. [Übersetzungen: Der Hinweis wird nie übersetzt](#uebersetzungen-der-hinweis-wird-nie-uebersetzt)
14. [Wie das Projekt den Hinweis prüft](#wie-das-projekt-den-hinweis-prueft)
15. [Die Lizenz der Dokumentation](#die-lizenz-der-dokumentation)
16. [Häufige Fragen](#haeufige-fragen)
17. [Weiterlesen](#weiterlesen)

## Der Hinweis in einer Minute

| Frage | Antwort |
|---|---|
| Wer hält das Copyright an Exponential? | Die **Exponential Foundation und 7x**, gemeinsam. |
| Seit wann lautet der Hinweis so? | Seit dem **29. Oktober 2026** (Linie 6.0.15). |
| Welche Jahre nennt er? | **1998 bis 2026**. Das Endjahr geht mit den Releases mit. |
| Was sieht ein Benutzer? | „Exponential Copyright © 1998-2026 Exponential Foundation and 7x“ |
| Was steht in einem Dateikopf? | „Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“ |
| Unter welcher Lizenz steht die Software? | GNU General Public License, Version 2 oder jede spätere Version (GPL-2.0-or-later). |
| Unter welcher Lizenz steht diese Dokumentation? | GNU Free Documentation License, Version 1.3 oder jede spätere Version. |
| Wird der ursprüngliche Hersteller weiter genannt? | Ja: „Copyright (C) 1999 - 2014 eZ Systems AS“, der Hersteller von eZ Publish, dem eingestellten Produkt, aus dem Exponential hervorging. |
| Wird der Hinweis übersetzt? | Nein. Die Worte um ihn herum werden übersetzt, der Hinweis ist in jeder Sprache gleich. |
| Muss ich meine eigene Erweiterung ändern? | Nur wenn sie behauptet, Werk von 7x oder der Foundation zu sein. Ihre eigene Arbeit trägt Ihren eigenen Hinweis. |

## Ein historischer Wechsel: 29. Oktober 2026

Am **29. Oktober 2026** ging das Copyright an Exponential von **7x allein** auf die **Exponential Foundation und 7x
gemeinsam** über, und das Projekt ersetzte jeden seiner Hinweise durch einen einzigen. Es ist ein Meilenstein in der
Geschichte der Software und ist hier, in `COPYRIGHT.md` und im
[Changelog von 6.0.15](../../changelogs/6.0/6.0.15.md#2026-10-29-one-copyright-notice-exponential-foundation-and-7x)
festgehalten.

### Wie der Hinweis vorher lautete

Über die Jahre war der Hinweis auf die eigene Arbeit des Projekts in mehreren Schreibweisen gewachsen, jede aus einer
anderen Zeit und einer anderen Datei. Die alten Formen werden hier unverändert zitiert:

| Wo | Wortlaut vor dem 29.10.2026 |
|---|---|
| Fußzeile der Administration | „Exponential Copyright © 1998-2026 7x and others.“ (übersetzt, z. B. „7x und andere“) |
| Anmeldeseite der Administration | „Exponential Copyright © 1998-2026 7x and others.“ |
| Fußzeile des Standard-Designs | „Copyright © 1998-2026 7x (except where otherwise noted). All rights reserved.“ |
| Einrichtungsassistent | „Exponential copyright © 1998-2026 7x & Exponential Foundation“ |
| Seite eines schweren Fehlers | „Copyright © 1998-2026 7x and others.“ |
| `./console --version` | „Copyright (C) 1998-2026 7x / Exponential Foundation.“ |
| /ezinfo/copyright | „Copyright (C) 1998-2026 7x / Exponential Foundation. All rights reserved.“ |
| `LICENSE` | „Copyright (C) 1998 - 2026 7x / Exponential Foundation. All rights reserved.“ |
| `COPYRIGHT.md` | „Copyright © 1998 - 2025, 7x / Exponential Foundation.“ und „Copyright (C) 1998 - 2026 7x & Exponential Foundation.“ |
| Die meisten PHP-Dateiköpfe (etwa 2.850) | „@copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.“ |
| Viele neuere PHP-Dateiköpfe (etwa 590) | „@copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.“ |
| Andere Dateiköpfe | „Copyright (C) 7x / Exponential Foundation.“, „Copyright (C) 7x.“, „Copyright (C) 1998 - 2026 7x.“, „Copyright (C) 1998 - 2026 7x and others.“ |
| Übersetzungskataloge | „Copyright © 1999-2014 7x AS and others.“ (ein Überbleibsel der Umbenennung des ursprünglichen Hinweises) |

Vor der Änderung vom 02.10.2026 („Every copyright notice names 1998 - 2026 7x & Exponential Foundation first“)
nannten viele Dateien nur eZ Systems AS. Jene Änderung setzte die eigene Zeile des Projekts in rund 3.000 Dateien über
die Zeile von eZ Systems. Die Änderung vom 29.10.2026 schließt die Arbeit ab: eine Angabe der Rechteinhaber, eine
Reihenfolge, zwei Formen, überall.

### Was sich am 29. Oktober 2026 geändert hat

- Jede Fußzeile und jede Seite, die ein Benutzer sieht, zeigt den sichtbaren Hinweis (siehe
  [die Liste](#die-zwei-formen-und-wo-sie-stehen)).
- Rund 3.600 Dateiköpfe tragen die Form für Dateien: Die Zeile wurde per Skript umgeschrieben, sonst änderte sich in
  den Dateien nichts.
- `LICENSE` und `COPYRIGHT.md` nennen die neuen Rechteinhaber. **Der Text der GNU General Public License wurde nicht
  angerührt**: Nur die Zeilen der Rechteinhaber über ihm änderten sich.
- Die Metadaten der im Kernel mitgelieferten Erweiterungen (`ezinfo.php`, `extension.xml`) und der Generator neuer
  Erweiterungen (`./console exp:ini:move`) schreiben die Form für Dateien.
- `composer.json` nennt beide Rechteinhaber als Autoren.
- Jeder Hinweis Dritter und der Hinweis von eZ Systems AS blieben genau, wie sie waren.
- Ein Test (`CopyrightNoticeTest`) und ein Prüfskript schlagen fehl, wenn eine Datei 7x oder die Foundation in einer
  anderen Form nennt.

Die Erweiterungen mit eigenen Repositories (die Erweiterungen von se7enxweb und ihre `-ezpackage`-Gegenstücke,
Exponential Velocity, die Installer und Seitenpakete) folgen mit ihren nächsten Releases, jeweils mit erhöhter
Version in `ezinfo.php` und `extension.xml`.

## Warum die Exponential Foundation zuerst genannt wird

Die Reihenfolge ist Absicht. Die **Exponential Foundation** steht zuerst, weil sie die Treuhänderin des Projekts ist:
die gemeinnützige Organisation, die Exponential für seine Benutzer und seine Gemeinschaft betreibt und die die Rechte
der Software, ihre Lizenz und ihre Geschichte über die ganze Lebensdauer einer Release-Linie tragen soll, unabhängig
vom Schicksal eines einzelnen Unternehmens. **7x** steht an zweiter Stelle und bleibt voller Rechteinhaber: das
Unternehmen, das die Entwicklung leitet, den Code pflegt, Beiträge prüft und übernimmt und die Kunden betreut.

Die Foundation zuerst zu nennen sagt an der Stelle, an die jeder Benutzer und jede Juristin zuerst schaut: Exponential
ist nicht das Produkt eines einzelnen Herstellers. Das ist die Lehre aus der eigenen Geschichte der Software: Als der
ursprüngliche Hersteller das freie Produkt einstellte, mussten Benutzer, die sich jahrelang darauf verlassen hatten,
eine neue Treuhänderin suchen. Ein Hinweis, der eine gemeinnützige Organisation zuerst nennt und das Unternehmen, das
die Arbeit macht, daneben, ist die Antwort des Projekts auf diese Geschichte.

Die Reihenfolge ist in jeder Form und jeder Sprache dieselbe: „Exponential Foundation and 7x“, nie „7x and the
Exponential Foundation“ und nie „7x & Exponential Foundation“.

## Die zwei Formen und wo sie stehen

### Der sichtbare Hinweis

> Exponential Copyright © 1998-2026 Exponential Foundation and 7x

Überall, wo ein Mensch ihn auf einem Bildschirm oder einer Seite liest:

| Ort | Datei |
|---|---|
| Fußzeile der Administration (admin, admin3, admin4, admin4l, editor) | `design/admin/templates/page_copyright.tpl`, `design/admin4/templates/page_copyright.tpl` |
| Anmeldeseite der Administration | `design/admin/templates/page_login_copyright.tpl`, `design/admin4/templates/page_login_copyright.tpl` |
| Fußzeile des Standard-Designs (plain, Druck, Popup, Vollbild, Anmeldung, OAuth) | `design/standard/templates/page_copyright.tpl` |
| Fußzeile des Base-Designs und seiner Anmeldeseite | `design/base/templates/pagelayout.tpl`, `design/base/templates/loginpagelayout.tpl` |
| Einrichtungsassistent | `design/standard/templates/setup_pagelayout.tpl`, `design/standard/templates/setup/page_copyright.tpl` |
| Seite eines unbekannten Moduls | `design/standard/templates/undefinedmodule.tpl` |
| Seite eines schweren Fehlers | `lib/ezutils/classes/ezexecution.php` |
| Die Copyright-Seite (/ezinfo/copyright) | `kernel/private/classes/views/ezinfo/copyright.php`, `design/*/templates/ezinfo/copyright.tpl` |
| Die Hilfe: der Lizenzkasten jeder Seite, das PDF einer Seite | `settings/exphelp.ini` `[License]`, `kernel/classes/help/exphelplicense.php` |
| `./console --version` | `bin/php/console` |
| Die Systemkarte | `share/syscard/about.txt` |

In HTML steht das Zeichen als `&copy;`. Links dürfen auf den Wörtern liegen (die Fußzeile der Administration verlinkt
„Exponential“, „Exponential Foundation“ und „7x“), der gelesene Text ist aber immer genau der Hinweis.

### Die Form für Dateien

> Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.

In Dateien und Metadaten: die Zeile `@copyright` von PHP-Docblocks, die Zeile `// COPYRIGHT NOTICE:` von
JavaScript- und C++-Quellen, Kommentarköpfe von Templates, Stylesheets und Skripten, `<copyright>` in
`extension.xml`, `'Copyright'` in `ezinfo.php`, `LICENSE` und `COPYRIGHT.md`. „(C)“ steht in reinem ASCII für das
Copyright-Zeichen, das jede Datei aufnehmen kann. „All rights reserved“ ist der übliche Schluss eines solchen Hinweises
und nimmt keine Freiheit, die die GPL gibt (siehe [Häufige Fragen](#haeufige-fragen)).

## Was „Copyleft“ bedeutet

**Copyleft** ist eine Art, das Urheberrecht zu nutzen, um Software frei zu halten. Die Rechteinhaber hängen an ihr
Werk eine Lizenz, die sagt: Jeder darf es benutzen, untersuchen, ändern und weitergeben, **unter der Bedingung**, dass
jeder, der es geändert oder unverändert weitergibt, dieselben Freiheiten mitgibt. Die GNU General Public License ist
die bekannteste Copyleft-Lizenz.

Copyright-Hinweis und Copyleft gehören zusammen. Ohne Rechteinhaber gibt es niemanden, der die Lizenz erteilen oder
durchsetzen kann; ohne die Lizenz würde das Copyright das Kopieren verbieten. Der Hinweis sagt, wer die GPL im Namen
aller durchsetzen kann, die sich auf sie verlassen. Darum hält das Projekt ihn genau.

## Was die GPL für Sie bedeutet

Die Lizenz von Exponential ist die **GNU General Public License, Version 2 oder jede spätere Version**. Kurz gefasst:

### Wenn Sie Exponential benutzen (Seitenbetreiber, Redakteure, Administratoren)

- Sie dürfen es für jeden Zweck **betreiben**, kommerziell oder nicht, auf so vielen Servern und Websites, wie Sie
  wollen. Keine Gebühr, keine Registrierung, kein Lizenzschlüssel.
- Sie dürfen es für Ihren eigenen Gebrauch **ändern**. Änderungen, die Sie nur auf Ihren eigenen Servern betreiben,
  ohne Kopien an andere zu geben, müssen Sie nicht veröffentlichen.
- Die Hinweise bleiben stehen. Sie dürfen weder den Copyright-Hinweis noch die Lizenz aus den Dateien entfernen.

### Wenn Sie Websites für andere bauen (Integratoren, Agenturen, Hoster)

- Sie dürfen Exponential an Kunden **ausliefern**, installiert oder als Dateien, und für Ihre Arbeit, Ihren Support und
  Ihr Hosting Geld nehmen.
- Wenn Sie einem Kunden eine Kopie geben (die Dateien der Installation, ein Repository, ein Archiv), reist die GPL mit:
  Behalten Sie `LICENSE`, `COPYRIGHT.md` und jeden Hinweis bei, und geben Sie dem Kunden den Quelltext dessen, was Sie
  ausliefern, samt Ihren Änderungen am Kernel.
- Eine Website für einen Kunden auf Ihren Servern zu betreiben, ohne die Dateien zu übergeben, ist unter GPL Version 2
  keine Verbreitung.

### Wenn Sie Erweiterungen schreiben (Autoren von Erweiterungen)

- **Ihre Erweiterung ist Ihr Werk.** Ihr Copyright gehört Ihnen (oder Ihrem Arbeitgeber), und ihr Hinweis nennt Sie,
  nicht 7x und nicht die Foundation.
- Eine Erweiterung, die in Exponential läuft und seine Klassen und Templates verwendet, gilt in der Regel als auf
  Exponential beruhendes Werk; **verbreiten** Sie sie also unter der GPL Version 2 oder später oder einer mit ihr
  verträglichen Lizenz. Erweiterungen, die Sie nur für Ihre eigenen Websites behalten, brauchen gar keine Lizenz.
- Enthält Ihre Erweiterung aus Exponential kopierten Code, behalten Sie den Hinweis dieses Codes
  („Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“) neben Ihrem eigenen.

### Wenn Sie Exponential forken oder weiterverbreiten

- Sie dürfen es forken, Ihren Fork umbenennen und veröffentlichen. Behalten Sie jeden Hinweis, fügen Sie Ihren eigenen
  für Ihre Änderungen hinzu, und behalten Sie die Lizenz GPL-2.0-or-later.
- Sie dürfen Ihren Fork nicht als das Projekt Exponential ausgeben oder mit dem Hinweis andeuten, dass die Foundation
  oder 7x hinter ihm stehen.

## Das Support-Fenster und warum der Hinweis dafür zählt

Exponential 6.x ist für lange Nutzung gedacht: Das Projekt plant Releases und Korrekturen für diese Linie über ein
Support-Fenster von **zwanzig Jahren und mehr**, passend zu einer Codebasis, deren Geschichte bis 1998 zurückreicht und
auf die sich ihre Benutzer seit mehr als zwei Jahrzehnten verlassen. Ein Release von 2026 muss auch 2046 noch klar über
seine Rechte sein.

Ein klarer, einziger Hinweis schützt dieses Fenster:

- **Durchsetzung.** Copyleft funktioniert nur, wenn die Rechteinhaber benannt sind und handeln können. Ein Hinweis mit
  zwei Rechteinhabern, die jeweils handeln können, lässt keinen Zweifel, wer die GPL für die Benutzer verteidigen kann.
- **Kontinuität.** Mit der Foundation als Rechteinhaberin hängen die Rechte nicht am Leben eines einzelnen Unternehmens.
- **Prüfung.** Einkäufer, Behörden und Rechtsabteilungen lesen den Hinweis zuerst. Eine Form, überall, mit denselben
  Jahren und Namen, beantwortet ihre Frage in Sekunden.
- **Kein Auseinanderlaufen.** Der Test, der jeden Hinweis prüft, verhindert, dass neue Dateien alte Schreibweisen
  zurückbringen.

## Beitragende und das CLA

Exponential wird von vielen Menschen geschrieben. Ihre Beiträge kommen auf zwei Wegen in den Kernel:

- **Code-Beiträge zum Kernel** (Repository se7enxweb/exponential) werden übernommen, nachdem die Autorin oder der Autor
  das **Contributor Licensing Agreement (CLA)** unterschrieben hat. Es überträgt das Copyright am Beitrag an die
  **Exponential Foundation und 7x**, die damit seine Eigentümer werden, die Foundation zuerst genannt wie im Hinweis
  (Entscheidung des Eigentümers vom 29. Oktober 2026; bis dahin übertrug die Vereinbarung, Version 1.0.1, es an 7x
  allein). Die beiden Rechteinhaber des Hinweises sind damit auch die Rechteinhaber jedes übernommenen Beitrags. Das
  Formular auf der CLA-Seite wird auf diese Übertragung umgestellt; lesen Sie es, bevor Sie unterschreiben
  (`CONTRIBUTING.md`).
- **Beiträge zur Dokumentation** werden unter der GNU Free Documentation License 1.3 oder später angenommen (siehe
  [Die Lizenz dieser Dokumentation](../README-LIZENZ.md)).

**Ein eigenes Urheberrecht „der Mitwirkenden von Exponential“ gibt es nicht.** Alle Mitwirkenden übertragen das
Urheberrecht an einem Beitrag über das CLA an die Exponential Foundation und 7x; das Projekt hat also genau zwei
Rechteinhaber, und der Hinweis nennt nur diese beiden, im Code wie in der Dokumentation. Frühere Fassungen des
Hinweises der Dokumentation ergänzten „and the Exponential contributors“; diese Formulierung ist entfallen
(Entscheidung des Eigentümers vom 29. Oktober 2026). Beiträge zur Dokumentation liegen im selben Repository (`doc/`)
und fallen unter dasselbe CLA.

Mitwirkende werden genannt, und die Nennung wird nie entfernt: in den Changelogs, in der Autorenschaft jedes Commits
(die Git-Historie hält fest, wer was geschrieben hat), auf den Seiten zur Anerkennung und in der Liste der
Mitwirkenden auf der Seite „Über“ (/ezinfo/about). Eine Nennung ist kein Anspruch auf Urheberrecht.

Ihre eigenen Erweiterungen, Themes und Seitenpakete in Ihren eigenen Repositories sind **keine** Beiträge und brauchen
kein CLA. Sie tragen Ihren Hinweis.

## Hinweise, die bleiben: eZ Systems AS und Software Dritter

Die GPL verlangt, dass jeder Copyright-Hinweis, der mit dem Code kam, stehen bleibt. Das Projekt ändert, entfernt oder
verschiebt sie nie. Diese blieben am 29.10.2026 erhalten:

| Hinweis | Wo | Warum er bleibt |
|---|---|---|
| „Copyright (C) 1999 - 2014 eZ Systems AS. All rights reserved.“ und „@copyright Copyright (C) eZ Systems AS. All rights reserved.“ | `LICENSE`, `COPYRIGHT.md`, rund 1.390 PHP-Dateien, die Zeilen `// COPYRIGHT NOTICE` von 39 JavaScript-Dateien, die Seite /ezinfo/copyright | eZ Systems AS entwickelte eZ Publish, das eingestellte Produkt, aus dem Exponential hervorging. Ihr Hinweis steht in jeder Datei mit ihrem Code, unter der eigenen Zeile des Projekts. |
| „Copyright (C) 1999 - 2026 7x / Brookins Consulting / eZ Systems AS“ | `lib/eztemplate/classes/eztemplateroleoperator.php` | Gemeinsamer Hinweis von Brookins Consulting (Name von 7x vor 2022) und eZ Systems AS für den ursprünglichen Operator; die neue Zeile steht direkt darüber. |
| „Copyright CJW Network and Exponential Foundation. All rights reserved.“ | rund 40 Dateien (der Bildeditor `expimageeditor`, die Klassen des Paketarchivs, einige Tests) | Der Hinweis von CJW Network, Mitautor dieser Dateien; der Hinweis eines anderen Autors bleibt, wie er ist. |
| „Copyright (C) Mugo Web, GPL v2“ | Templates und Klassen mit Code aus LoveStack | Code aus der Arbeit von Mugo Web behält seinen Hinweis und nennt den Commit, aus dem er stammt. |
| „Copyright (C) 1989, 1991 Free Software Foundation, Inc.“ | `LICENSE`, `LICENSE.md`, die `LICENSE`-Dateien von ezjscore, ezoe und ezformtoken | Teil des Lizenztextes, der sich nie ändern darf. |
| „Copyright ..., Moxiecode Systems AB“ und „Ephox Corporation DBA Tiny Technologies, Inc.“ | TinyMCE in `extension/ezoe` | Die Editor-Komponente des Online-Editors. |
| „Copyright (c) Unicode, Inc., Unicode License v3“ | `share/locale` (CLDR-Daten) | Gebietsschema-Daten aus dem Unicode CLDR. |
| „Copyright (C) 2009 Nokia Corporation and/or its subsidiary(-ies)“, „Trolltech AS“ | `support/ezlupdate-qt4.5` | Die Qt-Linguist-Quellen des Übersetzungswerkzeugs. |
| „Copyright (C) 2002 and following years KDE Artists“ | `share/icons/crystal*` | Der Crystal-Iconsatz. |
| „Copyright (C) 1999-2010 Tango_Icon_Library“ | `extension/ezoe` | Icons des Online-Editors. |
| Die Hinweise der Bücher („Copyright (c) ... 7x und Graham ...“) | Dokumentation über die Bücher | Die Bücher sind eigene Werke mit eigenem Copyright. |

Die vollständige Liste dieser Ausnahmen mit dem Grund für jede ist die Datei
`tests/tests/bin/copyright-notice-allowlist.json`.

## Den Hinweis in neue Dateien schreiben

Jede neue Datei des Kernels beginnt mit der Form für Dateien, im Kommentarstil ihrer Sprache. Behalten Sie den Aufbau
der umliegenden Dateien bei: die Zeile `@copyright`, dann die Zeile `@license`.

### PHP

```php
<?php
/**
 * File containing the expExampleThing class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */
```

Eine Datei, die auch Code von eZ Systems AS enthält, behält deren Zeile direkt darunter:

```php
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
```

### JavaScript

```js
/**
 * Exponential: the bookmark button of the page header.
 *
 * Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * GNU General Public License v2.0 (or any later version).
 */
```

Ältere JavaScript- und C++-Dateien nutzen den Stil `// COPYRIGHT NOTICE:`; in diesen Dateien bleibt er:

```js
// COPYRIGHT NOTICE: Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
// COPYRIGHT NOTICE: Copyright (C) 1999-2014 eZ Systems AS
```

### Templates (.tpl)

```
{* Die Fußzeile der Beispielseite.
   Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
   GNU General Public License v2.0 (or any later version) *}
```

### INI-Dateien, Shell- und Python-Skripte

```ini
# Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
# GNU General Public License v2.0 (or any later version)
```

Regeln für alle:

- Schreiben Sie den Hinweis **genau so**: „Copyright (C) 1998 - 2026“, ein Leerzeichen auf jeder Seite des
  Bindestrichs, „Exponential Foundation and 7x“, ein Punkt, „All rights reserved.“ mit Punkt. Der Hinweis bleibt
  englisch, auch in einer deutschen Datei.
- Nie „7x & Exponential Foundation“, „7x and the Exponential Foundation“, „7x / Exponential Foundation“, „7x and
  others“ oder „7x“ allein.
- Entfernen oder ändern Sie nie die Zeile eines anderen Rechteinhabers. Setzen Sie Ihre darüber.
- Ändern Sie die Jahre einer vorhandenen Zeile nicht, damit sie „passt“; das Projekt ändert das Endjahr zum
  Jahreswechsel für alle Dateien auf einmal.

## Den Hinweis in eine Erweiterung schreiben

### Eine Erweiterung des Projekts (im Kernel mitgeliefert oder von se7enxweb veröffentlicht)

`ezinfo.php`:

```php
'Copyright' => "Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.",
'License' => "GNU General Public License v2.0 (or any later version)",
```

`extension.xml`:

```xml
<copyright>Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.</copyright>
<license>GNU General Public License v2.0 (or any later version)</license>
```

Eine Erweiterung mit Code von eZ Systems AS behält beide Hinweise in einem Wert, den des Projekts zuerst:

```xml
<copyright>Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved. Copyright (C) eZ Systems AS. All rights reserved.</copyright>
```

`./console exp:ini:move` schreibt genau diese Dateien, wenn es eine Einstellungs-Erweiterung anlegt.

### Ihre eigene Erweiterung

Ihre Erweiterung trägt **Ihren** Hinweis, zum Beispiel:

```php
 * @copyright Copyright (C) 2026 Beispiel-Agentur GmbH. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
```

Nur wenn Sie Code aus Exponential hineinkopiert haben, fügen Sie für diesen Code die Zeile des Projekts hinzu. Setzen
Sie den Hinweis des Projekts nicht auf Code, den Sie geschrieben haben: Sie würden Ihr Copyright auf dem Papier
verschenken.

## Den Hinweis in einem Design oder Theme zeigen

Ein Frontend-Theme muss den Hinweis nicht zeigen. Viele Websites zeigen stattdessen ihre eigene Fußzeile, was ihr
gutes Recht ist. Zeigt Ihr Design den Hinweis der Software, dann genau so:

```
<a href="https://exponential.earth">Exponential</a> Copyright &copy; 1998-2026 <a href="https://foundation.exponential.earth">Exponential Foundation</a> and <a href="https://se7enx.com">7x</a>
```

## Übersetzungen: Der Hinweis wird nie übersetzt

Der Hinweis ist eine rechtliche Angabe und ein Name, darum lautet er in jeder Sprache gleich. In den Katalogen
(`share/translations/<locale>/translation.ts`) ist die Übersetzung der Fußzeilen-Meldung in allen 156 Katalogen der
Hinweis selbst; `translation-identical-allowlist.json` erlaubt, dass sie identisch bleibt. Übersetzt werden die Worte um
ihn herum („Weitere Informationen finden Sie unter ezinfo/about“, „Betrieben mit ...“, „Hilfe-Center“), nie der
Hinweis. Eine Übersetzung, die den Hinweis verändert, ist ein Fehler.

## Wie das Projekt den Hinweis prüft

- **`tests/tests/bin/CopyrightNoticeTest.php`** (PHPUnit, Testsuite `quality`, ohne Datenbank) liest jede Datei des
  Repositories und schlägt bei jeder Zeile fehl, die 7x oder die Exponential Foundation in einer anderen als den zwei
  erlaubten Formen als Rechteinhaber nennt, es sei denn, die Zeile steht mit Grund in
  `tests/tests/bin/copyright-notice-allowlist.json`.
- **`tests/tests/design/CopyrightFooterNoticeTest.php`** prüft die Fußzeilen, die Anmeldeseiten, den
  Einrichtungsassistenten, die Fehlerseite, die Konsole, die Copyright-Seite und die Hilfe, und dass kein Katalog den
  Hinweis übersetzt.
- Beide laufen bei jedem Push in der kontinuierlichen Integration.

Eine Zeile gilt als Hinweis, wenn sie das Wort „copyright“ und ein Kennzeichen hat: „(C)“, „©“ (oder `&copy;`), eine
Jahreszahl, `@copyright`, `<copyright>`, `'Copyright' =>` oder `Copyright=` / `Copyright:`. Fließtext wie „die
Foundation und 7x halten das Copyright“ ist kein Hinweis und wird nicht geprüft.

## Die Lizenz der Dokumentation

Diese Dokumentation ist freie Dokumentation unter der GNU Free Documentation License, Version 1.3 oder jede spätere
Version. Ihr Hinweis nennt dieselben Rechteinhaber; siehe [Die Lizenz dieser Dokumentation](../README-LIZENZ.md) und
[Freie Dokumentation](freie-dokumentation.md). Die von der ursprünglichen Software übernommenen Seiten behalten den
Hinweis von eZ Systems AS und ihre eigenen Bedingungen.

## Häufige Fragen

**Widerspricht „All rights reserved“ der GPL?**
Nein. Es ist der übliche Schluss eines Copyright-Hinweises und bedeutet, dass die Rechteinhaber ihr Copyright behalten.
Die GPL ist die Erlaubnis, die sie darüber hinaus geben; Sie behalten jede Freiheit, die die Lizenz gewährt.

**Warum 1998?**
1998 ist das erste Jahr des Hinweises auf die eigene Arbeit des Projekts, so wie er seit Beginn der Linie 6.x lautet.
Die Arbeit von eZ Systems AS ist getrennt datiert, 1999 bis 2014.

**Heißt die Reihenfolge, dass der Foundation mehr gehört?**
Die Reihenfolge sagt nichts über Anteile. Beide sind Rechteinhaber. Die Foundation steht aus den
[oben](#warum-die-exponential-foundation-zuerst-genannt-wird) genannten Gründen zuerst.

**Wem übertrage ich meinen Beitrag mit dem CLA?**
Der Exponential Foundation und 7x gemeinsam; sie werden seine Eigentümer und dürfen ihn nutzen, ändern, neu
lizenzieren und verbreiten. Seit dem 29. Oktober 2026 nennt das CLA beide, in derselben Reihenfolge wie der Hinweis.
Ihre eigenen Erweiterungen sind nicht betroffen.

**Warum nennt der Hinweis die Mitwirkenden nicht?**
Weil sie kein eigenes Urheberrecht an Exponential halten. Jeder Beitrag wird über das Contributor Licensing
Agreement an die Exponential Foundation und 7x übertragen; diese beiden sind die einzigen Rechteinhaber, und ein
Hinweis nennt Rechteinhaber. Die Menschen, die die Arbeit geleistet haben, werden in den Changelogs, in der
Commit-Historie und auf den Seiten zur Anerkennung mit Namen genannt; diese Nennung ist kein Anspruch auf
Urheberrecht.

**Hat sich am 29. Oktober 2026 die Lizenz geändert?**
Nein. Nur der Hinweis auf die Rechteinhaber hat sich geändert. Die Lizenz ist weiterhin die GNU General Public License,
Version 2 oder jede spätere Version, und ihr Text ist unverändert.

**Ich habe den alten Hinweis in meiner eigenen Dokumentation zitiert. Muss ich ihn ändern?**
Ihr Text gehört Ihnen. Für alles Neue empfehlen wir, den aktuellen Hinweis zu zitieren.

**Darf ich den Hinweis aus der Fußzeile meiner Website entfernen?**
Die GPL betrifft den Quelltext: Behalten Sie jeden Hinweis in den Dateien. Eine Fußzeile gehört zum Design Ihrer
Website, und eine angepasste Frontend-Fußzeile muss den Hinweis der Software nicht zeigen. Die Fußzeile der
Administration gehört zur Software; lassen Sie sie, wie sie ist.

## Weiterlesen

- `LICENSE` und `LICENSE.md`: der Lizenztext mit dem Hinweis der Rechteinhaber darüber.
- `COPYRIGHT.md`: der Copyright-Hinweis des Projekts mit dieser Geschichte.
- [Die Lizenz dieser Dokumentation](../README-LIZENZ.md)
- [Freie Dokumentation](freie-dokumentation.md)
- [Copyright- und Autorenzeilen einer Erweiterung](designer/erweiterungen/copyright-und-autorenzeilen.md)
- [Copyright-Hinweise in Dateien](designer/veroeffentlichung/copyright-hinweise-in-dateien.md)
- Der [Changelog von 6.0.15](../../changelogs/6.0/6.0.15.md#2026-10-29-one-copyright-notice-exponential-foundation-and-7x)
