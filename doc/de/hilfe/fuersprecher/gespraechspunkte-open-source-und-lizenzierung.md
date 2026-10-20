---
title: "Gesprächspunkte: Open Source und Lizenzierung"
audience: advocate
category: advocacy
order: 15
summary: Wie Sie korrekt über die Lizenzen von Exponential sprechen, die GPL v2 oder später für den Code und die GNU FDL 1.3 oder später für die Dokumentation, was sie erlauben und verlangen, wie Beiträge lizenziert werden und welche Fehler Sie vermeiden sollten.
keywords: Gesprächspunkte, Open Source, freie Software, GPL, GPLv2, GNU General Public License, GFDL, GNU Free Documentation License, Lizenz, Copyright, Beiträge, Freiheit, Anbieterbindung, talking points, open source, free software, license, copyright, contributions, freedom, lock-in
type: help
---
# Gesprächspunkte: Open Source und Lizenzierung

Diese Seite ist für Fürsprecher, die darüber sprechen, warum Exponential freie Software ist und was das in der Praxis
bedeutet. Lizenzen sind Rechtstexte; sagen Sie, was sie sind, verweisen Sie auf sie und überlassen Sie Rechtsberatung
den Fachleuten.

> **Warnung**
> Diese Seite ist keine Rechtsberatung. Zitieren Sie die Lizenztexte und die eigenen Aussagen des Projekts; bei einer
> rechtlichen Frage verweisen Sie die Person auf den Lizenztext und ihre eigene Beratung.

## Inhalt

1. [Die zwei Lizenzen](#die-zwei-lizenzen)
2. [Die Punkte](#die-punkte)
3. [Beiträge und ihre Lizenz](#beiträge-und-ihre-lizenz)
4. [Fehler, die Sie vermeiden sollten](#fehler-die-sie-vermeiden-sollten)
5. [Fragen, die Sie hören werden](#fragen-die-sie-hören-werden)

## Die zwei Lizenzen

| Was | Lizenz | Quelle |
|---|---|---|
| Der Code von Exponential | GNU General Public License, Version 2 oder jede spätere Version | [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE), [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| Die Dokumentation in `doc/` | GNU Free Documentation License, Version 1.3 oder jede spätere Version | [Die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md) |
| Copyright | „7x & Exponential Foundation“ (mit „eZ Systems AS“ für 1999 bis 2014) | [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) |
| Velocity | MIT-Lizenz (seine eigene `composer.json`) | [Exponential Velocity](../wer-ist-die-exponential-foundation.md#exponential-velocity) |

Erweiterungen haben jeweils ihre eigene Lizenz; lesen Sie die Dateien der Erweiterung ([Erweiterungen](../../anleitungen/extensions.md)).

## Die Punkte

| Sagen Sie | Quelle |
|---|---|
| „Exponential ist freie Software unter der GNU GPL Version 2 oder später. Sie dürfen es ausführen, studieren, ändern und weitergeben.“ | [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE) |
| „Die Klausel ‚oder später‘ erlaubt dem Projekt, auf künftige Versionen der GPL zu wechseln.“ | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| „Auch die Dokumentation ist frei: Sie dürfen sie unter der GNU FDL kopieren, übersetzen, anpassen und weitergeben.“ | [Freie Dokumentation](../freie-dokumentation.md) |
| „Für die Software gibt es keine Lizenzgebühr.“ | [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE) |
| „Weil der Code frei ist, kann kein einzelnes Unternehmen ihn wegnehmen. So hat er seinen ursprünglichen Hersteller überlebt.“ | [Mit Einwänden umgehen, Langlebigkeit](../geschaeft/einwandbehandlung.md#langlebigkeit-gibt-es-das-in-zehn-jahren-noch) |
| „Die vollständige Historie seit 2002 ist öffentlich: Jeder kann lesen, wer was warum geändert hat.“ | [Grundsätze, die man im Code sieht](../wer-ist-7x.md#grundsätze-die-sie-im-code-sehen-können) |
| „Beiträge bleiben frei: Änderungen werden unter derselben GPL veröffentlicht.“ | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |

## Beiträge und ihre Lizenz

- **Code**: „Mit Ihrem Beitrag stimmen Sie zu, dass Ihre Beiträge unter der GNU General Public License, Version 2.0 oder
  später, lizenziert werden“ ([CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md)).
- **Dokumentation**: „Eine Änderung an einer Seite unter dem obigen Hinweis wird unter derselben Lizenz angenommen, der
  GNU Free Documentation License 1.3 oder jeder späteren Version“ ([Zur Dokumentation beitragen](../../../en/us/README-LICENSE.md#contributing-to-the-documentation)).
- **Text, der von anderswo kopiert wurde** und eine andere Lizenz hat, muss das in seinem Pull Request sagen (dieselbe
  Quelle). Kopieren Sie nie einen Blogbeitrag oder eine Seite einer anderen Website in die Hilfe, ohne ihre Lizenz zu
  prüfen ([Quellen zitieren und Ihre Texte lizenzieren](quellen-zitieren-und-texte-lizenzieren.md)).

## Fehler, die Sie vermeiden sollten

| Sagen Sie nicht | Sagen Sie stattdessen | Warum |
|---|---|---|
| „Es ist Freeware“ oder „gemeinfrei“ | „Es ist freie Software unter der GNU GPL“ | Die GPL hat Bedingungen ([Marken- und Namensregeln, Tun und Lassen](../geschaeft/marken-und-namensregeln.md#richtig-und-falsch)) |
| „Sie können damit alles machen“ | „Sie dürfen es unter den Bedingungen der GPL ausführen, studieren, ändern und weitergeben“ | Die Weitergabe geänderter Fassungen hat Bedingungen |
| „Sie müssen den Code Ihrer Website veröffentlichen“ | „Lesen Sie die Lizenz; fragen Sie für Ihren Fall eine Rechtsberatung“ | Das ist eine Rechtsfrage zur Verbreitung |
| „7x gehört Exponential“ | „7x und die Exponential Foundation halten das Copyright; die Lizenz ist die GPL“ | [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) |
| „Licence“ (in englischen Texten) | „License“ | Amerikanisches Englisch ([Kurzkarte zu den Namensregeln](kurzkarte-namensregeln.md)) |

## Fragen, die Sie hören werden

| Frage | Antwort |
|---|---|
| „Dürfen wir es kommerziell nutzen?“ | Die GPL erlaubt kommerzielle Nutzung. Bedingungen gelten, wenn Sie es verbreiten. Lesen Sie die Lizenz; fragen Sie für Ihren Fall Ihre Beratung. |
| „Dürfen wir geschlossene Erweiterungen bauen?“ | Das ist eine Rechtsfrage zur GPL; verweisen Sie auf den Lizenztext und eine Beratung. Beantworten Sie sie nicht selbst. |
| „Darf ich eine Hilfeseite in unser Intranet kopieren?“ | Ja, unter den Bedingungen der GNU FDL: Behalten Sie den Hinweis und die Lizenz bei ([Freie Dokumentation](../freie-dokumentation.md#kopieren-und-an-andere-benutzer-weitergeben)). |
| „Darf ich die Hilfe übersetzen?“ | Ja, und wer die Übersetzung zurückgibt, hilft allen ([Freie Dokumentation](../freie-dokumentation.md#übersetzen)). |

## Verwandte Seiten

- [Für Open Source eintreten](fuer-open-source-eintreten.md)
- [Für freie Dokumentation eintreten](fuer-freie-dokumentation-eintreten.md)
- [Lizenzierung für Käufer](../geschaeft/lizenzen-fuer-kaeufer.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
