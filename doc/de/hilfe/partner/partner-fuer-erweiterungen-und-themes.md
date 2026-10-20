---
title: Partner für Erweiterungen und Themes
audience: partner
category: partnership
order: 410
summary: Für Partner, die Erweiterungen, Themes und Designs für Exponential bauen und veröffentlichen - wie diese Arbeit zu den Partnerprofilen passt, das Ökosystem, dem sie sich anschließt, Geschäftsmodelle, die die GPL respektieren, Pflegepflichten und die Zusammenarbeit mit den ursprünglichen Autoren geforkter Erweiterungen.
keywords: Erweiterungspartner, Theme-Partner, Erweiterungen, Themes, Designs, Ökosystem, Packagist, Composer, Geschäftsmodell, Pflege, Fork, Upstream, extension partner, theme partner, extensions, ecosystem, business model, maintenance
type: help
---
# Partner für Erweiterungen und Themes

Diese Seite ist für Partner, deren Produkt eine Erweiterung, ein Theme oder ein Design für Exponential ist. Ein eigenes
Profil „Erweiterungspartner“ ist nicht veröffentlicht; solche Partner wählen meist das Profil IT (Erweiterungen) oder
das Profil Kreativ (Themes und Designs) ([Partnerprofile](partnerprofile.md)).

## Das Ökosystem, dem Sie sich anschließen

- `composer.json` des Kernels verlangt 66 Pakete `se7enxweb/*`, und der Vendor se7enxweb hat 219 Pakete auf Packagist
  ([Wer ist die Exponential Foundation?](../../../en/us/help/who-is-the-exponential-foundation.md#the-extension-ecosystem)).
- Jede Erweiterung ist ein Git-Repository und ein Composer-Paket; die Administration listet installierte Erweiterungen
  unter **Setup > Extensions** ([Erweiterungen](../../../en/us/guides/extensions.md#2-find-an-extension)).
- Das Community-Projektportal <https://projects.exponential.earth> beherbergt „von Benutzern erstellte und gepflegte Projekte für Erweiterungen, Plugins, Designs und Bundles“ (<https://exponential.earth/about/project>).

## Geschäftsmodelle, die die GPL respektieren

| Modell | Womit es verdient | Hinweise |
|---|---|---|
| Kostenlose Erweiterung, bezahlte Dienstleistungen | Installation, Anpassung, Support | Das häufigste Modell in diesem Ökosystem |
| Kostenlose Erweiterung, bezahltes Support-Abonnement | Garantierte Antworten und Updates | Veröffentlichen Sie die Support-Bedingungen klar |
| Gesponserte Entwicklung | Ein Kunde oder Sponsor bezahlt eine Funktion, die für alle veröffentlicht wird | Siehe [Funktionswünsche und gesponserte Funktionen](funktionswuensche-und-gesponserte-funktionen.md) |
| Bezahlte Verbreitung | Sie verlangen Geld für die Lieferung des Pakets | Unter der GPL darf der Empfänger es weitergeben |
| OEM-Wiederverkauf über das Programm | „Ihre eigenen Produkte auf Grundlage von Exponential weiterverkaufen“ | [OEM und Wiederverkauf](oem-und-wiederverkauf.md) |

Ob Ihre Erweiterung unter der GPL stehen muss, hängt davon ab, wie sie mit Exponential kombiniert wird; fragen Sie Ihre
Rechtsberatung. Die eigenen Erweiterungen des Projekts stehen unter der GPL ([Lizenzierung für Partner](lizenzierung-fuer-partner.md)).

## Seitenpakete: Bitte tragen Sie Ihre bei

7x und die Exponential Foundation ermutigen Partner ausdrücklich, ihre Seitenpakete (vollständige Website-Einrichtungen,
die der Setup-Assistent installiert) per Pull Request an
<https://github.com/se7enxweb/exponential.packages.exponential.earth> einzureichen. Dieses Repository ist der
Paketserver des Setup-Assistenten: „Installations- und Add-on-Pakete für Exponential CMS“ (Beschreibung des Repositorys).
Ein Paket, das Sie dort beitragen, lässt jede neue Installation mit Ihrer Arbeit beginnen und nennt Sie als Autor.

Bevor Sie eines senden, entfernen Sie alle Kundeninhalte, Namen und Zugangsdaten daraus und prüfen Sie die Lizenzen
jedes Bildes und jeder Schrift darin ([Ein Theme oder Design als Partner veröffentlichen](ein-theme-veroeffentlichen.md#assets-und-ihre-lizenzen)).
Das Repository zum Forken, der Ordneraufbau, die Checkliste und der Pull Request Schritt für Schritt:
[Ihr Seitenpaket beitragen](../seitenpakete/ihr-seitenpaket-beitragen.md).

## Pflegepflichten

Kunden verlassen sich jahrelang auf Erweiterungen. Veröffentlichen Sie nur, was Sie pflegen werden, oder sagen Sie
deutlich, dass Sie es nicht tun werden.

- Halten Sie sie auf den unterstützten PHP-Versionen lauffähig (8.0 bis 8.5 für 6.0.15).
- Befolgen Sie die Release-Regeln: Version in `ezinfo.php` und `extension.xml`, nie ein veröffentlichtes Tag verschieben
  ([Erweiterungen, „Release it“](../../../en/us/guides/extensions.md#7-release-it)).
- Führen Sie ein Änderungsprotokoll und beantworten Sie Issues.
- Kennzeichnen Sie aufgegebene Erweiterungen als solche und verweisen Sie auf eine Alternative.

## Forks und ursprüngliche Autoren

Viele Erweiterungen im Ökosystem setzen die Arbeit früherer Autoren fort. Wenn Sie eine forken oder aktualisieren:

1. Behalten Sie die ursprünglichen Copyright- und Lizenzhinweise.
2. Nennen Sie den ursprünglichen Autor in der README.
3. Bieten Sie Ihre Änderungen dem ursprünglichen Repository an, wenn es noch aktiv ist
   ([Erweiterungen an die Upstream-Projekte zurückgeben](erweiterungen-zurueckgeben.md)).
4. Benennen Sie nur um, wenn Sie müssen, und sagen Sie, woher es stammt.

## Verwandte Seiten

- [Eine Erweiterung als Partner veröffentlichen](eine-erweiterung-veroeffentlichen.md)
- [Ein Theme oder Design als Partner veröffentlichen](ein-theme-veroeffentlichen.md)
- [Erweiterungen](../../../en/us/guides/extensions.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
