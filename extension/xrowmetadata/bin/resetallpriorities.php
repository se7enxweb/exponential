<?php

$db = eZDB::instance();
$offset = 0;
$length = 100;
$cond = array(
    'data_type_string' => xrowMetaDataType::DATA_TYPE_STRING
);

$count = eZPersistentObject::count( eZContentObjectAttribute::definition(), $cond );
echo "There are $count priorities to reset.\n";
$output = new ezcConsoleOutput();
$bar = new ezcConsoleProgressbar( $output, $count / $length );

$limit = array(
    'offset' => $offset ,
    'length' => $length
);
$list = eZPersistentObject::fetchObjectList( eZContentObjectAttribute::definition(), null, $cond, null, $limit );

while ( ! empty( $list ) )
{
    $db->begin();
    ;
    /* var eZContentObjectAttribute */
    foreach ( $list as $attribute )
    {
        /* var xrowMetaData */
        $data = $attribute->content();
        $data->priority = null;
        $attribute->setContent( $data );
        $attribute->store();
    }
    $db->commit();

    $bar->advance();
    $offset = $offset + $length;
    $limit = array(
        'offset' => $offset ,
        'length' => $length
    );
    $list = eZPersistentObject::fetchObjectList( eZContentObjectAttribute::definition(), null, $cond, null, $limit );
}
