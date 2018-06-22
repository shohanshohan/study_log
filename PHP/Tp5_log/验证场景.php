<?php
//如何在验证字段值的唯一性时区分添加数据和编辑数据时的验证，编辑数据时不能排除自己，会一直验证不通过，除非全改了。
//这样就要用到验证场景了，在编辑时要重写验证规则，具体如下：
namespace app\admin\validate;
use think\Validate;

class Modules extends Validate
{
    // 验证规则
    protected $rule = [
        'name'  => "require|alphaDash|unique:modules,name,,id",
        'title' => 'require|chs|unique:modules,title,,id|length:2,30'
    ];

    protected $message = [
        'name.require'  => '模块名称不能为空！',
        'title.require'  => '模块标题不能为空！',
        'name.unique'  => '模块名称已存在！',
        'title.unique'  => '模块标题不能重复！',
        'name.chsAlphaNum'  => '名称格式不正确！',
        'title.chsAlphaNum'  => '标题格式不正确！',
    ];

    protected $scene=[
        'add' => ['name','title'],
        'edit' => [
            'name'=>"require|alphaDash|unique:modules,name^id", // 注意这里的name^id 排除正在编辑的主键匹配
            'title'=>"require|chs|unique:modules,title^id|length:2,30"
        ],
    ];

}

/*
在控制器中区分验证场景：
    public function edit($id=0){
        $title = $id ? "编辑":"新增";
        if(IS_POST){
            // 提交数据
            $data = $this->request->param();
            //验证数据
            if($id){
                $result = $this->validate($data,'Modules.edit');
            }else{
                $result = $this->validate($data,'Modules.add');
            }
            if(true !== $result){
                // 验证失败 输出错误信息
                $this->error($result);exit;
            } 
       ........
*/

