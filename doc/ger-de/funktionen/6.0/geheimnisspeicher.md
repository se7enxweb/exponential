# Der Geheimnisspeicher: Passwörter und Schlüssel der Werkzeuge, verschlüsselt, nach Namen (6.0.15)

Lesen Sie diese Seite, wenn Sie ein Werkzeug schreiben, das ein Passwort, ein Token oder einen API-Schlüssel braucht
(eine Datenbankverbindung, eine Importquelle, einen entfernten Server), oder wenn Sie die Geheimnisse einer
Installation verwalten. Die ersten Nutzer sind die [Verbindungen der Datentabellen](datentabellen.md).

## Kurz gesagt

| | |
|---|---|
| Was es ist | Ein Speicher im Kernel für jedes Geheimnis, das ein Werkzeug braucht: die [Verbindungen der Datentabellen](datentabellen.md), die Importquellen von 7x SQLI Import, den entfernten Konvertierungsserver von ezodf und jedes weitere Werkzeug. Ein Werkzeug hält den **Namen** eines Geheimnisses, nie seinen Wert, und fragt den Speicher nach dem Wert, wenn es ihn braucht. |
| Wo die Werte liegen | In der Tabelle `exp_secret`, jeder Wert verschlüsselt mit XChaCha20-Poly1305 (die sodium-Erweiterung von PHP). |
| Der Schlüssel | Die Umgebungsvariable `EXP_SECRET_KEY` oder eine Schlüsseldatei, die der Speicher bei der ersten Benutzung anlegt (`var/secrets/exp_secret.key`). **Der Schlüssel liegt nicht in der Datenbank**: Eine Sicherung der Datenbank allein kann die Geheimnisse nicht entschlüsseln. |
| Wo | **Setup > Geheimnisse** (Modul `secrets`, Richtlinie `secrets/manage`): Namen, das Werkzeug, zu dem jedes gehört, ob dieser Server es entschlüsseln kann, der Schlüssel, der Schlüsselwechsel. Ein Wert wird dort eingetragen und nie wieder gezeigt. |
| Klassen | `expSecrets` (der Speicher), `expSecretKey` (der Schlüssel), `expSecret` (eine Zeile) |
| Einstellungen | `secrets.ini [SecretSettings]`: `KeyFile`, `Secrets[]` |

## Wie ein Name aufgelöst wird

`expSecrets::get( $name )` sucht in dieser Reihenfolge:

| Name | Wert |
|---|---|
| `env:<VARIABLE>` | die Umgebungsvariable des PHP-Prozesses (der PHP-FPM-Pool, die Umgebung von Velocity) |
| `<name>` | eine Zeile von `exp_secret` mit diesem Namen, mit dem Schlüssel des Speichers entschlüsselt |
| `<name>` | `secrets.ini [SecretSettings] Secrets[<name>]`, gesetzt in den eigenen Einstellungen der Site (nie in einem Repository) |
| `<name>` | die eigene Geheimnisliste des Werkzeugs, wenn es eine mitgibt (`get( $name, $fallback )`), damit die Geheimnisse, die ein Werkzeug vor 6.0.15 in seinen eigenen Einstellungen hielt, weiter gelten |

Ein Name besteht aus Buchstaben, Ziffern und `_ . : -`, höchstens 128 Zeichen. Ein Name, der in keiner Quelle steht,
ergibt `null`.

## Der Schlüssel

Der Schlüssel ist 32 Byte lang und kommt aus

1. der Umgebungsvariable **`EXP_SECRET_KEY`**, wenn sie gesetzt ist (Base64, mit oder ohne Auffüllung, oder 64
   Hexadezimalzeichen); oder
2. der **Schlüsseldatei** aus `secrets.ini [SecretSettings] KeyFile` (Vorgabe `var/secrets/exp_secret.key`, relativ zur
   Installation oder absolut), angelegt, wenn das erste Geheimnis gespeichert wird: 32 Zufallsbytes, Base64, Modus
   0600. Ihr Verzeichnis bekommt den Modus 0700 und eine `.htaccess`, die jede Anfrage ablehnt, und es darf in keinem
   Pfad liegen, den der Webserver ausliefert (`var/<site>/storage` und `var/cache/public` werden ausgeliefert).
   Schreibt ein Prozess als root (ein Befehl, Velocity), bekommen Datei und Verzeichnis den Eigentümer des
   `var/`-Verzeichnisses der Installation, damit der Benutzer des Webservers sie lesen kann. Zwei Requests, die im
   selben Moment das erste Geheimnis speichern, enden mit einem Schlüssel, nie mit je einem.

**Bewahren Sie zu jeder Sicherung eine Kopie des Schlüssels auf**, an einem eigenen, sicheren Ort. Der Schlüssel liegt
nicht in der Datenbank; eine Sicherung der Datenbank allein kann die Geheimnisse deshalb nicht entschlüsseln. Ohne den
Schlüssel wiederhergestellt, muss jedes Geheimnis neu gesetzt werden. Setup > Geheimnisse nennt den Ort des Schlüssels
und wiederholt diese Warnung.

## Verschlüsselung

Jeder Wert wird für sich mit XChaCha20-Poly1305 (`sodium_crypto_aead_xchacha20poly1305_ietf_encrypt`) und einer
zufälligen Nonce von 24 Byte verschlüsselt. Gespeichert wird Base64 aus einem Versionsbyte, der Nonce und dem
Chiffretext. Der Name des Geheimnisses und das Versionsbyte sind die zugehörigen Daten: Ein Wert, der in der Datenbank
unter einen anderen Namen verschoben oder in einem Byte verändert wurde, lässt sich nicht mehr entschlüsseln. Jede Zeile
hält außerdem die ID ihres Schlüssels (16 Hexadezimalzeichen eines Hashwerts des Schlüssels, nie den Schlüssel), damit
der Speicher weiß, welchen Schlüssel er nehmen muss, und Setup > Geheimnisse sagen kann, wie viele Geheimnisse noch
unter einem älteren Schlüssel liegen. Eine Zeile, die kein Schlüssel dieses Servers entschlüsselt, erscheint als „hier
nicht entschlüsselbar" und ergibt `null`.

## Den Schlüssel wechseln

**Schlüsseldatei** (Setup > Geheimnisse, **Schlüssel wechseln**, mit einem Haken bestätigt):

1. Ein neuer Schlüssel wird neben den aktuellen geschrieben (`exp_secret.key.next`).
2. Jedes Geheimnis wird in einer Transaktion entschlüsselt und mit dem neuen Schlüssel neu verschlüsselt.
3. Der aktuelle Schlüssel bleibt als `exp_secret.key.previous` erhalten, und der neue tritt an die Stelle von
   `exp_secret.key`.

Bis zum nächsten Wechsel entschlüsselt der vorige Schlüssel weiter, was er verschlüsselt hat. Ein Wechsel, der auf
halbem Weg stehen blieb, verliert nichts, denn eine Zeile wird immer mit dem Schlüssel entschlüsselt, den ihre ID nennt:
dem aktuellen, dem vorigen oder dem `.next`-Schlüssel eines laufenden Wechsels. Sichern Sie danach die neue
Schlüsseldatei.

**Schlüssel aus der Umgebung**: Setzen Sie den neuen Schlüssel als `EXP_SECRET_KEY` und den alten als
`EXP_SECRET_KEY_PREVIOUS`, dann auf Setup > Geheimnisse **Alle Geheimnisse mit dem aktuellen Schlüssel neu
verschlüsseln**. Wenn kein Geheimnis mehr unter dem alten Schlüssel liegt (die Seite nennt die Zahl), entfernen Sie
`EXP_SECRET_KEY_PREVIOUS`.

## Setup > Geheimnisse

| | |
|---|---|
| Der Schlüssel | woher er kommt (die Umgebung oder die Schlüsseldatei mit ihrem Pfad), seine ID, der vorige Schlüssel, die Verschlüsselung, die Warnung zur Sicherung, wie viele Geheimnisse unter einem anderen Schlüssel liegen; **Schlüssel wechseln** (Schlüsseldatei) und **Alle Geheimnisse mit dem aktuellen Schlüssel neu verschlüsseln** |
| Namen | jedes Geheimnis des Speichers und von `secrets.ini`: Name, Werkzeug (ein Link, der die Liste auf dieses Werkzeug filtert), Beschreibung, Zustand (verschlüsselt, `secrets.ini` oder hier nicht entschlüsselbar), wann es geändert wurde; **Entfernen** mit einem Haken daneben |
| Ein Geheimnis setzen | Name, Werkzeug (`datatable`, `sqliimport`, `ezodf` ...), Wert, Beschreibung. Ein neuer Name fügt ein Geheimnis hinzu, ein vorhandener ersetzt dessen Wert; mit leerem Wert ändert sich nur die Beschreibung. Das Wertfeld ist ein Passwortfeld und leer, wenn die Seite zurückkommt. |

Solange die Tabelle `exp_secret` fehlt, sagt die Seite das und bietet **Tabelle anlegen** an; bis dahin lassen sich nur
die Geheimnisse aus `secrets.ini` und Umgebungsvariablen benutzen. Die Richtlinie ist `secrets/manage`; in einer neuen
Installation hat nur die Rolle Administrator sie.

## In einem Werkzeug verwenden

```php
// der Name, den das Werkzeug hält (in seiner eigenen Zeile, seinen Einstellungen, seiner Import-Definition)
$name = $connection['secret'];

$password = expSecrets::get( $name );                      // null, wenn es keines dieses Namens gibt
$password = expSecrets::required( $name, 'Archive database' ); // eine RuntimeException mit dem Namen, nie dem Wert
if ( expSecrets::has( $name ) ) { ... }

// einen vom Benutzer eingetippten Wert speichern (nur schreibend: er wird nie in eine Seite zurückgelesen)
expSecrets::set( 'archive_db', $typed, 'datatable', 'The archive database' );
expSecrets::describe( 'archive_db', 'The archive database (read only)' );
expSecrets::remove( 'archive_db' );

// die Fehlermeldung eines Treibers kann das Passwort zitieren: maskieren, bevor sie eine Seite oder ein Protokoll erreicht
$message = expSecrets::mask( $e->getMessage(), array( $name ), array( $typed ) );

expSecrets::names( 'datatable' );   // Name => name, source, scope, description, readable, key_id, modified
expSecrets::reencryptAll();         // nachdem EXP_SECRET_KEY geändert wurde
expSecrets::rotate();               // eine Schlüsseldatei
```

`set()` lehnt einen Namen, der mit `env:` beginnt, und einen leeren Wert ab und arbeitet nur, wenn die Tabelle existiert
und der Schlüssel verfügbar ist. Ein Werkzeug mit einem Passwortfeld speichert den Wert mit `set()` unter einem Namen,
den es dann behält; die Verbindungen der Datentabellen speichern ihn als `datatable.<bezeichner>`, wenn kein anderer
Name angegeben ist.

Ein Werkzeug

- speichert einen Wert nie anders als über `expSecrets::set()` (nicht in seiner eigenen Tabelle, seinen
  Einstellungen, den Parametern eines Jobs, einer Datei, die es exportiert);
- schreibt einen Wert nie in eine Seite, eine Protokollzeile, die Debug-Ausgabe, die Historie eines Laufs oder eine
  Fehlermeldung (zuerst `mask()`);
- zeigt einen gespeicherten Wert nie wieder, auch nicht dem Benutzer, der ihn eingetippt hat.

## Tests

`tests/tests/kernel/classes/secrets/` (ohne Installation): Ver- und Entschlüsselung, Name und Schlüssel eingebunden, ein
veränderter Text abgelehnt; der Schlüssel aus der Umgebung und aus einer bei der ersten Benutzung angelegten Datei mit
Modus 0600, eine vorhandene Schlüsseldatei behalten, die Schlüssel-ID, der Wechsel und der vorige Schlüssel;
`env:`-Namen, gültige Namen und Maskierung. Die Tabelle `exp_secret` muss in `share/db_schema.dba`, der persistenten
Klasse, den drei Kernel-Schemas und den drei Update-Dateien gleich sein.
