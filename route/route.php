<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('/', 'index/index/index');

Route::get('test', 'index/admin/test');

Route::group('android', function() {
	Route::post('login', 'index/admin/login');
	Route::post('register', 'index/admin/register');
	Route::post('reset_passwd', 'index/admin/reset_passwd');
	Route::post('request_message', 'index/admin/request_message');
	Route::post('register_request_message', 'index/admin/register_request_message');
	Route::post('reset_passwd_request_message', 'index/admin/reset_passwd_request_message');
	Route::post('upload_score', 'index/admin/upload_score');
	Route::post('modify_user', 'index/admin/modify_user');
});

return [

];
