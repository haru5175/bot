<?php

/**
* yahooApi
*
* Yahoo!のAPI利用クラス
* API詳細は http://developer.yahoo.co.jp/ を参照
*
*/
class yahooApi
{

	private $logger = null;

	/**
	* __construct
	*
	* コンストラクタ
	*
	* @param object $logger ログオブジェクト
	*/
	public function __construct($logger = null)
	{
		try {

			// ログオブジェクトを設定
			$this->logger = $logger;

		} catch ( Exception $e ) {
			throw new Exception('classTwitterUtil::__construct() エラー' . $e->getMessage());
		}
	}

	/**
	* morphologicalAnalysis
	*
	* 形態素解析
	*
	* @param string $sentence 形態素解析を行う文字列
	* @return array $arrResult 形態素解析の結果
	*/
	function morphologicalAnalysis ($sentence)
	 {
		try {
				$this->logger->writeLog('info','classTwitterUtil::morphologicalAnalysis() 形態素解析開始',$sentence);

				// 文字コード変換
				$sentence = mb_convert_encoding($sentence, 'utf-8', 'auto');

				// APIへのリクエスト作成
				$url = "http://jlp.yahooapis.jp/MAService/V1/parse?appid=". API_ID . "&results=ma";
				$url .= "&sentence=".urlencode($sentence);
				$xml  = simplexml_load_file($url);
				// 形態素解析の結果を配列に格納
				foreach ($xml->ma_result->word_list->word as $cur){

					// 特殊は要らない
					if ($cur->pos == '特殊') continue;

					$arrResult[] = array('surface' => $this->escapestring($cur->surface) // 表記
										,'reading' => $this->escapestring($cur->reading) // 読み
										,'pos' => $this->escapestring($cur->pos) // 品詞
										,'baseform' => $this->escapestring($cur->baseform) // 基本形
										,'feature' => $this->escapestring($cur->feature) // 全情報
										);
				}

				$this->logger->writeLog('info','classTwitterUtil::morphologicalAnalysis() 形態素解析終了',$arrResult);

				return $arrResult;

		} catch ( Exception $e) {
			throw new Exception('classTwitterUtil::morphologicalAnalysis() エラー' . $e->getMessage());
		}
	}

	/**
	* escapestring
	*
	* 特殊文字を HTML エンティティに変換する
	*
	* @param string $str 文字列
	* @return string $str エスケープ後の文字列
	*/
	private function escapestring($str) {
		$str = htmlspecialchars($str, ENT_QUOTES);
    	return $str;
	}


}

?>
