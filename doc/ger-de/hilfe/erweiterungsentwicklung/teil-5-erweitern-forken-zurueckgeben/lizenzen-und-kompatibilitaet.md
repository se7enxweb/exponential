---
title: Lizenzen und wie sie sich kombinieren lassen
audience: developer, designer, app-creator
category: extension-development
order: 60
summary: Wie sich die Lizenzen, denen Sie in Erweiterungen begegnen, kombinieren lassen, wenn Sie Code zusammenführen - GPL 2 oder später, nur GPL 2, GPL 3, LGPL, MIT, BSD, Apache 2.0 - welche Mischungen erlaubt sind, welche nicht und wie Sie sie festhalten.
keywords: Lizenz, Kompatibilität, kombinieren, mitgelieferte Bibliothek, GPL-2.0-or-later, GPL-2.0-only, GPL-3.0, LGPL, MIT, BSD, Apache-2.0, SPDX, license, compatibility, combine, bundled library
type: help
---
# Lizenzen und wie sie sich kombinieren lassen

Teil 5, Kapitel 6. Wenn Sie Code aus mehreren Quellen in eine Erweiterung bringen (eine Bibliothek, ein Codeschnipsel,
die Klasse einer anderen Erweiterung), müssen deren Lizenzen zueinander passen. Teil 1 hat die Lizenzen einzeln
vorgestellt
([Lizenzen lesen, bevor Sie etwas wiederverwenden](../teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md),
mit der Tabelle „Combining licenses: a quick table“). Dieses Kapitel geht weiter: warum die Kombinationen so
funktionieren, wie sie funktionieren, und wie Sie festhalten, was Sie kombiniert haben.

## Die Lizenz des Kernels gibt den Rahmen vor

Exponential steht unter der GNU GPL Version 2 oder jeder späteren Version (die `CONTRIBUTING.md` des Projekts sagt,
dass Beiträge unter derselben Lizenz veröffentlicht werden). Eine Erweiterung läuft im Prozess des Kernels und ruft
dessen Klassen auf. Die übliche Sicht, und die der Free Software Foundation, ist daher, dass eine für Exponential
verbreitete Erweiterung unter einer Lizenz stehen muss, die mit der GPL kompatibel ist. Die einfachste Wahl für Ihre
eigene Erweiterung ist die des Kernels: GPL-2.0-or-later.

## Kompatibel mit einer GPL-Erweiterung

| Lizenz des Codes, den Sie übernehmen | In eine Erweiterung unter GPL-2.0-or-later? | Anmerkung |
|---|---|---|
| GPL-2.0-or-later | ja | dieselbe Lizenz |
| GPL-2.0-only | ja, aber das Ganze darf dann nur unter GPL 2 verbreitet werden | das „oder später“ Ihrer anderen Dateien lässt sich für die Kombination nicht nutzen |
| GPL-3.0-or-later | ja, aber das Ganze steht dann unter GPL 3 | Ihre Dateien unter GPL-2.0-or-later erlauben das |
| LGPL-2.1 oder LGPL-3.0 | ja | die LGPL erlaubt es |
| MIT, BSD-2-Clause, BSD-3-Clause | ja | Copyright-Hinweis und Lizenztext behalten |
| Apache-2.0 | nur mit GPL 3 | Apache 2.0 ist mit „nur GPL 2“ nicht kompatibel; mit GPL-2.0-or-later wird die Kombination als GPL 3 verbreitet |
| „All rights reserved“, keine Lizenz | nein | Sie haben überhaupt keine Erlaubnis |
| Creative Commons nicht-kommerziell (NC) oder ohne Bearbeitungen (ND) | nein | keine Lizenzen für freie Software |

Zwei Punkte, bei denen sich die meisten irren:

- **GPL-2.0-only und GPL-3.0 lassen sich nicht kombinieren.** Eine Bibliothek unter „nur GPL 2“ und eine andere
  unter „GPL 3“ dürfen nicht in dasselbe verbreitete Ganze. Ein „oder später“ auf Ihrer Seite ändert daran nichts.
- **Freizügig heißt nicht bedingungslos.** MIT- und BSD-Code darf in eine GPL-Erweiterung, aber seine Copyright-Zeile
  und sein Lizenztext müssen bei ihm bleiben.

## Dokumentation und Bilder

Lizenzen für Code sind für Code geschrieben. Für anderes Material:

| Material | Übliche Lizenzen |
|---|---|
| Dokumentation | GNU FDL 1.3 (die Lizenz dieses Buches), CC BY-SA 4.0 |
| Bilder, Icons | CC BY 4.0, CC BY-SA 4.0, die eigene Lizenz des Icon-Sets (der Kernel liefert Bootstrap Icons unter MIT mit) |
| Schriften | SIL Open Font License |

Bewahren Sie den Hinweis jedes Materials neben der Datei auf oder in einer Credits-Datei, die jede Datei nennt.

## Festhalten, was Sie kombiniert haben

Schreiben Sie es an drei Stellen auf:

1. **Neben dem Code.** Eine mitgelieferte Bibliothek behält ihre eigene `LICENSE`-Datei in ihrem Ordner.
2. **In `ezinfo.php`.** Ein `Includes`-Eintrag je enthaltener Software, mit Name, Version, Lizenz und Copyright. Die
   Seite „Über“ der Administration listet sie unter Ihrer Erweiterung auf
   ([ezinfo.php und extension.xml modernisieren](../../../../eng-us/help/extension-development/part-4-upgrading/modernizing-ezinfo-and-extension-xml.md)).
3. **In `composer.json`.** Das Feld `license` enthält die Lizenz Ihrer Erweiterung als SPDX-Bezeichner. Wenn das Ganze
   unter zwei Lizenzen verfügbar ist, ein Array: `"license": ["GPL-2.0-or-later", "MIT"]` bedeutet, dass der Benutzer
   eine von beiden wählen darf.

## Ein durchgerechnetes Beispiel

Sie schreiben eine Erweiterung unter GPL-2.0-or-later und möchten eine kleine JavaScript-Datumsbibliothek unter MIT
sowie eine PHP-Klasse aus einer anderen Erweiterung unter GPL-2.0-or-later aufnehmen:

- Die MIT-Bibliothek: erlaubt. Sie kommt mit ihrer `LICENSE`-Datei nach `design/standard/lib/datelib/` und erhält
  einen `Includes`-Eintrag.
- Die PHP-Klasse: erlaubt. Behalten Sie ihren Dateikopf mit dem ursprünglichen Copyright, fügen Sie Ihren Namen
  darunter hinzu, wenn Sie sie ändern, und nennen Sie die Erweiterung, aus der sie stammt, in einem `Includes`-Eintrag
  oder in den Credits Ihrer README.
- Ihre `composer.json`: `"license": "GPL-2.0-or-later"`.

Angenommen, die Bibliothek stünde unter Apache-2.0: Das wäre immer noch möglich, aber die kombinierte Erweiterung darf
dann nur unter GPL 3 verbreitet werden. Sagen Sie das in Ihrer README, oder wählen Sie eine andere Bibliothek.

## Im Zweifel

- [choosealicense.com](https://choosealicense.com) erklärt die üblichen Lizenzen in einfachen Worten.
- Die Free Software Foundation führt eine Liste von Lizenzen und ihrer Kompatibilität mit der GPL unter
  [gnu.org/licenses/license-list.html](https://www.gnu.org/licenses/license-list.html).
- Bei allem, wo es um Geld oder einen Streit geht, fragen Sie einen Anwalt. Diese Seite ist keine Rechtsberatung.

## Verwandte Seiten

- Zurück: [Ethisches Rebranding](ethisches-rebranding.md)
- Weiter: [Namensnennung und Urheberrechtshinweise](namensnennung-und-urheberrechtshinweise.md)
- [Lizenzen lesen, bevor Sie etwas wiederverwenden](../teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md)
- [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
