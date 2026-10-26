# Vietnamese (vie-VN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Vietnamese (tiếng Việt) as written in Vietnam, in the neutral, concise register of current Vietnamese
software (operating systems, office suites, content management systems). Sino-Vietnamese technical vocabulary is
used where it is established (cơ sở dữ liệu, phiên bản, thuộc tính); a loan word is used where the Vietnamese IT
community uses it (email, cookie, plugin is not used: tiện ích mở rộng). No other locale falls back to `vie-VN`.

- Short, plain sentences. No slang, no regional (Northern or Southern) colloquialisms.
- Classifiers and plural markers only where the sentence needs them: "Xóa các mục đã chọn", but "Người dùng" as a
  heading, not "Những người dùng".

## 2. Form of address

The interface speaks neutrally and addresses the user as **bạn** where a pronoun is needed, as current Vietnamese
software does. No quý vị, no anh/chị, no em.

- **Buttons, menu items and links**: the bare verb or verb phrase, without "hãy" or a pronoun: Lưu, Hủy, Xóa,
  Chỉnh sửa, Gửi để xuất bản, Xóa mục đã chọn, Chuyển vào thùng rác.
- **Titles, column headings and tabs**: nouns or noun phrases: Cài đặt, Bản dịch, Vị trí chính.
- **Instructions**: a polite imperative, with "hãy" or "vui lòng" where English has "please", otherwise the bare
  verb: "Chọn mục rồi nhấn \"OK\".", "Vui lòng đợi cho đến khi mức ưu tiên được lưu.". A reference ("see ...") is
  "xem ...".
- **Confirmations and questions**: "Bạn có chắc chắn muốn ... không?" or "Bạn có muốn ... không?":
  "Bạn có chắc chắn muốn xóa lớp này không?".
- **What the user can or may do**: "Bạn có thể ...", "Bạn không thể ...", or impersonal: "Không thể thay đổi ngôn
  ngữ chính ...". "You do not have permission" is "Bạn không có quyền ...".
- **Messages about a result**: passive with "đã ... thành công" or "đã được ...": "Đã lưu bản nháp.",
  "Tệp đã được tải lên thành công.".

## 3. Script and typography

- **Latin script with the full Vietnamese diacritics** (quốc ngữ), Unicode NFC (precomposed letters such as ế, ữ),
  never decomposed combining marks and never text without tones.
- **Tone mark placement**: the modern placement used by Microsoft and Google: hóa, xóa, hủy, thủy, khỏe (not hoá,
  xoá, huỷ). One spelling everywhere.
- **i / y**: the established spelling of the dictionaries: kỹ thuật, quy tắc, lý do, hệ thống, mỹ thuật.
- **Capitals**: only the first word of a button, title or sentence and proper names: "Chỉnh sửa bản nháp", not
  "Chỉnh Sửa Bản Nháp".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Vietnamese puts the noun after the number: "%count đối tượng".
  The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source; no space before colon, question or exclamation mark.
- **Numbers**: digits 0-9. Vietnamese has no plural inflection: "%count mục" for one and many.

## 4. Glossary

Binding for the kernel and every extension.

| English | Vietnamese | Note |
|---|---|---|
| content | nội dung | |
| content object | đối tượng nội dung | |
| object | đối tượng | |
| node | nút | main node: nút chính; parent node: nút cha |
| location | vị trí | main location: vị trí chính |
| item (list entry) | mục | |
| class | lớp | the content class; class group: nhóm lớp; a PHP class: lớp PHP |
| content class | lớp nội dung | |
| attribute | thuộc tính | |
| datatype | kiểu dữ liệu | |
| version | phiên bản | version history: lịch sử phiên bản |
| draft | bản nháp | store draft: lưu bản nháp |
| publish | xuất bản | published: đã xuất bản; publishing: việc xuất bản |
| send for publishing | gửi để xuất bản | |
| discard | hủy bỏ | discard draft: hủy bỏ bản nháp |
| archived | đã lưu trữ | |
| pending | đang chờ | |
| rejected | bị từ chối | |
| siteaccess | điểm truy cập trang | siteaccess as a setting name stays in code |
| site, website | trang web | |
| section | khu vực | the Exponential section; an INI section: mục |
| role | vai trò | |
| policy | chính sách | one rule of a role |
| limitation | giới hạn | |
| permission | quyền | access denied: truy cập bị từ chối |
| module | mô-đun | |
| function | chức năng | a module function; a PHP function: hàm |
| user | người dùng | |
| user group | nhóm người dùng | |
| group | nhóm | |
| anonymous | ẩn danh | |
| administrator | quản trị viên | |
| administration (interface) | giao diện quản trị | short: quản trị |
| username | tên người dùng | login (the name): tên đăng nhập |
| password | mật khẩu | |
| log in / sign in | đăng nhập | |
| log out / sign out | đăng xuất | |
| e-mail | email | e-mail address: địa chỉ email |
| cache | bộ nhớ đệm | clear cache: xóa bộ nhớ đệm |
| template | mẫu | template file: tệp mẫu |
| override | ghi đè | override template: mẫu ghi đè |
| fallback | dự phòng | fallback language: ngôn ngữ dự phòng |
| design | thiết kế | |
| workflow | quy trình làm việc | short: quy trình |
| trigger | trình kích hoạt | |
| event | sự kiện | |
| trash | thùng rác | empty trash: dọn sạch thùng rác; move to trash: chuyển vào thùng rác |
| bookmark | dấu trang | |
| settings | cài đặt | configuration: cấu hình |
| setup | thiết lập | the Setup tab |
| layout | bố cục | Exponential Layouts |
| block | khối | |
| zone | vùng | |
| extension | tiện ích mở rộng | |
| package | gói | |
| search | tìm kiếm | search index: chỉ mục tìm kiếm; indexing: lập chỉ mục |
| log | nhật ký | a log file; never "log in" |
| queue | hàng đợi | job queue: hàng đợi tác vụ |
| job / task | tác vụ | |
| cron | cron | a cron job: tác vụ cron |
| script | tập lệnh | |
| worker | tiến trình xử lý | |
| kernel, core | lõi | |
| server | máy chủ | |
| database | cơ sở dữ liệu | |
| folder | thư mục | |
| file | tệp | |
| image | hình ảnh | thumbnail: ảnh thu nhỏ |
| preview | xem trước | |
| upload | tải lên | |
| download | tải xuống | |
| export / import | xuất / nhập | |
| remove | gỡ bỏ | take out of a list or place |
| delete | xóa | destroy |
| edit | chỉnh sửa | |
| editor | trình soạn thảo | a person: biên tập viên |
| save / store | lưu | the verb "Store" of the forms |
| Store (shop tab) | cửa hàng | the shop, as "Shop" |
| cancel | hủy | |
| OK | OK | kept, as in Vietnamese software (allowlisted) |
| apply | áp dụng | |
| back | quay lại | |
| next / previous | tiếp theo / trước | |
| enable / disable | bật / tắt | also kích hoạt / vô hiệu hóa for a feature or account |
| enabled / disabled | đã bật / đã tắt | |
| active / inactive | hoạt động / không hoạt động | |
| run | chạy | a script, a job |
| start / stop | bắt đầu / dừng | |
| session | phiên | |
| hidden / visible | ẩn / hiển thị | hide / reveal: ẩn / hiện |
| archive | kho lưu trữ | a file archive: tệp nén |
| order (sort) | thứ tự | sort order: thứ tự sắp xếp |
| order (shop) | đơn hàng | |
| shop | cửa hàng | |
| basket | giỏ hàng | |
| product | sản phẩm | |
| customer | khách hàng | |
| discount | giảm giá | |
| VAT | thuế GTGT | value added tax: thuế giá trị gia tăng |
| price | giá | |
| currency | tiền tệ | |
| tag | thẻ | a keyword tag; an HTML tag: thẻ HTML |
| translation | bản dịch | |
| language | ngôn ngữ | main language: ngôn ngữ chính; interface language: ngôn ngữ giao diện |
| locale | bản địa | locale settings: thiết lập bản địa |
| state | trạng thái | the object state |
| status | tình trạng | kept apart from trạng thái where both appear |
| view (button) | xem | a module view (noun): chế độ xem |
| format | định dạng | |
| sort | sắp xếp | |
| filter | bộ lọc | the act: lọc |
| select | chọn | select all: chọn tất cả; selected: đã chọn |
| browse | duyệt | |
| copy | sao chép | |
| move | di chuyển | |
| add | thêm | |
| create | tạo | |
| new | mới | |
| restore | khôi phục | |
| install / uninstall | cài đặt / gỡ cài đặt | |
| sub items | mục con | |
| subtree | cây con | |
| related objects | đối tượng liên quan | relation: quan hệ |
| URL alias | bí danh URL | |
| redirect | chuyển hướng | |
| dashboard | bảng điều khiển | |
| content structure | cấu trúc nội dung | |
| media library | thư viện phương tiện | |
| identifier / ID | mã định danh / ID | |
| priority | mức ưu tiên | |
| owner | chủ sở hữu | |
| modified / created | đã sửa đổi / đã tạo | |
| date / time | ngày / giờ | |
| error / warning | lỗi / cảnh báo | |
| help | trợ giúp | |
| notification | thông báo | |
| wizard | trình hướng dẫn | setup wizard: trình hướng dẫn thiết lập |
| feed (RSS) | nguồn cấp | feed reader: trình đọc nguồn cấp |
| tab | thẻ | a tab of the interface; kept apart from tag by context |
| audit, audit trail | kiểm tra, nhật ký kiểm tra | |
| repository | kho | |
| checksum | mã kiểm tra | |
| account | tài khoản | |
| default | mặc định | default value: giá trị mặc định |
| toolbar | thanh công cụ | |
| container | vùng chứa | |
| AI | AI | trí tuệ nhân tạo where the source spells it out |
| pattern (name pattern) | mẫu tên | wildcard: ký tự đại diện |
| remote ID | ID từ xa | |
| placeholder | phần giữ chỗ | |
| collection | bộ sưu tập | information collector: bộ thu thập thông tin |
| approve, approval | phê duyệt | |
| software | phần mềm | |
| creator / modifier | người tạo / người sửa đổi | |
| request (HTTP) | yêu cầu | |
| query | truy vấn | |
| handler / provider / listener | trình xử lý / nhà cung cấp / trình lắng nghe | |
| engine | công cụ | |
| release / upgrade | bản phát hành / nâng cấp | |
| backup | bản sao lưu | |
| maintenance | bảo trì | |
| directory | thư mục | as folder |
| header (HTTP) | tiêu đề | |
| page layout (pagelayout template) | khung trang | kept apart from bố cục (Exponential Layouts) |
| passkey | khóa truy cập | |
| two-step sign-in | đăng nhập hai bước | |
| device / browser | thiết bị / trình duyệt | |
| link | liên kết | |
| registration, register | đăng ký | |
| payment gateway | cổng thanh toán | |
| none | không có | |
| yes / no | có / không | |
| name | tên | |
| title | tiêu đề | |
| description | mô tả | |
| value | giá trị | |
| type | loại | |
| list | danh sách | |
| page | trang | |
| path | đường dẫn | |
| required | bắt buộc | |
| optional | tùy chọn | |
| options | tùy chọn | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is cửa hàng, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is lưu.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID, OK ...) are listed with
  their reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of
  them.
