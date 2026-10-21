---
title: "Inkompatible Änderungen und die nächste Major-Version"
audience: designer, developer
category: theme-lifecycle
order: 240
summary: Wenn eine Theme-Änderung Nutzer zum Handeln zwingt - entfernte Templates oder Einstellungen, eine angehobene Mindestversion, ein Redesign - fassen Sie sie zusammen, verschieben bewusst eine höhere Versionsstelle, schreiben eine Upgrade-Anleitung und halten die vorige Linie eine Zeit lang am Leben.
keywords: inkompatible Änderungen, Major-Version, Upgrade-Anleitung, Migration, Mindestversion, Redesign, Versionsstelle, breaking changes, major version, upgrade guide, minimum version, version position
type: help
---
# Inkompatible Änderungen und die nächste Major-Version

Diese Seite ist für Designer, die etwas ändern müssen, worauf sich Nutzer verlassen. Eine inkompatible Änderung ist eine,
nach der die Website eines Nutzers nicht mehr funktioniert oder anders aussieht, wenn er nicht handelt.

## Beispiele

| Inkompatibel | Nicht inkompatibel |
|---|---|
| Ein Template entfernen, das Nutzer überschreiben | Ein Template hinzufügen |
| Eine Einstellung umbenennen, ohne den alten Namen zu lesen | Eine Einstellung mit Standardwert hinzufügen |
| Die Mindestversion von Exponential oder PHP anheben | Zusätzlich eine neuere Version unterstützen |
| Ein Redesign des Layouts | Die Korrektur eines optischen Fehlers |

## Schritt für Schritt

1. **Vermeiden Sie sie, wenn Sie können**: Lassen Sie den alten Weg weiter funktionieren ([Eine Abkündigungsrichtlinie](abkuendigungsrichtlinie.md)).
2. **Fassen Sie** inkompatible Änderungen in einem Release zusammen, statt sie zu verteilen.
3. **Wählen Sie die Version bewusst**: Verschieben Sie eine höhere Stelle (zum Beispiel `1.x` zu `2.0.0`), damit Nutzer mit
   einer Bedingung wie `~1.0.24` sie nicht versehentlich erhalten ([Versionsbedingungen Ihrer Nutzer](../veroeffentlichung/versionsbedingungen-fuer-nutzer.md)).
4. **Schreiben Sie eine Upgrade-Anleitung** (`UPGRADE-2.0.md`): jede Änderung, was der Nutzer tun muss, der Reihe nach.
5. **Halten Sie die vorige Linie** für eine angegebene Zeit in reiner Sicherheitswartung ([Eine Support-Richtlinie](support-richtlinie-fuer-ihr-theme.md)).
6. **Kündigen Sie es an**, vor dem Release und zum Release.

## Verwandte Seiten

- [Eine Versionsnummer wählen](../veroeffentlichung/eine-versionsnummer-waehlen.md)
- [Kompatibilität angeben](kompatibilitaet-angeben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
