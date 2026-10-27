# Mongolian (mon-MN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Khalkha Mongolian (монгол хэл) as written in Mongolia, in the neutral, technical register of current
Mongolian software (the Mongolian localisations of Windows, Microsoft Office, Google and Mozilla) and of the Mongolian
Wikipedia. No other locale falls back to `mon-MN`; Russian (`rus-RU`) is a different language and only helps with
terminology.

- Mongolian words are used where the software already has them: хадгалах, устгах, тохиргоо, хавтас, татаж авах,
  байршуулах, холбоос, таб, нууц үг. Internationalisms of computing keep their usual spelling: сервер, скрипт,
  модуль, кэш, файл, браузер, програм.
- No Russian words where Mongolian has its own (not "настройка", "папка", "пароль" for тохиргоо, хавтас, нууц үг).
- Mongolian suffixes follow vowel harmony and the final sound of the stem: файлыг, серверт, модулийн, кэшийг.

## 2. Form of address

The interface addresses the user with the polite **Та** (you), **Таны** (your), as Mongolian software does.

- **Buttons, menu items and links**: the verb in -х (the form Mongolian software uses on buttons): Хадгалах,
  Цуцлах, Устгах, Засах, Нийтлэх, Нийтлэхээр илгээх, Сонгосныг хасах, Хогийн сав руу зөөх. Not the imperative
  (Хадгална уу) on a button.
- **Titles, column headings and tabs**: nouns or noun phrases: Тохиргоо, Орчуулга, Үндсэн байршил.
- **Instructions**: the polite request in -на уу / -нэ үү / -но уу / -нө үү: "Зүйлийг сонгоод «OK» товчийг дарна уу.",
  "Түр хүлээнэ үү ...". A reference ("see ...") is "... -ыг үзнэ үү".
- **Confirmations and questions**: "Та ... -ыг устгахдаа итгэлтэй байна уу?", "Та ... хүсэж байна уу?".
- **What the user can or may do**: "Та ... боломжтой", "Та ... боломжгүй", or impersonal: "Үндсэн хэлийг өөрчлөх
  боломжгүй ...". "You do not have permission" is "Танд ... эрх байхгүй".
- Not allowed: the familiar чи / чиний, slash forms.

## 3. Script and typography

- **Cyrillic**, the script of the shipped locale (`share/locale/mon-MN.ini`, LanguageName Монгол) and of the
  software in use in Mongolia. The 35 letters of the Mongolian Cyrillic alphabet are written with their own code
  points: ө (U+04E9) and ү (U+04AF), never the look-alike Latin or Kazakh letters.
- **The traditional Mongolian script**: the state has promoted the traditional (Uyghur-derived, vertical) script
  again, and official documents carry it next to Cyrillic since 2025. Software and the web in Mongolia still use
  Cyrillic, and so does this catalogue. A catalogue in the traditional script would be a locale of its own.
- **Latin text inside Mongolian**: product names, codes and acronyms stay in Latin letters. A suffix after an
  acronym or product name follows a hyphen ("Exponential-ийн"); a wording without a suffix often reads better:
  "PHP файл", "URL хаяг", "HTML код".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay in Latin letters, unchanged, and stand free:
  a space before and after them, or a punctuation mark right after. Never a Mongolian letter glued to a
  placeholder: "%countобъект" is a different placeholder. Where the sentence needs a case ending, it follows a
  hyphen, the way Mongolian writes it after a numeral or a Latin word ("%name-ийн", "%2-оос"); where it reads as
  well, put the placeholder where it needs no ending: "Объект: %name", "%count зүйл".
- **Numbers**: a noun after a number stays in the singular, as Mongolian grammar wants it: "%count объект", not
  "объектууд". Digits 0-9.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: comma, full stop, colon, semicolon, question and exclamation mark as in the source. A label
  keeps its colon. Mongolian puts the verb at the end of the sentence and the modifier before its noun.

## 4. Glossary

Binding for the kernel and every extension.

| English | Mongolian | Note |
|---|---|---|
| content | агуулга | |
| content object | агуулгын объект | |
| object | объект | |
| node | зангилаа | main node: үндсэн зангилаа; parent node: эх зангилаа; child: дэд |
| location | байршил | main location: үндсэн байршил |
| item (list entry) | зүйл | |
| class | анги | the content class; class group: ангийн бүлэг; a PHP class: PHP класс |
| content class | агуулгын анги | |
| attribute | атрибут | |
| datatype | өгөгдлийн төрөл | |
| version | хувилбар | version history: хувилбарын түүх |
| draft | ноорог | store draft: ноорог хадгалах |
| publish | нийтлэх | published: нийтлэгдсэн |
| send for publishing | нийтлэхээр илгээх | |
| discard | хаях | discard draft: ноорог хаях |
| archived | архивлагдсан | |
| pending | хүлээгдэж буй | |
| rejected | татгалзсан | |
| siteaccess | сайтын хандалт | the configuration unit |
| site, website | сайт, вэб сайт | |
| section | хэсэг | the Exponential section |
| role | үүрэг | |
| policy | бодлого | |
| limitation | хязгаарлалт | |
| permission | зөвшөөрөл | access rights: хандах эрх |
| module | модуль | |
| function | функц | |
| user | хэрэглэгч | |
| user group | хэрэглэгчийн бүлэг | |
| group | бүлэг | |
| anonymous | нэргүй | anonymous user: нэргүй хэрэглэгч |
| administrator | администратор | |
| administration (interface) | удирдлагын интерфейс | |
| username | хэрэглэгчийн нэр | the column "Login" too |
| password | нууц үг | |
| log in / sign in | нэвтрэх | |
| log out / sign out | гарах | |
| e-mail | имэйл | e-mail address: имэйл хаяг |
| cache | кэш | clear cache: кэш цэвэрлэх |
| template | загвар | |
| override | дарж бичих | override template: дарж бичих загвар |
| fallback | нөөц | fallback language: нөөц хэл |
| design | дизайн | |
| workflow | ажлын урсгал | |
| trigger | гох | |
| event | үйл явдал | |
| trash | хогийн сав | empty trash: хогийн савыг хоослох |
| bookmark | хавчуурга | |
| settings | тохиргоо | |
| setup | тохируулга | the Setup tab; installation: суулгалт |
| layout | зохион байгуулалт | Exponential Layouts |
| block | блок | |
| zone | бүс | |
| extension | өргөтгөл | |
| package | багц | |
| search | хайлт | the button: Хайх |
| log | бүртгэл | log file: лог файл |
| queue | дараалал | |
| job / task | даалгавар | |
| cron | cron | cron job: cron даалгавар |
| script | скрипт | |
| worker | ажиллагч процесс | |
| kernel, core | цөм | |
| server | сервер | |
| database | өгөгдлийн сан | |
| folder | хавтас | |
| file | файл | |
| image | зураг | |
| preview | урьдчилан харах | |
| upload | байршуулах | |
| download | татаж авах | |
| export / import | экспортлох / импортлох | |
| remove | хасах | take out of a list or a place |
| delete | устгах | destroy |
| edit | засах | |
| editor | засварлагч | |
| save / store | хадгалах | |
| Store (shop tab) | Дэлгүүр | the word the shop menu uses for "Shop" |
| cancel | цуцлах | |
| OK | OK | |
| apply | хэрэгжүүлэх | |
| back | буцах | |
| next / previous | дараах / өмнөх | |
| enable / disable | идэвхжүүлэх / идэвхгүй болгох | a setting or feature |
| enabled / disabled | идэвхтэй / идэвхгүй | |
| active / inactive | идэвхтэй / идэвхгүй | |
| run | ажиллуулах | a script or job; start a service: эхлүүлэх |
| start / stop | эхлүүлэх / зогсоох | |
| session | сесс | |
| hidden / visible | нуугдсан / харагдах | hide / reveal: нуух / харуулах |
| archive | архив | |
| order (sort) | эрэмбэ | sort order: эрэмбэлэх дараалал |
| order (shop) | захиалга | |
| shop | дэлгүүр | |
| basket | сагс | kept apart from хогийн сав |
| product | бүтээгдэхүүн | |
| customer | худалдан авагч | |
| discount | хөнгөлөлт | |
| VAT | НӨАТ | VAT type: НӨАТ-ын төрөл |
| price | үнэ | |
| currency | валют | |
| tag | шошго | keyword tag; an HTML tag: HTML таг |
| translation | орчуулга | |
| language | хэл | main language: үндсэн хэл |
| locale | локал | |
| state | төлөв | the object state; state group: төлөвийн бүлэг |
| status | статус | |
| view (button) | харах | a module view: харагдац |
| format | формат | |
| sort | эрэмбэлэх | |
| filter | шүүлтүүр | |
| select | сонгох | selected: сонгосон |
| browse | үзэх | |
| copy | хуулах | |
| move | зөөх | |
| add | нэмэх | |
| create | үүсгэх | |
| new | шинэ | |
| restore | сэргээх | |
| install / uninstall | суулгах / устгах | |
| sub items | дэд зүйлс | |
| subtree | дэд мод | |
| related objects | холбоотой объектууд | relation: холбоо |
| URL alias | URL өөр нэр | |
| redirect | дахин чиглүүлэх | |
| dashboard | хяналтын самбар | |
| content structure | агуулгын бүтэц | |
| media library | медиа сан | |
| identifier / ID | танигч / ID | |
| priority | тэргүүлэх зэрэг | |
| owner | эзэмшигч | |
| modified / created | өөрчилсөн / үүсгэсэн | |
| date / time | огноо / цаг | |
| error / warning | алдаа / анхааруулга | |
| help | тусламж | |
| notification | мэдэгдэл | |
| wizard | шидтэн | |
| feed (RSS) | суваг | RSS feed: RSS суваг |
| tab | таб | |
| audit, audit trail | аудит, аудитын бүртгэл | |
| repository | агуулах | |
| checksum | хяналтын нийлбэр | |
| account | бүртгэл | user account: хэрэглэгчийн бүртгэл |
| default | өгөгдмөл | default value: өгөгдмөл утга |
| toolbar | хэрэгслийн самбар | |
| container | сав | |
| AI | хиймэл оюун ухаан | |
| pattern (name pattern) | хэв | kept apart from загвар (template) |
| wildcard | орлуулах тэмдэгт | |
| remote ID | алсын ID | |
| placeholder | орлуулагч | |
| collection | цуглуулга | information collector: мэдээлэл цуглуулагч |
| approve, approval | батлах, батламж | |
| software | програм хангамж | |
| creator / modifier | үүсгэгч / өөрчлөгч | |
| request (HTTP) | хүсэлт | |
| query | асуулга | search query: хайлтын асуулга |
| handler / provider / listener | боловсруулагч / нийлүүлэгч / сонсогч | |
| engine | хөдөлгүүр | |
| release / upgrade | хувилбар гаргалт / шинэчлэл | update: шинэчлэх |
| backup | нөөц хуулбар | |
| maintenance | засвар үйлчилгээ | |
| directory | директор | |
| header (HTTP) | толгой | |
| page layout (pagelayout template) | хуудасны загвар | kept apart from зохион байгуулалт (Exponential Layouts) |
| passkey | нэвтрэх түлхүүр | |
| two-step sign-in | хоёр шаттай нэвтрэлт | |
| device / browser | төхөөрөмж / браузер | |
| link | холбоос | |
| registration, register | бүртгэл, бүртгүүлэх | |
| payment gateway | төлбөрийн гарц | |
| form | маягт | |
| comment | сэтгэгдэл | |
| newsletter / subscription | мэдээллийн товхимол / захиалга | |
| field | талбар | |
| value | утга | |
| list | жагсаалт | |
| page | хуудас | |
| title / name | гарчиг / нэр | the column "Name": Нэр |
| description | тайлбар | |
| type | төрөл | |
| path | зам | |
| width / height | өргөн / өндөр | |
| size | хэмжээ | |
| none | байхгүй | |
| yes / no | тийм / үгүй | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Дэлгүүр, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is хадгалах.
- "Account" and "log" are both бүртгэл in Mongolian software; the context tells them apart, and the log file is
  лог файл.
