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
 * @package    Diggin_Felica
 * @subpackage Adapter_Sfcpeep
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

require_once 'Diggin/Felica/Adapter/Exception.php';

class Diggin_Felica_Adapter_Sfcpeep
{
    public $felicaIdm;//Felica製造番号
    public $sfcpeepArray; //配列格納後のsfcpeepデータ    
    protected $_to_encoding;
    protected $_sfcpeepPath;
    protected $_output; //execのoutput
    protected $_returnVar;
    protected $_sfcpeepRawData;
    protected $_sfcpeepExecData;

    /**
     * Sfcpeep実行後、取得出来たか
     * @return boolean
     */
    public function isSuccess()
    {
        if (isset($this->_returnVar) && ($this->_returnVar === 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Constructor 
     * file_get_contens("your.txt") or :タブ区切りのデータをセット 
     * 
     * @parma string $to_encoding
     * @param string $felicaIdm
     * @param string $sfcpeepRawData
     */
    public function __construct($to_encoding = 'utf-8', $felicaIdm = null, $sfcpeepRawData = null)
    {
        $this->_to_encoding = $to_encoding;
        $this->felicaIdm = $felicaIdm;
        $this->_sfcpeepRawData = $sfcpeepRawData;
    }

    /*
     * Sfcpeepへのパス設定
     * @return Diggin_Felica_Sfcpeep
     */
    public function setSfcpeepPath($path)
    {
        $this->_sfcpeepPath = $path;
        
        return $this;
    }
    
    /**
     * exec関数を用いてsfcpeep.exeを実行
     * 
     * @param string $sfcpeepDir
     * @param string $param
     * @see   Sfcpeep Readme.txt
     * @return Diggin_Felica_Sfcpeep::_output
     */
    public function execSfcpeep($parameter = 'h')
    {
        try {
            exec($this->_sfcpeepPath.' -'.$parameter,
                 $output,
                 $returnVar);
        } catch (Exception $e){
            die($e->getMessage());
        }
        
        $this->_output = $output;
        $this->_returnVar = $returnVar;
        
        return $this->_output;
    }
    
    /**
     * Get Felica IDm
     * 
     * @return string $this->_felicaIdm
     */
    public function getFelicaIdm()
    {
        if(!isset($this->felicaIdm)){
            $output = self::execSfcpeep('i');
            if (!self::isSuccess()) {
                throw new Diggin_Felica_Adapter_Exception ("Can not Read Felica") ;
            }
            $this->felicaIdm = substr(array_shift($output), 4);
        }
        
        return $this->felicaIdm;
    }
    
    /**
     * sfcpeep.exe実行後の結果を配列取得
     *
     * @return Diggin_Felica_Sfcpeep
     */
    public function getSfcPeep() 
    {
        if (isset($this->_sfcpeepRawData)) {
            self::_parseSfcPeepByText();
        } else {
            self::getFelicaIdm();
            if(!self::isSuccess()){
                throw new Diggin_Felica_Adapter_Exception(
                "Can not get Felica Data ".array_shift($this->_output));
            }
            $this->_sfcpeepExecData = self::execSfcpeep();
            if(!self::isSuccess()){
                throw new Diggin_Felica_Adapter_Exception(
                "Can not get Felica Data ".array_shift($this->_output));
            }
            
            self::_parseSfcPeepByExec();

        } 

       return $this;
    }
    
    
    /**
     * sfcpeepデータ一行分を配列にセットします。
     * 
     * @param  一行データ
     * @param  行番号
     * @return Diggin_Felica_Sfcpeep
     */
    protected function _parseSfcPeepLine($line, $lineNum)
    {
        if($this->_to_encoding){
            $line = mb_convert_encoding($line, $this->_to_encoding, 'SJIS');
        }
        $item = explode("\t", $line);
        $this->sfcpeepArray[$lineNum]["terminalHt"] = substr($item[0], 0, 4);
        $this->sfcpeepArray[$lineNum]["terminalCode"] = substr($item[0], 5, 2);
        $this->sfcpeepArray[$lineNum]["process"] = $item[1];
        $this->sfcpeepArray[$lineNum]["date"] = $item[2];
        $this->sfcpeepArray[$lineNum]["inLineCode"] = $item[3];
        $this->sfcpeepArray[$lineNum]["inStationCode"] = $item[4]; //入駅順コード
        $this->sfcpeepArray[$lineNum]["inCompanyName"] = $item[5]; //入会社
        $this->sfcpeepArray[$lineNum]["inStationName"] = $item[6]; //入駅名
        $this->sfcpeepArray[$lineNum]["outLineCode"] = $item[7]; //出線区コード
        $this->sfcpeepArray[$lineNum]["outStationCode"] = $item[8]; //出駅順コード
        $this->sfcpeepArray[$lineNum]["outCompanyName"] = $item[9]; //出会社
        $this->sfcpeepArray[$lineNum]["outStationName"] = $item[10]; //出駅名
        $this->sfcpeepArray[$lineNum]["balance"] = $item[11]; //残高
        $this->sfcpeepArray[$lineNum]["historyNum"] = $item[12]; //履歴連番

        return $this;
    }
    
    /**
     * sfcpeepのデータから、[行数][項目]の配列にセットします。
     * 
     * @return Diggin_Felica_Sfcpeep
     */
    protected function _parseSfcPeepByText()
    {

        $rawdata = $this->_sfcpeepRawData;
        foreach(explode("\n", $rawdata) as $lineNum => $line){
           if (!empty($line)){
              self::_parseSfcPeepLine($line, $lineNum);
           }
        }
    }

    /**
     * sfcpeepのデータから、[行数][項目]の配列にセットします。
     * 
     * @return Diggin_Felica_Sfcpeep
     */
    protected function _parseSfcPeepByExec()
    {
       foreach($this->_sfcpeepExecData as $lineNum => $line){
           self::_parseSfcPeepLine($line, $lineNum);
       }
    }
        
    /**
     * 入駅名, 出駅名をdistinctで返す。引数がnullだと店名なども返すので注意
     *
     * @param string $terminalCode //端末種コードの下2桁(16=改札、8=券売機)
     * @return array
     */
    public function getDistinctName($terminalCode = null)
    {
        if(!isset($this->sfcpeepArray)) {
           self::getSfcPeep(); 
        }

        $result = array();
        foreach($this->sfcpeepArray as $line){
           if ($line["inStationName"]) {
              if(!empty($terminalCode) AND ($terminalCode == $line["terminalCode"])){
                 array_push($result,$line["inStationName"]);
              } else if (empty($terminalCode)) {
                 array_push($result,$line["inStationName"]);
              }
           }
           if ($line["outStationName"]) {
              if(!empty($terminalCode) AND ($terminalCode == $line["terminalCode"])){
                 array_push($result,$line["outStationName"]);
              } else if (empty($terminalCode)) {
                 array_push($result,$line["outStationName"]);
              }
           }
        }
        
        return array_unique($result);
    }
    
}
