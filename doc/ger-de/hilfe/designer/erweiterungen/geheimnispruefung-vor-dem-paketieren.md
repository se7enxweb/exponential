---
title: "Die Geheimnisprüfung, bevor eine Erweiterung paketiert wird"
audience: designer, developer
category: theme-extensions
order: 370
summary: Warum ein Export Ihres Themes verweigert werden kann, weil es geheime Werte oder Schlüsselmaterial enthält, wonach die Prüfung sucht, wie Sie einen echten Befund beheben und wie package_check.ini ein Dokumentationsbeispiel benennt, das die Prüfung nicht von einem echten Wert unterscheiden kann.
keywords: Geheimnisprüfung, packRefusal, package_check.ini, AllowedFindings, Geheimnisse, Passwort, API-Schlüssel, privater Schlüssel, Schlüsselmaterial, Extension-Export, secret check, secrets, password, API key, private key, key material, extension export
type: help
---
# Die Geheimnisprüfung, bevor eine Erweiterung paketiert wird

Diese Seite ist für Designer, deren Theme-Export mit einer Liste von Befunden verweigert wurde. Seit Exponential 6.0.15
nimmt kein Paket eine Erweiterung auf, die geheime Werte oder Schlüsselmaterial enthält: **Setup > Extensions**, der
Paket-Assistent und `ezpm.php add` fragen alle dieselbe Prüfung (`eZExtensionPackageHandler::packRefusal()`). Die Seite
nennt die Datei und die Einstellung oder die Art des Geheimnisses, nie den Wert.

## Was verweigert wird

| Befund | Bedeutung |
|---|---|
| `<file>: <Setting>` | Eine Einstellung, deren Name ein Geheimnis kennzeichnet (`Password`, `ApiKey`, `ClientSecret`, `Token` ...), hat einen Wert, oder eine andere Einstellung trägt ein Passwort in einer Adresse |
| `<file>: (private key)` | Ein Block mit einem privaten Schlüssel in irgendeiner Datei. Immer verweigert |
| `<file>: (bearer token)`, `(API key)`, `(constant)`, `(address with a password)` | Die Form eines Geheimnisses in irgendeinem Text |
| `<file>: key material` | Eine Schlüsseldatei nach ihrem Namen: `*.key`, `*.p12`, `.env`, `id_rsa`, `credentials*` und andere. Immer verweigert |
| `<file>: copy of a settings file` | Eine Sicherung wie `*.bak`, `*.orig`, `*~`. Immer verweigert: Löschen Sie die Kopie |

Einstellungsdateien werden als Text gelesen, auskommentierte Zeilen eingeschlossen. Ein Verzeichnis `node_modules` wird
weder gepackt noch gelesen.

## Schritt für Schritt: ein echter Befund

1. Lesen Sie den Befund: Datei und Einstellung.
2. Entfernen Sie den Wert aus dem Theme. Ein Theme braucht nie ein echtes Passwort oder einen echten Schlüssel; die
   Installation liefert sie in ihrem eigenen `settings/override/`.
3. Wurde der Wert je committet und gepusht, betrachten Sie ihn als öffentlich: Lassen Sie ihn dort ändern, wo er
   verwendet wird.
4. Dokumentieren Sie im README, welche Einstellung die Installation füllen muss.
5. Exportieren Sie erneut.

## Schritt für Schritt: ein Beispiel, das die Prüfung nicht unterscheiden kann

Zum Beispiel ein Dokumentationskommentar mit einem Beispielwert in der Form eines Schlüssels:

1. Legen Sie `package_check.ini` im Wurzelverzeichnis des Themes an:

   ```ini
   [PackageCheck]
   # The example key in the documentation comment
   AllowedFindings[]=settings/mytheme.ini.append.php:ApiKey
   ```

2. Ein Befund pro Zeile, so geschrieben, wie die Verweigerung ihn nennt: `<path below the extension>:<setting name or
   (kind)>`. Ein Pfad, der auf `/` endet, deckt dieses Verzeichnis ab.
3. Exportieren Sie erneut.

Es lässt nur weg, was es nennt. Schlüsseldateien, ein Block mit einem privaten Schlüssel, eine Kopie einer
Einstellungsdatei und ein Link werden immer verweigert. Platzhalter (`...`, `<your key>`, `%password%`, `enabled`,
`none`) kommen ohne Eintrag durch. Eine aktive Einstellung wird beim Wort genommen: `Password=changeme` wird verweigert.

## Verwandte Seiten

- [Die Geheimnisprüfung von Erweiterungspaketen](../../../bc/6.0/geheimnispruefung-fuer-erweiterungspakete.md)
- [Eine .gitignore für Themes](gitignore-fuer-themes.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
