---
title: Namen und Marken bei einem Upgrade
audience: developer, designer, app-creator
category: extension-development
order: 210
summary: Was Sie beim Upgrade einer alten Erweiterung umbenennen sollten und was Sie nie anfassen dürfen - Produktnamen in Texten, neuer Code mit exp, Kernel-Bezeichner, Ordnernamen, Einstellungen - und wie die ursprünglichen Autoren genannt bleiben.
keywords: Namen, Marken, umbenennen, exp-Präfix, eZ-Bezeichner, Ordnername, Übersetzungskontext, Nennung der Urheber, ursprünglicher Autor, Upgrade, naming, branding, rename, Exponential, exp prefix, eZ identifiers, folder name, translation context, credit, original author, upgrade
type: help
---
# Namen und Marken bei einem Upgrade

Teil 4, Kapitel 21. Eine alte Erweiterung nennt das Produkt beim Namen seiner alten Releases, verwendet für alles ein
`ez`-Präfix und trägt vielleicht den Namen einer Firma, die sie nicht mehr pflegt. Wenn Sie sie aktualisieren, sollten
sich manche dieser Namen ändern, und viele dürfen es nicht. Das Falsche umzubenennen, bricht Websites; nichts
umzubenennen, verwirrt Benutzer. Dieses Kapitel zieht die Grenze.

## Die Regel in einem Satz

Benennen Sie um, was **Menschen lesen**; benennen Sie nie um, worauf **Code oder Einstellungen verweisen**.

| Art des Namens | Umbenennen? | Warum |
|---|---|---|
| Produktname in Texten der Oberfläche, README, Dokumentation, `Name` in `ezinfo.php` | ja: „Exponential“ | so heißt das Produkt |
| Der Ordnername der Erweiterung | nein | Einstellungen (`ActiveExtensions[]`), Templates (`design:`), andere Erweiterungen und `composer.json` verweisen darauf |
| Bestehende Klassennamen, Funktionsnamen, Namen von Template-Operatoren | nein | anderer Code ruft sie auf; ein umbenannter Operator bricht jedes Template, das ihn verwendet |
| Kernel-Bezeichner wie `eZContentObject`, `eZINI`, `eZDB` | niemals | das sind die echten Namen des Kernels |
| Namen, Abschnitte und Einstellungen von INI-Dateien | nein | Websites haben Overrides dafür |
| Datenbanktabellen und -spalten | nein | Umbenennen braucht ein Upgrade-Skript und bricht andere Leser |
| Übersetzungskontexte (`'extension/myoldext'`) | nein | bestehende Übersetzungen sind danach verschlüsselt |
| Neue Klassen, Dateien, Operatoren und Einstellungen, die Sie hinzufügen | `exp` für Code des Exponential-Projekts, sonst Ihr eigenes Präfix | die Konvention für neuen Code; siehe [Namenspräfixe: sevenx und exp](../../../../eng-us/standards/naming-prefixes-sevenx-and-exp.md) |

## Produktnamen in Texten

Durchsuchen Sie die Erweiterung nach dem alten Produktnamen in dem, was Menschen lesen:

```bash
grep -rniE 'ez ?publish' --include='*.tpl' --include='*.md' --include='*.txt' --include='*.ts' extension/myoldext/ | grep -v '^\S*:\s*[0-9]*:\s*//'
```

Ändern Sie ihn:

- in Texten der Oberfläche in Templates (und aktualisieren Sie die Übersetzungen, die sie übersetzen);
- in der README und der Dokumentation;
- in `ezinfo.php` und `extension.xml` (`Name`, die Zusammenfassung);
- in der `description` von `composer.json`;
- in Fehlermeldungen, die Ihr PHP-Code Benutzern zeigt.

Lassen Sie ihn stehen, wo er Geschichte ist: Ein Changelog-Eintrag von 2012 sagt, wie das Produkt 2012 hieß, und ein
Lizenzkopf, der das alte Produkt als das Werk nennt, für das er geschrieben wurde, bleibt, wie er ist (er ist Teil des
Lizenzhinweises).

> **Hinweis**
> Ändern Sie keinen Text in einem String, der ein Bezeichner ist: Ein `type` `ezpublish-legacy-extension` in
> `composer.json` oder ein `extra`-Schlüssel `ezpublish-legacy-extension-name` sind Namen, nach denen der
> Composer-Installer sucht. Sie bleiben genau, wie sie sind.

## Das ez-Präfix und neuer Code

Alte Erweiterungen benennen alles mit `ez`, weil der Kernel es tat. In Exponential 6 ist die Konvention für **neuen**
Code `exp` (oder für die eigenen Erweiterungen von 7x `sevenx`): Ein neues Modul, eine neue Klasse, ein neuer
Template-Operator oder eine neue Einstellung heißt `exp...`, niemals `ez...`; Ihre eigene Erweiterung verwendet Ihr
eigenes Präfix. Die Gründe stehen in [Namenspräfixe: sevenx und exp](../../../../eng-us/standards/naming-prefixes-sevenx-and-exp.md).
Bestehende Namen behalten ihr Präfix: `ezmyoperator` in `expmyoperator` umzubenennen, bricht jedes Template, das dafür
geschrieben wurde.

Wenn Sie einen neuen Namen anbieten müssen (weil der alte kollidiert oder irreführend ist), fügen Sie den neuen hinzu
und lassen Sie den alten weiter funktionieren:

- ein Template-Operator: Registrieren Sie beide Namen in `operatorList()` und sagen Sie im Changelog, welcher veraltet
  ist;
- eine Klasse: Behalten Sie die alte Klasse als Unterklasse der neuen;
- eine Einstellung: Lesen Sie die neue, mit der alten als Fallback.

Entfernen Sie den alten Namen nur in einem Major-Release, das vorher angekündigt wurde.

## Echte Beispiele

se7enxweb/explayouts_ui_api hat in Commit 2a95edc einen Text umbenannt, den Menschen sehen: Die Edition der Anwendung
im Tooltip des Logos wurde zu „Exponential Layouts“. Sein späterer Commit 8273a03 ging weiter und benannte die
Seiten-Tags, globalen Variablen und den Session-Schlüssel des Editors vom Präfix des Upstream-Projekts in
`explayouts`-Namen um, weil das die eigenen Interna der Erweiterung waren, von niemand anderem verwendet, und der
Editor in diesem Commit als Ganzes geändert wurde.

se7enxweb/expchangeclass ist ein Fork einer älteren Erweiterung: Es hat einen neuen Namen für das Paket
(`se7enxweb/expchangeclass`, `Name` „Exponential Change Class“) und behält den Namen des Originals in seiner
Beschreibung („A hardened fork of eZChangeclass“) und in seiner `Includes`-Nennung. Auch sein Ordnername ist neu, weil
es als neue Erweiterung veröffentlicht wurde, nicht als Upgrade an Ort und Stelle: Eine Website, die darauf wechselt,
ändert ihr `ActiveExtensions[]`.

## Wenn Sie forken, ist der Name ein Versprechen

Wenn Sie eine aktualisierte Kopie der Erweiterung eines anderen veröffentlichen:

- **Verwenden Sie einen Namen, der nicht vorgibt, das Original zu sein.** Einen anderen Paketnamen (Ihr Konto)
  verlangt Packagist ohnehin; ein anderer Anzeigename („... für Exponential 6“ oder ein neuer Name) sagt Benutzern, wer
  sie pflegt.
- **Behalten Sie den Namen des ursprünglichen Autors** in den Copyright-Zeilen, in `authors` von `composer.json` und
  in einem `Includes`-Eintrag in `ezinfo.php`. Ihn zu entfernen, ist kein „Rebranding“, sondern bedeutet, sich die
  Arbeit eines anderen zuzuschreiben.
- **Verwenden Sie für Ihre Kopie nicht das Logo oder die Marke einer anderen Firma.**

Die Ethik dahinter ist ein eigenes Kapitel: [Ethisches Rebranding](../teil-5-erweitern-forken-zurueckgeben/ethisches-rebranding.md).

## Checkliste

- [ ] Texte der Oberfläche, README und Metadaten sagen „Exponential“.
- [ ] Ordnername, Klassennamen, Operatornamen, Einstellungen, Tabellen und Übersetzungskontexte sind unverändert.
- [ ] Neuer Code, den Sie hinzugefügt haben, verwendet `exp` (Projektcode) oder Ihr eigenes Präfix.
- [ ] Jeder neue Name für einen alten lässt den alten weiter funktionieren, mit einem Hinweis, dass er veraltet ist.
- [ ] Die ursprünglichen Autoren sind in den Copyright-Zeilen, in `composer.json` und in `ezinfo.php` genannt.

## Verwandte Seiten

- Zurück: [Kontextmenüs und Integration in die Administration](kontextmenues-und-admin-integration.md)
- Weiter: [Tests und CI für eine aktualisierte Erweiterung](tests-und-ci-fuer-eine-aktualisierte-erweiterung.md)
- [Namensnennung und Copyright-Hinweise](../teil-5-erweitern-forken-zurueckgeben/namensnennung-und-urheberrechtshinweise.md)
- [Übersetzungen in einer Erweiterung](../teil-3-erweiterungsentwickler/uebersetzungen-in-einer-erweiterung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
