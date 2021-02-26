<?php
/**
 * WordPress基础配置文件。
 *
 * 这个文件被安装程序用于自动生成wp-config.php配置文件，
 * 您可以不使用网站，您需要手动复制这个文件，
 * 并重命名为“wp-config.php”，然后填入相关信息。
 *
 * 本文件包含以下配置选项：
 *
 * * MySQL设置
 * * 密钥
 * * 数据库表名前缀
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/zh-cn:%E7%BC%96%E8%BE%91_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL 设置 - 具体信息来自您正在使用的主机 ** //
/** WordPress数据库的名称 */
define('DB_NAME', 'goodone_db');

/** MySQL数据库用户名 */
define('DB_USER', 'magento2');

/** MySQL数据库密码 */
define('DB_PASSWORD', 'magento2');

/** MySQL主机 */
define('DB_HOST', 'localhost');

/** 创建数据表时默认的文字编码 */
define('DB_CHARSET', 'utf8');

/** 数据库整理类型。如不确定请勿更改 */
define('DB_COLLATE', '');

/**#@+
 * 身份认证密钥与盐。
 *
 * 修改为任意独一无二的字串！
 * 或者直接访问{@link https://api.wordpress.org/secret-key/1.1/salt/
 * WordPress.org密钥生成服务}
 * 任何修改都会导致所有cookies失效，所有用户将必须重新登录。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '{ qunJ4S],oc74j=uOmU6gv._O>}JjK<x@f|+;cJMR*%SidI_h*W*0s$aY$6.nA4');
define('SECURE_AUTH_KEY',  'Ei7|5ib$ZiN=KH4FRfh78/5relyA X14_A.CQK~yxeXVt7xULA4<}OJ3_Fr!G18o');
define('LOGGED_IN_KEY',    '/=`TmnP4nl6]:ZQqfu1acLeclU~79b*>C98&L@dXFAn{q*7 eGey%-+5%J4{<d_y');
define('NONCE_KEY',        'Rh~cF;$3|!ck`ALwH#i&G_- dC4M|49Ol`-BN|E>}19ON:1._Ykr@N;U7NnzR9]w');
define('AUTH_SALT',        ']]%^I zwx#P5D#*E:2;+~>^tG?DSF)l]-=%vW9m0_{ER)W/I7$R^+E3X2q#%f]]P');
define('SECURE_AUTH_SALT', 'Ke>eiD=Se*Cpl@u^oif90Wy-/7lH&wb0DdV=&g>GVhSBtdX{>U,s i!|8(l~xWIW');
define('LOGGED_IN_SALT',   'C-x<n:ODJwcYOs~510nc(8B9s ~MGs.;0z*-1I/b&HqWT~tv%J4V8E7 Y)|YBQ$O');
define('NONCE_SALT',       'l!raN|aS&?Il$]D Bqw>$f.NpRI}(M_ufbhP),sz=L}hIaL%/t0[bzpmJ0U=+FBs');

/**#@-*/

/**
 * WordPress数据表前缀。
 *
 * 如果您有在同一数据库内安装多个WordPress的需求，请为每个WordPress设置
 * 不同的数据表前缀。前缀名只能为数字、字母加下划线。
 */
$table_prefix  = 'ih_';

/**
 * 开发者专用：WordPress调试模式。
 *
 * 将这个值改为true，WordPress将显示所有用于开发的提示。
 * 强烈建议插件开发者在开发环境中启用WP_DEBUG。
 *
 * 要获取其他能用于调试的信息，请访问Codex。
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/**
 * zh_CN本地化设置：启用ICP备案号显示
 *
 * 可在设置→常规中修改。
 * 如需禁用，请移除或注释掉本行。
 */
define('WP_ZH_CN_ICP_NUM', true);

/* 好了！请不要再继续编辑。请保存本文件。使用愉快！ */

/** WordPress目录的绝对路径。 */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** 设置WordPress变量和包含文件。 */
require_once(ABSPATH . 'wp-settings.php');

/**
 * 设置为德国时间
 * setlocale(LC_ALL, 'de_DE');
 * date_default_timezone_set('Europe/Berlin');
 */