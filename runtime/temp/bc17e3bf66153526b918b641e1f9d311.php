<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:65:"D:\project\book_and_group/application/admin\view\login\index.html";i:1532226650;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>书单与社群</title>
	<link rel="stylesheet" href="/public/static/plug/element/css/element.css" />
</head>
<body style="background: black">
	<div id="app">
		<el-main>	
			<el-card class="box-card" style="width:40%;margin: 10% 15%;box-shadow: 1px 1px 10px #FFF;">
				<div slot="header" class="clearfix">
					<span>书单与社群</span>
				</div>
				<div class="text item">
					<el-form ref="form" :model="form" label-width="80px">
						<el-form-item>
						  <el-input placeholder="请输入用户名" v-model="account">
						    <template slot="prepend">ACCOUNT&nbsp;</template>
						  </el-input>
						</el-form-item>
						<el-form-item>
						  <el-input placeholder="请输入密码" v-model="psw" type="password">
						    <template slot="prepend">PASSWORD</template>
						  </el-input>
						</el-form-item>
						<el-form-item>
							<el-button type="primary" @click="onSubmit">LOIGN</el-button>
						</el-form-item>	
					</el-form>
				</div>
			</el-card>
		</el-main>
	</div>
</body>
<script src="/public/static/plug/element/js/vue.js" type="text/javascript" charset="utf-8"></script>
<script src="/public/static/plug/element/js/element.js" type="text/javascript" charset="utf-8"></script>
<script src="/public/static/plug/element/js/vue-resource.min.js"></script>
<script>
	var vm = new Vue({
        el: '#app',
        data: function() {
            return {
                form: {},
                account:'',
                psw:'',
            }
        },
        mounted: function() {

        },
        methods: {
        	onSubmit:function(){
        		var param = {
        			username:this.account,
        			password:this.psw,
        		};
        		this.$http.post('<?php echo url(""); ?>',param).then(function(res){
        			var result = res.body;
        			if (result.code == 200) {
        				window.location.href = '<?php echo url("admin/Index/index"); ?>';
        			}else{
                        this.$message.error(result.msg);
        			}
        		});
        	}
        },
        filters:{

        }
    })
</script>
</html>