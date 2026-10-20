---
title: "Ihr Paket auf einer Testseite installieren"
audience: designer, admin
category: theme-extensions
order: 430
summary: Ihr Paket genau so ausprobieren, wie ein Nutzer es tun wird - hochladen, prüfen, was die Installation vorfindet, installieren (oder als Entwürfe installieren), die Website prüfen und deinstallieren - auf einer Wegwerf-Installation, nie auf einer Live-Website.
keywords: Paket installieren, Testseite, Paket hochladen, als Entwürfe installieren, deinstallieren, vergleichen, prüfen, Wegwerf-Installation, install package, test site, upload package, install as drafts, uninstall, compare, throwaway installation
type: help
---
# Ihr Paket auf einer Testseite installieren

Diese Seite ist für Designer, die ihr Paket aus Sicht des Nutzers sehen wollen. Der Paket-Installer schreibt Klassen und
Inhalte, also verwenden Sie immer eine Wegwerf-Installation.

## Bevor Sie beginnen

- Eine Testinstallation, die Sie zurücksetzen können (eine Kopie einer Demo-Website oder eine frische Installation).
- Ein Administratorkonto darauf mit `package/import` und `package/install`.

## Schritt für Schritt

1. **Hochladen**: **Setup > Pakete > Neues Paket importieren**, das `.ezpkg` wählen, **Importiere Paket** drücken. Die
   Seite nennt die erste Regel, die fehlgeschlagen ist, falls eine ([Ein Paket prüfen](ein-paket-pruefen.md)).
2. **Lesen Sie die Seite des Pakets**: Einzelheiten, Lizenz, was es enthält, Dateien. Drücken Sie **Vergleichen**, um
   den Inhalt des Pakets mit der Website zu vergleichen.
3. **Installieren**: Drücken Sie **Installieren**. Die Installationsseite listet, was installiert wird und **was die
   Installation hier vorfindet**: jedes Inhaltsobjekt nach Remote-ID (neu, vorhanden, ein Konflikt oder ohne seine
   Klasse).
4. Wählen Sie:
   - **Paket installieren** für eine normale Installation. Eine vorhandene Klasse fragt, wie der Konflikt behandelt
     werden soll.
   - **Als Entwürfe installieren**, um Inhaltsobjekte nur als Entwürfe zu schreiben (nichts wird veröffentlicht;
     Klassen, Einstellungen und Design-Dateien werden nicht installiert), und prüfen Sie jeden Entwurf.
5. **Prüfen Sie die Website**: Leeren Sie die Caches, öffnen Sie die Seiten, die die neuen Klassen oder Inhalte
   verwenden, und sehen Sie sie im hellen und dunklen Modus und bei schmaler Breite an.
6. **Deinstallieren**: Drücken Sie auf der Seite des Pakets **Deinstallieren**. Es listet auf, was entfernt wird,
   Inhalte eingeschlossen, und lässt sich nicht rückgängig machen. Deinstallieren Sie, um zu prüfen, dass Ihr Paket die
   Website sauber hinterlässt.
7. Notieren Sie jede Überraschung und beheben Sie sie in der nächsten Version des Pakets.

![Setup, Pakete: die Paket-Repositories, in denen ein importiertes Paket erscheint, bevor es installiert wird](../../../../eng-us/help/images/designers/packages-repositories.webp)

## Auf der Kommandozeile

```bash
php ezpm.php import /path/to/mytheme_classes-1.0.0-1.ezpkg
php ezpm.php check mytheme_classes
php ezpm.php install mytheme_classes
```

## Verwandte Seiten

- [Pakete, Abschnitt 7](../../../anleitungen/pakete.md#7-installieren-und-deinstallieren)
- [Exportieren und importieren mit ezpm](exportieren-mit-ezpm.md)
- [Eine Upgrade-Testumgebung](../../../../eng-us/help/designers/lifecycle/upgrade-test-environment.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
