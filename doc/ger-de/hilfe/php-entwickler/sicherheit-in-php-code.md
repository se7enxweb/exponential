---
title: Sicherheit in PHP-Code
audience: php-developer, developer
category: php-development
order: 330
summary: Die Sicherheitsregeln für PHP-Code in Exponential an einem Ort - Eingaben validieren, Zahlen casten, SQL und Ausgaben je nach Kontext maskieren, POSTs mit dem Formular-Token schützen, Berechtigungen an jedem Einstiegspunkt prüfen, sicher weiterleiten, Geheimnisse aus Code und Logs heraushalten und Dateien und Uploads über den Kernel behandeln - mit der Seite zu jeder Regel.
keywords: Sicherheit, PHP-Sicherheit, Eingabevalidierung, Maskierung, Berechtigungen, sichere Weiterleitungen, Geheimnisse, Uploads, Logging, security, PHP security, input validation, escaping, SQL injection, XSS, CSRF, ezformtoken, permissions, hasAccessTo, safe redirects, eZRedirectManager, safeURI, secrets, uploads, phar, logging, OWASP
type: help
---
# Sicherheit in PHP-Code

Diese Seite richtet sich an PHP-Entwickler, die Code schreiben, der Anfragen, Daten oder Dateien verarbeitet. Sie
sammelt die Regeln, die Exponential-Code sicher halten, und verweist auf die Seite, die jede davon erklärt. Der
Sicherheitsstandard des Projekts ist der [Sicherheitsstandard](../../standards/sicherheit.md); Administratoren härten die
Installation mit [Sicherheitshärtung](../sicherheitshaertung.md).

## Inhalt

1. [Die Regeln](#die-regeln)
2. [Woher Eingaben kommen](#woher-eingaben-kommen)
3. [Berechtigungen an jedem Einstiegspunkt](#berechtigungen-an-jedem-einstiegspunkt)
4. [Weiterleitungen](#weiterleitungen)
5. [Geheimnisse](#geheimnisse)
6. [Dateien und Uploads](#dateien-und-uploads)
7. [Was der Kernel für Sie tut](#was-der-kernel-für-sie-tut)
8. [Eine Schwachstelle melden](#eine-schwachstelle-melden)

## Die Regeln

| # | Regel | Seite |
|---|---|---|
| 1 | Validieren Sie Eingaben dort, wo sie hereinkommen: Zahlen mit `(int)` und `(float)` casten, Auswahlwerte gegen Allow-Listen prüfen, Längen begrenzen | [Eingaben validieren und casten](eingaben-validieren-und-casten.md) |
| 2 | Maskieren Sie Strings für SQL mit `$db->escapeString()`, oder verwenden Sie Bedingungen von `eZPersistentObject` | [SQL mit eZDB, sicher](sql-mit-ezdb.md) |
| 3 | Maskieren Sie Ausgaben für ihren Kontext: `|wash` in Templates, `htmlspecialchars()` mit ausdrücklichen Flags in PHP, `json_encode()` für JSON | [Ausgaben aus PHP maskieren](ausgaben-aus-php-maskieren.md) |
| 4 | Ändern Sie Daten nur per POST, geschützt durch das Formular-Token | [CSRF-Schutz und ezformtoken](csrf-und-ezformtoken.md) |
| 5 | Prüfen Sie Berechtigungen in jeder View, Fetch-Funktion, Serverfunktion, REST-Aktion und jedem Befehl | [Berechtigungen an jedem Einstiegspunkt](#berechtigungen-an-jedem-einstiegspunkt) |
| 6 | Leiten Sie nur an Adressen weiter, die die Regeln des Kernels akzeptieren | [Weiterleitungen](#weiterleitungen) |
| 7 | Halten Sie Geheimnisse in den Einstellungen der Installation, niemals in Code, URLs oder Logs | [Geheimnisse](#geheimnisse) |
| 8 | Speichern Sie Dateien über den Kernel; prüfen Sie Uploads nach Inhalt und Größe | [Dateien und Uploads](#dateien-und-uploads) |
| 9 | Fügen Sie in von Ihnen erzeugtes Markup keine Inline-Skripte und kein `style=""` ein | [Content Security Policy für Ihren Code](../ersteller/anwendungsentwickler/content-security-policy.md) |

## Woher Eingaben kommen

Behandeln Sie all das als nicht vertrauenswürdig:

- `$Params` und ungeordnete Parameter einer View: Strings aus der Adresse.
- `eZHTTPTool::instance()->postVariable()`, `getVariable()`, `variable()`: die Anfrage. Lesen Sie Eingaben über
  `eZHTTPTool`, nicht direkt aus `$_POST` und `$_GET`, damit die Anfragevorbereitung des Kernels greift (unter Velocity
  bereitet er sie pro Anfrage vor).
- Argumente von ezjscore-Serverfunktionen und REST-Routen.
- Einstellungen: von Menschen geschrieben, manchmal von anderen Erweiterungen.
- Die Datenbank: früher von jemandem geschrieben, vielleicht von einem Import.
- Header wie `Referer`, `Host` und `X-Forwarded-For`; verwenden Sie die Behandlung vertrauenswürdiger Proxys des Kernels,
  nicht den rohen Header ([Vertrauenswürdige Proxys](../../bc/6.0/vertrauenswuerdige-proxys.md)).

## Berechtigungen an jedem Einstiegspunkt

```php
$user = eZUser::currentUser();
$access = $user->hasAccessTo( 'acmereport', 'read' );
if ( $access['accessWord'] === 'no' )
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
```

- `accessWord` ist `yes`, `no` oder `limited`; bei `limited` prüfen Sie die Einschränkung gegen das Objekt (oder
  verwenden Sie für Inhalte `$node->canRead()`, `$object->canEdit()`).
- Die `functions` einer View in `module.php` prüft der Kernel, bevor Ihr Code läuft, aber Fetch-Funktionen,
  ezjscore-Serverfunktionen, REST-Aktionen und Befehle werden von Stellen aufgerufen, die nicht prüfen: Sie müssen
  selbst prüfen ([Zugriffskontrolle im Code](../ersteller/anwendungsentwickler/zugriffskontrolle-im-code.md)).
- Inhaltslisten aus `eZContentObjectTreeNode::subTreeByNodeID()` wenden `content/read` des aktuellen Benutzers an,
  sofern Sie nicht `'Limitation' => array()` übergeben; übergeben Sie das niemals für Ergebnisse, die Benutzer sehen.

## Weiterleitungen

Leiten Sie niemals ungeprüft an eine Adresse weiter, die aus der Anfrage stammt. `eZRedirectManager`
(`kernel/classes/ezredirectmanager.php`) enthält die Regeln: Pfade derselben Site, der aktuelle Host oder Hosts aus
`site.ini [SiteSettings] AllowedRedirectHosts`, keine Steuerzeichen oder Backslashes, kein `//` und keine fremden
Schemata, auch nicht prozentkodiert, keine Zugangsdaten in der URL. `eZRedirectManager::safeURI( $uri )` prüft eine
Adresse; `redirectTo()` und `returnURI()` wählen ein sicheres Ziel mit einem Standardwert
([Sichere Weiterleitungen](../../funktionen/6.0/sichere-weiterleitungen.md)).

## Geheimnisse

- API-Schlüssel, Passwörter und Tokens liegen in `settings/override/` der Installation oder in Umgebungsvariablen,
  niemals im Repository Ihrer Erweiterung und niemals in einer URL.
- Protokollieren Sie sie niemals; geben Sie sie niemals in der Debug-Ausgabe aus; maskieren Sie sie in Fehlermeldungen.
- Vergleichen Sie Geheimnisse mit `hash_equals()`; speichern Sie Tokens nach Möglichkeit als Hashes (die API-Schlüssel
  des Kernels speichern nur einen gesalzenen HMAC-SHA-256-Hash, expservices-Tokens nur einen SHA-256-Hash).
- Erzeugen Sie Zufallswerte mit `random_bytes()` oder `random_int()`.

## Dateien und Uploads

- Lesen Sie Uploads über `eZHTTPFile::canFetch()` und `eZHTTPFile::fetch()`; prüfen Sie den Typ nach Inhalt und die
  Größe gegen eine Allow-Liste.
- Speichern Sie Dateien bevorzugt als Inhalte (die Klassen `file` und `image`), was Speicherung, Clustering und
  SVG-Bereinigung übernimmt ([SVG-Bilder](../../bc/6.0/svg-bilder.md)).
- Bauen Sie niemals einen Dateipfad aus einer Eingabe, ohne ihn zu normalisieren und zu prüfen, dass er im erwarteten
  Verzeichnis bleibt.
- `autoload.php` hebt bei jeder Anfrage die Registrierung des Stream-Wrappers `phar://` auf (sofern die Laufzeitumgebung
  nicht selbst aus einem Archiv läuft), sodass eine hochgeladene Datei, die zugleich ein gültiges phar ist, niemals als
  Code eingebunden werden kann. Registrieren Sie ihn nicht erneut.

## Was der Kernel für Sie tut

| Schutz | Wo |
|---|---|
| Formular-Token bei jedem POST angemeldeter Benutzer | `ezformtoken` |
| `|wash` maskiert einfache Anführungszeichen unter jeder PHP-Version | die Template-Engine, seit 6.0.15 |
| Sicherheits-Header auf jeder Seite | `ezpKernelWeb::securityHeaders()` |
| eine Content Security Policy für die Administration | [Content Security Policy](../../funktionen/6.0/content-security-policy.md) |
| Einordnung von Sondierungen und Angriffen in `security.log` | [Anfrage-Logs](../../funktionen/6.0/anfrageprotokolle.md) |
| ein Audit-Trail sensibler Aktionen | [Audit-Trail](../../funktionen/6.0/pruefprotokoll.md) |

## Eine Schwachstelle melden

Wenn Sie ein Sicherheitsproblem in Exponential finden, melden Sie es vertraulich an die Adresse in `SECURITY.md` im
Wurzelverzeichnis des Repositorys, nicht in einem öffentlichen Issue.

## Verwandte Seiten

- [Sicherheit für Anwendungen](../ersteller/anwendungsentwickler/sicherheit-fuer-anwendungen.md)
- [Sicherheit in Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md)
- [Referenz zur Sicherheitshärtung](../../bc/6.0/haertung.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
