---
title: Pakete mit dem Assistenten erstellen
audience: developer, designer, admin
category: install-upgrade
order: 90
summary: Was der Paketassistent in der Administration für Ihr Seitenpaket erstellen kann (Inhaltsklassen, Inhalte, Erweiterungen, Seitenstile), was er nicht erstellen kann (das Seitenpaket selbst) und wie Sie seine Ausgabe in Ihr Quell-Repository und in die Anforderungen Ihres Seitenpakets bringen.
keywords: Paketassistent, package/create, Neues Paket erstellen, Export von Inhaltsklassen, Export von Inhaltsobjekten, Extension-Export, Seitenstil, ezcontentclass, ezcontentobject, ezextension, ezstyle, lokales Repository, requires, package wizard, create new package, content class export, content object export, extension export, site style, local repository
type: help
---
# Pakete mit dem Assistenten erstellen

Diese Seite ist für Entwickler und Designer, die die Teile eines Seitenpakets im Browser bauen. Der Paketassistent
(**Setup > Pakete > Neues Paket erstellen**, `/package/create`) exportiert Klassen, Inhalte, Erweiterungen oder einen Seitenstil aus einer
laufenden Installation in ein neues Paket. Er ist der einfachste Weg, die Pakete zu erzeugen, die Ihr Seitenpaket verlangt; das Seitenpaket
selbst erzeugt er nicht.

![Paket erstellen, der Anfang jedes hier erstellten Pakets](../../../../eng-us/help/images/help/sp-own-wizard.webp)

7x und die Exponential Foundation ermutigen Sie ausdrücklich, das fertige Seitenpaket per Pull Request an
se7enxweb/exponential.packages.exponential.earth einzureichen: siehe [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Inhalt

1. [Was der Assistent erstellen kann](#was-der-assistent-erstellen-kann)
2. [Was er nicht erstellen kann](#was-er-nicht-erstellen-kann)
3. [Ein Paket erstellen](#ein-paket-erstellen)
4. [Der Schritt der Informationen](#der-schritt-der-informationen)
5. [Vom Assistenten in Ihr Quell-Repository](#vom-assistenten-in-ihr-quell-repository)

## Was der Assistent erstellen kann

`package.ini [CreationSettings] HandlerList[]` nennt die Ersteller; jeder erstellt einen Pakettyp (`kernel/classes/packagecreators/`):

| Assistent | Ersteller | Pakettyp | Installationstyp | Nutzung in einem Seitenpaket |
|---|---|---|---|---|
| Export von Inhaltsklassen | `ezcontentclass` | `contentclass` | install | Ihr Klassenpaket, wie `sevenx_classes` |
| Export von Inhaltsobjekten | `ezcontentobject` | `contentobject` | install | Ihr Demo-Inhaltspaket, wie `sevenx_multisite_democontent` |
| Extension-Export | `ezextension` | `extension` | install | eine als Paket ausgelieferte Erweiterung |
| Seitenstil | `ezstyle` | `sitestyle` | import | zwei Stylesheets, Bilder und ein Vorschaubild |

Der Export von Inhaltsobjekten lässt sich auch aus dem Inhaltsbaum öffnen (**Fortgeschritten > Exportieren**), mit dem Knoten und einigen
Wahlen bereits ausgefüllt: `/package/create/(creator)/ezcontentobject/(node)/<node id>`, wahlweise mit `(subtree)/1`, `(classes)/0|1` und
`(versions)/current|all` (`kernel/package/module.php`).

## Was er nicht erstellen kann

Keiner der vier Ersteller schreibt ein Paket vom Typ `site`, und keiner schreibt `<settings>` oder Dateien eines Installers. Das Seitenpaket
(seine `package.xml` mit `<type value="site" />`, sein `<requires>` und sein Ordner `settings/`) entsteht durch Forken eines vorhandenen
([Ein Seitenpaket forken](ein-seitenpaket-forken.md)) und wird von Hand bearbeitet.

## Ein Paket erstellen

1. Öffnen Sie **Setup > Pakete > Neues Paket erstellen**. Nur die Assistenten, die Ihre Rolle nutzen darf, werden gezeigt (`package/create`,
   eingeschränkt nach Typ, Ersteller und Rolle; siehe [Berechtigungen für Pakete](../admin-oberflaeche/berechtigungen-fuer-pakete.md)).
2. Wählen Sie einen und drücken Sie **Assistent starten**.
3. Wählen Sie, was hineinkommt: Klassen in einer Tabelle mit Filter, Knoten und Teilbäume über den Inhaltsbrowser, Erweiterungen mit ihren
   Details. Bei Inhalten wählen Sie außerdem, ob Klassen und Templates mitgehen, welche Versionen, welche Sprachen und wie Knotenzuweisungen
   und verwandte Objekte behalten werden.
4. Füllen Sie die Paketinformationen, den Betreuer (ausgelassen, wenn Ihre Rolle keine Betreuerrolle vergeben darf) und den Changelog aus.
5. Prüfen Sie die Überprüfung im letzten Schritt und drücken Sie **Paket erstellen**.

Die Fassung Schritt für Schritt mit jedem Feld ist
[Der Paketassistent Schritt für Schritt](../../designer/erweiterungen/paketerstellungs-assistent-schritt-fuer-schritt.md); der neu gestaltete Assistent
selbst ist auf [Der Paketassistent](../../../funktionen/6.0/paketassistent.md) beschrieben.

> **Hinweis**
> Der Extension-Export ist aus, bis er eingeschaltet wird: Führen Sie die Erweiterung in `package.ini [ExtensionExportSettings]
> AllowedExtensions[]` auf (oder `*`) und vergeben Sie mit `RequirePolicy=enabled` (dem Standard) `package/export_extension`. Eine
> Erweiterung mit geheimen Werten oder Schlüsselmaterial wird in jedem Fall abgewiesen
> ([Die Prüfung von Paketen auf Geheimnisse](../../designer/erweiterungen/geheimnispruefung-vor-dem-paketieren.md)).

## Der Schritt der Informationen

Was der Assistent prüft, bevor er weitergeht (`validatePackageInformation()` in `kernel/classes/ezpackagecreationhandler.php`):

| Feld | Regel |
|---|---|
| Paketname | erforderlich; nur Zeichen von a-z, 0-9 und Unterstriche; in keinem Repository darf ein Paket dieses Namens existieren |
| Zusammenfassung | erforderlich |
| Version | Semantic Versioning 2.0.0, `MAJOR.MINOR.PATCH` mit wahlweise `-prerelease` und `+build`, höchstens 24 Zeichen; `1.0.0` wird angeboten |
| Lizenz | eine aus `package.ini [LicenseSettings] LicenseList[]`; der Standard ist `GPL-2.0-or-later` |
| Format | klassisches XML (`package.xml`) oder JSON (`package.json`) |

Wählen Sie von Anfang an den Namen, den das Paket in Ihrem Index haben wird: Ein späteres Umbenennen heißt, `package.xml`, den Ordner und
jedes `<require>` zu bearbeiten, das es nennt.

## Vom Assistenten in Ihr Quell-Repository

**Paket erstellen** schreibt das Paket in das Repository `local` (`var/storage/packages/local/<name>/`) und öffnet seine Seite. Um es in Ihr
Quell-Repository zu bringen:

1. Drücken Sie **Herunterladen** auf der Seite des Pakets: Sie bekommen `<name>-<version>.ezpkg`.
2. Entpacken Sie es nach `packages/<name>/` Ihres Quell-Repositorys und committen Sie die Dateien.
3. Setzen Sie `<vendor>` in seiner `package.xml`: Der Assistent schreibt keinen, weshalb das Paket in `local` liegt; mit einem Hersteller
   legt ein Import es in `var/storage/packages/<vendor>/`.
4. Nennen Sie es in `<requires>` Ihres Seitenpakets mit einer `min-version` und führen Sie es in Ihrer `index.xml` auf.
5. Bauen Sie es mit `build.sh` neu wie jeden anderen Ordner ([Mit build.sh neu bauen](mit-build-sh-neu-bauen.md)).

> **Tipp**
> Drücken Sie vor dem Herunterladen **Prüfen** auf der Seite des Pakets: Es prüft das Paket so, wie ein Hochladen geprüft würde, sodass sich
> ein Problem auf Ihrer Testwebsite zeigt und nicht auf der neuen Installation von jemandem.

## Verwandte Seiten

- [Ein eigenes Seitenpaket erstellen](README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Der Paketassistent (Administration)](../admin-oberflaeche/der-paketassistent.md)
- [Pakete mit ezpm erstellen](pakete-mit-ezpm-erstellen.md)
- [Pakettypen](../grundlagen/pakettypen.md)
- [Pakete, Abschnitt 8](../../../anleitungen/pakete.md#8-ein-paket-erstellen)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
