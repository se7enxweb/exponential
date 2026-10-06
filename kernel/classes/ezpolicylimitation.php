<?php
/**
 * File containing the eZPolicyLimitation class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZPolicyLimitation ezpolicylimitation.php
  \ingroup eZRole
  \brief Defines a limitation for a policy in the permission system

*/
class eZPolicyLimitation extends eZPersistentObject
{
    // Used for assign subtree matching
    public $LimitValue;

    public $PolicyID;
    public $LimitationID;
    public $Value;
    public $Values;

    /**
     * Rows of ezpolicy_limitation_value by limitation id, loaded ahead by eZRole::accessArrayByUserID() (site.ini
     * [RoleSettings] AccessArrayPrefetch) while it builds an access array; null when valueList() asks the database.
     *
     * @var array|null
     */
    public static $prefetchedValueRows = null;

    /** @var bool true when valueList() could not read the values: the policy of this limitation grants nothing */
    public $ReadFailed = false;

    /** @var array|null|false The definition of the limitation in its module (definitionInModule()); false: not looked up */
    public $DefinitionInModule = false;

    /*!
     Constructor
    */
    public function __construct( $row )
    {
          parent::__construct( $row );
          $this->NodeID = 0;
    }

    static function definition()
    {
        static $definition = array( "fields" => array( "id" => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'policy_id' => array( 'name' => 'PolicyID',
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true,
                                                               'foreign_class' => 'eZPolicy',
                                                               'foreign_attribute' => 'id',
                                                               'multiplicity' => '1..*' ),
                                         'identifier' => array( 'name' => 'Identifier',
                                                                'datatype' => 'string',
                                                                'default' => '',
                                                                'required' => true ) ),
                      "keys" => array( "id" ),
                      "function_attributes" => array( 'policy' => 'policy',
                                                      'values' => 'valueList',
                                                      'values_as_array' => 'allValues',
                                                      'values_as_string' => 'allValuesAsString',
                                                      'values_as_array_with_names' => 'allValuesAsArrayWithNames',
                                                      'limit_value' => 'limitValue',
                                                      'label' => 'label',
                                                      'denies_without_handler' => 'deniesWithoutHandler' ),
                      "increment_key" => "id",
                      "sort" => array( "id" => "asc" ),
                      "class_name" => "eZPolicyLimitation",
                      "name" => "ezpolicy_limitation" );
        return $definition;
    }

    function limitValue()
    {
        return $this->LimitValue;
    }

    /*!
     Get policy object of this policy limitation
    */
    function policy()
    {
        return eZPolicy::fetch( $this->attribute( 'policy_id' ) );
    }

    function setAttribute( $attr, $val )
    {
        switch( $attr )
        {
            case 'limit_value':
            {
                $this->LimitValue = $val;
            } break;

            default:
            {
                parent::setAttribute( $attr, $val );
            } break;
        }
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    static function createNew( $policyID, $identifier )
    {
        $policyParameter = new eZPolicyLimitation( array() );
        $policyParameter->setAttribute( 'policy_id', $policyID );
        $policyParameter->setAttribute( 'identifier', $identifier );
        $policyParameter->store();

        return $policyParameter;
    }

    /*!
     \static
     Create a new policy limitation for the policy \a $policyID with the identifier \a $identifier.
     \note The limitation is not stored.
    */
    static function create( $policyID, $identifier )
    {
        $row = array( 'id' => null,
                      'policy_id' => $policyID,
                      'identifier' => $identifier );
        return new eZPolicyLimitation( $row );
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    static function removeSelected( $ID )
    {
        eZPersistentObject::removeObject( eZPolicyLimitation::definition(),
                                          array( "id" => $ID ) );
    }

    static function fetchByIdentifier( $policyID, $identifier, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZPolicyLimitation::definition(),
                                                null,
                                                array( "policy_id" => $policyID,
                                                       "identifier" => $identifier ),
                                                $asObject );
    }

    static function fetchByPolicyID( $policyID, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZPolicyLimitation::definition(),
                                                    null,
                                                    array( "policy_id" => $policyID ),
                                                    null,
                                                    null,
                                                    $asObject );
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    function copy( $policyID )
    {
        $newParameter = eZPolicyLimitation::createNew( $policyID, $this->attribute( 'identifier' ) );
        foreach( $this->attribute( 'values' ) as $value )
        {
            $value->copy( $newParameter->attribute( 'id' ) );
        }
    }

    /*!
     \sa removeThis
    */
    static function removeByID( $id )
    {
        $db = eZDB::instance();

        $idString = $db->escapeString( $id );
        $db->begin();

        if ( $db->databaseName() === 'mongo' )
        {
            $db->deleteWhere( 'ezpolicy_limitation_value', [ 'limitation_id' => (int)$id ] );
            $db->deleteWhere( 'ezpolicy_limitation', [ 'id' => (int)$id ] );
        }
        else
        {
            $db->query( "DELETE FROM ezpolicy_limitation_value
                         WHERE ezpolicy_limitation_value.limitation_id = '$idString'" );

            $db->query( "DELETE FROM ezpolicy_limitation
                         WHERE ezpolicy_limitation.id = '$idString' " );
        }
        $db->commit();
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    function removeThis()
    {
        eZPolicyLimitation::removeByID( $this->attribute( 'id' ) );
    }

    function allValuesAsString()
    {
        $str='';
        foreach ( $this->attribute( 'values' ) as $value )
        {
            if ( $str == '' )
            {
                $str .= $value->attribute( 'value' );
            }else
            {
                $str .= ',' . $value->attribute( 'value' );
            }
        }
        return $str;
    }

    /**
     * Returns the definition of this limitation in the function list of its policy's module (the entry of
     * module.php, or of an extension through the filter module/functionlist), or null when the module, the function
     * or the limitation is not there (an extension that added it is no longer active).
     *
     * @return array|null
     */
    function definitionInModule()
    {
        if ( $this->DefinitionInModule !== false )
        {
            return $this->DefinitionInModule;
        }
        $this->DefinitionInModule = null;
        $policy = $this->attribute( 'policy' );
        if ( !$policy )
        {
            return null;
        }
        $mod = eZModule::exists( $policy->attribute( 'module_name' ) );
        if ( !is_object( $mod ) )
        {
            return null;
        }
        $this->DefinitionInModule = self::findDefinition( $mod->attribute( 'available_functions' ),
                                                          $policy->attribute( 'function_name' ),
                                                          $this->attribute( 'identifier' ) );
        return $this->DefinitionInModule;
    }

    /**
     * Returns the definition of the limitation $identifier of $function in the function list $functions: the entry
     * under that key, or else the one whose 'name' it is; null when there is none.
     *
     * @param array $functions
     * @param string $function
     * @param string $identifier
     * @return array|null
     */
    static function findDefinition( $functions, $function, $identifier )
    {
        if ( !is_array( $functions ) || !isset( $functions[$function] ) || !is_array( $functions[$function] ) )
        {
            return null;
        }
        if ( isset( $functions[$function][$identifier] ) && is_array( $functions[$function][$identifier] ) )
        {
            return $functions[$function][$identifier];
        }
        foreach ( $functions[$function] as $definition )
        {
            if ( is_array( $definition ) && isset( $definition['name'] ) && $definition['name'] === $identifier )
            {
                return $definition;
            }
        }
        return null;
    }

    /**
     * Returns the name of the limitation $definition as the role screens show it: its 'label' when the module or
     * extension gives one (translated by whoever wrote it), otherwise its name.
     *
     * @param array $definition
     * @return string
     */
    static function definitionLabel( $definition )
    {
        if ( is_array( $definition ) && isset( $definition['label'] ) && is_string( $definition['label'] ) && trim( $definition['label'] ) !== '' )
        {
            return $definition['label'];
        }
        return is_array( $definition ) && isset( $definition['name'] ) ? (string)$definition['name'] : '';
    }

    /**
     * Returns the values the role editor offers for the limitation $definition: the 'value' of each of its
     * 'values', or the 'id' of each entry its 'class' and 'function' list. Null when it offers none to choose from
     * (Node and Subtree are picked in the content browser).
     *
     * @param array $definition
     * @return string[]|null
     */
    static function offeredValues( $definition )
    {
        if ( !is_array( $definition ) )
        {
            return null;
        }
        $offered = array();
        if ( isset( $definition['values'] ) && is_array( $definition['values'] ) && count( $definition['values'] ) > 0 )
        {
            foreach ( $definition['values'] as $value )
            {
                if ( is_array( $value ) && isset( $value['value'] ) && is_scalar( $value['value'] ) )
                {
                    $offered[] = (string)$value['value'];
                }
            }
            return $offered;
        }
        if ( isset( $definition['class'], $definition['function'] ) && is_string( $definition['class'] ) && class_exists( $definition['class'] ) )
        {
            $obj = new $definition['class']( array() );
            $list = call_user_func_array( array( $obj, $definition['function'] ),
                                          isset( $definition['parameter'] ) && is_array( $definition['parameter'] ) ? $definition['parameter'] : array() );
            foreach ( is_array( $list ) ? $list : array() as $value )
            {
                if ( is_array( $value ) && isset( $value['id'] ) && is_scalar( $value['id'] ) )
                {
                    $offered[] = (string)$value['id'];
                }
            }
            return $offered;
        }
        return null;
    }

    /**
     * Returns the values of $posted that the role editor offers for the limitation $definition, for storing a
     * policy: a value that was not offered (a hand-made request, a value of an extension that changed) is left out
     * and logged. '-1' ("Any") is kept as the editor treats it. Values of a limitation that offers none to choose
     * from (Node, Subtree) are returned as they are.
     *
     * @param array $definition
     * @param array $posted
     * @return array
     */
    static function validValues( $definition, $posted )
    {
        $posted = is_array( $posted ) ? array_values( array_filter( $posted, 'is_scalar' ) ) : array();
        $offered = self::offeredValues( $definition );
        if ( $offered === null )
        {
            return array_values( array_unique( $posted ) );
        }
        $valid = array();
        foreach ( $posted as $value )
        {
            if ( (string)$value === '-1' || in_array( (string)$value, $offered, true ) )
            {
                $valid[] = $value;
            }
            else
            {
                eZDebug::writeWarning( 'The value ' . substr( (string)$value, 0, 100 ) . ' is not one of the values of the limitation ' .
                                       ( isset( $definition['name'] ) ? $definition['name'] : '?' ) . '; not stored', __METHOD__ );
            }
        }
        return array_values( array_unique( $valid ) );
    }

    /**
     * The name of this limitation as the role screens show it (see definitionLabel()); the identifier when its
     * module does not define it any more.
     *
     * @return string
     */
    function label()
    {
        $definition = $this->definitionInModule();
        return $definition ? self::definitionLabel( $definition ) : (string)$this->attribute( 'identifier' );
    }

    /**
     * Whether this is a limitation of a content policy that the kernel does not know and that no handler evaluates
     * (site.ini [RoleSettings] LimitationHandlers[]): its policy gives no access, which the role screens say.
     *
     * @return bool
     */
    function deniesWithoutHandler()
    {
        $policy = $this->attribute( 'policy' );
        if ( !$policy || !in_array( $policy->attribute( 'module_name' ), array( 'content', '*' ), true ) )
        {
            return false;
        }
        $identifier = (string)$this->attribute( 'identifier' );
        return !ezpContentLimitation::isKernelLimitation( $identifier ) && ezpContentLimitation::handler( $identifier ) === null;
    }

    function allValuesAsArrayWithNames()
    {
        $returnValue = null;
        $valueList   = $this->attribute( 'values_as_array' );
        $names       = array();
        $policy      = $this->attribute( 'policy' );
        if ( !$policy )
        {
            return $returnValue;
        }

        $currentModule = $policy->attribute( 'module_name' );
        $mod = eZModule::exists( $currentModule );
        if ( !is_object( $mod ) )
        {
            eZDebug::writeError( 'Failed to fetch instance for module ' . $currentModule );
            return $returnValue;
        }
        $functions = $mod->attribute( 'available_functions' );
        $functionNames = array_keys( $functions );

        $currentFunction = $policy->attribute( 'function_name' );
        $limitationValueArray = array();

        $limitation = self::findDefinition( $functions, $currentFunction, $this->attribute( 'identifier' ) );
        if ( !$limitation )
        {
            // A limitation its module does not define (any more): an extension that added it is not active. Its
            // values are shown as they are stored instead of not at all.
            $limitationValuesWithNames = array();
            foreach ( $valueList as $value )
            {
                $limitationValuesWithNames[] = array( 'Name' => $value, 'value' => $value );
            }
            return $limitationValuesWithNames;
        }

        if ( $limitation &&
             isset( $limitation['class'] ) &&
             count( $limitation[ 'values' ] ) == 0  )
        {
            $obj = new $limitation['class']( array() );
            $limitationValueList = call_user_func_array ( array( $obj , $limitation['function']) , $limitation['parameter'] );
            foreach( $limitationValueList as $limitationValue )
            {
                $limitationValuePair = array();
                $limitationValuePair['Name'] = $limitationValue[ 'name' ];
                $limitationValuePair['value'] = $limitationValue[ 'id' ];
                $limitationValueArray[] = $limitationValuePair;
            }
        }
        else if ( $limitation['name'] === 'Node' )
        {
            foreach ( $valueList as $value )
            {
                $node = eZContentObjectTreeNode::fetch( $value, false, false );
                if ( $node == null )
                    continue;
                $limitationValuePair = array();
                $limitationValuePair['Name'] = $node['name'];
                $limitationValuePair['value'] = $value;
                $limitationValuePair['node_data'] = $node;
                $limitationValueArray[] = $limitationValuePair;
            }
        }
        else if ( $limitation['name'] === 'Subtree' )
        {
            foreach ( $valueList as $value )
            {
                $subtreeObject = eZContentObjectTreeNode::fetchByPath( $value, false );
                if ( $subtreeObject != null )
                {
                    $limitationValuePair = array();
                    $limitationValuePair['Name'] = $subtreeObject['name'];
                    $limitationValuePair['value'] = $value;
                    $limitationValuePair['node_data'] = $subtreeObject;
                    $limitationValueArray[] = $limitationValuePair;
                }
            }
        }
        else
        {
            $limitationValueArray = $limitation[ 'values' ];
        }
        $limitationValuesWithNames = array();
        foreach ( array_keys( $valueList ) as $key )
        {
            $value = $valueList[$key];
            if ( isset( $limitationValueArray ) )
            {
                reset( $limitationValueArray );
                foreach ( array_keys( $limitationValueArray ) as $ckey )
                {
                    if ( $value == $limitationValueArray[$ckey]['value'] )
                    {
                        $limitationValuesWithNames[] = $limitationValueArray[$ckey];
                    }
                }
            }
        }

        return $limitationValuesWithNames;
    }

    /*!
     Get limitation array

     \return access limitation array
    */
    function limitArray()
    {
        $limitValues = $this->attribute( 'values' );

        $valueArray = array();

        foreach ( array_keys( $limitValues ) as $valueKey )
        {
            $valueArray[] = $limitValues[$valueKey]->attribute( 'value' );
        }

        return array( $this->attribute( 'identifier' ) => $valueArray );
    }

    function allValues()
    {
        $values = array();
        foreach ( $this->attribute( 'values' ) as $value )
        {
                $values[] = $value->attribute( 'value' );
        }

        return $values;
    }

    function valueList()
    {
        if ( !isset( $this->Values ) )
        {
            if ( isset( self::$prefetchedValueRows[(int)$this->attribute( 'id' )] ) )
            {
                // Rows eZRole::accessArrayByUserID() loaded for all limitations at once, by value as below
                $values = array();
                foreach ( self::$prefetchedValueRows[(int)$this->attribute( 'id' )] as $row )
                {
                    $values[] = new eZPolicyLimitationValue( $row );
                }
            }
            else
            {
                $values = eZPersistentObject::fetchObjectList( eZPolicyLimitationValue::definition(),
                                                               null, array( 'limitation_id' => $this->attribute( 'id') ), null, null,
                                                               true);
                if ( !is_array( $values ) )
                {
                    // eZPolicy::accessArray() leaves out a policy with a limitation whose values could not be read
                    eZRole::noteAccessReadFailure( 'values of limitation', $this->attribute( 'id' ) );
                    $this->ReadFailed = true;
                    $values = array();
                }
            }

            if ( $this->LimitValue )
            {
                $values[] = new eZPolicyLimitationValue( array ( 'id' => -1,
                                                                 'value' => $this->LimitValue ) );
            }

            $this->Values = $values;
        }

        return $this->Values;
    }

    static function findByType( $type, $value, $asObject = true, $useLike = true )
    {
        $cond = '';
        $db = eZDB::instance();
        $value = $db->escapeString( $value );
        $type = $db->escapeString( $type );
        if ( $useLike === true )
        {
            $cond = "ezpolicy_limitation_value.value like '$value%' ";
        }
        else
        {
            $cond = "ezpolicy_limitation_value.value = '$value' ";
        }

        $query = "SELECT DISTINCT ezpolicy_limitation.*
                  FROM ezpolicy_limitation,
                       ezpolicy_limitation_value
                  WHERE
                       ezpolicy_limitation.identifier = '$type' AND
                       $cond AND
                       ezpolicy_limitation_value.limitation_id =  ezpolicy_limitation.id";

        if ( $db->databaseName() === 'mongo' )
        {
            $valueFilter = $useLike
                ? [ '$regex' => '^' . preg_quote( $value, '/' ), '$options' => 'i' ]
                : $value;
            // Find matching limitation_ids from values collection
            $valRows = $db->aggregate( 'ezpolicy_limitation_value', [
                [ '$match' => [ 'value' => $valueFilter ] ],
                [ '$project' => [ '_id' => 0, 'limitation_id' => 1 ] ],
            ] );
            $limitationIDs = array_values( array_unique( array_map(
                fn( $r ) => (int)$r['limitation_id'], $valRows
            ) ) );
            if ( empty( $limitationIDs ) )
                return [];
            $dbResult = $db->aggregate( 'ezpolicy_limitation', [
                [ '$match' => [ 'identifier' => $type, 'id' => [ '$in' => $limitationIDs ] ] ],
            ] );
        }
        else
        {
            $dbResult = $db->arrayQuery( $query );
        }
        $resultArray = array();
        $resultCount = count( $dbResult );
        for( $i = 0; $i < $resultCount; $i++ )
        {
            if ( $asObject )
            {
                $resultArray[] = new eZPolicyLimitation( $dbResult[$i] );
            }
            else
            {
                $resultArray[] = $dbResult[$i]['id'];
            }
        }
        return $resultArray;
    }
}

?>