---
title: Lizenzen lesen, bevor Sie etwas wiederverwenden
audience: user, developer, designer, app-creator
category: extension-development
order: 150
summary: Die Lizenzen, denen Sie rund um Exponential begegnen, in einfachen Worten - GPL 2 oder später, GPL 3, MIT, GNU FDL, alle Rechte vorbehalten und keine Lizenz - und wo Sie sie finden. Keine Rechtsberatung.
keywords: Lizenz, Urheberrecht, Wiederverwendung, Kompatibilität, alle Rechte vorbehalten, keine Lizenz, license, licence, GPL, GPL-2.0-or-later, GPL-3.0, MIT, BSD, GFDL, all rights reserved, NOASSERTION, no license, copyright, reuse, compatibility
type: help
---
# Lizenzen lesen, bevor Sie etwas wiederverwenden

Teil 1, Kapitel 15. Jede Idee, die Sie in den anderen Kapiteln von Teil 1 finden, kommt mit einer Lizenz, und die
Lizenz entscheidet, was Sie mit dem Code oder Text tun dürfen. Dieses Kapitel erklärt die Lizenzen, denen Sie begegnen
werden, in einfachen Worten, und wo Sie sie in einer Erweiterung finden. Es ist ein praktischer Leitfaden, **keine
Rechtsberatung**; für eine echte Entscheidung in einem kommerziellen Projekt fragen Sie jemanden mit entsprechender
Qualifikation, und lesen Sie immer den Lizenztext selbst.

## Ideen sind frei, Code und Text nicht

Sie dürfen immer aus dem lernen, was Sie lesen, und dieselbe *Idee* selbst bauen, mit Ihren eigenen Worten und Ihrem
eigenen Code. Was eine Lizenz regelt, ist das **Kopieren**: des Codes, des Textes, der Bilder. Um das Kopieren geht es im
Rest dieses Kapitels.

## Wo Sie in einer Erweiterung nachsehen

| Ort | Was er Ihnen sagt |
|---|---|
| `LICENSE`, `LICENSE.md`, `COPYING`, `doc/LICENSE` | Den Lizenztext. Er ist maßgeblich. |
| `COPYRIGHT.md`, `doc/COPYRIGHT` | Wer das Copyright hält, oft mehrere Inhaber über die Jahre |
| `ezinfo.php` | Die Zeilen `'License'` und `'Copyright'`; ältere Erweiterungen nennen hier ihre ursprünglichen Autoren |
| `extension.xml` | `<license>` |
| `composer.json` | `"license"`, ein SPDX-Bezeichner wie `GPL-2.0-or-later` |
| Der Kopfkommentar jeder PHP-Datei | Zeilen `@copyright` und `@license` |
| Die Seitenleiste auf GitHub | Die Lizenz, die GitHub *erkennt*; eine Vermutung anhand der Lizenzdatei, keine Erklärung |

Wenn sich diese widersprechen, zählen der Lizenztext und die Dateiköpfe mehr als eine Zusammenfassungszeile.
`NOASSERTION` bei GitHub bedeutet, dass GitHub die Lizenzdatei nicht erkennen konnte, nicht, dass es keine gibt: Öffnen
Sie die Datei und lesen Sie sie.

## Die Lizenzen, denen Sie begegnen

### GNU GPL Version 2 oder später (die Lizenz des Kernels)

Exponential ist freie Software unter der GNU General Public License Version 2 oder, nach Ihrer Wahl, jeder späteren
Version (`COPYRIGHT.md` und `LICENSE.md` des Kernels). Die meisten Erweiterungen verwenden dieselbe Lizenz.

- Sie dürfen den Code für jeden Zweck verwenden, untersuchen, ändern und weitergeben, auch kommerziell.
- Wenn Sie den Code oder eine geänderte Fassung **verbreiten**, müssen Sie das unter derselben Lizenz tun, mit dem
  Quellcode, und die Copyright- und Lizenzhinweise beibehalten.
- Eine Website auf Ihrem eigenen Server zu betreiben, ist keine Verbreitung der Erweiterung an Ihre Besucher; die
  Erweiterung einem Kunden zu geben oder sie zu veröffentlichen, schon.

Lesen Sie sie unter [gnu.org/licenses/old-licenses/gpl-2.0.html](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html).

### GNU GPL nur Version 2

Manche Dateien sagen „version 2“ ohne „or later“ (GitHub zeigt `GPL-2.0-only`, zum Beispiel bei
`enhancedselection2`). Code unter „nur 2“ kann nicht in ein Werk unter GPL 3 eingebunden werden, weil keine der beiden
Lizenzen die Bedingungen der anderen zulässt. Wenn Sie Code aus mehreren Erweiterungen kombinieren, prüfen Sie dieses
Detail.

### GNU GPL Version 3 oder später

Zum Beispiel `owsimpleoperator` (seine README und `ezinfo.php` nennen Version 3 oder jede spätere Version). Code unter
GPL 3 kann mit Code unter „GPL 2 *oder später*“ kombiniert werden (die Kombination wird unter GPL 3 verbreitet), aber
nicht mit „nur GPL 2“.

### MIT- und BSD-artige Lizenzen

Kurze, freizügige Lizenzen: Sie dürfen fast alles, auch die Verwendung in unfreier Software, solange Sie den
Copyright-Hinweis und den Lizenztext beibehalten. Code unter MIT darf in eine GPL-Erweiterung aufgenommen werden;
behalten Sie seinen Hinweis in den kopierten Dateien bei. Einige Upstream-Pakete, aus denen Erweiterungen von
Exponential portiert wurden, stehen unter MIT: Packagist führt `netgen/layouts-core` und `netgen/query-translator` (die
Quellen von `explayouts_core` und `expquery_translator`) als MIT, während `netgen/site-bundle` und
`netgen/ibexa-site-api` als GPL-2.0-only geführt werden. Teil 5 zeigt, wie solche Portierungen ihre Quellen nennen
([Fallstudie: ein Paket aus einem anderen Projekt mit Quellenangabe portieren](../teil-5-erweitern-forken-zurueckgeben/fallstudie-aus-einem-anderen-projekt-portiert.md)).

### GNU Free Documentation License (für Dokumentation)

Diese Hilfe, eZpedia und zwei der freien Bücher verwenden die GNU FDL. Sie dürfen den Text kopieren, bearbeiten und
übersetzen, unter derselben Lizenz und mit Quellenangabe. Siehe [Freie Dokumentation](../../freie-dokumentation.md).

### „Alle Rechte vorbehalten“

Eine Website oder Datei, die nur „All rights reserved“ sagt, gibt Ihnen **keine** Erlaubnis zum Kopieren, über das
kurze Zitat hinaus, das das Urheberrecht selbst erlaubt. Verlinken Sie stattdessen. Die eigenen Seiten der
Projektplattform sind ein Beispiel dafür ([Exponential Projects](projects-exponential-earth.md)); die dort aufgeführten
Projekte haben ihre eigenen Lizenzen.

### Gar keine Lizenz

Code ohne jede Lizenzangabe darf nicht frei kopiert werden, auch wenn er öffentlich auf GitHub liegt. Bitten Sie den
Autor, eine Lizenz hinzuzufügen; bis dahin übernehmen Sie nur die Idee.

## Lizenzen kombinieren: eine kurze Tabelle

| Sie möchten in eine Erweiterung unter GPL 2 oder später aufnehmen | Erlaubt? |
|---|---|
| Code unter GPL 2 oder später | Ja |
| Code unter nur GPL 2 | Ja, aber die Kombination kann dann nicht mehr zu GPL 3 wechseln |
| Code unter GPL 3 oder später | Nur wenn die ganze Erweiterung dann unter GPL 3 verbreitet wird |
| Code unter MIT oder BSD | Ja, behalten Sie den Hinweis bei |
| Ein Forenbeitrag ohne Lizenz | Nur die Idee, außer der Autor ist einverstanden |
| Text unter der GNU FDL | In die Dokumentation ja, unter der GNU FDL; nicht in den Code |

Eine ausführlichere Erklärung, wie sich Lizenzen kombinieren lassen, finden Sie in der
[Lizenzliste](https://www.gnu.org/licenses/license-list.html) der FSF und auf
[choosealicense.com](https://choosealicense.com/licenses/).

## Die Lizenz Ihrer eigenen Erweiterung

Wählen Sie eine vor Ihrem ersten Commit und tragen Sie sie an jeder oben genannten Stelle ein. Die meisten
Erweiterungen in diesem Ökosystem verwenden „GNU General Public License v2.0 (or any later version)“, was Ihre Arbeit mit
dem Kernel und mit fast jeder anderen Erweiterung kombinierbar hält. Wie Sie sie in `ezinfo.php` und `extension.xml`
eintragen, steht in [ezinfo.php](../teil-3-erweiterungsentwickler/ezinfo-php.md); die ausführliche Darstellung ist
[Lizenzen und wie sie sich kombinieren lassen](../teil-5-erweitern-forken-zurueckgeben/lizenzen-und-kompatibilitaet.md).

## Checkliste vor dem Kopieren

- [ ] Ich habe den Lizenztext gefunden, nicht nur eine Zusammenfassung.
- [ ] Er erlaubt Kopieren und Ändern.
- [ ] Er ist mit der Lizenz meiner Erweiterung vereinbar.
- [ ] Ich behalte jede Copyright-Zeile und den Lizenzhinweis in den Dateien bei, die ich kopiere.
- [ ] Ich sage, woher der Code stammt (siehe [Namensnennung und Copyright-Hinweise](../teil-5-erweitern-forken-zurueckgeben/namensnennung-und-urheberrechtshinweise.md)).

## Verwandte Seiten

- Zurück: [Aus den freien Büchern lernen](aus-den-buechern-lernen.md)
- Weiter: [Verlinken oder kopieren: Quellen fair zitieren](verlinken-oder-kopieren.md)
- [Lizenzen und wie sie sich kombinieren lassen](../teil-5-erweitern-forken-zurueckgeben/lizenzen-und-kompatibilitaet.md)
- [Geben Sie Ihre Erweiterungen zurück](../../erweiterungen-zurueckgeben.md), [FAQ zur Lizenzierung](../../projekt/lizenz-faq.md)
- [Die Lizenz dieser Dokumentation](../../../README-LIZENZ.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
