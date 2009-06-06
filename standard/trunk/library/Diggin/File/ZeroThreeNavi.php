<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Diggin
 * @package    Diggin_File
 * @subpackage ZeroThreeNavi
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

class Diggin_File_ZeroThreeNavi
{

    private $_areatxt;
    
    public function __construct($_areatxt)
    {
       $this->_areatxt = $_areatxt;
    }
    
    /**
     * area.txtからの経路情報を配列取得
     * 
     * @param string $_areatxt
     * @return array
     */
    public function getLineData()
    {
        $lines = explode("#", $this->_areatxt);
        foreach ($lines as $lineNo => $line ) {
            if(trim($line)){
                $track = explode("\n", $line);
                array_pop($track);
                $nameAndColor = array_shift($track);
                list($data[$lineNo]["Name"], $data[$lineNo]["Color"]) = explode(",",$nameAndColor);
                foreach($track as $no => $location) {
                    $data[$lineNo]["locations"][$no]["keido"] = substr($location, 0, 9);
                    $data[$lineNo]["locations"][$no]["ido"] = substr($location, 10, 8);
                    $data[$lineNo]["locations"][$no]["locationNm"] = trim(substr($location, 19));
                }
            } 
        }

        return $data;
    }
    
    /**
     * ロケーション名から、経度・緯度取得
     * 
     * @param String $queryLocation
     * @return Array $keidoIdo
     */
    public function getLocateFromLocationNm($queryLocation)
    {
        
        $lineDatas = self::getLineData();
        $keidoIdo = array();
        
        foreach ($lineDatas as $lineData){
            foreach($lineData["locations"] as $location) {
                if ($location["locationNm"] == $queryLocation){
                    $keidoIdo = array($location["keido"], $location["ido"]);
                }
            }
        }
                
        return $keidoIdo;
    }  
    
    /**
     * ロケーション名から経路取得
     * 
     * @param string $queryLocation
     * @return Array
     */
    public function getLinesFromLocation($queryLocation)
    {
        $lines = array();
        $lineDatas = self::getLineData();
        
        foreach ($lineDatas as $lineData){
            foreach($lineData["locations"] as $location) {
                if ($location["locationNm"] == $queryLocation){
                    $lines[] = $lineData["Name"];
                }
            }
        }
                
        return $lines;
    }
    
    /**
     * 経路名からその1経路上のロケーション名取得
     * 
     * @param string $lineNm
     * @return Array
     */
    public function getLocationFromLineNm($lineNm)
    {
        $linesLocationNm = array();
        $lineDatas = self::getLineData();
        
        foreach($lineDatas as $lineDataCount => $lineData){
            if ($lineData["Name"] == $lineNm){
                $linesLocation = array_values($lineData["locations"]);
                foreach($linesLocation as $location) {
                     $linesLocationNm[] = $location["locationNm"]; 
                }
            }
        }
        
        return $linesLocationNm;
    }
     
    /**
     * ロケーション名から１経路内のロケーション名取得
     * 
     * @param String $queryLocation
     * @return Array
     */
    public function getLocationsByOneLine($queryLocation, $returnMethod = null)
    {
        $locates = array();
        $lines = array();
        
        $lines = self::getLinesFromLocation($queryLocation);
        foreach($lines as $line){
            $locates[] = self::getLocationFromLineNm($line);
        }
                
        return $locates;
    }
}
