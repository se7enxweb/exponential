{*
    The kernel's HTML mail layout: the one template that holds the styles of an HTML mail. A content template
    (e.g. user/registrationfeedback.tpl) passes plain text and URLs; every value is washed here.

    Built for mail clients (Gmail, Outlook, Apple Mail, phones): tables with role="presentation", inline styles,
    bgcolor fallbacks, at most 600px wide, no external CSS, no flexbox or grid. A design overrides this file
    (design:mail/layout.tpl) with the same inputs to give mails its own look and logo.

    Inputs (all optional):
    - mail_title       heading of the mail
    - mail_intro       first paragraph (text)
    - mail_rows        array of hash( 'label', ..., 'value', ... ), shown as a table
    - mail_notice      a highlighted sentence (text), e.g. what the reader is expected to do
    - mail_buttons     array of hash( 'label', ..., 'url', ..., 'kind', 'primary'|'secondary'|'danger' )
    - mail_links       array of hash( 'label', ..., 'url', ... ), shown as plain text links below the buttons
    - mail_links_note  small print below the buttons (text)
    - mail_footer      footer text; default: the site name
    - mail_logo_url    absolute https URL of a PNG/JPG logo; none: the site name is shown as text
    - mail_logo_alt    alternative text of the logo; default: the site name
    - mail_logo_link   where the logo links to; default: the site
    - mail_logo_width, mail_logo_height  display size of the logo in pixels
    The plain-text version of the same inputs is design:mail/layout_text.tpl.
*}
{* palette (neutral) *}
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
     $mx_site_name = ezini( 'SiteSettings', 'SiteName' )
     $mx_site_link = concat( 'https://', ezini( 'SiteSettings', 'SiteURL' ), '/' )
     $mx_title = first_set( $mail_title, '' )
     $mx_intro = first_set( $mail_intro, '' )
     $mx_rows = first_set( $mail_rows, array() )
     $mx_notice = first_set( $mail_notice, '' )
     $mx_buttons = first_set( $mail_buttons, array() )
     $mx_links = first_set( $mail_links, array() )
     $mx_links_note = first_set( $mail_links_note, '' )
     $mx_footer = first_set( $mail_footer, '' )
     $mx_logo_url = first_set( $mail_logo_url, '' )
     $mx_logo_alt = first_set( $mail_logo_alt, '' )
     $mx_logo_link = first_set( $mail_logo_link, '' )
     $mx_logo_width = first_set( $mail_logo_width, 0 )
     $mx_logo_height = first_set( $mail_logo_height, 0 )
     $mx_bg = ''
     $mx_fg = ''
     $mx_line = ''}
{if $mx_logo_alt|eq( '' )}{set $mx_logo_alt = $mx_site_name}{/if}
{if $mx_logo_link|eq( '' )}{set $mx_logo_link = $mx_site_link}{/if}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{ezini( 'RegionalSettings', 'Locale' )|locale().http_locale_code|wash}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="x-apple-disable-message-reformatting" />
<meta name="format-detection" content="telephone=no, date=no, address=no, email=no" />
<title>{$mx_title|wash}</title>
</head>
<body style="margin:0; padding:0; background-color:{$mx_page}; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;" bgcolor="{$mx_page}">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{$mx_page}" style="width:100%; background-color:{$mx_page}; border-collapse:collapse;">
<tr>
<td align="center" style="padding:24px 12px;">
<!--[if mso]><table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{$mx_card}" style="width:100%; max-width:600px; background-color:{$mx_card}; border:1px solid {$mx_border}; border-collapse:separate; border-radius:8px; font-family:{$mx_font}; font-size:16px; line-height:1.5; color:{$mx_text};">
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
<td style="padding:28px 28px 8px 28px; font-family:{$mx_font}; color:{$mx_text};">
{if $mx_title|ne( '' )}
<h1 style="margin:0 0 16px 0; font-size:22px; line-height:1.25; font-weight:bold; color:{$mx_text};">{$mx_title|wash}</h1>
{/if}
{if $mx_intro|ne( '' )}
<p style="margin:0 0 20px 0; font-size:16px; line-height:1.5; color:{$mx_text};">{$mx_intro|wash}</p>
{/if}
{if $mx_rows|count}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin:0 0 20px 0; border-collapse:collapse; border-top:1px solid {$mx_border};">
{foreach $mx_rows as $mx_row}
<tr>
<td valign="top" width="36%" style="width:36%; padding:10px 12px 10px 0; border-bottom:1px solid {$mx_border}; font-size:14px; line-height:1.4; color:{$mx_muted}; font-family:{$mx_font};">{$mx_row.label|wash}</td>
<td valign="top" style="padding:10px 0; border-bottom:1px solid {$mx_border}; font-size:15px; line-height:1.4; color:{$mx_text}; font-family:{$mx_font}; word-break:break-word;">{$mx_row.value|wash}</td>
</tr>
{/foreach}
</table>
{/if}
{if $mx_notice|ne( '' )}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin:0 0 20px 0; border-collapse:collapse;">
<tr>
<td bgcolor="{$mx_notice_bg}" style="padding:12px 16px; background-color:{$mx_notice_bg}; border-left:4px solid {$mx_accent}; font-size:15px; line-height:1.45; color:{$mx_text}; font-family:{$mx_font};">{$mx_notice|wash}</td>
</tr>
</table>
{/if}
{if $mx_buttons|count}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin:0 0 12px 0; border-collapse:collapse;">
<tr>
<td style="padding:0;">
{foreach $mx_buttons as $mx_button}
{switch match=first_set( $mx_button.kind, 'primary' )}
{case match='danger'}{set $mx_bg = $mx_danger_bg $mx_fg = $mx_danger_text $mx_line = $mx_danger_bg}{/case}
{case match='secondary'}{set $mx_bg = $mx_secondary_bg $mx_fg = $mx_secondary_text $mx_line = $mx_secondary_text}{/case}
{case}{set $mx_bg = $mx_primary_bg $mx_fg = $mx_primary_text $mx_line = $mx_primary_bg}{/case}
{/switch}
<table role="presentation" align="left" cellpadding="0" cellspacing="0" border="0" style="margin:0 10px 10px 0; border-collapse:separate;">
<tr>
<td align="center" bgcolor="{$mx_bg}" style="background-color:{$mx_bg}; border:1px solid {$mx_line}; border-radius:6px; padding:11px 20px; font-family:{$mx_font};"><a href="{$mx_button.url|wash}" style="display:inline-block; color:{$mx_fg}; font-size:15px; line-height:1.2; font-weight:bold; text-decoration:none; font-family:{$mx_font};">{$mx_button.label|wash}</a></td>
</tr>
</table>
{/foreach}
</td>
</tr>
</table>
{/if}
{if $mx_links|count}
<p style="margin:0 0 16px 0; font-size:14px; line-height:1.5; color:{$mx_muted}; overflow-wrap:anywhere; word-break:break-word;">
{foreach $mx_links as $mx_link sequence array( '', ' &middot; ' ) as $mx_sep}{$mx_sep}<a href="{$mx_link.url|wash}" style="color:{$mx_text}; text-decoration:underline;">{$mx_link.label|wash}</a>{/foreach}
</p>
{/if}
{if $mx_links_note|ne( '' )}
<p style="margin:0 0 16px 0; font-size:13px; line-height:1.5; color:{$mx_muted}; overflow-wrap:anywhere; word-break:break-word;">{$mx_links_note|wash}</p>
{/if}
</td>
</tr>
<tr>
<td bgcolor="{$mx_notice_bg}" style="padding:16px 28px; background-color:{$mx_notice_bg}; border-top:1px solid {$mx_border}; border-radius:0 0 8px 8px; font-size:12px; line-height:1.5; color:{$mx_muted}; font-family:{$mx_font};">
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
       $mx_site_name $mx_site_link $mx_title $mx_intro $mx_rows $mx_notice $mx_buttons $mx_links $mx_links_note
       $mx_footer $mx_logo_url $mx_logo_alt $mx_logo_link $mx_logo_width $mx_logo_height $mx_bg $mx_fg $mx_line}
