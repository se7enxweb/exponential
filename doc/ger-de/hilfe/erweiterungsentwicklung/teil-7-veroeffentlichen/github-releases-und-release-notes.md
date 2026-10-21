---
title: GitHub-Releases und Release Notes
audience: developer, designer, app-creator
category: extension-development
order: 80
summary: Machen Sie aus einem gepushten Tag ein GitHub-Release - Titel, Notes aus dem Changelog, was Sie anhängen und was nicht - mit dem Befehl gh und der Regel, dass ein veröffentlichtes Release nie so bearbeitet wird, dass sich ändert, was es ausliefert.
keywords: GitHub-Release, Release Notes, Tag, Changelog, Anhänge, Archiv, Entwurf, veröffentlichen, GitHub release, release notes, gh release create, notes, assets, archive, draft, publish
type: help
---
# GitHub-Releases und Release Notes

Teil 7, Kapitel 8. Ein Tag ist für Git und Composer. Ein GitHub-Release ist für Menschen: eine Seite mit Titel und
Notes, die auf der Startseite des Repositorys aufgeführt wird und über die Benutzer und Beobachter benachrichtigt
werden. Dieses Kapitel legt eines an.

## Was ein Release ist

Ein GitHub-Release gehört zu einem Tag. GitHub fügt die Quellarchive (zip und tar.gz) dieses Tags automatisch hinzu.
Sie fügen einen Titel und Notes hinzu und optional Dateien („Assets“).

## Anlegen

Nachdem der Tag gepusht ist ([Tags und Releases sind dauerhaft](tags-sind-dauerhaft.md)), mit dem
Kommandozeilenwerkzeug von GitHub:

```bash
gh release create v1.2.4 --title "1.2.4" --notes-file var/release-notes-1.2.4.md
```

oder auf der Website: **Releases > Draft a new release**, den vorhandenen Tag wählen, Titel und Notes schreiben,
**Publish**.

Verwenden Sie die Version als Titel, oder die Version und ein kurzes Thema („1.2.4: Content Security Policy“).

## Die Notes

Die Notes sind der Changelog-Eintrag dieser Version ([Changelogs](changelogs.md)), plus zwei Dinge, die auf eine
Release-Seite passen:

```markdown
Upgrade notes
- Clear the template override cache after updating: two templates were added.

Fixed
- The parameter file shown after a conversion is escaped.

Updated
- The context menu entries carry no inline handlers, so they work under a Content Security Policy.

Requires: Exponential 6.0.15 or later, PHP 8.0 or later.
Install or update: composer require your-account/myext:^1.2
```

- **Die Voraussetzungen** in einer Zeile.
- **Der Installationsbefehl**, damit ein Leser handeln kann, ohne die README zu öffnen.

Schreiben Sie keine privaten Pfade, Hostnamen oder Namen interner Werkzeuge in die Notes; sie sind öffentlich.

## Anhänge

Die meisten Erweiterungen brauchen keine: Composer installiert aus dem Tag, und die eigenen Archive von GitHub
enthalten den Quellcode. Hängen Sie eine Datei nur an, wenn Benutzer etwas brauchen, das der Quellcode ihnen nicht
gibt, zum Beispiel ein `.ezpkg`-Paket ([Pakete (.ezpkg) und der Paket-Ersteller](pakete-ezpkg.md)) oder ein gebautes
JavaScript-Bundle, das nicht committet ist.

## Ändern Sie nie, was ein Release ausliefert

Den Text eines veröffentlichten Releases zu bearbeiten (ein Tippfehler in den Notes), ist harmlos. Einen Anhang durch
einen mit anderem Inhalt zu ersetzen oder einen hinzuzufügen, ist es nicht: Es gibt zwei verschiedenen Artefakten
dieselbe Version, genau wie das Verschieben eines Tags. Wenn ein Anhang falsch war, veröffentlichen Sie die nächste
Version.

## Entwürfe

`gh release create ... --draft` oder **Save draft** auf der Website hält das Release unsichtbar, bis Sie es
veröffentlichen. Nutzen Sie das, um die Notes vorzubereiten, während die letzten Prüfungen laufen. Der Tag muss
trotzdem stimmen, bevor er gepusht wird: Ein Entwurf schützt keinen Tag.

## Verwandte Seiten

- Zurück: [Changelogs](changelogs.md)
- Weiter: [filelist.md5 und die Upgrade-Prüfung](filelist-md5-und-die-upgrade-pruefung.md)
- [About releases](https://docs.github.com/en/repositories/releasing-projects-on-github/about-releases) in der GitHub-Dokumentation (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
