---
title: datetime.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von datetime.ini.
keywords: Beispiele, Referenz, datetime.ini, examples, reference
type: generated
generated: exp:help:examples
---
# datetime.ini

Die ausgelieferten Einstellungen von `settings/datetime.ini`: 1 Blöcke, 1 Einstellungen.

> Exponential-Konfigurationsdatei für den Umgang mit Datum und Zeit.  
>
> HINWEIS:  Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>        eine Datei in override an, um die Werte zu setzen,  
>        die Ihre Website braucht. Legen Sie entweder  
>        eine Datei settings/override/datetime.ini.append oder  
>        settings/override/datetime.ini.append.php an, für mehr Sicherheit  
>        im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>        und kann dafür verwendet werden).  
>  Element    Beispiel                         Beschreibung  
>  -------    -------                          -----------  
>  %a         am                               Kleingeschriebenes Ante meridiem und Post meridiem.  
>  %A         AM                               Großgeschriebenes Ante meridiem und Post meridiem.  
>  %c         2004-02-12T15:19:21+00:00        Datum nach ISO 8601  
>  %d         08                               Tag des Monats, zweistellig mit führender Null.  
>  %D         Wed                              Kurzer Name eines Tages, gemäß  
>                                              dem Abschnitt "\[ShortDayNames\]" der Sprach-INI-Datei  
>                                              im Verzeichnis "share/locale".  
>  %F         October                          Voller Name eines Monats, etwa January oder March.  
>  %g         12                               Stunde im 12-Stunden-Format ohne führende Null.  
>  %G         3                                Stunde im 24-Stunden-Format ohne führende Null.  
>  %h         12                               Stunde im 12-Stunden-Format mit führender Null.  
>  %H         03                               Stunde im 24-Stunden-Format mit führender Null.  
>  %i         00                               Minuten mit führender Null  
>  %j         8                                Tag des Monats ohne führende Null  
>  %l         Wednesday                        Voller Name des Wochentags.  
>  %m         10                               Monat als Zahl, mit führender Null.  
>  %M         Oct                              Kurzer Name eines Monats, gemäß  
>                                              dem Abschnitt "\[ShortMonthNames\]" der Sprach-INI-Datei  
>                                              im Verzeichnis "share/locale".  
>  %n         10                               Monat als Zahl, ohne führende Null.  
>  %O         -0500                            Abweichung von der Greenwich-Zeit (GMT) in Stunden.  
>  %r         Thu, 21 Dec 2000 16:01:07 +0200  Datum im Format nach RFC 2822  
>  %s         00                               Sekunden, mit führender Null.  
>  %T         CDT                              Zeitzoneneinstellung dieses Rechners.  
>  %U         1065589200                       Sekunden seit der Unix-Epoche (1. Januar 1970 00:00:00 GMT).  
>  %w         3                                Wochentag als Zahl.  
>  %W         41                               Kalenderwoche nach ISO 8601, Wochen beginnen am Montag.  
>  %Y         2003                             Jahr als volle Zahl, vierstellig.  
>  %y         03                               Jahr zweistellig.  
>  %z         280                              Der Tag des Jahres.  
>  %Z         -18000                           Zeitzonenversatz in Sekunden. Der Versatz für Zeitzonen westlich von UTC ist  
>                                              immer negativ, für die östlich von UTC immer positiv.  

| Block | Einstellungen |
|---|---|
| [ClassSettings](ClassSettings.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/datetime.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
