# Ersatzsprache für Inhalte: eng-US und eine Einstellung statt eines fest eingetragenen eng-GB (6.0.15)

Lesen Sie diese Seite, bevor Sie eine Installation auf den Stand der Änderungen vom 5. Oktober 2026 bringen. Sie
beschreibt, was sich geändert hat, wie Sie es prüfen und was zu tun ist.

## Was sich geändert hat

| Änderung | Wie Sie es prüfen | Was zu tun ist |
|---|---|---|
| Namen und Beschreibungen von Inhaltsklassen, Klassenattributen, Klassengruppen und Zuständen werden unter einer Locale gespeichert. Wenn noch keine Inhaltssprache priorisiert ist (der Setup-Assistent und Kommandozeilenskripte legen Klassen an, bevor die Sprachen der Website existieren) und `ContentObjectLocale` nicht gesetzt ist, verwendete `eZSerializedObjectNameList` ein fest eingetragenes `eng-GB`. Jetzt liest es die neue Einstellung `site.ini [RegionalSettings] ContentObjectFallbackLocale` und verwendet `eng-US`, wenn die Einstellung fehlt oder leer ist. | Mit `php bin/php/ezexec.php` ein Skript ausführen, das `eZSerializedObjectNameList::fallbackLanguageLocale()` ausgibt; die Antwort ist `eng-US`, sofern Sie die Einstellung nicht gesetzt haben. | Nichts, es sei denn, Ihre Websites sind in einer anderen Sprache geschrieben und Sie möchten diese als letzten Ausweg: Setzen Sie `ContentObjectFallbackLocale=<locale>` in `settings/override/site.ini.append.php`. |
| Der ausgelieferte Standard von `site.ini [RegionalSettings] ContentObjectLocale` ist `eng-US` (bisher `eng-GB`). Das Installationsprogramm schreibt `ContentObjectLocale` immer in jeden Siteaccess, daher bemerkt es nur ein Siteaccess ohne eigenen Wert. | `grep -rn ContentObjectLocale settings/siteaccess settings/override` | Ein Siteaccess, der sich auf den alten Standard verlassen hat und wirklich in `eng-GB` geschrieben ist, setzt `ContentObjectLocale=eng-GB` selbst. |
| Die Grunddaten des Kernels in SQL (`kernel/sql/common/cleandata.sql`, `kernel/sql/sqlite/cleandata.sql`) sind wie `share/db_data.dba` in `eng-US`: Die Sprachzeile ist `eng-US` „English (American)“, die Namen von Klassen, Attributen und Objekten sind `eng-US`. Die Sprach-IDs sind unverändert. | `grep -c eng-GB kernel/sql/common/cleandata.sql` gibt 0 aus. | Nichts für bestehende Websites; eine aus diesen Dateien erzeugte Datenbank enthält kein `eng-GB`. |
| `kickstart.ini--example` führt `eng-GB` nicht mehr als zweite Sprache auf; `kickstart.ini-dist` zeigt `eng-US` als Beispiel für die Hauptsprache. Eine Kickstart-Datei, die `Languages[]=eng-GB` aufführt, fügt diese Sprache der neuen Website hinzu. | Ihre `kickstart.ini`, `[language_options]`. | Entfernen Sie `Languages[]=eng-GB`, sofern Sie keine britisch-englische Übersetzung möchten. |
| Die Installationspakete für Multisites (`sevenx_multisite`, `sevenx_multisite_clean`) benennen die Ordnerattribute `tags` und `publish_date` in der konfigurierten Locale, sonst in der Ersatz-Locale des Kernels; bisher fielen sie auf `eng-GB` zurück. | | Nichts. |

Absichtlich unverändert: `eng-GB` ist weiterhin eine unterstützte Locale (`share/locale/eng-GB.ini`), die
Ausgangssprache der Oberflächentexte (`Locale=eng-GB` schaltet die Textübersetzung aus) und der Ersatz in `i18n.ini`
für die Übersetzungen der Oberfläche.

## eng-GB aus einer bestehenden Website entfernen

Eine Website, die `eng-GB` nur als Nebenprodukt erhalten hat (eine alte Kickstart-Datei, der obige Ersatz, die
Klassenpakete von `cjw_newsletter` vor 4.2.1), kann es unter **Setup > Sprachen** entfernen, sobald nichts es mehr
verwendet. `eZContentLanguage::removeLanguage()` des Kernels verweigert das, solange ein Objekt oder eine Klasse eine
Übersetzung in dieser Sprache hat; Beschreibungen von Klassenattributen, die mit einem leeren `eng-GB`-Eintrag
gespeichert sind, `eng-GB`-Namen von Entwürfen und URL-Aliase entfernter Knoten zählt es nicht mit, sie halten aber
das Bit der Sprache in der Datenbank. Wandeln Sie diese zuerst um: Verschieben Sie jeden `eng-GB`-Eintrag einer
serialisierten Namensliste nach `eng-US` (ein nicht leerer `eng-US`-Text bleibt erhalten), entfernen Sie die
`eng-GB`-Namenszeilen von Versionen, die eine `eng-US`-Zeile haben, und entfernen Sie die URL-Aliase entfernter Knoten
mit `eZURLAliasML::removeByAction()`. Leeren Sie dann die Caches `content`, `classid`, `content_language`, `sortkey`,
`urlalias`, `template`, `template-block` und den INI-Cache.
