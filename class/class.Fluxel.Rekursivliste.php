<?php

namespace Fluxel;

class Rekursivliste {
    
	private $cache = null;
	private $tag = "fluxel_rekursivliste";
	
	public function __construct() {
		$this->cache = \Shop::Cache();
	}
	
	public function getRecursiveCategoryIds($kKategorie) {
        $cacheId = $this->tag . '_' . $kKategorie;
		if (($arr = $this->cache->get($cacheId)) === false)
		{
			$arr = $this->buildRecursiveCategoryIds($kKategorie);
            $this->cache->set($cacheId, $arr, [CACHING_GROUP_OPTION, $this->tag]);
        }
		return $arr;
	}
	
	private function buildRecursiveCategoryIds($kKategorie) {
		$sql = "SELECT *
				FROM (SELECT tkategorie.kKategorie,
								tkategorie.kOberKategorie,
								tkategorie.cName,
								IFNULL(ttEinschliessen.cWert, '0') AS 'nEinschliessen',
								IFNULL(ttAusschliessen.cWert, '0') AS 'nAusschliessen',
								tkategorie.nLevel
						FROM (SELECT tkategorie.kKategorie, 
								  tkategorie.kOberKategorie,
								  tkategorie.cName,
								  tkategorie.nLevel,
								  @pv := '" . ((int) $kKategorie) . "'
								  FROM tkategorie
								  ORDER BY tkategorie.nLevel) AS tkategorie
						LEFT JOIN tkategorieattribut AS ttEinschliessen ON ttEinschliessen.kKategorie = tkategorie.kKategorie AND ttEinschliessen.cName = 'Rekursion einschließen' AND ttEinschliessen.cWert='1'
						LEFT JOIN tkategorieattribut AS ttAusschliessen ON ttAusschliessen.kKategorie = tkategorie.kKategorie AND ttAusschliessen.cName = 'Rekursion ausschließen' AND ttAusschliessen.cWert='1'
						WHERE FIND_IN_SET(tkategorie.kOberKategorie, @pv)
							AND LENGTH(@pv := CONCAT(@pv, ',', tkategorie.kKategorie))
					UNION
						SELECT tkategorie.kKategorie,
								tkategorie.kOberKategorie,
								tkategorie.cName,
								IFNULL(ttEinschliessen.cWert, '0') AS 'nEinschliessen',
								IFNULL(ttAusschliessen.cWert, '0') AS 'nAusschliessen',
								tkategorie.nLevel
						FROM (SELECT tkategorie.kKategorie, 
								  tkategorie.kOberKategorie,
								  tkategorie.nLevel,
								  tkategorie.cName,
								  @pv := '" . ((int) $kKategorie) . "'
							 FROM tkategorie
							 ORDER BY nLevel DESC) AS tkategorie
						LEFT JOIN tkategorieattribut AS ttEinschliessen ON ttEinschliessen.kKategorie = tkategorie.kKategorie AND ttEinschliessen.cName = 'Rekursion einschließen' AND ttEinschliessen.cWert='1'
						LEFT JOIN tkategorieattribut AS ttAusschliessen ON ttAusschliessen.kKategorie = tkategorie.kKategorie AND ttAusschliessen.cName = 'Rekursion ausschließen' AND ttAusschliessen.cWert='1'
						WHERE FIND_IN_SET(tkategorie.kKategorie, @pv)
							AND LENGTH(@pv := CONCAT(@pv, ',', tkategorie.kOberKategorie))) AS tkategorie
				ORDER BY tkategorie.nLevel";
				
		$rows = \Shop::DB()->query($sql, 2);

		$oKategorie_arr = [];
		foreach($rows AS $row) {
			$row->children = [];
			$oKategorie_parent = null;
			
			if(@$oKategorie_arr[$row->kOberKategorie]) {
				$oKategorie_parent = $oKategorie_arr[$row->kOberKategorie];
			}
			
			if($oKategorie_parent) {
				$oKategorie_parent->children[] = $row;
			}
			
			$oKategorie_arr[$row->kKategorie] = $row;
		}
		
		$include = false;
		$this->firstInclude($kKategorie, $oKategorie_arr, $include);
		return $this->getRecursiveCategories($kKategorie, $oKategorie_arr, $include);
	}
		
	private function getRecursiveCategories($kKategorie, $oKategorie_arr, $include, $found_first = false) {
		$kKategorie_arr = [];
		foreach($oKategorie_arr AS $oKategorie) {
			$include = $oKategorie->nEinschliessen || ($include && !$oKategorie->nAusschliessen);
			
			if($kKategorie == $oKategorie->kKategorie) {
				$found_first = true;
			}
			
			if($found_first && ($oKategorie->kKategorie == $kKategorie || $include)) {
				$kKategorie_arr[] = $oKategorie->kKategorie;
			}
			
			$kKategorie_arr = array_merge($kKategorie_arr, $this->getRecursiveCategories($kKategorie, $oKategorie->children, $include, $found_first));
		}
		
		return $kKategorie_arr;
	}

	private function firstInclude($kKategorie, &$oKategorie_arr, &$include = false) {
		foreach($oKategorie_arr AS $oKategorie) {
			$include = $oKategorie->nEinschliessen || ($include && !$oKategorie->nAusschliessen);
			
			if($oKategorie->kKategorie == $kKategorie) {
				return true;
			}
			
			$tmpInclude = $include;
			if($this->firstInclude($kKategorie, $oKategorie->children, $tmpInclude)) {
				$include = $tmpInclude;
				return true;
			}
		}
	}
}