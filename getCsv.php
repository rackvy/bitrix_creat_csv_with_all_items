<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/ajax/slim_tech_create_csv_file.php");

if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock")){
	echo "failure";
	return;
}




$nameFile = date("Y.m.d").'_'.date("His").'_active.csv';

$pathToFile = $_SERVER['DOCUMENT_ROOT'].'/upload/csv/'.$nameFile;


$arrCSV = [
	[
		'',
		date("Y.m.d").' '.date("H:i:s")
	],
	[
		'',
		''
	]
];


$arItems = [];



$arSelect = Array("ID", "NAME", "CODE", "IBLOCK_SECTION_ID");
$arFilter = Array("IBLOCK_ID"=>1, "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
while($ob = $res->GetNextElement())
{
 $arFields = $ob->GetFields();
 
 if($arFields['IBLOCK_SECTION_ID'] == ''){
	 $arFields['IBLOCK_SECTION_ID'] = 0;
 }
 
 $arItems[$arFields['IBLOCK_SECTION_ID']][] = [
	 'CODE' => $arFields['CODE'],
	 'NAME' => $arFields['NAME']
 ];
 
 
}



	
$arSection = [];

$tree = CIBlockSection::GetTreeList(
    $arFilter=Array('IBLOCK_ID' => 1),
    $arSelect=Array()
);
while($section = $tree->GetNext()) {
	if($section['DEPTH_LEVEL'] == 1){continue;}
	if($section['DEPTH_LEVEL'] == 2){
		$arSection[$section['ID']]['NAME'] = $section['NAME'];
		$arSection[$section['ID']]['ID'] = $section['ID'];
	}
	if($section['DEPTH_LEVEL'] == 3){
		$arSection[$section['IBLOCK_SECTION_ID']]['SUBSECTION'][] = [
			'NAME' => $section['NAME'],
			'ID' => $section['ID']
		];
	}
}

foreach($arSection as $sectionDepth1){
	$arrCSV[] = [
		$sectionDepth1['NAME'],
		''
	];
	
	if(!empty($arItems[$sectionDepth1['ID']])){
		foreach($arItems[$sectionDepth1['ID']] as $arItem){
			$arrCSV[] = [
				$arItem['CODE'],
				$arItem['NAME']
			];
		}
	}
	if(count($sectionDepth1['SUBSECTION']) > 0){
		foreach($sectionDepth1['SUBSECTION'] as $sectionDepth2){
			$arrCSV[] = [
				$sectionDepth2['NAME'],
				''
			];
			if(!empty($arItems[$sectionDepth2['ID']])){
				foreach($arItems[$sectionDepth2['ID']] as $arItem){
					$arrCSV[] = [
						$arItem['CODE'],
						$arItem['NAME']
					];
				}
			}

		}
	}
}
   


slim_tech_create_csv_file( $arrCSV, $pathToFile );

LocalRedirect('/upload/csv/'.$nameFile);

   

