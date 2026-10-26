{*
    The kernel's HTML mail layout: the one template that holds the styles of an HTML mail. A content template
    (e.g. user/registrationfeedback.tpl) passes plain text and URLs; every value is washed here.

    Built for mail clients (Gmail, Outlook, Apple Mail, phones): tables with role="presentation", inline styles,
    bgcolor fallbacks, at most 600px wide, no external CSS, no flexbox or grid. A design overrides this file
    (design:mail/layout.tpl) with the same inputs to give mails its own look and logo.

    Inline styles are required here: mail programs drop style sheets and <link>s, so the administration's rule of
    "no inline CSS, tokens only" (doc/eng-us/bc/6.0/admin4-styling.md) is for the pages of the administration and does not
    apply to mail bodies. The one <style> block below only adds the dark colors for the mail programs that honor
    prefers-color-scheme (Apple Mail, iOS, Outlook for Mac); every other program shows the light colors, which are
    set inline on every element, so a program that inverts colors by itself (Gmail app, Outlook.com) still gets
    readable text on a solid background.

    Inputs (all optional):
    - mail_title       heading of the mail
    - mail_preheader   the preview text mail programs show next to the subject (hidden in the mail); default: intro
    - mail_intro       first paragraph (text)
    - mail_code        a code to type in (a sign-in code): shown large, in a monospace font, easy to select and copy
    - mail_code_label  a short caption above the code
    - mail_paragraphs  more paragraphs after the rows; each is a text, or an array of parts hash( 'text', ... ) and
                       hash( 'url', ..., 'text', ... ) (a link; 'text' defaults to the URL)
                       and hash( 'break', true() ) (a line break)
    - mail_rows        array of hash( 'label', ..., 'value', ... ), shown as a table; a row with 'washed', true()
                       holds a value the content template has already escaped (the output of attribute_result_gui)
    - mail_notice      a highlighted sentence (text), e.g. what the reader is expected to do
    - mail_buttons     array of hash( 'label', ..., 'url', ..., 'kind', 'primary'|'secondary'|'danger' )
    - mail_links       array of hash( 'label', ..., 'url', ... ), shown as plain text links below the buttons
    - mail_links_note  small print below the buttons (text), e.g. the address to copy when a button does not work
    - mail_outro       array of texts after the buttons, in small print: the security notes
    - mail_footer      footer text; default: the site name
    - mail_site_name   the site's name in the header and footer; default: site.ini [SiteSettings] SiteName
    - mail_logo_url    absolute https URL of a PNG/JPG logo; none: the site name is shown as text
    - mail_logo_alt    alternative text of the logo; default: the site name
    - mail_logo_link   where the logo links to; default: the site
    - mail_logo_width, mail_logo_height  display size of the logo in pixels
    The order is: title, intro, code, rows, paragraphs, notice, buttons, links, links note, outro, footer.
    The comment exp-mail-footer marks where the mail gate (expMailGate) puts the footer of an optional mail.
    The plain-text version of the same inputs is design:mail/layout_text.tpl.
*}
{* palette (neutral); the dark values are in the <style> block *}
{def $mx_page = '#f3f4f6'
     $mx_card = '#ffffff'
     $mx_text = '#1f2328'
     $mx_muted = '#59636e'
     $mx_border = '#d8dee4'
     $mx_head_bg = '#24292f'
     $mx_head_text = '#ffffff'
     $mx_accent = '#24292f'
     $mx_notice_bg = '#f6f8fa'
     $mx_primary_bg = '#24292f'
     $mx_primary_text = '#ffffff'
     $mx_secondary_bg = '#ffffff'
     $mx_secondary_text = '#24292f'
     $mx_danger_bg = '#b42318'
     $mx_danger_text = '#ffffff'
     $mx_font = 'Arial, Helvetica, sans-serif'
     $mx_mono = 'Consolas, Menlo, Monaco, Courier, monospace'
     $mx_site_name = first_set( $mail_site_name, '' )
     $mx_site_link = concat( 'https://', ezini( 'SiteSettings', 'SiteURL' ), '/' )
     $mx_title = first_set( $mail_title, '' )
     $mx_preheader = first_set( $mail_preheader, '' )
     $mx_intro = first_set( $mail_intro, '' )
     $mx_code = first_set( $mail_code, '' )
     $mx_code_label = first_set( $mail_code_label, '' )
     $mx_paragraphs = first_set( $mail_paragraphs, array() )
     $mx_rows = first_set( $mail_rows, array() )
     $mx_notice = first_set( $mail_notice, '' )
     $mx_buttons = first_set( $mail_buttons, array() )
     $mx_links = first_set( $mail_links, array() )
     $mx_links_note = first_set( $mail_links_note, '' )
     $mx_outro = first_set( $mail_outro, array() )
     $mx_footer = first_set( $mail_footer, '' )
     $mx_logo_url = first_set( $mail_logo_url, '' )
     $mx_logo_alt = first_set( $mail_logo_alt, '' )
     $mx_logo_link = first_set( $mail_logo_link, '' )
     $mx_logo_width = first_set( $mail_logo_width, 0 )
     $mx_logo_height = first_set( $mail_logo_height, 0 )
     $mx_bg = ''
     $mx_fg = ''
     $mx_line = ''
     $mx_class = ''}
{if $mx_site_name|eq( '' )}{set $mx_site_name = ezini( 'SiteSettings', 'SiteName' )}{/if}
{if $mx_logo_alt|eq( '' )}{set $mx_logo_alt = $mx_site_name}{/if}
{if $mx_logo_link|eq( '' )}{set $mx_logo_link = $mx_site_link}{/if}
{if $mx_preheader|eq( '' )}{set $mx_preheader = $mx_intro}{/if}
{if $mx_paragraphs|is_array|not}{set $mx_paragraphs = array( $mx_paragraphs )}{/if}
{if $mx_outro|is_array|not}{set $mx_outro = array( $mx_outro )}{/if}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{ezini( 'RegionalSettings', 'Locale' )|locale().http_locale_code|wash}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="x-apple-disable-message-reformatting" />
<meta name="format-detection" content="telephone=no, date=no, address=no, email=no" />
<meta name="color-scheme" content="light dark" />
<meta name="supported-color-schemes" content="light dark" />
<title>{$mx_title|wash}</title>
<style type="text/css">
:root {ldelim} color-scheme: light dark; supported-color-schemes: light dark; {rdelim}
@media (prefers-color-scheme: dark) {ldelim}
  .mx-page {ldelim} background-color: #0d1117 !important; {rdelim}
  .mx-card {ldelim} background-color: #161b22 !important; border-color: #30363d !important; {rdelim}
  .mx-text, .mx-text p, .mx-text h1, .mx-text td {ldelim} color: #e6edf3 !important; {rdelim}
  p.mx-muted, td.mx-muted, .mx-muted p, .mx-muted a, p.mx-muted a {ldelim} color: #9198a1 !important; {rdelim}
  .mx-box {ldelim} background-color: #21262d !important; border-color: #30363d !important; color: #e6edf3 !important; {rdelim}
  .mx-row td, table.mx-rows {ldelim} border-color: #30363d !important; {rdelim}
  td.mx-notice {ldelim} border-left-color: #9198a1 !important; {rdelim}
  .mx-link {ldelim} color: #e6edf3 !important; {rdelim}
  .mx-btn-secondary {ldelim} background-color: #161b22 !important; border-color: #e6edf3 !important; {rdelim}
  .mx-btn-secondary a {ldelim} color: #e6edf3 !important; {rdelim}
  .mx-btn-primary {ldelim} background-color: #e6edf3 !important; border-color: #e6edf3 !important; {rdelim}
  .mx-btn-primary a {ldelim} color: #0d1117 !important; {rdelim}
{rdelim}
</style>
</head>
<body class="mx-page" style="margin:0; padding:0; background-color:{$mx_page}; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;" bgcolor="{$mx_page}">
{if $mx_preheader|ne( '' )}
<div style="display:none; font-size:1px; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden; mso-hide:all;">{$mx_preheader|wash}</div>
{/if}
<table class="mx-page" role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{$mx_page}" style="width:100%; background-color:{$mx_page}; border-collapse:collapse;">
<tr>
<td align="center" style="padding:24px 12px;">
<!--[if mso]><table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
<table class="mx-card mx-text" role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{$mx_card}" style="width:100%; max-width:600px; background-color:{$mx_card}; border:1px solid {$mx_border}; border-collapse:separate; border-radius:8px; font-family:{$mx_font}; font-size:16px; line-height:1.5; color:{$mx_text};">
<tr>
<td bgcolor="{$mx_head_bg}" style="padding:20px 28px; background-color:{$mx_head_bg}; border-radius:8px 8px 0 0; color:{$mx_head_text}; font-family:{$mx_font};">
{if $mx_logo_url|ne( '' )}
<a href="{$mx_logo_link|wash}" style="text-decoration:none;"><img src="{$mx_logo_url|wash}" alt="{$mx_logo_alt|wash}"{if $mx_logo_width|gt( 0 )} width="{$mx_logo_width|wash}"{/if}{if $mx_logo_height|gt( 0 )} height="{$mx_logo_height|wash}"{/if} border="0" style="display:block; border:0; outline:none; text-decoration:none; height:auto; max-width:100%; color:{$mx_head_text};" /></a>
{else}
<a href="{$mx_logo_link|wash}" style="color:{$mx_head_text}; text-decoration:none; font-size:18px; font-weight:bold;">{$mx_logo_alt|wash}</a>
{/if}
</td>
</tr>
<tr>
<td class="mx-text" style="padding:28px 28px 8px 28px; font-family:{$mx_font}; color:{$mx_text};">
{if $mx_title|ne( '' )}
<h1 style="margin:0 0 16px 0; font-size:22px; line-height:1.25; font-weight:bold; color:{$mx_text};">{$mx_title|wash}</h1>
{/if}
{if $mx_intro|ne( '' )}
<p style="margin:0 0 20px 0; font-size:16px; line-height:1.5; color:{$mx_text};">{$mx_intro|wash}</p>
{/if}
{if $mx_code|ne( '' )}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin:0 0 20px 0; border-collapse:collapse;">
{if $mx_code_label|ne( '' )}
<tr>
<td class="mx-muted" style="padding:0 0 6px 0; font-size:13px; line-height:1.4; color:{$mx_muted}; font-family:{$mx_font};">{$mx_code_label|wash}</td>
</tr>
{/if}
<tr>
<td class="mx-box" align="center" bgcolor="{$mx_notice_bg}" style="padding:16px 12px; background-color:{$mx_notice_bg}; border:1px solid {$mx_border}; border-radius:8px; font-family:{$mx_mono}; font-size:32px; line-height:1.2; font-weight:bold; letter-spacing:6px; color:{$mx_text}; -webkit-user-select:all; user-select:all;">{$mx_code|wash}</td>
</tr>
</table>
{/if}
{if $mx_rows|count}
<table class="mx-rows" role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin:0 0 20px 0; border-collapse:collapse; border-top:1px solid {$mx_border};">
{foreach $mx_rows as $mx_row}
<tr class="mx-row">
<td class="mx-muted" valign="top" width="36%" style="width:36%; padding:10px 12px 10px 0; border-bottom:1px solid {$mx_border}; font-size:14px; line-height:1.4; color:{$mx_muted}; font-family:{$mx_font};">{$mx_row.label|wash}</td>
<td valign="top" style="padding:10px 0; border-bottom:1px solid {$mx_border}; font-size:15px; line-height:1.4; color:{$mx_text}; font-family:{$mx_font}; word-break:break-word;">{if first_set( $mx_row.washed, false() )}{$mx_row.value}{else}{$mx_row.value|wash|nl2br}{/if}</td>
</tr>
{/foreach}
</table>
{/if}
{foreach $mx_paragraphs as $mx_paragraph}
{if $mx_paragraph|is_array}
<p style="margin:0 0 16px 0; font-size:16px; line-height:1.5; color:{$mx_text}; overflow-wrap:anywhere; word-break:break-word;">{foreach $mx_paragraph as $mx_part}{if is_set( $mx_part.break )}<br />{elseif is_set( $mx_part.url )}<a class="mx-link" href="{$mx_part.url|wash}" style="color:{$mx_text}; text-decoration:underline;">{first_set( $mx_part.text, $mx_part.url )|wash}</a>{else}{first_set( $mx_part.text, '' )|wash}{/if}{/foreach}</p>
{elseif $mx_paragraph|ne( '' )}
<p style="margin:0 0 16px 0; font-size:16px; line-height:1.5; color:{$mx_text};">{$mx_paragraph|wash|nl2br}</p>
{/if}
{/foreach}
{if $mx_notice|ne( '' )}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin:0 0 20px 0; border-collapse:collapse;">
<tr>
<td class="mx-box mx-notice" bgcolor="{$mx_notice_bg}" style="padding:12px 16px; background-color:{$mx_notice_bg}; border-left:4px solid {$mx_accent}; font-size:15px; line-height:1.45; color:{$mx_text}; font-family:{$mx_font};">{$mx_notice|wash|nl2br}</td>
</tr>
</table>
{/if}
{if $mx_buttons|count}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin:0 0 12px 0; border-collapse:collapse;">
<tr>
<td style="padding:0;">
{foreach $mx_buttons as $mx_button}
{switch match=first_set( $mx_button.kind, 'primary' )}
{case match='danger'}{set $mx_bg = $mx_danger_bg $mx_fg = $mx_danger_text $mx_line = $mx_danger_bg $mx_class = 'mx-btn-danger'}{/case}
{case match='secondary'}{set $mx_bg = $mx_secondary_bg $mx_fg = $mx_secondary_text $mx_line = $mx_secondary_text $mx_class = 'mx-btn-secondary'}{/case}
{case}{set $mx_bg = $mx_primary_bg $mx_fg = $mx_primary_text $mx_line = $mx_primary_bg $mx_class = 'mx-btn-primary'}{/case}
{/switch}
<table role="presentation" align="left" cellpadding="0" cellspacing="0" border="0" style="margin:0 10px 10px 0; border-collapse:separate;">
<tr>
<td class="{$mx_class}" align="center" bgcolor="{$mx_bg}" style="background-color:{$mx_bg}; border:1px solid {$mx_line}; border-radius:6px; padding:11px 20px; font-family:{$mx_font};"><a href="{$mx_button.url|wash}" style="display:inline-block; color:{$mx_fg}; font-size:15px; line-height:1.2; font-weight:bold; text-decoration:none; font-family:{$mx_font};">{$mx_button.label|wash}</a></td>
</tr>
</table>
{/foreach}
</td>
</tr>
</table>
{/if}
{if $mx_links|count}
<p class="mx-muted" style="margin:0 0 16px 0; font-size:14px; line-height:1.5; color:{$mx_muted}; overflow-wrap:anywhere; word-break:break-word;">
{foreach $mx_links as $mx_link sequence array( '', ' &middot; ' ) as $mx_sep}{$mx_sep}<a class="mx-link" href="{$mx_link.url|wash}" style="color:{$mx_text}; text-decoration:underline;">{$mx_link.label|wash}</a>{/foreach}
</p>
{/if}
{if $mx_links_note|ne( '' )}
<p class="mx-muted" style="margin:0 0 16px 0; font-size:13px; line-height:1.5; color:{$mx_muted}; overflow-wrap:anywhere; word-break:break-word;">{$mx_links_note|wash}</p>
{/if}
{foreach $mx_outro as $mx_line_text}
{if $mx_line_text|is_array}
<p class="mx-muted" style="margin:0 0 12px 0; font-size:13px; line-height:1.5; color:{$mx_muted}; overflow-wrap:anywhere; word-break:break-word;">{foreach $mx_line_text as $mx_part}{if is_set( $mx_part.break )}<br />{elseif is_set( $mx_part.url )}<a href="{$mx_part.url|wash}" style="color:{$mx_muted}; text-decoration:underline;">{first_set( $mx_part.text, $mx_part.url )|wash}</a>{else}{first_set( $mx_part.text, '' )|wash}{/if}{/foreach}</p>
{elseif $mx_line_text|ne( '' )}
<p class="mx-muted" style="margin:0 0 12px 0; font-size:13px; line-height:1.5; color:{$mx_muted};">{$mx_line_text|wash}</p>
{/if}
{/foreach}
<!-- exp-mail-footer -->
</td>
</tr>
<tr>
<td class="mx-box mx-muted" bgcolor="{$mx_notice_bg}" style="padding:16px 28px; background-color:{$mx_notice_bg}; border-top:1px solid {$mx_border}; border-radius:0 0 8px 8px; font-size:12px; line-height:1.5; color:{$mx_muted}; font-family:{$mx_font};">
{if $mx_footer|ne( '' )}{$mx_footer|wash}{else}<a href="{$mx_site_link|wash}" style="color:{$mx_muted}; text-decoration:underline;">{$mx_site_name|wash}</a>{/if}
</td>
</tr>
</table>
<!--[if mso]></td></tr></table><![endif]-->
</td>
</tr>
</table>
</body>
</html>
{undef $mx_page $mx_card $mx_text $mx_muted $mx_border $mx_head_bg $mx_head_text $mx_accent $mx_notice_bg
       $mx_primary_bg $mx_primary_text $mx_secondary_bg $mx_secondary_text $mx_danger_bg $mx_danger_text $mx_font
       $mx_mono $mx_site_name $mx_site_link $mx_title $mx_preheader $mx_intro $mx_code $mx_code_label $mx_paragraphs
       $mx_rows $mx_notice $mx_buttons $mx_links $mx_links_note $mx_outro $mx_footer $mx_logo_url $mx_logo_alt
       $mx_logo_link $mx_logo_width $mx_logo_height $mx_bg $mx_fg $mx_line $mx_class}
