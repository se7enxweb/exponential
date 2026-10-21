---
title: "Theme-Erweiterungen: hier beginnen"
audience: designer, developer
category: theme-extensions
order: 10
featured: true
summary: Die Übersicht über alles, was dazugehört, ein Design in eine Theme-Erweiterung zu packen - Ordnerstruktur, ezinfo.php und extension.xml, die Einstellungen eines Themes, der RAD-Design-Assistent, Composer-Pakete und Exponential-Pakete (.ezpkg) - mit einer empfohlenen Lesereihenfolge.
keywords: Theme-Erweiterung, Design-Erweiterung, Aufbau einer Erweiterung, ezinfo.php, extension.xml, composer.json, ezpkg, Paket-Assistent, RAD, Design-Assistent, Paketierung, hier beginnen, theme extension, design extension, extension structure, package wizard, design wizard, packaging, start here
type: help
---
# Theme-Erweiterungen: hier beginnen

Ein Design, das Sie für Exponential bauen, wird für andere nützlich, wenn es in einer **Theme-Erweiterung** liegt: ein
Ordner unter `extension/`, der die Templates, Stylesheets, Skripte, Bilder, Einstellungen und die kleinen Metadatendateien
enthält, die einer Installation sagen, wer das Theme ist. Dieser Bereich zeigt, wie Sie diesen Ordner richtig aufbauen und
ihn als Composer-Paket oder als Exponential-Paket (`.ezpkg`) packen.

Wie das Design selbst entsteht (Templates, Overrides, Stylesheets, Dunkelmodus, Barrierefreiheit), erklären
[Theme-Design](../theme-design/design-erweiterungen.md) und [Templates](../templates/templates-ueberblick.md). Dieser Bereich
handelt von dem Behälter darum herum.

## Eine Lesereihenfolge

1. Die Idee verstehen: [Was eine Theme-Erweiterung ist](was-eine-theme-erweiterung-ist.md),
   [Ihre Theme-Erweiterung benennen](ihre-theme-erweiterung-benennen.md),
   [Die Ordnerstruktur einer Theme-Erweiterung](ordnerstruktur-einer-theme-erweiterung.md).
2. Ein Gerüst erzeugen: [Der Design-Erweiterungs-Assistent](der-design-erweiterungs-assistent.md), dann
   [Vom Ergebnis des Assistenten zum ersten Commit](vom-assistenten-zum-ersten-commit.md).
3. Es beschreiben: [ezinfo.php](ezinfo-php.md), [extension.xml](extension-xml.md),
   [Alle Versionsfelder synchron halten](versionsfelder-synchron-halten.md).
4. Es einbinden: [Das Design registrieren](das-design-registrieren.md),
   [Ein Theme aktivieren](eine-theme-erweiterung-aktivieren.md), [Ladereihenfolge](ladereihenfolge-von-themes.md).
5. Es packen: [composer.json für ein Theme](composer-json-fuer-ein-theme.md) oder
   [der Paketerstellungs-Assistent](paketerstellungs-assistent-schritt-fuer-schritt.md);
   [Composer-Paket oder .ezpkg](composer-oder-ezpkg.md) hilft bei der Wahl.
6. Es prüfen: [Checkliste: Die Theme-Erweiterung ist bereit](checkliste-theme-erweiterung-bereit.md).

Fahren Sie dann mit [Ein Theme veröffentlichen](../veroeffentlichung/ein-theme-veroeffentlichen-hier-beginnen.md) fort.

## Alle Seiten dieses Bereichs

| Seite | Was sie beantwortet |
|---|---|
| [Was eine Theme-Erweiterung ist](was-eine-theme-erweiterung-ist.md) | Warum ein Design in eine Erweiterung gehört |
| [Ihre Theme-Erweiterung benennen](ihre-theme-erweiterung-benennen.md) | Welche Namen erlaubt und welche klug sind |
| [Die Ordnerstruktur einer Theme-Erweiterung](ordnerstruktur-einer-theme-erweiterung.md) | Welcher Ordner was enthält |
| [ezinfo.php](ezinfo-php.md) | Die Metadaten, die die Seite Über liest |
| [extension.xml](extension-xml.md) | Die paketierte Beschreibung, die zuerst gelesen wird |
| [Alle Versionsfelder synchron halten](versionsfelder-synchron-halten.md) | Wo eine Versionsnummer steht |
| [Copyright- und Autorenzeilen](copyright-und-autorenzeilen.md) | Wen Sie nennen und wie |
| [Die LICENSE-Datei eines Themes](die-license-datei.md) | Welche Datei, welcher Text |
| [Das README eines Themes schreiben](readme-fuer-ein-theme.md) | Was ein Leser zuerst braucht |
| [INSTALL.md und CHANGELOG.md](install-und-changelog-dateien.md) | Installationsschritte und Historie |
| [Das Design registrieren](das-design-registrieren.md) | `DesignExtensions[]` |
| [Die Design-Einstellungen, die ein Theme mitliefert](design-einstellungen-eines-themes.md) | `SiteDesign`, `AdditionalSiteDesignList[]`, Stylesheet-Listen |
| [Einstellungsdateien in einer Erweiterung](einstellungsdateien-in-einer-erweiterung.md) | `.ini.append.php`-Dateien |
| [Siteaccess-Einstellungen in einem Theme](siteaccess-einstellungen-in-einem-theme.md) | `settings/siteaccess/<name>/` |
| [Einem Theme eine eigene Einstellungsdatei geben](eine-eigene-ini-datei.md) | Eine `mytheme.ini` |
| [Ein Theme aktivieren](eine-theme-erweiterung-aktivieren.md) | `ActiveExtensions[]` und `ActiveAccessExtensions[]` |
| [Ladereihenfolge und welches Theme gewinnt](ladereihenfolge-von-themes.md) | Rangfolge zwischen Erweiterungen |
| [Abhängigkeiten in extension.xml deklarieren](abhaengigkeiten-in-extension-xml.md) | `<dependencies>` |
| [Template-Operatoren in einem Theme](template-operatoren-in-einem-theme.md) | PHP-Helfer für Templates |
| [Übersetzungen in einem Theme](uebersetzungen-in-einem-theme.md) | `translations/<locale>/translation.ts` |
| [Der share-Ordner](der-share-ordner.md) | Demobilder, Dateien und Startdaten |
| [Inhaltsklassen-Pakete mitliefern](inhaltsklassen-pakete-beilegen.md) | `.ezpkg`-Dateien in einem Theme |
| [Der Design-Erweiterungs-Assistent](der-design-erweiterungs-assistent.md) | `/setup/designextension` |
| [Die Teile des Design-Assistenten](die-teile-des-design-assistenten.md) | Jedes Kontrollkästchen des Assistenten |
| [Das Ergebnis des Assistenten als ZIP herunterladen](design-assistent-als-zip-herunterladen.md) | Dateien, ohne sie zu schreiben |
| [Vom Ergebnis des Assistenten zum ersten Commit](vom-assistenten-zum-ersten-commit.md) | Die erste Stunde |
| [Ein Theme unter Git stellen](ein-theme-unter-git-stellen.md) | Ein Repository pro Theme |
| [Eine .gitignore für Themes](gitignore-fuer-themes.md) | Was nie hineingehört |
| [composer.json für ein Theme](composer-json-fuer-ein-theme.md) | Die Paketbeschreibung |
| [Der Composer-Installer und der Ordnername](composer-installer-und-erweiterungsname.md) | `ezpublish-legacy-extension-name` |
| [require und suggest](composer-require-und-suggest.md) | Abhängigkeiten eines Themes |
| [Eine Composer-Installation testen](composer-installation-lokal-testen.md) | Vor der Veröffentlichung |
| [Was ein Exponential-Paket ist](was-ein-exponential-paket-ist.md) | `.ezpkg` erklärt |
| [Das Seitenstil-Paket](das-site-style-paket.md) | Zwei Stylesheets und Bilder |
| [Eine Erweiterung als Paket exportieren](eine-erweiterung-als-paket-exportieren.md) | Der Extension-Export |
| [Die Geheimnisprüfung](geheimnispruefung-vor-dem-paketieren.md) | Warum ein Export verweigert wird |
| [Der Paketerstellungs-Assistent](paketerstellungs-assistent-schritt-fuer-schritt.md) | `/package/create` |
| [Eine Lizenz im Paket-Assistenten wählen](paketlizenzen-im-assistenten.md) | Die Lizenzliste |
| [Klassische XML- und JSON-Pakete](klassische-und-json-paketformate.md) | Die beiden Formate |
| [Exportieren und importieren mit ezpm](exportieren-mit-ezpm.md) | Die Kommandozeile |
| [Ein Paket prüfen](ein-paket-pruefen.md) | Bevor Sie es weitergeben |
| [Ihr Paket auf einer Testseite installieren](ihr-paket-auf-einer-testseite-installieren.md) | Ein sicherer Versuch |
| [Composer-Paket oder .ezpkg](composer-oder-ezpkg.md) | Was Sie ausliefern |
| [Checkliste: Die Theme-Erweiterung ist bereit](checkliste-theme-erweiterung-bereit.md) | Die letzte Prüfung |
| [Fehlersuche bei Theme-Erweiterungen](fehlersuche-bei-theme-erweiterungen.md) | Anzeichen und Abhilfen |

## Verwandte Seiten

- [Erweiterungen: finden, installieren, konfigurieren, bauen und veröffentlichen](../../../anleitungen/extensions.md)
- [Pakete](../../../anleitungen/pakete.md)
- [Ein Theme veröffentlichen: hier beginnen](../veroeffentlichung/ein-theme-veroeffentlichen-hier-beginnen.md)
- [Der Lebenszyklus der Wartung: hier beginnen](../lebenszyklus/lebenszyklus-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
