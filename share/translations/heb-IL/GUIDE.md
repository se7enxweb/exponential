# Hebrew (heb-IL): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Modern Hebrew as used in Israel, in the neutral, technical register of current Hebrew software. No other locale
falls back to `heb-IL`; Yiddish (`yid-US`) is written in the same script but is a different language and does not
use this catalogue.

## 2. Form of address

Hebrew verbs and the pronoun "you" have a masculine and a feminine form. The catalogue avoids both, so that the
interface addresses every user the same way:

- **Buttons, menu items and links**: the verbal noun (שם פעולה), not the imperative: שמירה, ביטול, הסרה, עריכה,
  שליחה לפרסום, הסרת הנבחרים, העברה לסל המחזור. Not שמור, not לשמור.
- **Titles, column headings and tabs**: nouns: הגדרות, תרגומים, מיקום ראשי.
- **Instructions**: יש ל־ or נא ל־ with the infinitive: "יש לבחור את הפריט שישמש כבחירת ברירת המחדל ואז ללחוץ על
  "אישור".", "נא לבחור מיקום", "נא להמתין עד שהעדיפות תישמר.". Short instructions in a heading or a step take the
  verbal noun: "בחירת מיקום התחלתי". A reference ("see ...") is "יש לעיין ב־...", not ראה or ראו.
- **Confirmations and questions**: האם with the infinitive: "האם להסיר מחלקה זו?", "האם להעתיק את המחלקה
  <%class_name>?". Not "האם אתה בטוח".
- **What the user can or may do**: impersonal: "ניתן ליצור ...", "לא ניתן לשנות את השפה הראשית ...", "אין לך הרשאה
  להסיר מחלקות.". Describe a field by what it does: "שדה זה משמש לקביעת ...", not "השתמש בשדה זה".
- Not allowed: masculine or feminine imperatives (בחר, בחרי), אתה / את as the subject, slash forms (בחר/י, יכול/ה).
  Past forms of the second person that read the same for both genders in unpointed writing (שלחת) are acceptable
  where a sentence cannot be turned.

## 3. Script and typography

- Hebrew script without vowel points, written right to left. The administration turns right to left by itself; a
  translation needs no direction marks. Placeholders (`%name`, `%1`) stay as they are inside the Hebrew sentence.
- **Prefix letters before Latin text, digits and placeholders** (ה, ב, ל, מ, ש, ו) are joined with the maqaf ־
  (U+05BE), not a hyphen: "ה־URL", "ב־%scope", "ל־10000", "קובץ ה־CSS".
- **Abbreviations**: a straight double quote " before the last letter (דוא"ל, מע"מ, סה"כ) and the geresh ׳ after a
  shortened word (דק׳, שנ׳) or for a foreign sound (צ׳אט).
- **Quotation marks** as in the source: straight "..." where the source has them, “...” where it has those.
- **Punctuation**: Latin comma, full stop, colon, semicolon and question mark. The dash in a list item such as
  "%language – שפה ראשית" is the en dash as in the source.
- **Numbers**: digits 0–9.
- **Counts**: the catalogue has no plural forms. Write "%count אובייקטים", or a label with a count.

## 4. Glossary

Binding for the kernel and every extension.

| English | Hebrew | Note |
|---|---|---|
| content | תוכן | |
| content object | אובייקט תוכן | |
| object | אובייקט | |
| node | צומת | |
| location | מיקום | main location: מיקום ראשי |
| class | מחלקה | the content class; class group: קבוצת מחלקות |
| content class | מחלקת תוכן | |
| attribute | מאפיין | |
| datatype | סוג נתונים | |
| version | גרסה | |
| draft | טיוטה | |
| publish | פרסום | published: פורסם |
| send for publishing | שליחה לפרסום | |
| store draft | שמירת טיוטה | |
| discard | ביטול | discard draft: מחיקת טיוטה |
| archived | בארכיון | an archived version |
| rejected | נדחה | |
| pending | ממתין | |
| siteaccess | גישת אתר | plural: גישות אתר |
| section | מדור | the Exponential section; an INI section or a part of a page: מקטע |
| role | תפקיד | |
| policy | מדיניות | plural the same |
| limitation | הגבלה | |
| module | מודול | |
| function | פונקציה | |
| user | משתמש | |
| user group | קבוצת משתמשים | |
| anonymous | אנונימי | |
| administrator | מנהל מערכת | |
| login (the name) | שם התחברות | the field |
| username | שם משתמש | |
| log in | התחברות | sign in: כניסה |
| log out | התנתקות | |
| password | סיסמה | |
| e-mail | דוא"ל | |
| cache | מטמון | clear cache: ניקוי מטמון |
| template | תבנית | |
| override | דריסה | override template: תבנית דריסה |
| fallback | חלופה | the fallback chain: שרשרת החלופות |
| workflow | זרימת עבודה | |
| trigger | טריגר | |
| event | אירוע | |
| trash | סל המחזור | empty trash: ריקון סל המחזור |
| bookmarks | סימניות | |
| settings | הגדרות | configuration |
| setup | הגדרות | the Setup tab |
| layout | פריסה | Exponential Layouts; layouts: פריסות |
| block | בלוק | |
| zone | אזור | |
| extension | הרחבה | |
| package | חבילה | |
| search index | אינדקס חיפוש | indexing: אינדוקס |
| search engine | מנוע חיפוש | |
| log | יומן | a log file; never "log in" |
| queue | תור | job queue: תור משימות |
| job | משימה | |
| cron | cron | a cron job: משימה מתוזמנת |
| script | סקריפט | |
| worker | תהליך עבודה | plural: תהליכי עבודה |
| kernel | ליבה | ליבת המערכת where "core" alone is unclear |
| folder | תיקייה | |
| file | קובץ | |
| image | תמונה | thumbnail: תמונה ממוזערת |
| preview | תצוגה מקדימה | |
| upload | העלאה | |
| download | הורדה | |
| export | ייצוא | |
| import | ייבוא | |
| remove | הסרה | take out of a list or place |
| delete | מחיקה | destroy |
| edit | עריכה | |
| editor | עורך | |
| save / store | שמירה | the verb "Store" of the forms |
| Store (shop tab) | חנות | the shop, as "Shop" |
| cancel | ביטול | |
| OK | אישור | confirm: אישור |
| back | חזרה | |
| next / previous | הבא / הקודם | |
| enable / disable | הפעלה / השבתה | |
| enabled / disabled | מופעל / מושבת | |
| active / inactive | פעיל / לא פעיל | |
| run | הרצה | a script, a job; הפעלה is enable and session |
| start | התחלה | |
| session | הפעלה | plural: הפעלות |
| hidden / visible | מוסתר / גלוי | |
| archive | ארכיון | |
| order (sort) | סדר | |
| order (shop) | הזמנה | orders: הזמנות |
| shop | חנות | webshop: חנות מקוונת |
| basket | סל קניות | |
| product | מוצר | |
| customer | לקוח | |
| discount | הנחה | |
| VAT | מע"מ | |
| tag | תגית | keyword and HTML tag alike |
| translation | תרגום | |
| language | שפה | main language: שפה ראשית |
| locale | הגדרות אזוריות | |
| state | מצב | the object state; object states: מצבי אובייקט |
| status | מצב | order status: סטטוס הזמנה |
| view (button) | תצוגה | |
| format | פורמט | |
| sort | מיון | |
| search | חיפוש | |
| filter | סינון | |
| select | בחירה | select all: בחירת הכול |
| browse | דפדוף | |
| copy | העתקה | |
| move | העברה | |
| add | הוספה | |
| create | יצירה | |
| new | חדש | |
| restore | שחזור | |
| install / uninstall | התקנה / הסרת התקנה | |
| sub items | פריטי משנה | |
| subtree | תת־עץ | |
| related objects | אובייקטים קשורים | |
| URL alias | כינוי URL | |
| dashboard | לוח בקרה | |
| content structure | מבנה התוכן | |
| media library | ספריית מדיה | |
| identifier / ID | מזהה | |
| priority | עדיפות | |
| owner | בעלים | |
| modified / created | שונה / נוצר | |
| none | ללא | |
| yes / no | כן / לא | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is חנות, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is שמירה.
