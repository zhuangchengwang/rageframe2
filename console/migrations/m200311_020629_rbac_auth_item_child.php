<?php

use yii\db\Migration;

class m200311_020629_rbac_auth_item_child extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%rbac_auth_item_child}}', [
            'role_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '角色id'",
            'item_id' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权限id'",
            'name' => "varchar(64) NOT NULL DEFAULT '' COMMENT '别名'",
            'app_id' => "varchar(20) NOT NULL DEFAULT '' COMMENT '类别'",
            'is_addon' => "tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否插件'",
            'addons_name' => "varchar(100) NULL DEFAULT '' COMMENT '插件名称'",
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公用_授权角色权限表'");
        
        /* 索引设置 */
        $this->createIndex('role_id','{{%rbac_auth_item_child}}','role_id',0);
        $this->createIndex('item_id','{{%rbac_auth_item_child}}','item_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%rbac_auth_item_child}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

