---
title: Die nächste Versionsnummer wählen
audience: developer, designer, app-creator
category: extension-development
order: 20
summary: Sehen Sie nach, was schon veröffentlicht ist, bevor Sie eine Version wählen - die Tags holen, als Versionen sortieren, mit GitHub-Releases und Packagist vergleichen - damit Sie nie eine Nummer doppelt vergeben, mit den Befehlen und der Sortierfalle.
keywords: nächste Version, Versionsnummer, Tags, sortieren, veröffentlichte Versionen, Sortierfalle, next version, tags, git fetch --tags, sort -V, version:refname, gh release list, Packagist, published versions, sorting trap
type: help
---
# Die nächste Versionsnummer wählen

Teil 7, Kapitel 2. Die nächste Version ist „die letzte veröffentlichte Version plus eins“. Das klingt einfach, bis sich
herausstellt, dass eine Version schon vergeben ist: durch einen Tag, den Ihr Klon noch nicht geholt hatte, oder durch
ein Release, das jemand anderes gemacht hat. Dieses Kapitel zeigt, wie Sie die letzte veröffentlichte Version
zuverlässig finden.

## Schritt 1: jeden Tag holen

Ihr Klon kennt nur die Tags, die er geholt hat:

```bash
git fetch --tags
```

## Schritt 2: sie als Versionen sortieren

```bash
git tag -l 'v*' --sort=version:refname | tail -5
```

Verwenden Sie nie ein einfaches `git tag -l | tail -1`. Die Sortierung nach Namen stellt `v1.0.10` vor `v1.0.8`:

```bash
git tag -l 'v1.0.*' | tail -1                        # v1.0.9    wrong
git tag -l 'v1.0.*' --sort=version:refname | tail -1 # v1.0.10   right
git tag -l 'v1.0.*' | sort -V | tail -1              # v1.0.10   right
git describe --tags --abbrev=0                       # the newest tag on your branch's history
```

Lassen Sie sich fünf anzeigen, nicht einen: So sehen Sie das Muster und bemerken sofort eine Lücke oder einen
seltsamen Tag.

## Schritt 3: mit den veröffentlichten Releases vergleichen

Ein Tag kann ohne GitHub-Release existieren, und ein Release kann zu einem Tag existieren, den Ihr Klon nicht geholt
hat. Mit dem Kommandozeilenwerkzeug von GitHub:

```bash
gh release list --limit 10
```

Und auf Packagist listet die Seite des Pakets jede Version auf, die es kennt
(`https://packagist.org/packages/<vendor>/<name>`). Wenn Packagist eine ältere neueste Version zeigt als Ihre Tags,
siehe [Der Packagist-Webhook](der-packagist-webhook.md).

## Schritt 4: entscheiden

Nehmen Sie die höchste der drei und erhöhen Sie die Stelle, die zur Änderung passt
([Regeln für Versionsnummern](regeln-fuer-versionsnummern.md)):

| Neueste veröffentlichte | Änderung | Nächste |
|---|---|---|
| v1.0.9 | eine Korrektur | v1.0.10 |
| v1.0.10 | eine neue Einstellung, altes Verhalten unverändert | v1.1.0 |
| v1.1.0 | ein Template umbenannt | v2.0.0 |

Entscheiden Sie nach dem, was veröffentlicht ist, nicht aus dem Gedächtnis, nicht nach der Version in `ezinfo.php`
(sie kann voraus oder zurück sein) und nicht nach dem, was das vorige Release in Ihren Notizen sagt.

## In einem Skript

Wenn Sie Releases automatisieren, gilt dieselbe Regel für das Skript. Ein Shell-Skript, das „den neuesten Tag“
auswählt, muss mit `--sort=version:refname` oder `sort -V` sortieren:

```bash
#!/usr/bin/env bash
set -euo pipefail
git fetch --tags --quiet
last=$(git tag -l 'v*' --sort=version:refname | tail -1)
echo "Newest tag: ${last:-none}"
gh release list --limit 5
```

Das Skript zeigt nur an; die Person, die veröffentlicht, entscheidet.

## Verwandte Seiten

- Zurück: [Regeln für Versionsnummern](regeln-fuer-versionsnummern.md)
- Weiter: [Tags und Releases sind dauerhaft](tags-sind-dauerhaft.md)
- [git tag](https://git-scm.com/docs/git-tag) in der Git-Dokumentation (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
