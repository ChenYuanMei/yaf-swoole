<?php
/**
 * @name IndexController
 * @author {&$AUTHOR&}
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends BaseController {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/{&$APP_NAME&}/index/index/index/name/{&$AUTHOR&} 的时候, 你就会发现不同
     */
	public function indexAction() {
        $this->getView()->display('login.phtml');
        return false;
	}

	/*
	 * 登录方法
	 */
	/*public function loginAction(){
	    try{
            //非法请求
            if($this->getRequest()->isGet()) throw new Exception('非法请求');

            //$username =
        }catch (Exception $exception){
	        $this->getView()->render('error/error/phtml');
        }


        return false;
    }*/
}
