<?php

return array(
	'TMPL_PARSE_STRING' => array(
        '__UPLOAD__' => __ROOT__ . '/Upload',
        '__PUBLIC__' => __ROOT__ . '/Public',
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
       
    ),
    'DATA_CACHE_TYPE' => 'file',
	'DATA_CACHE_TIME' => '5000',
    //'TMPL_EXCEPTION_FILE' => './404.html',
);

?>
