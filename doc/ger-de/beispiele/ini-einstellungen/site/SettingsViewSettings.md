---
title: site.ini [SettingsViewSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SettingsViewSettings] in site.ini: CommonINIFileList, MaskedNameList, UnmaskedNameList, MaskRevealLastCharacters, RestartSettingList"
keywords: Einstellung, site.ini, SettingsViewSettings, INI, setting, CommonINIFileList, MaskedNameList, UnmaskedNameList, MaskRevealLastCharacters, RestartSettingList
type: generated
generated: exp:help:examples
---
# site.ini [SettingsViewSettings]

Der Block `[SettingsViewSettings]` von `settings/site.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## CommonINIFileList

> Die INI-Dateien, die oben im Auswahlfeld unter Setup > Einstellungen ansehen angeboten werden,  
> über einem Trenner, vor den übrigen in alphabetischer Reihenfolge. Diese Installation  
> hat gut hundert INI-Dateien, und eine der etwa zwölf zu finden, die  
> tatsächlich jeden Tag gelesen werden, ist das ganze Problem.  
>
> Die Reihenfolge richtet sich danach, wie viel jede Datei hier wirklich verwendet wird, auf drei Arten gezählt:  
> wie viele Override-Dateien es für sie gibt, wie oft das PHP sie liest und wie  
> oft die Templates sie lesen. Alles Aufgeführte, was diese Installation nicht  
> hat, wird übergangen, eine verkleinerte Installation zeigt also eine kürzere Liste statt  
> toter Einträge.  
>
> Ergänzen Sie sie nach Belieben: Dies ist eine Bequemlichkeit, und nichts hängt davon ab.  
> Die Hauptdatei: alles über die Website und ihre Siteaccesses.  
> Datentypen, Versionierung, der Baum, View-Caching.  
> Designs, Stylesheets, JavaScript und wo nach ihnen gesucht wird.  
> Die Regeln der Template-Overrides: welches Template für welche Inhalte antwortet.  
> Die Menüs der Website.  
> Das Baummenü der Administration links.  
> Module, ihre Ansichten und wer sie erreichen darf.  
> Die Template-Engine: Caching, Kompilierung, Debugging.  
> Bild-Aliase und die Umwandlungsregeln dahinter.  
> Die Reiter und Bedienelemente der Administrationsoberfläche.  
> Der Inhaltsbrowser: wo jede Durchsuchen-Schaltfläche beginnt und was sie wählen darf.  
> Workflows und die Ereignisse, aus denen sie bestehen.  
> Was nach welchem Cron-Zeitplan läuft.  
> Umgang mit Dateien und dem Cluster, wo die var-Dateien liegen.  
> Zeichensätze und Übersetzungen.  

Standard: eine Liste mit 15: `site.ini`, `content.ini`, `design.ini`, `override.ini`, `menu.ini`, `contentstructuremenu.ini`, `module.ini`, `template.ini`, `image.ini`, `admininterface.ini`, `browse.ini`, `workflow.ini` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
# ein leeres "CommonINIFileList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CommonINIFileList[]
CommonINIFileList[]=site.ini
CommonINIFileList[]=content.ini
CommonINIFileList[]=design.ini
CommonINIFileList[]=override.ini
CommonINIFileList[]=menu.ini
CommonINIFileList[]=contentstructuremenu.ini
CommonINIFileList[]=module.ini
CommonINIFileList[]=template.ini
CommonINIFileList[]=image.ini
CommonINIFileList[]=admininterface.ini
CommonINIFileList[]=browse.ini
CommonINIFileList[]=workflow.ini
CommonINIFileList[]=cronjob.ini
CommonINIFileList[]=file.ini
CommonINIFileList[]=i18n.ini
*/ ?>
```

## MaskedNameList

> Einstellungen, deren Werte die Einstellungsseiten (settings/view, settings/edit) nie  
> zeigen: nur, ob sie gesetzt oder leer sind. Eine Einstellung ist ein Geheimnis, wenn ihr Name  
> auf einen Eintrag aus MaskedNameList\[\] und auf keinen aus UnmaskedNameList\[\] passt, oder wenn  
> sie eine ist, die exp:ini und das Audit-Log maskieren (Password, Secret, Token, Salt,  
> ...Key, Credential: diese lassen sich hier nicht abschalten). Ein Eintrag ohne '\*'  
> wird überall im Namen gefunden, ohne Beachtung der Groß-/Kleinschreibung ("password" passt auf  
> TransportPassword); ein großgeschriebenes einzelnes Wort ("Key", "DSN") muss der  
> ganze Name sein; ein Eintrag mit '\*' ist ein Muster für den ganzen Namen und beachtet die Groß-/Kleinschreibung  
> ("\*Key" passt auf LicenseKey, nicht auf Monkey). Ein Passwort in einem beliebigen Wert  
> (scheme://user:password@host, password=... in einer Verbindungszeichenkette) wird ebenfalls  
> maskiert, und der Wert eines Geheimnisses wird nie durchsucht.  

Standard: eine Liste mit 17: `password`, `passwd`, `passphrase`, `secret`, `token`, `salt`, `credential`, `privatekey`, `apikey`, `Key`, `*Key`, `*_key` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
# ein leeres "MaskedNameList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MaskedNameList[]
MaskedNameList[]=password
MaskedNameList[]=passwd
MaskedNameList[]=passphrase
MaskedNameList[]=secret
MaskedNameList[]=token
MaskedNameList[]=salt
MaskedNameList[]=credential
MaskedNameList[]=privatekey
MaskedNameList[]=apikey
MaskedNameList[]=Key
MaskedNameList[]=*Key
MaskedNameList[]=*_key
MaskedNameList[]=*_KEY
MaskedNameList[]=*-key
MaskedNameList[]=DSN
MaskedNameList[]=*Dsn
MaskedNameList[]=*DSN
*/ ?>
```

## UnmaskedNameList

> Namen, auf die die Einträge oben passen würden, die aber keine Geheimnisse sind (ganze Namen).  

Standard: eine Liste mit 11: `SortKey`, `CacheKey`, `PrimaryKey`, `ForeignKey`, `IndexKey`, `ShortcutKey`, `HotKey`, `GroupKey`, `SectionKey`, `LanguageKey`, `IdKey`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
# ein leeres "UnmaskedNameList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
UnmaskedNameList[]
UnmaskedNameList[]=SortKey
UnmaskedNameList[]=CacheKey
UnmaskedNameList[]=PrimaryKey
UnmaskedNameList[]=ForeignKey
UnmaskedNameList[]=IndexKey
UnmaskedNameList[]=ShortcutKey
UnmaskedNameList[]=HotKey
UnmaskedNameList[]=GroupKey
UnmaskedNameList[]=SectionKey
UnmaskedNameList[]=LanguageKey
UnmaskedNameList[]=IdKey
*/ ?>
```

## MaskRevealLastCharacters

> Die letzten Zeichen eines Geheimnisses zeigen (höchstens 4), damit sich zwei Werte  
> unterscheiden lassen, und nur bei einem Wert, der mindestens viermal so lang ist. 0 zeigt keine.  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
MaskRevealLastCharacters=0
*/ ?>
```

## RestartSettingList

> Einstellungen, die ein laufender Velocity-Server nur beim Start liest: Nach einer Änderung  
> sagt die Einstellungsseite, dass er neu gestartet werden muss (exp:velocity restart). Eine Datei, eine  
> Datei/ein Block oder eine Datei/ein Block/eine Einstellung. Jede andere Einstellung erreicht PHP-FPM und  
> Velocity bei ihrer nächsten Anfrage, weil ein Schreibvorgang den INI-Cache leert.  

Standard: eine Liste mit 5: `velocity.ini`, `site.ini/ExtensionSettings`, `site.ini/DatabaseSettings`, `site.ini/FileSettings/VarDir`, `file.ini/ClusteringSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
# ein leeres "RestartSettingList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RestartSettingList[]
RestartSettingList[]=velocity.ini
RestartSettingList[]=site.ini/ExtensionSettings
RestartSettingList[]=site.ini/DatabaseSettings
RestartSettingList[]=site.ini/FileSettings/VarDir
RestartSettingList[]=file.ini/ClusteringSettings
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
