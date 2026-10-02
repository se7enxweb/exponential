<?php
/**
 * The alert rule class "schedule" (RuleClasses[schedule]=expAuditScheduleRule): fires for records matching Event
 * that happen outside business hours ([AuditAlertSettings] BusinessDays, BusinessHours, in the site's time zone)
 * with OutOfHours=enabled, or inside them with OutOfHours=disabled. One alert per group (GroupBy, default
 * actor.user_id) and Window (default 3600 s), so a session writing ten settings at night raises one alert.
 *
 *   [AlertRule_settings_out_of_hours]  Class=schedule  Event=system.setting.write  OutOfHours=enabled
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditScheduleRule extends expAuditAlertRuleBase
{
    protected function refiresWhenDoubled()
    {
        return false;
    }

    public function defaults()
    {
        return array( 'Threshold' => '1', 'Window' => '3600', 'GroupBy' => 'actor.user_id', 'OutOfHours' => 'enabled',
                      'BusinessDays' => null, 'BusinessHours' => null, 'TimeZone' => null );
    }

    public function evaluate( array $config, array $records, $state )
    {
        $config += $this->defaults();
        $days = self::range( $config['BusinessDays'] !== null ? $config['BusinessDays'] : expAuditConfig::value( 'AuditAlertSettings', 'BusinessDays', '1-5' ), 1, 7 );
        $hours = self::range( $config['BusinessHours'] !== null ? $config['BusinessHours'] : expAuditConfig::value( 'AuditAlertSettings', 'BusinessHours', '7-19' ), 0, 24, true );
        $outside = strtolower( trim( (string)$config['OutOfHours'] ) ) !== 'disabled';
        $tz = self::timeZone( $config['TimeZone'] );
        $alerts = array();
        foreach ( $records as $r )
        {
            $d = new DateTime( '@' . intdiv( self::timeMs( $r ), 1000 ) );
            $d->setTimezone( $tz );
            $inHours = in_array( (int)$d->format( 'N' ), $days, true ) && in_array( (int)$d->format( 'G' ), $hours, true );
            if ( $inHours === $outside )
                continue;
            $a = $this->count( $state, self::groupOf( $r, $config['GroupBy'] ), $r, 1, max( 1, (int)$config['Threshold'] ),
                               max( 0, (int)$config['Window'] ) * 1000 );
            if ( $a )
            {
                $a['message'] = $config['Event'] . ' at ' . $d->format( 'D H:i T' ) . ( $outside ? ', outside business hours' : ', inside business hours' );
                $alerts[] = $a;
            }
        }
        return $alerts;
    }

    /**
     * "1-5", "1,3,5-7", "7-19" as a list of numbers; hours: "7-19" means 7:00 to 18:59.
     *
     * @return int[]
     */
    public static function range( $spec, $min, $max, $hours = false )
    {
        $out = array();
        foreach ( explode( ',', (string)$spec ) as $part )
        {
            $part = trim( $part );
            if ( preg_match( '/^(\d+)\s*-\s*(\d+)$/', $part, $m ) )
            {
                $to = $hours ? (int)$m[2] - 1 : (int)$m[2];
                for ( $i = max( $min, (int)$m[1] ); $i <= min( $hours ? $max - 1 : $max, $to ); $i++ )
                    $out[] = $i;
            }
            elseif ( ctype_digit( $part ) )
                $out[] = (int)$part;
        }
        return array_values( array_unique( $out ) );
    }

    /** @return DateTimeZone The site's time zone (the rule's TimeZone when set) */
    public static function timeZone( $name = null )
    {
        try
        {
            if ( $name )
                return new DateTimeZone( $name );
        }
        catch ( Throwable $e )
        {
        }
        return new DateTimeZone( date_default_timezone_get() );
    }
}
