<?php
/**
 * ログ出力クラス
 */

require_once 'Log.php';

class Logging
{
	public $log; // ログオブジェクト
	private $_errlog; // エラーログオブジェクト
	private $_name;

	/**
	 * __construct コンストラクタ
	 *
	 */
	public function __construct($name, $logPath, $loglevel = PEAR_LOG_INFO)
	{
		try {
			// ログオブジェクト生成
			$logfile = $logPath . str_replace(".php", ".log", $name);

			if (!file_exists($logfile)) touch( $logfile );

			$this->log =& Log::singleton('filerolling', $logfile, 'php', array('datePattern'=>'ymd'), $loglevel);

			// エラーログオブジェクト生成
			$errlogfile = $logfile = $logPath . "err_" . str_replace(".php", ".log", $name);
			$this->_errlog =& Log::singleton('filerolling', $errlogfile, 'php', array('datePattern'=>'ymd'), $loglevel);

			$this->_name = str_replace(".php", "", $name);

		} catch (Exception $e) {
			throw new Exception("Logging::__construct : " . $e->getMessage());
		}
	}


	/**
	 * __destruct() デストラクタ
	 *
	 */
	function __destruct()
	{
		try {
			// ログクローズ
			if ( is_object($this->log) ) {
				$this->log->close();
			}
			if ( is_object($this->_errlog) ) {
				$this->_errlog->close();
			}
		} catch (Exception $e) {
			throw new Exception("Logging::__destruct : " . $e->getMessage());
		}
	}


	/**
	 * writeLog() ログ出力
	 *
	 * @param string $level ログレベル (debug|info|notice|warning|err|crit|alert|emerg)
	 * @param string $message ログメッセージ
     * @param string[] $params パラメータ
	 */
	public function writeLog($level, $message, $params = null)
	{
		try {
			// $params対応
			$delimiter = "\t";
			if ( is_array($params) || is_object($params) ) {
				$message .= " " . print_r($params, true);
			} else {
				$message .= " " . $delimiter . $params;
			} // end of if ( is_array($params) )

			// ログ出力
			switch (strtolower($level)) {
			case "debug":
				$this->log->debug($message);
				break;
			case "info":
				$this->log->info($message);
				break;
			case "notice":
				$this->log->notice($message);
				break;
			case "warning":
				$this->log->warning($message);
				break;
			case "err":
				$this->log->err($message);
				$this->_errlog->err($this->_name . " " . $message);
				break;
			case "crit":
				$this->log->crit($message);
				$this->_errlog->crit($this->_name . " " . $message);
				break;
			case "alert":
				$this->log->alert($message);
				$this->_errlog->alert($this->_name . " " . $message);
				break;
			case "emerg":
				$this->log->emerg($message);
				$this->_errlog->emerg($this->_name . " " . $message);
				break;
			default:
				// なにもしない
				break;
			} // end of switch (strtolower($level))
		} catch (Exception $e) {
			throw new Exception("Logging::writeLog : " . $e->getMessage());
		}
	}
}


?>