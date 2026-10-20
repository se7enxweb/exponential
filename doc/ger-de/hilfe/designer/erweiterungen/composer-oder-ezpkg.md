---
title: "Composer-Paket oder .ezpkg: was Sie ausliefern"
audience: designer, project-manager
category: theme-extensions
order: 440
summary: Eine Entscheidungstabelle, ob Sie ein Theme als Composer-Paket, als Exponential-Paket (.ezpkg) oder als beides ausliefern - danach, wer es installiert, was es enthält, wie es aktualisiert und wo es gefunden wird.
keywords: Composer oder ezpkg, Auslieferungsformat, Entscheidung, Paketierung, Packagist, ezpkg, Update, Installationsmethode, composer or ezpkg, distribution format, decision, packaging, install method
type: help
---
# Composer-Paket oder .ezpkg: was Sie ausliefern

Diese Seite ist für Designer, die entscheiden, wie sie ein Theme weitergeben. Es gibt nicht die eine richtige Antwort;
die drei veröffentlichten Themes von 7x sind Composer-Pakete, und eines davon enthält `.ezpkg`-Dateien.

## Die Entscheidungstabelle

| Frage | Composer-Paket | `.ezpkg` |
|---|---|---|
| Wer installiert es? | Entwickler und Hoster, die `composer` ausführen | Administratoren, die unter **Setup > Pakete** arbeiten |
| Was enthält es? | Dateien: die Theme-Erweiterung | Dateien **und** Datenbankinhalte: Klassen, Objekte, einen Site-Style |
| Wie wird es aktualisiert? | `composer update yourvendor/mytheme` | Durch Installieren der nächsten Paketversion |
| Wo wird es gefunden? | Packagist, GitHub | Wo immer Sie die Datei ablegen; auf Projektseiten aufgeführt |
| Versionshistorie? | Tags und Releases in Git | Version und Changelog im Paket |
| Abhängigkeiten? | `require` installiert sie | Paketabhängigkeiten, von Hand installiert |

## Drei häufige Antworten

1. **Nur Composer**: ein Theme, das mit den Inhaltsklassen arbeitet, die die Website schon hat. Am einfachsten zu pflegen.
2. **Composer mit `.ezpkg` darin**: Das Theme braucht eigene Klassen. Liefern Sie sie in `packages/` mit und erklären Sie
   den Import in `INSTALL.md`, wie `sevenx_themes_simple` es mit 20 Klassenpaketen tut.
3. **Nur `.ezpkg`**: ein Farbschema für ein bestehendes Design ([Das Site-Style-Paket](das-site-style-paket.md)) oder ein
   Publikum, das Composer nicht verwendet.

## Schritt für Schritt

1. Beantworten Sie die ersten beiden Zeilen der Tabelle für Ihr Theme.
2. Wählen Sie eine der drei Antworten.
3. Schreiben Sie die Installationsschritte dafür ins README und probieren Sie sie auf einer Testinstallation aus
   ([Eine Composer-Installation testen](composer-installation-lokal-testen.md),
   [Ihr Paket auf einer Testseite installieren](ihr-paket-auf-einer-testseite-installieren.md)).

## Verwandte Seiten

- [Was ein Exponential-Paket ist](was-ein-exponential-paket-ist.md)
- [composer.json für ein Theme](composer-json-fuer-ein-theme.md)
- [Wählen, wo veröffentlicht wird](../veroeffentlichung/waehlen-wo-veroeffentlicht-wird.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
