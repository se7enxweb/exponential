---
title: Erweiterungen in einem Bundle hinzufügen und entfernen
audience: bundler, developer
category: bundling
order: 100
summary: Wie ein Bundle eine Erweiterung hinzufügt (Composer, Autoloads, Caches, ActiveExtensions oder das Seitenpaket), wie es eine erforderliche weglässt, die Ladereihenfolge, Exporte von Erweiterungen und ihre Prüfung auf Geheimnisse sowie eine Checkliste für jede Erweiterung, die Sie aufnehmen.
keywords: Erweiterung, Erweiterung hinzufügen, Erweiterung entfernen, Ladereihenfolge, Export, Geheimnisprüfung, extension, add extension, remove extension, ActiveExtensions, composer require, composer remove, autoload, loading order, extension export, secret check, AllowedExtensions, bundle
type: help
---
# Erweiterungen in einem Bundle hinzufügen und entfernen

Diese Seite richtet sich an Bündler, die den Satz an Erweiterungen ändern, mit dem Exponential ausgeliefert wird. Das
grundlegende Vorgehen steht unter
[Die Standardauswahl an Erweiterungen](../../funktionen/6.0/standard-extension-auslieferung.md); diese Seite ergänzt,
woran ein Bundle denken muss.

## Inhalt

1. [Eine Erweiterung hinzufügen](#eine-erweiterung-hinzufügen)
2. [Aktivieren](#aktivieren)
3. [Eine weglassen](#eine-weglassen)
4. [Ladereihenfolge](#ladereihenfolge)
5. [Eine Erweiterung als Paket aus einer Installation holen](#eine-erweiterung-als-paket-aus-einer-installation-holen)
6. [Checkliste für jede Erweiterung, die Sie hinzufügen](#checkliste-für-jede-erweiterung-die-sie-hinzufügen)

## Eine Erweiterung hinzufügen

```bash
composer require se7enxweb/ezflow
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

Quelle: [Eine Erweiterung hinzufügen](../../funktionen/6.0/standard-extension-auslieferung.md#eine-extension-hinzufügen).
Verwenden Sie das Paket und die Version, die Ihr Bundle wirklich braucht; das Beispiel ist das jener Seite.

## Aktivieren

Eine Erweiterung lässt sich an zwei Stellen aktivieren:

| Wo | Wann Sie es verwenden |
|---|---|
| Die Einstellungen Ihres Seitenpakets (es schreibt `ActiveExtensions[]` während der Installation) | die Erweiterung gehört zu der Website, die Sie installieren; "Eine Installation aus einem Seitenpaket aktiviert die Erweiterungen ihres Pakets für Sie" |
| `settings/override/site.ini.append.php` | die Erweiterung gehört zu jeder Website, die das Bundle installiert |

```ini
[ExtensionSettings]
ActiveExtensions[]=ezflow
```

`settings/override/` wird vom Installer geschrieben und enthält danach die Zugangsdaten der Datenbank; ein Bundle, das
Einstellungen mitbringt, muss sie als Teil des Seitenpakets liefern oder nach der Installation anwenden, nicht ein
befülltes `settings/override/` ausliefern ([Sicherheitsvorgaben eines Bundles](sicherheitsvorgaben-eines-bundles.md)).

## Eine weglassen

1. Entfernen Sie sie aus `ActiveExtensions` und leeren Sie die Caches.
2. Führen Sie `composer remove se7enxweb/<name>` aus.

"Wenn Sie mit Git-Checkouts der Erweiterungen statt mit Composer installiert haben, führen Sie `composer update` nicht
aus: Es ersetzt die Checkouts"
([Eine weglassen](../../funktionen/6.0/standard-extension-auslieferung.md#eine-weglassen)).

Bevor Sie eine **erforderliche** Erweiterung weglassen, prüfen Sie, dass nichts anderes sie braucht: `extension.xml`
nennt Abhängigkeiten in `<requires>`
([Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md)), und Seitenpakete aktivieren sie
womöglich. Halten Sie den Grund in Ihren Release-Hinweisen fest; die Empfänger werden fragen.

## Ladereihenfolge

Die Reihenfolge von `ActiveExtensions` entscheidet, wessen Einstellungen und Templates gewinnen.
**Setup > Extensions** zeigt standardmäßig die Ladereihenfolge und übernimmt Änderungen nach einer Prüfung
([Liste der Erweiterungen](../../funktionen/6.0/extension-liste-und-downloads.md),
[Die Seite Extensions](../../anleitungen/extensions-seite.md)).
Platzieren Sie Ihr Theme und Ihre Override-Erweiterungen dort, wo sie gewinnen sollen, und testen Sie die
Reihenfolge, die Sie ausliefern.

## Eine Erweiterung als Paket aus einer Installation holen

Unter Setup > Extensions lässt sich eine Erweiterung als `tar.gz`, `.zip`, `tar.bz2` oder `.ezpkg` herunterladen, und
`ezpm.php add` kann eine einpacken. Der Export "ist jetzt standardmäßig ausgeschaltet": Die Erweiterung muss in
`package.ini [ExtensionExportSettings] AllowedExtensions[]` aufgeführt sein (oder `*`), und die Rolle braucht die
Richtlinie `package/export_extension`. "Eine Erweiterung, die einen geheimen Wert oder Schlüsselmaterial enthält, wird
nicht eingepackt" ([Liste der Erweiterungen](../../funktionen/6.0/extension-liste-und-downloads.md),
[Die Prüfung auf Geheimnisse](../../bc/6.0/geheimnispruefung-fuer-erweiterungspakete.md)). Bauen Sie ein Bundle lieber aus Ihren
Repositorys, statt aus einer laufenden Website zu exportieren.

## Checkliste für jede Erweiterung, die Sie hinzufügen

- [ ] Ihre Lizenz ist bekannt und erlaubt die Weitergabe neben GPL-Code
      ([Komponenten Dritter](komponenten-dritter-und-ihre-lizenzen.md)).
- [ ] Sie nennt `Version`, `License`, `Copyright` und `Info_url` in `ezinfo.php` und `extension.xml`, damit die Seite
      **Info > Über** sie korrekt anzeigt
      ([Die Seiten Über und Copyright](../../funktionen/6.0/info-und-paketseiten.md)).
- [ ] Sie läuft auf jeder PHP-Version, die Ihr Bundle unterstützt.
- [ ] Sie ist festgelegt (Tag, Lock-Datei).
- [ ] Sie wissen, wer sie pflegt und wohin ihre Fehlermeldungen gehen ([Grenzen des Supports](grenzen-des-supports.md)).
- [ ] Sie enthält keine Geheimnisse, Schlüssel oder Kundendaten.

## Verwandte Seiten

- [Exponential 6 als Basis](exponential-6-als-basis.md)
- [Erweiterungs-Bundles](erweiterungs-bundles.md)
- [Kapitel 10.13 Verwaltung von Erweiterungen](../../installation/10-nach-der-installation.md#1013-verwaltung-von-extensions)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
