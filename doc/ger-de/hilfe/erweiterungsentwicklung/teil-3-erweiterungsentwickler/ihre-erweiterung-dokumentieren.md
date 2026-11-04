---
title: Ihre Erweiterung dokumentieren, einschließlich Hilfeseiten
audience: developer, app-creator, designer
category: extension-development
order: 370
summary: Eine Erweiterung dort dokumentieren, wo Menschen suchen: README.md und INSTALL.md oben, der Rest als Markdown in doc/, auf Englisch; das Hilfe-Center zeigt sie, solange die Erweiterung aktiv ist, mit Front Matter, hervorgehobenen Seiten und Kontexthilfe für Ihre Views.
keywords: Dokumentation, README, Changelog, Hilfe-Center, Kontexthilfe, Front Matter, hervorgehoben, documentation, README, doc folder, changelog, help center, exphelp, ExtensionDocDirs, front matter, context help, ContextHelp Map, expHelpContextProviderInterface, featured, GFDL
type: help
---
# Ihre Erweiterung dokumentieren, einschließlich Hilfeseiten

Teil 3, Kapitel 37, das letzte über den Aufbau. Eine Erweiterung, die niemand versteht, ist eine Erweiterung, die
niemand verwendet. Dieses Kapitel listet die Dokumente auf, die eine Erweiterung haben sollte, und zeigt, wie Sie Ihre
Dokumentation neben die des Kernels in das Hilfe-Center der Administration bringen, einschließlich der Hilfe-Schaltfläche
"?" auf den Seiten Ihres Moduls.

## Die Dokumente

Die Konvention: `README.md` und `INSTALL.md` oben in `extension/<extensionName>/`, jede weitere Seite als Markdown
(`.md`) in `extension/<extensionName>/doc/`, auf Englisch. Alte Extensions lieferten Klartext (`README`, `doc/*.txt`),
der weiterhin angezeigt wird; neue und überarbeitete nutzen Markdown. Die vollständige Referenz, mit einem
Verzeichnisbaum und einem in Markdown umgewandelten Klartextdokument:
[Eine Extension dokumentieren](../../../anleitungen/eine-extension-dokumentieren.md).

| Datei | Enthält |
|---|---|
| `README.md` | was sie tut, in einem Absatz, Anforderungen, Installation, Aktivierung, Einstellungen, ein erstes Beispiel, Lizenz |
| `INSTALL.md` | die Installation Schritt für Schritt |
| `doc/` | längere Anleitungen als Markdown-Seiten: Referenz der Einstellungen, Templates und ihre Variablen, Hinweise zum Upgrade |
| `CHANGELOG.md` oder `doc/changelogs/` | was sich in jedem Release geändert hat ([Changelogs](../teil-7-veroeffentlichen/changelogs.md)) |
| `LICENSE` | der Lizenztext |
| `ezinfo.php`, `extension.xml` | Name, Version, Lizenz, Website ([ezinfo.php](ezinfo-php.md)) |

Schreiben Sie für den Leser, der sie nächstes Jahr auf einer neueren Version installiert: Nennen Sie die getesteten
Versionen, die Einstellungen mit ihren Vorgaben und jeden Befehl mit seiner erwarteten Ausgabe.

## Ihr doc-Ordner im Hilfe-Center

Das Hilfe-Center (`/exphelp/dashboard` in der Administration) zeigt die Dokumentation jeder aktiven Erweiterung von
selbst (`[IndexSettings] ActiveExtensionDocs=enabled`, die Vorgabe): die Dateien von `extension/mysite_hello/doc/`, die
das Git-Repository der Erweiterung verfolgt, und oben ihre `README*`, `INSTALL*`, `CHANGELOG*`, `LICENSE*`, `USAGE*`
und `COPYING*`. Ihre Seiten erhalten die ID `ext/mysite_hello/<path below doc>` und erscheinen unter **Extensions**.
Nur verfolgte Dateien werden ausgeliefert; sonst wird nichts von der Erweiterung je gelesen.

Auch eine nicht aktive Erweiterung kann angezeigt werden, wenn die Installation sie in `settings/exphelp.ini` nennt:

```ini
[IndexSettings]
ExtensionDocDirs[]=mysite_hello
```

Siehe [Eine Extension dokumentieren](../../../anleitungen/eine-extension-dokumentieren.md) und
[Das Hilfesystem](../../../funktionen/6.0/hilfesystem.md#dokumentation-der-extensions).

## Front Matter

Hilfeseiten sind Markdown mit einem kleinen Front-Matter-Block. Die Schlüssel, die dieses Buch verwendet:

```text
---
title: Greetings: settings and templates
audience: developer, admin
category: development
order: 10
summary: One sentence, at most 220 characters, shown in lists and search results.
keywords: greeting, mysite_hello, settings
type: help
---
# Greetings: settings and templates
```

`audience` nimmt die Zielgruppen-IDs der Hilfe auf (editor, admin, developer, user und die anderen, die das Dashboard
auflistet), `category` eine ihrer Kategorien; `featured: true` stellt eine Seite auf das Dashboard; `hidden: true`
hält sie aus Listen und der Suche heraus. Die Hilfe rendert Markdown sicher: Reines HTML wird maskiert, schreiben Sie
also nur Markdown.

## Kontexthilfe für Ihr Modul

Die Schaltfläche "?" in der Kopfzeile der Administration öffnet die "Hilfe zu dieser Seite", ausgewählt über
`exphelp.ini [ContextHelp]`. Ihre Erweiterung registriert ihre eigenen Views in
`settings/exphelp.ini.append.php`:

```ini
[ContextHelp]
Map[hello]=ext/mysite_hello/README.md
Map[hello/admin]=ext/mysite_hello/admin.md#clearing-greetings
```

Schlüssel sind `module/view`, `module` oder solche mit einem Designpräfix (`admin4l:hello/admin`); das Ziel ist eine
Seiten-ID mit optionalem Überschriftenanker. Ein Eintrag, dessen Seite die Hilfe nicht ausliefert, wird übersprungen,
sodass der nächste Schlüssel antwortet. Für Hilfe, die von mehr als der View abhängt, implementieren Sie
`expHelpContextProviderInterface` (`kernel/classes/help/exphelpcontextproviderinterface.php`), dessen einzige Methode
`helpContext( $module, $view, $design )` `"path[#anchor]"` oder `null` zurückgibt, und tragen die Klasse in
`[ContextHelp] Providers[]` ein.

## Eine Seite im Katalog des Kernels

Die Dokumentation des Kernels hat für jede mitgelieferte Erweiterung eine Seite in `doc/eng-us/features/6.0/extensions/`.
Wenn Ihre Erweiterung dem Projekt angeboten wird, hilft dort eine kurze Seite (was sie tut, Einstellungen, was sich pro
Release geändert hat) jedem Administrator; schlagen Sie sie mit einem Pull Request vor
([Mit Pull Requests zurückgeben](../teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md)).

## Ihre Dokumentation lizenzieren

Code steht meist unter der GPL; Dokumentation ist unter einer Dokumentationslizenz besser aufgehoben. Diese Hilfe
verwendet die GNU FDL 1.3, die es jedem erlaubt, sie zu kopieren, zu übersetzen und zu verbessern
([Freie Dokumentation](../../freie-dokumentation.md)). Nennen Sie die Lizenz Ihres `doc/` in dessen README.

## Verwandte Seiten

- Zurück: [Leistung in Erweiterungen](leistung-in-erweiterungen.md)
- Weiter: [Tutorials: echte Erweiterungen Schritt für Schritt bauen](tutorials/README.md)
- [Das Hilfesystem](../../../funktionen/6.0/hilfesystem.md), [Spezifikation des Hilfesystems](../../../spezifikationen/6.0/hilfesystem.md)
- [Das Hilfesystem für Erweiterungen](../teil-8-referenz/hilfesystem-fuer-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
