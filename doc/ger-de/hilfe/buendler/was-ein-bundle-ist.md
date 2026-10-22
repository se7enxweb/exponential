---
title: Was ein Bundle ist
audience: bundler, partner
category: bundling
order: 10
summary: Was als Bundle von Exponential gilt, aus welchen Teilen jedes Bundle besteht, was sich rechtlich und praktisch ändert, sobald Sie ein Bundle an andere weitergeben, und welche Fragen Sie vor dem Bauen beantworten sollten.
keywords: Bundle, bündeln, Distribution, Weitergabe, Image, Installer, Starter-Kit, Appliance, Teile eines Bundles, Empfänger, bundle, bundling, redistribution, installer, starter kit, recipient, conveying, GPL
type: help
---
# Was ein Bundle ist

Diese Seite richtet sich an alle, die Exponential mit anderen Dingen zusammenstellen und das Ergebnis an andere
weitergeben wollen. Sie definiert das Wort "Bundle", wie dieser Bereich es verwendet, zählt die Teile auf, aus denen
ein Bundle besteht, und erklärt, warum der Moment der Übergabe zählt.

## Inhalt

1. [Eine Arbeitsdefinition](#eine-arbeitsdefinition)
2. [Die Teile eines Bundles](#die-teile-eines-bundles)
3. [Wann ein Bundle zur Weitergabe wird](#wann-ein-bundle-zur-weitergabe-wird)
4. [Was ein Bundle nicht ist](#was-ein-bundle-nicht-ist)
5. [Fragen, die Sie zuerst beantworten sollten](#fragen-die-sie-zuerst-beantworten-sollten)

## Eine Arbeitsdefinition

Ein **Bundle** ist Exponential, kombiniert mit anderer Software, Konfiguration oder Inhalten, von Ihnen vorbereitet
und an jemanden außerhalb Ihrer eigenen Organisation übergeben, bereit zur Installation oder bereit zum Betrieb. Der
Empfänger kann ein Kunde sein, ein Hosting-Kunde, ein Student, der Nutzer einer Distribution oder die Öffentlichkeit.

Das Wort umfasst sehr unterschiedliche Formen ([Arten von Bundles](arten-von-bundles.md)): ein Container-Image, ein
Cloud-Server-Image, den Ein-Klick-Installer eines Hosting-Panels, ein Debian- oder RPM-Paket, das Starter-Kit einer
Agentur in einem Git-Repository, eine Platform-Distribution, ein Seitenpaket, ein Erweiterungs-Bundle, eine Hardware-
oder virtuelle Appliance, ein Schulungs-Image oder eine öffentliche Demo. Gemeinsam ist ihnen: **Sie entscheiden, was
darin ist, und jemand anderes erhält es**.

## Die Teile eines Bundles

| Teil | Typischer Inhalt | Woher er kommt | Seite |
|---|---|---|---|
| Der Kernel | `se7enxweb/exponential` in einer festgelegten Version | Git-Tag oder Composer | [Den Code beschaffen](den-code-fuer-ein-bundle-beschaffen.md) |
| Erforderliche Erweiterungen | die `require`-Liste der `composer.json` des Kernels | Composer | [Exponential 6 als Basis](exponential-6-als-basis.md) |
| Ihre Erweiterungen | Ihr eigener Code, Erweiterungen Dritter | Ihre Repositorys, Packagist | [Erweiterungen hinzufügen und entfernen](erweiterungen-hinzufuegen-und-entfernen.md) |
| Ein Seitenpaket | Inhaltsklassen, Inhalte, Rollen, Einstellungen, Installationsskripte (`.ezpkg`) | der Paketserver oder Ihr eigener | [Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md) |
| Installer-Konfiguration | `kickstart.ini`-Vorlage, `exp:install`-Optionen | Sie | [Die drei Installer](die-drei-installer.md) |
| Laufzeitumgebung | PHP und seine Erweiterungen, ein Webserver oder Velocity, eine Datenbank | das Betriebssystem, Images, Composer | [Webserver- und Engine-Wahl](webserver-und-engine-wahl.md) |
| Betriebssystem und Werkzeuge | Basis-Image, Benutzer, Cron, Backups, Überwachung | Ihr Image oder Paket | [Server- und VM-Images](server-und-vm-images.md) |
| Dokumentation und Hinweise | Lizenztexte, Urheberrechtshinweise, diese Hilfe, Ihre eigenen Anleitungen | das Repository und Sie | [Mitzuliefernde Lizenztexte und Hinweise](mitzuliefernde-lizenztexte-und-hinweise.md) |

Schreiben Sie die Liste für Ihr eigenes Bundle einmal auf, im [Arbeitsblatt für Bundles](arbeitsblatt-fuer-bundles.md). Jede
spätere Entscheidung (Updates, Support, Lizenzierung) hängt davon ab.

## Wann ein Bundle zur Weitergabe wird

Der Code von Exponential steht unter der GNU General Public License, Version 2 oder später (`composer.json`
`"license": "GPL-2.0-or-later"`, `COPYRIGHT.md`). Die Lizenz erlaubt jedem, die Software ohne Bedingungen
auszuführen, zu untersuchen und zu ändern. Ihre Bedingungen greifen, wenn Sie Kopien **weitergeben**: Ab dann müssen
Sie die Lizenz, die Hinweise und den Quellcode weitergeben, und Sie dürfen keine Einschränkungen hinzufügen
([Lizenzierung für Partner](../partner/lizenzierung-fuer-partner.md#was-partner-bei-der-verbreitung-tun-müssen)).

| Situation | Weitergabe? |
|---|---|
| Sie betreiben eine Website für einen Kunden auf Ihren eigenen Servern | Es wird keine Kopie übergeben: Ihre Pflichten ergeben sich aus Ihrem Vertrag, nicht aus einer Weitergabe. |
| Sie übergeben dem Kunden ein Image, einen Container, ein Paket oder ein Repository | Ja. |
| Ein Hosting-Panel lädt Exponential herunter und installiert es im Konto des Kunden | Fragen Sie einen Rechtsbeistand: Das hängt davon ab, wer die Kopie anfertigt und woher. |
| Sie veröffentlichen einen Demo-Download für alle | Ja. |

Diese Tabelle ist eine Erinnerung, keine Rechtsberatung. Die Einzelheiten stehen unter
[GPL-Pflichten beim Weitergeben](gpl-pflichten-beim-weitergeben.md).

## Was ein Bundle nicht ist

- **Kein Fork.** Ein Bundle verwendet veröffentlichtes Exponential und ergänzt es. Wenn Sie Kernel-Dateien ändern,
  pflegen Sie einen Fork; reichen Sie die Änderung stattdessen beim Projekt ein
  ([Korrekturen zurückgeben](bundle-korrekturen-zurueckgeben.md)).
- **Kein neuer Produktname für Exponential.** Ein Bundle ist "basierend auf Exponential"
  ([Ein Bundle benennen und gestalten](ein-bundle-benennen-und-gestalten.md)).
- **Keine Lizenz.** Niemand kann eine "Exponential-Lizenz" verkaufen; Sie verkaufen das Bundle, Ihre Arbeit und
  Ihren Service ([Lizenzierung für Käufer erklärt](../geschaeft/lizenzen-fuer-kaeufer.md)).
- **Keine installierte Website.** Ein Bundle sollte die Website beim ersten Start installieren, nicht die
  Live-Datenbank und die Geheimnisse eines anderen mitbringen
  ([Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md)).

## Fragen, die Sie zuerst beantworten sollten

- [ ] Wer erhält das Bundle, und wie (Download, Image-Registry, Panel, Paket-Repository)?
- [ ] Welche Exponential-Version, und wie wird sie nach dem Release aktualisiert?
- [ ] Welche Erweiterungen und welches Seitenpaket kommen hinein, und unter welchen Lizenzen?
- [ ] Welche Datenbank und welchen Webserver oder welche Engine verwendet es?
- [ ] Wie erhält der Empfänger das Administratorpasswort, und wie wird es geändert?
- [ ] Wo veröffentlichen Sie den zugehörigen Quellcode?
- [ ] Wer unterstützt was: Sie, 7x, die Community?

## Verwandte Seiten

- [Arten von Bundles](arten-von-bundles.md)
- [Bündler: hier beginnen](buendler-hier-beginnen.md)
- [Distributionspartner und Wiederverkäufer](../partner/vertriebspartner.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
