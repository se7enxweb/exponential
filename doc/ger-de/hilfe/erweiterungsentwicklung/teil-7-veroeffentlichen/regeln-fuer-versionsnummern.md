---
title: Regeln für Versionsnummern
audience: developer, designer, app-creator
category: extension-development
order: 10
summary: Was die Zahlen der Version einer Erweiterung bedeuten, die Regel, dass die letzte Stelle weiterzählt (auf 1.0.9 folgt 1.0.10), wann Sie eine höhere Stelle erhöhen und wie Composer die Zahlen liest.
keywords: Version, Versionierung, Versionsnummer, semantische Versionierung, Major, Minor, Patch, letzte Stelle, Versionsangabe, Tilde, Caret, Tag-Name, version, versioning, semantic versioning, 1.0.10, last position, composer constraint, tilde, caret, tag name
type: help
---
# Regeln für Versionsnummern

Teil 7, Kapitel 1. Eine Versionsnummer sagt Benutzern, wie viel sich geändert hat und ob ein Update sicher ist. Dieses
Kapitel gibt die Regeln an, denen dieses Projekt bei seinen Erweiterungen folgt. Sie liegen nahe an
[Semantic Versioning](https://semver.org), mit einem Punkt, bei dem sich so viele irren, dass er einen eigenen
Abschnitt verdient.

## Die Stellen

| Stelle | Erhöhen Sie sie, wenn | Beispiel |
|---|---|---|
| Letzte (Patch) | Sie etwas korrigieren, Übersetzungen hinzufügen, Texte ändern und nichts, was vorher funktioniert hat, aufhört zu funktionieren | 1.0.8 auf 1.0.9 |
| Mittlere (Minor) | Sie eine Funktion oder eine API hinzufügen und alter Code weiter funktioniert | 1.0.9 auf 1.1.0 |
| Erste (Major) | etwas, das vorher funktioniert hat, aufhört zu funktionieren: eine entfernte Einstellung, ein umbenanntes Template, eine höhere PHP-Mindestversion, die Benutzer ausschließt | 1.4.2 auf 2.0.0 |

Manche Pakete dieses Projekts haben vier Stellen (`0.0.4.10`); die Regel ist dieselbe, angewandt auf die letzte.

## Die letzte Stelle zählt weiter

Nach `.9` kommt `.10`. Nicht die nächste mittlere Stelle.

```text
1.0.8  ->  1.0.9  ->  1.0.10  ->  1.0.11
```

Die Ziffern gehen nicht „aus“: Jede Stelle ist eine ganze Zahl. Eine höhere Stelle zu erhöhen, ist eine Entscheidung
darüber, was das Release bedeutet, nie eine Folge davon, dass die Neun erreicht ist. Dieses Projekt hat am
22. September 2026 `v0.0.4.10` nach `v0.0.4.9` veröffentlicht, und sowohl Packagist als auch GitHub ordnen es richtig
ein: Packagist vergleicht jede Stelle als Zahl, und GitHub Releases ordnet nach dem Datum des Tags.

Die eine Stelle, an der `.10` Ärger macht, ist das Sortieren von Tags nach Namen, das `1.0.10` vor `1.0.8` stellt. Das
ist ein Sortierproblem, gelöst in [Die nächste Versionsnummer wählen](die-naechste-version-waehlen.md).

## Tags und Versionen

| | Format | Beispiel |
|---|---|---|
| Git-Tag | die Version, meist mit einem `v` davor | `v1.0.10` |
| `ezinfo.php`, `extension.xml`, `composer.json` | die Version ohne `v` | `1.0.10` |

Manche Repositorys taggen ohne `v` (`4.1.5`). Wählen Sie einen Stil je Repository und bleiben Sie dabei; sehen Sie
sich vor dem ersten Release einer übernommenen Erweiterung die vorhandenen Tags an.

## Wie die Versionsangaben der Benutzer Ihre Nummern lesen

Benutzer schreiben Versionsangaben (Constraints) in ihre `composer.json`. Ihre Nummerierung entscheidet, was diese
Angaben übernehmen:

| Versionsangabe | Bedeutet | Übernimmt |
|---|---|---|
| `~1.0.9` | mindestens 1.0.9, unter 1.1.0 | 1.0.10, 1.0.11: Ihre Korrekturen |
| `^1.0` | mindestens 1.0.0, unter 2.0.0 | jedes 1.x: Korrekturen und Funktionen |
| `1.0.9` | genau diese | nichts Neues |

Eine inkompatible Änderung, die als `1.0.10` veröffentlicht wird, erreicht also alle mit `~1.0.9` oder `^1.0`, ohne
Vorwarnung. Deshalb muss sich die erste Stelle bewegen, wenn etwas kaputtgeht.

## Was keine Version ist

`dev-master`, `dev-main`, `//autogentag//` und eine leere Zeichenkette sind keine Versionen. Schreiben Sie sie nie in
`ezinfo.php` oder `extension.xml` ([ezinfo.php und extension.xml anheben](ezinfo-und-extension-xml-anheben.md)).

## Verwandte Seiten

- Zurück: [Teil 7: Ihre Erweiterung veröffentlichen](README.md)
- Weiter: [Die nächste Versionsnummer wählen](die-naechste-version-waehlen.md)
- [Semantic Versioning](https://semver.org)
- [Versions and constraints](https://getcomposer.org/doc/articles/versions.md) auf getcomposer.org (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
