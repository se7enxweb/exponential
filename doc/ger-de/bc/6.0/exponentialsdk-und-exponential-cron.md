# ExponentialSDK und exponential.cron

Lesen Sie diese Seite, wenn Ihr Code die Versionsklasse `eZPublishSDK` aufruft oder wenn Ihr Deployment die
Beispiel-Crontab `ezpublish.cron` installiert. Beide Namen wurden in 6.0.15 geändert. Die alten Namen funktionieren
weiter: In einer bestehenden Installation, Erweiterung oder Crontab muss sich nichts ändern. Das Einzige, was zu tun
ist: Velocity nach dem Bereitstellen neu starten.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Die Versionsklasse ist `ExponentialSDK` (`eZPublishSDK` ist eine leere Unterklasse). Die Beispiel-Crontab ist `exponential.cron` (`ezpublish.cron` ist eine beibehaltene Kopie). |
| Wer betroffen ist | Niemand muss Code ändern. Benutzer von Velocity müssen nach dem Bereitstellen neu starten. |
| Wie Sie es prüfen | `grep -n "class ExponentialSDK" lib/version.php` und `ls exponential.cron ezpublish.cron` |
| Wie Sie es beheben | Starten Sie Velocity nach dem Bereitstellen neu. Verwenden Sie die neuen Namen in neuem Code und neuen Crontabs. |

## Die Versionsklasse: `ExponentialSDK`

`lib/version.php` deklariert `ExponentialSDK`, die Klasse, die das Release meldet. Der Kernel ruft sie unter diesem
Namen auf. Sie enthält:

- die Konstanten `VERSION_MAJOR`, `VERSION_MINOR`, `VERSION_RELEASE`, `VERSION_STATE`, `VERSION_DEVELOPMENT`,
  `VERSION_ALIAS` und `EDITION`;
- die statischen Methoden `version()`, `majorVersion()`, `minorVersion()`, `release()`, `state()`,
  `developmentVersion()`, `alias()`, `databaseVersion()` und `databaseRelease()`.

Der frühere Name, `eZPublishSDK`, ist eine leere Unterklasse in `lib/ezpublishsdk.php`:

```php
class eZPublishSDK extends ExponentialSDK
{
}
```

Jede Konstante und jede statische Methode wird geerbt, daher gibt alter Code genau das zurück, was er bisher
zurückgegeben hat:

| Alter Code | Funktioniert weiter |
|---|---|
| `eZPublishSDK::version()`, `::majorVersion()`, ... | ja, dieselben Werte |
| `eZPublishSDK::VERSION_MAJOR`, `::EDITION`, ... | ja, dieselben Werte |
| `class_exists( 'eZPublishSDK' )` | ja, über den Autoloader |
| `require 'lib/version.php'` ohne den Autoloader | ja; die Datei lädt `lib/ezpublishsdk.php` selbst, sodass beide Namen deklariert sind |
| `new ReflectionClass( 'eZPublishSDK' )`, `getConstant()` | ja; Konstanten werden geerbt |
| `is_subclass_of( 'eZPublishSDK', 'ExponentialSDK' )` | true |

Der eine Unterschied: Ein Objekt, das als `new ExponentialSDK()` erzeugt wird, ist nicht `instanceof eZPublishSDK`. Die
Klasse wird nur statisch verwendet, und es ist kein Code bekannt, der davon abhängt.

`eZPublishSDK` ist veraltet; verwenden Sie in neuem Code `ExponentialSDK`. Innerhalb der Linie 6.x wird es nicht
entfernt.

Die Datenbankzeilen `ezpublish-version` und `ezpublish-release` in `ezsite_data` behalten ihre Namen. Es sind
gespeicherte Daten, die Upgrade-Skripte lesen.

### Nach dem Aktualisieren eines laufenden Servers

Die Worker von Exponential Velocity behalten die Klassenzuordnung aus ihrem Vorwärmen. Bis Velocity neu gestartet ist,
kennen sie `ExponentialSDK` nicht, und jede Seite, die aktualisierten Code erreicht, der die Klasse aufruft, schlägt
fehl: die Setup-Ansichten, der RSS-Feed, der PDF-Export, die Seite „Über“ und der XML-Texteditor. **Starten Sie
Velocity nach dem Bereitstellen dieser Änderung neu.** PHP-FPM braucht nur sein übliches Neuladen.

## Die Beispiel-Crontab: `exponential.cron`

Die Beispiel-Crontab im Wurzelverzeichnis der Installation ist `exponential.cron`. Kopieren Sie sie, setzen Sie
`EXPONENTIALROOT` und `PHP` und installieren Sie sie:

```bash
crontab exponential.cron
```

`ezpublish.cron` bleibt erhalten, als veraltet gekennzeichnet, mit denselben Einträgen, sodass Anleitungen und
Deploy-Skripte, die den alten Dateinamen installieren, weiter funktionieren. Sie behält auch ihre alte Variable
`EZPUBLISHROOT` und den Platzhalter `/path/to/the/ez/publish/directory`, sodass ein Skript, das diese über ihren Namen
ausfüllt, sie weiterhin findet. Eine Crontab kann keine andere Datei einbinden; deshalb ist sie eine Kopie und kein
Verweis. Wenn Sie beide pflegen, halten Sie ihre Einträge gleich.

Eine früher aus `ezpublish.cron` installierte Crontab ist nicht betroffen. `crontab` kopiert den Inhalt der Datei,
daher führen die installierten Einträge `runcronjobs.php` direkt aus und lesen die Datei nie wieder.

## Verwandte Seiten

- [Versionsinformationen und der Operator expinfo](expinfo-operator.md)
- [Konsole und Cronjob-Teile](konsole.md)
- [Velocity-Engines: eine PHP-Änderung bereitstellen](velocity-engines.md#eine-php-änderung-bereitstellen-expvelocity-deploy)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
