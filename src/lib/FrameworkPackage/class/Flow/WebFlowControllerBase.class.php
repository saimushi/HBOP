<?php

class WebFlowControllerBase extends WebControllerBase {

	public $section = '';
	public $action = '';

	protected function _reverseRewriteURL(){
		$action = $this->action;
		if(isset($_SERVER['ReverseRewriteRule'])){
			$reverseRules = explode(' ', $_SERVER['ReverseRewriteRule']);
			if(count($reverseRules) == 2){
				$reverseAction = preg_replace('/' . $reverseRules[0] . '/', $reverseRules[1], $action);
				if(NULL !== $reverseAction && strlen($reverseAction) > 0){
					$action = $reverseAction;
				}
			}
		}
		return $action;
	}

	protected function _initWebFlow(){
		// Flowパラムの初期化
		if(NULL === Flow::$params){
			Flow::$params = array();
		}

		// GETパラメータの各種自動処理
		if(isset($_GET) && count($_GET) > 0){
			Flow::$params['get'] = array();
			foreach($_GET as $key => $val){
				// Flow用としてPOSTパラメータをしまっておく
				Flow::$params['get'][$key] = $val;
				if(NULL === Flow::$params['view']){
					Flow::$params['view'] = array();
				}
				debug('[frowparamsection=' . $key . ']');
				Flow::$params['view'][] = array('[frowparamsection=' . $key . ']' => array(HtmlViewAssignor::PART_REPLACE_NODE_KEY => array('_flow_'.$key.'_' => $val)));
				Flow::$params['view'][] = array('[frowparamsection=' . $key . ']' => array(HtmlViewAssignor::PART_REPLACE_ATTR_KEY => array('href' => array('_flow_'.$key.'_' => $val), 'value' => array('_flow_'.$key.'_' => $val), 'src' => array('_flow_'.$key.'_' => $val))));
			}
		}

		// flowFormでPOSTされていたら自動的にバリデートする
		if(isset($_POST['flowpostformsection']) && $_GET['_c_'] === $_POST['flowpostformsection'] && count($_POST) > 0){
			Flow::$params['post'] = array();
			foreach($_POST as $key => $val){
				// Flow用としてPOSTパラメータをしまっておく
				Flow::$params['post'][$key] = $val;
				// backflowがポストされてきたらそれをviewのformに自動APPEND
				if(0 !== strpos($key, 'flowpostformsection-backflow-section')){
					Flow::$params['view']['form[flowpostformsection]'] = array(HtmlViewAssignor::APPEND_NODE_KEY => '<input type="hidden" name="flowpostformsection-backflow-section" value="' . $val . '"/>');
				}
				// パスワード以外はREPLACE ATTRIBUTEを自動でして上げる
				if(0 !== strpos($key, 'pass')){
					if(NULL === Flow::$params['view']){
						Flow::$params['view'] = array();
					}
					Flow::$params['view'][] = array('input[name=' . $key . ']' => array(HtmlViewAssignor::REPLACE_ATTR_KEY => array('value'=>$val)));
				}
				// auto validate
				debug('$key='.$key);
				debug('$val='.$val);
				try{
					if(FALSE !== strpos($key, 'mail')){
						// メールアドレスのオートバリデート
						Validations::isEmail($val);
					}
					if(FALSE !== strpos($key, '_must') && 0 === strlen($val)){
						debug('must exception');
						// 必須パラメータの存在チェック
						throw new Exception();
					}
				}
				catch (Exception $Exception){
					// 最後のエラーメッセージを取っておく
					$validateError = TRUE;
					if(NULL === Flow::$params['view']){
						Flow::$params['view'] = array();
					}
					Flow::$params['view'][] = array('div[flowpostformsectionerror=' . $_POST['flowpostformsection'] . ']' => 'メールアドレスの形式が違います');
				}
			}
			if(isset($validateError)){
				// オートバリデートでエラー
				debug('$validateError');
				return FALSE;
			}
		}

		// Backflowの初期化
		if(NULL === Flow::$params['backflow']){
			Flow::$params['backflow'] = array();
		}

		// 一つ前の画面のbackflowをflowpostformsectionに自動で挿入
		if(count(Flow::$params['backflow']) > 0){
			$backFrowID = Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['target'] . '/' . Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['section'];
			if('' === Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['target']){
				$backFrowID = $this->section;
			}
			else {
				$backFrowID = str_replace('//', '/', $backFrowID);
			}
			// Viewの初期化
			if(NULL === Flow::$params['view']){
				Flow::$params['view'] = array();
			}
			Flow::$params['view']['form[flowpostformsection]'] = array(HtmlViewAssignor::APPEND_NODE_KEY => '<input type="hidden" name="flowpostformsection-backflow-section" value="' . $backFrowID . '"/>');
		}

		// 現在実行中のFlowをBackflowとして登録しておく
		Flow::$params['backflow'][] = array('section' => $this->section, 'target' => $this->target);
		debug(Flow::$params['backflow']);

		// flowpostformsectionに現在の画面をBackFlowとして登録する
		if(NULL === Flow::$params['view'] || !isset(Flow::$params['view']['form[flowpostformsection]'])){
			$backFrowID = Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['target'] . '/' . Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['section'];
			if('' === Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['target']){
				$backFrowID = Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['section'];
			}
			else {
				$backFrowID = str_replace('//', '/', $backFrowID);
			}
			Flow::$params['view']['form[flowpostformsection]'] = array(HtmlViewAssignor::APPEND_NODE_KEY => '<input type="hidden" name="flowpostformsection-backflow-section" value="' . $backFrowID . '"/>');
		}

		return TRUE;
	}
}

?>