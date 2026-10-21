---
title: "Inhaltsklassen-Pakete mit einem Theme mitliefern"
audience: designer, developer
category: theme-extensions
order: 230
summary: Wenn die Templates eines Themes Inhaltsklassen erwarten (Artikel, Termin, Blogbeitrag), liefern Sie diese als Inhaltsklassen-Pakete im Ordner packages/ des Themes mit, wie sevenx_themes_simple es tut, und sagen Nutzern, wie sie sie importieren.
keywords: packages-Ordner, Inhaltsklassen-Paket, ezcontentclass, ezpkg, sevenx_themes_simple, importieren, ezpm, Klassenbezeichner, packages folder, content class package, import, class identifiers
type: help
---
# Inhaltsklassen-Pakete mit einem Theme mitliefern

Diese Seite ist für Designer, deren Templates für bestimmte Inhaltsklassen geschrieben sind. Ein Template für die
Vollansicht von `event` ist auf einer Website ohne Klasse `event` nutzlos; die Klasse mit dem Theme mitzuliefern, löst
das.

## Ein echtes Beispiel

`sevenx_themes_simple` liefert in `packages/` 20 Inhaltsklassen-Pakete mit, eines pro Klasse, zum Beispiel
`article-1.0-1.ezpkg`, `blog_post-1.0-1.ezpkg`, `event_calendar-1.1-1.ezpkg`, `frontpage-1.0-1.ezpkg` und
`product-1.0-1.ezpkg`. Der Dateiname lautet `<package name>-<version>-<release>.ezpkg`.

## Schritt für Schritt: die Pakete erstellen

1. Legen Sie auf einer Entwicklungsinstallation die Inhaltsklassen an, die Ihre Templates brauchen, oder passen Sie sie
   an.
2. Öffnen Sie **Setup > Pakete > Neues Paket erstellen** und wählen Sie **Export von Inhaltsklassen**
   ([Der Paketerstellungs-Assistent](paketerstellungs-assistent-schritt-fuer-schritt.md)). Haken Sie eine Klasse an, füllen Sie Name,
   Version und Lizenz aus und schließen Sie ab. Ein Paket pro Klasse hält sie unabhängig; ein Paket für alle ist
   einfacher zu installieren.
3. Laden Sie jedes Paket von seiner Seite herunter (**Herunterladen**) oder exportieren Sie es auf der Kommandozeile:

   ```bash
   php ezpm.php export article -d extension/mytheme/packages
   ```

4. Prüfen Sie jedes Archiv: `tar tzf extension/mytheme/packages/article-1.0-1.ezpkg | head` listet `package.xml` und
   einen Ordner `ezcontentclass/`.
5. Committen Sie die Dateien und führen Sie sie in `INSTALL.md` auf.

## Schritt für Schritt: was Ihre Nutzer tun

1. Ein Paket importieren: `php ezpm.php import extension/mytheme/packages/article-1.0-1.ezpkg` oder unter
   **Setup > Pakete > Neues Paket importieren** hochladen.
2. Es von seiner Seite aus installieren (**Installieren**). Eine schon vorhandene Klasse fragt, wie der Konflikt
   behandelt werden soll.
3. Die Caches leeren.

Schreiben Sie diese Schritte mit Ihren echten Dateinamen in Ihre `INSTALL.md`.

## Bewährte Praxis

- Verwenden Sie Klassenbezeichner, die kaum kollidieren, oder verwenden Sie die üblichen (`article`, `folder`) bewusst
  wieder und sagen Sie es.
- Erhöhen Sie die Paketversion, wenn sich die Klasse ändert, und halten Sie es im Changelog des Themes fest.
- Exportieren Sie nie Inhalte mit personenbezogenen Daten als Teil eines Themes.

## Verwandte Seiten

- [Was ein Exponential-Paket (.ezpkg) ist](was-ein-exponential-paket-ist.md)
- [Exportieren und importieren mit ezpm](exportieren-mit-ezpm.md)
- [Anleitung zu Paketen](../../../anleitungen/pakete.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
