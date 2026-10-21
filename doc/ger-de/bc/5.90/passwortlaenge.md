# Änderung der vorgeschriebenen Standardlänge von Passwörtern

Mit dem ersten Release von v2019 wurde die vorgeschriebene Standardlänge von Passwörtern auf 10 Zeichen erhöht.
Vorhandene kürzere Passwörter funktionieren weiter, aber beim Anlegen neuer Passwörter muss die neue Anforderung
erfüllt werden.

Das Standardpasswort des Benutzers Admin wird nicht geändert. Sie müssen es aber selbstverständlich ändern, bevor Sie
mit einem neuen Projekt live gehen, und wenn Sie das tun, gilt die neue Regel.

Auch die Länge automatisch erzeugter Passwörter wurde erhöht, auf 16 Zeichen.

Diese Standardwerte können in site.ini [UserSettings] geändert werden, siehe MinPasswordLength und
GeneratePasswordLength.
