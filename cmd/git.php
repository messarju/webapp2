<?php



            $opts = array(
                'http' => array(
                    'method'     => "GET",
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                ),
            );

            $context = stream_context_create($opts);

            if (!$fp = fopen($url, 'r', false, $context)) {
                trigger_error("Unable to open URL ($url)", E_USER_ERROR);
            }


function git_tree($data)
{
/*
The format of a tree object:

tree [content size]\0[Entries having references to other trees and blobs]
The format of each entry having references to other trees and blobs:

[mode] [file/folder name]\0[SHA-1 of referencing blob or tree]
'/^tree (\d+)\x00/'
'/^(\d+) [^\x00]+\x00.{20}/'


'tree %u\x00'
'%u %s\x00'
*/



}
