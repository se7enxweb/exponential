<?php
/**
 * Setup > Interface languages (setup/languages): every locale with the translation the administration shows in it,
 * the chain of translations it falls back to (expI18nFallbackResolver: i18n.ini [TranslationSettings]
 * FallbackLanguages[], FallbackRules[], the automatic same-language fallback), how much of the kernel's interface
 * and of the extensions' it shows translated, and the problems ./console exp:i18n check finds. With (locale) one
 * locale's chain step by step and every extension. Read only; policy setup/system_info.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Setup
{

class Languages extends \Exponential\Runnable\ModuleView
{
    /** the kinds the list can be limited to (expI18nFallbackReport::kind()) */
    const KINDS = array( 'own', 'fallback', 'untranslated' );

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $http = \eZHTTPTool::instance();

        $show = isset( $Params['Show'] ) ? (string)$Params['Show'] : '';
        if ( $http->hasGetVariable( 'Show' ) )
            $show = (string)$http->getVariable( 'Show' );
        $show = in_array( $show, self::KINDS, true ) ? $show : '';
        $locale = isset( $Params['Locale'] ) ? (string)$Params['Locale'] : '';
        if ( !preg_match( \expI18nFallbackResolver::LOCALE_PATTERN, $locale ) )
            $locale = '';

        $report = new \expI18nFallbackReport();
        $resolver = $report->resolver();
        $all = $report->rows( null, true );
        $counts = \expI18nFallbackReport::counts( $all );
        $rows = array();
        foreach ( \expI18nFallbackReport::filterRows( $all, $show ) as $row )
            $rows[] = self::shown( $row );

        $detail = null;
        if ( $locale !== '' )
        {
            $detailRows = $report->rows( array( $locale ), true );
            $detail = self::shown( $detailRows[0] );
            $detail['exists'] = $resolver->localeExists( $locale );
            $detail['extension_rows'] = array();
            foreach ( $detailRows[0]['extensions'] as $name => $coverage )
                $detail['extension_rows'][] = array( 'name' => $name, 'coverage' => $coverage,
                                                     'pct' => $coverage === null ? 0 : (int)$coverage );
        }

        $site = \eZINI::instance();
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'language_rows', $rows );
        $tpl->setVariable( 'language_counts', $counts );
        $tpl->setVariable( 'language_total', count( $all ) );
        $tpl->setVariable( 'language_show', $show );
        $tpl->setVariable( 'language_detail', $detail );
        $tpl->setVariable( 'language_problems', $report->problems() );
        $tpl->setVariable( 'language_current', (string)$site->variable( 'RegionalSettings', 'Locale' ) );
        $tpl->setVariable( 'language_extensions', count( $report->extensionRoots() ) );
        $tpl->setVariable( 'language_settings', self::fallbackSettings( $resolver ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/languages.tpl' );
        $Result['path'] = array( array( 'url' => '/setup/menu', 'text' => \ezpI18n::tr( 'kernel/setup', 'Setup' ) ),
                                 array( 'url' => $detail ? '/setup/languages' : false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Interface languages' ) ) );
        if ( $detail )
            $Result['path'][] = array( 'url' => false, 'text' => $locale );
        return $Result;
    }

    /**
     * A report row as the template shows it: the kind, the kernel coverage as a whole percentage for the meter, and
     * each step's rule as configured, automatic or rule (an extension's, with its class).
     *
     * @param array $row expI18nFallbackReport::rows()
     * @return array
     */
    public static function shown( array $row )
    {
        $chain = array();
        foreach ( $row['chain'] as $step )
        {
            $kind = $step['rule'] === \expI18nFallbackResolver::RULE_CONFIGURED ? 'configured'
                  : ( $step['rule'] === \expI18nFallbackResolver::RULE_AUTOMATIC ? 'automatic' : 'rule' );
            $chain[] = array( 'target' => $step['target'], 'source' => $step['target'] === \expI18nFallbackResolver::SOURCE,
                              'rule' => $step['rule'], 'rule_kind' => $kind, 'translated' => $step['translated'] );
        }
        $summary = isset( $row['extension_summary'] ) ? $row['extension_summary'] : \expI18nFallbackReport::summary( array() );
        return array( 'locale' => $row['locale'], 'english' => $row['english'], 'native' => $row['native'], 'http' => $row['http'],
                      'has_coverage' => $row['coverage'] !== null,
                      'own' => $row['own'], 'kind' => \expI18nFallbackReport::kind( $row ),
                      'coverage' => $row['coverage'], 'pct' => $row['coverage'] === null ? 0 : (int)$row['coverage'],
                      'from' => $row['from'], 'chain' => $chain, 'problem' => $row['problem'],
                      'problem_target' => $row['problem_target'], 'extensions' => $summary );
    }

    /**
     * The settings in effect, for the page.
     *
     * @param \expI18nFallbackResolver $resolver
     * @return array
     */
    public static function fallbackSettings( \expI18nFallbackResolver $resolver )
    {
        $rules = array();
        foreach ( $resolver->ruleNames() as $name )
        {
            if ( $name !== \expI18nFallbackResolver::RULE_CONFIGURED && $name !== \expI18nFallbackResolver::RULE_AUTOMATIC )
                $rules[] = $name;
        }
        return array( 'mode' => $resolver->mode(), 'automatic' => (bool)$resolver->option( 'automatic' ),
                      'exclude' => (array)$resolver->option( 'automatic_exclude', array() ),
                      'max_depth' => (int)$resolver->option( 'max_depth' ), 'rules' => $rules,
                      'configured' => count( $resolver->configured() ) );
    }
}

}
