{*
    $attribute.content.year - year
    $attribute.content.month - month
    $attribute.content.day - day
    $attribute.content.zodiac - the name of the zodiac
    $attribute.content.zodiac_no - the number of the zodiac
    $attribute.content.is_valid - true, if the date is correct
    $attribute.content.days_on_earth - number of days from date to now
    $attribute.content.age - the age in years
*}
{if $attribute.content.is_valid}
    {makedate( $attribute.content.month, $attribute.content.day, 1990)|datetime( 'custom', '%d %F' )} {$attribute.content.year}
{/if}