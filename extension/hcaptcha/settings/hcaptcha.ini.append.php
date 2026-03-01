<?php /* #?ini charset="utf-8"?

[Keys]
# Visit https://www.google.com/recaptcha to signup and get your own API keys
#
# If you are using in a single site setup you can simply set PublicKey &
# PrivateKey to the key values. e.g.
#   PublicKey=9009890098kmml;kjfvdl98980989-g_kHoQlkK
#   PrivateKey=6Ldk4gAAAAAAAOebF5yjUCa5C4QKMD-g_kHoQlkK
#
# If you are running multiple sites and have multiple keys define the keys as
# arrays with the hostname a the array key. e.g.
#
# PublicKey[test.stuffandcontent.com]=9009890098kmml;kjfvdl98980989-g_kHoQlkK
# PrivateKey[test.stuffandcontent.com]=9009890098kmml;kjfvdl98980989-g_kHoQlkK
#
# PublicKey[www.stuffandcontent.com]=900989jjyhyys;kjfvdl98980989-g_kHoQlkK
# PrivateKey[www.stuffandcontent.com]=90098jjyhyys;kjfvdl98980989-g_kHoQlkK
#
#PublicKey[hostname]=Enter your Public Key here for hostname
#PublicKey[localhost]=Enter your Public Key here for localhost
PublicKey=Enter your Public Key here

#PrivateKey[hostname]=Enter your Private Key here for hostname
#PrivateKey[localhost]=Enter your Private Key here for localhost
PrivateKey=Enter your Private Key here

[Display]
# Possible themes 'red' | 'white' | 'blackglass' | 'clean' | 'custom'
# see https://developers.google.com/recaptcha/old/docs/customization
#
# If you choose a value that is not supported the captcha will default to the
# red theme
Theme=white

# The templates currently attempt to work out the language to present the
# capture in based on the language_code attribute. This is currently not
# perfect (Will work on this) and you may want to override the automated
# language chosing and set a default value.
#
# If OverrideLang is set (not empty) then it will be used as the display
# language of the reCAPTCHA widget
#  Current possible values are:
#   English     en 
#   Dutch       nl
#   French      fr
#   German      de
#   Portuguese  pt
#   Russian     ru
#   Spanish     es
#   Turkish     tr
OverrideLang=

[PublishSettings]
# Allows to use recaptcha only on newly created objects and to ignore it on objects that are re-edited.
# Usefull if you want to use recaptcha only for user/register and not on user/edit
# Another use would be to use recaptcha only when adding comments and not when editing them.
NewObjectsOnly=false

*/ ?>
