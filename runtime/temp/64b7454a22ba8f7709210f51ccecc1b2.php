<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:64:"D:\project\book_and_group/application/admin\view\book\index.html";i:1532586416;}*/ ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title></title>
    <meta name="renderer" content="webkit">
<title>书单与社群</title>
<link rel="stylesheet" href="/public/static/plug/element/css/element.css" />
</head>
<style type="text/css">
    .main_show{
        padding: 2% 5%;
    }
</style>
<body class="bgPadding" style="width: auto">
<section id="main" class="main_show">
    <el-menu class="el-menu-demo" mode="horizontal">
        <el-menu-item index="1"><a href="<?php echo url('Index/index'); ?>">主题管理</a></el-menu-item>
        <el-menu-item index="2" default-active><a href="javascript:;">书本管理</a></el-menu-item>
    </el-menu>
    <br>
    <div class="addClass">
        <el-row :gutter="20">
            
            <el-col :span="18">
                <el-input class="inline-input" style="width: 210px" v-model="keyword" placeholder="关键词"></el-input>
                <el-button type="primary" @click="searchKeyword" class="fs16 padcut">搜索</el-button>
            </el-col>
            <el-col :span="6" class="tar">
                <el-button type="primary" class="btn fs16 padcut" @click.native="addMenu('form')" v-cloak>添加书本</el-button>
            </el-col>
        </el-row>



        <!-- Form start -->
        <el-dialog title="添加书本" width="60%" :visible.sync="dialogFormVisible" @close="clearFrom" v-cloak>
            <el-form :model="form" :rules="rules" ref="form" prop="form">
                <el-form-item label="名称" label-width="80px" prop="title">
                    <el-input v-model="form.title" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="作者名称" label-width="80px" prop="author">
                    <el-input v-model="form.author" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="内容简介" label-width="80px" prop="remark">
                    <div id="editor"></div>
                </el-form-item>
                <el-form-item label="选择主题" label-width="80px" prop="menu_id">
                    <el-select v-model="form.menu_id" placeholder="请选择">
                        <el-option v-for="item in menu_list" :key="item.id" :label="item.name" :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer flex justify">
                <el-button type="primary" @click.native="submitTo" class="fs16 padcut">提交</el-button>
            </div>
        </el-dialog>
        <!-- Form end -->
        <!-- FormEdit start -->
        <el-dialog title="编辑书本" :visible.sync="dialogFormEdit" v-cloak>
            <el-form :model="formEdit" :rules="rules" ref="formEdit" prop="formEdit">
                <el-form-item label="名称" label-width="80px" prop="title">
                    <el-input v-model="formEdit.title" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="作者名称" label-width="80px" prop="author">
                    <el-input v-model="formEdit.author" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="内容简介" label-width="80px" prop="remark">
                    <div id="edit_formedit"></div>
                </el-form-item>
                <el-form-item label="选择主题" label-width="80px" prop="menu_id">
                    <el-select v-model="formEdit.menu_id" multiple placeholder="请选择">
                        <el-option v-for="item in menu_list" :key="item.id" :label="item.name" :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="状态" label-width="80px" prop="status">
                    <template>
                        <el-radio v-model="formEdit.status" label="1">正常</el-radio>
                        <el-radio v-model="formEdit.status" label="2">禁用</el-radio>
                    </template>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer flex justify">
                <el-button type="primary" @click.native="submitEdit" class="fs16 padcut">提交</el-button>
            </div>
        </el-dialog>
        <!-- FormEdit end -->
    </div>
    <div>
        <!-- 表格Start -->
        <div class="table">
            <template>
                <el-table :data="tableData" style="width: 100%" class="tableHead">
                    <el-table-column prop="id" label="编号ID" width="80">
                    </el-table-column>
                    <el-table-column prop="title" label="名称">
                    </el-table-column>
                    <el-table-column prop="author" label="作者">
                    </el-table-column>
                    <el-table-column prop="menu_name" label="主题名称">
                    </el-table-column>
                    <el-table-column prop="create_time" :formatter="dateFormat" label="创建时间">
                    </el-table-column>
                    <el-table-column prop="update_time" :formatter="dateFormat" label="更新时间">
                    </el-table-column>
                    <el-table-column prop="status" label="状态">
                        <template scope="scope">
                            <template v-if="scope.row.status=='1'">
                                <span class="colorg">正常</span>
                            </template>
                            <template v-else>
                                <span class="colorr">禁用</span>
                            </template>
                        </template>
                    </el-table-column>
                    <el-table-column label="操作">
                        <template slot-scope="scope">
                            <el-dropdown style="margin-left: 20px">
                                <span class="el-dropdown-link" style="cursor: pointer">
                                    编辑
                                    <i class="el-icon-arrow-down el-icon--right"></i>
                                </span>
                                <el-dropdown-menu slot="dropdown" class="dropdownBox">
                                    <div class="dropdown">
                                        <el-dropdown-item @click.native.prevent="editor(scope.$index, tableData)">编辑</el-dropdown-item>
                                        <el-dropdown-item @click.native.prevent="deleteRow(scope.$index, tableData)">删除</el-dropdown-item>
                                    </div>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </template>
                    </el-table-column>
                </el-table>
            </template>
        </div>
        <!-- 表格Start -->
        <!-- 分页Start -->
        <div class="page">
            <br>
            <div align="center" class="flex align justify">
                <el-pagination @current-change="currChange" :current-page="curr" :page-size="limits" layout="prev, pager, next, jumper" :total="total" v-cloak prev-text="上一页" next-text="下一页">
                </el-pagination>
                <!-- <el-button type="primary" class="determine">确定</el-button> -->
            </div>
        </div>
        <!-- 分页end -->
    </div>
</section>
<script src="/public/static/plug/element/js/vue.js" type="text/javascript" charset="utf-8"></script>
<script src="/public/static/plug/element/js/element.js" type="text/javascript" charset="utf-8"></script>
<script src="/public/static/plug/element/js/vue-resource.min.js"></script>
<script type="application/javascript" src="/public/static/plug/wang_editor/release/wangEditor.js"></script>
<script type="text/javascript">
    var edit_form = '',
        edit_formedit = '';
    var vm = new Vue({
        el: '#main',
        data: {
            tableData: [],
            limits: 0, //默认每页数据量
            curr: 1, //当前页码
            total: 0,
            keyword: '',
            menu_list:[],
            dialogFormVisible: false, //添加弹窗
            dialogFormEdit: false, //编辑弹窗
            form: {
                title: '',
                author: '',
                remark:'',
                menu_id:'',
            },
            formEdit: {
                title: '',
                status: '1',
                author: '',
                remark:'',
                menu_id:0,
            },
            rules: {
                title: [{
                    required: true,
                    message: '请输入书本名称',
                    trigger: 'blur'
                }]
            }
        },
        mounted: function() {
            this.ajaxGetBook(1, 10);
            this.ajaxGetMenuList();
        },
        methods: {
            ajaxGetBook: function(curr, limits) {
                var param = {
                    curr: curr,
                    limits: limits,
                    keyword: this.keyword
                };
                this.$http.post('<?php echo url(); ?>', param).then(function(res) {
                    var resu = res.body;
                    this.limits = resu.data.limits;
                    this.tableData = resu.data.list;
                    this.total = resu.data.total;
                });
            },
            ajaxGetMenuList: function() {
                var param = {
                    curr: 1,
                    limits: 100
                };
                this.$http.post('<?php echo url("get_menu_list"); ?>', param).then(function(res) {
                    this.menu_list = res.body.data;
                });
            },
            currChange: function(val) {
                this.curr = val;
                this.ajaxGetBook(val, this.limits);
            },
            searchKeyword: function() {
                this.curr = 1;
                this.ajaxGetBook(1, this.limits);
            },
            //时间格式化
            dateFormat: function(row, column) {
                if (row[column.property] == 0) {
                    return '未知';
                }

                var date = new Date(row[column.property] * 1000); //获取一个时间对象  注意：如果是uinx时间戳记得乘于1000。比如php函数time()获得的时间戳就要乘于1000

                Y = date.getFullYear() + '-';
                M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
                D = date.getDate() + ' ';
                h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
                m = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
                s = (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds());
                return Y + M + D + h + m + s;
            },
            checkStatus: function(row, column) {
                var status_str;
                if (row[column.property] == 1) {
                    status_str = '正常';
                } else {
                    status_str = '禁用';
                }
                return status_str;
            },
            //添加主题页面
            addMenu: function(formName) {
                this.dialogFormVisible = true;
                var _this = this;
                setTimeout(function () {
                    edit_form = _this.show_edit('editor','');
                },300);
            },
            //添加主题
            submitTo: function() {
                var _self = this;
                _self.$refs.form.validate(function(valid) {
                    if (valid) {
                        var param = {
                            title: _self.form.title,
                            author: _self.form.author,
                            menu_id: _self.form.menu_id,
                            remark: edit_form.txt.html(),
                        };
                        _self.$http.post('<?php echo url("add"); ?>', param).then(function(res) {
                            if (res.body.code == 200) {
                                _self.$message({
                                    type: 'info',
                                    message: '添加成功'
                                });
                                _self.ajaxGetBook(_self.curr, _self.limits);
                                _self.dialogFormVisible = false;
                            } else {
                                _self.$message({
                                    type: 'info',
                                    message: res.body.msg
                                });
                                _self.dialogFormVisible = false;
                            }
                        });
                    } else {
                        return false;
                    }
                });
            },
            //删除
            deleteRow: function(index, rows) {
                var _self = this;
                var prompt = _self.$confirm('是否删除该书本?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                });
                prompt.then(function() {
                    _self.$http.post('<?php echo url("del"); ?>', {
                        book_id: rows[index].id
                    }).then(function(res) {
                        if (res.body.code == 200) {
                            _self.$message({
                                type: 'info',
                                message: '删除成功'
                            });
                            _self.ajaxGetBook(_self.curr, _self.limits);
                        }
                    });
                });
                prompt.catch(function() {
                    _self.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            //编辑页面
            editor: function(index, rows) {
                this.dialogFormEdit = true;
                this.formEdit.title = rows[index].title;
                this.formEdit.status = '' + rows[index].status;
                this.formEdit.author = rows[index].author;
                this.formEdit.menu_id = '' + rows[index].menu_id;
                //this.formEdit.remark = rows[index].remark;
                this.formEdit.editRow = index;
                var _this = this;
                setTimeout(function () {
                    edit_formedit = _this.show_edit('edit_formedit',rows[index].remark);
                },300);
            },
            //编辑内容提交
            submitEdit: function() {
                var _self = this;
                _self.$refs.formEdit.validate(function(valid) {
                    if (valid) {
                        var param = {
                            book_id: _self.formEdit.id,
                            status: _self.formEdit.status,
                            name: _self.formEdit.name,
                            author: _self.formEdit.author,
                            menu_id: _self.formEdit.menu_id,
                            remark: edit_formedit.txt.html(),
                        };
                        _self.$http.post('<?php echo url("edit"); ?>', param).then(function(res) {
                            var resu = res.body;
                            _self.$message({
                                type: 'info',
                                message: resu.msg
                            });
                            _self.dialogFormEdit = false;
                            _self.ajaxGetBook(_self.curr, _self.limits);
                        });
                    } else {
                        return false;
                    }
                });
            },
            clearFrom: function() {
                this.form = {
                    title: '',
                    author: '',
                    remark:'',
                    menu_id:'',
                };
            },
            show_edit:function(id_name,html_str) {
                var E = window.wangEditor;
                var editor = new E('#'+id_name);

                editor.customConfig.uploadVideoServer = '<?php echo url("ajax/qupload"); ?>';
                editor.customConfig.uploadImgServer = '<?php echo url("ajax/qupload"); ?>';
                editor.customConfig.uploadFileName = 'file';
                editor.customConfig.uploadImgHooks = {
                    customInsert: function (insertImg, result, editor) {
                        var url = result.data.savepath;
                        insertImg(url);
                    },
                };
                editor.customConfig.uploadVideoHooks = {
                    customInsert:function (insertVideo,result) {
                        var url = result.data.savepath;
                        insertVideo(url);
                    }
                };
                editor.create();
                if(html_str !== ""){
                    editor.txt.html(html_str);
                }

                return editor;
            }
        }
    });
</script>
</body>

</html>