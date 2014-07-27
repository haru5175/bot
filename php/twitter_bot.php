<?

/* ------------------------------------------------------ */
/* libraries */
require_once "twitter.php";
require_once "class/yahooApi.php";
require_once "class/bot.php";
require_once "config.php";

// ログオブジェクト準備
require_once "class/logging.php";
define('LOG_LEVEL', 7); // DEBUG
$logger = new Logging(basename(__FILE__), '/var/logs/', LOG_LEVEL);

$logger->writeLog('info', '開始');

/* ------------------------------------------------------ */
/* constants */
//

// テスト

define('RETRY_COUNT', 3);
define('SLEEP_TIME', 5);
define('QUOTETWEET_DENOMINATOR', 10); // 何回に1回QT生成を試みるか
define('FLAG_FORCE', true);	// 重複を無視して取得


/* ------------------------------------------------------ */
/* variables */
//
$post_status = false;

/* ------------------------------------------------------ */
/* functions */

/* ------------------------------------------------------ */
/* main */
try {

	// ツイッター用オブジェクト生成
	$twitter = new twitter($logger);

	// botユーティリティクラスオブジェクト生成
	$butil = new bot($logger);

	// アカウント情報を設定
	$twitter->setAccontInfo(TWITTER_USER_ID
						, TWITTER_CONSUMER_KEY
						, TWITTER_CONSUMER_SECRET
						, TWITTER_OAUTH_TOKEN
						, TWITTER_OAUTH_TOKEN_SECRET
						);

	// フォロワーの一覧を取得
	$arrfriends = $twitter->getFollower();
	$logger->writeLog('info', 'フォロワ',$arrfriends);

	$yahooApi = new yahooApi($logger);

	//テスト用に自分だけにする
	//$arrfriends = array('screen_name' => 'haru141', 'name' => 'はるる', 'following' => 1);

	foreach ($arrfriends as $friend) {
		// 友達のツイートを最新1件取得(リプライ除く、RT除く)
		$arrTweets = $twitter->getUserTimeLine($friend['screen_name'], 1, false, true);

		// まだこっちからフォローしてなかったらフォロー返ししてあげる
		if ($friend['following'] != 1) {
			$logger->writeLog('info', 'フォローしてあげる',$friend['screen_name']);
			// 結果が失敗でもあんまり重要では無いので先に進む
			$res = $twitter->friendshipsCreate($friend['screen_name']);
		}

		foreach ($arrTweets as $tweet) {
			// 形態素解析を行う
			$arrMa = $yahooApi->morphologicalAnalysis($tweet['text']);

			// 複雑なメッセージ取得


			// キーワードが含まれているかチェックして含まれていたらメッセージを返す
			$reply = $butil->getMessage($arrMa,$friend['name']);

			// メッセージが取得できたら
			if ($reply) {

				$logger->writeLog('info', 'ranking_bot リプライ開始', $reply);
				$status = $reply . 'RT @' . $friend['screen_name'] . ':' . $tweet['text'];
				//長さ丸め込み
				$status = $butil->mbStrlen($status,0,140);
				$logger->writeLog('info', 'ranking_bot 丸め込み結果', $status);

				// 既につぶやいているかチェック
				$arrstatus = $twitter->searchTweets($status);
				if (!$butil->tweetAlready(TWITTER_USER_ID,$arrstatus)){
					$logger->writeLog('info', 'ranking_bot 重複なしなのでつぶやく', $status);
					$tweet = array('in_reply_to_status_id' => $tweet['id_str']
									,'status' => $status);
					$logger->writeLog('err', 'ranking_bot つぶやく内容', $tweet);
					// つぶやく
					$twitter->twitterPost($tweet,5,0);
					break;
				}
			}

		}
	}

	$logger->writeLog('info', '終了');

}catch (Exception $e) {
	$logger->writeLog('err', 'ranking_bot メイン エラー', $e->getMessage());
	throw new Exception('ranking_bot メイン エラー' . $e->getMessage());
}

?>
