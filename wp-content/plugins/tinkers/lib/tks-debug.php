<?php
/*
 * 以下デバッグ出力用
*/

function tks_debug($n,$arg){
	//preで囲んで出力
	echo('<pre>');
	echo('<strong>' . $n . '</strong>');
	var_dump($arg);
	echo('</pre>');
}