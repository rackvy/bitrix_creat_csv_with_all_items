<?php
	
function slim_tech_create_csv_file( $create_data, $file = null, $col_delimiter = ';', $row_delimiter = "\r\n" ){

	if( ! is_array($create_data) )
		return false;

	if( $file && ! is_dir( dirname($file) ) )
		return false;

	// строка, которая будет записана в csv файл
	$CSV_str = '';

	// перебираем все данные
	foreach( $create_data as $row ){
		$cols = array();

		foreach( $row as $col_val ){
			// строки должны быть в кавычках ""
			// кавычки " внутри строк нужно предварить такой же кавычкой "
			if( $col_val && preg_match('/[",;\r\n]/', $col_val) ){
				// поправим перенос строки
				if( $row_delimiter === "\r\n" ){
					$col_val = str_replace( "\r\n", '\n', $col_val );
					$col_val = str_replace( "\r", '', $col_val );
				}
				elseif( $row_delimiter === "\n" ){
					$col_val = str_replace( "\n", '\r', $col_val );
					$col_val = str_replace( "\r\r", '\r', $col_val );
				}

				$col_val = str_replace( '"', '""', $col_val ); // предваряем "
				$col_val = '"'. $col_val .'"'; // обрамляем в "
			}

			$cols[] = $col_val; // добавляем колонку в данные
		}

		$CSV_str .= implode( $col_delimiter, $cols ) . $row_delimiter; // добавляем строку в данные
	}

	$CSV_str = rtrim( $CSV_str, $row_delimiter );

	// задаем кодировку windows-1251 для строки
	if( $file ){
		$CSV_str = iconv( "UTF-8", "cp1251",  $CSV_str );
		//$CSV_str = chr(0xEF) . chr(0xBB) . chr(0xBF). $CSV_str;
		// создаем csv файл и записываем в него строку
		$done = file_put_contents( $file, $CSV_str );
		
		return $done ? $CSV_str : false;
	}

	return $CSV_str;

}
