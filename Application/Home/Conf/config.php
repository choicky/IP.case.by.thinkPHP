<?php
return array(
	//'配置项'=>'配置值'
	
	    /* URL设置 */
    'URL_MODEL'             =>  2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式
	
	    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'thinkPHP',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'tp_',    // 数据库表前缀
	
	// 显示页面Trace信息
	'SHOW_PAGE_TRACE' =>true, 
	
	//自定义分页显示时每页显示的最大条目数
	'RECORDS_PER_PAGE'			=>	'15',
	
	//自定义下拉选框的最大行数
	'ROWS_PER_SELECT'			=>	'10',	
	

);