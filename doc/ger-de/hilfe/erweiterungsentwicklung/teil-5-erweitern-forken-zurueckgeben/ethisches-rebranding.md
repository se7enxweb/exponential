---
title: Ethisches Rebranding
audience: developer, designer, app-creator
category: extension-development
order: 50
summary: Was Sie tun dürfen und was nicht, wenn Sie Ihren eigenen Namen auf einen Fork setzen - neue Namen, erhaltene Namensnennung, ehrliche Beschreibungen, keine geliehenen Marken - mit einer Checkliste und Beispielen aus Erweiterungen, die dieses Projekt geforkt hat.
keywords: Rebranding, umbenennen, Fork, Namensnennung, Ethik, Marke, Ehrlichkeit, ursprünglicher Autor, Beschreibung, rebranding, rename, fork, credit, ethics, trademark, attribution, honesty, original author, description
type: help
---
# Ethisches Rebranding

Teil 5, Kapitel 5. Lizenzen für freie Software erlauben Ihnen, Code zu kopieren, zu ändern und unter Ihrem eigenen
Namen neu zu veröffentlichen. Sie erlauben Ihnen nicht, so zu tun, als hätten Sie ihn geschrieben, und sie geben Ihnen
keine fremde Marke. Zwischen diesen beiden Grenzen gibt es viel Raum, und dieses Kapitel zeigt, wie Sie ihn ehrlich
nutzen.

## Was die Lizenz erlaubt

Die Lizenzen GPL, MIT und BSD erlauben Ihnen alle:

- eine geänderte Kopie unter einem neuen Namen zu veröffentlichen;
- sie zu verkaufen oder Dienstleistungen darum herum;
- sie für immer getrennt vom Original zu pflegen.

## Was sie nicht erlaubt oder verlangt

| Regel | Woher sie kommt |
|---|---|
| Die vorhandenen Urheberrechtshinweise behalten | jede übliche freie Lizenz verlangt es |
| Die Lizenz behalten (GPL: dieselbe Lizenz für das Ganze) | die Lizenz selbst |
| Kennzeichnen, dass Sie die Dateien geändert haben (GPL 2: „prominent notices stating that you changed the files and the date“) | GPL 2, Abschnitt 2a |
| Die Marke oder das Logo des Originals nicht so verwenden, dass der Eindruck entsteht, es sei das Original | Markenrecht, nicht die Lizenz |

## Was Fairness darüber hinaus verlangt

Die Lizenz ist das Minimum. Fairness verlangt etwas mehr:

- **Sagen Sie, was es ist.** „Ein Fork von X“ in der Beschreibung und in der README. Die Beschreibung in der
  `composer.json` von se7enxweb/expchangeclass endet mit „A hardened fork of eZChangeclass“, und ihre README beginnt
  ihre Erklärung mit „This is a fork of eZChangeclass“ und einem Link.
- **Sagen Sie, warum.** Ein kurzer Abschnitt darüber, warum es den Fork gibt und was er ändert. Benutzer verdienen es,
  wählen zu können.
- **Nennen Sie die Menschen.** Die ursprünglichen Autoren bleiben in `authors` der `composer.json` (expchangeclass gibt
  dem ursprünglichen Autor die Rolle „Original author of eZChangeclass“) und in einem `Includes`-Eintrag der
  `ezinfo.php`.
- **Verbergen Sie nicht die Historie.** Forken Sie mit Git, damit die ursprünglichen Commits und ihre Autoren im Log
  bleiben.
- **Erwecken Sie nicht den Anschein einer Billigung.** „Funktioniert mit X“ ist in Ordnung; „das offizielle X“ nicht,
  es sei denn, die Inhaber von X sagen es.

## Namen in der Praxis

| Situation | Ein guter Name |
|---|---|
| Sie pflegen eine PHP-8-Version einer verwaisten Erweiterung | ein neuer Paketname unter Ihrem Konto; der Anzeigename darf „for Exponential 6“ sagen |
| Ihr Fork schlägt eine andere Richtung ein | ein eigener neuer Name, mit „basiert auf X“ in der Beschreibung |
| Ein vorübergehender Fork für einen Pull Request | gar kein Rebranding: alles so lassen, wie es ist |

## Produktnamen im Code

Den Produktnamen in Oberflächentexten und Dokumentation umzubenennen, gehört zu einem Upgrade
([Namen und Marken bei einem Upgrade](../teil-4-aktualisieren/namen-und-marken-bei-einem-upgrade.md)).
Die Klassennamen, Operatoren oder Einstellungen des ursprünglichen Autors umzubenennen, ist kein Rebranding: Es macht
jede Website kaputt, die sie verwendet, und es löscht eine sichtbare Spur dessen, woher der Code stammt. Behalten Sie
sie.

## Logos und Designs

Ein Theme oder eine Erweiterung kann das Logo der ursprünglichen Firma in Bildern enthalten. Ersetzen Sie Logos, die
eine Firma oder ein Produkt kennzeichnen, das Sie nicht sind; behalten Sie Lizenz und Copyright jedes Bildes, das Sie
behalten. Ein Logo fällt in der Regel überhaupt nicht unter die Lizenz des Codes.

## Checkliste

- [ ] Paketname und Anzeigename sind neu und erwecken nicht den Eindruck, Sie seien das Original.
- [ ] Beschreibung und README sagen „Fork von“ oder „basiert auf“, mit einem Link.
- [ ] Jede Copyright-Zeile ist erhalten; Ihre ist daneben hinzugefügt.
- [ ] Die Lizenz ist unverändert (oder nur so geändert, wie die Lizenz es erlaubt, zum Beispiel von GPL-2.0-or-later
  zu GPL-3.0).
- [ ] Die ursprünglichen Autoren stehen in `composer.json` und `ezinfo.php`.
- [ ] Kein Logo und keine Marke des Originals wird als Ihre eigene verwendet.
- [ ] Die Git-Historie ist erhalten.

## Verwandte Seiten

- Zurück: [Einen Fork mit seiner Quelle synchron halten](einen-fork-synchron-halten.md)
- Weiter: [Lizenzen und wie sie sich kombinieren lassen](lizenzen-und-kompatibilitaet.md)
- [Namensnennung und Urheberrechtshinweise](namensnennung-und-urheberrechtshinweise.md)
- [The GNU GPL version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) auf gnu.org (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
