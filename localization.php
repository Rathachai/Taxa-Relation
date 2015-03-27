<?php

//****************************************************************************************************
//  PROJECT             :   Visualization of Taxonomic Interactions
//  Developer           :   Rathachai CHAWUTHAI     (National Institute of Informatics,Japan)
//  Supervisor          :   Hideaki TAKEDA          (National Institute of Informatics,Japan)
//  Product Mananger    :   Tsuyoshi HOSOYA         (National Museum of Nature and Science, Japan)
//  Created             :   2015
//****************************************************************************************************

function L($label){
	global $LABEL;
	global $lang;
	if(isset($LABEL[$label])){
		if(isset($LABEL[$label][$lang])){
			return $LABEL[$label][$lang];
		}else{
			return $LABEL[$label]["en"];
		}
	}else{
		return $label;
	}
}


// DECLARE VARIABLE

$LABEL = array();

//****************************************************************************************************
// START LOCALIZATION
//****************************************************************************************************

// ******************************** Common ********************************

$LABEL["Project Name"] = array(
	"en"	=>	"Taxa Relation",
	"th"	=>	"ความสัมพันธ์ระหว่างสิ่งมีชีวิต",
	"jp"	=>	"生物間相互関係"
);

$LABEL["Home"] = array(
	"en"	=>	"Home",
	"th"	=>	"หน้าหลัก",
	"jp"	=>	"ホーム"
);

$LABEL["About"] = array(
	"en"	=>	"About",
	"th"	=>	"ข้อมูลทั่วไป",
	"jp"	=>	"このサイトについて"
);

$LABEL["Guide"] = array(
	"en"	=>	"Guideline",
	"th"	=>	"วิธีใช้",
	"jp"	=>	"ガイドライン"
);

$LABEL["TBD"] = array(
	"en"	=>	"This page is under construction.",
	"th"	=>	"ขออภัย หน้านี้อยู่ระหว่างการจัดทำ",
	"jp"	=>	"このページは工事中です。"
);

//Footer
$LABEL["Footer Text"] = array(
	"en"	=>	"National Museum of Nature and Science | Linked Open Data Initiative <br/>".
                "© ".date("Y")." — All rights reserved." ,
	"jp"	=>	"国立科学博物館 | リンクト・オープン・データ・イニシアティブ <br/>".
                "© ".date("Y")." — 全著作権所有." 
);

// ******************************** index.php ********************************

$LABEL["Search"]= array(
	"en"	=>	"Search",
	"th"	=>	"ค้นหา",
	"jp"	=>	"検索"
);

$LABEL["Add"]= array(
	"en"	=>	"Add",
	"th"	=>	"เพิ่ม",
	"jp"	=>	"追加"
);

$LABEL["Add and Expand"]= array(
	"en"	=>	"Add with Relationships",
	"th"	=>	"เพิ่มพร้อมกับความสัมพันธ์",
	"jp"	=>	"関係を追加"
);

$LABEL["Fungi"] = array(
	"en"	=>	"Fungi",
	"th"	=>	"เห็ดรา",
	"jp"	=>	"菌類"
);


$LABEL["Animal"] = array(
	"en"	=>	"Animal",
	"th"	=>	"สัตว์",
	"jp"	=>	"動物"
);

$LABEL["Plant"] = array(
	"en"	=>	"Plant",
	"th"	=>	"พืช",
	"jp"	=>	"植物"
);

$LABEL["Exploration"] = array(
	"en"	=>	"Exploration",
	"th"	=>	"สำรวจความสัมพันธ์ของสิ่งมีชีวิต",
	"jp"	=>	"関係を検索"
);

$LABEL["Relations"] = array(
	"en"	=>	"Relations",
	"th"	=>	"ความสัมพันธ์",
	"jp"	=>	"関係"
);

$LABEL["Interactions"] = array(
	"en"	=>	"Interactions",
	"th"	=>	"ปฎิสัมพันธ์",
	"jp"	=>	"相互関係"
);

$LABEL["Taxonomy"] = array(
	"en"	=>	"Taxonomy",
	"th"	=>	"อนุกรมวิธาน",
	"jp"	=>	"分類"
);

$LABEL["Clear Screen"] = array(
	"en"	=>	"Clear Screen !",
	"th"	=>	"ลบทั้งหมด !",
	"jp"	=>	"画面をクリア !"
);

$LABEL["Clear Links"] = array(
	"en"	=>	"Clear Links !",
	"th"	=>	"ลบเส้น !",
	"jp"	=>	"リンクをクリア !"
);

$LABEL["Find Paths"] = array(
	"en"	=>	"Find Paths",
	"th"	=>	"ค้นหาความสัมพันธ์",
	"jp"	=>	"相互関係の検索"
);


// ******************************** guide.php ********************************

$LABEL["search head"] = array(
	"en"	=>	"Finding Taxon's Information",
	"jp"	=>	"分類群の情報の検索"
);

$LABEL["search 1"] = array(
	"en"	=>	"Search for taxa using keyword.",
	"jp"	=>	"キーワードを使用して分類群を検索します。"
);

$LABEL["search 2"] = array(
	"en"	=>	"Click on {1} to add only the selected taxon into the screen.",
	"jp"	=>	"検索結果ウィンドウで調べたい生物種を選び、追加ボタン（ {1} ）をクリックしてください。 選択した生物種がサークルとして関係表示画面に現れます。"
);

$LABEL["search 3"] = array(
	"en"	=>	"(or) Click on {1} to add the selected taxon and its relationships into screen.",
	"jp"	=>	"（または展開ボタン（ {1} ）をクリックすると、選択した生物種が関係表示画面に表示されると共に、その生物種と関係ある生物種が関係を示すラインと共に提示されます。"
);

$LABEL["search 4"] = array(
	"en"	=>	"If double-click on a node in the screen, its relationships will be displayed.",
	"jp"	=>	"画面にあるノードにダブルクリックをすると、関係性を表示する。"
);

$LABEL["path head"] = array(
	"en"	=>	"Finding Relationships between two taxa",
	"jp"	=>	"分類群間の関係を見つける"
);


$LABEL["path 1"] = array(
	"en"	=>	"Move one taxon to left side of the border.",
	"jp"	=>	"ウィンドウの左側に生物種を示すサークルを移動します。"
);

$LABEL["path 2"] = array(
	"en"	=>	"When the taxon close to the border, a big blue bubble appears.",
	"jp"	=>	"サークルがウインドウの左側に近づくと、大きな青い泡が表示されます。"
);

$LABEL["path 3"] = array(
	"en"	=>	"Put the taxon inside the bubble.",
	"jp"	=>	"バブルの内側に生物種のサークルを置きます。"
);

$LABEL["path 4"] = array(
	"en"	=>	"Then, the taxon is locked by the bubble.",
	"jp"	=>	"そうすると、生物種はバブルにロックされます。"
);

$LABEL["path 5"] = array(
	"en"	=>	"Move another taxon to the right side of the border.",
	"jp"	=>	"同様にウインドウの右側に別の生物種を移動して、バブルに固定します。"
);

$LABEL["path 6"] = array(
	"en"	=>	"When two taxa are locked in the bubbles, a button 'Find Paths' appears at left side under the screen. <br/> Then, Click on the button in order to find relationships between two taxa.",
	"jp"	=>	"二つの生物種が左右のバブルにロックされると、「相互関係の検索」ボタンが関係表示画面の下の左側に表示されます。 <br/> このボタンを押します。"
);

$LABEL["path 7"] = array(
	"en"	=>	"Some relationships between the selected taxa are discovered.",
	"jp"	=>	"選択した2つ生物種の間の関係が表示されます。"
);

$LABEL["path 8"] = array(
	"en"	=>	"After that, users can take a taxon away from a bubble or move another taxon into a bubble and find paths again.",
	"jp"	=>	"その後、バブルから生物種を外して、バブルに別の分類群を移動することで再び関係を見つけることができます。"
);

// ******************************** about.php ********************************
$LABEL["about text"] = array(
	"en"	=>	"This prototype system aims to be a web-based application for illustrating the visualization of relationships and taxonomies of fungi, plants, and animals. The graph representation of this project is mainly developed on top of D3, Javascript, and JQuery. It also retrieves the search from a dataset on biodiversity, which is in an RDF (Resource Description Framework) repository, using the SPARQL (SPARQL Protocol and RDF Query Language) engine.",
	"jp"	=>	"本システムは、共通の構造をもった生物多様性総合関係に関する複数のデータセットから検索対象を検索し、それと相互関係ある生物を図示するものである。各データセットとは生物種と生物種の相互関係が記述されたデータファイルであり、本システムはこれらのデータセットの情報を統合して提示する。
本システムはユーザに提供するアプリケーションを開発する上での機能要件を検討するためのプロトタイプである。"
);

$LABEL["Organizers"] = array(
	"en"	=>	"Organizers",
	"jp"	=>	"オーガナイザー"
);

$LABEL["KAHAKU"] = array(
	"en"	=>	"National Museum of Nature and Science",
	"jp"	=>	"国立科学博物館"
);

$LABEL["LODI"] = array(
	"en"	=>	"Linked Open Data Initiative",
	"jp"	=>	"リンクト・オープン・データ・イニシアティブ"
);

$LABEL["LODAC"] = array(
	"en"	=>	"Linked Open Data for ACadmia",
	"jp"	=>	"Linked Open Data for ACadmia"
);

$LABEL["NII"] = array(
	"en"	=>	"National Institute of Informatics",
	"jp"	=>	"国立情報学研究所"
);

$LABEL["Development Team"] = array(
	"en"	=>	"Development Team",
	"jp"	=>	"開発チーム"
);

$LABEL["Hosoya-name"] = array(
	"en"	=>	"Tsuyoshi HOSOYA",
	"jp"	=>	"細矢 剛"
);

$LABEL["Hosoya-position"] = array(
	"en"	=>	"PhD, Mycologist",
	"jp"	=>	"菌学者"
);

$LABEL["Takeda-name"] = array(
	"en"	=>	"Hideaki TAKEDA ",
	"jp"	=>	"武田 英明"
);

$LABEL["Takeda-position"] = array(
	"en"	=>	"Dr.Eng, Professor ",
	"jp"	=>	"教授"
);

$LABEL["Rathachai-name"] = array(
	"en"	=>	"Rathachai CHAWUTHAI ",
	"jp"	=>	"ラッタチャイ チャウウタイ"
);

$LABEL["Rathachai-position"] = array(
	"en"	=>	"M.Eng, PhD student",
	"jp"	=>	"研究生"
);

$LABEL["Software License"] = array(
	"en"	=>	"Software License",
	"jp"	=>	"ソフトウェアライセンス"
);

$LABEL["Our License Statement"] = array(
	"en"	=>	"All source code files are published under the <a target='_blank' href='LICENSE'>MIT License</a>, and have the following copyrights",
	"jp"	=>	"本件において開発されたプログラムは <a target='_blank' href='LICENSE'>MIT License</a> 野本でオープンソースとして公開。そして、次の著作権を持っている"
);

$LABEL["Note 1"] = array(
	"en"	=>	"Note: All library files in the folder ~/lib/* are distributed under their original license.",
	"jp"	=>	"注：フォルダの~/lib/*内のすべてのライブラリファイルを元のライセンスの下で配布されている。"
);

$LABEL["Soucecode Host"] = array(
	"en"	=>	"All files are hosted on",
	"jp"	=>	"ソースコードファイルのリポジトリ"
);

$LABEL["Third-Party Libraries"] = array(
	"en"	=>	"Third-Party Libraries",
	"jp"	=>	"サードパーティ製のライブラリ"
);

// ******************************** template ********************************

$LABEL["key"] = array(
	"en"	=>	"English",
	"jp"	=>	"Japanese"
);

?>