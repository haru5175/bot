<?php

require_once 'Lib_Utility.php';

/**
* bot
*
* botで使うユーティリティクラスの作成
*
*/
class bot extends Lib_Utility
{

	private $_logger = null;

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

			// 親クラスにログインスタンス渡す
			parent::__construct($logger);

			// ログオブジェクトを設定
			$this->_logger = $logger;

		} catch ( Exception $e ) {
			throw new Exception('botUtil::__construct() エラー' . $e->getMessage());
		}
	}

	/**
	* getMessage
	*
	* メッセージ取得
	*
	* @param array $arrWords 形態素解析結果の配列
	* @param string $name リプライを返すユーザの名前
	* @return string $message 送信するメッセージ
	*/
	function getMessage ($arrWords, $name = '')
	 {
		try {
				$this->_logger->writeLog('info','botUtil::getMessage() メッセージ取得開始',$arrWords,$name);

				$arrMessage = array('たま' => '%sもう一回言って、たたたたまぁ～～～～！'
									//,'いく' => 'あぁすごい！イクんだ？イッちゃうんだ？？'
									,'まんかい' => '%sマンかいいんだ？ちゃんと手入れしてる？'
									,'いき' => '%sイキそう？もうイキそうなの？？すごいすごい！！'
									,'したい' => '%sしたいんだ？ふ～んそうなんだ、溜まってるの？？'
									,'やりたい' => '%sやりたいんだ？ふ～んそうなんだ、溜まってるの？？'
									,'やった' => 'そうなんだ、ヤッチャッタんだ？エッチだねえ%s'
									,'あな' => 'ああああなぁ～～～～！すっごい、%sもう一回言って！！'
									,'るーにー' => 'クンニー好きなんだ？%sすごいなあエッチだなあ。'
									,'ふぁんぺるしー' => '%sすっごい、マンペロシー！！好きなんだ？'
									,'くりしー' => 'クリトリー？敏感なところだよね、あぁ！！%s'
//									,'チチャ' => 'チチャリートってお豆のことだよね？アレのこと言ってる？%sあぁすごい！！'
//									,'ちちゃりーと' => 'チチャリートってお豆のことだよね？、アレのこと言ってるあぁすごい！！%s'
									//,'きた' => 'きちゃった？きちゃったの？？I\'m coming 英語でイクって意味だよね？すごい！'
									//,'くる' => 'くるの？本当に？？ I\'m coming 英語でイクって意味だよね？すごい！'
									,'しゅんすけ' => '%sあぁ！ちんこ頭'
									,'なかむら' => '%sあぁ！ちんこ頭'
									,'なかむらしゅんすけ' => '%sあぁ！ちんこ頭'
									,'あざーる' => '%sあ、あ、アザーメン、あぁっす！！'
									,'なに' => 'すぅ～～！あぁすごい%sもう一回言って、ナニをナニしてくれるのかな？'
									,'また' => '%s誰にでも股開いちゃうんだ？すごいなあ'
									,'ちんちん' => 'あぁ～～す！すごい%sちんちんってもう一回言って'
									,'まんだ' => 'いますっごいこと言ったよね%s？まんだのマンがいわゆるマンなんだ？'
									,'ほわいとでー' => 'あっ！ああ！！%sにチョコボーイのホワイトチョコが（びしゃびしゃ！！）'
									,'かわさき' => 'すっごい！！%s皮の先っぽどうしてくれるの？教えて？？'
									,'つゆだく' => 'あぁ！！すっごいおしるだくだくになっちゃったんだ？'
									);

				// メッセージ初期化
				$message = '';
				// 形態素解析した単語数分ループ
				foreach ($arrWords as $word) {
					// $arrMessageの添字に単語が一致するか確認
					if (isset($arrMessage[$word['reading']])){
						// 一致すれば添字の変数に入っているつぶやきを取得
						$message = $arrMessage[$word['reading']];
						// ユーザ名をつぶやきに含めてあげる
						$message = sprintf($message,"$name" . 'ちゃん、');
						$this->_logger->writeLog('info','botUtil::getMessage() 生成したメッセージ',$message);
					}
				}

				$this->_logger->writeLog('info','botUtil::getMessage() メッセージ取得終了',$message);

				// 取得したメッセージを返却
				return $message;

		} catch ( Exception $e) {
			throw new Exception('classTwitterUtil::morphologicalAnalysis() エラー' . $e->getMessage());
		}
	}

	/**
	* tweetAlready
	*
	* ツイートの重複を確認
	*
	* @param string $screenName 文字列
	* @return array $arrStatuses ツイートとその発言者の配列
	*/
	public function tweetAlready($screenName, $arrStatuses)
	{
		try {

			$this->_logger->writeLog('info','botUtil::tweetAlready() 開始', array($screenName, $arrStatuses));

			if (!is_array($arrStatuses)){
				$this->_logger->writeLog('err','botUtil::tweetAlready() $arrStatusesが配列ではない');
				return false;
			}

			// 戻り値初期設定
			$response = false;

			// メッセージを指定のユーザがつぶやいてるか確認
			foreach ($arrStatuses as $status){
				if ($screenName == $status['screen_name']) $response = true;
			}

			$this->_logger->writeLog('info','botUtil::tweetAlready() 終了', $response);

			// 結果を返却
			return $response;

		} catch ( Exception $e ) {
			throw new Exception('botUtil::tweetAlready() エラー' . $e->getMessage());
		}
	}

	/**
	* getComplexMessage
	*
	* メッセージ取得
	*
	* @param array $arrWords 形態素解析結果の配列
	* @param string $name リプライを返すユーザの名前
	* @return string $message 送信するメッセージ
	*/
/*
	function getComplexMessage ($arrWords, $name = '')
	 {
		try {
				$this->_logger->writeLog('info','botUtil::getComplexMessage() メッセージ取得開始',$arrWords,$name);

				$arrMessage = array(
									'ばなな' => array('たべる' => '%sちゃん、イタイイタイ！！', 'すき' => '%sちゃんバナナ好きなんだ？どうやって食べるの？見せて')
									'そーせーじ' => array('たべる' => '%sちゃんソーセージ食べるの好きなんだ？', 'すき' => 'ソーセージ好きなんて%sちゃんエッチだな')
									);

				// メッセージ初期化
				$message = '';
				// 形態素解析した単語数分ループ
				for ($i = 0 ; $i <= count($arrWords) ; $i++) {
					// $arrMessageの添字に単語が一致するか確認
					if (isset($arrMessage[$arrWords[$i]['reading']])){

						foreach ( $arrWords as $word ) {
							if (isset($arrMessage[$arrMessage[$arrWords[$i]['reading']][$word['reading']])){
								// 一致すれば添字の変数に入っているつぶやきを取得
								$message = $arrMessage[$arrMessage[$arrWords[$i]['reading']][$word['reading']];
								// ユーザ名をつぶやきに含めてあげる
								$message = sprintf($message,$name);
								$this->_logger->writeLog('info','botUtil::getMessage() 生成したメッセージ',$message);
							}
						}



					}
				}

				$this->_logger->writeLog('info','botUtil::getMessage() メッセージ取得終了',$message);

				// 取得したメッセージを返却
				return $message;

		} catch ( Exception $e) {
			throw new Exception('classTwitterUtil::morphologicalAnalysis() エラー' . $e->getMessage());
		}
	}
*/



}

?>
