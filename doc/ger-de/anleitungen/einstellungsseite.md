# Die Einstellungsseite: sehen, woher jeder INI-Wert kommt, und einen Wert sicher ändern

Diese Anleitung richtet sich an Administratoren, die eine INI-Einstellung in der Administrationsoberfläche statt in
einem Texteditor nachschlagen oder ändern. Sie folgt der Seite **Setup > INI-Einstellungen** (`settings/view`) und dem
Formular, zu dem sie führt (`settings/edit`). Am Ende wissen Sie, welche Datei über einen Wert entscheidet, wie Sie
jede Einstellung in jeder Datei finden, wie Sie zwei Siteaccesses vergleichen und was auf beiden Webservern geschieht,
wenn Sie speichern.

Dieselbe Arbeit in einer Shell erledigt `./console exp:ini` (`exp:ini where`, `exp:ini get`, `exp:ini set`).

## Kurz gesagt

- Jede INI-Datei wird in **Schichten** gelesen. Eine spätere Datei gewinnt: Sie ersetzt einen einfachen Wert und
  ergänzt ein Array, sofern sie das Array nicht zuerst leert.
- Die Seite zeigt für jede Einstellung den **wirksamen** Wert, die Datei, aus der er kommt, und **jede Datei, die ihn
  setzt**, in der Reihenfolge, in der sie gelesen werden (die Override-Kette).
- **Geheimnisse werden nie angezeigt**: Passwörter, Schlüssel, Tokens, Salts, Zugangsdaten und das Passwort in einem
  DSN werden verborgen. Sie sehen, ob sie gesetzt oder leer sind.
- **Durchsuchen** Sie eine Datei oder alle Dateien, zeigen Sie nur **vom Standard geänderte** Einstellungen an, oder
  **vergleichen** Sie zwei Siteaccesses.
- **Speichern** leert den INI-Cache, sodass PHP-FPM und Velocity die Änderung beide bei ihrer nächsten Anfrage
  verwenden. Einige Einstellungen liest Velocity nur beim Start: Die Seite sagt, wann `./console exp:velocity restart`
  auszuführen ist.

## 1. Eine Datei öffnen

1. Melden Sie sich an der Administrationsoberfläche an und öffnen Sie **Setup > INI-Einstellungen**.
2. Wählen Sie eine **INI-Datei** (die am häufigsten verwendeten stehen oben) und einen **Siteaccess** und klicken Sie
   dann auf **Einstellungen anzeigen**.

Die Adresse wird zu `settings/view/<siteaccess>/<file>`, zum Beispiel
`/admin/settings/view/admin/site.ini`. Sie können sie als Lesezeichen speichern.

## 2. Die Übersicht lesen

Die Zahlen oben beschreiben die Datei in diesem Siteaccess:

| Angabe | Bedeutung |
|---|---|
| Blöcke, Einstellungen, Arrays | Was die Datei enthält, nachdem jede Schicht gelesen wurde. |
| Vom Standard geändert | Einstellungen, deren wirksamer Wert von `settings/<file>.ini` abweicht. Klicken Sie darauf, um nur diese aufzuführen. |
| Ohne Standard | Einstellungen, die `settings/<file>.ini` gar nicht hat (von einer Extension oder einem Override hinzugefügt). |
| Geheimnisse, verborgen | Einstellungen, deren Wert die Seite nicht anzeigt. |
| Gelesene Dateien | Aus wie vielen Dateien sich diese Datei in diesem Siteaccess zusammensetzt. |
| Noch nicht wirksam | Einstellungen, deren Wert auf der Festplatte von dem abweicht, mit dem dieser Server läuft (siehe Schritt 6). |

**Die gelesenen Dateien, in Reihenfolge** öffnet die Ladereihenfolge selbst: `settings/<file>.ini`, die
Siteaccess-Verzeichnisse der Extensions, `settings/siteaccess/<siteaccess>`, die aktiven Extensions, dann
`settings/override`. Die Zahl neben jeder Datei gibt an, wie viele Einstellungen sie setzt. Eine Datei, die dieser
Server nicht lesen kann (falscher Eigentümer oder falsche Berechtigungen), ist als **nicht lesbar** markiert, mit einer
Warnung darüber: Ihre Einstellungen fehlen auf diesem Server.

## 3. Eine Einstellung lesen

Jeder Block ist eine Karte; die Chips über den Karten springen zu einem Block. Jede Einstellung zeigt:

- ihren **Namen**, mit `[]` für ein Array, und Badges: **geändert**, **kein Standard**, **Geheimnis, verborgen**,
  **Liste mit N** oder **N Schlüssel**, **Velocity-Neustart**, **noch nicht wirksam**;
- ihren **wirksamen Wert**. `true`/`enabled` ist grün, `false`/`disabled` rot, ein leerer Wert sagt *leer*;
- rechts, **woher er kommt**: Standard, Extension *Name*, Siteaccess *Name*, Extension *Name* für *Siteaccess* oder
  Globaler Override. Bei einem Array wird jede Datei aufgeführt, die Elemente hinzufügt, und jedes Element nennt seine
  Datei, wenn es mehr als eine gibt;
- **Standard:** den Standardwert, wenn der wirksame Wert davon abweicht;
- **In N Dateien gesetzt, M überschrieben**: Öffnen Sie es, um die Kette zu sehen. Jede Datei, die die Einstellung
  setzt, wird in Ladereihenfolge aufgeführt, mit dem, was sie tut, und ob das noch zählt:

| Zeile in einer Datei | Was sie tut | Angezeigt als |
|---|---|---|
| `Name=value` | Setzt einen einfachen Wert; frühere Werte zählen nicht mehr. | *wirksam* oder *überschrieben* |
| `Name[]` | Leert das Array: Was frühere Dateien hinzugefügt haben, entfällt. | *leert das Array* |
| `Name[]=value` | Fügt am Ende ein Element hinzu. | *fügt ein Element hinzu* |
| `Name[key]=value` | Setzt das Element *key* und ersetzt ein früheres mit demselben Schlüssel. | *setzt den Schlüssel* |

Eine Datei, deren Elemente noch Teil des Arrays sind, ist mit *Elemente wirksam* markiert; eine, deren Beitrag durch
ein späteres `Name[]` entfallen ist, mit *überschrieben*. Lange Arrays zeigen ihre ersten acht Elemente;
**N weitere anzeigen** öffnet den Rest.

## 4. Eine Einstellung finden

Das Suchfeld sucht in Blocknamen, Einstellungsnamen und Werten. Wählen Sie **Diese Datei** oder **Alle Dateien**:

- *Diese Datei* behält die Seite bei und führt nur die passenden Einstellungen auf.
- *Alle Dateien* durchsucht jede INI-Datei des Siteaccess (etwa hundert) und führt die Treffer mit ihrer Datei, ihrem
  Wert und ihrer Herkunft auf. Ein Dateiname führt zu der Einstellung.

Die Werte von Geheimnissen werden nie durchsucht. Die Suche nach einem Teil eines Passworts findet nichts, daher kann
die Suche nicht zum Erraten eines Passworts verwendet werden.

Markieren Sie **Nur vom Standard geänderte Einstellungen**, um alles auszublenden, was noch seinen Standardwert hat.

## 5. Zwei Siteaccesses vergleichen

Wählen Sie einen Siteaccess unter **Mit Siteaccess vergleichen** und klicken Sie auf **Anzeigen**. Die Seite führt nur
die Einstellungen der Datei auf, deren wirksamer Wert sich zwischen den beiden unterscheidet, mit Wert und Herkunft auf
jeder Seite. Eine Einstellung, die nur einer der beiden hat, zeigt auf der anderen Seite *nicht gesetzt*. Bei einem
Geheimnis steht nur, dass es sich unterscheidet.

## 6. Eine Einstellung ändern

1. Klicken Sie auf **Bearbeiten** neben einer Einstellung oder auf **Einstellung hinzufügen** bei einem Block.
2. **Wo sie gesetzt wird** wiederholt die Kette, sodass Sie sehen, welche Datei gewinnt, bevor Sie etwas ändern.
3. Wählen Sie den **Typ** (Zeichenkette, numerisch, true/false, enabled/disabled, Array). Ohne JavaScript klicken Sie
   auf **Typ ändern**.
4. Geben Sie den **Wert** ein:
   - Ein **Array** ist ein Element pro Zeile: `=value` fügt ein Element hinzu, `[key]=value` setzt einen Schlüssel. Das
     Feld beginnt mit einer leeren ersten Zeile: Sie leert das Array in der Datei, in die Sie speichern, vor Ihren
     Elementen, sodass das Speichern des Angezeigten das Array ersetzt, statt dieselben Elemente erneut hinzuzufügen.
     Entfernen Sie die leere Zeile, um das zu ergänzen, was frühere Dateien liefern.
   - Ein **Geheimnis** beginnt leer. Geben Sie einen neuen Wert ein, um es zu ersetzen; lassen Sie es leer, um den
     bisherigen Wert zu behalten.
5. Wählen Sie **Speichern in**:
   - **Nur Siteaccess *Name*** schreibt `settings/siteaccess/<siteaccess>/<file>.append.php`;
   - **Alle Siteaccesses (globaler Override)** schreibt `settings/override/<file>.append.php`;
   - **In einer Extension** schreibt die eigene `settings/<file>.append.php` der Extension. Ein Update der Extension
     ersetzt diese Datei, daher sind die ersten beiden vorzuziehen.
6. Klicken Sie auf **Speichern**. Sie kehren zur Einstellung auf der Einstellungsseite zurück.

Der Hinweis oben sagt, was in welche Datei geschrieben wurde und welche Caches geleert wurden:
*Query-Cache (SQL-Ergebnisse), Globaler INI Cache, INI Cache, Cache der aktiven Extensions, Cache der SSL-Zonen*.

### Wann die Änderung wirksam wird

`config.php` schaltet die Prüfung der Dateizeiten durch eZINI ab (`EZP_INI_FILEMTIME_CHECK`). Ohne diese Prüfung wird
eine geänderte Datei erst wieder gelesen, wenn der INI-Cache geleert wurde. Das Speichern auf dieser Seite leert ihn,
daher gilt:

- **PHP-FPM** (die Site auf Port 443) verwendet den neuen Wert bei seiner nächsten Anfrage;
- **Velocity** (Port 8080) liest den INI-Cache bei jeder Anfrage erneut, daher verwenden seine Worker ihn ebenfalls bei
  ihrer nächsten Anfrage;
- Einstellungen, die in `site.ini [SettingsViewSettings] RestartSettingList[]` aufgeführt sind (standardmäßig ganz
  `velocity.ini`, `site.ini [DatabaseSettings]`, `[ExtensionSettings]`, `[FileSettings] VarDir` und
  `file.ini [ClusteringSettings]`), liest Velocity nur beim Start. Sie tragen das Badge **Velocity-Neustart**, und nach
  dem Speichern einer davon fordert Sie der Hinweis auf, `./console exp:velocity restart` auszuführen, wenn Velocity
  läuft.

Wurde eine Datei auf anderem Weg geändert (ein Editor, `git pull`) und der INI-Cache nicht geleert, markiert die Seite
die Einstellungen als **noch nicht wirksam** und sagt, womit dieser Server noch läuft. Die Prüfung gilt für den
Siteaccess, in dem die Seite läuft (`admin`), auf dem Server, der geantwortet hat: Öffnen Sie die Seite auf Port 8080,
um Velocity zu prüfen. Leeren Sie den INI-Cache mit **Setup > Cache-Verwaltung** (INI-Caches) oder
`php bin/php/ezcache.php --clear-tag=ini`.

## 7. Eine Einstellung entfernen

Markieren Sie Einstellungen und klicken Sie auf **Ausgewählte entfernen**. Jede wird aus der höchsten Datei der eigenen
Einstellungen der Installation entfernt, die sie setzt (`settings/override` oder `settings/siteaccess/<siteaccess>`),
und die nächste Datei darunter oder der Standard übernimmt. Einstellungen, die nur `settings/<file>.ini` oder eine
Extension setzt, haben kein Kontrollkästchen. Das Entfernen leert den INI-Cache wie das Speichern.

## Was verborgen wird und wie Sie das ändern

Eine Einstellung ist ein Geheimnis, wenn ihr Name zu `site.ini [SettingsViewSettings] MaskedNameList[]` und zu keinem
Eintrag aus `UnmaskedNameList[]` passt, oder wenn `exp:ini` und die Maske des Audit-Protokolls sie verbergen (Namen
mit Password, Secret, Token, Salt, Credential, PrivateKey, ApiKey oder mit der Endung Key). Diese letzte Regel lässt
sich hier nicht abschalten.

- Ein Eintrag ohne `*` wird irgendwo im Namen gefunden, ohne Rücksicht auf Groß- und Kleinschreibung: `password` passt
  zu `TransportPassword`.
- Ein großgeschriebenes einzelnes Wort muss der ganze Name sein: `Key`, `DSN`.
- Ein Eintrag mit `*` ist ein Muster für den ganzen Namen und beachtet die Groß- und Kleinschreibung: `*Key` passt zu
  `LicenseKey`, nicht zu `Monkey`.

In jeder Einstellung werden außerdem das Passwort von `scheme://user:password@host` und `user:password@host` sowie der
Wert von `password=`, `pwd=`, `secret=`, `token=`, `apikey=` in einem Verbindungsstring verborgen.

`MaskRevealLastCharacters` (standardmäßig 0, höchstens 4) zeigt die letzten Zeichen eines langen Geheimnisses, damit
sich zwei Werte unterscheiden lassen. Fügen Sie einen Namen zu `MaskedNameList[]` in
`settings/override/site.ini.append.php` hinzu:

```ini
[SettingsViewSettings]
MaskedNameList[]=Pin
```

## Sicherheitsregeln, die die Seite befolgt

- Die Datei und der Siteaccess müssen solche sein, die die Installation aufführt; der Speicherort muss der Siteaccess,
  der globale Override oder eine aktive Extension sein. Ein Name mit `../`, ein Pfad oder irgendetwas anderes wird
  abgewiesen, daher kann nichts außerhalb des Einstellungsbaums geschrieben werden.
- Ein Block- oder Einstellungsname muss einer sein, den eZINI als denselben Namen zurückliest. Ein Wert mit einem
  Zeilenumbruch, einem NUL-Byte oder `*/` wird abgewiesen, weil er andere Einstellungen hinzufügen oder den
  PHP-Kommentar beenden würde, der eine `.ini.append.php`-Datei verbirgt.
- Einstellungen in `site.ini [eZINISettings] ReadonlySettingList[]` können weder geändert noch entfernt werden.
- Jedes Formular braucht das Formular-Token der Sitzung, und das Modul braucht die Richtlinie `settings`.

## Eine große Einstellungsdatei mit `[Include ...]` aufteilen

Eine Einstellungsdatei kann an einer Stelle die Einstellungen anderer Dateien einlesen. So bleibt eine lange
Override-Datei lesbar, oder ein Deployment kann pro Thema eine kleine Datei ablegen:

```ini
# settings/override/site.ini.append.php
<?php /* #?ini charset="utf-8"?

[SiteSettings]
SiteName=My site

[Include site.ini.d/*.ini]

*/ ?>
```

```ini
# settings/override/site.ini.d/10-mail.ini
[MailSettings]
AdminEmail=webmaster@example.com
```

- Der Pfad ist **relativ zu der Datei, die die Zeile enthält**, hier `settings/override/`. Platzhalter (`*`, `?`,
  `[...]`) sind erlaubt; die Dateien werden in Namensreihenfolge gelesen (`10-...` vor `20-...`), jede genau an der
  Stelle der Zeile, sodass eine Einstellung nach der Zeile gegenüber einer eingebundenen gewinnt und eine davor
  überschrieben wird.
- Die Zeile **beendet die Gruppe davor**: Jede eingebundene Datei beginnt ihre eigenen Gruppen, und Einstellungen nach
  der Zeile brauchen einen eigenen Gruppenkopf.
- Eingebundene Dateien dürfen ihrerseits Dateien einbinden. Eine Datei, die innerhalb ihrer eigenen Kette erneut
  gelesen würde (eine Schleife), wird mit einer Warnung übersprungen; ein Muster, das keine Datei trifft, ist ein
  Hinweis in der Debug-Ausgabe.
- Für eine Datei ohne eine solche Zeile ändert sich nichts. Eine Gruppe, deren Name nur mit „Include“ beginnt (wie
  `[IncludedFiles]`), ist wie bisher eine Gruppe; die Direktive ist `[Include`, gefolgt von einem Leerzeichen und einem
  Muster.
- **Caches.** Die eingebundenen Dateien gehören zum INI-Cache: `php bin/php/ezcache.php --clear-tag=ini` (und der
  INI-Schritt von `exp:velocity deploy`) erfasst eine Änderung an ihnen wie bei jeder Einstellungsdatei. `config.php`
  schaltet die Prüfung der Dateizeiten ab, daher wird auf dieser Installation eine geänderte eingebundene Datei nach
  diesem Leeren erkannt; mit eingeschalteter Prüfung baut eine geänderte, hinzugefügte oder entfernte eingebundene
  Datei den Cache von selbst neu auf. Velocity-Worker behalten die Einstellungen ihres Warm-ups und sehen die Änderung
  nach `exp:velocity restart`.
- **Bearbeiten.** Diese Seite, der klassische Einstellungseditor und `exp:ini` zeigen und ändern die Einstellungen der
  Datei selbst: Sie öffnen die eingebundenen Dateien nicht, und ein Speichern behält die Zeile `[Include ...]` genau so
  bei, wie sie geschrieben ist, und kopiert nie eingebundene Einstellungen in die Datei. Öffnen Sie die eingebundene
  Datei, um eine Einstellung zu ändern, die dort steht. Die Liste „woher kommt dieser Wert“ dieser Seite liest die
  Dateien der Ladereihenfolge selbst, daher erscheint ein in einer eingebundenen Datei gesetzter Wert nicht in dieser
  Liste.

Mit Dank an Mugo Web, deren Distribution LoveStack (mugoweb/ezpublish-legacy, Commit `f7c382bc2b`, 2018) diese
Direktive als Erste angeboten hat.

## Selbst prüfen

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/expSettingsPageTest.php
php vendor/bin/phpunit tests/tests/lib/ezutils/eZINIIncludeTest.php
```

Erwartet: `OK (13 tests, ...)`. Die Tests brauchen keine Datenbank; sie decken die Kette, das Verbergen und die
Prüfungen der Anfrage ab.

## Siehe auch

- [Betrieb einer Site](betrieb-einer-website.md)
- [Upgrade](aktualisieren.md)
- [Sichere Weiterleitungen](../funktionen/6.0/sichere-weiterleitungen.md)
- [Dateiberechtigungen beim Speichern von INI-Dateien](../bc/6.0/dateirechte-beim-speichern-von-ini-dateien.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
