---
title: Sicherheit in Erweiterungen
audience: developer, app-creator
category: extension-development
order: 310
summary: Die Sicherheitsregeln, die jede Erweiterung befolgt - Richtlinien an Views, POST und Formular-Token, Maskieren der Ausgabe, sicheres SQL, nicht vertrauenswürdige Eingaben, sichere Weiterleitungen, Dateien, Geheimnisse und kein Inline-Code.
keywords: Sicherheit, Zugriffskontrolle, Richtlinie, Formular-Token, Maskieren, Eingabeprüfung, Weiterleitung, Geheimnisse, security, access control, policy, CSRF, ezformtoken, ezxform_token, XSS, wash, SQL injection, escapeString, input validation, redirect, eZRedirectManager, path traversal, secrets, CSP
type: help
---
# Sicherheit in Erweiterungen

Teil 3, Kapitel 31. Eine Erweiterung läuft mit den vollen Rechten des Kernels. Ein Fehler darin ist ein Fehler in jeder
Website, die sie installiert. Dieses Kapitel ist die Checkliste, der der eigene Code des Kernels folgt, mit dem
Mechanismus zu jeder Regel.

## 1. Jede View hat eine Richtlinie

```php
$ViewList['admin'] = array( 'script' => 'admin.php', 'functions' => array( 'administrate' ) );
$FunctionList['administrate'] = array();
```

Eine View ohne `'functions'` steht jedem offen, der die Website erreicht. Setzen Sie Einschränkungen in Ihrem Code
durch, und verwenden Sie für Inhalte die eigenen Prüfungen der Inhaltsobjekte (`canRead()`, `canEdit()`). Siehe
[Richtlinienfunktionen und Zugriffsprüfungen](richtlinienfunktionen-und-zugriff.md).

## 2. Änderungen brauchen POST und das Formular-Token

Cross-Site Request Forgery: Eine Seite anderswo bringt den Browser des Besuchers dazu, mit dessen Sitzung einen Request
an Ihre Website zu senden. Abwehr:

- Ändern Sie Daten nur bei **POST**; ein GET-Request darf nie etwas ändern (ein `<img src=".../delete/5">` würde es
  sonst tun).
- Die Erweiterung **ezformtoken** fügt jedem Formular in der Ausgabe ein verstecktes Feld `ezxform_token` hinzu und
  prüft es bei jedem POST (`ezxFormToken::output()` und `ezxFormToken::input()`, als Listener von `response/output`
  und `request/input` eingehängt). Lassen Sie sie aktiv. Ein POST ohne gültiges Token wird abgewiesen, bevor Ihre
  View läuft.
- Ajax: `Exp.io` sendet das Token bei seinen POST-Aufrufen mit ([Ajax mit ezjscore-Serverfunktionen](ajax-mit-ezjscore-serverfunktionen.md)).

Einzelheiten: [ezformtoken und CSRF-Schutz](../teil-8-referenz/ezformtoken-und-csrf.md).

## 3. Die Ausgabe maskieren

Wenden Sie in Templates `|wash` auf jeden Wert an, der von einer Person stammen kann: Namen, Titel, Suchbegriffe,
View-Parameter, Attributwerte, die Sie als Text ausgeben. In PHP, das HTML erzeugt, verwenden Sie
`htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' )`. In einer JavaScript-Zeichenkette in einem Data-Attribut oder in
JSON `|wash( 'javascript' )` oder `json_encode()`.

## 4. Sicheres SQL

| Tun Sie | Tun Sie nicht |
|---|---|
| Bedingungen von `eZPersistentObject` (für Sie maskiert) | SQL aus Request-Werten zusammensetzen |
| `(int)` für jede Zahl, die Sie in SQL einsetzen | einer ID vertrauen, weil sie „aus unserem eigenen Formular kommt“ |
| `$db->escapeString( $text )` und Anführungszeichen für Zeichenketten | unmaskierten Text per Zeichenkettenverkettung einfügen |
| eine Liste erlaubter Spaltennamen zum Sortieren | einen Spaltennamen, der aus der Adresse stammt |

## 5. Eingaben sind nicht vertrauenswürdig, auch ihr Typ

Ein gesendeter Wert kann ein Array sein (`name[]=x`), sehr lang sein oder fehlen. Prüfen Sie mit `is_string()`, bevor
Sie ihn als Text behandeln, wandeln Sie Zahlen um, vergleichen Sie Auswahlwerte mit festen Listen und begrenzen Sie
Längen. Die Datentypen des Kernels wurden im September 2026 auf dieselbe Weise gehärtet
([Härtung der Datentyp-Eingaben](../../../spezifikationen/6.0/datentyp-eingabehaertung.md)).

## 6. Weiterleitungen führen nur dorthin, wo sie hinführen sollen

Ein Weiterleitungsziel aus einem Formularfeld, der Sitzung oder der Adresse kann einen Besucher auf eine andere Website
schicken. Verwenden Sie die Regeln des Kernels: `eZRedirectManager` (`kernel/classes/ezredirectmanager.php`) wählt ein
sicheres Ziel für „zurück“ (`redirectTo()`, `returnURI()`), und seine Regeln gelten für jedes Ziel, das nicht aus dem
Code selbst stammt. Siehe [Sichere Weiterleitungen](../../../../eng-us/features/6.0/safe-redirects.md).

## 7. Dateien und Pfade

- Bilden Sie nie einen Pfad aus einer Eingabe, ohne Verzeichnisbestandteile zu entfernen (mindestens `basename()`),
  und prüfen Sie, dass das Ergebnis innerhalb des gemeinten Verzeichnisses bleibt.
- Speichern Sie Uploads über die Datei- und Cluster-Handler des Kernels unter `var/<site>/storage`, nie im Web-Root.
- Senden Sie Downloads mit den Hilfsfunktionen des Kernels, die den Dateinamen sicher in Anführungszeichen setzen
  (`eZFile::downloadHeaders()`).

## 8. Geheimnisse

Committen oder liefern Sie nie Schlüssel, Passwörter oder Tokens aus; lesen Sie sie aus Einstellungen, die die
Installation in ihrem eigenen `settings/override/` füllt, benennen Sie sie so, dass sie maskiert werden, und
protokollieren Sie sie nie
([.ini.append.php-Dateien und sichere Einstellungen](../teil-2-builder/append-php-dateien-und-sicherheit.md)).

## 9. Keine Inline-Skripte oder -Styles

Die Content Security Policy der Administration lehnt Inline-Code ab; sie ist zugleich eine zusätzliche
Verteidigungslinie gegen Cross-Site-Scripting. Siehe
[Content Security Policy für Autoren von Erweiterungen](content-security-policy-fuer-erweiterungen.md).

## 10. Erzeugter Code ist Code

Erweiterungen, die die RAD-Assistenten schreiben, sind durch acht Schutzmechanismen gesichert (Werte können nicht aus
Kommentaren oder INI-Zeilen ausbrechen, nichts wird außerhalb von `extension/<name>` geschrieben, erzeugte Templates
maskieren die Ausgabe, erzeugte Views prüfen die Eingabe). Prüfen Sie, was jeder Generator schreibt, auch Ihre eigenen
Skripte. Siehe [RAD-Werkzeuge: Sicherheit](../../../bc/6.0/rad-sicherheit.md).

## Eine Sicherheitslücke melden

Wenn Sie ein Sicherheitsproblem in einer Erweiterung finden, die Sie nicht geschrieben haben, melden Sie es vertraulich
an deren Betreuer; für den Kernel und die se7enxweb-Erweiterungen nennt `SECURITY.md` die Adresse
`security@se7enx.com` und die vertrauliche Meldung von Sicherheitslücken auf GitHub
([Die Community um Hilfe bitten](../teil-1-vom-benutzer-zum-entwickler/die-community-fragen.md)).

## Verwandte Seiten

- Zurück: [Kernel-Klassen-Overrides: das letzte Mittel](kernel-overrides-der-letzte-ausweg.md)
- Weiter: [Content Security Policy für Autoren von Erweiterungen](content-security-policy-fuer-erweiterungen.md)
- [Eine alte Erweiterung bewerten, bevor Sie sie einsetzen](../teil-1-vom-benutzer-zum-entwickler/eine-alte-erweiterung-bewerten.md)
- [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
